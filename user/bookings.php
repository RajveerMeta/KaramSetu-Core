<?php
require_once __DIR__ . '/../includes/functions.php';
ob_start();

// Demo data for the logged-in user's event bookings
$bookings = [
    [
        'booking_id' => 'BK-1001',
        'event_name' => 'Hope For Every Child — Charity Evening',
        'date' => '24 Sep 2026',
        'time' => '06:00 PM',
        'venue' => 'Grand Crystal Hall, Ahmedabad',
        'tickets' => 2,
        'status' => 'Confirmed'
    ],
    [
        'booking_id' => 'BK-1002',
        'event_name' => 'Skills For Tomorrow Workshop',
        'date' => '05 Oct 2026',
        'time' => '10:00 AM',
        'venue' => 'Youth Centre, Rajkot',
        'tickets' => 1,
        'status' => 'Confirmed'
    ],
    [
        'booking_id' => 'BK-1003',
        'event_name' => 'Run For A Cause Marathon 2026',
        'date' => '15 Aug 2026',
        'time' => '06:00 AM',
        'venue' => 'Riverfront Park, Surat',
        'tickets' => 1,
        'status' => 'Completed'
    ],
    [
        'booking_id' => 'BK-0994',
        'event_name' => 'Community Tree Plantation Drive',
        'date' => '30 Jul 2026',
        'time' => '08:00 AM',
        'venue' => 'City Botanical Garden, Surat',
        'tickets' => 2,
        'status' => 'Completed'
    ],
    [
        'booking_id' => 'BK-0985',
        'event_name' => 'Women Empowerment Seminar',
        'date' => '01 Sep 2026',
        'time' => '02:00 PM',
        'venue' => 'Convention Centre, Mumbai',
        'tickets' => 1,
        'status' => 'Cancelled'
    ],
];
?>

<div class="bg-ivory/40 min-h-screen py-10 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-serif font-bold text-forest mb-2">My Bookings</h1>
                <p class="text-charcoal-light text-sm md:text-base">View your registered event bookings, venues, and ticket details.</p>
            </div>
            <div>
                <a href="<?= baseUrl('?page=events') ?>" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold rounded-full text-white bg-forest hover:bg-forest-dark transition-colors shadow-sm">
                    Browse Events
                </a>
            </div>
        </div>

        <!-- Bookings Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-gold/20 overflow-hidden">
            
            <!-- Table Header Bar -->
            <div class="p-5 md:p-6 border-b border-charcoal/10 bg-ivory/20 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-serif font-bold text-forest">All Event Bookings</h2>
                    <p class="text-xs text-charcoal-light mt-0.5">List of your recent event registrations</p>
                </div>
                <span class="text-xs font-semibold px-3 py-1 bg-white border border-gold/30 rounded-full text-forest">
                    <?= count($bookings) ?> Total Bookings
                </span>
            </div>

            <?php if (!empty($bookings)): ?>
                <!-- Desktop Table View (Hidden on mobile) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs text-charcoal-light uppercase tracking-wider border-b border-charcoal/10 bg-ivory/30">
                                <th class="px-6 py-4 font-bold text-forest">Booking ID</th>
                                <th class="px-6 py-4 font-bold text-forest">Event Name</th>
                                <th class="px-6 py-4 font-bold text-forest">Date & Time</th>
                                <th class="px-6 py-4 font-bold text-forest">Venue</th>
                                <th class="px-6 py-4 font-bold text-forest text-center">Tickets</th>
                                <th class="px-6 py-4 font-bold text-forest">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-charcoal/5">
                            <?php foreach ($bookings as $booking): ?>
                            <tr class="hover:bg-ivory/20 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-charcoal text-xs">
                                    <?= e($booking['booking_id']) ?>
                                </td>
                                <td class="px-6 py-4 font-medium text-charcoal">
                                    <?= e($booking['event_name']) ?>
                                </td>
                                <td class="px-6 py-4 text-charcoal-light text-xs">
                                    <div class="font-medium text-charcoal"><?= e($booking['date']) ?></div>
                                    <div class="text-[11px] text-charcoal-light"><?= e($booking['time']) ?></div>
                                </td>
                                <td class="px-6 py-4 text-charcoal-light text-xs">
                                    <?= e($booking['venue']) ?>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-forest">
                                    <?= e($booking['tickets']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($booking['status'] === 'Confirmed'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-green-50 text-forest border border-green-200">
                                            <?= e($booking['status']) ?>
                                        </span>
                                    <?php elseif ($booking['status'] === 'Completed'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-blue-50 text-blue-700 border border-blue-200">
                                            <?= e($booking['status']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-red-50 text-red-700 border border-red-200">
                                            <?= e($booking['status']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View (Hidden on desktop) -->
                <div class="md:hidden divide-y divide-charcoal/5">
                    <?php foreach ($bookings as $booking): ?>
                    <div class="p-5 hover:bg-ivory/20 transition-colors">
                        <div class="flex justify-between items-start gap-2 mb-2">
                            <div>
                                <span class="font-mono text-xs font-bold text-charcoal-light block mb-1">
                                    <?= e($booking['booking_id']) ?>
                                </span>
                                <h3 class="font-bold text-charcoal text-base">
                                    <?= e($booking['event_name']) ?>
                                </h3>
                            </div>
                            <div>
                                <?php if ($booking['status'] === 'Confirmed'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-green-50 text-forest border border-green-200">
                                        <?= e($booking['status']) ?>
                                    </span>
                                <?php elseif ($booking['status'] === 'Completed'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-blue-50 text-blue-700 border border-blue-200">
                                        <?= e($booking['status']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-red-50 text-red-700 border border-red-200">
                                        <?= e($booking['status']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="space-y-1.5 text-xs text-charcoal-light mt-3 pt-3 border-t border-charcoal/5">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-charcoal w-16">Date:</span>
                                <span><?= e($booking['date']) ?> (<?= e($booking['time']) ?>)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-charcoal w-16">Venue:</span>
                                <span><?= e($booking['venue']) ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-charcoal w-16">Tickets:</span>
                                <span class="font-bold text-forest"><?= e($booking['tickets']) ?> <?= $booking['tickets'] > 1 ? 'Tickets' : 'Ticket' ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <!-- Empty State -->
                <div class="p-12 text-center">
                    <p class="text-charcoal-light mb-4">You have no event bookings yet.</p>
                    <a href="<?= baseUrl('?page=events') ?>" class="inline-flex items-center px-5 py-2 text-sm font-bold rounded-full text-white bg-forest hover:bg-forest-dark transition-colors">
                        Browse Events
                    </a>
                </div>
            <?php endif; ?>

        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>
