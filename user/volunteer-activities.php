<?php
require_once __DIR__ . '/../includes/functions.php';
requireUser();
require_once __DIR__ . '/../Database/db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = (int)($_SESSION['user_id'] ?? 0);

// Handle cancellation
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['cancel_activity_id'])) {
    $cancelId = (int)$_POST['cancel_activity_id'];
    if ($cancelId > 0 && $userId > 0) {
        $stmtCancel = mysqli_prepare($con, "UPDATE volunteer_activities SET status = 'cancelled', updated_at = NOW() WHERE id = ? AND user_id = ? AND status = 'registered'");
        mysqli_stmt_bind_param($stmtCancel, "ii", $cancelId, $userId);
        mysqli_stmt_execute($stmtCancel);
    }
    header("Location: " . baseUrl('user/volunteer-activities.php'));
    exit;
}

// Filters
$selectedStatus = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : 'all';
if (!in_array($selectedStatus, ['all', 'upcoming', 'completed'])) {
    $selectedStatus = 'all';
}
$selectedNgo = isset($_GET['ngo']) ? (int)$_GET['ngo'] : 0;

// Summary stats
$completedCount = 0;
$upcomingCount = 0;
$totalHours = 0.0;
$totalNgos = 0;

if ($userId > 0) {
    $statsSql = "SELECT 
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) AS completed_count,
                    COUNT(CASE WHEN status = 'registered' THEN 1 END) AS upcoming_count,
                    COALESCE(SUM(CASE WHEN status = 'completed' THEN hours_completed ELSE 0 END), 0) AS total_hours,
                    COUNT(DISTINCT ngo_id) AS total_ngos
                 FROM volunteer_activities
                 WHERE user_id = ?";
    $stmtStats = mysqli_prepare($con, $statsSql);
    mysqli_stmt_bind_param($stmtStats, "i", $userId);
    mysqli_stmt_execute($stmtStats);
    $statsRes = mysqli_stmt_get_result($stmtStats);
    if ($statsRes) {
        $statsRow = mysqli_fetch_assoc($statsRes);
        $completedCount = (int)($statsRow['completed_count'] ?? 0);
        $upcomingCount = (int)($statsRow['upcoming_count'] ?? 0);
        $totalHours = (float)($statsRow['total_hours'] ?? 0);
        $totalNgos = (int)($statsRow['total_ngos'] ?? 0);
    }
}

// Fetch user's distinct NGOs for filter dropdown
$filterNgos = [];
if ($userId > 0) {
    $ngosSql = "SELECT DISTINCT n.id, n.name 
                FROM volunteer_activities va
                INNER JOIN ngos n ON n.id = va.ngo_id
                WHERE va.user_id = ?
                ORDER BY n.name ASC";
    $stmtNgos = mysqli_prepare($con, $ngosSql);
    mysqli_stmt_bind_param($stmtNgos, "i", $userId);
    mysqli_stmt_execute($stmtNgos);
    $ngosRes = mysqli_stmt_get_result($stmtNgos);
    if ($ngosRes) {
        while ($nRow = mysqli_fetch_assoc($ngosRes)) {
            $filterNgos[] = $nRow;
        }
    }
}

// Fetch activities
$upcomingActivities = [];
$completedActivities = [];
$allActivities = [];

