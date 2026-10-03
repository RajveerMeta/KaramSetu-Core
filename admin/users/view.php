<?php
require_once __DIR__ . '/../../includes/functions.php';

$user = [
    'id' => 1,
    'name' => 'Rajveer Meta',
    'email' => 'rajveer@example.com',
    'phone' => '+91 98765 43210',
    'location' => 'Rajkot, Gujarat',
    'registered' => '12 Jan 2026',
    'status' => 'Active',
    'karma_points' => '1,280',
    'rank' => 10,
    'total_contributions' => 18,
    'total_contributed' => '₹24,500',
    'events_joined' => 7
];

$contributions = [
    [
        'cause' => 'Education Support',
        'type' => 'Money',
        'amount' => '₹2,000',
        'date' => '23 Sep 2026',
        'status' => 'Completed'
    ],
    [
        'cause' => 'Food & Nutrition',
        'type' => 'Money',
        'amount' => '₹1,500',
        'date' => '18 Sep 2026',
        'status' => 'Completed'
    ],
    [
        'cause' => 'Community Development',
        'type' => 'Resources',
        'amount' => '₹3,000',
        'date' => '12 Sep 2026',
        'status' => 'Completed'
    ],
    [
        'cause' => 'Education Support',
        'type' => 'Volunteer',
        'amount' => '—',
        'date' => '05 Sep 2026',
        'status' => 'Completed'
    ],
    [
        'cause' => 'Food & Nutrition',
        'type' => 'Money',
        'amount' => '₹750',
        'date' => '29 Aug 2026',
        'status' => 'Completed'
    ]
];

$activities = [
    ['date' => '23 Sep 2026', 'description' => 'Contributed ₹2,000 to Education Support'],
    ['date' => '18 Sep 2026', 'description' => 'Contributed ₹1,500 to Food & Nutrition'],
    ['date' => '05 Sep 2026', 'description' => 'Joined Education Support volunteer activity'],
    ['date' => '29 Aug 2026', 'description' => 'Contributed ₹750 to Food & Nutrition']
];

