
<?php
require_once __DIR__ . '/../Database/db_config.php';

/* GET FILTER VALUES */

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'all';
$location = isset($_GET['location']) ? $_GET['location'] : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'recent';

/* BOOKS ARE STORED AS ITEMS */

if ($type == 'books') {
    $type = 'items';
}

/* GET LOCATIONS */

$locationsQuery = "SELECT DISTINCT n.city
                   FROM ngos n
                   INNER JOIN causes c ON c.ngo_id = n.id
                   WHERE c.status = 'active'
                   AND n.verification_status = 'approved'
                   AND n.city IS NOT NULL
                   AND n.city != ''
                   ORDER BY n.city ASC";

$locationsResult = mysqli_query($con, $locationsQuery);


/* BUILD CAUSE FILTERS */

$where = "WHERE c.status = 'active'
          AND n.verification_status = 'approved'";

if ($search != '') {
    $searchValue = mysqli_real_escape_string($con, $search);

    $where .= " AND (
        c.title LIKE '%$searchValue%'
        OR c.short_description LIKE '%$searchValue%'
        OR c.detailed_description LIKE '%$searchValue%'
        OR n.name LIKE '%$searchValue%'
    )";
}

if ($location != 'all') {
    $locationValue = mysqli_real_escape_string($con, $location);
    $where .= " AND n.city = '$locationValue'";
}

if ($type != 'all') {
    $typeValue = mysqli_real_escape_string($con, $type);

    $where .= " AND EXISTS (
        SELECT 1
        FROM cause_accepted_types cat_filter
        WHERE cat_filter.cause_id = c.id
        AND cat_filter.contribution_type = '$typeValue'
    )";
}


/* SORT CAUSES */

$orderBy = "c.created_at DESC";

if ($sort == 'urgent') {
    $orderBy = "c.end_date ASC";
} elseif ($sort == 'recent') {
    $orderBy = "c.created_at DESC";
}


/* GET CAUSES */

$causesQuery = "SELECT
                    c.*,
                    n.name AS ngo_name,
                    n.city AS ngo_city,
                    n.state AS ngo_state,
                    n.verification_status,

                    COALESCE((
                        SELECT SUM(co.monetary_amount)
                        FROM contributions co
                        WHERE co.cause_id = c.id
                        AND co.contribution_type = 'money'
                        AND co.payment_status = 'completed'
                    ), 0) AS raised_amount,

                    (
                        SELECT GROUP_CONCAT(DISTINCT cat.contribution_type SEPARATOR ',')
                        FROM cause_accepted_types cat
                        WHERE cat.cause_id = c.id
                    ) AS accepted_types_str

                FROM causes c
                INNER JOIN ngos n ON n.id = c.ngo_id
                $where
                ORDER BY $orderBy";

$causesResult = mysqli_query($con, $causesQuery);


/* GET FEATURED CAUSE USING SAME FILTERS */

$featuredQuery = "SELECT
                    c.*,
                    n.name AS ngo_name,
                    n.city AS ngo_city,
                    n.state AS ngo_state,
                    n.verification_status,

                    COALESCE((
                        SELECT SUM(co.monetary_amount)
                        FROM contributions co
                        WHERE co.cause_id = c.id
                        AND co.contribution_type = 'money'
                        AND co.payment_status = 'completed'
                    ), 0) AS raised_amount,

                    (
                        SELECT GROUP_CONCAT(DISTINCT cat.contribution_type SEPARATOR ',')
                        FROM cause_accepted_types cat
                        WHERE cat.cause_id = c.id
                    ) AS accepted_types_str

                FROM causes c
                INNER JOIN ngos n ON n.id = c.ngo_id
                $where
                ORDER BY c.end_date ASC
                LIMIT 1";

$featuredResult = mysqli_query($con, $featuredQuery);
$featuredCause = $featuredResult ? mysqli_fetch_assoc($featuredResult) : null;


/* FEATURED CAUSE VALUES */

$featuredId = 0;
$featuredTitle = '';
$featuredImage = '';
$featuredNgo = '';
$featuredLocation = '';
$featuredDescription = '';
$featuredRaised = 0;
$featuredTarget = 0;
$featuredProgress = 0;
$featuredAccepts = [];

if ($featuredCause) {
    $featuredId = $featuredCause['id'];
    $featuredTitle = $featuredCause['title'];
    $featuredImage = $featuredCause['cause_image'];
    $featuredNgo = $featuredCause['ngo_name'];

    $featuredLocation = $featuredCause['ngo_city'] . ', ' . $featuredCause['ngo_state'];

    $featuredDescription = !empty($featuredCause['short_description'])
        ? $featuredCause['short_description']
        : 'Community support';

    $featuredRaised = (float) $featuredCause['raised_amount'];
    $featuredTarget = (float) $featuredCause['target_amount'];

    $featuredProgress = $featuredTarget > 0
        ? min(100, round(($featuredRaised / $featuredTarget) * 100))
        : 0;

    $featuredAccepts = !empty($featuredCause['accepted_types_str'])
        ? explode(',', $featuredCause['accepted_types_str'])
        : [];
}


