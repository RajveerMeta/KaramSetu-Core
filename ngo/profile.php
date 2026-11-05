<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../Database/db_config.php';

ob_start();

$user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

$ngo = null;
$documents = [];
$total_causes = 0;
$active_causes = 0;
$profile_completion = 0;
$errorMsg = '';

if ($user_id > 0) {

    // Get NGO profile and user contact details
    $ngoQuery = mysqli_query(
        $con,
        "SELECT 
            n.*,
            u.name AS user_name,
            u.email AS user_email,
            u.phone AS user_phone
        FROM ngos n
        LEFT JOIN users u ON n.user_id = u.id
        WHERE n.user_id = $user_id
        LIMIT 1"
    );

    if ($ngoQuery && mysqli_num_rows($ngoQuery) > 0) {

        $ngo = mysqli_fetch_assoc($ngoQuery);

        $ngo_id = (int) $ngo['id'];

        // Get NGO documents
        $documentQuery = mysqli_query(
            $con,
            "SELECT *
             FROM ngo_documents
             WHERE ngo_id = $ngo_id
             ORDER BY created_at DESC"
        );

        if ($documentQuery) {
            while ($document = mysqli_fetch_assoc($documentQuery)) {
                $documents[] = $document;
            }
        }

        // Get cause statistics
        $causeQuery = mysqli_query(
            $con,
            "SELECT
                COUNT(*) AS total_causes,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_causes
             FROM causes
             WHERE ngo_id = $ngo_id"
        );

        if ($causeQuery) {
            $causeData = mysqli_fetch_assoc($causeQuery);

            $total_causes = (int) ($causeData['total_causes'] ?? 0);
            $active_causes = (int) ($causeData['active_causes'] ?? 0);
        }

        // Calculate profile completion
        $profileFields = [
            'name',
            'registration_number',
            'established_date',
            'ngo_type',
            'primary_cause',
            'description',
            'mission',
            'official_email',
            'phone',
            'contact_person',
            'contact_designation',
            'address',
            'city',
            'state',
            'pincode'
        ];

        $completedFields = 0;

        foreach ($profileFields as $field) {
            if (!empty($ngo[$field])) {
                $completedFields++;
            }
        }

        $profile_completion = (int) round(
            ($completedFields / count($profileFields)) * 100
        );

    } else {
        $errorMsg = 'NGO profile not found.';
    }

} else {
    $errorMsg = 'Please login to view your NGO profile.';
}

// Verification status
$verificationStatus = $ngo['verification_status'] ?? 'pending';

$statusLabels = [
    'approved' => 'Verified NGO',
    'pending' => 'Pending Verification',
    'rejected' => 'Verification Rejected',
    'suspended' => 'Account Suspended'
];

$statusClasses = [
    'approved' => 'bg-green-100 text-green-800',
    'pending' => 'bg-amber-100 text-amber-800',
    'rejected' => 'bg-red-100 text-red-800',
    'suspended' => 'bg-gray-100 text-gray-800'
];

$statusLabel = $statusLabels[$verificationStatus] ?? 'Unknown Status';
$statusClass = $statusClasses[$verificationStatus] ?? 'bg-gray-100 text-gray-800';

// Document labels
$documentLabels = [
    'registration_certificate' => 'Registration Certificate',
    'pan_card' => 'PAN Card',
    'address_proof' => 'Address Proof',
    'supporting_document' => 'Supporting Document',
    'other' => 'Other Document'
];

// NGO type labels
$ngoTypeLabels = [
    'trust' => 'Trust',
    'society' => 'Society',
    'section_8' => 'Section 8 Company',
    'other' => 'Other'
];

$establishedYear = '';

if (!empty($ngo['established_date'])) {
    $establishedYear = date('Y', strtotime($ngo['established_date']));
}
?>

