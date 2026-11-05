<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../Database/db_config.php';

$user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

$ngo = null;
$ngo_id = 0;

$successMsg = '';
$errorMsg = '';

if ($user_id > 0) {

    // Get NGO details
    $ngoQuery = mysqli_query(
        $con,
        "SELECT *
         FROM ngos
         WHERE user_id = $user_id
         LIMIT 1"
    );

    if ($ngoQuery && mysqli_num_rows($ngoQuery) > 0) {
        $ngo = mysqli_fetch_assoc($ngoQuery);
        $ngo_id = (int) $ngo['id'];
    } else {
        $errorMsg = 'NGO profile not found.';
    }

} else {
    $errorMsg = 'Please login to continue.';
}

// Default form values from database
$ngo_name = $ngo['name'] ?? '';
$registration_number = $ngo['registration_number'] ?? '';
$established_date = $ngo['established_date'] ?? '';
$ngo_type = $ngo['ngo_type'] ?? '';
$primary_cause = $ngo['primary_cause'] ?? '';
$description = $ngo['description'] ?? '';

$email = $ngo['official_email'] ?? '';
$phone = $ngo['phone'] ?? '';
$website = $ngo['website'] ?? '';
$contact_person = $ngo['contact_person'] ?? '';
$designation = $ngo['contact_designation'] ?? '';

$address = $ngo['address'] ?? '';
$city = $ngo['city'] ?? '';
$state = $ngo['state'] ?? '';
$pincode = $ngo['pincode'] ?? '';

$mission = $ngo['mission'] ?? '';
$impact = $ngo['impact_summary'] ?? '';

// Load existing documents
$existingDocuments = [];

