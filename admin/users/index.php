
<?php
require_once __DIR__ . '/../../DataBase/admin/users/index.php';

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-serif font-bold text-forest mb-2">
            User Management
        </h1>
        <p class="text-charcoal-light max-w-2xl">
            View registered users, monitor account activity, and manage user access.
        </p>
    </div>

    <!-- Alert -->
    <?php if ($alertMessage !== ''): ?>
        <div class="mb-6 p-4 rounded-lg border
            <?= $alertType === 'success'
                ? 'bg-green-50 border-green-200 text-green-800'
                : 'bg-red-50 border-red-200 text-red-800' ?>">
            <p class="text-sm font-medium"><?= e($alertMessage) ?></p>
        </div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Total Users</p>
            <p class="text-2xl font-bold text-charcoal mt-1"><?= $totalUsers ?></p>
        </div>

        <div class="bg-white rounded-xl border border-green-200/50 shadow-sm p-5 border-l-4 border-green-500">
            <p class="text-sm font-medium text-charcoal-light">Active Users</p>
            <p class="text-2xl font-bold text-green-700 mt-1"><?= $activeUsers ?></p>
        </div>

        <div class="bg-white rounded-xl border border-blue-200/50 shadow-sm p-5 border-l-4 border-blue-500">
            <p class="text-sm font-medium text-charcoal-light">New This Month</p>
            <p class="text-2xl font-bold text-blue-700 mt-1"><?= $newUsers ?></p>
        </div>

        <div class="bg-white rounded-xl border border-red-200/50 shadow-sm p-5 border-l-4 border-red-500">
            <p class="text-sm font-medium text-charcoal-light">Suspended</p>
            <p class="text-2xl font-bold text-red-700 mt-1"><?= $suspendedUsers ?></p>
        </div>

    </div>

    <!-- Search and Filters -->
    <form method="GET" action="<?= e(baseUrl('admin/users/index.php')) ?>"
        class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-4 mb-6">

        <div class="flex flex-col md:flex-row md:items-end gap-4">

            <div class="flex-grow">
                <label for="search" class="block text-xs font-medium text-charcoal-light mb-1">
                    Search
                </label>
                <input type="text" name="search" id="search"
                    value="<?= e($search) ?>"
                    placeholder="Search by name or email..."
                    class="w-full px-4 py-2 border border-charcoal/20 rounded-md focus:outline-none focus:ring-1 focus:ring-forest text-sm">
            </div>

            <div class="w-full md:w-48">
                <label for="status" class="block text-xs font-medium text-charcoal-light mb-1">
                    Status
                </label>
                <select name="status" id="status"
                    class="w-full px-4 py-2 border border-charcoal/20 rounded-md text-sm bg-white">
                    <option value="">All Status</option>
                    <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>
                        Active
                    </option>
                    <option value="suspended" <?= $statusFilter === 'suspended' ? 'selected' : '' ?>>
                        Suspended
                    </option>
                </select>
            </div>

            <div class="w-full md:w-48">
                <label for="registration" class="block text-xs font-medium text-charcoal-light mb-1">
                    Registration
                </label>
                <select name="registration" id="registration"
                    class="w-full px-4 py-2 border border-charcoal/20 rounded-md text-sm bg-white">
                    <option value="">All Time</option>
                    <option value="month" <?= $registrationFilter === 'month' ? 'selected' : '' ?>>
                        This Month
                    </option>
                    <option value="three_months" <?= $registrationFilter === 'three_months' ? 'selected' : '' ?>>
                        Last 3 Months
                    </option>
                    <option value="year" <?= $registrationFilter === 'year' ? 'selected' : '' ?>>
                        This Year
                    </option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-forest text-white text-sm font-medium rounded-md hover:bg-forest-dark">
                    Search
                </button>

                <a href="<?= e(baseUrl('admin/users/index.php')) ?>"
                    class="px-4 py-2 bg-ivory-dark text-charcoal text-sm font-medium rounded-md hover:bg-gold/20 border border-charcoal/10">
                    Clear
                </a>
            </div>

        </div>
    </form>

    <!-- User Table -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
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
                        $nameParts = explode(' ', $user['name']);
                        $initials = '';

                        foreach ($nameParts as $part) {
                            if ($part !== '') {
                                $initials .= strtoupper(substr($part, 0, 1));
                            }

                            if (strlen($initials) >= 2) {
                                break;
                            }
                        }

                        $isActive = $user['status'] === 'active';

                        $statusClass = $isActive
                            ? 'bg-green-100 text-green-800 border-green-200'
                            : 'bg-red-100 text-red-800 border-red-200';
                        ?>

                        <tr class="hover:bg-ivory/30 transition-colors">

                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-forest text-white flex items-center justify-center font-serif font-bold text-sm">
                                        <?= e($initials) ?>
                                    </div>

                                    <div>
                                        <div class="font-medium text-charcoal">
                                            <?= e($user['name']) ?>
                                        </div>
                                        <a href="mailto:<?= e($user['email']) ?>"
                                            class="text-sm text-forest hover:text-gold">
                                            <?= e($user['email']) ?>
                                        </a>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm text-charcoal-light">
                                <?= !empty($user['created_at'])
                                    ? e(date('d M Y', strtotime($user['created_at'])))
                                    : 'N/A' ?>
                            </td>

                            <td class="px-6 py-4 text-sm text-center font-medium text-charcoal">
                                0
                            </td>

                            <td class="px-6 py-4 text-sm text-center font-medium text-forest">
                                0
                            </td>

                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= $statusClass ?>">
                                    <?= e(ucfirst($user['status'])) ?>
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm text-right">
                                <div class="flex items-center justify-end gap-2">

                                    <a href="<?= e(baseUrl('admin/users/view.php?id=' . $user['id'])) ?>"
                                        class="inline-flex items-center px-3 py-1.5 border border-forest text-forest hover:bg-forest hover:text-white text-xs font-medium rounded-md">
                                        View
                                    </a>

                                    <form method="POST"
                                        action="<?= e(baseUrl('admin/users/index.php')) ?>"
                                        onsubmit="return true;">

                                        <input type="hidden" name="user_id"
                                            value="<?= (int) $user['id'] ?>">

                                        <input type="hidden" name="new_status"
                                            value="<?= $isActive ? 'suspended' : 'active' ?>">

                                        <button type="submit" name="update_status"
                                            value="1"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md
                                            <?= $isActive
                                                ? 'border border-red-200 text-red-600 hover:bg-red-50'
                                                : 'border border-green-200 text-green-700 hover:bg-green-50' ?>">
                                            <?= $isActive ? 'Suspend' : 'Activate' ?>
                                        </button>

                                    </form>

                                </div>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    <?php if (count($users) === 0): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <h3 class="text-lg font-medium text-charcoal">No users found</h3>
                                <p class="text-sm text-charcoal-light mt-1">
                                    Try changing your search or filter criteria.
                                </p>
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>

    <p class="text-sm text-charcoal-light mb-6">
        Showing <?= count($users) ?> users
    </p>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/admin.php';
?>
