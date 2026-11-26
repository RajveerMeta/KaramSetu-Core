<?php
require_once __DIR__ . '/../includes/functions.php';
requireUser();
require_once __DIR__ . '/../Database/db_config.php';

$userId = (int) ($_SESSION['user_id'] ?? 0);
if ($userId <= 0) {
    header('Location: ' . baseUrl('auth/login.php'));
    exit;
}

$successMsg = $_SESSION['change_password_success'] ?? '';
$errorMsg = $_SESSION['change_password_error'] ?? '';
unset($_SESSION['change_password_success'], $_SESSION['change_password_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errorMsg = 'Invalid security token. Please refresh the page and try again.';
    } else {
        $currentPassword = (string) ($_POST['current_password'] ?? '');
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $errorMsg = 'All password fields are required.';
        } elseif (strlen($newPassword) < 8 || strlen($newPassword) > 25) {
            $errorMsg = 'New password must be between 8 and 25 characters.';
        } elseif (!preg_match('/[A-Z]/', $newPassword)) {
            $errorMsg = 'New password must include at least one uppercase letter.';
        } elseif (!preg_match('/[a-z]/', $newPassword)) {
            $errorMsg = 'New password must include at least one lowercase letter.';
        } elseif (!preg_match('/\d/', $newPassword)) {
            $errorMsg = 'New password must include at least one number.';
        } elseif (!preg_match('/[@$!%*?&]/', $newPassword)) {
            $errorMsg = 'New password must include at least one special character (@$!%*?&).';
        } elseif (!preg_match('/^[A-Za-z\d@$!%*?&]{8,25}$/', $newPassword)) {
            $errorMsg = 'New password contains invalid characters. Only letters, numbers, and @$!%*?& are allowed.';
        } elseif ($newPassword !== $confirmPassword) {
            $errorMsg = 'New password and confirm new password do not match.';
        } else {
            $userStmt = mysqli_prepare($con, "SELECT id, password_hash, status FROM users WHERE id = ? AND role = 'user' LIMIT 1");
            if (!$userStmt) {
                $errorMsg = 'Unable to verify your account right now. Please try again.';
            } else {
                mysqli_stmt_bind_param($userStmt, 'i', $userId);
                mysqli_stmt_execute($userStmt);
                $userRes = mysqli_stmt_get_result($userStmt);
                $dbUser = $userRes ? mysqli_fetch_assoc($userRes) : null;
                mysqli_stmt_close($userStmt);

                if (!$dbUser || strtolower(trim($dbUser['status'] ?? '')) !== 'active') {
                    header('Location: ' . baseUrl('auth/logout.php'));
                    exit;
                } elseif (!hash_equals((string) ($dbUser['password_hash'] ?? ''), $currentPassword)) {
                    $errorMsg = 'Current password is incorrect.';
                } elseif (hash_equals($currentPassword, $newPassword)) {
                    $errorMsg = 'New password must be different from your current password.';
                } else {
                    $updateStmt = mysqli_prepare($con, "UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ? AND role = 'user'");
                    if ($updateStmt) {
                        mysqli_stmt_bind_param($updateStmt, 'si', $newPassword, $userId);
                        $updated = mysqli_stmt_execute($updateStmt);
                        mysqli_stmt_close($updateStmt);

                        if ($updated) {
                            $_SESSION['change_password_success'] = 'Your password has been updated successfully.';
                            header('Location: ' . baseUrl('index.php?page=change-password'));
                            exit;
                        }
                    }
                    $errorMsg = 'Unable to update your password. Please try again.';
                }
            }
        }
    }
}

