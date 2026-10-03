<?php
require_once __DIR__ . '/../../includes/functions.php';

$ngos = [
    [
        'id' => 1,
        'name' => 'Seva Roots Initiative',
        'location' => 'Rajkot',
        'email' => 'contact@sevaroots.org',
        'registered' => '22 Sep 2026',
        'status' => 'Pending'
    ],
    [
        'id' => 2,
        'name' => 'Helping Hands Foundation',
        'location' => 'Ahmedabad',
        'email' => 'hello@helpinghands.org',
        'registered' => '21 Sep 2026',
        'status' => 'Pending'
    ],
    [
        'id' => 3,
        'name' => 'Udaan Community Trust',
        'location' => 'Surat',
        'email' => 'info@udaantrust.org',
        'registered' => '20 Sep 2026',
        'status' => 'Pending'
    ],
    [
        'id' => 4,
        'name' => 'Sarthak Seva Foundation',
        'location' => 'Vadodara',
        'email' => 'contact@sarthakseva.org',
        'registered' => '18 Sep 2026',
        'status' => 'Approved'
    ],
    [
        'id' => 5,
        'name' => 'Jeevan Jyoti Trust',
        'location' => 'Rajkot',
        'email' => 'info@jeevanjyoti.org',
        'registered' => '15 Sep 2026',
        'status' => 'Approved'
    ],
    [
        'id' => 6,
        'name' => 'Asha Community Foundation',
        'location' => 'Ahmedabad',
        'email' => 'hello@ashafoundation.org',
        'registered' => '12 Sep 2026',
        'status' => 'Approved'
    ],
    [
        'id' => 7,
        'name' => 'Nayi Disha Welfare Trust',
        'location' => 'Surat',
        'email' => 'contact@nayidisha.org',
        'registered' => '09 Sep 2026',
        'status' => 'Rejected'
    ],
    [
        'id' => 8,
        'name' => 'Jan Kalyan Initiative',
        'location' => 'Vadodara',
        'email' => 'info@jankalyan.org',
        'registered' => '05 Sep 2026',
        'status' => 'Suspended'
    ],
];

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-serif font-bold text-forest mb-2">NGO Management</h1>
            <p class="text-charcoal-light max-w-2xl">Review registered organizations, monitor their status, and manage NGO accounts.</p>
        </div>
       
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Total NGOs</p>
            <p class="text-2xl font-bold text-charcoal mt-1">86</p>
        </div>
        <div class="bg-white rounded-xl border border-amber-200/50 shadow-sm p-5 border-l-4 border-amber-400">
            <p class="text-sm font-medium text-charcoal-light">Pending</p>
            <p class="text-2xl font-bold text-amber-700 mt-1">12</p>
        </div>
        <div class="bg-white rounded-xl border border-green-200/50 shadow-sm p-5 border-l-4 border-green-500">
            <p class="text-sm font-medium text-charcoal-light">Approved</p>
            <p class="text-2xl font-bold text-green-700 mt-1">68</p>
        </div>
        <div class="bg-white rounded-xl border border-red-200/50 shadow-sm p-5 border-l-4 border-red-500">
            <p class="text-sm font-medium text-charcoal-light">Rejected</p>
            <p class="text-2xl font-bold text-red-700 mt-1">6</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-end space-y-4 md:space-y-0 md:space-x-4">
            <div class="flex-grow">
                <label for="searchNGO" class="block text-xs font-medium text-charcoal-light mb-1">Search</label>
                <input type="text" id="searchNGO" placeholder="Search NGO by name..." class="w-full px-4 py-2 border border-charcoal/20 rounded-md focus:outline-none focus:ring-1 focus:ring-forest text-sm">
            </div>
            <div class="w-full md:w-48">
                <label for="filterStatus" class="block text-xs font-medium text-charcoal-light mb-1">Status</label>
                <select id="filterStatus" class="w-full px-4 py-2 border border-charcoal/20 rounded-md focus:outline-none focus:ring-1 focus:ring-forest text-sm bg-white">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Suspended">Suspended</option>
                </select>
            </div>
            <div class="w-full md:w-48">
                <label for="filterLocation" class="block text-xs font-medium text-charcoal-light mb-1">Location</label>
                <select id="filterLocation" class="w-full px-4 py-2 border border-charcoal/20 rounded-md focus:outline-none focus:ring-1 focus:ring-forest text-sm bg-white">
                    <option value="">All Locations</option>
                    <option value="Rajkot">Rajkot</option>
                    <option value="Ahmedabad">Ahmedabad</option>
                    <option value="Surat">Surat</option>
                    <option value="Vadodara">Vadodara</option>
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

    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="ngoTable">
                <thead>
                    <tr class="bg-ivory/50 text-charcoal-light text-sm border-b border-charcoal/10">
                        <th class="px-6 py-4 font-medium">NGO</th>
                        <th class="px-6 py-4 font-medium">Location</th>
                        <th class="px-6 py-4 font-medium">Contact</th>
                        <th class="px-6 py-4 font-medium">Registered</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-charcoal/5">
                    <?php foreach ($ngos as $ngo): ?>
                    <?php 
                        $statusClass = '';
                        $actionLabel = 'View';
                        $actionLink = baseUrl('admin/ngos/view.php?id=' . $ngo['id']);
                        
                        if ($ngo['status'] === 'Pending') {
                            $statusClass = 'bg-amber-100 text-amber-800 border-amber-200';
                            $actionLabel = 'Review';
                        } elseif ($ngo['status'] === 'Approved') {
                            $statusClass = 'bg-green-100 text-green-800 border-green-200';
                        } elseif ($ngo['status'] === 'Rejected') {
                            $statusClass = 'bg-red-100 text-red-800 border-red-200';
                        } elseif ($ngo['status'] === 'Suspended') {
                            $statusClass = 'bg-gray-100 text-gray-800 border-gray-200';
                        }
                    ?>
                    <tr class="hover:bg-ivory/30 transition-colors ngo-row" 
                        data-name="<?= strtolower(e($ngo['name'])) ?>" 
                        data-status="<?= e($ngo['status']) ?>" 
                        data-location="<?= e($ngo['location']) ?>">
                        <td class="px-6 py-4">
                            <div class="font-medium text-charcoal"><?= e($ngo['name']) ?></div>
                        </td>
                        <td class="px-6 py-4 text-sm text-charcoal-light">
                            <?= e($ngo['location']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="mailto:<?= e($ngo['email']) ?>" class="text-forest hover:text-gold transition-colors"><?= e($ngo['email']) ?></a>
                        </td>
                        <td class="px-6 py-4 text-sm text-charcoal-light">
                            <?= e($ngo['registered']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= $statusClass ?>">
                                <?= e($ngo['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-right">
                            <a href="<?= $actionLink ?>" class="inline-flex justify-center items-center px-3 py-1.5 border border-forest text-forest hover:bg-forest hover:text-white text-xs font-medium rounded-md transition-colors">
                                <?= e($actionLabel) ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Empty State (Hidden by default) -->
        <div id="emptyState" class="hidden py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-charcoal-light mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-lg font-medium text-charcoal">No NGOs found</h3>
            <p class="text-sm text-charcoal-light mt-1">Try adjusting your search or filters.</p>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between pt-2 pb-6">
        <div class="flex flex-col sm:flex-row flex-wrap sm:items-center justify-between gap-4 w-full">
            <div>
                <p class="text-sm text-charcoal-light">
                    Showing <span class="font-medium text-charcoal">1</span> to <span class="font-medium text-charcoal">8</span> of <span class="font-medium text-charcoal">86</span> results
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex flex-wrap rounded-md shadow-sm -space-x-px justify-center" aria-label="Pagination">
                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-charcoal/20 bg-white text-sm font-medium text-charcoal-light hover:bg-ivory transition-colors">
                        <span class="sr-only">Previous</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
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
                        11
                    </a>
                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-charcoal/20 bg-white text-sm font-medium text-charcoal-light hover:bg-ivory transition-colors">
                        <span class="sr-only">Next</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
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
        const searchInput = document.getElementById('searchNGO');
        const filterStatus = document.getElementById('filterStatus');
        const filterLocation = document.getElementById('filterLocation');
        const btnSearch = document.getElementById('btnSearch');
        const btnClear = document.getElementById('btnClear');
        const rows = document.querySelectorAll('.ngo-row');
        const emptyState = document.getElementById('emptyState');

        
        
        
    });
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/admin.php';
?>

