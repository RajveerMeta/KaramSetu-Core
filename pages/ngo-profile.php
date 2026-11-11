
<?php
require_once __DIR__ . '/../Database/db_config.php';

/* GET NGO ID */

$ngoId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

/* GET NGO DETAILS */

$sql = "SELECT 
            id,
            name,
            primary_cause AS category,
            CONCAT(city, ', ', state) AS location,
            mission,
            description,
            (verification_status = 'approved') AS verified
        FROM ngos
        WHERE id = $ngoId
        LIMIT 1";

$result = mysqli_query($con, $sql);
$ngo = mysqli_fetch_assoc($result);

if (!$ngo) {
    echo "<div class='py-20 text-center'><h2 class='text-2xl font-bold'>NGO not found</h2></div>";
    return;
}

/* GET FOCUS AREAS */

$ngo['causes'] = [];

$sql = "SELECT DISTINCT category
        FROM causes
        WHERE ngo_id = $ngoId
        AND status = 'active'";

$result = mysqli_query($con, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $ngo['causes'][] = $row['category'];
}

/* GET ACCEPTED CONTRIBUTION TYPES */

$ngo['accepts'] = [];

$sql = "SELECT DISTINCT cat.contribution_type
        FROM cause_accepted_types cat
        INNER JOIN causes c ON cat.cause_id = c.id
        WHERE c.ngo_id = $ngoId
        AND c.status = 'active'";

$result = mysqli_query($con, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $ngo['accepts'][] = $row['contribution_type'];
}

/* GET ACTIVE CAUSES */

$ngo['campaigns'] = [];

$sql = "SELECT 
            c.id,
            c.title,
            c.short_description,
            c.target_amount,
            c.status,
            COALESCE(
                (
                    SELECT SUM(co.monetary_amount)
                    FROM contributions co
                    WHERE co.cause_id = c.id
                    AND co.ngo_id = c.ngo_id
                    AND co.contribution_type = 'money'
                    AND co.payment_status = 'completed'
                ), 0
            ) AS collected_amount
        FROM causes c
        WHERE c.ngo_id = $ngoId
        AND c.status = 'active'
        ORDER BY c.created_at DESC";

$result = mysqli_query($con, $sql);

while ($row = mysqli_fetch_assoc($result)) {

    $progress = 0;

    if ($row['target_amount'] > 0) {
        $progress = round(
            ($row['collected_amount'] / $row['target_amount']) * 100
        );
    }

    $progress = min(100, $progress);

    /* GET ACCEPTED TYPES FOR THIS CAUSE */

    $accepts = [];

    $causeId = (int) $row['id'];

    $typeSql = "SELECT contribution_type
                FROM cause_accepted_types
                WHERE cause_id = $causeId";

    $typeResult = mysqli_query($con, $typeSql);

    while ($typeRow = mysqli_fetch_assoc($typeResult)) {
        $accepts[] = $typeRow['contribution_type'];
    }

    $ngo['campaigns'][] = [
        'id' => $causeId,
        'title' => $row['title'],
        'need_text' => $row['short_description'],
        'location' => $ngo['location'],
        'progress' => $progress,
        'accepts' => $accepts,
        'cta_text' => 'View Cause',
        'status' => 'active'
    ];
}

/* GET UPCOMING EVENTS */

$ngo['events'] = [];

$sql = "SELECT 
            id,
            title,
            MONTHNAME(event_date) AS month,
            DAY(event_date) AS day,
            short_description AS description
        FROM events
        WHERE ngo_id = $ngoId
        AND status = 'upcoming'
        AND event_date >= CURDATE()
        ORDER BY event_date ASC";

$result = mysqli_query($con, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $ngo['events'][] = $row;
}

/* VOLUNTEER OPPORTUNITIES */

$ngo['opportunities'] = [];

?>

