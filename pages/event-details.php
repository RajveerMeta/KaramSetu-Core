<?php
$events = require __DIR__ . '/../data/events.php';
$id = $_GET['id'] ?? 1;
$event = null;
foreach ($events as $e) {
    if ($e['id'] == $id) {
        $event = $e;
        break;
    }
}
if (!$event) {
    echo "<div class='py-20 text-center'><h2 class='text-2xl md:text-4xl font-bold'>Event not found</h2></div>";
    return;
}
?>



    <!-- 01. EVENT HERO SECTION -->
    <section class="bg-ivory pb-12 pt-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Hero Image Box -->
            <div class="relative w-full aspect-[16/9] md:aspect-[16/7] bg-forest/5 rounded-3xl overflow-hidden shadow-sm mb-10 group">
                <img src="<?= e($event['image']) ?>" alt="<?= e($event['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                
                <!-- Subtle dark gradient at bottom for badges -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent pointer-events-none"></div>
                
                <!-- Status & Category Badges Overlay -->
                <div class="absolute bottom-6 left-6 md:bottom-8 md:left-8 z-10 flex flex-col md:flex-row flex-wrap gap-2">
                    <span class="bg-gold text-forest font-bold tracking-widest uppercase text-xs px-4 py-2 rounded-full shadow-md">
                        <?= e($event['category']) ?>
                    </span>
                    <span class="bg-white text-forest font-bold tracking-widest uppercase text-xs px-4 py-2 rounded-full shadow-md border border-gold/20">
                        <?= e($event['entry_fee'] === 'Free' ? 'FREE' : $event['entry_fee']) ?>
                    </span>
                    <?php if($event['status'] !== 'Open'): ?>
                        <span class="<?= e($event['status'] === 'Sold Out' ? 'bg-red-500' : 'bg-amber-500') ?> text-white font-bold tracking-widest uppercase text-xs px-4 py-2 rounded-full shadow-md">
                            <?= e($event['status']) ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Title & Summary -->
            <div class="max-w-4xl mb-10">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif text-forest mb-6 leading-tight text-balance"><?= e($event['title']) ?></h1>
                <p class="text-xl md:text-2xl text-charcoal-light font-light leading-relaxed border-l-4 border-gold pl-6 py-1">
                    <?= e($event['description']) ?>
                </p>
            </div>

            <!-- Event Meta Information -->
            <div class="flex flex-col md:flex-row flex-wrap items-center gap-y-6 gap-x-12 pb-8 border-b border-gold/20">
                
                <!-- Organizer -->
                <div class="flex flex-col md:flex-row items-start gap-4 min-w-[200px]">
                    <div class="w-12 h-12 rounded-full bg-white border border-gold/30 flex flex-col md:flex-row items-center justify-center shrink-0 shadow-sm text-gold">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-charcoal-light uppercase tracking-wider mb-1">Organizer</span>
                        <span class="block text-base font-medium text-forest"><?= e($event['ngo_name']) ?></span>
                    </div>
                </div>

                <!-- Date -->
                <div class="flex flex-col md:flex-row items-start gap-4 min-w-[200px]">
                    <div class="w-12 h-12 rounded-full bg-white border border-gold/30 flex flex-col md:flex-row items-center justify-center shrink-0 shadow-sm text-gold">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-charcoal-light uppercase tracking-wider mb-1">Date</span>
                        <span class="block text-base font-medium text-forest"><?= e(date('l, d F Y', strtotime($event['date']))) ?></span>
                    </div>
                </div>

                <!-- Time -->
                <div class="flex flex-col md:flex-row items-start gap-4 min-w-[150px]">
                    <div class="w-12 h-12 rounded-full bg-white border border-gold/30 flex flex-col md:flex-row items-center justify-center shrink-0 shadow-sm text-gold">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-charcoal-light uppercase tracking-wider mb-1">Time</span>
                        <span class="block text-base font-medium text-forest"><?= e(date('h:i A', strtotime($event['time']))) ?></span>
                    </div>
                </div>

                <!-- Location -->
                <div class="flex flex-col md:flex-row items-start gap-4 min-w-[200px]">
                    <div class="w-12 h-12 rounded-full bg-white border border-gold/30 flex flex-col md:flex-row items-center justify-center shrink-0 shadow-sm text-gold">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-charcoal-light uppercase tracking-wider mb-1">Location</span>
                        <span class="block text-base font-medium text-forest"><?= e($event['location']) ?></span>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- 03. MAIN EVENT CONTENT (Two-Column Layout) -->
    <section class="py-12 bg-ivory relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-[1.7fr_1fr] gap-12 items-start">
                
                <!-- LEFT SIDE: Content -->
                <div class="w-full space-y-12">
                    
                    <!-- About This Event -->
                    <div class="bg-white p-8 md:p-10 rounded-3xl border border-gold/20 shadow-sm">
                        <h2 class="text-3xl md:text-4xl font-serif text-forest mb-6">About This Event</h2>
                        <div class="prose prose-lg text-charcoal-light font-light max-w-none space-y-6 leading-relaxed">
                            <p>Join us for an impactful gathering organized by <strong><?= e($event['ngo_name']) ?></strong>. This event focuses on <?= e(strtolower($event['category'])) ?> and aims to bring the community together to make a tangible difference in the lives of those who need it most.</p>
                            <p>Participants will have the opportunity to engage directly with the cause, meet like-minded individuals, and learn more about ongoing initiatives. Whether you are a long-time supporter or new to the community, your presence helps amplify the impact of our collective efforts.</p>
                            <p>Every contribution, big or small, goes directly towards supporting the core mission of this initiative. By participating, you are not just attending an event—you are becoming part of a movement for positive change. We look forward to welcoming you.</p>
                        </div>
                    </div>

                    <!-- Event Details -->
                    <div class="bg-white p-8 md:p-10 rounded-3xl border border-gold/20 shadow-sm">
                        <h3 class="text-2xl md:text-4xl font-serif text-forest mb-8">Event Details</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-8 gap-x-12">
                            <div>
                                <h4 class="text-sm font-bold text-gold uppercase tracking-wider mb-2">Date & Time</h4>
                                <p class="text-base font-medium text-charcoal"><?= e(date('l, d F Y', strtotime($event['date']))) ?></p>
                                <p class="text-sm text-charcoal-light mt-1"><?= e(date('h:i A', strtotime($event['time']))) ?></p>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-bold text-gold uppercase tracking-wider mb-2">Venue</h4>
                                <p class="text-base font-medium text-charcoal">Main Grounds</p>
                                <p class="text-sm text-charcoal-light mt-1"><?= e($event['location']) ?></p>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-bold text-gold uppercase tracking-wider mb-2">Organizer</h4>
                                <p class="text-base font-medium text-charcoal"><?= e($event['ngo_name']) ?></p>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-bold text-gold uppercase tracking-wider mb-2">Audience</h4>
                                <p class="text-base font-medium text-charcoal">Open for All</p>
                            </div>

                            <div>
                                <h4 class="text-sm font-bold text-gold uppercase tracking-wider mb-2">Category</h4>
                                <p class="text-base font-medium text-charcoal"><?= e($event['category']) ?></p>
                            </div>

                            <div>
                                <h4 class="text-sm font-bold text-gold uppercase tracking-wider mb-2">Event Type</h4>
                                <p class="text-base font-medium text-charcoal"><?= e($event['entry_fee'] === 'Free' ? 'Free Entry' : 'Paid Entry') ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- 04. FUNDRAISING PROGRESS -->
                    <div class="bg-white p-8 md:p-10 rounded-3xl border border-gold/20 shadow-sm">
                        <h3 class="text-2xl md:text-4xl font-serif text-forest mb-2">Fundraising Progress</h3>
                        <p class="text-sm text-charcoal-light mb-8">Help us reach our goal to maximize the impact of this event.</p>
                        
                        <div class="flex flex-col md:flex-row justify-between items-end mb-4">
                            <div>
                                <span class="text-4xl font-serif text-forest block">₹3,75,000</span>
                                <span class="text-sm font-bold text-gold uppercase tracking-wider">Raised</span>
                            </div>
                            <div class="text-center">
                                <span class="text-3xl md:text-4xl font-serif text-forest block">75%</span>
                                <span class="text-sm font-bold text-gold uppercase tracking-wider">Funded</span>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl md:text-4xl font-serif text-charcoal-light block">₹5,00,000</span>
                                <span class="text-sm font-bold text-charcoal-light uppercase tracking-wider">Goal</span>
                            </div>
                        </div>
                        
                        <div class="w-full bg-ivory rounded-full h-5 mb-5 border border-gold/20 overflow-hidden shadow-inner">
                            <div class="bg-forest h-full rounded-full relative" style="width: 75%">
                                <div class="absolute inset-0 bg-white/20 w-full h-full" style="background-image: linear-gradient(45deg, rgba(255,255,255,0.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.15) 50%, rgba(255,255,255,0.15) 75%, transparent 75%, transparent); background-size: 1rem 1rem;"></div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col md:flex-row justify-between text-sm font-medium text-charcoal-light">
                            <span>₹1,25,000 Remaining</span>
                            <span>320 Contributions</span>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDE: Sidebar / Booking -->
                <div class="w-full space-y-8">
                    
                    <!-- 05. TICKET / BOOKING CARD -->
                    <?php
                        $priceString = preg_replace('/[^0-9]/', '', $event['entry_fee']);
                        $price = $priceString ? (int) $priceString : 0;
                    ?>
                    
                    <div 
                        id="ticketBookingCard" data-ticket-price="<?= e($price) ?>" class="bg-white p-6 md:p-8 rounded-3xl border border-gold/30 shadow-md">
                        
                        <h3 class="text-2xl md:text-4xl font-serif text-forest mb-6">Book Tickets</h3>
                        
                        <div class="space-y-6">
                            <div class="flex flex-col md:flex-row justify-between items-center pb-6 border-b border-gold/10">
                                <span class="text-sm font-bold text-charcoal uppercase tracking-wider">Ticket Price</span>
                                <div class="text-right">
                                    <span class="text-2xl md:text-4xl font-serif text-forest"><?= e($event['entry_fee'] === 'Free' ? 'Free' : '₹' . number_format($price)) ?></span>
                                    <span class="text-xs text-charcoal-light block">/ person</span>
                                </div>
                            </div>
                            
                            <?php if($price > 0): ?>
                                <div class="pb-6 border-b border-gold/10">
                                    <label class="block text-sm font-bold text-charcoal uppercase tracking-wider mb-4">Quantity</label>
                                    <div class="flex flex-col md:flex-row items-center justify-between border border-gold/20 rounded-xl p-2 bg-ivory">
                                        <button id="donationMinusBtn" class="w-12 h-12 flex flex-col md:flex-row items-center justify-center rounded-lg bg-white border border-gold/20 text-forest hover:bg-forest hover:text-ivory transition-colors shadow-sm">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                        </button>
                                        <span id="donationCountDisplay"class="text-2xl md:text-4xl font-serif text-forest w-16 text-center">1</span>
                                        <button id="donationPlusBtn" class="w-12 h-12 flex flex-col md:flex-row items-center justify-center rounded-lg bg-white border border-gold/20 text-forest hover:bg-forest hover:text-ivory transition-colors shadow-sm">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col md:flex-row justify-between items-center pb-6">
                                    <span class="text-sm font-bold text-charcoal uppercase tracking-wider">Total Amount</span>
                                    <span id="totalAmountDisplay" class="text-3xl md:text-4xl font-serif text-forest">₹<?= e(number_format($price)) ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <button class="w-full bg-forest text-ivory hover:bg-forest/90 px-6 py-4 rounded-xl font-bold transition-all shadow-md text-lg flex flex-col md:flex-row items-center justify-center gap-2 group">
                                <?= e($price > 0 ? 'Book Now' : 'Register for Free') ?>
                                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                            
                            <div class="flex flex-col md:flex-row items-start gap-2 mt-4 text-charcoal-light">
                                <svg class="w-4 h-4 text-green-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                <p class="text-xs leading-relaxed">Secure booking via KarmaSetu. All proceeds go directly to the organizer.</p>
                            </div>
                        </div>
                    </div>

                    <!-- 06. OPTIONAL DONATION SECTION -->
                    <div id="donationWrapper" class="bg-white p-6 md:p-8 rounded-3xl border border-gold/20 shadow-sm">
                        <h3 class="text-xl font-serif text-forest mb-2">Want to Support More?</h3>
                        <p class="text-sm text-charcoal-light mb-6">Add a donation to help <?= e($event['ngo_name']) ?> achieve their mission faster.</p>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4">
                            <button class="donation-amount-btn py-2.5 border rounded-xl text-sm font-bold transition-colors" data-amount="500">₹500</button>
                            <button class="donation-amount-btn py-2.5 border rounded-xl text-sm font-bold transition-colors" data-amount="1000">₹1,000</button>
                            <button class="donation-amount-btn py-2.5 border rounded-xl text-sm font-bold transition-colors" data-amount="2000">₹2,000</button>
                            <button class="donation-amount-btn py-2.5 border rounded-xl text-sm font-bold transition-colors" data-amount="5000">₹5,000</button>
                            <button class="donation-custom-btn py-2.5 border rounded-xl text-sm font-bold transition-colors col-span-2 sm:col-span-2">Other</button>
                        </div>
                        
                        <div id="donationCustomInput" style="display:none;" class="mb-4">
                            <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Custom Amount</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-charcoal-light font-medium">₹</span>
                                <input type="number" min="1" placeholder="Enter amount" class="w-full pl-8 pr-4 py-3 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-forest font-medium transition-all" data-validate="numeric">
                            </div>
                        </div>
                        
                        <a href="<?= baseUrl('?page=contribute&id=1') ?>" class="w-full bg-white border-2 border-forest text-forest hover:bg-forest hover:text-ivory px-4 py-3.5 rounded-xl font-bold transition-colors text-sm flex flex-col md:flex-row items-center justify-center gap-2">
                            Donate Now 
                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>
                        </a>
                    </div>

                    <!-- 07. ABOUT THE ORGANIZER -->
                    <div class="bg-white p-6 md:p-8 rounded-3xl border border-gold/20 shadow-sm">
                        <h3 class="text-xl font-serif text-forest mb-6">About the Organizer</h3>
                        
                        <div class="flex flex-col md:flex-row items-start gap-4 mb-5">
                            <div class="w-16 h-16 bg-ivory border border-gold/30 rounded-2xl flex flex-col md:flex-row items-center justify-center shrink-0 shadow-sm">
                                <span class="text-3xl md:text-4xl font-serif text-forest"><?= e(substr($event['ngo_name'], 0, 1)) ?></span>
                            </div>
                            <div>
                                <h4 class="font-serif text-forest text-lg mb-1"><?= e($event['ngo_name']) ?></h4>
                                <span class="inline-block bg-forest/5 text-forest text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md border border-forest/10"><?= e($event['category']) ?></span>
                            </div>
                        </div>
                        
                        <p class="text-sm text-charcoal-light mb-6 line-clamp-3 leading-relaxed">A dedicated non-profit organization working relentlessly to bring positive change in the community through impactful events, sustainable initiatives, and collaborative community efforts.</p>
                        
                        <!-- Link to NGO #1 for now -->
                        <a href="<?= baseUrl('?page=ngo-profile&id=1') ?>" class="w-full flex flex-col md:flex-row items-center justify-center gap-2 bg-white text-forest border border-forest/20 hover:bg-forest hover:text-ivory hover:border-forest px-4 py-3 rounded-xl font-bold transition-all text-sm group">
                            View NGO Profile
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>

                    <!-- 08. EVENT LOCATION CARD -->
                    <div class="bg-white p-6 md:p-8 rounded-3xl border border-gold/20 shadow-sm">
                        <h3 class="text-xl font-serif text-forest mb-6">Location</h3>
                        
                        <div class="flex flex-col md:flex-row items-start gap-3 mb-5">
                            <svg class="w-5 h-5 text-gold shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <div>
                                <h4 class="font-bold text-charcoal mb-1">Main Grounds, <?= e($event['location']) ?></h4>
                                <p class="text-sm text-charcoal-light leading-relaxed">Opp. Riverfront, Near Sabarmati, <?= e($event['location']) ?> 380001, India</p>
                            </div>
                        </div>

                        <!-- Map Placeholder -->
                        <div class="w-full h-40 bg-ivory rounded-2xl border border-gold/20 flex flex-col md:flex-row items-center justify-center relative overflow-hidden group">
                            <!-- Simulated map grid -->
                            <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(#000 1px, transparent 1px), linear-gradient(90deg, #000 1px, transparent 1px); background-size: 20px 20px;"></div>
                            <div class="absolute inset-0 bg-forest/5 group-hover:bg-forest/10 transition-colors z-10"></div>
                            
                            <!-- Location Pin -->
                            <div class="relative z-20 flex flex-col items-center">
                                <div class="w-10 h-10 bg-white rounded-full flex flex-col md:flex-row items-center justify-center shadow-md text-forest mb-2 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                </div>
                                <span class="bg-white/90 backdrop-blur-sm text-[10px] font-bold text-charcoal px-2 py-1 rounded shadow-sm border border-gold/20">View Map</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- 09. RELATED EVENTS -->
    <section class="py-16 md:py-20 bg-white border-t border-gold/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 border-b border-gold/10 pb-6">
                <div>
                    <h2 class="text-3xl md:text-4xl font-serif text-forest mb-2">You May Also Like</h2>
                    <p class="text-charcoal-light font-medium text-base">Discover more events similar to this one.</p>
                </div>
                <a href="<?= baseUrl('?page=events') ?>" class="text-sm font-bold text-forest hover:text-gold transition-colors inline-flex items-center group bg-ivory px-5 py-2.5 rounded-full border border-gold/20">
                    Browse All Events
                    <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 items-stretch">
                <?php
                    $allEvents = require __DIR__ . '/../data/events.php';
                    $relatedEvents = array_slice(array_filter($allEvents, function($e) use ($event) { return $e['id'] != $event['id']; }), 0, 3);
                ?>
                
                <?php foreach($relatedEvents as $relatedEvent): ?>
                    <div class="flex">
                        <?php $event = $relatedEvent; $id = $event['id']; $title = $event['title']; $category = $event['category']; $date = $event['date']; $ngoName = $event['ngo_name']; $location = $event['location']; $description = $event['description']; $entryFee = $event['entry_fee']; $status = $event['status']; $image = $event['image']; $featured = false; include __DIR__ . "/../includes/components/events/event-card.php"; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>




