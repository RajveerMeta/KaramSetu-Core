<?php
require_once __DIR__ . '/../../includes/functions.php';

$event = [
    'name' => 'Community Food Distribution',
    'ngo' => 'Seva Roots Initiative',
    'category' => 'Food & Nutrition',
    'date' => '28 Sep 2026',
    'time' => '09:00 AM - 01:00 PM',
    'location' => 'Rajkot Community Hall',
    'registered' => 42,
    'capacity' => 100,
    'status' => 'Upcoming',
    'about' => "Community Food Distribution is a community-focused initiative organized by Seva Roots Initiative to provide nutritious food support to individuals and families in need. Volunteers will help with food packing, distribution, and basic coordination throughout the event.",
    'objective' => "Provide accessible food support while encouraging local volunteers to participate in community service."
];

$ngo = [
    'name' => 'Seva Roots Initiative',
    'category' => 'Community Welfare NGO',
    'location' => 'Rajkot, Gujarat',
    'status' => 'Approved'
];

$participants = [
    ['name' => 'Rajveer Meta', 'email' => 'rajveer@example.com', 'date' => '20 Sep 2026', 'status' => 'Confirmed'],
    ['name' => 'Aarav Shah', 'email' => 'aarav@example.com', 'date' => '21 Sep 2026', 'status' => 'Confirmed'],
    ['name' => 'Ananya Patel', 'email' => 'ananya@example.com', 'date' => '21 Sep 2026', 'status' => 'Confirmed'],
    ['name' => 'Dev Mehta', 'email' => 'dev@example.com', 'date' => '22 Sep 2026', 'status' => 'Confirmed'],
    ['name' => 'Krisha Joshi', 'email' => 'krisha@example.com', 'date' => '23 Sep 2026', 'status' => 'Confirmed'],
    ['name' => 'Yash Trivedi', 'email' => 'yash@example.com', 'date' => '24 Sep 2026', 'status' => 'Confirmed']
];