<!-- 03. NGO PROFILE HEADER -->
<section class="pt-16 md:pt-20 pb-10 bg-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col md:flex-row gap-8 items-start md:items-center">
            <div class="w-28 h-28 flex-shrink-0 bg-ivory border border-gold/30 rounded-2xl flex items-center justify-center shadow-sm">
                <span class="text-4xl font-serif text-forest"><?= e(substr($ngo['name'], 0, 1)) ?></span>
            </div>
            
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-3 mb-2">
                    <span class="text-gold font-bold tracking-widest uppercase text-xs"><?= e($ngo['category']) ?></span>
                    <?php if($ngo['verified']): ?>
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-green-500/10 text-green-700 border border-green-500/20 px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Verified Profile
                        </span>
                    <?php endif; ?>
                </div>
                <h1 class="text-3xl md:text-5xl font-serif text-forest mb-3"><?= e($ngo['name']) ?></h1>
                <div class="flex items-center text-charcoal-light font-medium text-sm gap-2 mb-4">
                    <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span><?= e($ngo['location']) ?></span>
                </div>
                <p class="text-lg text-charcoal font-light max-w-3xl">
                    <?= e($ngo['mission'] ?? $ngo['description']) ?>
                </p>
            </div>
            
            <!-- 04. PRIMARY ACTION AREA -->
            <div class="flex flex-col gap-3 w-full sm:w-auto mt-6 md:mt-0">
                <button class="bg-forest text-ivory hover:bg-forest-light px-8 py-3.5 rounded-full font-bold transition-colors text-center shadow-md">
                    Support This NGO
                </button>
                <a href="#active-causes" class="bg-white border border-forest/20 text-forest hover:bg-ivory px-8 py-3.5 rounded-full font-medium transition-colors text-center">
                    View Causes
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 05. ABOUT THE NGO & 06. WHAT THEY WORK ON -->
<section class="py-12 bg-ivory">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2">
                <h2 class="text-3xl font-serif text-forest mb-6">About the Organization</h2>
                <div class="prose prose-lg text-charcoal-light font-light max-w-none space-y-4">
                    <p><?= e($ngo['description']) ?></p>
                    <p><?= e($ngo['mission']) ?></p>
                </div>
            </div>

            <div>
                <div class="bg-white p-8 rounded-2xl border border-gold/30 shadow-md">
                    <h3 class="text-xl font-serif text-forest mb-5 border-b border-gold/10 pb-4">Focus Areas</h3>
                    <ul class="space-y-4">
                        <?php foreach($ngo['causes'] as $cause): ?>
                            <li class="flex items-center gap-3 text-charcoal-light font-medium">
                                <span class="w-2.5 h-2.5 rounded-full bg-gold shadow-sm"></span>
                                <?= e($cause) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 07. WAYS TO CONTRIBUTE -->
<section class="py-16 bg-white border-y border-gold/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-gold font-bold tracking-widest uppercase text-xs">How you can help</span>
            <h2 class="text-3xl font-serif text-forest mt-2 mb-4">Multiple Ways to Contribute</h2>
            <p class="text-charcoal-light font-light max-w-2xl mx-auto">This organization accepts various forms of contribution. Choose the way that works best for you.</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php if(in_array('money', $ngo['accepts'])): ?>
            <div class="bg-ivory p-6 rounded-2xl text-center border border-gold/20 shadow-sm hover:border-forest/40 hover:shadow-md hover:-translate-y-1 transition-all duration-300 ease-out">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mx-auto mb-3 text-forest shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h4 class="font-medium text-forest mb-1">Money</h4>
                <p class="text-sm text-charcoal-light">Support financial needs.</p>
            </div>
            <?php endif; ?>
            
            <?php if(in_array('food', $ngo['accepts'])): ?>
            <div class="bg-ivory p-6 rounded-2xl text-center border border-gold/20 shadow-sm hover:border-forest/40 hover:shadow-md hover:-translate-y-1 transition-all duration-300 ease-out">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mx-auto mb-3 text-forest shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15a2 2 0 01-2 2H5a2 2 0 01-2-2m18 0V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6m18 0h-2m-2 0H7m12 0a9 9 0 01-2 2H9a2 2 0 01-2-2"></path></svg>
                </div>
                <h4 class="font-medium text-forest mb-1">Food</h4>
                <p class="text-sm text-charcoal-light">Contribute food supplies.</p>
            </div>
            <?php endif; ?>

            <?php if(in_array('clothes', $ngo['accepts'])): ?>
            <div class="bg-ivory p-6 rounded-2xl text-center border border-gold/20 shadow-sm hover:border-forest/40 hover:shadow-md hover:-translate-y-1 transition-all duration-300 ease-out">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mx-auto mb-3 text-forest shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <h4 class="font-medium text-forest mb-1">Clothes</h4>
                <p class="text-sm text-charcoal-light">Provide usable clothing.</p>
            </div>
            <?php endif; ?>
            
            <?php if(in_array('items', $ngo['accepts'])): ?>
            <div class="bg-ivory p-6 rounded-2xl text-center border border-gold/20 shadow-sm hover:border-forest/40 hover:shadow-md hover:-translate-y-1 transition-all duration-300 ease-out">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mx-auto mb-3 text-forest shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <h4 class="font-medium text-forest mb-1">Useful Items</h4>
                <p class="text-sm text-charcoal-light">Donate practical resources.</p>
            </div>
            <?php endif; ?>
            
            <?php if(in_array('time', $ngo['accepts'])): ?>
            <div class="bg-ivory p-6 rounded-2xl text-center border border-gold/20 shadow-sm hover:border-forest/40 hover:shadow-md hover:-translate-y-1 transition-all duration-300 ease-out">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mx-auto mb-3 text-forest shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h4 class="font-medium text-forest mb-1">Volunteer Time</h4>
                <p class="text-sm text-charcoal-light">Contribute your skills.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- 08. ACTIVE CAUSES / NEEDS -->
