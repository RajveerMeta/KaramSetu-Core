<?php
require_once __DIR__ . '/../../includes/functions.php';

$users = [
    [
        'id' => 1,
        'name' => 'Rajveer Meta',
        'email' => 'rajveer@example.com',
        'registered' => '12 Jan 2026',
        'contributions' => 18,
        'karma_points' => '1,280',
        'status' => 'Active'
    ],
    [
        'id' => 2,
        'name' => 'Ananya Patel',
        'email' => 'ananya@example.com',
        'registered' => '28 Feb 2026',
        'contributions' => 14,
        'karma_points' => '1,050',
        'status' => 'Active'
    ],
    [
        'id' => 3,
        'name' => 'Aarav Shah',
        'email' => 'aarav@example.com',
        'registered' => '05 Mar 2026',
        'contributions' => 11,
        'karma_points' => '920',
        'status' => 'Active'
    ],
    [
        'id' => 4,
        'name' => 'Dev Mehta',
        'email' => 'dev@example.com',
        'registered' => '18 Mar 2026',
        'contributions' => 9,
        'karma_points' => '780',
        'status' => 'Active'
    ],
    [
        'id' => 5,
        'name' => 'Priya Joshi',
        'email' => 'priya@example.com',
        'registered' => '02 Apr 2026',
        'contributions' => 7,
        'karma_points' => '640',
        'status' => 'Active'
    ],
    [
        'id' => 6,
        'name' => 'Rohan Desai',
        'email' => 'rohan@example.com',
        'registered' => '11 Apr 2026',
        'contributions' => 4,
        'karma_points' => '320',
        'status' => 'Suspended'
    ],
    [
        'id' => 7,
        'name' => 'Neha Patel',
        'email' => 'neha@example.com',
        'registered' => '23 May 2026',
        'contributions' => 6,
        'karma_points' => '510',
        'status' => 'Active'
    ],
    [
        'id' => 8,
        'name' => 'Kunal Shah',
        'email' => 'kunal@example.com',
        'registered' => '08 Jun 2026',
        'contributions' => 2,
        'karma_points' => '180',
        'status' => 'Suspended'
    ]
];

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-serif font-bold text-forest mb-2">User Management</h1>
            <p class="text-charcoal-light max-w-2xl">View registered users, monitor account activity, and manage user access.</p>
        </div>
        
    </div>

    <!-- Alert Container -->
    <div id="alertContainer" class="hidden mb-6 p-4 rounded-lg flex items-center">
        <svg id="alertIcon" class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>
        <span id="alertMessage" class="text-sm font-medium"></span>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Total Users</p>
            <p class="text-2xl font-bold text-charcoal mt-1">1,248</p>
        </div>
        <div class="bg-white rounded-xl border border-green-200/50 shadow-sm p-5 border-l-4 border-green-500">
            <p class="text-sm font-medium text-charcoal-light">Active Users</p>
            <p class="text-2xl font-bold text-green-700 mt-1">1,176</p>
        </div>
        <div class="bg-white rounded-xl border border-blue-200/50 shadow-sm p-5 border-l-4 border-blue-500">
            <p class="text-sm font-medium text-charcoal-light">New This Month</p>
            <p class="text-2xl font-bold text-blue-700 mt-1">42</p>
        </div>
        <div class="bg-white rounded-xl border border-red-200/50 shadow-sm p-5 border-l-4 border-red-500">
            <p class="text-sm font-medium text-charcoal-light">Suspended</p>
            <p class="text-2xl font-bold text-red-700 mt-1">30</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-end space-y-4 md:space-y-0 md:space-x-4">
            <div class="flex-grow">
                <label for="searchUser" class="block text-xs font-medium text-charcoal-light mb-1">Search</label>
                <input type="text" id="searchUser" placeholder="Search by name or email..." class="w-full px-4 py-2 border border-charcoal/20 rounded-md focus:outline-none focus:ring-1 focus:ring-forest text-sm">
            </div>
            <div class="w-full md:w-48">
                <label for="filterStatus" class="block text-xs font-medium text-charcoal-light mb-1">Status</label>
                <select id="filterStatus" class="w-full px-4 py-2 border border-charcoal/20 rounded-md focus:outline-none focus:ring-1 focus:ring-forest text-sm bg-white">
                    <option value="">All Status</option>
                    <option value="Active">Active</option>
                    <option value="Suspended">Suspended</option>
                </select>
            </div>
            <div class="w-full md:w-48">
                <label for="filterRegistration" class="block text-xs font-medium text-charcoal-light mb-1">Registration</label>
                <select id="filterRegistration" class="w-full px-4 py-2 border border-charcoal/20 rounded-md focus:outline-none focus:ring-1 focus:ring-forest text-sm bg-white">
                    <option value="">All Time</option>
                    <option value="This Month">This Month</option>
                    <option value="Last 3 Months">Last 3 Months</option>
                    <option value="This Year">This Year</option>
                </select>
            </div>
            <div class="flex space-x-2 w-full md:w-auto">
                <button type="button" id="btnSearch" class="flex-1 md:flex-none px-4 py-2 bg-forest text-white text-sm font-medium rounded-md hover:bg-forest-dark transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-forest">
                    Search
                </button>
                <button type="button" id="btnClear" class="flex-1 md:flex-none px-4 py-2 bg-ivory-dark text-charcoal text-sm font-medium rounded-md hover:bg-gold/20 border border-charcoal/10 transition-colors focus:outline-none">
                    Clear
                </button>
            </div>
        </div>
    </div>

    <!-- User Table -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="userTable">
                <thead>
                    <tr class="bg-ivory/50 text-charcoal-light text-sm border-b border-charcoal/10">
                        <th class="px-6 py-4 font-medium">User</th>
                        <th class="px-6 py-4 font-medium">Registered</th>
                        <th class="px-6 py-4 font-medium text-center">Contributions</th>
                        <th class="px-6 py-4 font-medium text-center">Karma Points</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-charcoal/5">
                    <?php foreach ($users as $user): ?>
                    <?php 
                        $statusClass = '';
                        $actionLink = baseUrl('admin/users/view.php?id=' . $user['id']);
                        
                        $nameParts = explode(' ', $user['name']);
                        $initials = '';
                        foreach ($nameParts as $part) {
                            if (!empty($part)) {
                                $initials .= strtoupper(substr($part, 0, 1));
                            }
                            if (strlen($initials) >= 2) break;
                        }
                        
                        if ($user['status'] === 'Active') {
                            $statusClass = 'bg-green-100 text-green-800 border-green-200';
                        } elseif ($user['status'] === 'Suspended') {
                            $statusClass = 'bg-red-100 text-red-800 border-red-200';
                        }
                    ?>
                    <tr class="hover:bg-ivory/30 transition-colors user-row" 
                        data-id="<?= $user['id'] ?>"
                        data-name="<?= strtolower(e($user['name'])) ?>" 
                        data-email="<?= strtolower(e($user['email'])) ?>"
                        data-status="<?= e($user['status']) ?>">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-forest text-white flex items-center justify-center font-serif font-bold text-sm">
                                    <?= e($initials) ?>
                                </div>
                                <div>
                                    <div class="font-medium text-charcoal"><?= e($user['name']) ?></div>
                                    <a href="mailto:<?= e($user['email']) ?>" class="text-sm text-forest hover:text-gold transition-colors"><?= e($user['email']) ?></a>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-charcoal-light">
                            <?= e($user['registered']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-center font-medium text-charcoal">
                            <?= e($user['contributions']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-center font-medium text-forest">
                            <?= e($user['karma_points']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm status-cell">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= $statusClass ?>">
                                <?= e($user['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-right action-cell">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="<?= $actionLink ?>" class="inline-flex justify-center items-center px-3 py-1.5 border border-forest text-forest hover:bg-forest hover:text-white text-xs font-medium rounded-md transition-colors">
                                    View
                                </a>
                                <?php if ($user['status'] === 'Active'): ?>
                                <button type="button" class="btn-toggle-status inline-flex justify-center items-center px-3 py-1.5 border border-red-200 text-red-600 hover:bg-red-50 text-xs font-medium rounded-md transition-colors" data-action="Suspend">
                                    Suspend
                                </button>
                                <?php else: ?>
                                <button type="button" class="btn-toggle-status inline-flex justify-center items-center px-3 py-1.5 border border-green-200 text-green-700 hover:bg-green-50 text-xs font-medium rounded-md transition-colors" data-action="Activate">
                                    Activate
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Empty State (Hidden by default) -->
        <div id="emptyState" class="hidden py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-charcoal-light mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <h3 class="text-lg font-medium text-charcoal">No users found</h3>
            <p class="text-sm text-charcoal-light mt-1">Try changing your search or filter criteria.</p>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between pt-2 pb-6">
        <div class="flex flex-col sm:flex-row flex-wrap sm:items-center justify-between gap-4 w-full">
            <div>
                <p class="text-sm text-charcoal-light">
                    Showing <span class="font-medium text-charcoal">1</span> to <span class="font-medium text-charcoal">8</span> of <span class="font-medium text-charcoal">1,248</span> results
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex flex-wrap rounded-md shadow-sm -space-x-px justify-center" aria-label="Pagination">
                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-charcoal/20 bg-white text-sm font-medium text-charcoal-light hover:bg-ivory transition-colors">
                        <span class="sr-only">Previous</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" aria-current="page" class="z-10 bg-forest/5 border-forest text-forest relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                        1
                    </a>
                    <a href="#" class="bg-white border-charcoal/20 text-charcoal-light hover:bg-ivory relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors">
                        2
                    </a>
                    <a href="#" class="bg-white border-charcoal/20 text-charcoal-light hover:bg-ivory relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors">
                        3
                    </a>
                    <span class="relative inline-flex items-center px-4 py-2 border border-charcoal/20 bg-white text-sm font-medium text-charcoal-light">
                        ...
                    </span>
                    <a href="#" class="bg-white border-charcoal/20 text-charcoal-light hover:bg-ivory relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors">
                        156
                    </a>
                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-charcoal/20 bg-white text-sm font-medium text-charcoal-light hover:bg-ivory transition-colors">
                        <span class="sr-only">Next</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </nav>
            </div>
        </div>
        <div class="flex items-center justify-between w-full sm:hidden mt-4">
            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-charcoal/20 text-sm font-medium rounded-md text-charcoal bg-white hover:bg-ivory">
                Previous
            </a>
            <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-charcoal/20 text-sm font-medium rounded-md text-charcoal bg-white hover:bg-ivory">
                Next
            </a>
        </div>
    </div>
</div>

<!-- Status Toggle Modal -->
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
        const searchInput = document.getElementById('searchUser');
        const filterStatus = document.getElementById('filterStatus');
        const filterRegistration = document.getElementById('filterRegistration'); // purely visual for now
        const btnSearch = document.getElementById('btnSearch');
        const btnClear = document.getElementById('btnClear');
        const rows = document.querySelectorAll('.user-row');
        const emptyState = document.getElementById('emptyState');

        
        
        

        const statusModal = document.getElementById('statusModal');
        const btnConfirmStatus = document.getElementById('btnConfirmStatus');
        
        let targetRow = null;
        let targetAction = '';

        document.querySelectorAll('.btn-toggle-status').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                targetRow = this.closest('tr');
                targetAction = this.getAttribute('data-action');
                
                const modalTitle = document.getElementById('modalTitle');
                const modalDesc = document.getElementById('modalDescription');
                const modalIcon = document.getElementById('modalIcon');
                const modalIconContainer = document.getElementById('modalIconContainer');
                
                if (targetAction === 'Suspend') {
                    modalTitle.textContent = 'Suspend this user?';
                    modalTitle.className = 'text-lg leading-6 font-medium font-serif text-red-800';
                    modalDesc.textContent = "The user's account will be marked as suspended.";
                    modalIconContainer.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10';
                    modalIcon.className.baseVal = 'h-6 w-6 text-red-600';
                    modalIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
                    
                    btnConfirmStatus.className = 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors';
                    btnConfirmStatus.textContent = 'Suspend';
                } else {
                    modalTitle.textContent = 'Activate this user?';
                    modalTitle.className = 'text-lg leading-6 font-medium font-serif text-green-800';
                    modalDesc.textContent = "The user's account will be activated and granted full access.";
                    modalIconContainer.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10';
                    modalIcon.className.baseVal = 'h-6 w-6 text-green-600';
                    modalIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
                    
                    btnConfirmStatus.className = 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors';
                    btnConfirmStatus.textContent = 'Activate';
                }
                
                statusModal.classList.remove('hidden');
            });
        });

        document.getElementById('btnCancelStatus').addEventListener('click', () => {
            statusModal.classList.add('hidden');
        });
        
        document.getElementById('statusOverlay').addEventListener('click', () => {
            statusModal.classList.add('hidden');
        });
        
        btnConfirmStatus.addEventListener('click', () => {
            statusModal.classList.add('hidden');
            
            if (targetRow && targetAction) {
                const statusCell = targetRow.querySelector('.status-cell');
                const button = targetRow.querySelector('.btn-toggle-status');
                
                if (targetAction === 'Suspend') {
                    targetRow.setAttribute('data-status', 'Suspended');
                    statusCell.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-red-100 text-red-800 border-red-200">Suspended</span>';
                    
                    button.setAttribute('data-action', 'Activate');
                    button.textContent = 'Activate';
                    button.className = 'btn-toggle-status inline-flex justify-center items-center px-3 py-1.5 border border-green-200 text-green-700 hover:bg-green-50 text-xs font-medium rounded-md transition-colors';
                    
                    showAlert('User account suspended successfully.', 'error');
                } else {
                    targetRow.setAttribute('data-status', 'Active');
                    statusCell.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-green-100 text-green-800 border-green-200">Active</span>';
                    
                    button.setAttribute('data-action', 'Suspend');
                    button.textContent = 'Suspend';
                    button.className = 'btn-toggle-status inline-flex justify-center items-center px-3 py-1.5 border border-red-200 text-red-600 hover:bg-red-50 text-xs font-medium rounded-md transition-colors';
                    
                    showAlert('User account activated successfully.', 'success');
                }
                
                
            }
        });

        function showAlert(message, type) {
            const container = document.getElementById('alertContainer');
            const icon = document.getElementById('alertIcon');
            const msg = document.getElementById('alertMessage');
            
            msg.textContent = message;
            
            if (type === 'success') {
                container.className = 'mb-6 p-4 rounded-lg flex items-center bg-green-50 border border-green-200';
                icon.className.baseVal = 'w-5 h-5 mr-3 text-green-500';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
                msg.className = 'text-sm font-medium text-green-800';
            } else {
                container.className = 'mb-6 p-4 rounded-lg flex items-center bg-amber-50 border border-amber-200';
                icon.className.baseVal = 'w-5 h-5 mr-3 text-amber-500';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
                msg.className = 'text-sm font-medium text-amber-800';
            }
            
            container.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/admin.php';
?>