$csrfToken = getCsrfToken();
?>
<div class="min-h-[calc(100vh-200px)] bg-ivory/40 py-10 lg:py-14 px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl mx-auto">

        <div class="mb-6">
            <a href="<?= baseUrl('user/profile.php') ?>" class="inline-flex items-center text-sm font-medium text-forest hover:text-gold transition-colors mb-4">
                &larr; Back to Profile &amp; Settings
            </a>
            <h1 class="font-serif text-3xl md:text-4xl font-bold text-forest">Change Password</h1>
            <p class="mt-2 text-sm lg:text-base text-charcoal-light">
                Update your password to keep your KarmaSetu account secure.
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gold/20 p-6 sm:p-8">

            <?php if ($successMsg !== ''): ?>
                <div class="mb-6 rounded-xl border border-forest/20 bg-forest/10 p-4 text-sm font-medium text-forest flex items-center gap-3">
                    <svg class="h-5 w-5 text-forest shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span><?= e($successMsg) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($errorMsg !== ''): ?>
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700 flex items-center gap-3">
                    <svg class="h-5 w-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><?= e($errorMsg) ?></span>
                </div>
            <?php endif; ?>

            <form id="changePasswordForm" class="space-y-6" action="<?= baseUrl('index.php?page=change-password') ?>" method="POST" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-charcoal">
                            Current Password
                        </label>
                        <a href="<?= baseUrl('auth/forgot-password.php') ?>" class="text-xs font-medium text-forest hover:text-gold transition-colors">
                            Forgot Password?
                        </a>
                    </div>
                    <div class="relative">
                        <input
                            id="current_password"
                            name="current_password"
                            type="password"
                            autocomplete="current-password"
                            placeholder="Enter your current password"
                            class="w-full rounded-lg border border-charcoal/20 bg-ivory/30 focus:bg-white px-4 py-3 pr-10 text-sm text-charcoal placeholder-charcoal/40 outline-none transition focus:border-gold focus:ring-1 focus:ring-gold"
                        >
                        <button
                            type="button"
                            class="toggle-password-btn absolute inset-y-0 right-3 flex items-center text-charcoal/50 hover:text-forest focus:outline-none"
                            data-target="current_password"
                            aria-label="Show password"
                        >
                            <svg class="w-5 h-5 eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg class="w-5 h-5 eye-off hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="new_password" class="mb-2 block text-xs font-bold uppercase tracking-wider text-charcoal">
                        New Password
                    </label>
                    <div class="relative">
                        <input
                            id="new_password"
                            name="new_password"
                            type="password"
                            autocomplete="new-password"
                            minlength="8"
                            maxlength="25"
                            placeholder="Create a new password"
                            class="w-full rounded-lg border border-charcoal/20 bg-ivory/30 focus:bg-white px-4 py-3 pr-10 text-sm text-charcoal placeholder-charcoal/40 outline-none transition focus:border-gold focus:ring-1 focus:ring-gold"
                        >
                        <button
                            type="button"
                            class="toggle-password-btn absolute inset-y-0 right-3 flex items-center text-charcoal/50 hover:text-forest focus:outline-none"
                            data-target="new_password"
                            aria-label="Show password"
                        >
                            <svg class="w-5 h-5 eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg class="w-5 h-5 eye-off hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="mt-3 rounded-lg border border-charcoal/10 bg-ivory/40 p-3.5 text-xs text-charcoal-light">
                        <p class="font-bold text-charcoal mb-1.5">Password requirements:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>8&ndash;25 characters long</li>
                            <li>Include at least one uppercase and one lowercase letter</li>
                            <li>Include at least one number</li>
                            <li>Include at least one special character (@$!%*?&amp;)</li>
                        </ul>
                    </div>
                </div>

                <div>
                    <label for="confirm_password" class="mb-2 block text-xs font-bold uppercase tracking-wider text-charcoal">
                        Confirm New Password
                    </label>
                    <div class="relative">
                        <input
                            id="confirm_password"
                            name="confirm_password"
                            type="password"
                            autocomplete="new-password"
                            minlength="8"
                            maxlength="25"
                            placeholder="Re-enter your new password"
                            class="w-full rounded-lg border border-charcoal/20 bg-ivory/30 focus:bg-white px-4 py-3 pr-10 text-sm text-charcoal placeholder-charcoal/40 outline-none transition focus:border-gold focus:ring-1 focus:ring-gold"
                        >
                        <button
                            type="button"
                            class="toggle-password-btn absolute inset-y-0 right-3 flex items-center text-charcoal/50 hover:text-forest focus:outline-none"
                            data-target="confirm_password"
                            aria-label="Show password"
                        >
                            <svg class="w-5 h-5 eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg class="w-5 h-5 eye-off hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="pt-4 border-t border-charcoal/10 flex flex-col-reverse sm:flex-row gap-3">
                    <a
                        href="<?= baseUrl('user/profile.php') ?>"
                        class="w-full sm:w-1/2 rounded-full border border-charcoal/20 bg-white px-6 py-3 text-sm font-bold text-charcoal text-center shadow-sm transition-colors hover:bg-ivory"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        name="update_password"
                        value="1"
                        class="w-full sm:w-1/2 rounded-full bg-forest px-6 py-3 text-sm font-bold text-white shadow-sm transition-colors hover:bg-forest-dark"
                    >
                        Update Password
                    </button>
                </div>

            </form>

        </div>

    </div>
</div>

<script>
$(document).ready(function () {
    $('.toggle-password-btn').on('click', function () {
        var btn = $(this);
        var input = $('#' + btn.data('target'));
        if (!input.length) {
            return;
        }
        var isPassword = input.attr('type') === 'password';
        input.attr('type', isPassword ? 'text' : 'password');
        btn.attr('aria-label', isPassword ? 'Hide password' : 'Show password');
        btn.find('.eye-open').toggleClass('hidden', isPassword);
        btn.find('.eye-off').toggleClass('hidden', !isPassword);
    });
});
</script>
