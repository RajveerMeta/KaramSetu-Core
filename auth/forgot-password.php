<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../Database/db_config.php';
require_once __DIR__ . '/../includes/mailer.php';

if (!empty($_SESSION['user_id'])) {
    redirectUserToHomeDashboard();
}

$successMsg = $_SESSION['forgot_pw_success'] ?? '';
$errorMsg = $_SESSION['forgot_pw_error'] ?? '';
$emailInput = $_SESSION['forgot_pw_email'] ?? '';
unset($_SESSION['forgot_pw_success'], $_SESSION['forgot_pw_error'], $_SESSION['forgot_pw_email']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['forgot_pw_error'] = 'Invalid security token. Please refresh the page and try again.';
        header('Location: ' . baseUrl('auth/forgot-password.php'));
        exit;
    }

    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $_SESSION['forgot_pw_email'] = $email;

    if ($email === '') {
        $_SESSION['forgot_pw_error'] = 'Please enter your registered email address.';
        header('Location: ' . baseUrl('auth/forgot-password.php'));
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['forgot_pw_error'] = 'Please enter a valid email address.';
        header('Location: ' . baseUrl('auth/forgot-password.php'));
        exit;
    }

    $nowTs = time();
    $windowSeconds = 900;
    $maxWindowRequests = 5;
    $attempts = $_SESSION['forgot_pw_attempts'] ?? [];
    $attempts = array_values(array_filter($attempts, function ($ts) use ($nowTs, $windowSeconds) {
        return is_int($ts) && ($nowTs - $ts) < $windowSeconds;
    }));

    if (count($attempts) >= $maxWindowRequests) {
        $_SESSION['forgot_pw_attempts'] = $attempts;
        $_SESSION['forgot_pw_error'] = 'Too many reset requests. Please wait a few minutes before trying again.';
        header('Location: ' . baseUrl('auth/forgot-password.php'));
        exit;
    }

    $attempts[] = $nowTs;
    $_SESSION['forgot_pw_attempts'] = $attempts;

    $genericSuccess = 'If an eligible account matches that email address, a password reset link has been sent. Please check your inbox and spam folder.';

    $accountStmt = mysqli_prepare(
        $con,
        "SELECT u.id, u.name, u.email, u.role, u.status,
                n.id AS ngo_id, n.name AS ngo_name, n.verification_status
         FROM users u
         LEFT JOIN ngos n ON n.user_id = u.id
         WHERE LOWER(u.email) = ?
         LIMIT 1"
    );

    if (!$accountStmt) {
        error_log('KarmaSetu ForgotPassword prepare failure.');
        $_SESSION['forgot_pw_error'] = 'Unable to process your request right now. Please try again later.';
        header('Location: ' . baseUrl('auth/forgot-password.php'));
        exit;
    }

    mysqli_stmt_bind_param($accountStmt, 's', $email);
    mysqli_stmt_execute($accountStmt);
    $accountRes = mysqli_stmt_get_result($accountStmt);
    $account = $accountRes ? mysqli_fetch_assoc($accountRes) : null;
    mysqli_stmt_close($accountStmt);

    $isEligible = false;
    if ($account) {
        $role = strtolower(trim((string) ($account['role'] ?? '')));
        $userStatus = strtolower(trim((string) ($account['status'] ?? '')));
        $ngoStatus = strtolower(trim((string) ($account['verification_status'] ?? '')));

        if ($role === 'user') {
            $isEligible = ($userStatus === 'active');
        } elseif ($role === 'ngo') {
            $hasNgo = !empty($account['ngo_id']);
            $isEligible = $hasNgo
                && in_array($userStatus, ['active', 'pending'], true)
                && in_array($ngoStatus, ['approved', 'pending', 'rejected'], true);
        }
    }

    if (!$isEligible) {
        unset($_SESSION['forgot_pw_email']);
        $_SESSION['forgot_pw_success'] = $genericSuccess;
        header('Location: ' . baseUrl('auth/forgot-password.php'));
        exit;
    }

    $targetEmail = (string) $account['email'];

    $cooldownStmt = mysqli_prepare(
        $con,
        "SELECT id FROM password_resets WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 60 SECOND) LIMIT 1"
    );
    if ($cooldownStmt) {
        mysqli_stmt_bind_param($cooldownStmt, 's', $targetEmail);
        mysqli_stmt_execute($cooldownStmt);
        $cooldownRes = mysqli_stmt_get_result($cooldownStmt);
        $inCooldown = ($cooldownRes && mysqli_num_rows($cooldownRes) > 0);
        mysqli_stmt_close($cooldownStmt);

        if ($inCooldown) {
            unset($_SESSION['forgot_pw_email']);
            $_SESSION['forgot_pw_success'] = $genericSuccess;
            header('Location: ' . baseUrl('auth/forgot-password.php'));
            exit;
        }
    }

    $rawToken = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $rawToken);

    mysqli_begin_transaction($con);

    try {
        $delStmt = mysqli_prepare($con, 'DELETE FROM password_resets WHERE email = ?');
        if (!$delStmt) {
            throw new Exception('Unable to clear previous reset tokens.');
        }
        mysqli_stmt_bind_param($delStmt, 's', $targetEmail);
        if (!mysqli_stmt_execute($delStmt)) {
            mysqli_stmt_close($delStmt);
            throw new Exception('Failed to clear previous reset tokens.');
        }
        mysqli_stmt_close($delStmt);

        $insStmt = mysqli_prepare(
            $con,
            'INSERT INTO password_resets (email, token_hash, expires_at, created_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE), NOW())'
        );
        if (!$insStmt) {
            throw new Exception('Unable to store reset token.');
        }
        mysqli_stmt_bind_param($insStmt, 'ss', $targetEmail, $tokenHash);
        if (!mysqli_stmt_execute($insStmt)) {
            mysqli_stmt_close($insStmt);
            throw new Exception('Failed to save reset token.');
        }
        mysqli_stmt_close($insStmt);

        $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $scheme = $isHttps ? 'https' : 'http';
        $host = trim((string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
        $host = preg_replace('/[^A-Za-z0-9.\-:\[\]]/', '', $host);
        if ($host === '') {
            $host = 'localhost';
        }
        $resetUrl = $scheme . '://' . $host . baseUrl('auth/reset-password.php?token=' . urlencode($rawToken));

        $recipientDisplayName = ($account['role'] === 'ngo' && !empty($account['ngo_name']))
            ? (string) $account['ngo_name']
            : (string) ($account['name'] ?? 'User');

        $safeName = e($recipientDisplayName);
        $safeUrl = e($resetUrl);

        $subject = 'Reset Your KarmaSetu Password';
        $body = '<div style="font-family: Arial, sans-serif; background-color: #F7F5EF; padding: 24px;">'
            . '<div style="max-width: 600px; margin: 0 auto; background-color: #FFFFFF; border: 1px solid #E2D5B5; border-radius: 12px; overflow: hidden;">'
            . '<div style="background-color: #173D29; padding: 20px 24px;">'
            . '<h2 style="margin: 0; color: #FFFFFF; font-size: 22px;">KarmaSetu Password Reset</h2>'
            . '</div>'
            . '<div style="padding: 24px; color: #2D2D2A; font-size: 15px; line-height: 1.6;">'
            . '<p style="margin-top: 0;">Hello ' . $safeName . ',</p>'
            . '<p>We received a request to reset the password for your KarmaSetu account. Click the button below to choose a new password:</p>'
            . '<div style="text-align: center; margin: 28px 0;">'
            . '<a href="' . $safeUrl . '" style="display: inline-block; background-color: #173D29; color: #FFFFFF; text-decoration: none; padding: 12px 28px; border-radius: 999px; font-weight: bold; font-size: 14px;">Reset Password</a>'
            . '</div>'
            . '<p style="font-size: 13px; color: #5C5C56;">If the button does not work, copy and paste this link into your browser:</p>'
            . '<p style="font-size: 13px; word-break: break-all;"><a href="' . $safeUrl . '" style="color: #173D29;">' . $safeUrl . '</a></p>'
            . '<p style="font-size: 13px; color: #991B1B; font-weight: bold;">This link will expire in 30 minutes and can only be used once.</p>'
            . '<hr style="border: none; border-top: 1px solid #EAE6DC; margin: 20px 0;">'
            . '<p style="font-size: 12px; color: #7A7A72; margin-bottom: 0;">If you did not request a password reset, you can safely ignore this email. Your password will remain unchanged.</p>'
            . '</div></div></div>';

        if (!sendEmail($targetEmail, $subject, $body)) {
            throw new Exception('Password reset email dispatch failed.');
        }

        mysqli_commit($con);
        unset($_SESSION['forgot_pw_email']);
        $_SESSION['forgot_pw_success'] = $genericSuccess;
    } catch (\Throwable $e) {
        mysqli_rollback($con);
        error_log('KarmaSetu ForgotPassword error: ' . $e->getMessage());
        $_SESSION['forgot_pw_error'] = 'We could not send the password reset email right now. Please try again later.';
    }

    header('Location: ' . baseUrl('auth/forgot-password.php'));
    exit;
}

$csrfToken = getCsrfToken();

ob_start();
?>

<div class="min-h-[calc(100vh-200px)] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 sm:p-10 rounded-2xl shadow-sm border border-gold/20 relative overflow-hidden">

        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-gold/10 rounded-full blur-xl z-0 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-24 h-24 bg-forest/5 rounded-full blur-xl z-0 pointer-events-none"></div>

        <div class="relative z-10">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-serif font-bold text-forest mb-2">Forgot Password?</h1>
                <p class="text-charcoal-light text-sm leading-relaxed">
                    Enter the email address registered with your KarmaSetu User or NGO account and we will send you a password reset link.
                </p>
            </div>

            <?php if ($successMsg !== ''): ?>
                <div class="mb-6 rounded-lg border border-forest/20 bg-forest/10 p-4 text-sm font-medium text-forest">
                    <?= e($successMsg) ?>
                </div>
            <?php endif; ?>

            <?php if ($errorMsg !== ''): ?>
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">
                    <?= e($errorMsg) ?>
                </div>
            <?php endif; ?>

            <form id="ForgotPasswordForm" class="space-y-6 validation-form" action="<?= baseUrl('auth/forgot-password.php') ?>" method="POST" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                <div class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-charcoal mb-1">Email Address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required data-validate="required email"
                            value="<?= e($emailInput) ?>"
                            class="appearance-none block w-full px-4 py-3 border border-charcoal/20 rounded-lg text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-1 focus:ring-gold focus:border-gold transition-colors sm:text-sm bg-ivory/30 focus:bg-white"
                            placeholder="Enter your email address">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" id="forgotSubmitBtn"
                        class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-full text-white bg-forest hover:bg-forest-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-forest transition-all shadow-md hover:shadow-lg">
                        Send Reset Link
                    </button>
                </div>
            </form>

            <div class="text-center mt-8 pt-6 border-t border-charcoal/10 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm">
                <a href="<?= baseUrl('auth/login.php') ?>" class="font-medium text-forest hover:text-gold transition-colors">
                    &larr; Back to Login
                </a>
                <a href="<?= baseUrl('auth/register.php') ?>" class="font-medium text-charcoal-light hover:text-forest transition-colors">
                    Create an account
                </a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#ForgotPasswordForm').on('submit', function (e) {
        if (!e.isDefaultPrevented()) {
            var btn = $('#forgotSubmitBtn');
            btn.prop('disabled', true).text('Sending...');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>
