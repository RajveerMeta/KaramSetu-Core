
<?php
require_once __DIR__ . '/../../DataBase/admin/contributions/view.php';

ob_start();
?>


<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Page Header -->
    <div class="mb-6">
        <a href="<?= baseUrl('admin/contributions/index.php') ?>"
           class="inline-flex items-center text-sm font-medium text-forest hover:text-gold transition-colors mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Contributions
        </a>

        <div class="flex flex-col md:flex-row md:items-start justify-between">
            <div class="max-w-3xl">
                <h1 class="text-3xl font-serif font-bold text-forest mb-2">
                    <?= e($contributionName) ?>
                </h1>
                <p class="text-charcoal-light">
                    Review contribution details, contributor information, and contribution status.
                </p>
            </div>

            <div class="mt-4 md:mt-0 flex-shrink-0">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium border <?= $statusClass ?>">
                    <?= e($status) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Contribution Summary -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden mb-6">
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6 text-center md:text-left divide-y md:divide-y-0 md:divide-x divide-charcoal/10">

                <div class="col-span-2 md:col-span-1 py-3 md:py-0">
                    <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">
                        Amount
                    </p>
                    <p class="text-2xl font-bold text-forest">
                        <?= e($formattedAmount) ?>
                    </p>
                </div>

                <div class="py-3 md:py-0 md:pl-6">
                    <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">
                        Type
                    </p>
                    <p class="text-lg font-medium text-charcoal">
                        <?= e($contributionType) ?>
                    </p>
                </div>

                <div class="py-3 md:py-0 md:pl-6">
                    <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">
                        Status
                    </p>
                    <p class="text-lg font-medium text-charcoal">
                        <?= e($status) ?>
                    </p>
                </div>

                <div class="py-3 md:py-0 md:pl-6">
                    <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">
                        Date
                    </p>
                    <p class="text-lg font-medium text-charcoal">
                        <?= e($date) ?>
                    </p>
                </div>

                <div class="col-span-2 md:col-span-1 py-3 md:py-0 md:pl-6">
                    <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">
                        Contribution ID
                    </p>
                    <p class="text-sm font-mono font-medium text-charcoal mt-1.5">
                        <?= e($contributionId) ?>
                    </p>
                </div>

            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8 items-start">

        <!-- Left Column -->
        <div class="flex flex-col gap-6">

            <!-- Contributor Information -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30 flex justify-between items-center">
                    <h3 class="text-lg font-serif font-bold text-forest">
                        Contributor Information
                    </h3>

                    <?php if (!empty($row['user_id'])): ?>
                        <a href="<?= baseUrl('admin/users/view.php?id=' . (int) $row['user_id']) ?>"
                           class="text-sm font-medium text-forest hover:text-gold transition-colors flex items-center">
                            View User
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8">

                        <div>
                            <p class="text-xs font-medium text-charcoal-light mb-1">Name</p>
                            <p class="text-base font-medium text-charcoal break-words">
                                <?= e($contributorName) ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-charcoal-light mb-1">Email</p>
                            <p class="text-base font-medium text-charcoal break-words">
                                <?= e($contributorEmail) ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-charcoal-light mb-1">Phone</p>
                            <p class="text-base font-medium text-charcoal">
                                <?= e($contributorPhone) ?>
                            </p>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Contribution Information -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h3 class="text-lg font-serif font-bold text-forest">
                        Contribution Information
                    </h3>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8">

                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">
                                Contribution
                            </p>
                            <p class="text-base font-medium text-charcoal">
                                <?= e($contributionName) ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">
                                Type
                            </p>
                            <p class="text-base font-medium text-charcoal">
                                <?= e($contributionType) ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">
                                Amount
                            </p>
                            <p class="text-base font-bold text-forest">
                                <?= e($formattedAmount) ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">
                                Payment Status
                            </p>
                            <p class="text-base font-medium text-charcoal">
                                <?= e(ucfirst($row['payment_status'])) ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">
                                Date
                            </p>
                            <p class="text-base font-medium text-charcoal">
                                <?= e($date) ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">
                                Fulfillment Status
                            </p>
                            <p class="text-base font-medium text-charcoal">
                                <?= e(ucfirst($row['fulfillment_status'])) ?>
                            </p>
                        </div>

                        <?php if (!empty($row['message'])): ?>
                            <div class="sm:col-span-2">
                                <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">
                                    Message
                                </p>
                                <p class="text-sm text-charcoal leading-relaxed">
                                    <?= e($row['message']) ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">
                                Karma Points Awarded
                            </p>
                            <p class="text-base font-medium text-charcoal">
                                <?= e($row['karma_points_awarded']) ?>
                            </p>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column -->
        <div class="flex flex-col gap-6">

            <!-- Receiving NGO -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30 flex justify-between items-center">
                    <h3 class="text-lg font-serif font-bold text-forest">
                        Receiving NGO
                    </h3>

                    <?php if (!empty($row['receiving_ngo_id'])): ?>
                        <a href="<?= baseUrl('admin/ngos/view.php?id=' . (int) $row['receiving_ngo_id']) ?>"
                           class="text-sm font-medium text-forest hover:text-gold transition-colors flex items-center">
                            View NGO
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="p-6">
                    <h4 class="text-lg font-bold text-charcoal mb-4">
                        <?= e($ngoName) ?>
                    </h4>

                    <dl class="space-y-3">

                        <div>
                            <dt class="text-xs font-medium text-charcoal-light">NGO Type</dt>
                            <dd class="text-sm text-charcoal">
                                <?= e($ngoType) ?>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-charcoal-light">Primary Cause</dt>
                            <dd class="text-sm text-charcoal">
                                <?= e($causeCategory) ?>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-charcoal-light">Location</dt>
                            <dd class="text-sm text-charcoal">
                                <?= e($ngoLocation) ?>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-charcoal-light">Verification Status</dt>
                            <dd class="text-sm font-medium text-charcoal">
                                <?= e($ngoStatus) ?>
                            </dd>
                        </div>

                    </dl>
                </div>
            </div>

            <!-- Supported Cause -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h3 class="text-lg font-serif font-bold text-forest">
                        Supported Cause
                    </h3>
                </div>

                <div class="p-6">
                    <h4 class="text-lg font-bold text-charcoal mb-4">
                        <?= e($causeName) ?>
                    </h4>

                    <dl class="space-y-3 mb-4">
                        <div>
                            <dt class="text-xs font-medium text-charcoal-light">Category</dt>
                            <dd class="text-sm text-charcoal">
                                <?= e($causeCategory) ?>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-charcoal-light">NGO Verification</dt>
                            <dd class="text-sm font-medium text-charcoal">
                                <?= e($ngoStatus) ?>
                            </dd>
                        </div>
                    </dl>

                    <div class="pt-4 border-t border-charcoal/10">
                        <p class="text-sm text-charcoal-light leading-relaxed">
                            <?= e($causeDescription) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contribution Status -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h3 class="text-lg font-serif font-bold text-forest">
                        Contribution Status
                    </h3>
                </div>

                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-xs font-medium text-charcoal-light uppercase mb-1">
                            Current Status
                        </p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border <?= $statusClass ?>">
                            <?= e($status) ?>
                        </span>
                    </div>

                    <p class="text-sm text-charcoal leading-relaxed">
                        <?php if ($status == 'Completed'): ?>
                            This contribution has been successfully recorded as completed.
                        <?php elseif ($status == 'Pending'): ?>
                            This contribution is awaiting payment or fulfillment completion.
                        <?php elseif ($status == 'Cancelled'): ?>
                            This contribution has been cancelled or its payment has failed.
                        <?php else: ?>
                            The current contribution status is <?= e(strtolower($status)) ?>.
                        <?php endif; ?>
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/admin.php';
?>