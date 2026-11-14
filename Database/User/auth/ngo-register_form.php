<?php
require_once __DIR__ . "/../../../includes/functions.php";
require_once __DIR__ . "/../../db_config.php";

if (isset($_POST["register_ngo"])) {

    // Organization details
    $name = trim($_POST["ngo_name"] ?? "");
    $registrationNumber = trim($_POST["registration_number"] ?? "");
    $establishedDate = trim($_POST["established_date"] ?? "");
    $ngoTypeInput = trim($_POST["ngo_type"] ?? "");
    $primaryCause = trim($_POST["primary_cause"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $mission = trim($_POST["mission"] ?? "");
    $impact = trim($_POST["impact"] ?? "");
    $website = trim($_POST["website"] ?? "");

    // Account details
    $email = trim($_POST["official_email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    // Contact details
    $contactPerson = trim($_POST["contact_person"] ?? "");
    $designation = trim($_POST["designation"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $state = trim($_POST["state"] ?? "");
    $pincode = trim($_POST["pincode"] ?? "");

    // Check required fields
    if (
        $name === "" || $registrationNumber === "" ||
        $establishedDate === "" || $ngoTypeInput === "" ||
        $primaryCause === "" || $description === "" ||
        $mission === "" || $impact === "" ||
        $email === "" || $phone === "" ||
        $password === "" || $confirmPassword === "" ||
        $contactPerson === "" || $designation === "" ||
        $address === "" || $city === "" ||
        $state === "" || $pincode === ""
    ) {
        exit("Please fill all required fields.");
    }

    if ($password !== $confirmPassword) {
        exit("Passwords do not match.");
    }

    if (!isset($_POST["confirm_info"]) || !isset($_POST["agree_guidelines"])) {
        exit("Please accept both declarations.");
    }

    // Validate service areas
    $rawServiceAreas = $_POST["service_areas"] ?? [];
    if (!is_array($rawServiceAreas) || empty($rawServiceAreas)) {
        exit("Please select a service area.");
    }

    $serviceAreas = [];
    foreach ($rawServiceAreas as $area) {
        $cleanArea = trim($area);
        if ($cleanArea !== "" && !in_array($cleanArea, $serviceAreas, true)) {
            $serviceAreas[] = $cleanArea;
        }
    }

    if (empty($serviceAreas)) {
        exit("Please select a service area.");
    }

    // Map NGO type to database enum: enum('trust','society','section_8','other')
    $ngoTypeMap = [
        'trust' => 'trust',
        'society' => 'society',
        'section 8 company' => 'section_8',
        'section_8' => 'section_8',
        'section 8' => 'section_8',
        'non-profit organization' => 'other',
        'other' => 'other'
    ];
    $ngoType = $ngoTypeMap[strtolower($ngoTypeInput)] ?? 'other';

    // Document configurations
    $docConfigs = [
        'registration_certificate' => [
            'required' => true,
            'label' => 'NGO Registration Certificate',
            'default_type' => 'registration_certificate'
        ],
        'tax_document' => [
            'required' => true,
            'label' => 'PAN / Tax Document',
            'default_type' => 'other'
        ],
        'address_proof' => [
            'required' => true,
            'label' => 'Address Proof',
            'default_type' => 'address_proof'
        ],
        'supporting_document' => [
            'required' => false,
            'label' => 'Supporting Document',
            'default_type' => 'supporting_document'
        ]
    ];

    $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
    $allowedMimeTypes = [
        'pdf'  => ['application/pdf'],
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png']
    ];

    // Validate uploaded files before touching the database or filesystem
    $validatedFiles = [];

    foreach ($docConfigs as $field => $config) {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
            if ($config['required']) {
                exit("Please upload the required document: " . $config['label'] . ".");
            }
            continue;
        }

        if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            exit("Error uploading " . $config['label'] . ".");
        }

        // Maximum upload size: 5 MB
        if ($_FILES[$field]['size'] > 5 * 1024 * 1024) {
            exit($config['label'] . " must be smaller than 5 MB.");
        }

        if ($_FILES[$field]['size'] <= 0) {
            exit($config['label'] . " file is empty.");
        }

        $origFileName = basename($_FILES[$field]['name']);
        $ext = strtolower(pathinfo($origFileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions, true)) {
            exit("Invalid file type for " . $config['label'] . ". Only PDF, JPG, JPEG, and PNG files are allowed.");
        }

        // Verify MIME type using PHP fileinfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $_FILES[$field]['tmp_name']);
        finfo_close($finfo);

        if (!isset($allowedMimeTypes[$ext]) || !in_array($mimeType, $allowedMimeTypes[$ext], true)) {
            exit("Invalid file format for " . $config['label'] . ".");
        }

        // Determine valid database document_type enum value
        $docType = $config['default_type'];
        if ($field === 'tax_document') {
            $docType = (stripos($origFileName, 'pan') !== false) ? 'pan_card' : 'other';
        }

        $validatedFiles[$field] = [
            'orig_name' => $origFileName,
            'ext'       => $ext,
            'tmp_name'  => $_FILES[$field]['tmp_name'],
            'size'      => (int) $_FILES[$field]['size'],
            'mime_type' => $mimeType,
            'doc_type'  => $docType,
            'label'     => $config['label']
        ];
    }

    // Check if email already registered in users table
    $emailEsc = mysqli_real_escape_string($con, $email);
    $userCheckSql = "SELECT id FROM users WHERE email = '$emailEsc' LIMIT 1";
    $userCheckRes = mysqli_query($con, $userCheckSql);
    if ($userCheckRes && mysqli_num_rows($userCheckRes) > 0) {
        exit("Email is already registered.");
    }

    // Check if registration number or official email already registered in ngos table
    $regNumberEsc = mysqli_real_escape_string($con, $registrationNumber);
    $ngoCheckSql = "SELECT id FROM ngos WHERE registration_number = '$regNumberEsc' OR official_email = '$emailEsc' LIMIT 1";
    $ngoCheckRes = mysqli_query($con, $ngoCheckSql);
    if ($ngoCheckRes && mysqli_num_rows($ngoCheckRes) > 0) {
        exit("NGO with this registration number or official email is already registered.");
    }

    // Target upload folder in root uploads/ngo_documents/
    $folder = __DIR__ . "/../../../uploads/ngo_documents/";
    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }

    $uploadedFilesOnDisk = [];

    // Begin transaction for all database insertions
    mysqli_begin_transaction($con);

    try {
        // 1. Insert User account
        $contactPersonEsc = mysqli_real_escape_string($con, $contactPerson);
        $phoneEsc = mysqli_real_escape_string($con, $phone);
        $passwordEsc = mysqli_real_escape_string($con, $password);

        $userSql = "INSERT INTO users (role, name, email, phone, password_hash, status, created_at, updated_at)
                    VALUES ('ngo', '$contactPersonEsc', '$emailEsc', '$phoneEsc', '$passwordEsc', 'pending', NOW(), NOW())";

        if (!mysqli_query($con, $userSql)) {
            throw new Exception("Account registration failed: " . mysqli_error($con));
        }

        $userId = (int) mysqli_insert_id($con);

        // 2. Insert NGO details
        $nameEsc = mysqli_real_escape_string($con, $name);
        $establishedDateEsc = mysqli_real_escape_string($con, $establishedDate);
        $ngoTypeEsc = mysqli_real_escape_string($con, $ngoType);
        $primaryCauseEsc = mysqli_real_escape_string($con, $primaryCause);
        $descriptionEsc = mysqli_real_escape_string($con, $description);
        $missionEsc = mysqli_real_escape_string($con, $mission);
        $impactEsc = mysqli_real_escape_string($con, $impact);
        $websiteEsc = $website !== "" ? "'" . mysqli_real_escape_string($con, $website) . "'" : "NULL";
        $designationEsc = mysqli_real_escape_string($con, $designation);
        $addressEsc = mysqli_real_escape_string($con, $address);
        $cityEsc = mysqli_real_escape_string($con, $city);
        $stateEsc = mysqli_real_escape_string($con, $state);
        $pincodeEsc = mysqli_real_escape_string($con, $pincode);

        $ngoSql = "INSERT INTO ngos (
                        user_id, name, registration_number,
                        established_date, ngo_type, primary_cause,
                        description, mission, impact_summary,
                        official_email, phone, website,
                        contact_person, contact_designation,
                        address, city, state, pincode,
                        verification_status, created_at, updated_at
                    ) VALUES (
                        $userId, '$nameEsc', '$regNumberEsc',
                        '$establishedDateEsc', '$ngoTypeEsc', '$primaryCauseEsc',
                        '$descriptionEsc', '$missionEsc', '$impactEsc',
                        '$emailEsc', '$phoneEsc', $websiteEsc,
                        '$contactPersonEsc', '$designationEsc',
                        '$addressEsc', '$cityEsc', '$stateEsc', '$pincodeEsc',
                        'pending', NOW(), NOW()
                    )";

        if (!mysqli_query($con, $ngoSql)) {
            throw new Exception("NGO registration failed: " . mysqli_error($con));
        }

        $ngoId = (int) mysqli_insert_id($con);

        // 3. Insert Service Areas
        foreach ($serviceAreas as $area) {
            $areaEsc = mysqli_real_escape_string($con, $area);
            $serviceAreaSql = "INSERT INTO ngo_service_areas (ngo_id, service_area, created_at)
                               VALUES ($ngoId, '$areaEsc', NOW())";
            if (!mysqli_query($con, $serviceAreaSql)) {
                throw new Exception("Service area registration failed: " . mysqli_error($con));
            }
        }

        // 4. Move uploaded files and insert Document records
        foreach ($validatedFiles as $field => $fileData) {
            $safeFileName = 'ngo_' . $ngoId . '_' . uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . $fileData['ext'];
            $destPath = $folder . $safeFileName;
            $dbFilePath = 'uploads/ngo_documents/' . $safeFileName;

            if (!move_uploaded_file($fileData['tmp_name'], $destPath)) {
                throw new Exception("Unable to upload document: " . $fileData['label']);
            }
            $uploadedFilesOnDisk[] = $destPath;

            $origNameEsc = mysqli_real_escape_string($con, $fileData['orig_name']);
            $dbFilePathEsc = mysqli_real_escape_string($con, $dbFilePath);
            $mimeEsc = mysqli_real_escape_string($con, $fileData['mime_type']);
            $docTypeEsc = mysqli_real_escape_string($con, $fileData['doc_type']);
            $fileSize = $fileData['size'];

            $docSql = "INSERT INTO ngo_documents (
                            ngo_id, document_type, document_number,
                            file_path, file_name, file_size, mime_type, created_at
                        ) VALUES (
                            $ngoId, '$docTypeEsc', '',
                            '$dbFilePathEsc', '$origNameEsc', $fileSize, '$mimeEsc', NOW()
                        )";

            if (!mysqli_query($con, $docSql)) {
                throw new Exception("Document record failed: " . mysqli_error($con));
            }
        }

        // Commit transaction if all steps succeeded
        mysqli_commit($con);

        header("Location: " . baseUrl("auth/login.php"));
        exit();

    } catch (Exception $e) {
        mysqli_rollback($con);

        // Clean up any files moved to disk during failed registration
        foreach ($uploadedFilesOnDisk as $filePath) {
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        exit($e->getMessage());
    }

} else {
    header("Location: " . baseUrl("auth/ngo-register.php"));
    exit();
}
?>
