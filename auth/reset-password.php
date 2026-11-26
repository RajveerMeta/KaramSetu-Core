<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../Database/db_config.php';

if (!empty($_SESSION['user_id'])) {
    redirectUserToHomeDashboard();
}

$errorMsg = '';
$token = trim($_GET['token'] ?? '');

if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
    $errorMsg = 'Invalid or expired password reset link.';
} else {
    $tokenHash = hash('sha256', $token);

    $stmt = mysqli_prepare($con, "SELECT email FROM password_resets WHERE token_hash = ? AND expires_at > NOW() LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $tokenHash);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $reset = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$reset) {
        $errorMsg = 'Invalid or expired password reset link.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $errorMsg === '') {

    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errorMsg = 'Invalid security token. Please try again.';
    } else {
        $password = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($password === '' || $confirmPassword === '') {
            $errorMsg = 'Both password fields are required.';
        } elseif (strlen($password) < 8 || strlen($password) > 25) {
            $errorMsg = 'Password must be between 8 and 25 characters.';
        } elseif ($password !== $confirmPassword) {
            $errorMsg = 'Passwords do not match.';
        } else {
            mysqli_begin_transaction($con);

            try {
                $stmt = mysqli_prepare($con, "SELECT id FROM password_resets WHERE token_hash = ? AND expires_at > NOW() FOR UPDATE");
                mysqli_stmt_bind_param($stmt, 's', $tokenHash);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $validToken = mysqli_fetch_assoc($result);
                mysqli_stmt_close($stmt);

                if (!$validToken) {
                    throw new Exception('Reset link has expired or already been used.');
                }

                $stmt = mysqli_prepare($con, "UPDATE users SET password_hash = ?, updated_at = NOW() WHERE email = ?");
                mysqli_stmt_bind_param($stmt, 'ss', $password, $reset['email']);

                if (!mysqli_stmt_execute($stmt) || mysqli_stmt_affected_rows($stmt) !== 1) {
                    throw new Exception('Unable to update password.');
                }

                mysqli_stmt_close($stmt);

                $stmt = mysqli_prepare($con, "DELETE FROM password_resets WHERE email = ?");
                mysqli_stmt_bind_param($stmt, 's', $reset['email']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                mysqli_commit($con);

                $_SESSION['reset_pw_success'] = 'Password reset successfully. Please login.';
                header('Location: ' . baseUrl('auth/login.php'));
                exit;

            } catch (Throwable $e) {
                mysqli_rollback($con);
                error_log($e->getMessage());
                $errorMsg = 'Unable to reset password. Please request a new reset link.';
            }
        }
    }
}

$csrfToken = getCsrfToken();

ob_start();
?>

<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-sm border border-gold/20">

        <h1 class="text-3xl font-serif font-bold text-forest text-center mb-3">
            Reset Password
        </h1>

        <p class="text-center text-charcoal-light text-sm mb-6">
            Create a new password for your KarmaSetu account.
        </p>

        <?php if ($errorMsg !== ''): ?>
            <div class="mb-5 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                <?= e($errorMsg) ?>
            </div>

            <a href="<?= baseUrl('auth/forgot-password.php') ?>"
               class="block text-center text-forest font-bold">
                Request New Reset Link
            </a>

        <?php else: ?>

            <form method="POST" action="<?= baseUrl('auth/reset-password.php?token=' . urlencode($token)) ?>" class="space-y-5">

                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                <div>
                    <label class="block text-sm font-medium text-charcoal mb-1">
                        New Password
                    </label>
                    <input type="password" id="new_password" name="new_password" required minlength="8" maxlength="25"
                           class="w-full px-4 py-3 border border-charcoal/20 rounded-lg focus:border-gold focus:outline-none" data-validate="required strongPassword">
                </div>

                <div>
                    <label class="block text-sm font-medium text-charcoal mb-1">
                        Confirm Password
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="8" maxlength="25" data-validate="required confirmPassword" data-password-id="new_password"
                           class="w-full px-4 py-3 border border-charcoal/20 rounded-lg focus:border-gold focus:outline-none">
                </div>

                <button type="submit"
                        class="w-full py-3 rounded-full bg-forest text-white font-bold hover:bg-forest-dark">
                    Reset Password
                </button>

            </form>

        <?php endif; ?>

        <div class="text-center mt-6">
            <a href="<?= baseUrl('auth/login.php') ?>" class="text-sm text-forest font-medium">
                Back to Login
            </a>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>
