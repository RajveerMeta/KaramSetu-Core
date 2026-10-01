<?php
require_once __DIR__ . '/../../includes/functions.php';

$contributions = [
    [
        'id' => 1,
        'contributor' => 'Rajveer Meta',
        'contribution' => 'Community Food Support',
        'type' => 'Money',
        'ngo' => 'Seva Roots Initiative',
        'date' => '24 Sep 2026',
        'amount' => '₹5,000',
        'status' => 'Completed'
    ],
    [
        'id' => 2,
        'contributor' => 'Aarav Shah',
        'contribution' => 'Food Supplies',
        'type' => 'Resource',
        'ngo' => 'Seva Roots Initiative',
        'date' => '23 Sep 2026',
        'amount' => '25 Food Kits',
        'status' => 'Completed'
    ],
    [
        'id' => 3,
        'contributor' => 'Ananya Patel',
        'contribution' => 'School Books',
        'type' => 'Item',
        'ngo' => 'Helping Hands Foundation',
        'date' => '22 Sep 2026',
        'amount' => '40 Books',
        'status' => 'Completed'
    ],
    [
        'id' => 4,
        'contributor' => 'Dev Mehta',
        'contribution' => 'Volunteer Support',
        'type' => 'Time',
        'ngo' => 'Udaan Community Trust',
        'date' => '21 Sep 2026',
        'amount' => '6 Hours',
        'status' => 'Completed'
    ],
    [
        'id' => 5,
        'contributor' => 'Krisha Joshi',
        'contribution' => 'Education Support',
        'type' => 'Money',
        'ngo' => 'Helping Hands Foundation',
        'date' => '20 Sep 2026',
        'amount' => '₹10,000',
        'status' => 'Completed'
    ],
    [
        'id' => 6,
        'contributor' => 'Yash Trivedi',
        'contribution' => 'Medical Supplies',
        'type' => 'Resource',
        'ngo' => 'Nayi Disha Welfare Trust',
        'date' => '19 Sep 2026',
        'amount' => '15 Supply Kits',
        'status' => 'Completed'
    ],
    [
        'id' => 7,
        'contributor' => 'Meera Shah',
        'contribution' => 'Community Kitchen',
        'type' => 'Money',
        'ngo' => 'Seva Roots Initiative',
        'date' => '18 Sep 2026',
        'amount' => '₹2,500',
        'status' => 'Pending'
    ],
    [
        'id' => 8,
        'contributor' => 'Harsh Patel',
        'contribution' => 'Tree Plantation',
        'type' => 'Time',
        'ngo' => 'Jeevan Jyoti Trust',
        'date' => '17 Sep 2026',
        'amount' => '4 Hours',
        'status' => 'Completed'
    ],
    [
        'id' => 9,
        'contributor' => 'Riya Mehta',
        'contribution' => 'Clothing Donation',
        'type' => 'Item',
        'ngo' => 'Asha Community Foundation',
        'date' => '16 Sep 2026',
        'amount' => '30 Clothing Sets',
        'status' => 'Completed'
    ],
    [
        'id' => 10,
        'contributor' => 'Kunal Joshi',
        'contribution' => 'Food Donation',
        'type' => 'Money',
        'ngo' => 'Seva Roots Initiative',
        'date' => '15 Sep 2026',
        'amount' => '₹7,500',
        'status' => 'Completed'
    ]
];

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-serif font-bold text-forest mb-2">Contributions Management</h1>
            <p class="text-charcoal-light max-w-2xl">Review donations, resources, items, and volunteer contributions across the platform.</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Total Contributions</p>
            <p class="text-2xl font-bold text-charcoal mt-1">24</p>
        </div>
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Money Contributions</p>
            <p class="text-2xl font-bold text-forest mt-1">₹52,500</p>
        </div>
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Items & Resources</p>
            <p class="text-2xl font-bold text-charcoal mt-1">12</p>
        </div>
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Volunteer Contributions</p>
            <p class="text-2xl font-bold text-charcoal mt-1">8</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-2 lg:col-span-4">
                <label for="searchContribution" class="block text-xs font-medium text-charcoal-light mb-1">Search</label>
                <input type="text" id="searchContribution" placeholder="Search contributor, NGO, or contribution..." class="w-full px-4 py-2 border border-charcoal/20 rounded-md focus:outline-none focus:ring-1 focus:ring-forest text-sm">
            </div>
            <div class="lg:col-span-2">
                <label for="filterType" class="block text-xs font-medium text-charcoal-light mb-1">Contribution Type</label>
                <select id="filterType" class="w-full px-4 py-2 border border-charcoal/20 rounded-md focus:outline-none focus:ring-1 focus:ring-forest text-sm bg-white">
                    <option value="">All Types</option>
                    <option value="Money">Money</option>
                    <option value="Resource">Resource</option>
                    <option value="Item">Item</option>
                    <option value="Time">Time</option>
                </select>
            </div>
            <div class="lg:col-span-2">
                <label for="filterStatus" class="block text-xs font-medium text-charcoal-light mb-1">Status</label>
                <select id="filterStatus" class="w-full px-4 py-2 border border-charcoal/20 rounded-md focus:outline-none focus:ring-1 focus:ring-forest text-sm bg-white">
                    <option value="">All Status</option>
                    <option value="Completed">Completed</option>
                    <option value="Pending">Pending</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <div class="lg:col-span-2">
                <label for="filterDate" class="block text-xs font-medium text-charcoal-light mb-1">Date</label>
                <select id="filterDate" class="w-full px-4 py-2 border border-charcoal/20 rounded-md focus:outline-none focus:ring-1 focus:ring-forest text-sm bg-white">
                    <option value="">All Dates</option>
                    <option value="Today">Today</option>
                    <option value="This Week">This Week</option>
                    <option value="This Month">This Month</option>
                </select>
            </div>
            <div class="md:col-span-2 lg:col-span-2 flex space-x-2">
                <button type="button" id="btnSearch" class="flex-1 px-4 py-2 bg-forest text-white text-sm font-medium rounded-md hover:bg-forest-dark transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-forest">
                    Search
                </button>
                <button type="button" id="btnClear" class="flex-1 px-4 py-2 bg-ivory-dark text-charcoal text-sm font-medium rounded-md hover:bg-gold/20 border border-charcoal/10 transition-colors focus:outline-none">
                    Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Contributions Table -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30 flex justify-between items-center">
            <h3 class="text-lg font-serif font-bold text-forest">Recent Contributions</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="contributionsTable">
                <thead>
                    <tr class="bg-ivory/10 text-charcoal-light text-sm border-b border-charcoal/10">
                        <th class="px-4 lg:px-6 py-4 font-medium min-w-[150px]">Contributor</th>
                        <th class="px-4 lg:px-6 py-4 font-medium min-w-[200px]">Contribution</th>
                        <th class="px-4 py-4 font-medium">Type</th>
                        <th class="px-4 lg:px-6 py-4 font-medium min-w-[180px]">NGO / Cause</th>
                        <th class="px-4 py-4 font-medium min-w-[120px]">Date</th>
                        <th class="px-4 py-4 font-medium min-w-[150px]">Amount / Details</th>
                        <th class="px-4 py-4 font-medium">Status</th>
                        <th class="px-4 lg:px-6 py-4 font-medium text-right min-w-[100px]">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-charcoal/5">
                    <?php foreach ($contributions as $contrib): ?>
                    <?php 
                        $typeClass = '';
                        if ($contrib['type'] === 'Money') {
                            $typeClass = 'bg-green-50 text-green-700 border-green-200';
                        } elseif ($contrib['type'] === 'Resource') {
                            $typeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                        } elseif ($contrib['type'] === 'Item') {
                            $typeClass = 'bg-purple-50 text-purple-700 border-purple-200';
                        } elseif ($contrib['type'] === 'Time') {
                            $typeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                        }
                        
                        $statusClass = '';
                        if ($contrib['status'] === 'Completed') {
                            $statusClass = 'bg-green-100 text-green-800 border-green-200';
                        } elseif ($contrib['status'] === 'Pending') {
                            $statusClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                        } elseif ($contrib['status'] === 'Cancelled') {
                            $statusClass = 'bg-red-100 text-red-800 border-red-200';
                        }
                        
                        $actionLink = baseUrl('admin/contributions/view.php?id=' . $contrib['id']);
                    ?>
                    <tr class="hover:bg-ivory/30 transition-colors contribution-row" 
                        data-contributor="<?= strtolower(e($contrib['contributor'])) ?>" 
                        data-contribution="<?= strtolower(e($contrib['contribution'])) ?>"
                        data-ngo="<?= strtolower(e($contrib['ngo'])) ?>"
                        data-type="<?= e($contrib['type']) ?>"
                        data-status="<?= e($contrib['status']) ?>"
                        data-date="<?= e($contrib['date']) ?>">
                        <td class="px-4 lg:px-6 py-4">
                            <div class="font-medium text-charcoal"><?= e($contrib['contributor']) ?></div>
                        </td>
                        <td class="px-4 lg:px-6 py-4">
                            <div class="text-sm font-medium text-charcoal"><?= e($contrib['contribution']) ?></div>
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border <?= $typeClass ?>">
                                <?= e($contrib['type']) ?>
                            </span>
                        </td>
                        <td class="px-4 lg:px-6 py-4 text-sm text-charcoal-light">
                            <?= e($contrib['ngo']) ?>
                        </td>
                        <td class="px-4 py-4 text-sm text-charcoal-light">
                            <?= e($contrib['date']) ?>
                        </td>
                        <td class="px-4 py-4 text-sm font-medium <?= $contrib['type'] === 'Money' ? 'text-forest' : 'text-charcoal' ?>">
                            <?= e($contrib['amount']) ?>
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= $statusClass ?>">
                                <?= e($contrib['status']) ?>
                            </span>
                        </td>
                        <td class="px-4 lg:px-6 py-4 text-sm text-right">
                            <a href="<?= $actionLink ?>" class="inline-flex justify-center items-center px-3 py-1.5 border border-forest text-forest hover:bg-forest hover:text-white text-xs font-medium rounded-md transition-colors">
                                View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Empty State (Hidden by default) -->
            <div id="emptyState" class="hidden py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-charcoal-light mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="text-lg font-medium text-charcoal">No contributions found.</h3>
                <p class="text-sm text-charcoal-light mt-1">Try adjusting your search or filters.</p>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between pt-2 pb-6">
        <div class="flex flex-col sm:flex-row flex-wrap sm:items-center justify-between gap-4 w-full">
            <div>
                <p class="text-sm text-charcoal-light">
                    Showing <span class="font-medium text-charcoal">1</span> to <span class="font-medium text-charcoal">10</span> of <span class="font-medium text-charcoal">24</span> contributions
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchContribution');
        const filterType = document.getElementById('filterType');
        const filterStatus = document.getElementById('filterStatus');
        const filterDate = document.getElementById('filterDate');
        const btnSearch = document.getElementById('btnSearch');
        const btnClear = document.getElementById('btnClear');
        const rows = document.querySelectorAll('.contribution-row');
        const emptyState = document.getElementById('emptyState');
        const tableHeader = document.querySelector('#contributionsTable thead');

        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const type = filterType.value;
            const status = filterStatus.value;
            
            let visibleCount = 0;

            rows.forEach(row => {
                const rowContributor = row.getAttribute('data-contributor');
                const rowContribution = row.getAttribute('data-contribution');
                const rowNgo = row.getAttribute('data-ngo');
                const rowType = row.getAttribute('data-type');
                const rowStatus = row.getAttribute('data-status');
                
                const matchesSearch = searchTerm === '' || 
                                      rowContributor.includes(searchTerm) || 
                                      rowContribution.includes(searchTerm) || 
                                      rowNgo.includes(searchTerm);
                                      
                const matchesType = type === '' || rowType === type;
                const matchesStatus = status === '' || rowStatus === status;
                
                if (matchesSearch && matchesType && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
                tableHeader.style.display = 'none';
            } else {
                emptyState.classList.add('hidden');
                tableHeader.style.display = '';
            }
        }

        searchInput.addEventListener('input', applyFilters);
        filterType.addEventListener('change', applyFilters);
        filterStatus.addEventListener('change', applyFilters);
        filterDate.addEventListener('change', applyFilters); // Trigger update to show interactivity
        btnSearch.addEventListener('click', applyFilters);
        
        btnClear.addEventListener('click', function() {
            searchInput.value = '';
            filterType.value = '';
            filterStatus.value = '';
            filterDate.value = '';
            applyFilters();
        });
    });
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/admin.php';
?>
