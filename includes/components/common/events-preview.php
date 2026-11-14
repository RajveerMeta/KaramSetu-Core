<?php
$events_preview_query = "
    SELECT 
        e.*,
        n.name AS ngo_name
    FROM events e
    INNER JOIN ngos n ON n.id = e.ngo_id
    WHERE e.status = 'upcoming'
    AND n.verification_status = 'approved'
    AND e.event_date >= CURRENT_DATE()
    ORDER BY e.event_date ASC
    LIMIT 4
";

$events_preview_res = mysqli_query($con, $events_preview_query);
$events_list = [];
if ($events_preview_res) {
    while ($erow = mysqli_fetch_assoc($events_preview_res)) {
        $events_list[] = $erow;
    }
}
$has_events = (count($events_list) > 0);
?>

<section class="py-24 bg-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16">
            <div class="max-w-2xl">
                <span class="text-gold font-bold tracking-widest uppercase text-sm">Experience</span>
                <h2 class="mt-3 text-4xl md:text-5xl font-serif text-forest">Help can also be an experience.</h2>
            </div>
            <a href="<?= baseUrl('?page=events') ?>" class="inline-flex items-center text-forest font-semibold hover:text-gold transition-colors mt-6 md:mt-0">
                <span class="border-b-2 border-forest hover:border-gold pb-1 transition-colors">Explore fundraising events</span>
                <svg class="ml-2 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>

        <?php if ($has_events): ?>
            <?php
            $featuredEvent = $events_list[0];
            $listEvents = array_slice($events_list, 1);
            
            $f_img = 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80';
            if (!empty($featuredEvent['event_image'])) {
                if (strpos($featuredEvent['event_image'], 'http') === 0) {
                    $f_img = $featuredEvent['event_image'];
                } elseif (strpos($featuredEvent['event_image'], 'photo-') === 0) {
                    $f_img = 'https://images.unsplash.com/' . $featuredEvent['event_image'] . '?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80';
                } elseif (strpos($featuredEvent['event_image'], 'uploads/') === 0) {
                    $f_img = baseUrl($featuredEvent['event_image']);
                } else {
                    $f_img = baseUrl('uploads/events/' . basename($featuredEvent['event_image']));
                }
            }
            
            $f_time = strtotime($featuredEvent['event_date']);
            $f_month = date('M', $f_time);
            $f_day = date('d', $f_time);
            ?>
            <div class="flex flex-col lg:flex-row gap-8">
                
                <!-- Featured Event -->
                <div class="lg:w-1/2 relative rounded-3xl overflow-hidden group h-[500px]">
                    <img src="<?= $f_img ?>" alt="<?= $featuredEvent['title'] ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                    <div class="absolute inset-0 bg-gradient-to-t from-forest-dark via-forest-dark/40 to-transparent"></div>
                    
                    <div class="absolute bottom-0 left-0 right-0 p-8 text-ivory">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="bg-gold text-forest text-center px-4 py-2 rounded-lg font-bold">
                                <span class="block text-sm uppercase"><?= $f_month ?></span>
                                <span class="block text-2xl"><?= $f_day ?></span>
                            </div>
                            <div>
                                <span class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-medium border border-white/20 capitalize"><?= $featuredEvent['category'] ?></span>
                            </div>
                        </div>
                        <h3 class="text-3xl font-serif mb-2 group-hover:text-gold transition-colors"><?= $featuredEvent['title'] ?></h3>
                        <p class="text-ivory/80 mb-6 line-clamp-2"><?= !empty($featuredEvent['short_description']) ? $featuredEvent['short_description'] : 'Join us for this meaningful community event supporting verified grassroots initiatives.' ?></p>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium flex items-center"><svg class="w-4 h-4 mr-1 text-gold shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg> <?= $featuredEvent['location'] ?></span>
                            <a href="<?= baseUrl('?page=event-details&id=' . $featuredEvent['id']) ?>" class="bg-ivory text-forest px-6 py-2 rounded-full text-sm font-bold hover:bg-gold transition-colors inline-block">Get Tickets</a>
                        </div>
                    </div>
                </div>

                <!-- Event List -->
                <div class="lg:w-1/2 flex flex-col gap-4 justify-between">
                    
                    <?php if (!empty($listEvents)): ?>
                        <?php foreach ($listEvents as $item): ?>
                            <?php
                            $i_img = 'https://images.unsplash.com/photo-1544027993-37dbfe43562a?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80';
                            if (!empty($item['event_image'])) {
                                if (strpos($item['event_image'], 'http') === 0) {
                                    $i_img = $item['event_image'];
                                } elseif (strpos($item['event_image'], 'photo-') === 0) {
                                    $i_img = 'https://images.unsplash.com/' . $item['event_image'] . '?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80';
                                } elseif (strpos($item['event_image'], 'uploads/') === 0) {
                                    $i_img = baseUrl($item['event_image']);
                                } else {
                                    $i_img = baseUrl('uploads/events/' . basename($item['event_image']));
                                }
                            }
                            $i_time = strtotime($item['event_date']);
                            $i_date_str = date('M d', $i_time);
                            ?>
                            <a href="<?= baseUrl('?page=event-details&id=' . $item['id']) ?>" class="flex flex-col sm:flex-row items-center gap-6 bg-ivory rounded-2xl p-4 border border-gold/10 hover:border-gold/40 hover:bg-white transition-all group h-full">
                                <div class="w-full sm:w-32 h-32 rounded-xl overflow-hidden shrink-0 bg-forest/5">
                                    <img src="<?= $i_img ?>" alt="<?= $item['title'] ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="text-gold font-bold text-sm"><?= $i_date_str ?></span>
                                        <span class="bg-forest/10 text-forest px-2 py-1 rounded text-xs font-medium capitalize"><?= $item['category'] ?></span>
                                    </div>
                                    <h4 class="text-xl font-serif text-forest group-hover:text-gold transition-colors mb-2 truncate"><?= $item['title'] ?></h4>
                                    <p class="text-sm text-charcoal-light flex items-center"><svg class="w-4 h-4 mr-1 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg> <span class="truncate"><?= $item['location'] ?></span></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (count($listEvents) < 3): ?>
                        <div class="flex flex-col sm:flex-row items-center gap-6 bg-ivory/60 rounded-2xl p-6 border border-dashed border-gold/30 h-full text-center sm:text-left justify-between">
                            <div>
                                <h4 class="text-lg font-serif text-forest mb-1">More Events Coming Soon</h4>
                                <p class="text-sm text-charcoal-light">New charity drives, concerts, and volunteer workshops are being planned by our verified NGO partners.</p>
                            </div>
                            <a href="<?= baseUrl('?page=events') ?>" class="px-5 py-2.5 bg-forest text-ivory rounded-full text-xs font-bold hover:bg-forest-dark transition-colors whitespace-nowrap shrink-0">
                                View Calendar
                            </a>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-16 bg-ivory rounded-3xl border border-gold/20 p-8 shadow-sm">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 text-gold border border-gold/20">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-2xl font-serif text-forest mb-2">No Upcoming Events Scheduled</h3>
                <p class="text-sm text-charcoal-light max-w-md mx-auto mb-6">There are currently no upcoming fundraising events. Check back soon or discover ongoing causes you can support today.</p>
                <a href="<?= baseUrl('?page=causes') ?>" class="inline-block bg-forest text-ivory px-6 py-2.5 rounded-full text-xs font-bold hover:bg-forest-dark transition-colors">
                    Explore Causes
                </a>
            </div>
        <?php endif; ?>

    </div>
</section>
