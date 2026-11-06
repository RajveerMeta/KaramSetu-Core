<?php
require_once __DIR__ . '/../Database/db_config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . baseUrl('auth/login.php'));
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$contribQuery = "SELECT co.*, n.id AS ngo_id, n.name AS ngo_name, n.primary_cause AS ngo_category, n.description AS ngo_description, n.city AS ngo_city, n.state AS ngo_state,
                        c.title AS cause_title, c.category AS cause_category, c.short_description AS cause_short_desc
                 FROM contributions co
                 INNER JOIN ngos n ON n.id = co.ngo_id
                 LEFT JOIN causes c ON c.id = co.cause_id
                 WHERE co.id = $id AND co.user_id = $user_id
                 LIMIT 1";
$contribResult = mysqli_query($con, $contribQuery);
$contribData = ($contribResult && mysqli_num_rows($contribResult) > 0) ? mysqli_fetch_assoc($contribResult) : null;

$itemData = null;
if ($contribData && $contribData['contribution_type'] !== 'money') {
    $itemQ = mysqli_query($con, "SELECT * FROM contribution_items WHERE contribution_id = " . (int)$contribData['id'] . " LIMIT 1");
    if ($itemQ && mysqli_num_rows($itemQ) > 0) {
        $itemData = mysqli_fetch_assoc($itemQ);
    }
}

$title = $contribData ? (!empty($contribData['cause_title']) ? $contribData['cause_title'] : ('Donation to ' . $contribData['ngo_name'])) : '';
$typeDisplay = $contribData ? ucfirst($contribData['contribution_type'] === 'items' ? 'Useful Items' : $contribData['contribution_type']) : '';
$statusDisplay = 'Pending';
if ($contribData) {
    if ($contribData['contribution_type'] === 'money') {
        $statusDisplay = ($contribData['payment_status'] === 'completed') ? 'Completed' : ucfirst($contribData['payment_status']);
    } else {
        $statusDisplay = ($contribData['fulfillment_status'] === 'completed' || $contribData['fulfillment_status'] === 'received') ? 'Completed' : ucfirst($contribData['fulfillment_status']);
    }
}

$amountDisplay = '—';
if ($contribData) {
    if ($contribData['contribution_type'] === 'money') {
        $amountDisplay = '₹' . number_format((float)$contribData['monetary_amount']);
    } elseif ($itemData) {
        $amountDisplay = $itemData['quantity'] . ' ' . (!empty($itemData['unit']) ? $itemData['unit'] : 'items');
    } else {
        $amountDisplay = 'Resource Donation';
    }
}

$dateDisplay = $contribData ? date('d F Y', strtotime($contribData['created_at'])) : '';
$shortDate = $contribData ? date('d M Y', strtotime($contribData['created_at'])) : '';

$timeline = [];
if ($contribData) {
    $timeline[] = (object)['title' => 'Contribution Initiated', 'date' => $shortDate];
    if ($contribData['contribution_type'] === 'money') {
        $timeline[] = (object)['title' => 'Payment Confirmed (' . ucfirst($contribData['payment_status']) . ')', 'date' => $shortDate];
    } else {
        $timeline[] = (object)['title' => 'Resource Pledge Recorded (' . ucfirst($contribData['fulfillment_status']) . ')', 'date' => $shortDate];
    }
    if ($statusDisplay === 'Completed') {
        $timeline[] = (object)['title' => 'Contribution Completed & Verified', 'date' => $shortDate];
    } else {
        $timeline[] = (object)['title' => 'Pending Fulfillment by NGO', 'date' => 'In Progress'];
    }
}

ob_start();
?>

