
<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../DataBase/db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Update cause status */
if (isset($_POST['update_status'])) {
    $causeId = (int) $_POST['cause_id'];
    $newStatus = $_POST['new_status'];

    if ($causeId > 0 && in_array($newStatus, ['active', 'suspended'], true)) {
        $sql = "UPDATE causes
                SET status = '$newStatus'
                WHERE id = $causeId
                AND status IN ('active', 'suspended')";

        mysqli_query($con, $sql);
    }

    header('Location: ' . baseUrl('admin/causes/index.php'));
    exit;
}

/* Search and filters */
$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$category = $_GET['category'] ?? '';

$searchSql = mysqli_real_escape_string($con, $search);
$categorySql = mysqli_real_escape_string($con, $category);

$where = [];

if ($search !== '') {
    $where[] = "(c.title LIKE '%$searchSql%'
                 OR n.name LIKE '%$searchSql%')";
}

if (in_array($status, ['active', 'completed', 'suspended'], true)) {
    $where[] = "c.status = '$status'";
} else {
    $status = '';
}

if ($category !== '') {
    $where[] = "c.category = '$categorySql'";
}

$whereSql = count($where) > 0
    ? 'WHERE ' . implode(' AND ', $where)
    : '';

/* Summary counts */
$totalResult = mysqli_query($con, "SELECT COUNT(*) AS total FROM causes");
$totalCauses = (int) mysqli_fetch_assoc($totalResult)['total'];

$activeResult = mysqli_query(
    $con,
    "SELECT COUNT(*) AS total FROM causes WHERE status = 'active'"
);
$activeCauses = (int) mysqli_fetch_assoc($activeResult)['total'];

$completedResult = mysqli_query(
    $con,
    "SELECT COUNT(*) AS total FROM causes WHERE status = 'completed'"
);
$completedCauses = (int) mysqli_fetch_assoc($completedResult)['total'];

$suspendedResult = mysqli_query(
    $con,
    "SELECT COUNT(*) AS total FROM causes WHERE status = 'suspended'"
);
$suspendedCauses = (int) mysqli_fetch_assoc($suspendedResult)['total'];

/* Category options */
$categoryResult = mysqli_query(
    $con,
    "SELECT DISTINCT category
     FROM causes
     ORDER BY category ASC"
);

/* Pagination */
$perPage = 10;
$page = max(1, (int) ($_GET['page'] ?? 1));

$countSql = "SELECT COUNT(*) AS total
             FROM causes c
             LEFT JOIN ngos n ON n.id = c.ngo_id
             $whereSql";

$countResult = mysqli_query($con, $countSql);
$totalFiltered = (int) mysqli_fetch_assoc($countResult)['total'];