<section id="active-causes" class="py-16 bg-ivory">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-serif text-forest mb-8">Active Needs</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
            <?php foreach($ngo['campaigns'] as $campaign): ?>
                <?php
                $id = $campaign['id'];
                $title = $campaign['title'];
                $needText = $campaign['need_text'];
                $location = $campaign['location'];
                $progress = $campaign['progress'];
                $accepts = $campaign['accepts'];
                $ctaText = $campaign['cta_text'];
                $status = $campaign['status'];

                include __DIR__ . '/../includes/components/causes/cause-card.php';
                ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 09. UPCOMING EVENTS & 10. VOLUNTEER OPPORTUNITIES -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Upcoming Events -->
            <!-- Upcoming Events -->
<div>
    <h2 class="text-3xl font-serif text-forest mb-6">Upcoming Events</h2>

    <div class="space-y-6">

        <?php if (!empty($ngo['events'])): ?>

            <?php foreach($ngo['events'] as $event): ?>

                <div class="border border-gold/20 rounded-2xl p-6 bg-ivory hover:bg-white hover:border-forest/40 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-out group cursor-pointer">

                    <div class="flex items-start gap-5">

                        <div class="bg-white text-forest text-center px-4 py-3 rounded-xl shadow-sm border border-gold/20 group-hover:border-forest/30 transition-colors flex-shrink-0">

                            <span class="block text-xs font-bold tracking-widest text-gold uppercase mb-1">
                                <?= e($event['month']) ?>
                            </span>

                            <span class="block text-2xl font-serif leading-none">
                                <?= e($event['day']) ?>
                            </span>

                        </div>

                        <div class="flex-1 min-w-0 w-full" style="overflow-wrap: anywhere;">

                            <h4 class="text-xl font-medium text-forest mb-2 group-hover:text-gold transition-colors"
                                style="overflow-wrap: anywhere;">
                                <?= e($event['title']) ?>
                            </h4>

                            <p class="text-sm text-charcoal-light mb-3 leading-relaxed"
                               style="overflow-wrap: anywhere; word-break: break-word;">
                                <?= e($event['description']) ?>
                            </p>

                            <span class="inline-flex items-center text-forest font-bold text-sm group-hover:text-gold transition-colors">
                                View Event

                                <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M14 5l7 7m0 0l-7 7m7-7H3">
                                    </path>
                                </svg>
                            </span>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <p class="text-sm text-charcoal-light">
                No upcoming events at the moment.
            </p>

        <?php endif; ?>

    </div>
</div>

            <!-- Volunteer Opportunities -->
            <div>
                <h2 class="text-3xl font-serif text-forest mb-6">Volunteer Opportunities</h2>
                <div class="space-y-6">
                    <?php foreach($ngo['opportunities'] as $opportunity): ?>
                        <div class="border border-gold/20 rounded-2xl p-6 bg-ivory hover:bg-white hover:border-forest/40 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-out group cursor-pointer">
                            <h4 class="text-xl font-medium text-forest mb-2 group-hover:text-gold transition-colors"><?= e($opportunity['title']) ?></h4>
                            <div class="flex items-center gap-4 text-xs font-bold text-charcoal uppercase tracking-wider mb-3">
                                <span class="flex items-center gap-1.5 bg-white px-2.5 py-1 rounded shadow-sm border border-gold/10">
                                    <svg class="w-3.5 h-3.5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <?= e($opportunity['duration']) ?>
                                </span>
                            </div>
                            <p class="text-sm text-charcoal-light mb-4 leading-relaxed"><?= e($opportunity['description']) ?></p>
                            <span class="inline-block bg-forest/5 text-forest border border-forest/20 group-hover:bg-forest group-hover:text-ivory px-5 py-2 rounded-lg font-bold text-sm transition-colors">
                                Apply to Volunteer
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 11. TRANSPARENCY / VERIFICATION -->
<?php if($ngo['verified']): ?>
    <?php include __DIR__ . "/../includes/components/causes/verification-info.php"; ?>
<?php endif; ?>

<!-- 12. FINAL SUPPORT CTA -->
<section class="py-16 bg-forest text-ivory text-center">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl md:text-4xl font-serif text-gold mb-5">Your contribution can become part of their work.</h2>
        <p class="text-ivory/80 text-lg mb-8 font-light max-w-2xl mx-auto">
            KarmaSetu provides multiple ways to contribute. Whether you give money, items, or time, you make a difference.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="<?= baseUrl('?page=contribute&id=' . e($ngo['id'])) ?>" class="bg-gold text-forest hover:bg-white px-8 py-3.5 rounded-full font-bold transition-colors inline-block text-center">
                Support This NGO &rarr;
            </a>
            <a href="#active-causes" class="bg-transparent border border-ivory/30 hover:border-ivory hover:bg-white/5 px-8 py-3.5 rounded-full font-medium transition-colors inline-block text-center">
                Explore Causes
            </a>
        </div>
    </div>
</section>