if ($userId > 0) {
    $actSql = "SELECT va.*, n.name AS organization_name
               FROM volunteer_activities va
               INNER JOIN ngos n ON n.id = va.ngo_id
               WHERE va.user_id = ?";
    
    if ($selectedNgo > 0) {
        $actSql .= " AND va.ngo_id = " . (int)$selectedNgo;
    }

    $actSql .= " ORDER BY va.activity_date DESC, va.created_at DESC";

    $stmtAct = mysqli_prepare($con, $actSql);
    mysqli_stmt_bind_param($stmtAct, "i", $userId);
    mysqli_stmt_execute($stmtAct);
    $resAct = mysqli_stmt_get_result($stmtAct);

    if ($resAct) {
        while ($row = mysqli_fetch_assoc($resAct)) {
            $timeStr = null;
            if (!empty($row['start_time'])) {
                $timeStr = date('h:i A', strtotime($row['start_time']));
                if (!empty($row['end_time'])) {
                    $timeStr .= ' – ' . date('h:i A', strtotime($row['end_time']));
                }
            }

            $item = (object)[
                'id' => (int)$row['id'],
                'event_id' => !empty($row['event_id']) ? (int)$row['event_id'] : null,
                'organization' => $row['organization_name'],
                'title' => $row['title'],
                'date' => date('d F Y', strtotime($row['activity_date'])),
                'time' => $timeStr,
                'location' => $row['location'],
                'role' => $row['volunteer_role'],
                'hours' => (float)$row['hours_completed'],
                'status' => ucfirst($row['status']),
                'karma_points' => (int)$row['karma_points_awarded']
            ];

            $allActivities[] = $item;
            if ($row['status'] === 'registered') {
                $upcomingActivities[] = $item;
            } elseif ($row['status'] === 'completed') {
                $completedActivities[] = $item;
            }
        }
    }
}

ob_start();
?>

