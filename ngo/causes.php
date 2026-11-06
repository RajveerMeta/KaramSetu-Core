<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../Database/db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$causes = [];
$errorMsg = '';

$user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
$ngo_id = 0;

if ($user_id > 0) {

    $ngoQuery = mysqli_query(
        $con,
        "SELECT id FROM ngos WHERE user_id = $user_id LIMIT 1"
    );

    if ($ngoQuery && mysqli_num_rows($ngoQuery) > 0) {
        $ngoData = mysqli_fetch_assoc($ngoQuery);
        $ngo_id = (int) $ngoData['id'];
    }
}

if ($ngo_id > 0) {

    // Automatically complete causes whose end date has passed
    mysqli_query(
        $con,
        "UPDATE causes
         SET status = 'completed'
         WHERE ngo_id = $ngo_id
         AND status = 'active'
         AND end_date < CURDATE()"
    );

    // Get causes
    $causeQuery = mysqli_query(
        $con,
        "SELECT c.*,
                COALESCE((
                    SELECT SUM(co.monetary_amount)
                    FROM contributions co
                    WHERE co.cause_id = c.id
                      AND co.contribution_type = 'money'
                      AND co.payment_status = 'completed'
                ), 0) AS raised_amount,
                GROUP_CONCAT(
                    DISTINCT cat.contribution_type
                    ORDER BY cat.contribution_type
                    SEPARATOR ', '
                ) AS accepted_types
         FROM causes c
         LEFT JOIN cause_accepted_types cat
            ON cat.cause_id = c.id
         WHERE c.ngo_id = $ngo_id
         GROUP BY c.id
         ORDER BY c.created_at DESC"
    );

    if ($causeQuery) {

        while ($row = mysqli_fetch_assoc($causeQuery)) {
            $causes[] = $row;
        }

    } else {
        $errorMsg = 'Unable to load causes.';
    }
}

ob_start();
?>

<div class="min-h-screen bg-ivory py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <!-- HEADER -->
        <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-4xl font-serif font-bold text-forest mb-2">
                    Causes
                </h1>
                <p class="text-charcoal-light">
                    Manage the causes supported by your organization.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="<?= baseUrl('ngo/create-cause.php') ?>"
                   class="inline-block bg-forest text-white font-bold py-2.5 px-6 rounded-full hover:bg-forest-dark transition-colors shadow-sm">
                    + Create New Cause
                </a>
            </div>
        </div>

        <?php if ($errorMsg !== ''): ?>
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
                <?= htmlspecialchars($errorMsg) ?>
            </div>
        <?php endif; ?>

        <?php if ($user_id <= 0 || $ngo_id <= 0): ?>

            <div class="bg-white rounded-3xl p-8 text-center border border-gold/20">
                <h2 class="text-xl font-serif font-bold text-forest mb-2">
                    NGO account not found
                </h2>
                <p class="text-charcoal-light">
                    Please log in with your registered NGO account to manage causes.
                </p>
            </div>

        <?php elseif (empty($causes)): ?>

            <div class="bg-white rounded-3xl p-8 md:p-12 text-center border border-gold/20">
                <h2 class="text-xl md:text-2xl font-serif font-bold text-forest mb-3">
                    No Causes Yet
                </h2>
                <p class="text-charcoal-light mb-6">
                    Create your first fundraising cause to get started.
                </p>
                <a href="<?= baseUrl('ngo/create-cause.php') ?>"
                   class="inline-block bg-forest text-white font-bold py-3 px-6 rounded-full hover:bg-forest-dark transition-colors">
                    + Create New Cause
                </a>
            </div>

        <?php else: ?>

            <!-- CAUSES GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <?php foreach ($causes as $cause): ?>

                    <?php
                    $target = (float) $cause['target_amount'];
                    $raised = (float) $cause['raised_amount'];

                    $progress = $target > 0
                        ? min(100, round(($raised / $target) * 100))
                        : 0;

                    $status = $cause['status'] ?? 'active';

                    $statusClasses = [
                        'active' => 'bg-forest/10 text-forest',
                        'completed' => 'bg-blue-100 text-blue-800',
                        'suspended' => 'bg-red-100 text-red-800'
                    ];

                    $statusClass = $statusClasses[$status]
                        ?? 'bg-gray-100 text-gray-800';
                    ?>

                    <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20 flex flex-col justify-between">

                        <div>
                            <div class="flex justify-between items-start mb-3">
                                <span class="inline-block px-2.5 py-1 <?= $statusClass ?> text-xs font-bold uppercase tracking-wider rounded-md">
                                    <?= htmlspecialchars(ucfirst($status)) ?>
                                </span>
                            </div>

                            <h2 class="text-xl font-serif font-bold text-forest mb-2">
                                <?= htmlspecialchars($cause['title']) ?>
                            </h2>

                            <p class="text-xs font-bold uppercase tracking-wider text-gold mb-3">
                                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $cause['category']))) ?>
                            </p>

                            <p class="text-sm text-charcoal-light mb-6 line-clamp-2">
                                <?= htmlspecialchars($cause['short_description']) ?>
                            </p>

                            <div class="mb-6">
                                <div class="flex flex-wrap items-end justify-between gap-4 mb-1">
                                    <span class="text-lg font-bold text-forest">
                                        ₹<?= number_format($raised, 2) ?>
                                        <span class="text-xs text-charcoal-light font-normal">raised</span>
                                    </span>

                                    <span class="text-xs font-bold text-charcoal">
                                        Goal: ₹<?= number_format($target, 2) ?>
                                    </span>
                                </div>

                                <div class="w-full bg-ivory rounded-full h-2 overflow-hidden border border-charcoal/5">
                                    <div class="bg-forest h-2 rounded-full"
                                         style="width: <?= $progress ?>%"></div>
                                </div>

                                <p class="text-xs font-bold text-forest text-right mt-1">
                                    <?= $progress ?>%
                                </p>
                            </div>

                            <?php if (!empty($cause['accepted_types'])): ?>
                                <div class="mb-3">
                                    <span class="block text-[10px] font-bold text-charcoal-light uppercase tracking-wider mb-1.5">Accepts</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        <?php foreach (explode(', ', $cause['accepted_types']) as $atype): ?>
                                            <span class="bg-ivory border border-gold/30 text-charcoal text-[10px] px-2 py-0.5 rounded font-medium capitalize">
                                                <?= htmlspecialchars($atype == 'items' ? 'Useful Items' : $atype) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-charcoal/10">
                            <a href="<?= baseUrl('ngo/cause-details.php?id=' . (int) $cause['id']) ?>"
                               class="flex-1 text-center bg-ivory text-forest font-bold py-2 rounded-xl hover:bg-forest/5 transition-colors border border-charcoal/10 text-sm">
                                View Details
                            </a>

                            <a href="<?= baseUrl('ngo/edit-cause.php?id=' . (int) $cause['id']) ?>"
                               class="flex-1 text-center bg-white text-gold font-bold py-2 rounded-xl hover:bg-forest transition-colors border border-gold/30 text-sm">
                                Edit
                            </a>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/ngo.php';
?>