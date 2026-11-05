<?php
require_once __DIR__ . '/../../DataBase/admin/ngos/index.php';

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
            <p class="text-2xl font-bold text-charcoal mt-1"><?php echo count($ngos); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-amber-200/50 shadow-sm p-5 border-l-4 border-amber-400">
            <p class="text-sm font-medium text-charcoal-light">Pending</p>
            <p class="text-2xl font-bold text-amber-700 mt-1"><?php echo $counts['pending']; ?></p>
        </div>
        <div class="bg-white rounded-xl border border-green-200/50 shadow-sm p-5 border-l-4 border-green-500">
            <p class="text-sm font-medium text-charcoal-light">Approved</p>
            <p class="text-2xl font-bold text-green-700 mt-1"><?php echo $counts['approved']; ?></p>
        </div>
        <div class="bg-white rounded-xl border border-red-200/50 shadow-sm p-5 border-l-4 border-red-500">
            <p class="text-sm font-medium text-charcoal-light">Rejected</p>
            <p class="text-2xl font-bold text-red-700 mt-1"><?php echo $counts['rejected']; ?></p>
        </div>
    </div>

    <!-- Filters Section -->
     <form method="GET" action="">
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-end space-y-4 md:space-y-0 md:space-x-4">
            <div class="flex-grow">
                <label for="searchNGO" class="block text-xs font-medium text-charcoal-light mb-1">Search</label>
                <input
                    type="text"
                    id="searchNGO"
                    name="search"
                    value="<?= e($search) ?>"
                    placeholder="Search NGO by name..."
                    class="w-full px-4 py-2 border border-charcoal/20 rounded-md focus:outline-none focus:ring-1 focus:ring-forest text-sm">
                        
            </div>
            <div class="w-full md:w-48">
                <label for="filterStatus" class="block text-xs font-medium text-charcoal-light mb-1">Status</label>
                <select name="status" id="filterStatus"
                    class="w-full px-4 py-2 border border-charcoal/20 rounded-md bg-white text-sm">

                    <option value="">All Status</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>
            </div>
            <div class="w-full md:w-48">
                <label for="filterLocation" class="block text-xs font-medium text-charcoal-light mb-1">Location</label>
                <select name="location" id="filterLocation"
                    class="w-full px-4 py-2 border border-charcoal/20 rounded-md bg-white text-sm">
                
                    <option value="">All Locations</option>
                    <option value="Rajkot" <?= $location === 'Rajkot' ? 'selected' : '' ?>>Rajkot</option>
                    <option value="Ahmedabad" <?= $location === 'Ahmedabad' ? 'selected' : '' ?>>Ahmedabad</option>
                    <option value="Surat" <?= $location === 'Surat' ? 'selected' : '' ?>>Surat</option>
                    <option value="Vadodara" <?= $location === 'Vadodara' ? 'selected' : '' ?>>Vadodara</option>
                </select>
            </div>
            <div class="flex space-x-2 w-full md:w-auto">
                <button type="submit"
                    class="px-4 py-2 bg-forest text-white text-sm font-medium rounded-md">
                    Search
                </button>

                <a href="<?= e(strtok($_SERVER['REQUEST_URI'], '?')) ?>"
                   class="px-4 py-2 bg-ivory-dark text-charcoal text-sm font-medium rounded-md border border-charcoal/10">
                    Clear
                </a>
            </div>
        </div>
    </div>
    </form>

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
    <div class="flex items-center justify-between mt-4">
        <div class="text-sm text-charcoal-light">
            Showing <?= $startRecord ?> to <?= $endRecord ?> of <?= $totalRecords ?> NGOs
        </div>
        <div class="flex space-x-1">
            <?php if ($page > 1): ?>
                <a href="?<?= http_build_query(array_merge($queryParams, ['page' => $page - 1])) ?>" class="px-3 py-1 bg-white border border-charcoal/20 rounded-md text-sm text-charcoal hover:bg-ivory transition-colors">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?<?= http_build_query(array_merge($queryParams, ['page' => $i])) ?>" class="px-3 py-1 border rounded-md text-sm <?= $i === $page ? 'bg-forest text-white' : 'bg-white border-charcoal/20 text-charcoal hover:bg-ivory transition-colors' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?<?= http_build_query(array_merge($queryParams, ['page' => $page + 1])) ?>" class="px-3 py-1 bg-white border border-charcoal/20 rounded-md text-sm text-charcoal hover:bg-ivory transition-colors">Next</a>
            <?php endif; ?>
        </div>
</div>



<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/admin.php';
?>

