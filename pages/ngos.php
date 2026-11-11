<?php
include __DIR__ . '/../Database/db_config.php';

/* GET FILTER VALUES */

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'all';
$selectedLocation = isset($_GET['location']) ? $_GET['location'] : 'all';
$selectedVerification = isset($_GET['verification']) ? $_GET['verification'] : 'all';
$selectedSort = isset($_GET['sort']) ? $_GET['sort'] : 'recommended';

/* GET APPROVED NGOs */

$sql = "SELECT 
            n.id,
            n.name,
            n.primary_cause AS category,
            n.city,
            n.state,
            n.mission,
            n.description,
            n.verification_status,
            n.created_at,

            (
                SELECT c.cause_image
                FROM causes c
                WHERE c.ngo_id = n.id
                AND c.status = 'active'
                AND c.cause_image IS NOT NULL
                AND c.cause_image != ''
                ORDER BY c.created_at DESC
                LIMIT 1
            ) AS image,

            (
                SELECT GROUP_CONCAT(DISTINCT cat.contribution_type SEPARATOR ',')
                FROM causes c
                INNER JOIN cause_accepted_types cat ON c.id = cat.cause_id
                WHERE c.ngo_id = n.id
                AND c.status = 'active'
            ) AS accepts

        FROM ngos n
        WHERE n.verification_status = 'approved'
        ORDER BY n.created_at DESC";

$result = mysqli_query($con, $sql);

$allNgos = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {

        $row['location'] = $row['city'] . ', ' . $row['state'];

        $row['description'] = !empty($row['mission'])
            ? $row['mission']
            : $row['description'];

        $row['verified'] = ($row['verification_status'] == 'approved');

        $row['accepts'] = !empty($row['accepts'])
            ? explode(',', $row['accepts'])
            : [];

        $allNgos[] = $row;
    }
}

/* FEATURED NGO */

$featuredNgo = !empty($allNgos) ? $allNgos[0] : null;

/* DYNAMIC FILTER OPTIONS */

$ngoCategories = [];
$ngoLocations = [];

foreach ($allNgos as $ngo) {

    if (!in_array($ngo['category'], $ngoCategories)) {
        $ngoCategories[] = $ngo['category'];
    }

    if (!in_array($ngo['city'], $ngoLocations)) {
        $ngoLocations[] = $ngo['city'];
    }
}

sort($ngoCategories);
sort($ngoLocations);

/* APPLY FILTERS */

$ngos = [];

foreach ($allNgos as $ngo) {

    $matchesSearch = true;
    $matchesCategory = true;
    $matchesLocation = true;
    $matchesVerification = true;

    /* SEARCH FILTER */

    if ($search != '') {

        $searchText = strtolower($search);

        $matchesSearch =
            strpos(strtolower($ngo['name']), $searchText) !== false ||
            strpos(strtolower($ngo['category']), $searchText) !== false ||
            strpos(strtolower($ngo['location']), $searchText) !== false ||
            strpos(strtolower($ngo['description']), $searchText) !== false;
    }

    /* CATEGORY FILTER */

    if ($selectedCategory != 'all') {
        $matchesCategory = strtolower($ngo['category']) == strtolower($selectedCategory);
    }

    /* LOCATION FILTER */

    if ($selectedLocation != 'all') {
        $matchesLocation = strtolower($ngo['city']) == strtolower($selectedLocation);
    }

    /* VERIFICATION FILTER */

    if ($selectedVerification == 'verified') {
        $matchesVerification = $ngo['verified'];
    }

    /* ADD MATCHING NGOs */

    if (
        $matchesSearch &&
        $matchesCategory &&
        $matchesLocation &&
        $matchesVerification
    ) {
        $ngos[] = $ngo;
    }
}

/* SORT NGOs */

