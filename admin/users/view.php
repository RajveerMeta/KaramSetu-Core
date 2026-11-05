<?php
require_once __DIR__ . '/../../DataBase/admin/users/view.php';

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Page Header -->
    <div class="mb-6">
        <a href="<?= e(baseUrl('admin/users/index.php')) ?>"
           class="inline-flex items-center text-sm font-medium text-forest hover:text-gold transition-colors mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Users
        </a>

        <h1 class="text-3xl font-serif font-bold text-forest mb-2">
            User Details
        </h1>

        <p class="text-charcoal-light">
            View account information and contribution activity.
        </p>
    </div>

    <!-- Status Messages -->
    <?php if ($message === 'updated'): ?>
        <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-sm font-medium text-green-800">
            Account status updated successfully.
        </div>
    <?php elseif ($message === 'error'): ?>
        <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-sm font-medium text-red-800">
            Unable to update account status. Please try again.
        </div>
    <?php endif; ?>

    <!-- User Profile -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm mb-8 p-6 flex flex-col md:flex-row items-center md:items-start gap-6">
        <div class="flex-shrink-0 h-24 w-24 rounded-full bg-forest text-white flex items-center justify-center font-serif font-bold text-3xl shadow-sm">
            <?= e($initials) ?>
        </div>

        <div class="flex-grow text-center md:text-left">
            <div class="flex flex-col md:flex-row md:items-center md:gap-3 mb-2">
                <h2 class="text-2xl font-serif font-bold text-charcoal">
                    <?= e($userName) ?>
                </h2>

                <span class="mt-2 md:mt-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border self-center md:self-auto <?= e($statusClass) ?>">
                    <?= e($statusText) ?>
                </span>
            </div>

            <p class="text-charcoal-light font-medium mb-1">
                <?= e($userEmail) ?>
            </p>

            <p class="text-sm text-charcoal-light">
                Member since <?= e($registered) ?>
            </p>
        </div>
    </div>

    <!-- Personal Information and Karma Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        <!-- Personal Information -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h3 class="text-lg font-serif font-bold text-forest">
                    Personal Information
                </h3>
            </div>

            <div class="p-6">
                <dl class="space-y-4">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">
                            Full Name
                        </dt>
                        <dd class="text-sm font-medium text-charcoal sm:col-span-2">
                            <?= e($userName) ?>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">
                            Email
                        </dt>
                        <dd class="text-sm text-charcoal sm:col-span-2 break-words">
                            <a href="mailto:<?= e($userEmail) ?>"
                               class="text-forest hover:text-gold transition-colors">
                                <?= e($userEmail) ?>
                            </a>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">
                            Phone
                        </dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">
                            <?= e($userPhone) ?>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">
                            Location
                        </dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">
                            <?= e($userLocation) ?>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">
                            Member Since
                        </dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">
                            <?= e($registered) ?>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">
                            Account Status
                        </dt>
                        <dd class="text-sm font-medium text-charcoal sm:col-span-2">
                            <?= e($statusText) ?>
                        </dd>
                    </div>

                </dl>
            </div>
        </div>

        <!-- Karma Overview -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h3 class="text-lg font-serif font-bold text-forest">
                    Karma Overview
                </h3>
            </div>

            <div class="p-6">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <p class="text-sm font-medium text-charcoal-light">
                            Current Karma Points
                        </p>
                        <p class="text-3xl font-bold text-forest mt-1">
                            <?= number_format($karmaPoints) ?>
                        </p>
                    </div>

                    <div class="text-right">
                        <p class="text-sm font-medium text-charcoal-light">
                            Leaderboard Rank
                        </p>
                        <p class="text-2xl font-bold text-gold mt-1">—</p>
                    </div>
                </div>

                <p class="text-sm text-charcoal-light">
                    Karma points and leaderboard rank will be displayed when
                    the related database tables are connected.
                </p>
            </div>
        </div>
    </div>

    <!-- Contribution Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">
                Total Contributions
            </p>
            <p class="text-2xl font-bold text-charcoal mt-1">
                <?= number_format($totalContributions) ?>
            </p>
        </div>

        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">
                Total Contributed
            </p>
            <p class="text-2xl font-bold text-forest mt-1">
                ₹<?= number_format($totalContributed, 2) ?>
            </p>
        </div>

        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">
                Karma Points
            </p>
            <p class="text-2xl font-bold text-gold mt-1">
                <?= number_format($karmaPoints) ?>
            </p>
        </div>

        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">
                Events Joined
            </p>
            <p class="text-2xl font-bold text-charcoal mt-1">
                <?= number_format($eventsJoined) ?>
            </p>
        </div>
    </div>

    <!-- Contributions and Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        <!-- Recent Contributions -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden lg:col-span-2">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h3 class="text-lg font-serif font-bold text-forest">
                    Recent Contributions
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-ivory/10 text-charcoal-light text-sm border-b border-charcoal/10">
                            <th class="px-6 py-3 font-medium">Cause</th>
                            <th class="px-6 py-3 font-medium">Type</th>
                            <th class="px-6 py-3 font-medium">Amount</th>
                            <th class="px-6 py-3 font-medium">Date</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-charcoal/5">
                        <?php if (empty($contributions)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center">
                                    <p class="text-sm font-medium text-charcoal">
                                        No contribution records available.
                                    </p>
                                    <p class="text-xs text-charcoal-light mt-2">
                                        Connect the contributions database to display records here.
                                    </p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($contributions as $contrib): ?>
                                <tr class="hover:bg-ivory/30 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-charcoal">
                                        <?= e($contrib['cause']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-charcoal-light">
                                        <?= e($contrib['type']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-forest">
                                        <?= e($contrib['amount']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-charcoal-light">
                                        <?= e($contrib['date']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <?= e($contrib['status']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h3 class="text-lg font-serif font-bold text-forest">
                    Recent Activity
                </h3>
            </div>

            <div class="p-6">
                <?php if (empty($activities)): ?>
                    <p class="text-sm text-charcoal-light">
                        No recent activity records available.
                    </p>
                <?php else: ?>
                    <div class="flex flex-col">
                        <?php foreach ($activities as $index => $activity): ?>
                            <div class="relative flex gap-4 <?= $index < count($activities) - 1 ? 'pb-6' : '' ?>">
                                <div class="relative flex flex-col items-center w-3 min-w-[0.75rem]">
                                    <div class="w-2 h-2 rounded-full bg-gold relative z-10 mt-1.5"></div>
                                    <?php if ($index < count($activities) - 1): ?>
                                        <div class="absolute top-3 -bottom-6 w-px bg-gold/30"></div>
                                    <?php endif; ?>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-charcoal-light mb-1">
                                        <?= e($activity['date']) ?>
                                    </p>
                                    <p class="text-sm text-charcoal leading-snug">
                                        <?= e($activity['description']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Account Status and Admin Actions -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-6 flex flex-col md:flex-row md:items-center justify-between gap-5">

        <div class="max-w-2xl">
            <h3 class="text-lg font-serif font-bold text-forest mb-2">
                Account Status
            </h3>

            <p class="text-sm text-charcoal leading-relaxed">
                Administrators can suspend or activate user accounts here.
                A suspended user will lose access to the platform only if
                your login system checks the account status.
            </p>
        </div>

        <form method="POST" action="<?= e(baseUrl('admin/users/view.php?id=' . $userId)) ?>"
              class="flex-shrink-0">

            <?php if ($userStatus === 'active'): ?>
                <input type="hidden" name="new_status" value="suspended">

                <button type="submit" name="update_status"
                        class="px-6 py-2.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 font-medium rounded-md transition-colors shadow-sm text-sm">
                    Suspend User
                </button>
            <?php else: ?>
                <input type="hidden" name="new_status" value="active">

                <button type="submit" name="update_status"
                        class="px-6 py-2.5 bg-white border border-green-200 text-green-700 hover:bg-green-50 hover:border-green-300 font-medium rounded-md transition-colors shadow-sm text-sm">
                    Activate User
                </button>
            <?php endif; ?>

        </form>
    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/admin.php';
?>
