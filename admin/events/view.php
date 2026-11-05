
<?php
require_once __DIR__ . '/../../DataBase/admin/events/view.php';

ob_start();
?>


<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Page Header -->
    <div class="mb-6">
        <a href="<?= baseUrl('admin/events/index.php') ?>"
           class="inline-flex items-center text-sm font-medium text-forest hover:text-gold transition-colors mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Events
        </a>

        <div class="flex flex-col md:flex-row md:items-start justify-between">
            <div class="max-w-3xl">
                <h1 class="text-3xl font-serif font-bold text-forest mb-2">
                    <?= e($event['title']) ?>
                </h1>
                <p class="text-charcoal-light">
                    Review event information, organizer details, participation, and current status.
                </p>
            </div>

            <div class="mt-4 md:mt-0 flex-shrink-0">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium border <?= $statusClass ?>">
                    <?= e($status) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Success and Error Messages -->
    <?php if (!empty($message)): ?>
        <div class="mb-6 p-4 rounded-lg border <?= $messageType == 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800' ?>">
            <p class="text-sm font-medium"><?= e($message) ?></p>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 items-start">

        <!-- Left Column -->
        <div class="lg:col-span-2 flex flex-col gap-6">

            <!-- Event Overview -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">

                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Category</p>
                            <p class="text-base font-medium text-forest"><?= e($event['category']) ?></p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Date</p>
                            <p class="text-base font-medium text-charcoal">
                                <?= e(date('d M Y', strtotime($event['event_date']))) ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Time</p>
                            <p class="text-base font-medium text-charcoal"><?= e($eventTime) ?></p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Location</p>
                            <p class="text-base font-medium text-charcoal"><?= e($event['location']) ?></p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Participants</p>
                            <p class="text-base font-medium text-charcoal">
                                <?= e($registered) ?>

                                <?php if ($capacity > 0): ?>
                                    / <?= e($capacity) ?>
                                <?php else: ?>
                                    / No limit
                                <?php endif; ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Entry Fee</p>
                            <p class="text-base font-medium text-charcoal">
                                <?php if ((float) $event['entry_fee'] > 0): ?>
                                    ₹<?= e(number_format((float) $event['entry_fee'], 2)) ?>
                                <?php else: ?>
                                    Free
                                <?php endif; ?>
                            </p>
                        </div>

                    </div>
                </div>
            </div>

            <!-- About Event -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h3 class="text-lg font-serif font-bold text-forest">About This Event</h3>
                </div>

                <div class="p-6">
                    <p class="text-charcoal leading-relaxed mb-6">
                        <?php if (!empty($event['detailed_description'])): ?>
                            <?= nl2br(e($event['detailed_description'])) ?>
                        <?php elseif (!empty($event['short_description'])): ?>
                            <?= nl2br(e($event['short_description'])) ?>
                        <?php else: ?>
                            No event description available.
                        <?php endif; ?>
                    </p>

                    <h4 class="text-sm font-bold text-forest uppercase tracking-wider mb-2">Event Summary</h4>
                    <p class="text-charcoal leading-relaxed">
                        <?php if (!empty($event['short_description'])): ?>
                            <?= nl2br(e($event['short_description'])) ?>
                        <?php else: ?>
                            No summary available.
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <!-- Recent Participants -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30 flex justify-between items-center">
                    <h3 class="text-lg font-serif font-bold text-forest">Recent Participants</h3>
                </div>

                <!-- Desktop Table -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-ivory/10 text-charcoal-light text-sm border-b border-charcoal/10">
                                <th class="px-6 py-3 font-medium">Participant</th>
                                <th class="px-6 py-3 font-medium">Email</th>
                                <th class="px-6 py-3 font-medium">Registered On</th>
                                <th class="px-6 py-3 font-medium">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-charcoal/5">
                            <?php if ($participantsResult && mysqli_num_rows($participantsResult) > 0): ?>
                                <?php while ($p = mysqli_fetch_assoc($participantsResult)): ?>
                                    <?php
                                    $participantStatusClass = 'bg-green-50 text-green-700';

                                    if ($p['booking_status'] == 'cancelled') {
                                        $participantStatusClass = 'bg-red-50 text-red-700';
                                    } elseif ($p['booking_status'] == 'attended') {
                                        $participantStatusClass = 'bg-blue-50 text-blue-700';
                                    }
                                    ?>
                                    <tr class="hover:bg-ivory/30 transition-colors">
                                        <td class="px-6 py-4 text-sm font-medium text-charcoal">
                                            <?= e($p['participant_name']) ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-charcoal-light">
                                            <?= e($p['participant_email']) ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-charcoal-light">
                                            <?= e(date('d M Y', strtotime($p['created_at']))) ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= $participantStatusClass ?>">
                                                <?= e(ucfirst($p['booking_status'])) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-charcoal-light">
                                        No participants have registered for this event.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Layout -->
                <?php
                mysqli_data_seek($participantsResult, 0);
                ?>

                <div class="block sm:hidden divide-y divide-charcoal/10">
                    <?php if ($participantsResult && mysqli_num_rows($participantsResult) > 0): ?>
                        <?php while ($p = mysqli_fetch_assoc($participantsResult)): ?>
                            <div class="p-4 space-y-2">
                                <div class="flex justify-between items-start gap-3">
                                    <span class="text-sm font-medium text-charcoal">
                                        <?= e($p['participant_name']) ?>
                                    </span>

                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium <?= $p['booking_status'] == 'cancelled' ? 'bg-red-50 text-red-700' : ($p['booking_status'] == 'attended' ? 'bg-blue-50 text-blue-700' : 'bg-green-50 text-green-700') ?>">
                                        <?= e(ucfirst($p['booking_status'])) ?>
                                    </span>
                                </div>

                                <div class="text-xs text-charcoal-light"><?= e($p['participant_email']) ?></div>

                                <div class="text-xs text-charcoal-light">
                                    Registered: <?= e(date('d M Y', strtotime($p['created_at']))) ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="p-6 text-center text-sm text-charcoal-light">
                            No participants have registered for this event.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="px-6 py-4 bg-ivory/10 border-t border-charcoal/10">
                    <p class="text-sm text-charcoal-light text-center sm:text-left">
                        Showing recent participants. Total registered participants: <?= e($registered) ?>
                    </p>
                </div>
            </div>

        </div>

        <!-- Right Sidebar -->
        <div class="flex flex-col gap-6">

            <!-- Participation Overview -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h3 class="text-lg font-serif font-bold text-forest">Participation Overview</h3>
                </div>

                <div class="p-6">
                    <div class="flex justify-between items-end mb-6 gap-2">
                        <div class="text-center">
                            <p class="text-3xl font-bold text-forest"><?= e($registered) ?></p>
                            <p class="text-xs font-medium text-charcoal-light uppercase mt-1">Registered</p>
                        </div>

                        <div class="text-center">
                            <p class="text-2xl font-bold text-charcoal">
                                <?= $capacity > 0 ? e($capacity) : '—' ?>
                            </p>
                            <p class="text-xs font-medium text-charcoal-light uppercase mt-1">Capacity</p>
                        </div>

                        <div class="text-center">
                            <p class="text-2xl font-bold text-gold">
                                <?= $capacity > 0 ? e($progress) . '%' : '—' ?>
                            </p>
                            <p class="text-xs font-medium text-charcoal-light uppercase mt-1">Filled</p>
                        </div>
                    </div>

                    <?php if ($capacity > 0): ?>
                        <div class="mb-3">
                            <div class="w-full bg-ivory rounded-full h-2.5 border border-charcoal/10">
                                <div class="bg-forest h-2.5 rounded-full" style="width: <?= $progress ?>%"></div>
                            </div>
                        </div>

                        <p class="text-sm text-charcoal-light text-center">
                            <span class="font-medium text-charcoal"><?= e($registered) ?></span>
                            of
                            <span class="font-medium text-charcoal"><?= e($capacity) ?></span>
                            participant spots are registered.
                        </p>
                    <?php else: ?>
                        <p class="text-sm text-charcoal-light text-center">
                            This event has no participant capacity limit.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Organized By -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h3 class="text-lg font-serif font-bold text-forest">Organized By</h3>
                </div>

                <div class="p-6">
                    <h4 class="text-lg font-bold text-charcoal mb-4">
                        <?= e($event['ngo_name']) ?>
                    </h4>

                    <dl class="space-y-3 mb-6">
                        <div>
                            <dt class="text-xs font-medium text-charcoal-light">Category</dt>
                            <dd class="text-sm text-charcoal">
                                <?= !empty($event['ngo_type']) ? e($event['ngo_type']) : 'Not specified' ?>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-charcoal-light">Location</dt>
                            <dd class="text-sm text-charcoal"><?= e($ngoLocation) ?></dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-charcoal-light">Status</dt>
                            <dd class="text-sm font-medium <?= $event['verification_status'] == 'approved' ? 'text-green-700' : 'text-amber-700' ?>">
                                <?= e(ucfirst($event['verification_status'])) ?>
                            </dd>
                        </div>
                    </dl>

                    <a href="<?= baseUrl('admin/ngos/view.php?id=' . (int) $event['ngo_id']) ?>"
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-forest text-forest hover:bg-forest hover:text-white text-sm font-medium rounded-md transition-colors">
                        View NGO
                    </a>
                </div>
            </div>

            <!-- Event Status -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h3 class="text-lg font-serif font-bold text-forest">Event Status</h3>
                </div>

                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-xs font-medium text-charcoal-light uppercase mb-1">Current Status</p>
                        <p class="text-lg font-bold text-charcoal"><?= e($status) ?></p>
                    </div>

                    <p class="text-sm text-charcoal leading-relaxed mb-6">
                        <?= e($statusDescription) ?>
                    </p>

                    <?php if ($event['status'] == 'upcoming'): ?>
                        <form method="POST">
                            <input type="hidden" name="event_id" value="<?= e($eventId) ?>">
                            <input type="hidden" name="action" value="cancel">

                            <button type="submit" name="update_status"
                                    class="w-full px-4 py-2 bg-white border border-red-200 text-red-600 hover:bg-red-50 font-medium rounded-md transition-colors text-sm">
                                Cancel Event
                            </button>
                        </form>
                    <?php elseif ($event['status'] == 'cancelled'): ?>
                        <form method="POST">
                            <input type="hidden" name="event_id" value="<?= e($eventId) ?>">
                            <input type="hidden" name="action" value="activate">

                            <button type="submit" name="update_status"
                                    class="w-full px-4 py-2 bg-white border border-green-200 text-green-700 hover:bg-green-50 font-medium rounded-md transition-colors text-sm">
                                Activate Event
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="text-sm text-charcoal-light">
                            Completed events cannot be activated or cancelled from this page.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/admin.php';
?>