if ($selectedSort == 'name_asc') {

    usort($ngos, function ($a, $b) {
        return strcasecmp($a['name'], $b['name']);
    });

} elseif ($selectedSort == 'recent') {

    usort($ngos, function ($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
}
?>
```

<!-- 01 - Page Introduction / Hero -->
<section class="relative pt-12 pb-10 overflow-hidden bg-ivory">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="text-gold font-bold tracking-widest uppercase text-xs">Our Partners</span>
        <h1 class="mt-3 text-4xl md:text-5xl lg:text-6xl font-serif text-forest mb-4 leading-tight">Organizations turning compassion into action.</h1>
        <p class="text-lg md:text-xl text-charcoal-light font-light max-w-2xl mx-auto leading-relaxed">
            KarmaSetu connects you with trustworthy, verified NGOs working across different causes. Discover organizations making a real impact and find one you want to support.
        </p>
        <div class="mt-7">
            <button class="bg-forest text-ivory hover:bg-forest/90 px-8 py-3.5 rounded-full font-bold transition-colors shadow hover:shadow-md">
                Explore NGOs
            </button>
        </div>
    </div>

    <!-- Subtle bridge line -->
    <svg class="absolute top-1/2 left-0 w-full h-32 -translate-y-1/2 opacity-20 pointer-events-none text-gold" preserveAspectRatio="none" viewBox="0 0 1000 100">
        <path d="M0,50 Q250,90 500,50 T1000,50" stroke="currentColor" stroke-width="1" stroke-dasharray="4 4" fill="none" />
    </svg>
</section>

<!-- 02 - Trust / Verification Strip -->
<section class="bg-forest text-ivory py-4 border-y border-gold/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row flex-wrap justify-center items-center gap-4 sm:gap-8 md:gap-12 text-sm font-medium">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Verified organizations</span>
            </div>
            <div class="hidden sm:block text-gold/30">&bull;</div>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z"></path></svg>
                <span>Transparent profiles</span>
            </div>
            <div class="hidden sm:block text-gold/30">&bull;</div>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Multiple ways to contribute</span>
            </div>
        </div>
    </div>
</section>

<div id="ngoWrapper">

    <!-- FILTER SECTION -->
    <section class="bg-white border-b border-gold/10 py-3 sticky top-0 z-40 shadow-sm">
        <form method="GET" action="<?= baseUrl('?page=ngos') ?>">
            <input type="hidden" name="page" value="ngos">

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col xl:flex-row gap-3 items-center justify-between">

                    <!-- Search -->
                    <div class="relative w-full xl:w-1/4 shrink-0">
                        <svg class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input
                            type="text"
                            name="search"
                            id="ngoSearchQuery"
                            value="<?= e($search) ?>"
                            placeholder="Search NGOs..."
                            class="w-full h-12 pl-10 pr-4 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-sm text-charcoal transition-shadow"
                        >
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-wrap xl:flex-nowrap gap-3 w-full xl:w-auto flex-1 xl:justify-end">

                        <select name="category" id="ngoSelectedCategory" class="h-10 px-3 bg-white border border-gray-200 rounded-md focus:ring-2 focus:ring-forest outline-none text-sm text-charcoal flex-1 sm:flex-none min-w-[130px] transition-shadow">
                            <option value="all" <?= $selectedCategory == 'all' ? 'selected' : '' ?>>All Causes</option>

                            <?php foreach ($ngoCategories as $category): ?>
                                <option value="<?= e(strtolower($category)) ?>" <?= strtolower($selectedCategory) == strtolower($category) ? 'selected' : '' ?>>
                                    <?= e($category) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select name="location" id="ngoSelectedLocation" class="h-10 px-3 bg-white border border-gray-200 rounded-md focus:ring-2 focus:ring-forest outline-none text-sm text-charcoal flex-1 sm:flex-none min-w-[130px] transition-shadow">
                            <option value="all" <?= $selectedLocation == 'all' ? 'selected' : '' ?>>All Locations</option>

                            <?php foreach ($ngoLocations as $location): ?>
                                <option value="<?= e(strtolower($location)) ?>" <?= strtolower($selectedLocation) == strtolower($location) ? 'selected' : '' ?>>
                                    <?= e($location) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select name="verification" id="ngoVerification" class="h-10 px-3 bg-white border border-gray-200 rounded-md focus:ring-2 focus:ring-forest outline-none text-sm text-charcoal flex-1 sm:flex-none min-w-[130px] transition-shadow">
                            <option value="all" <?= $selectedVerification == 'all' ? 'selected' : '' ?>>Any Status</option>
                            <option value="verified" <?= $selectedVerification == 'verified' ? 'selected' : '' ?>>Verified Only</option>
                        </select>

                        <select name="sort" id="ngoSort" class="h-10 px-3 bg-white border border-gray-200 rounded-md focus:ring-2 focus:ring-forest outline-none text-sm text-charcoal flex-1 sm:flex-none min-w-[130px] transition-shadow">
                            <option value="recommended" <?= $selectedSort == 'recommended' ? 'selected' : '' ?>>Recommended</option>
                            <option value="name_asc" <?= $selectedSort == 'name_asc' ? 'selected' : '' ?>>Name A-Z</option>
                            <option value="recent" <?= $selectedSort == 'recent' ? 'selected' : '' ?>>Recently Added</option>
                        </select>

                        <button type="submit" class="h-10 px-5 bg-forest text-ivory rounded-md text-sm font-bold hover:bg-forest/90 transition-colors">
                            Apply
                        </button>

                    </div>
                </div>
            </div>
        </form>
    </section>

    <?php if ($featuredNgo): ?>

        <?php
        $featuredImage = !empty($featuredNgo['image'])
            ? baseUrl($featuredNgo['image'])
            : 'https://images.unsplash.com/photo-1577896851231-70ef18881754?w=800&q=80';
        ?>

        <!-- 04 - Featured NGO Area -->
        <section class="pt-6 pb-10 bg-ivory">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-forest rounded-3xl overflow-hidden shadow-2xl flex flex-col md:flex-row relative group">
                    <div class="md:w-1/2 p-8 lg:px-12 lg:py-14 flex flex-col justify-center relative z-10 text-ivory">
                        <div class="w-full">
                            <div class="flex items-center gap-3 mb-5">
                                <span class="inline-flex items-center gap-1.5 bg-green-500/20 text-green-300 text-[11px] font-bold px-3.5 py- rounded-full uppercase tracking-widest border border-green-400/30">
                                    <svg class="w-3.5 h-3.5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Verified Partner
                                </span>
                                <span class="text-[11px] font-bold text-gold uppercase tracking-widest"><?= e($featuredNgo['category']) ?></span>
                            </div>

                            <h2 class="text-3xl md:text-4xl lg:text-5xl font-serif text-white mb-3 leading-tight"><?= e($featuredNgo['name']) ?></h2>

                            <p class="text-ivory/80 text-sm flex items-center mb-5 font-medium">
                                <svg class="w-4 h-4 mr-1.5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <?= e($featuredNgo['location']) ?>
                            </p>

                            <p class="text-ivory/90 mb-8 font-light text-base lg:text-lg leading-relaxed max-w-lg"><?= e($featuredNgo['description']) ?></p>

                            <div class="mb-10">
                                <span class="block text-[10px] font-bold text-ivory/60 uppercase tracking-widest mb-3">Supported Contributions</span>
                                <div class="flex flex-wrap gap-3">
                                    <?php foreach ($featuredNgo['accepts'] as $type): ?>
                                        <span class="bg-ivory/10 border border-ivory/20 text-ivory text-xs px-3 py-1.5 rounded-md font-medium shadow-sm">
                                            <?= e($type == 'items' ? 'Useful Items' : ($type == 'time' ? 'Volunteer Time' : $type)) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div>
                                <a href="<?= baseUrl('?page=ngo-profile&id=' . $featuredNgo['id']) ?>" class="inline-flex items-center justify-center bg-gold text-forest hover:bg-white shadow-md hover:shadow-lg px-8 py-4 rounded-xl text-sm font-bold transition-all group/btn">
                                    <span>View NGO Profile</span>
                                    <svg class="w-4 h-4 ml-2 transform group-hover/btn:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="md:w-1/2 bg-charcoal relative overflow-hidden h-64 md:h-auto">
                        <img src="<?= e($featuredImage) ?>" alt="<?= e($featuredNgo['name']) ?>" class="absolute inset-0 w-full h-full object-cover opacity-60">
                        <div class="absolute inset-0 bg-gradient-to-r from-forest via-forest/80 to-transparent"></div>
                    </div>
                </div>
            </div>
        </section>

    <?php endif; ?>

    <!-- 05 - NGO Grid -->
    <section class="pb-8 bg-ivory relative min-h-[400px]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <?php if (!empty($ngos)): ?>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8" x-ref="grid">

                    <?php foreach ($ngos as $ngo): ?>

                        <div class="ngo-item ngo-card-item"
                             data-name="<?= e(strtolower($ngo['name'])) ?>"
                             data-category="<?= e(strtolower($ngo['category'])) ?>"
                             data-location="<?= e(strtolower($ngo['location'])) ?>"
                             data-description="<?= e(strtolower($ngo['description'])) ?>"
                             data-verified="<?= e($ngo['verified'] ? 'true' : 'false') ?>">

                            <?php
                            $id = $ngo['id'];
                            $name = $ngo['name'];
                            $category = $ngo['category'];
                            $location = $ngo['location'];
                            $description = $ngo['description'];
                            $accepts = $ngo['accepts'];
                            $verified = $ngo['verified'];
                            $image = $ngo['image'];

                            include __DIR__ . "/../includes/components/ngos/ngo-card.php";
                            ?>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <!-- 06 - Empty / No Results State -->
                <div id="ngoEmptyState2" class="text-center py-16">

                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>

                    <h3 class="text-xl font-serif text-forest mb-2">No organizations match your current filters.</h3>

                    <p class="text-charcoal-light mb-6">Try adjusting your search or filter criteria to find more NGOs.</p>

                    <a href="<?= baseUrl('?page=ngos') ?>" id="ngoClearFiltersBtn" class="inline-block bg-forest text-ivory hover:bg-forest/90 px-6 py-2 rounded-full font-medium transition-colors">
                        Clear all filters
                    </a>

                </div>

            <?php endif; ?>

        </div>
    </section>

</div>

<!-- 07 - NGO Registration CTA -->
<section class="pt-10 pb-12 bg-charcoal text-ivory text-center border-t border-gold/20">
    </br>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center">
        <span class="text-gold font-bold tracking-widest uppercase text-xs mb-2">For NGOs</span>
        <h2 class="text-white text-3xl md:text-4xl font-serif mb-5 leading-tight w-full">Are you an organization creating change?</h2>
        <p class="text-white text-lg mb-8 font-light leading-relaxed max-w-xl mx-auto">
            Join KarmaSetu and connect your work with people who want to contribute. Reach a wider audience and manage your contributions transparently.
        </p>
        <a href="<?= baseUrl('auth/ngo-register.php') ?>" class="bg-gold text-forest hover:bg-white shadow-md px-8 py-3.5 rounded-full font-bold transition-colors inline-block text-center">
            Register Your NGO
        </a>
    </div>
</section>