/* CONTRIBUTION MATCHER COUNTS */

$matcherCounts = [
    'money' => 0,
    'food' => 0,
    'clothes' => 0,
    'items' => 0,
    'time' => 0
];

$matcherQuery = "SELECT
                    cat.contribution_type,
                    COUNT(DISTINCT c.id) AS total_causes
                 FROM cause_accepted_types cat
                 INNER JOIN causes c ON c.id = cat.cause_id
                 INNER JOIN ngos n ON n.id = c.ngo_id
                 WHERE c.status = 'active'
                 AND n.verification_status = 'approved'
                 GROUP BY cat.contribution_type";

$matcherResult = mysqli_query($con, $matcherQuery);

if ($matcherResult) {
    while ($matcherRow = mysqli_fetch_assoc($matcherResult)) {
        $matcherCounts[$matcherRow['contribution_type']] = (int) $matcherRow['total_causes'];
    }
}

/* BOOKS USE THE ITEMS CATEGORY */

$matcherCounts['books'] = $matcherCounts['items'];

?>
<!-- 01 - Page Introduction -->
    <section class="relative pt-16 pb-12 overflow-hidden bg-ivory">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <span class="text-gold font-bold tracking-widest uppercase text-xs">Current Needs</span>
            <h1 class="mt-3 text-5xl md:text-6xl font-serif text-forest mb-6">Where help is needed.</h1>
            <p class="text-xl text-charcoal-light font-light max-w-2xl">
                Discover verified causes and choose the kind of contribution that feels right for you.
            </p>
        </div>
        
        <!-- Subtle bridge line -->
        <svg class="absolute top-1/2 left-0 w-full h-32 -translate-y-1/2 opacity-20 pointer-events-none text-gold" preserveAspectRatio="none" viewBox="0 0 1000 100">
            <path d="M0,50 Q250,90 500,50 T1000,50" stroke="currentColor" stroke-width="1" stroke-dasharray="4 4" fill="none" />
        </svg>
    </section>

    <div id="causesWrapper">
        
        <?php include __DIR__ . "/../includes/components/causes/cause-filters.php"; ?>
        
        <?php include __DIR__ . "/../includes/components/causes/featured-cause.php"; ?>
        
        <!-- 05 - Cause Discovery Grid -->
        <section class="py-16 bg-ivory relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl md:text-4xl font-serif text-forest mb-10">Explore current needs</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php if ($causesResult && mysqli_num_rows($causesResult) > 0): ?>
                        <?php while ($causeRow = mysqli_fetch_assoc($causesResult)): ?>
                            <?php
                            $id = $causeRow['id'];
                            $title = $causeRow['title'];
                            $needText = !empty($causeRow['short_description']) ? $causeRow['short_description'] : 'Community support';
                            $location = !empty($causeRow['ngo_city']) ? $causeRow['ngo_city'] : 'India';
                            $target = (float) $causeRow['target_amount'];
                            $raised = (float) $causeRow['raised_amount'];
                            $progress = $target > 0 ? min(100, round(($raised / $target) * 100)) : 0;
                            $status = $causeRow['status'];
                            $ctaText = 'See how you can help';
                            $accepts = !empty($causeRow['accepted_types_str']) ? explode(',', $causeRow['accepted_types_str']) : ['money'];
                            include __DIR__ . "/../includes/components/causes/cause-card.php";
                            ?>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-span-full py-12 text-center bg-white rounded-3xl border border-gold/20 p-8">
                            <h3 class="text-xl font-serif font-bold text-forest mb-2">No Active Causes Found</h3>
                            <p class="text-charcoal-light">Please check back soon or explore other ways to contribute.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        
    </div>

    <?php include __DIR__ . "/../includes/components/causes/contribution-matcher.php"; ?>

    <?php include __DIR__ . "/../includes/components/causes/verification-info.php"; ?>

    <section class="py-24 bg-forest text-ivory text-center">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-serif mb-6">Can't find what you're looking for?</h2>
            <p class="text-ivory/80 text-lg mb-10 font-light">
                Explore verified NGOs and discover the work happening behind each cause. Or, if you have time to give, find out how you can volunteer.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="<?= baseUrl('?page=ngos') ?>" class="inline-block bg-gold text-forest hover:bg-white px-8 py-3.5 rounded-full font-bold transition-colors">
                    Explore NGOs &rarr;
                </a>
                <a href="<?= baseUrl('?page=events') ?>" class="inline-block bg-transparent border border-ivory/30 hover:border-ivory px-8 py-3.5 rounded-full font-medium transition-colors">
                    Become a Volunteer &rarr;
                </a>
            </div>
        </div>
    </section>
