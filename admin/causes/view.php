<?php
require_once __DIR__ . '/../../includes/functions.php';

$cause = [
    'id' => 1,
    'name' => 'Education Support Program',
    'ngo' => 'Seva Roots Initiative',
    'category' => 'Education',
    'created_date' => '10 Sep 2026',
    'start_date' => '10 Sep 2026',
    'end_date' => '31 Dec 2026',
    'location' => 'Rajkot, Gujarat',
    'target' => 100000,
    'raised' => 72500,
    'status' => 'Active',
    'description' => "The Education Support Program helps students from underserved\ncommunities access essential educational materials, learning\nresources, and academic support.\n\nThe initiative focuses on improving access to school supplies,\ndigital learning resources, and community-based educational\nactivities.",
    'ngo_reg' => 'NGO/GJ/RJK/2026/084',
    'ngo_email' => 'contact@sevaroots.org',
    'ngo_status' => 'Approved'
];

$remaining = $cause['target'] - $cause['raised'];
$progress = round(($cause['raised'] / $cause['target']) * 100);

$contributions = [
    ['contributor' => 'Rajveer Meta', 'type' => 'Money', 'amount' => '₹2,000', 'date' => '23 Sep 2026', 'status' => 'Completed'],
    ['contributor' => 'Ananya Patel', 'type' => 'Money', 'amount' => '₹3,500', 'date' => '21 Sep 2026', 'status' => 'Completed'],
    ['contributor' => 'Dev Mehta', 'type' => 'Resources', 'amount' => '₹5,000', 'date' => '18 Sep 2026', 'status' => 'Completed'],
    ['contributor' => 'Priya Joshi', 'type' => 'Money', 'amount' => '₹1,500', 'date' => '16 Sep 2026', 'status' => 'Completed'],
    ['contributor' => 'Aarav Shah', 'type' => 'Volunteer', 'amount' => 'â€”', 'date' => '14 Sep 2026', 'status' => 'Completed'],
    ['contributor' => 'Neha Patel', 'type' => 'Money', 'amount' => '₹2,500', 'date' => '12 Sep 2026', 'status' => 'Completed']
];

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Page Header & Back Link -->
    <div class="mb-6">
        <a href="<?= baseUrl('admin/causes/index.php') ?>" class="inline-flex items-center text-sm font-medium text-forest hover:text-gold transition-colors mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Causes
        </a>
        <div class="flex flex-col md:flex-row md:items-end justify-between">
            <div>
                <h1 class="text-3xl font-serif font-bold text-forest mb-2">Cause Details</h1>
                <p class="text-charcoal-light">Review cause information, fundraising progress, and contribution activity.</p>
            </div>
        </div>
    </div>

    <!-- Alert Container for Success Messages -->
    <div id="alertContainer" class="hidden mb-6 p-4 rounded-lg bg-green-50 border border-green-200 flex items-center">
        <svg id="alertIcon" class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span id="alertMessage" class="text-sm font-medium text-green-800"></span>
    </div>

    <!-- Cause Header -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden mb-8 p-6 flex flex-col md:flex-row items-center md:items-start justify-between">
        <div class="text-center md:text-left flex flex-col justify-center">
            <div class="flex flex-col md:flex-row md:items-center md:space-x-4 mb-2">
                <h2 class="text-2xl font-serif font-bold text-charcoal"><?= e($cause['name']) ?></h2>
                <span id="headerStatusBadge" class="mt-2 md:mt-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200 self-center md:self-auto">
                    <?= e($cause['status']) ?>
                </span>
            </div>
            <p class="text-charcoal-light font-medium text-sm mb-1"><?= e($cause['ngo']) ?></p>
            <p class="text-sm text-forest font-medium"><?= e($cause['category']) ?></p>
        </div>
    </div>

    <!-- Details and NGO Info -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Cause Information -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h3 class="text-lg font-serif font-bold text-forest">Cause Information</h3>
            </div>
            <div class="p-6 flex-grow">
                <dl class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Cause Name</dt>
                        <dd class="text-sm font-medium text-charcoal sm:col-span-2"><?= e($cause['name']) ?></dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Category</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2"><?= e($cause['category']) ?></dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Created By</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2"><?= e($cause['ngo']) ?></dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Created Date</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2"><?= e($cause['created_date']) ?></dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Start Date</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2"><?= e($cause['start_date']) ?></dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">End Date</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2"><?= e($cause['end_date']) ?></dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Location</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2"><?= e($cause['location']) ?></dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Managing NGO -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h3 class="text-lg font-serif font-bold text-forest">Managing NGO</h3>
            </div>
            <div class="p-6 flex flex-col flex-grow justify-between">
                <div>
                    <h4 class="text-md font-bold text-charcoal mb-1"><?= e($cause['ngo']) ?></h4>
                    <p class="text-sm text-charcoal-light mb-4">Rajkot, Gujarat</p>
                    
                    <dl class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Registration Number</dt>
                            <dd class="text-sm font-medium text-charcoal sm:col-span-2"><?= e($cause['ngo_reg']) ?></dd>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Email</dt>
                            <dd class="text-sm text-charcoal sm:col-span-2"><a href="mailto:<?= e($cause['ngo_email']) ?>" class="text-forest hover:text-gold transition-colors"><?= e($cause['ngo_email']) ?></a></dd>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-charcoal-light sm:col-span-1">Status</dt>
                            <dd class="text-sm font-medium text-green-700 sm:col-span-2"><?= e($cause['ngo_status']) ?></dd>
                        </div>
                    </dl>
                </div>
                <div class="mt-6 pt-4 border-t border-charcoal/5">
                    <a href="<?= baseUrl('admin/ngos/view.php?id=1') ?>" class="inline-flex justify-center items-center px-4 py-2 border border-forest text-forest hover:bg-forest hover:text-white text-sm font-medium rounded-md transition-colors">
                        View NGO
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- About This Cause -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
            <h3 class="text-lg font-serif font-bold text-forest">About This Cause</h3>
        </div>
        <div class="p-6">
            <div class="prose prose-sm max-w-none text-charcoal">
                <?php foreach(explode("\n\n", $cause['description']) as $paragraph): ?>
                    <p class="mb-4 last:mb-0"><?= nl2br(e($paragraph)) ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Fundraising Overview -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
            <h3 class="text-lg font-serif font-bold text-forest">Fundraising Overview</h3>
        </div>
        <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-4">
                <div class="mb-2 md:mb-0">
                    <p class="text-sm font-medium text-charcoal-light">Raised</p>
                    <p class="text-3xl font-bold text-forest">₹<?= number_format($cause['raised']) ?> <span class="text-lg font-normal text-charcoal-light">/ ₹<?= number_format($cause['target']) ?></span></p>
                </div>
                <div class="text-left md:text-right">
                    <p class="text-sm font-medium text-charcoal-light">Remaining</p>
                    <p class="text-xl font-bold text-charcoal">₹<?= number_format($remaining) ?></p>
                </div>
            </div>
            
            <div class="mt-4">
                <div class="flex justify-between text-sm font-medium mb-2">
                    <span class="text-charcoal-light">Progress</span>
                    <span class="text-forest font-bold"><?= $progress ?>%</span>
                </div>
                <div class="w-full bg-ivory rounded-full h-3 border border-charcoal/10">
                    <div class="bg-forest h-3 rounded-full" style="width: <?= $progress ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contribution Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Total Contributions</p>
            <p class="text-2xl font-bold text-charcoal mt-1">18</p>
        </div>
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Money Contributions</p>
            <p class="text-2xl font-bold text-forest mt-1">₹52,500</p>
        </div>
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Resource Contributions</p>
            <p class="text-2xl font-bold text-gold mt-1">₹20,000</p>
        </div>
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Contributors</p>
            <p class="text-2xl font-bold text-charcoal mt-1">14</p>
        </div>
    </div>

    <!-- Recent Contributions Table -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30 flex justify-between items-center">
            <h3 class="text-lg font-serif font-bold text-forest">Recent Contributions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-ivory/10 text-charcoal-light text-sm border-b border-charcoal/10">
                        <th class="px-6 py-3 font-medium">Contributor</th>
                        <th class="px-6 py-3 font-medium">Type</th>
                        <th class="px-6 py-3 font-medium text-right">Amount</th>
                        <th class="px-6 py-3 font-medium">Date</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-charcoal/5">
                    <?php foreach ($contributions as $contrib): ?>
                    <tr class="hover:bg-ivory/30 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-charcoal"><?= e($contrib['contributor']) ?></td>
                        <td class="px-6 py-4 text-sm text-charcoal-light"><?= e($contrib['type']) ?></td>
                        <td class="px-6 py-4 text-sm font-medium text-right <?= $contrib['amount'] === 'â€”' ? 'text-charcoal-light' : 'text-forest' ?>">
                            <?= e($contrib['amount']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-charcoal-light"><?= e($contrib['date']) ?></td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                <?= e($contrib['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Cause Status & Admin Actions -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-6 flex flex-col md:flex-row md:items-center justify-between">
        <div class="mb-4 md:mb-0 max-w-2xl">
            <h3 class="text-lg font-serif font-bold text-forest mb-2">Cause Status</h3>
            <p id="statusDescription" class="text-sm text-charcoal leading-relaxed">
                This cause is currently visible and accepting contributions.
            </p>
        </div>
        
        <div id="actionButtonsContainer">
            <button type="button" id="btnToggleStatusInit" class="px-6 py-2.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 font-medium rounded-md transition-colors shadow-sm text-sm" data-current-status="Active">
                Suspend Cause
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
        const statusDescription = document.getElementById('statusDescription');
        
        const alertContainer = document.getElementById('alertContainer');
        const alertIcon = document.getElementById('alertIcon');
        const alertMessage = document.getElementById('alertMessage');
        
        let pendingAction = '';

        btnToggleStatusInit.addEventListener('click', function() {
            const currentStatus = this.getAttribute('data-current-status');
            
            if (currentStatus === 'Active') {
                pendingAction = 'Suspend';
                
                modalTitle.textContent = 'Suspend this cause?';
                modalTitle.className = 'text-lg leading-6 font-medium font-serif text-red-800';
                modalDesc.textContent = "The cause will no longer be available as an active cause.";
                
                modalIconContainer.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10';
                modalIcon.className.baseVal = 'h-6 w-6 text-red-600';
                modalIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
                
                btnConfirmStatus.className = 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors';
                btnConfirmStatus.textContent = 'Suspend Cause';
            } else {
                pendingAction = 'Activate';
                
                modalTitle.textContent = 'Activate this cause?';
                modalTitle.className = 'text-lg leading-6 font-medium font-serif text-green-800';
                modalDesc.textContent = "The cause will become visible and active again for users.";
                
                modalIconContainer.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10';
                modalIcon.className.baseVal = 'h-6 w-6 text-green-600';
                modalIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
                
                btnConfirmStatus.className = 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors';
                btnConfirmStatus.textContent = 'Activate Cause';
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
                
                statusDescription.textContent = 'This cause is currently suspended.';
                
                btnToggleStatusInit.setAttribute('data-current-status', 'Suspended');
                btnToggleStatusInit.textContent = 'Activate Cause';
                btnToggleStatusInit.className = 'px-6 py-2.5 bg-white border border-green-200 text-green-700 hover:bg-green-50 hover:border-green-300 font-medium rounded-md transition-colors shadow-sm text-sm';
                
                showAlert('Cause suspended successfully.', 'error');
            } else {
                headerStatusBadge.textContent = 'Active';
                headerStatusBadge.className = 'mt-2 md:mt-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200 self-center md:self-auto';
                
                statusDescription.textContent = 'This cause is currently visible and accepting contributions.';
                
                btnToggleStatusInit.setAttribute('data-current-status', 'Active');
                btnToggleStatusInit.textContent = 'Suspend Cause';
                btnToggleStatusInit.className = 'px-6 py-2.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 font-medium rounded-md transition-colors shadow-sm text-sm';
                
                showAlert('Cause activated successfully.', 'success');
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