if ($ngo_id > 0) {

    $documentQuery = mysqli_query(
        $con,
        "SELECT *
         FROM ngo_documents
         WHERE ngo_id = $ngo_id"
    );

    if ($documentQuery) {
        while ($document = mysqli_fetch_assoc($documentQuery)) {
            $existingDocuments[$document['document_type']] = $document;
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $ngo_id > 0) {

    // Keep submitted values if validation or database update fails
    $ngo_name = trim($_POST['ngo_name'] ?? '');
    $registration_number = trim($_POST['registration_number'] ?? '');
    $established_date = trim($_POST['established_date'] ?? '');
    $ngo_type = trim($_POST['ngo_type'] ?? '');
    $primary_cause = trim($_POST['primary_cause'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $contact_person = trim($_POST['contact_person'] ?? '');
    $designation = trim($_POST['designation'] ?? '');

    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');

    $mission = trim($_POST['mission'] ?? '');
    $impact = trim($_POST['impact'] ?? '');

    // Basic required field validation
    if (
        $ngo_name === '' ||
        $registration_number === '' ||
        $established_date === '' ||
        $ngo_type === '' ||
        $primary_cause === '' ||
        $description === '' ||
        $email === '' ||
        $phone === '' ||
        $contact_person === '' ||
        $designation === '' ||
        $address === '' ||
        $city === '' ||
        $state === '' ||
        $pincode === '' ||
        $mission === '' ||
        $impact === ''
    ) {
        $errorMsg = 'Please fill all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = 'Please enter a valid email address.';
    } elseif (!in_array($ngo_type, ['trust', 'society', 'section_8', 'other'])) {
        $errorMsg = 'Please select a valid NGO type.';
    } else {

        // Escape values for MySQL
        $ngo_name_db = mysqli_real_escape_string($con, $ngo_name);
        $registration_number_db = mysqli_real_escape_string($con, $registration_number);
        $established_date_db = mysqli_real_escape_string($con, $established_date);
        $ngo_type_db = mysqli_real_escape_string($con, $ngo_type);
        $primary_cause_db = mysqli_real_escape_string($con, $primary_cause);
        $description_db = mysqli_real_escape_string($con, $description);

        $email_db = mysqli_real_escape_string($con, $email);
        $phone_db = mysqli_real_escape_string($con, $phone);
        $website_db = mysqli_real_escape_string($con, $website);
        $contact_person_db = mysqli_real_escape_string($con, $contact_person);
        $designation_db = mysqli_real_escape_string($con, $designation);

        $address_db = mysqli_real_escape_string($con, $address);
        $city_db = mysqli_real_escape_string($con, $city);
        $state_db = mysqli_real_escape_string($con, $state);
        $pincode_db = mysqli_real_escape_string($con, $pincode);

        $mission_db = mysqli_real_escape_string($con, $mission);
        $impact_db = mysqli_real_escape_string($con, $impact);

        // Update NGO profile
        $updateNgo = mysqli_query(
            $con,
            "UPDATE ngos SET
                name = '$ngo_name_db',
                registration_number = '$registration_number_db',
                established_date = '$established_date_db',
                ngo_type = '$ngo_type_db',
                primary_cause = '$primary_cause_db',
                description = '$description_db',
                official_email = '$email_db',
                phone = '$phone_db',
                website = " . ($website_db !== '' ? "'$website_db'" : "NULL") . ",
                contact_person = '$contact_person_db',
                contact_designation = '$designation_db',
                address = '$address_db',
                city = '$city_db',
                state = '$state_db',
                pincode = '$pincode_db',
                mission = '$mission_db',
                impact_summary = '$impact_db'
             WHERE id = $ngo_id"
        );

        if ($updateNgo) {

            // Update user name and phone
            $updateUser = mysqli_query(
                $con,
                "UPDATE users SET
                    name = '$ngo_name_db',
                    phone = '$phone_db'
                 WHERE id = $user_id"
            );

            if ($updateUser) {

                // Document upload settings
                $uploadDirectory = __DIR__ . '/../uploads/ngo_documents/';

                if (!is_dir($uploadDirectory)) {
                    mkdir($uploadDirectory, 0777, true);
                }

                // Form input name => database document type
                $documentUploads = [
                    'doc_registration' => 'registration_certificate',
                    'doc_tax' => 'other',
                    'doc_address' => 'address_proof',
                    'doc_support' => 'supporting_document'
                ];

                $uploadErrors = [];

                foreach ($documentUploads as $inputName => $documentType) {

                    if (
                        !isset($_FILES[$inputName]) ||
                        $_FILES[$inputName]['error'] === UPLOAD_ERR_NO_FILE
                    ) {
                        continue;
                    }

                    $file = $_FILES[$inputName];

                    if ($file['error'] !== UPLOAD_ERR_OK) {
                        $uploadErrors[] = 'There was a problem uploading ' . $inputName . '.';
                        continue;
                    }

                    // Maximum file size: 5 MB
                    if ($file['size'] > 5 * 1024 * 1024) {
                        $uploadErrors[] = 'Each document must be smaller than 5 MB.';
                        continue;
                    }

                    $extension = strtolower(
                        pathinfo($file['name'], PATHINFO_EXTENSION)
                    );

                    $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];

                    if (!in_array($extension, $allowedExtensions)) {
                        $uploadErrors[] = 'Only PDF, JPG, JPEG and PNG files are allowed.';
                        continue;
                    }

                    // Check actual file MIME type
                    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($fileInfo, $file['tmp_name']);
                    finfo_close($fileInfo);

                    $allowedMimeTypes = [
                        'pdf' => 'application/pdf',
                        'jpg' => 'image/jpeg',
                        'jpeg' => 'image/jpeg',
                        'png' => 'image/png'
                    ];

                    if (
                        !isset($allowedMimeTypes[$extension]) ||
                        $mimeType !== $allowedMimeTypes[$extension]
                    ) {
                        $uploadErrors[] = 'Invalid file format for ' . $inputName . '.';
                        continue;
                    }

                    // Create a unique file name
                    $newFileName = 'ngo_' . $ngo_id . '_' . uniqid() . '.' . $extension;

                    $fileDestination = $uploadDirectory . $newFileName;

                    // Save uploaded file
                    if (move_uploaded_file($file['tmp_name'], $fileDestination)) {

                        $relativePath = 'uploads/ngo_documents/' . $newFileName;

                        $relativePathDb = mysqli_real_escape_string($con, $relativePath);
                        $fileNameDb = mysqli_real_escape_string($con, basename($file['name']));
                        $mimeTypeDb = mysqli_real_escape_string($con, $mimeType);

                        $documentTypeDb = mysqli_real_escape_string($con, $documentType);

                        // Check whether this document already exists
                        $checkDocument = mysqli_query(
                            $con,
                            "SELECT id
                             FROM ngo_documents
                             WHERE ngo_id = $ngo_id
                             AND document_type = '$documentTypeDb'
                             ORDER BY created_at DESC
                             LIMIT 1"
                        );

                        if ($checkDocument && mysqli_num_rows($checkDocument) > 0) {

                            $oldDocument = mysqli_fetch_assoc($checkDocument);
                            $documentId = (int) $oldDocument['id'];

                            // Replace existing document record
                            $saveDocument = mysqli_query(
                                $con,
                                "UPDATE ngo_documents SET
                                    file_path = '$relativePathDb',
                                    file_name = '$fileNameDb',
                                    file_size = " . (int) $file['size'] . ",
                                    mime_type = '$mimeTypeDb',
                                    created_at = NOW()
                                 WHERE id = $documentId"
                            );

                        } else {

                            // Insert new document record
                            $saveDocument = mysqli_query(
                                $con,
                                "INSERT INTO ngo_documents
                                (
                                    ngo_id,
                                    document_type,
                                    file_path,
                                    file_name,
                                    file_size,
                                    mime_type
                                )
                                VALUES
                                (
                                    $ngo_id,
                                    '$documentTypeDb',
                                    '$relativePathDb',
                                    '$fileNameDb',
                                    " . (int) $file['size'] . ",
                                    '$mimeTypeDb'
                                )"
                            );
                        }

                        if (!$saveDocument) {
                            $uploadErrors[] = 'Could not save document information in the database.';
                        }

                    } else {
                        $uploadErrors[] = 'Could not upload ' . $inputName . '.';
                    }
                }

                if (empty($uploadErrors)) {
                    $successMsg = 'NGO profile updated successfully!';
                } else {
                    $errorMsg = 'Profile details were saved, but some documents could not be uploaded: ' . implode(' ', $uploadErrors);
                }

                // Reload updated NGO information
                $refreshQuery = mysqli_query(
                    $con,
                    "SELECT *
                     FROM ngos
                     WHERE id = $ngo_id
                     LIMIT 1"
                );

                if ($refreshQuery && mysqli_num_rows($refreshQuery) > 0) {
                    $ngo = mysqli_fetch_assoc($refreshQuery);
                }

                // Reload document information
                $existingDocuments = [];

                $refreshDocuments = mysqli_query(
                    $con,
                    "SELECT *
                     FROM ngo_documents
                     WHERE ngo_id = $ngo_id"
                );

                if ($refreshDocuments) {
                    while ($document = mysqli_fetch_assoc($refreshDocuments)) {
                        $existingDocuments[$document['document_type']] = $document;
                    }
                }

            } else {
                $errorMsg = 'NGO details were updated, but user information could not be updated.';
            }

        } else {
            $errorMsg = 'Unable to update NGO profile. Please check your details and try again.';
        }
    }
}

// Document display names
$documentLabels = [
    'registration_certificate' => 'Registration Certificate',
    'pan_card' => 'PAN Card',
    'address_proof' => 'Address Proof',
    'supporting_document' => 'Supporting Document',
    'other' => 'Other Document'
];

ob_start();
?>

<div class="min-h-screen bg-ivory pt-24 pb-16">
    <div class="container mx-auto px-4 max-w-4xl">

        <!-- Header -->
        <div class="mb-8">

            <a href="<?= baseUrl('ngo/dashboard.php') ?>"
               class="inline-flex items-center text-charcoal-light hover:text-forest font-medium mb-4 transition-colors">

                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>

                Back to Dashboard
            </a>

            <h1 class="text-4xl md:text-5xl font-serif font-bold text-forest mb-4">
                Edit NGO Profile
            </h1>

            <p class="text-lg text-charcoal-light">
                Update your organization's information and profile details.
            </p>

        </div>

        <!-- Success Message -->
        <?php if (!empty($successMsg)) : ?>

            <div class="mb-8 bg-green-50 p-6 rounded-xl shadow-sm border border-green-200 text-center">
                <h2 class="text-2xl font-serif font-bold text-green-800 mb-2">
                    <?= $successMsg ?>
                </h2>
            </div>

        <?php endif; ?>

        <!-- Error Message -->
        <?php if (!empty($errorMsg)) : ?>

            <div class="mb-8 bg-red-50 p-5 rounded-xl border border-red-200 text-red-700">
                <?= $errorMsg ?>
            </div>

        <?php endif; ?>

        <?php if ($ngo) : ?>

            <div class="bg-white rounded-3xl shadow-sm border border-gold/20 overflow-hidden">

                <form id="ngoEditProfileForm"
                      action=""
                      method="POST"
                      class="p-6 md:p-10 space-y-12 validation-form"
                      novalidate
                      enctype="multipart/form-data">

                    <!-- Section 01: Organization Information -->
                    <section>

                        <div class="border-b border-charcoal/10 pb-4 mb-6">

                            <h2 class="text-2xl font-serif font-bold text-forest flex items-center">
                                <span class="bg-forest text-white text-sm w-8 h-8 rounded-full flex items-center justify-center mr-3 font-sans">
                                    01
                                </span>
                                Organization Information
                            </h2>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="md:col-span-2">

                                <label for="ngo_name" class="block text-sm font-bold text-charcoal mb-2">
                                    NGO Name *
                                </label>

                                <input type="text"
                                       id="ngo_name"
                                       name="ngo_name"
                                       value="<?= htmlspecialchars($ngo_name) ?>"
                                       required
                                       class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal"
                                       data-validate="required alpha">

                            </div>

                            <div>

                                <label for="registration_number" class="block text-sm font-bold text-charcoal mb-2">
                                    Registration Number *
                                </label>

                                <input type="text"
                                       id="registration_number"
                                       name="registration_number"
                                       value="<?= htmlspecialchars($registration_number) ?>"
                                       required
                                       class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal"
                                       data-validate="required">

                            </div>

                            <div>

                                <label for="established_date" class="block text-sm font-bold text-charcoal mb-2">
                                    Established Date *
                                </label>

                                <input type="date"
                                       id="established_date"
                                       name="established_date"
                                       value="<?= htmlspecialchars($established_date) ?>"
                                       required
                                       class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal"
                                       data-validate="required">

                            </div>

                            <div>

                                <label for="ngo_type" class="block text-sm font-bold text-charcoal mb-2">
                                    NGO Type *
                                </label>

                                <div class="relative">

                                    <select id="ngo_type"
                                            name="ngo_type"
                                            required
                                            class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal appearance-none"
                                            data-validate="required">

                                        <option value="" disabled <?= $ngo_type === '' ? 'selected' : '' ?>>
                                            Select NGO Type
                                        </option>

                                        <option value="trust" <?= $ngo_type === 'trust' ? 'selected' : '' ?>>
                                            Trust
                                        </option>

                                        <option value="society" <?= $ngo_type === 'society' ? 'selected' : '' ?>>
                                            Society
                                        </option>

                                        <option value="section_8" <?= $ngo_type === 'section_8' ? 'selected' : '' ?>>
                                            Section 8 Company
                                        </option>

                                        <option value="other" <?= $ngo_type === 'other' ? 'selected' : '' ?>>
                                            Other
                                        </option>

                                    </select>

                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-charcoal">

                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 9l-7 7-7-7"/>
                                        </svg>

                                    </div>

                                </div>

                            </div>

                            <div>

                                <label for="primary_cause" class="block text-sm font-bold text-charcoal mb-2">
                                    Primary Cause *
                                </label>

                                <div class="relative">

                                    <select id="primary_cause"
                                            name="primary_cause"
                                            required
                                            class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal appearance-none"
                                            data-validate="required">

                                        <option value="" disabled <?= $primary_cause === '' ? 'selected' : '' ?>>
                                            Select Primary Cause
                                        </option>

                                        <?php
                                        $causes = [
                                            'Education',
                                            'Healthcare',
                                            'Women Empowerment',
                                            'Child Welfare',
                                            'Animal Welfare',
                                            'Environment',
                                            'Community Development',
                                            'Other'
                                        ];
                                        ?>

                                        <?php foreach ($causes as $cause) : ?>

                                            <option value="<?= $cause ?>" <?= $primary_cause === $cause ? 'selected' : '' ?>>
                                                <?= $cause ?>
                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-charcoal">

                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 9l-7 7-7-7"/>
                                        </svg>

                                    </div>

                                </div>

                            </div>

                            <div class="md:col-span-2">

                                <label for="description" class="block text-sm font-bold text-charcoal mb-2">
                                    Description *
                                </label>

                                <textarea id="description"
                                          name="description"
                                          rows="4"
                                          required
                                          class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal resize-none"
                                          data-validate="required"><?= htmlspecialchars($description) ?></textarea>

                            </div>

                        </div>

                    </section>

                    <!-- Section 02: Contact Information -->
                    <section>

                        <div class="border-b border-charcoal/10 pb-4 mb-6">

                            <h2 class="text-2xl font-serif font-bold text-forest flex items-center">
                                <span class="bg-forest text-white text-sm w-8 h-8 rounded-full flex items-center justify-center mr-3 font-sans">
                                    02
                                </span>
                                Contact Information
                            </h2>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>

                                <label for="email" class="block text-sm font-bold text-charcoal mb-2">
                                    Official Email *
                                </label>

                                <input type="email"
                                       id="email"
                                       name="email"
                                       value="<?= htmlspecialchars($email) ?>"
                                       required
                                       class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal"
                                       data-validate="required email">

                            </div>

                            <div>

                                <label for="phone" class="block text-sm font-bold text-charcoal mb-2">
                                    Phone *
                                </label>

                                <input type="tel"
                                       id="phone"
                                       name="phone"
                                       value="<?= htmlspecialchars($phone) ?>"
                                       required
                                       class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal"
                                       data-validate="required numeric">

                            </div>

                            <div>

                                <label for="website" class="block text-sm font-bold text-charcoal mb-2">
                                    Website
                                </label>

                                <input type="text"
                                       id="website"
                                       name="website"
                                       value="<?= htmlspecialchars($website) ?>"
                                       class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal">

                            </div>

                            <div>

                                <label for="contact_person" class="block text-sm font-bold text-charcoal mb-2">
                                    Contact Person *
                                </label>

                                <input type="text"
                                       id="contact_person"
                                       name="contact_person"
                                       value="<?= htmlspecialchars($contact_person) ?>"
                                       required
                                       class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal"
                                       data-validate="required alpha">

                            </div>

                            <div>

                                <label for="designation" class="block text-sm font-bold text-charcoal mb-2">
                                    Designation *
                                </label>

                                <input type="text"
                                       id="designation"
                                       name="designation"
                                       value="<?= htmlspecialchars($designation) ?>"
                                       required
                                       class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal"
                                       data-validate="required alpha">

                            </div>

                        </div>

                    </section>

                    <!-- Section 03: Address Information -->
                    <section>

                        <div class="border-b border-charcoal/10 pb-4 mb-6">

                            <h2 class="text-2xl font-serif font-bold text-forest flex items-center">
                                <span class="bg-forest text-white text-sm w-8 h-8 rounded-full flex items-center justify-center mr-3 font-sans">
                                    03
                                </span>
                                Address Information
                            </h2>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="md:col-span-2">

                                <label for="address" class="block text-sm font-bold text-charcoal mb-2">
                                    Address *
                                </label>

                                <input type="text"
                                       id="address"
                                       name="address"
                                       value="<?= htmlspecialchars($address) ?>"
                                       required
                                       class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal"
                                       data-validate="required">

                            </div>

                            <div>

                                <label for="city" class="block text-sm font-bold text-charcoal mb-2">
                                    City *
                                </label>

                                <input type="text"
                                       id="city"
                                       name="city"
                                       value="<?= htmlspecialchars($city) ?>"
                                       required
                                       class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal"
                                       data-validate="required alpha">

                            </div>

                            <div>

                                <label for="state" class="block text-sm font-bold text-charcoal mb-2">
                                    State *
                                </label>

                                <input type="text"
                                       id="state"
                                       name="state"
                                       value="<?= htmlspecialchars($state) ?>"
                                       required
                                       class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal"
                                       data-validate="required alpha">

                            </div>

                            <div>

                                <label for="pincode" class="block text-sm font-bold text-charcoal mb-2">
                                    Pincode *
                                </label>

                                <input type="text"
                                       id="pincode"
                                       name="pincode"
                                       value="<?= htmlspecialchars($pincode) ?>"
                                       required
                                       class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal"
                                       data-validate="required numeric">

                            </div>

                        </div>

                    </section>

                    <!-- Section 04: Documents -->
                    <section>

                        <div class="border-b border-charcoal/10 pb-4 mb-6">

                            <h2 class="text-2xl font-serif font-bold text-forest flex items-center">
                                <span class="bg-forest text-white text-sm w-8 h-8 rounded-full flex items-center justify-center mr-3 font-sans">
                                    04
                                </span>
                                Documents
                            </h2>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <?php
                            $documentInputs = [
                                [
                                    'title' => 'Registration Certificate',
                                    'input' => 'doc_registration',
                                    'type' => 'registration_certificate'
                                ],
                                [
                                    'title' => 'Tax Document (12A/80G)',
                                    'input' => 'doc_tax',
                                    'type' => 'other'
                                ],
                                [
                                    'title' => 'Address Proof',
                                    'input' => 'doc_address',
                                    'type' => 'address_proof'
                                ],
                                [
                                    'title' => 'Supporting Document',
                                    'input' => 'doc_support',
                                    'type' => 'supporting_document'
                                ]
                            ];
                            ?>

                            <?php foreach ($documentInputs as $documentInput) : ?>

                                <?php
                                $documentType = $documentInput['type'];
                                $currentDocument = $existingDocuments[$documentType] ?? null;
                                ?>

                                <div class="bg-ivory/30 p-5 rounded-xl border border-charcoal/10">

                                    <label class="block text-sm font-bold text-charcoal mb-2">
                                        <?= $documentInput['title'] ?>
                                    </label>

                                    <?php if ($currentDocument) : ?>

                                        <div class="mb-3 text-sm text-forest font-medium">

                                            <p class="flex items-center gap-1 break-words">

                                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>

                                                Current: <?= $currentDocument['file_name'] ?>

                                            </p>

                                            <a href="<?= baseUrl($currentDocument['file_path']) ?>"
                                               target="_blank"
                                               class="inline-block mt-2 text-gold hover:text-forest underline">
                                                View Current Document
                                            </a>

                                        </div>

                                    <?php else : ?>

                                        <p class="mb-3 text-sm text-charcoal-light">
                                            No document currently uploaded
                                        </p>

                                    <?php endif; ?>

                                    <input type="file"
                                           id="<?= $documentInput['input'] ?>"
                                           name="<?= $documentInput['input'] ?>"
                                           class="block w-full text-sm text-charcoal-light file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-gold/10 file:text-forest hover:file:bg-gold/20 transition-colors"
                                           accept=".pdf,.jpg,.jpeg,.png">

                                    <p class="text-xs text-charcoal-light mt-2">
                                        Optional. Upload a new file (PDF, JPG, PNG up to 5MB).
                                    </p>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    </section>

                    <!-- Section 05: Organization Details -->
                    <section>

                        <div class="border-b border-charcoal/10 pb-4 mb-6">

                            <h2 class="text-2xl font-serif font-bold text-forest flex items-center">
                                <span class="bg-forest text-white text-sm w-8 h-8 rounded-full flex items-center justify-center mr-3 font-sans">
                                    05
                                </span>
                                Organization Details
                            </h2>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="md:col-span-2">

                                <label for="mission" class="block text-sm font-bold text-charcoal mb-2">
                                    Mission *
                                </label>

                                <textarea id="mission"
                                          name="mission"
                                          rows="3"
                                          required
                                          class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal resize-none"
                                          data-validate="required"><?= htmlspecialchars($mission) ?></textarea>

                            </div>

                            <div class="md:col-span-2">

                                <label for="impact" class="block text-sm font-bold text-charcoal mb-2">
                                    Impact *
                                </label>

                                <textarea id="impact"
                                          name="impact"
                                          rows="3"
                                          required
                                          class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all bg-ivory/30 focus:bg-white text-charcoal resize-none"
                                          data-validate="required"><?= htmlspecialchars($impact) ?></textarea>

                            </div>

                        </div>

                    </section>

                    <!-- Actions -->
                    <div class="flex flex-col-reverse md:flex-row items-center justify-end gap-4 pt-6 border-t border-charcoal/10">

                        <a href="<?= baseUrl('ngo/profile.php') ?>"
                           class="w-full md:w-auto px-8 py-3.5 border-2 border-charcoal/20 text-charcoal font-bold rounded-xl hover:bg-charcoal/5 transition-colors text-center">
                            Cancel
                        </a>

                        <button type="submit"
                                class="w-full md:w-auto px-8 py-3.5 bg-forest text-white font-bold rounded-xl hover:bg-forest-dark shadow-md hover:shadow-lg transition-all text-center">
                            Save Changes
                        </button>

                    </div>

                </form>

            </div>

        <?php endif; ?>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/ngo.php';
?>