<div class="bg-ivory/40 min-h-screen py-10 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Page Header -->
        <div class="mb-10 text-center lg:text-left">
            <h1 class="text-3xl md:text-4xl lg:text-4xl font-serif font-bold text-forest mb-2">Volunteer Activities</h1>
            <p class="text-charcoal-light">Make a difference with your time, skills, and participation.</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6 mb-10">
            <!-- Activities Completed -->
            <div class="bg-white rounded-2xl p-4 md:p-8 shadow-sm border border-gold/20 flex flex-col justify-center transition-shadow hover:shadow-md">
                <p class="text-sm font-bold uppercase tracking-wide text-charcoal-light mb-1">Activities Completed</p>
                <p class="text-3xl md:text-4xl font-serif font-bold text-forest mb-1"><?= $completedCount ?></p>
                <p class="text-xs font-medium text-charcoal-light">Held across <?= $totalNgos ?> <?= $totalNgos === 1 ? 'organization' : 'organizations' ?></p>
            </div>
            
            <!-- Hours Contributed -->
            <div class="bg-white rounded-2xl p-4 md:p-8 shadow-sm border border-gold/20 flex flex-col justify-center transition-shadow hover:shadow-md">
                <p class="text-sm font-bold uppercase tracking-wide text-charcoal-light mb-1">Hours Contributed</p>
                <p class="text-3xl md:text-4xl font-serif font-bold text-forest mb-1"><?= $totalHours ?>h</p>
                <p class="text-xs font-medium text-charcoal-light">Time given to the community</p>
            </div>
            
            <!-- Upcoming Activities -->
            <div class="bg-white rounded-2xl p-4 md:p-8 shadow-sm border border-gold/20 flex flex-col justify-center transition-shadow hover:shadow-md">
                <p class="text-sm font-bold uppercase tracking-wide text-charcoal-light mb-1">Upcoming Activities</p>
                <p class="text-3xl md:text-4xl font-serif font-bold text-forest mb-1"><?= $upcomingCount ?></p>
                <p class="text-xs font-medium text-charcoal-light">Ready to make an impact</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-3 mb-8 pb-4 border-b border-charcoal/5">
            <?php
            $ngoParam = $selectedNgo > 0 ? '&ngo=' . $selectedNgo : '';
            ?>
            <a href="<?= baseUrl('user/volunteer-activities.php?status=all' . $ngoParam) ?>" class="px-4 py-2 rounded-full text-sm font-bold <?= $selectedStatus === 'all' ? 'bg-forest text-white' : 'bg-white text-charcoal border border-charcoal/10 hover:bg-ivory' ?> transition-colors shadow-sm">All Activities</a>
            <a href="<?= baseUrl('user/volunteer-activities.php?status=upcoming' . $ngoParam) ?>" class="px-4 py-2 rounded-full text-sm font-bold <?= $selectedStatus === 'upcoming' ? 'bg-forest text-white' : 'bg-white text-charcoal border border-charcoal/10 hover:bg-ivory' ?> transition-colors shadow-sm">Upcoming</a>
            <a href="<?= baseUrl('user/volunteer-activities.php?status=completed' . $ngoParam) ?>" class="px-4 py-2 rounded-full text-sm font-bold <?= $selectedStatus === 'completed' ? 'bg-forest text-white' : 'bg-white text-charcoal border border-charcoal/10 hover:bg-ivory' ?> transition-colors shadow-sm">Completed</a>
            
            <div class="ml-auto mt-2 sm:mt-0 w-full sm:w-auto">
                <form method="GET" action="<?= baseUrl('user/volunteer-activities.php') ?>" class="inline-block w-full sm:w-auto">
                    <input type="hidden" name="status" value="<?= e($selectedStatus) ?>">
                    <select name="ngo" onchange="this.form.submit()" class="w-full sm:w-auto px-4 py-2 rounded-lg text-sm font-medium bg-white text-charcoal border border-charcoal/10 focus:outline-none focus:border-forest/50 shadow-sm">
                        <option value="0">All Organizations</option>
                        <?php foreach ($filterNgos as $fNgo): ?>
                            <option value="<?= (int)$fNgo['id'] ?>" <?= $selectedNgo === (int)$fNgo['id'] ? 'selected' : '' ?>>
                                <?= e($fNgo['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

      <?php
    $renderCard = function ($activity) {
        $statusColor = 'bg-gray-50 text-gray-700 border-gray-200';

        if ($activity->status === 'Registered') {
            $statusColor = 'bg-amber-50 text-amber-700 border-amber-200';
        } elseif ($activity->status === 'Completed') {
            $statusColor = 'bg-green-50 text-forest border-green-200';
        } elseif ($activity->status === 'Cancelled') {
            $statusColor = 'bg-red-50 text-red-700 border-red-200';
        }

        ob_start();
?>

<div class="bg-white rounded-2xl shadow-sm border border-gold/20 overflow-hidden flex flex-col h-full hover:shadow-md transition-shadow">
    <div class="p-6 md:p-8 flex-1">
        <div class="flex flex-col md:flex-row justify-between items-start mb-4">
            <div>
                <h3 class="font-bold text-charcoal text-lg mb-1">
                    <?= e($activity->title) ?>
                </h3>

                <p class="text-sm font-medium text-forest">
                    <?= e($activity->organization) ?>
                </p>
            </div>

            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border <?= $statusColor ?>">
                <?= e($activity->status) ?>
            </span>
        </div>

        <div class="space-y-2 mb-6">
            <div class="flex flex-col md:flex-row items-center text-sm text-charcoal-light">
                <span class="mr-2">📅</span>
                <span class="font-medium">
                    <?= e($activity->date) ?>
                </span>
            </div>

            <?php if ($activity->time): ?>
                <div class="flex flex-col md:flex-row items-center text-sm text-charcoal-light">
                    <span class="mr-2">🕐</span>
                    <span class="font-medium">
                        <?= e($activity->time) ?>
                    </span>
                </div>
            <?php endif; ?>

            <div class="flex flex-col md:flex-row items-center text-sm text-charcoal-light">
                <span class="mr-2">📍</span>
                <span class="font-medium">
                    <?= e($activity->location) ?>
                </span>
            </div>
        </div>

        <div class="border-t border-charcoal/5 pt-4">
            <div class="flex flex-col md:flex-row justify-between items-center mb-2">
                <span class="text-xs font-bold uppercase tracking-wide text-charcoal-light">
                    Role
                </span>

                <span class="text-sm font-bold text-charcoal">
                    <?= e($activity->role) ?>
                </span>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-center">
                <span class="text-xs font-bold uppercase tracking-wide text-charcoal-light">
                    Hours
                </span>

                <span class="text-sm font-bold text-charcoal">
                    <?= $activity->status === 'Completed' ? e($activity->hours) . ' hours' : 'Pending completion' ?>
                </span>
            </div>

            <?php if ($activity->status === 'Completed' && $activity->karma_points > 0): ?>
                <div class="flex flex-col md:flex-row justify-between items-center mt-2 pt-2 border-t border-dashed border-charcoal/10">
                    <span class="text-xs font-bold uppercase tracking-wide text-gold">
                        Karma Points
                    </span>

                    <span class="text-sm font-bold text-forest">
                        +<?= (int)$activity->karma_points ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="p-4 md:p-6 bg-ivory/20 border-t border-charcoal/5 mt-auto flex flex-col sm:flex-row items-center gap-3">
        <?php if ($activity->event_id): ?>
            <a
                href="<?= baseUrl('?page=event-details&id=' . $activity->event_id) ?>" 
                class="block text-center flex-1 w-full px-4 py-2 border border-charcoal/10 text-sm font-bold rounded-lg text-forest bg-white hover:bg-ivory-dark transition-colors shadow-sm"
            >
                View Event
            </a>
        <?php else: ?>
            <a
                href="<?= baseUrl('?page=events') ?>" 
                class="block text-center flex-1 w-full px-4 py-2 border border-charcoal/10 text-sm font-bold rounded-lg text-forest bg-white hover:bg-ivory-dark transition-colors shadow-sm"
            >
                Browse Events
            </a>
        <?php endif; ?>

        <?php if ($activity->status === 'Registered'): ?>
            <form method="POST" onsubmit="return confirm('Are you sure you want to cancel your volunteer registration?');" class="w-full sm:w-auto">
                <input type="hidden" name="cancel_activity_id" value="<?= (int)$activity->id ?>">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 border border-red-200 text-sm font-bold rounded-lg text-red-600 bg-white hover:bg-red-50 transition-colors shadow-sm">
                    Cancel
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php
        return ob_get_clean();
    };
?>

        <?php if ($selectedStatus === 'all' || $selectedStatus === 'upcoming'): ?>
            <!-- Upcoming Activities Section -->
            <div class="mb-12">
                <h2 class="text-2xl md:text-4xl font-serif font-bold text-forest mb-6">Upcoming Activities</h2>
                <?php if (count($upcomingActivities) > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($upcomingActivities as $activity): ?>
                            <?= $renderCard($activity) ?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-2xl p-10 text-center shadow-sm border border-charcoal/5">
                        <h3 class="text-xl font-bold text-charcoal mb-2">No Upcoming Activities</h3>
                        <p class="text-charcoal-light mb-6">Find an opportunity to contribute your time and make a difference.</p>
                        <a href="<?= baseUrl('?page=events') ?>" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-full shadow-sm text-white bg-forest hover:bg-forest-dark transition-colors">Find Volunteer Opportunities</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($selectedStatus === 'all' || $selectedStatus === 'completed'): ?>
            <!-- Completed Activities Section -->
            <div class="mb-12">
                <h2 class="text-2xl md:text-4xl font-serif font-bold text-forest mb-6">Completed Activities</h2>
                <?php if (count($completedActivities) > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($completedActivities as $activity): ?>
                            <?= $renderCard($activity) ?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-2xl p-10 text-center shadow-sm border border-charcoal/5">
                        <h3 class="text-xl font-bold text-charcoal mb-2">No Completed Volunteer Activities Yet</h3>
                        <p class="text-charcoal-light mb-6">Find an opportunity to contribute your time and make a difference.</p>
                        <a href="<?= baseUrl('?page=events') ?>" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-full shadow-sm text-white bg-forest hover:bg-forest-dark transition-colors">Find Volunteer Opportunities</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Impact Section -->
        <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gold/20 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-forest/5 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex-1">
                    <h2 class="text-2xl md:text-4xl font-serif font-bold text-forest mb-2">Your Volunteer Impact</h2>
                    <p class="text-sm md:text-base text-charcoal-light max-w-2xl">Every hour you contribute helps strengthen the communities and organizations supported through KarmaSetu.</p>
                </div>
                <div class="flex flex-col md:flex-row gap-6 sm:gap-8 justify-center text-center md:text-right">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-charcoal-light mb-1">Completed</p>
                        <p class="text-2xl md:text-4xl font-serif font-bold text-forest"><?= $completedCount ?> <?= $completedCount === 1 ? 'Activity' : 'Activities' ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-charcoal-light mb-1">Contributed</p>
                        <p class="text-2xl md:text-4xl font-serif font-bold text-forest"><?= $totalHours ?> Hours</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>