<div class="bg-ivory/40 min-h-screen py-10 lg:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <?php if (!$contribData): ?>
            <div class="bg-white rounded-3xl p-12 text-center border border-gold/20 shadow-sm">
                <h2 class="text-2xl font-serif text-forest mb-4">Contribution Not Found</h2>
                <p class="text-charcoal-light mb-8">The requested contribution record could not be found or does not belong to your account.</p>
                <a href="<?= baseUrl('user/contributions.php') ?>" class="px-6 py-3 bg-forest text-ivory rounded-xl font-bold shadow-md hover:bg-forest/90 transition-all">Back to My Contributions</a>
            </div>
        <?php else: ?>
            <!-- Header -->
            <div class="text-center mb-10 md:mb-12">
                <h1 class="text-3xl md:text-4xl lg:text-4xl font-serif font-bold text-forest mb-3">Contribution Details</h1>
                <p class="text-charcoal-light">Thank you for making a difference through KarmaSetu.</p>
            </div>

            <!-- Main Contribution Summary Card -->
            <div class="bg-white rounded-3xl p-6 md:p-8 md:p-10 shadow-sm border border-gold/20 mb-8 relative overflow-hidden">
                <div class="absolute -right-16 -top-16 w-48 h-48 bg-gold/5 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8 border-b border-charcoal/5 pb-8">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-serif text-forest mb-2"><?= $title ?></h2>
                        <p class="text-lg text-forest/80 font-medium"><?= $contribData['ngo_name'] ?></p>
                    </div>
                    <div class="text-left md:text-right">
                        <p class="text-sm font-bold uppercase tracking-widest text-charcoal-light mb-1"><?= $contribData['contribution_type'] === 'money' ? 'Amount' : 'Quantity' ?></p>
                        <p class="text-3xl md:text-4xl font-serif text-forest font-bold"><?= $amountDisplay ?></p>
                    </div>
                </div>

                <!-- Structured Information -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-12">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Contribution Reference</p>
                        <p class="font-medium text-charcoal font-mono"><?= $contribData['contribution_reference'] ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Contribution Type</p>
                        <p class="font-medium text-charcoal"><?= $typeDisplay ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Date</p>
                        <p class="font-medium text-charcoal"><?= $dateDisplay ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Status</p>
                        <?php if ($statusDisplay === 'Completed'): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide bg-green-50 text-forest border border-green-200 shadow-sm"><?= $statusDisplay ?></span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide bg-yellow-50 text-yellow-700 border border-yellow-200 shadow-sm"><?= $statusDisplay ?></span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Organization</p>
                        <p class="font-medium text-charcoal"><?= $contribData['ngo_name'] ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Cause</p>
                        <p class="font-medium text-charcoal"><?= !empty($contribData['cause_title']) ? $contribData['cause_title'] : 'General Support' ?></p>
                    </div>
                    <?php if ($itemData): ?>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Item Details</p>
                            <p class="font-medium text-charcoal"><?= $itemData['item_name'] ?> (<?= $itemData['sub_type'] ?? '' ?>)</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Delivery / Condition</p>
                            <p class="font-medium text-charcoal"><?= $itemData['delivery_method'] ?? 'Standard' ?> &bull; <?= $itemData['condition_state'] ?? 'Good' ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($contribData['message'])): ?>
                        <div class="sm:col-span-2">
                            <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Your Message</p>
                            <p class="text-sm text-charcoal italic bg-ivory/50 p-3 rounded-xl border border-gold/20">"<?= $contribData['message'] ?>"</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Impact Section -->
                <div class="bg-forest rounded-3xl p-8 shadow-sm text-ivory flex flex-col justify-center border border-forest-light">
                    <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center mb-5 text-gold">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-serif mb-3">Your Impact</h3>
                    <p class="text-ivory/90 leading-relaxed font-light">
                        Your <?= $amountDisplay ?> contribution helps <?= $contribData['ngo_name'] ?> deliver vital resources to the community.
                    </p>
                </div>

                <!-- Karma Points Section -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gold/20 flex flex-col justify-center text-center">
                    <div class="w-16 h-16 rounded-full bg-ivory border border-gold/30 flex items-center justify-center mx-auto mb-4 text-gold shadow-sm">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-sm font-bold uppercase tracking-widest text-charcoal-light mb-2">Karma Points Earned</h3>
                    <p class="text-3xl md:text-4xl font-serif font-bold text-forest mb-2">+<?= (int)$contribData['karma_points_awarded'] ?></p>
                    <p class="text-sm text-charcoal-light">Thank you for contributing to the KarmaSetu community.</p>
                </div>
            </div>

            <!-- Organization Section & Timeline row -->
            <div class="grid grid-cols-1 md:grid-cols-[1.5fr_1fr] gap-8 mb-10">
                
                <!-- Organization Section -->
                <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gold/20 flex flex-col justify-between">
                    <div>
                        <h3 class="text-xs font-bold text-gold uppercase tracking-wider mb-5">NGO / Organization</h3>
                        <div class="flex items-start gap-4 mb-5">
                            <div class="w-14 h-14 bg-ivory border border-gold/30 rounded-xl flex items-center justify-center shrink-0 shadow-sm text-2xl font-serif text-forest">
                                <?= substr($contribData['ngo_name'], 0, 1) ?>
                            </div>
                            <div>
                                <h4 class="font-serif text-forest text-xl mb-1"><?= $contribData['ngo_name'] ?></h4>
                                <p class="text-sm text-charcoal-light leading-relaxed"><?= !empty($contribData['ngo_description']) ? $contribData['ngo_description'] : 'Dedicated non-profit organization serving communities.' ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-4 mt-auto border-t border-charcoal/5">
                        <a href="<?= baseUrl('?page=ngo-profile&id=' . (int)$contribData['ngo_id']) ?>" class="inline-flex items-center justify-center gap-2 text-sm font-bold text-forest hover:text-gold transition-colors group">
                            View NGO Profile 
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>

                <!-- Contribution Timeline -->
                <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gold/20">
                    <h3 class="text-xs font-bold text-gold uppercase tracking-wider mb-6">Timeline</h3>
                    <div class="relative border-l-2 border-forest/20 ml-2 space-y-6">
                        <?php foreach ($timeline as $index => $event): ?>
                            <div class="relative pl-6">
                                <div class="absolute w-3 h-3 bg-forest rounded-full -left-[7px] top-1.5 shadow-sm border-2 border-white"></div>
                                <h4 class="text-sm font-bold text-charcoal mb-0.5"><?= $event->title ?></h4>
                                <p class="text-xs font-medium text-charcoal-light"><?= $event->date ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="<?= baseUrl('user/contributions.php') ?>" class="w-full sm:w-auto px-8 py-3.5 bg-white border border-charcoal/20 text-charcoal hover:bg-ivory hover:border-gold/30 rounded-xl font-bold transition-all text-center shadow-sm">
                    Back to My Contributions
                </a>
                <a href="<?= baseUrl('?page=causes') ?>" class="w-full sm:w-auto px-8 py-3.5 bg-forest text-ivory hover:bg-forest/90 rounded-xl font-bold transition-all text-center shadow-md">
                    Explore More Causes
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>
