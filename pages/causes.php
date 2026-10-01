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
                    
                    <?php $id = '8'; $title = 'Children\'s Education Support'; $needText = 'Books + educational materials'; $location = 'Vadodara'; $rogress = '45'; $ccepts = '[\'books\', \'items\', \'money\']'; $ctaText = 'See how you can help'; $status = 'active'; $progress = 45; $accepts = ['books', 'items', 'money']; include __DIR__ . "/../includes/components/causes/cause-card.php"; ?>
                    
                    
                    <?php $id = '5'; $title = 'Winter Clothing Collection'; $needText = '500 clothing items'; $location = 'Surat'; $rogress = '64'; $ccepts = '[\'clothes\']'; $ctaText = 'Give Clothes'; $status = 'active'; $progress = 64; $accepts = ['clothes']; include __DIR__ . "/../includes/components/causes/cause-card.php"; ?>
                    
                    
                    <?php $id = '4'; $title = 'Community Meal Program'; $needText = 'Food supplies'; $location = 'Ahmedabad'; $rogress = '72'; $ccepts = '[\'food\', \'money\']'; $ctaText = 'Support Meals'; $status = 'urgent'; $progress = 72; $accepts = ['food', 'money']; include __DIR__ . "/../includes/components/causes/cause-card.php"; ?>
                    
                    
                    <?php $id = '3'; $title = 'Community Health Assistance'; $needText = 'Medical/community resources'; $location = 'Rajkot'; $rogress = '30'; $ccepts = '[\'money\', \'items\', \'time\']'; $ctaText = 'View Cause'; $status = 'active'; $progress = 30; $accepts = ['money', 'items', 'time']; include __DIR__ . "/../includes/components/causes/cause-card.php"; ?>
                    
                    
                    <?php $id = '1'; $title = 'Shelter Home Repair'; $needText = 'Building materials & funds'; $location = 'Mumbai'; $rogress = '90'; $ccepts = '[\'money\', \'items\']'; $ctaText = 'Help Finish'; $status = 'nearly-complete'; $progress = 90; $accepts = ['money', 'items']; include __DIR__ . "/../includes/components/causes/cause-card.php"; ?>
                    
                  
                    <?php $id = '2'; $title = 'Weekend Teaching Camp'; $needText = 'Volunteer educators'; $location = 'Pune'; $rogress = '100'; $ccepts = '[\'time\']'; $ctaText = 'Completed'; $status = 'completed'; $progress = 100; $accepts = ['time']; include __DIR__ . "/../includes/components/causes/cause-card.php"; ?>
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
                <button class="bg-gold text-forest hover:bg-white px-8 py-3.5 rounded-full font-bold transition-colors">
                    Explore NGOs &rarr;
                </button>
                <button class="bg-transparent border border-ivory/30 hover:border-ivory px-8 py-3.5 rounded-full font-medium transition-colors">
                    Become a Volunteer &rarr;
                </button>
            </div>
        </div>
    </section>
