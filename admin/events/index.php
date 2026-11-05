
<?php
require_once __DIR__ . '/../../DataBase/admin/events/index.php';

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-serif font-bold text-forest mb-2">Events Management</h1>
            <p class="text-charcoal-light max-w-2xl">Review community events, monitor participation, and manage event status.</p>
        </div>
    </div>

    <?php if ($success !== ''): ?>
        <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-sm font-medium text-green-800">
            <?= e($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-sm font-medium text-red-800">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Total Events</p>
            <p class="text-2xl font-bold text-charcoal mt-1"><?= $totalEvents ?></p>
        </div>

        <div class="bg-white rounded-xl border border-green-200/50 shadow-sm p-5 border-l-4 border-green-500">
            <p class="text-sm font-medium text-charcoal-light">Upcoming</p>
            <p class="text-2xl font-bold text-green-700 mt-1"><?= $upcomingCount ?></p>
        </div>

        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5 border-l-4 border-charcoal/40">
            <p class="text-sm font-medium text-charcoal-light">Completed</p>
            <p class="text-2xl font-bold text-charcoal mt-1"><?= $completedCount ?></p>
        </div>

        <div class="bg-white rounded-xl border border-red-200/50 shadow-sm p-5 border-l-4 border-red-500">
            <p class="text-sm font-medium text-charcoal-light">Suspended</p>
            <p class="text-2xl font-bold text-red-700 mt-1"><?= $suspendedCount ?></p>
        </div>
    </div>

    <form method="GET" action="" class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-4 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-end gap-4">

            <div class="flex-grow">
                <label for="search" class="block text-xs font-medium text-charcoal-light mb-1">Search</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    value="<?= e($search) ?>"
                    placeholder="Search events or NGOs..."
                    class="w-full px-4 py-2 border border-charcoal/20 rounded-md focus:outline-none focus:ring-1 focus:ring-forest text-sm">
            </div>

            <div class="w-full lg:w-40">
                <label for="status" class="block text-xs font-medium text-charcoal-light mb-1">Status</label>
                <select id="status" name="status" class="w-full px-4 py-2 border border-charcoal/20 rounded-md text-sm bg-white">
                    <option value="">All Status</option>
                    <option value="upcoming" <?= $status === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                    <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Suspended</option>
                </select>
            </div>

            <div class="w-full lg:w-40">
                <label for="category" class="block text-xs font-medium text-charcoal-light mb-1">Category</label>
                <select id="category" name="category" class="w-full px-4 py-2 border border-charcoal/20 rounded-md text-sm bg-white">
                    <option value="">All Categories</option>
                    <?php while ($categoryRow = mysqli_fetch_assoc($categoryResult)): ?>
                        <option value="<?= e($categoryRow['category']) ?>" <?= $category === $categoryRow['category'] ? 'selected' : '' ?>>
                            <?= e($categoryRow['category']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="w-full lg:w-40">
                <label for="date" class="block text-xs font-medium text-charcoal-light mb-1">Date</label>
                <select id="date" name="date" class="w-full px-4 py-2 border border-charcoal/20 rounded-md text-sm bg-white">
                    <option value="">All Dates</option>
                    <option value="week" <?= $dateFilter === 'week' ? 'selected' : '' ?>>This Week</option>
                    <option value="month" <?= $dateFilter === 'month' ? 'selected' : '' ?>>This Month</option>
                    <option value="next_month" <?= $dateFilter === 'next_month' ? 'selected' : '' ?>>Next Month</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-forest text-white text-sm font-medium rounded-md hover:bg-forest-dark">
                    Search
                </button>
                <a href="<?= baseUrl('admin/events/index.php') ?>" class="px-4 py-2 bg-ivory-dark text-charcoal text-sm font-medium rounded-md border border-charcoal/10">
                    Clear
                </a>
            </div>

        </div>
    </form>

    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-ivory/50 text-charcoal-light text-sm border-b border-charcoal/10">
                        <th class="px-4 lg:px-6 py-4 font-medium min-w-[200px]">Event</th>
                        <th class="px-4 lg:px-6 py-4 font-medium min-w-[150px]">NGO</th>
                        <th class="px-4 py-4 font-medium">Category</th>
                        <th class="px-4 lg:px-6 py-4 font-medium min-w-[180px]">Date &amp; Time</th>
                        <th class="px-4 py-4 font-medium min-w-[180px]">Location</th>
                        <th class="px-4 py-4 font-medium min-w-[120px]">Participants</th>
                        <th class="px-4 py-4 font-medium">Status</th>
                        <th class="px-4 lg:px-6 py-4 font-medium text-right min-w-[150px]">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-charcoal/5">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($event = mysqli_fetch_assoc($result)): ?>
                            <?php
                            $registered = (int) $event['registered'];
                            $capacity = (int) $event['max_participants'];

                            if ($capacity > 0) {
                                $progress = round(($registered / $capacity) * 100);
                            } else {
                                $progress = 0;
                            }

                            if ($progress > 100) {
                                $progress = 100;
                            }

                            if ($event['status'] === 'upcoming') {
                                $statusLabel = 'Upcoming';
                                $statusClass = 'bg-green-100 text-green-800 border-green-200';
                            } elseif ($event['status'] === 'completed') {
                                $statusLabel = 'Completed';
                                $statusClass = 'bg-gray-100 text-gray-800 border-gray-200';
                            } else {
                                $statusLabel = 'Suspended';
                                $statusClass = 'bg-red-100 text-red-800 border-red-200';
                            }

                            $startTime = '';
                            $endTime = '';

                            if ($event['start_time'] !== null) {
                                $startTime = date('h:i A', strtotime($event['start_time']));
                            }

                            if ($event['end_time'] !== null) {
                                $endTime = date('h:i A', strtotime($event['end_time']));
                            }
                            ?>

                            <tr class="hover:bg-ivory/30 transition-colors">
                                <td class="px-4 lg:px-6 py-4">
                                    <div class="font-medium text-charcoal"><?= e($event['title']) ?></div>
                                </td>

                                <td class="px-4 lg:px-6 py-4">
                                    <div class="text-sm text-charcoal-light"><?= e($event['ngo_name']) ?></div>
                                </td>

                                <td class="px-4 py-4 text-sm text-charcoal-light">
                                    <?= e($event['category']) ?>
                                </td>

                                <td class="px-4 lg:px-6 py-4">
                                    <div class="text-sm font-medium text-charcoal">
                                        <?= date('d M Y', strtotime($event['event_date'])) ?>
                                    </div>
                                    <div class="text-xs text-charcoal-light">
                                        <?= e($startTime) ?><?php if ($startTime !== '' && $endTime !== ''): ?> – <?php endif; ?><?= e($endTime) ?>
                                    </div>
                                </td>

                                <td class="px-4 py-4 text-sm text-charcoal-light">
                                    <?= e($event['location']) ?>
                                </td>

                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-charcoal mb-1">
                                        <?php if ($capacity > 0): ?>
                                            <?= $registered ?> / <?= $capacity ?>
                                        <?php else: ?>
                                            <?= $registered ?> registered
                                        <?php endif; ?>
                                    </div>
                                    <div class="w-full bg-ivory rounded-full h-1.5 border border-charcoal/10">
                                        <div class="bg-forest h-1.5 rounded-full" style="width: <?= $progress ?>%"></div>
                                    </div>
                                </td>

                                <td class="px-4 py-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= $statusClass ?>">
                                        <?= e($statusLabel) ?>
                                    </span>
                                </td>

                                <td class="px-4 lg:px-6 py-4 text-sm text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="<?= baseUrl('admin/events/view.php?id=' . $event['id']) ?>"
                                           class="inline-flex justify-center items-center px-3 py-1.5 border border-forest text-forest hover:bg-forest hover:text-white text-xs font-medium rounded-md">
                                            View
                                        </a>

                                        <?php if ($event['status'] === 'upcoming'): ?>
                                            <form method="POST" action="">
                                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                                <input type="hidden" name="new_status" value="cancelled">
                                                <button type="submit" name="update_status"
                                                        onclick="return confirm('Are you sure you want to suspend this event?');"
                                                        class="inline-flex justify-center items-center px-3 py-1.5 border border-red-200 text-red-600 hover:bg-red-50 text-xs font-medium rounded-md">
                                                    Suspend
                                                </button>
                                            </form>
                                        <?php elseif ($event['status'] === 'cancelled'): ?>
                                            <form method="POST" action="">
                                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                                <input type="hidden" name="new_status" value="upcoming">
                                                <button type="submit" name="update_status"
                                                        onclick="return confirm('Are you sure you want to activate this event?');"
                                                        class="inline-flex justify-center items-center px-3 py-1.5 border border-green-200 text-green-700 hover:bg-green-50 text-xs font-medium rounded-md">
                                                    Activate
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <h3 class="text-lg font-medium text-charcoal">No events found</h3>
                                <p class="text-sm text-charcoal-light mt-1">Try changing your search or filter criteria.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2 pb-6">
        <p class="text-sm text-charcoal-light">
            Showing
            <span class="font-medium text-charcoal">
                <?php if ($filteredTotal > 0): ?>
                    <?= $offset + 1 ?>
                <?php else: ?>
                    0
                <?php endif; ?>
            </span>
            to
            <span class="font-medium text-charcoal">
                <?= min($offset + $perPage, $filteredTotal) ?>
            </span>
            of
            <span class="font-medium text-charcoal"><?= $filteredTotal ?></span>
            results
        </p>

        <?php if ($totalPages > 1): ?>
            <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
                <?php if ($page > 1): ?>
                    <a href="?search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&category=<?= urlencode($category) ?>&date=<?= urlencode($dateFilter) ?>&page=<?= $page - 1 ?>"
                       class="px-4 py-2 border border-charcoal/20 bg-white text-sm text-charcoal rounded-l-md hover:bg-ivory">
                        Previous
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&category=<?= urlencode($category) ?>&date=<?= urlencode($dateFilter) ?>&page=<?= $i ?>"
                       class="px-4 py-2 border text-sm <?= $page === $i ? 'bg-forest text-white border-forest' : 'bg-white text-charcoal border-charcoal/20 hover:bg-ivory' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&category=<?= urlencode($category) ?>&date=<?= urlencode($dateFilter) ?>&page=<?= $page + 1 ?>"
                       class="px-4 py-2 border border-charcoal/20 bg-white text-sm text-charcoal rounded-r-md hover:bg-ivory">
                        Next
                    </a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/admin.php';
?>