<div class="min-h-screen bg-ivory py-10 px-4 sm:px-6 lg:px-8">

    <div class="max-w-7xl mx-auto">

        <!-- PAGE HEADER -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">

            <div>
                <h1 class="text-3xl font-serif font-bold text-forest">
                    NGO Profile
                </h1>

                <p class="text-charcoal/60 mt-2">
                    View your organization's profile information.
                </p>
            </div>

            <?php if ($ngo) : ?>
                <a href="<?= baseUrl('ngo/edit-profile.php') ?>"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-forest text-white rounded-lg font-semibold hover:bg-forest/90 transition">

                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>

                    Edit Profile
                </a>
            <?php endif; ?>

        </div>

        <?php if (!empty($errorMsg)) : ?>

            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-5">
                <?= $errorMsg ?>
            </div>

        <?php elseif ($ngo) : ?>

            <!-- PROFILE HEADER CARD -->
            <div class="bg-white rounded-2xl shadow-sm border border-charcoal/10 p-6 mb-8">

                <div class="flex flex-col sm:flex-row sm:items-center gap-5">

                    <div class="w-20 h-20 rounded-2xl bg-forest flex items-center justify-center shrink-0">

                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M9 12l2 2 4-4"/>
                        </svg>

                    </div>

                    <div class="flex-1 min-w-0">

                        <h2 class="text-2xl font-bold text-forest break-words">
                            <?= $ngo['name'] ?>
                        </h2>

                        <p class="text-sm text-charcoal/60 mt-1">
                            <?= $ngoTypeLabels[$ngo['ngo_type']] ?? $ngo['ngo_type'] ?>
                        </p>

                        <div class="flex flex-wrap items-center gap-3 mt-3">

                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold <?= $statusClass ?>">
                                <?= $statusLabel ?>
                            </span>

                            <span class="text-sm text-charcoal/60">
                                Registration No:
                                <span class="font-semibold text-charcoal">
                                    <?= $ngo['registration_number'] ?>
                                </span>
                            </span>

                        </div>

                    </div>

                </div>

            </div>

            <!-- MAIN GRID -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- LEFT CONTENT -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- ORGANIZATION INFORMATION -->
                    <div class="bg-white rounded-2xl shadow-sm border border-charcoal/10 p-6">

                        <h3 class="text-xl font-serif font-bold text-forest mb-6">
                            Organization Information
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                            <div>
                                <p class="text-sm text-charcoal/50 mb-1">
                                    Organization Name
                                </p>
                                <p class="font-semibold text-charcoal break-words">
                                    <?= $ngo['name'] ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-charcoal/50 mb-1">
                                    Registration Number
                                </p>
                                <p class="font-semibold text-charcoal break-words">
                                    <?= $ngo['registration_number'] ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-charcoal/50 mb-1">
                                    Established Year
                                </p>
                                <p class="font-semibold text-charcoal">
                                    <?= $establishedYear ?: 'Not available' ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-charcoal/50 mb-1">
                                    Organization Type
                                </p>
                                <p class="font-semibold text-charcoal">
                                    <?= $ngoTypeLabels[$ngo['ngo_type']] ?? $ngo['ngo_type'] ?>
                                </p>
                            </div>

                            <div class="sm:col-span-2">
                                <p class="text-sm text-charcoal/50 mb-1">
                                    Primary Cause
                                </p>
                                <p class="font-semibold text-charcoal break-words">
                                    <?= $ngo['primary_cause'] ?>
                                </p>
                            </div>

                            <div class="sm:col-span-2">
                                <p class="text-sm text-charcoal/50 mb-2">
                                    About Organization
                                </p>
                                <p class="text-charcoal/80 leading-relaxed whitespace-pre-line break-words">
                                    <?= $ngo['description'] ?>
                                </p>
                            </div>

                            <div class="sm:col-span-2">
                                <p class="text-sm text-charcoal/50 mb-2">
                                    Our Mission
                                </p>
                                <p class="text-charcoal/80 leading-relaxed whitespace-pre-line break-words">
                                    <?= !empty($ngo['mission']) ? $ngo['mission'] : 'Mission information has not been added yet.' ?>
                                </p>
                            </div>

                        </div>

                    </div>

                    <!-- CONTACT INFORMATION -->
                    <div class="bg-white rounded-2xl shadow-sm border border-charcoal/10 p-6">

                        <h3 class="text-xl font-serif font-bold text-forest mb-6">
                            Contact Information
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                            <div>
                                <p class="text-sm text-charcoal/50 mb-1">
                                    Official Email
                                </p>
                                <p class="font-semibold text-charcoal break-all">
                                    <?= $ngo['official_email'] ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-charcoal/50 mb-1">
                                    Phone Number
                                </p>
                                <p class="font-semibold text-charcoal">
                                    <?= $ngo['phone'] ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-charcoal/50 mb-1">
                                    Contact Person
                                </p>
                                <p class="font-semibold text-charcoal">
                                    <?= $ngo['contact_person'] ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-charcoal/50 mb-1">
                                    Designation
                                </p>
                                <p class="font-semibold text-charcoal">
                                    <?= $ngo['contact_designation'] ?>
                                </p>
                            </div>

                            <div class="sm:col-span-2">
                                <p class="text-sm text-charcoal/50 mb-1">
                                    Website
                                </p>

                                <?php if (!empty($ngo['website'])) : ?>
                                    <a href="<?= $ngo['website'] ?>"
                                       target="_blank"
                                       class="font-semibold text-gold hover:text-forest break-all">
                                        <?= $ngo['website'] ?>
                                    </a>
                                <?php else : ?>
                                    <p class="text-charcoal/50">
                                        Website not added
                                    </p>
                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                    <!-- ADDRESS INFORMATION -->
                    <div class="bg-white rounded-2xl shadow-sm border border-charcoal/10 p-6">

                        <h3 class="text-xl font-serif font-bold text-forest mb-6">
                            Address Information
                        </h3>

                        <div class="flex items-start gap-4">

                            <div class="w-11 h-11 rounded-xl bg-ivory flex items-center justify-center shrink-0">

                                <svg class="w-6 h-6 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 21s7-5.5 7-12a7 7 0 10-14 0c0 6.5 7 12 7 12z"/>
                                    <circle cx="12" cy="9" r="2.5" stroke-width="2"/>
                                </svg>

                            </div>

                            <div class="min-w-0">

                                <p class="font-semibold text-charcoal break-words">
                                    <?= $ngo['address'] ?>
                                </p>

                                <p class="text-sm text-charcoal/60 mt-2 break-words">
                                    <?= $ngo['city'] ?>,
                                    <?= $ngo['state'] ?> -
                                    <?= $ngo['pincode'] ?>
                                </p>

                            </div>

                        </div>

                    </div>

                    <!-- IMPACT INFORMATION -->
                    <div class="bg-white rounded-2xl shadow-sm border border-charcoal/10 p-6">

                        <h3 class="text-xl font-serif font-bold text-forest mb-6">
                            Impact Information
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">

                            <div class="bg-ivory rounded-xl p-5">
                                <p class="text-sm text-charcoal/60">
                                    Total Causes
                                </p>

                                <p class="text-3xl font-bold text-forest mt-2">
                                    <?= $total_causes ?>
                                </p>
                            </div>

                            <div class="bg-ivory rounded-xl p-5">
                                <p class="text-sm text-charcoal/60">
                                    Active Causes
                                </p>

                                <p class="text-3xl font-bold text-forest mt-2">
                                    <?= $active_causes ?>
                                </p>
                            </div>

                        </div>

                        <p class="text-sm text-charcoal/50 mb-2">
                            Impact Summary
                        </p>

                        <p class="text-charcoal/80 leading-relaxed whitespace-pre-line break-words">
                            <?= !empty($ngo['impact_summary']) ? $ngo['impact_summary'] : 'Impact summary has not been added yet.' ?>
                        </p>

                    </div>

                    <!-- VERIFICATION DOCUMENTS -->
                    <div class="bg-white rounded-2xl shadow-sm border border-charcoal/10 p-6">

                        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">

                            <h3 class="text-xl font-serif font-bold text-forest">
                                Verification Documents
                            </h3>

                            <span class="text-sm text-charcoal/50">
                                <?= count($documents) ?> document(s)
                            </span>

                        </div>

                        <?php if (!empty($documents)) : ?>

                            <div class="space-y-4">

                                <?php foreach ($documents as $document) : ?>

                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border border-charcoal/10 rounded-xl p-4">

                                        <div class="flex items-center gap-3 min-w-0">

                                            <div class="w-11 h-11 rounded-lg bg-ivory flex items-center justify-center shrink-0">

                                                <svg class="w-6 h-6 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M7 3h7l5 5v13H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M14 3v5h5M9 13h6M9 17h6"/>
                                                </svg>

                                            </div>

                                            <div class="min-w-0">

                                                <p class="font-semibold text-charcoal break-words">
                                                    <?= $documentLabels[$document['document_type']] ?? 'Document' ?>
                                                </p>

                                                <p class="text-xs text-charcoal/50 mt-1 break-words">
                                                    <?= $document['file_name'] ?>
                                                </p>

                                                <p class="text-xs text-charcoal/50 mt-1">
                                                    Uploaded:
                                                    <?= date('d M Y', strtotime($document['created_at'])) ?>
                                                </p>

                                            </div>

                                        </div>

                                        <a href="<?= baseUrl($document['file_path']) ?>"
                                           target="_blank"
                                           class="inline-flex items-center justify-center px-4 py-2 border border-forest text-forest rounded-lg text-sm font-semibold hover:bg-forest hover:text-white transition shrink-0">

                                            View Document

                                        </a>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        <?php else : ?>

                            <div class="text-center py-8">

                                <p class="text-charcoal/60">
                                    No verification documents uploaded yet.
                                </p>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

                <!-- RIGHT SIDEBAR -->
                <div class="space-y-8">

                    <!-- PROFILE COMPLETION -->
                    <div class="bg-white rounded-2xl shadow-sm border border-charcoal/10 p-6">

                        <h3 class="text-lg font-bold text-forest mb-5">
                            Profile Completion
                        </h3>

                        <div class="flex items-center justify-between mb-2">

                            <span class="text-sm text-charcoal/60">
                                Profile Progress
                            </span>

                            <span class="font-bold text-forest">
                                <?= $profile_completion ?>%
                            </span>

                        </div>

                        <div class="w-full h-3 bg-charcoal/10 rounded-full overflow-hidden">

                            <div class="h-full bg-forest rounded-full transition-all"
                                 style="width: <?= $profile_completion ?>%">
                            </div>

                        </div>

                        <p class="text-xs text-charcoal/50 mt-3">
                            Complete your organization information to improve your profile.
                        </p>

                        <a href="<?= baseUrl('ngo/edit-profile.php') ?>"
                           class="block text-center mt-5 px-4 py-3 bg-forest text-white rounded-lg font-semibold hover:bg-forest/90 transition">
                            Complete Profile
                        </a>

                    </div>

                    <!-- VERIFICATION STATUS -->
                    <div class="bg-white rounded-2xl shadow-sm border border-charcoal/10 p-6">

                        <h3 class="text-lg font-bold text-forest mb-5">
                            Verification Status
                        </h3>

                        <div class="flex items-center gap-3">

                            <div class="w-11 h-11 rounded-full bg-ivory flex items-center justify-center shrink-0">

                                <svg class="w-6 h-6 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m5-1a8 8 0 11-16 0 8 8 0 0116 0z"/>
                                </svg>

                            </div>

                            <div class="min-w-0">

                                <p class="font-semibold text-charcoal">
                                    <?= $statusLabel ?>
                                </p>

                                <span class="inline-flex mt-1 px-2 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>">
                                    <?= ucfirst($verificationStatus) ?>
                                </span>

                            </div>

                        </div>

                        <?php if ($verificationStatus === 'approved' && !empty($ngo['verified_at'])) : ?>

                            <p class="text-xs text-charcoal/50 mt-4">
                                Verified on:
                                <?= date('d M Y', strtotime($ngo['verified_at'])) ?>
                            </p>

                        <?php elseif ($verificationStatus === 'rejected' && !empty($ngo['rejection_reason'])) : ?>

                            <div class="mt-4 p-3 rounded-lg bg-red-50 border border-red-100">

                                <p class="text-xs font-semibold text-red-700 mb-1">
                                    Rejection Reason
                                </p>

                                <p class="text-sm text-red-700 break-words">
                                    <?= $ngo['rejection_reason'] ?>
                                </p>

                            </div>

                        <?php elseif ($verificationStatus === 'pending') : ?>

                            <p class="text-sm text-charcoal/60 mt-4">
                                Your documents and organization details are waiting for admin verification.
                            </p>

                        <?php endif; ?>

                    </div>

                    <!-- ORGANIZATION DETAILS -->
                    <div class="bg-white rounded-2xl shadow-sm border border-charcoal/10 p-6">

                        <h3 class="text-lg font-bold text-forest mb-5">
                            Organization Details
                        </h3>

                        <div class="space-y-4">

                            <div>
                                <p class="text-xs text-charcoal/50">
                                    NGO ID
                                </p>
                                <p class="font-semibold text-charcoal mt-1">
                                    #<?= $ngo['id'] ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-charcoal/50">
                                    Registered On
                                </p>
                                <p class="font-semibold text-charcoal mt-1">
                                    <?= date('d M Y', strtotime($ngo['created_at'])) ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-charcoal/50">
                                    Last Updated
                                </p>
                                <p class="font-semibold text-charcoal mt-1">
                                    <?= date('d M Y', strtotime($ngo['updated_at'])) ?>
                                </p>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        <?php endif; ?>

    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/ngo.php';
?>