$nameParts = explode(' ', $user['name']);
$initials = '';
foreach ($nameParts as $part) {
    if (!empty($part)) {
        $initials .= strtoupper(substr($part, 0, 1));
    }
    if (strlen($initials) >= 2) break;
}

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Page Header & Back Link -->
    <div class="mb-6">
        <a href="<?= baseUrl('admin/users/index.php') ?>" class="inline-flex items-center text-sm font-medium text-forest hover:text-gold transition-colors mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Users
        </a>
        <div class="flex flex-col md:flex-row md:items-end justify-between">
            <div>
                <h1 class="text-3xl font-serif font-bold text-forest mb-2">User Details</h1>
                <p class="text-charcoal-light">View account information and contribution activity.</p>
            </div>
        </div>
    </div>

    <!-- Alert Container for Success Messages -->
    <div id="alertContainer" class="hidden mb-6 p-4 rounded-lg bg-green-50 border border-green-200 flex items-center">
        <svg id="alertIcon" class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span id="alertMessage" class="text-sm font-medium text-green-800"></span>
    </div>

    <!-- User Profile Header -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden mb-8 p-6 flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6">
        <div class="flex-shrink-0 h-24 w-24 rounded-full bg-forest text-white flex items-center justify-center font-serif font-bold text-3xl shadow-sm">
            <?= e($initials) ?>
        </div>
        <div class="flex-grow text-center md:text-left flex flex-col justify-center">
            <div class="flex flex-col md:flex-row md:items-center md:space-x-3 mb-1">
                <h2 class="text-2xl font-serif font-bold text-charcoal"><?= e($user['name']) ?></h2>
                <span id="headerStatusBadge" class="mt-2 md:mt-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200 self-center md:self-auto">
                    <?= e($user['status']) ?>
                </span>
            </div>
            <p class="text-charcoal-light font-medium mb-1"><?= e($user['email']) ?></p>
            <p class="text-sm text-charcoal-light">Member since <?= e($user['registered']) ?></p>
        </div>
    </div>

    <!-- Personal Info and Karma Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Personal Information -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h3 class="text-lg font-serif font-bold text-forest">Personal Information</h3>
            </div>
            <div class="p-6 flex-grow">
                <dl class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Full Name</dt>
                        <dd class="text-sm font-medium text-charcoal sm:col-span-2"><?= e($user['name']) ?></dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Email</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2"><a href="mailto:<?= e($user['email']) ?>" class="text-forest hover:text-gold transition-colors"><?= e($user['email']) ?></a></dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Phone</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2"><?= e($user['phone']) ?></dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Location</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2"><?= e($user['location']) ?></dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Member Since</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2"><?= e($user['registered']) ?></dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Account Status</dt>
                        <dd class="text-sm font-medium text-charcoal sm:col-span-2" id="infoStatusText"><?= e($user['status']) ?></dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Karma Overview -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h3 class="text-lg font-serif font-bold text-forest">Karma Overview</h3>
            </div>
            <div class="p-6 flex flex-col justify-center flex-grow">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-charcoal-light">Current Karma Points</p>
                        <p class="text-3xl font-bold text-forest mt-1"><?= e($user['karma_points']) ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-charcoal-light">Leaderboard Rank</p>
                        <div class="inline-flex items-center mt-1">
                            <span class="flex items-center justify-center bg-gold text-white rounded-full w-8 h-8 font-bold mr-2 text-sm shadow-sm">
                                #<?= e($user['rank']) ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="flex justify-between text-xs font-medium text-charcoal-light mb-1">
                        <span>Current level progress</span>
                        <span>70%</span>
                    </div>
                    <div class="w-full bg-ivory rounded-full h-2.5 border border-charcoal/10">
                        <div class="bg-forest h-2.5 rounded-full" style="width: 70%"></div>
                    </div>
                    <p class="text-xs text-charcoal-light mt-2 text-right">220 points to next level</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contribution Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Total Contributions</p>
            <p class="text-2xl font-bold text-charcoal mt-1"><?= e($user['total_contributions']) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Total Contributed</p>
            <p class="text-2xl font-bold text-forest mt-1"><?= e($user['total_contributed']) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Karma Points</p>
            <p class="text-2xl font-bold text-gold mt-1"><?= e($user['karma_points']) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Events Joined</p>
            <p class="text-2xl font-bold text-charcoal mt-1"><?= e($user['events_joined']) ?></p>
        </div>
    </div>

    <!-- Two Column Layout for Tables/Lists -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Recent Contributions -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden lg:col-span-2 flex flex-col h-full">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30 flex justify-between items-center">
                <h3 class="text-lg font-serif font-bold text-forest">Recent Contributions</h3>
            </div>
            <div class="overflow-x-auto flex-grow">
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
                        <?php foreach ($contributions as $contrib): ?>
                        <tr class="hover:bg-ivory/30 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-charcoal"><?= e($contrib['cause']) ?></td>
                            <td class="px-6 py-4 text-sm text-charcoal-light"><?= e($contrib['type']) ?></td>
                            <td class="px-6 py-4 text-sm font-medium <?= $contrib['amount'] === 'â€”' ? 'text-charcoal-light' : 'text-forest' ?>">
                                <?= e($contrib['amount']) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-charcoal-light"><?= e($contrib['date']) ?></td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                    <?= e($contrib['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden lg:col-span-1 flex flex-col h-full">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h3 class="text-lg font-serif font-bold text-forest">Recent Activity</h3>
            </div>
            <div class="p-6 flex-grow">
                <div class="flex flex-col">
                    <?php 
                    $total = count($activities);
                    $i = 0;
                    foreach ($activities as $activity): 
                        $i++;
                        $isLast = ($i === $total);
                    ?>
                    <div class="relative flex gap-4 <?= $isLast ? '' : 'pb-6' ?>">
                        <div class="relative flex flex-col items-center w-3 min-w-[0.75rem]">
                            <div class="w-2 h-2 rounded-full bg-gold relative z-10 mt-1.5"></div>
                            <?php if (!$isLast): ?>
                            <div class="absolute top-3 -bottom-6 w-px bg-gold/30"></div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-charcoal-light mb-1"><?= e($activity['date']) ?></p>
                            <p class="text-sm text-charcoal leading-snug"><?= e($activity['description']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Status & Admin Actions -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-6 flex flex-col md:flex-row md:items-center justify-between">
        <div class="mb-4 md:mb-0 max-w-2xl">
            <h3 class="text-lg font-serif font-bold text-forest mb-2">Account Status</h3>
            <p class="text-sm text-charcoal leading-relaxed">
                Administrators can suspend or activate user accounts here. A suspended user will lose access to the platform.
            </p>
        </div>
        
        <div id="actionButtonsContainer">
            <button type="button" id="btnToggleStatusInit" class="px-6 py-2.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 font-medium rounded-md transition-colors shadow-sm text-sm" data-current-status="Active">
                Suspend User
            </button>
        </div>
    </div>

</div>

<!-- Status Modal -->
<div id="statusModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-charcoal/60 backdrop-blur-sm transition-opacity" aria-hidden="true" id="statusOverlay"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-charcoal/10">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div id="modalIconContainer" class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <svg id="modalIcon" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"></svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium font-serif" id="modalTitle">Confirm Action</h3>
                        <div class="mt-2">
                            <p class="text-sm text-charcoal-light" id="modalDescription">Are you sure you want to proceed?</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-ivory/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-charcoal/10">
                <button type="button" id="btnConfirmStatus" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                    Confirm
                </button>
                <button type="button" id="btnCancelStatus" class="mt-3 w-full inline-flex justify-center rounded-md border border-charcoal/20 shadow-sm px-4 py-2 bg-white text-base font-medium text-charcoal hover:bg-ivory focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnToggleStatusInit = document.getElementById('btnToggleStatusInit');
        const statusModal = document.getElementById('statusModal');
        const btnConfirmStatus = document.getElementById('btnConfirmStatus');
        
        const modalTitle = document.getElementById('modalTitle');
        const modalDesc = document.getElementById('modalDescription');
        const modalIcon = document.getElementById('modalIcon');
        const modalIconContainer = document.getElementById('modalIconContainer');
        
        const headerStatusBadge = document.getElementById('headerStatusBadge');
        const infoStatusText = document.getElementById('infoStatusText');
        
        const alertContainer = document.getElementById('alertContainer');
        const alertIcon = document.getElementById('alertIcon');
        const alertMessage = document.getElementById('alertMessage');
        
        let pendingAction = ''; // 'Suspend' or 'Activate'

        btnToggleStatusInit.addEventListener('click', function() {
            const currentStatus = this.getAttribute('data-current-status');
            
            if (currentStatus === 'Active') {
                pendingAction = 'Suspend';
                
                modalTitle.textContent = 'Suspend Rajveer Meta?';
                modalTitle.className = 'text-lg leading-6 font-medium font-serif text-red-800';
                modalDesc.textContent = "The user will no longer be able to access their account.";
                
                modalIconContainer.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10';
                modalIcon.className.baseVal = 'h-6 w-6 text-red-600';
                modalIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
                
                btnConfirmStatus.className = 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors';
                btnConfirmStatus.textContent = 'Suspend User';
            } else {
                pendingAction = 'Activate';
                
                modalTitle.textContent = 'Activate Rajveer Meta?';
                modalTitle.className = 'text-lg leading-6 font-medium font-serif text-green-800';
                modalDesc.textContent = "The user's account will be restored and they will regain access.";
                
                modalIconContainer.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10';
                modalIcon.className.baseVal = 'h-6 w-6 text-green-600';
                modalIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
                
                btnConfirmStatus.className = 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors';
                btnConfirmStatus.textContent = 'Activate User';
            }
            
            statusModal.classList.remove('hidden');
        });
        
        document.getElementById('btnCancelStatus').addEventListener('click', () => {
            statusModal.classList.add('hidden');
        });
        
        document.getElementById('statusOverlay').addEventListener('click', () => {
            statusModal.classList.add('hidden');
        });
        
        btnConfirmStatus.addEventListener('click', () => {
            statusModal.classList.add('hidden');
            
            if (pendingAction === 'Suspend') {
                headerStatusBadge.textContent = 'Suspended';
                headerStatusBadge.className = 'mt-2 md:mt-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200 self-center md:self-auto';
                
                infoStatusText.textContent = 'Suspended';
                
                btnToggleStatusInit.setAttribute('data-current-status', 'Suspended');
                btnToggleStatusInit.textContent = 'Activate User';
                btnToggleStatusInit.className = 'px-6 py-2.5 bg-white border border-green-200 text-green-700 hover:bg-green-50 hover:border-green-300 font-medium rounded-md transition-colors shadow-sm text-sm';
                
                showAlert('User suspended successfully.', 'error');
            } else {
                headerStatusBadge.textContent = 'Active';
                headerStatusBadge.className = 'mt-2 md:mt-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200 self-center md:self-auto';
                
                infoStatusText.textContent = 'Active';
                
                btnToggleStatusInit.setAttribute('data-current-status', 'Active');
                btnToggleStatusInit.textContent = 'Suspend User';
                btnToggleStatusInit.className = 'px-6 py-2.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 font-medium rounded-md transition-colors shadow-sm text-sm';
                
                showAlert('User activated successfully.', 'success');
            }
        });

        function showAlert(message, type) {
            alertMessage.textContent = message;
            
            if (type === 'success') {
                alertContainer.className = 'mb-6 p-4 rounded-lg flex items-center bg-green-50 border border-green-200';
                alertIcon.className.baseVal = 'w-5 h-5 mr-3 text-green-500';
                alertIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
                alertMessage.className = 'text-sm font-medium text-green-800';
            } else {
                alertContainer.className = 'mb-6 p-4 rounded-lg flex items-center bg-red-50 border border-red-200';
                alertIcon.className.baseVal = 'w-5 h-5 mr-3 text-red-500';
                alertIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
                alertMessage.className = 'text-sm font-medium text-red-800';
            }
            
            alertContainer.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/admin.php';
?>