$totalPages = max(1, (int) ceil($totalFiltered / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

/* Causes with fundraising totals */
$sql = "SELECT
            c.id,
            c.ngo_id,
            c.title,
            c.category,
            c.target_amount,
            c.start_date,
            c.end_date,
            c.status,
            n.name AS ngo_name,
            COALESCE(
                SUM(
                    CASE
                        WHEN co.contribution_type = 'money'
                         AND co.payment_status = 'completed'
                        THEN co.monetary_amount
                        ELSE 0
                    END
                ),
                0
            ) AS raised_amount
        FROM causes c
        LEFT JOIN ngos n ON n.id = c.ngo_id
        LEFT JOIN contributions co ON co.cause_id = c.id
        $whereSql
        GROUP BY c.id
        ORDER BY c.created_at DESC
        LIMIT $perPage OFFSET $offset";

$result = mysqli_query($con, $sql);

if (!$result) {
    die('Unable to load causes.');
}

function causeStatusClass($status)
{
    if ($status === 'active') {
        return 'bg-green-100 text-green-800';
    }

    if ($status === 'completed') {
        return 'bg-blue-100 text-blue-800';
    }

    return 'bg-red-100 text-red-800';
}

function causeStatusLabel($status)
{
    return ucfirst($status);
}

ob_start();
?>

<div class="min-h-screen bg-ivory px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">

        <!-- Page Heading -->
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-3xl font-bold text-forest">
                    Causes Management
                </h1>
                <p class="mt-2 text-sm text-charcoal/60">
                    Manage and monitor NGO fundraising causes.
                </p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

            <div class="rounded-xl border border-charcoal/10 bg-white p-5 shadow-sm">
                <p class="text-sm text-charcoal/60">Total Causes</p>
                <p class="mt-2 text-3xl font-bold text-forest">
                    <?= $totalCauses ?>
                </p>
            </div>

            <div class="rounded-xl border border-charcoal/10 bg-white p-5 shadow-sm">
                <p class="text-sm text-charcoal/60">Active Causes</p>
                <p class="mt-2 text-3xl font-bold text-green-700">
                    <?= $activeCauses ?>
                </p>
            </div>

            <div class="rounded-xl border border-charcoal/10 bg-white p-5 shadow-sm">
                <p class="text-sm text-charcoal/10">Completed Causes</p>
                <p class="mt-2 text-3xl font-bold text-blue-700">
                    <?= $completedCauses ?>
                </p>
            </div>

            <div class="rounded-xl border border-charcoal/10 bg-white p-5 shadow-sm">
                <p class="text-sm text-charcoal/60">Suspended Causes</p>
                <p class="mt-2 text-3xl font-bold text-red-700">
                    <?= $suspendedCauses ?>
                </p>
            </div>

        </div>

        <!-- Search and Filters -->
        <div class="mb-6 rounded-xl border border-charcoal/10 bg-white p-5 shadow-sm">
            <form method="GET" action="<?= e(baseUrl('admin/causes/index.php')) ?>"
                  class="grid grid-cols-1 gap-4 md:grid-cols-4">

                <div>
                    <label for="search" class="mb-2 block text-sm font-medium text-charcoal">
                        Search causes
                    </label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="<?= e($search) ?>"
                        placeholder="Cause or NGO name"
                        class="w-full rounded-lg border border-charcoal/20 px-4 py-2.5 text-sm outline-none focus:border-forest"
                    >
                </div>

                <div>
                    <label for="status" class="mb-2 block text-sm font-medium text-charcoal">
                        Status
                    </label>
                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-lg border border-charcoal/20 bg-white px-4 py-2.5 text-sm outline-none focus:border-forest"
                    >
                        <option value="">All Statuses</option>
                        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                    </select>
                </div>

                <div>
                    <label for="category" class="mb-2 block text-sm font-medium text-charcoal">
                        Category
                    </label>
                    <select
                        id="category"
                        name="category"
                        class="w-full rounded-lg border border-charcoal/20 bg-white px-4 py-2.5 text-sm outline-none focus:border-forest"
                    >
                        <option value="">All Categories</option>

                        <?php while ($cat = mysqli_fetch_assoc($categoryResult)): ?>
                            <option
                                value="<?= e($cat['category']) ?>"
                                <?= $category === $cat['category'] ? 'selected' : '' ?>
                            >
                                <?= e($cat['category']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button
                        type="submit"
                        class="rounded-lg bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forest/90"
                    >
                        Search
                    </button>

                    <a
                        href="<?= e(baseUrl('admin/causes/index.php')) ?>"
                        class="rounded-lg border border-charcoal/20 px-5 py-2.5 text-sm font-semibold text-charcoal hover:bg-ivory"
                    >
                        Reset
                    </a>
                </div>
            </form>
        </div>

        
<!-- Causes Table -->
<div class="overflow-hidden rounded-xl border border-charcoal/10 bg-white shadow-sm">

    <div class="flex flex-col gap-2 border-b border-charcoal/10 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="font-serif text-xl font-bold text-forest">
            All Causes
        </h2>

        <p class="text-sm text-charcoal/60">
            <?= $totalFiltered ?> cause(s) found
        </p>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="w-full min-w-[1000px] table-fixed border-collapse">

            <colgroup>
                <col style="width: 24%;">
                <col style="width: 16%;">
                <col style="width: 20%;">
                <col style="width: 16%;">
                <col style="width: 12%;">
                <col style="width: 12%;">
            </colgroup>

            <thead>
                <tr class="border-b border-charcoal/10 bg-ivory">
                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-charcoal/60">
                        Cause
                    </th>

                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-charcoal/60">
                        NGO
                    </th>

                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-charcoal/60">
                        Fundraising
                    </th>

                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-charcoal/60">
                        Dates
                    </th>

                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-charcoal/60">
                        Status
                    </th>

                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-charcoal/60">
                        Actions
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-charcoal/10">

                <?php if (mysqli_num_rows($result) > 0): ?>

                    <?php while ($cause = mysqli_fetch_assoc($result)): ?>
                        <?php
                        $target = (float) $cause['target_amount'];
                        $raised = (float) $cause['raised_amount'];

                        $progress = $target > 0
                            ? min(100, ($raised / $target) * 100)
                            : 0;
                        ?>

                        <tr class="transition-colors hover:bg-ivory/50">

                            <td class="break-words px-5 py-5 align-top">
                                <p class="font-semibold leading-5 text-forest">
                                    <?= e($cause['title']) ?>
                                </p>

                                <p class="mt-2 text-sm text-charcoal/60">
                                    <?= e($cause['category']) ?>
                                </p>

                                <p class="mt-1 text-xs text-charcoal/50">
                                    ID: <?= (int) $cause['id'] ?>
                                </p>
                            </td>

                            <td class="break-words px-5 py-5 align-top">
                                <p class="text-sm font-medium leading-5 text-charcoal">
                                    <?= e($cause['ngo_name'] ?? 'NGO not found') ?>
                                </p>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <p class="whitespace-nowrap text-sm font-semibold text-forest">
                                    ₹<?= number_format($raised, 2) ?>
                                </p>

                                <p class="mt-1 whitespace-nowrap text-xs text-charcoal/60">
                                    of ₹<?= number_format($target, 2) ?>
                                </p>

                                <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-charcoal/10">
                                    <div
                                        class="h-full rounded-full bg-gold"
                                        style="width: <?= $progress ?>%;"
                                    ></div>
                                </div>

                                <p class="mt-1 text-xs text-charcoal/60">
                                    <?= number_format($progress, 1) ?>% raised
                                </p>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <p class="text-xs text-charcoal/50">Start date</p>
                                <p class="mt-1 whitespace-nowrap text-sm text-charcoal">
                                    <?= e(date('d M Y', strtotime($cause['start_date']))) ?>
                                </p>

                                <p class="mt-3 text-xs text-charcoal/50">End date</p>
                                <p class="mt-1 whitespace-nowrap text-sm text-charcoal">
                                    <?= e(date('d M Y', strtotime($cause['end_date']))) ?>
                                </p>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <span class="inline-flex whitespace-nowrap rounded-full px-3 py-1.5 text-xs font-semibold <?= causeStatusClass($cause['status']) ?>">
                                    <?= e(causeStatusLabel($cause['status'])) ?>
                                </span>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <div class="flex flex-col items-start gap-3">

                                    <a
                                        href="<?= e(baseUrl('admin/causes/view.php?id=' . (int) $cause['id'])) ?>"
                                        class="text-sm font-semibold text-forest hover:underline"
                                    >
                                        View Details
                                    </a>

                                    <?php if ($cause['status'] === 'active' || $cause['status'] === 'suspended'): ?>
                                        <form
                                            method="POST"
                                            action="<?= e(baseUrl('admin/causes/index.php')) ?>"
                                        >
                                            <input
                                                type="hidden"
                                                name="cause_id"
                                                value="<?= (int) $cause['id'] ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="new_status"
                                                value="<?= $cause['status'] === 'active' ? 'suspended' : 'active' ?>"
                                            >

                                            <button
                                                type="submit"
                                                name="update_status"
                                                value="1"
                                                class="text-sm font-semibold <?= $cause['status'] === 'active' ? 'text-red-700' : 'text-green-700' ?> hover:underline"
                                            >
                                                <?= $cause['status'] === 'active' ? 'Suspend' : 'Activate' ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                </div>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <h3 class="text-base font-semibold text-charcoal">
                                    No causes found
                                </h3>

                                <p class="mt-2 text-sm text-charcoal/60">
                                    Try changing your search or filter options.
                                </p>
                            </div>
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="flex flex-col gap-3 border-t border-charcoal/10 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

            <p class="text-sm text-charcoal/60">
                Page <?= $page ?> of <?= $totalPages ?>
            </p>

            <div class="flex gap-2">
                <?php if ($page > 1): ?>
                    <a
                        href="?<?= e(http_build_query([
                            'search' => $search,
                            'status' => $status,
                            'category' => $category,
                            'page' => $page - 1
                        ])) ?>"
                        class="rounded-lg border border-charcoal/20 px-4 py-2 text-sm hover:bg-ivory"
                    >
                        Previous
                    </a>
                <?php endif; ?>

                <?php if ($page < $totalPages): ?>
                    <a
                        href="?<?= e(http_build_query([
                            'search' => $search,
                            'status' => $status,
                            'category' => $category,
                            'page' => $page + 1
                        ])) ?>"
                        class="rounded-lg border border-charcoal/20 px-4 py-2 text-sm hover:bg-ivory"
                    >
                        Next
                    </a>
                <?php endif; ?>
            </div>

        </div>
    <?php endif; ?>

</div>


            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="flex flex-col gap-3 border-t border-charcoal/10 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-charcoal/60">
                        Page <?= $page ?> of <?= $totalPages ?>
                    </p>

                    <div class="flex gap-2">
                        <?php if ($page > 1): ?>
                            <a
                                href="?<?= e(http_build_query([
                                    'search' => $search,
                                    'status' => $status,
                                    'category' => $category,
                                    'page' => $page - 1
                                ])) ?>"
                                class="rounded-lg border border-charcoal/20 px-4 py-2 text-sm hover:bg-ivory"
                            >
                                Previous
                            </a>
                        <?php endif; ?>

                        <?php if ($page < $totalPages): ?>
                            <a
                                href="?<?= e(http_build_query([
                                    'search' => $search,
                                    'status' => $status,
                                    'category' => $category,
                                    'page' => $page + 1
                                ])) ?>"
                                class="rounded-lg border border-charcoal/20 px-4 py-2 text-sm hover:bg-ivory"
                            >
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/admin.php';
?>