$progress = round(($event['registered'] / $event['capacity']) * 100);

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Page Header & Back Link -->
    <div class="mb-6">
        <a href="<?= baseUrl('admin/events/index.php') ?>" class="inline-flex items-center text-sm font-medium text-forest hover:text-gold transition-colors mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Events
        </a>
        <div class="flex flex-col md:flex-row md:items-start justify-between">
            <div class="max-w-3xl">
                <h1 class="text-3xl font-serif font-bold text-forest mb-2"><?= e($event['name']) ?></h1>
                <p class="text-charcoal-light">Review event information, organizer details, participation, and current status.</p>
            </div>
            <div class="mt-4 md:mt-0 flex-shrink-0">
                <span id="headerStatusBadge" class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                    <?= e($event['status']) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Alert Container for Success Messages -->
    <div id="alertContainer" class="hidden mb-6 p-4 rounded-lg bg-green-50 border border-green-200 flex items-center">
        <svg id="alertIcon" class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span id="alertMessage" class="text-sm font-medium text-green-800"></span>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 items-start">
        
        <!-- Left Column: Primary Details -->
        <div class="lg:col-span-2 flex flex-col gap-6">
            
            <!-- Event Overview Card -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden flex flex-col">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Category</p>
                            <p class="text-base font-medium text-forest"><?= e($event['category']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Date</p>
                            <p class="text-base font-medium text-charcoal"><?= e($event['date']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Time</p>
                            <p class="text-base font-medium text-charcoal"><?= e($event['time']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Location</p>
                            <p class="text-base font-medium text-charcoal"><?= e($event['location']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Participants</p>
                            <p class="text-base font-medium text-charcoal"><?= e($event['registered']) ?> / <?= e($event['capacity']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- About This Event -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h3 class="text-lg font-serif font-bold text-forest">About This Event</h3>
                </div>
                <div class="p-6">
                    <p class="text-charcoal leading-relaxed mb-6">
                        <?= nl2br(e($event['about'])) ?>
                    </p>
                    <h4 class="text-sm font-bold text-forest uppercase tracking-wider mb-2">Event Objective</h4>
                    <p class="text-charcoal leading-relaxed">
                        <?= e($event['objective']) ?>
                    </p>
                </div>
            </div>

            <!-- Recent Participants Table -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30 flex justify-between items-center">
                    <h3 class="text-lg font-serif font-bold text-forest">Recent Participants</h3>
                </div>
                
                <!-- Desktop Table -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-ivory/10 text-charcoal-light text-sm border-b border-charcoal/10">
                                <th class="px-6 py-3 font-medium">Participant</th>
                                <th class="px-6 py-3 font-medium">Email</th>
                                <th class="px-6 py-3 font-medium">Registered On</th>
                                <th class="px-6 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-charcoal/5">
                            <?php foreach ($participants as $p): ?>
                            <tr class="hover:bg-ivory/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-charcoal"><?= e($p['name']) ?></td>
                                <td class="px-6 py-4 text-sm text-charcoal-light"><?= e($p['email']) ?></td>
                                <td class="px-6 py-4 text-sm text-charcoal-light"><?= e($p['date']) ?></td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-50 text-green-700">
                                        <?= e($p['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Stacked Layout -->
                <div class="block sm:hidden divide-y divide-charcoal/10">
                    <?php foreach ($participants as $p): ?>
                    <div class="p-4 space-y-2">
                        <div class="flex justify-between items-start">
                            <span class="text-sm font-medium text-charcoal"><?= e($p['name']) ?></span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-50 text-green-700">
                                <?= e($p['status']) ?>
                            </span>
                        </div>
                        <div class="text-xs text-charcoal-light"><?= e($p['email']) ?></div>
                        <div class="text-xs text-charcoal-light">Registered: <?= e($p['date']) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="px-6 py-4 bg-ivory/10 border-t border-charcoal/10">
                    <p class="text-sm text-charcoal-light text-center sm:text-left">
                        Showing <?= count($participants) ?> of <?= e($event['registered']) ?> registered participants
                    </p>
                </div>
            </div>

        </div>

        <!-- Right Column: Sidebar Stats & Actions -->
        <div class="flex flex-col gap-6">
            
            <!-- Participation Overview -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h3 class="text-lg font-serif font-bold text-forest">Participation Overview</h3>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-end mb-6">
                        <div class="text-center">
                            <p class="text-3xl font-bold text-forest"><?= e($event['registered']) ?></p>
                            <p class="text-xs font-medium text-charcoal-light uppercase mt-1">Registered</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-charcoal"><?= e($event['capacity']) ?></p>
                            <p class="text-xs font-medium text-charcoal-light uppercase mt-1">Capacity</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gold"><?= $progress ?>%</p>
                            <p class="text-xs font-medium text-charcoal-light uppercase mt-1">Filled</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="w-full bg-ivory rounded-full h-2.5 border border-charcoal/10">
                            <div class="bg-forest h-2.5 rounded-full" style="width: <?= min(100, $progress) ?>%"></div>
                        </div>
                    </div>
                    <p class="text-sm text-charcoal-light text-center">
                        <span class="font-medium text-charcoal"><?= e($event['registered']) ?></span> of <span class="font-medium text-charcoal"><?= e($event['capacity']) ?></span> participant spots are currently registered.
                    </p>
                </div>
            </div>
            
            <!-- Organized By -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h3 class="text-lg font-serif font-bold text-forest">Organized By</h3>
                </div>
                <div class="p-6">
                    <h4 class="text-lg font-bold text-charcoal mb-4"><?= e($ngo['name']) ?></h4>
                    <dl class="space-y-3 mb-6">
                        <div>
                            <dt class="text-xs font-medium text-charcoal-light">Category</dt>
                            <dd class="text-sm text-charcoal"><?= e($ngo['category']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-charcoal-light">Location</dt>
                            <dd class="text-sm text-charcoal"><?= e($ngo['location']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-charcoal-light">Status</dt>
                            <dd class="text-sm font-medium text-green-700"><?= e($ngo['status']) ?></dd>
                        </div>
                    </dl>
                    <a href="<?= baseUrl('admin/ngos/view.php?id=1') ?>" class="w-full inline-flex justify-center items-center px-4 py-2 border border-forest text-forest hover:bg-forest hover:text-white text-sm font-medium rounded-md transition-colors">
                        View NGO
                    </a>
                </div>
            </div>

            <!-- Event Status -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h3 class="text-lg font-serif font-bold text-forest">Event Status</h3>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-xs font-medium text-charcoal-light uppercase mb-1">Current Status</p>
                        <p class="text-lg font-bold text-charcoal" id="statusCardTitle">Upcoming</p>
                    </div>
                    <p id="statusDescription" class="text-sm text-charcoal leading-relaxed mb-6">
                        This event is scheduled and currently accepting participants.
                    </p>
                    
                    <button type="button" id="btnToggleStatusInit" class="w-full px-4 py-2 bg-white border border-red-200 text-red-600 hover:bg-red-50 font-medium rounded-md transition-colors text-sm" data-current-status="Upcoming">
                        Suspend Event
                    </button>
                </div>
            </div>
            
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
        const statusCardTitle = document.getElementById('statusCardTitle');
        const statusDescription = document.getElementById('statusDescription');
        
        const alertContainer = document.getElementById('alertContainer');
        const alertIcon = document.getElementById('alertIcon');
        const alertMessage = document.getElementById('alertMessage');
        
        let pendingAction = '';

        btnToggleStatusInit.addEventListener('click', function() {
            const currentStatus = this.getAttribute('data-current-status');
            
            if (currentStatus === 'Upcoming') {
                pendingAction = 'Suspend';
                
                modalTitle.textContent = 'Suspend Event?';
                modalTitle.className = 'text-lg leading-6 font-medium font-serif text-red-800';
                modalDesc.textContent = "Are you sure you want to suspend this event?";
                
                modalIconContainer.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10';
                modalIcon.className.baseVal = 'h-6 w-6 text-red-600';
                modalIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
                
                btnConfirmStatus.className = 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors';
                btnConfirmStatus.textContent = 'Suspend Event';
            } else {
                pendingAction = 'Activate';
                
                modalTitle.textContent = 'Activate Event?';
                modalTitle.className = 'text-lg leading-6 font-medium font-serif text-green-800';
                modalDesc.textContent = "Are you sure you want to activate this event?";
                
                modalIconContainer.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10';
                modalIcon.className.baseVal = 'h-6 w-6 text-green-600';
                modalIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
                
                btnConfirmStatus.className = 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors';
                btnConfirmStatus.textContent = 'Activate Event';
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
                headerStatusBadge.className = 'inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-red-100 text-red-800 border border-red-200';
                
                statusCardTitle.textContent = 'Suspended';
                statusDescription.textContent = 'This event has been suspended and is hidden from public view.';
                
                btnToggleStatusInit.setAttribute('data-current-status', 'Suspended');
                btnToggleStatusInit.textContent = 'Activate Event';
                btnToggleStatusInit.className = 'w-full px-4 py-2 bg-white border border-green-200 text-green-700 hover:bg-green-50 font-medium rounded-md transition-colors text-sm';
                
                showAlert('Event suspended successfully.', 'error');
            } else {
                headerStatusBadge.textContent = 'Upcoming';
                headerStatusBadge.className = 'inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200';
                
                statusCardTitle.textContent = 'Upcoming';
                statusDescription.textContent = 'This event is scheduled and currently accepting participants.';
                
                btnToggleStatusInit.setAttribute('data-current-status', 'Upcoming');
                btnToggleStatusInit.textContent = 'Suspend Event';
                btnToggleStatusInit.className = 'w-full px-4 py-2 bg-white border border-red-200 text-red-600 hover:bg-red-50 font-medium rounded-md transition-colors text-sm';
                
                showAlert('Event activated successfully.', 'success');
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
