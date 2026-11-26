<?php
require_once __DIR__ . '/../includes/functions.php';
$ngo = requireNgo(true);
require_once __DIR__ . '/../Database/db_config.php';

$ngoId = (int) ($ngo['id'] ?? 0);
$userId = (int) ($_SESSION['user_id'] ?? 0);

$status = strtolower(trim($ngo['verification_status'] ?? 'pending'));
if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
    $status = 'pending';
}

$ngoTypeLabels = [
    'trust' => 'Trust',
    'society' => 'Society',
    'section_8' => 'Section 8 Company',
    'other' => 'Other'
];

$primaryCauseOptions = [
    'Education',
    'Healthcare',
    'Women Empowerment',
    'Child Welfare',
    'Animal Welfare',
    'Environment',
    'Hunger & Food Support',
    'Elderly Care',
    'Disability Support',
    'Community Development',
    'Other'
];

$availableServiceAreas = [
    'Local Community',
    'District',
    'State',
    'National',
    'Rural Communities',
    'Urban Communities'
];

$documentTypeLabels = [
    'registration_certificate' => 'NGO Registration Certificate',
    'pan_card' => 'PAN / Tax Document',
    'address_proof' => 'Address Proof',
    'supporting_document' => 'Supporting Document',
    'other' => 'PAN / Tax Document'
];

$serviceAreaRows = getNgoServiceAreas($ngoId);
$serviceAreas = array_values(array_unique(array_filter(array_column($serviceAreaRows, 'service_area'))));
$documents = getNgoDocuments($ngoId);

$docsBySlot = [
    'registration_certificate' => null,
    'tax_document' => null,
    'address_proof' => null,
    'supporting_document' => null
];

foreach ($documents as $docRow) {
    $docTypeVal = $docRow['document_type'] ?? '';
    if ($docTypeVal === 'registration_certificate' && $docsBySlot['registration_certificate'] === null) {
        $docsBySlot['registration_certificate'] = $docRow;
    } elseif (in_array($docTypeVal, ['pan_card', 'other'], true) && $docsBySlot['tax_document'] === null) {
        $docsBySlot['tax_document'] = $docRow;
    } elseif ($docTypeVal === 'address_proof' && $docsBySlot['address_proof'] === null) {
        $docsBySlot['address_proof'] = $docRow;
    } elseif ($docTypeVal === 'supporting_document' && $docsBySlot['supporting_document'] === null) {
        $docsBySlot['supporting_document'] = $docRow;
    }
}

$formValues = [
    'ngo_name' => $ngo['name'] ?? '',
    'registration_number' => $ngo['registration_number'] ?? '',
    'established_date' => $ngo['established_date'] ?? '',
    'ngo_type' => $ngo['ngo_type'] ?? '',
    'primary_cause' => $ngo['primary_cause'] ?? '',
    'description' => $ngo['description'] ?? '',
    'official_email' => $ngo['official_email'] ?? '',
    'phone' => $ngo['phone'] ?? '',
    'website' => $ngo['website'] ?? '',
    'contact_person' => $ngo['contact_person'] ?? '',
    'designation' => $ngo['contact_designation'] ?? '',
    'address' => $ngo['address'] ?? '',
    'city' => $ngo['city'] ?? '',
    'state' => $ngo['state'] ?? '',
    'pincode' => $ngo['pincode'] ?? '',
    'mission' => $ngo['mission'] ?? '',
    'impact' => $ngo['impact_summary'] ?? '',
    'service_areas' => $serviceAreas
];

$formErrors = [];
$showEditForm = ($status === 'rejected' && isset($_GET['edit']) && $_GET['edit'] === '1');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['app_status_error'] = 'Invalid security token. Please refresh the page and try again.';
        header('Location: ' . baseUrl('ngo/application-status.php'));
        exit;
    }

    if ($status !== 'rejected' || !isset($_POST['resubmit_application'])) {
        $_SESSION['app_status_error'] = 'Application editing is only available when your application requires changes.';
        header('Location: ' . baseUrl('ngo/application-status.php'));
        exit;
    }

    $showEditForm = true;

    $formValues = [
        'ngo_name' => trim((string) ($_POST['ngo_name'] ?? '')),
        'registration_number' => trim((string) ($_POST['registration_number'] ?? '')),
        'established_date' => trim((string) ($_POST['established_date'] ?? '')),
        'ngo_type' => trim((string) ($_POST['ngo_type'] ?? '')),
        'primary_cause' => trim((string) ($_POST['primary_cause'] ?? '')),
        'description' => trim((string) ($_POST['description'] ?? '')),
        'official_email' => trim((string) ($_POST['official_email'] ?? '')),
        'phone' => trim((string) ($_POST['phone'] ?? '')),
        'website' => trim((string) ($_POST['website'] ?? '')),
        'contact_person' => trim((string) ($_POST['contact_person'] ?? '')),
        'designation' => trim((string) ($_POST['designation'] ?? '')),
        'address' => trim((string) ($_POST['address'] ?? '')),
        'city' => trim((string) ($_POST['city'] ?? '')),
        'state' => trim((string) ($_POST['state'] ?? '')),
        'pincode' => trim((string) ($_POST['pincode'] ?? '')),
        'mission' => trim((string) ($_POST['mission'] ?? '')),
        'impact' => trim((string) ($_POST['impact'] ?? '')),
        'service_areas' => []
    ];

    $rawServiceAreas = $_POST['service_areas'] ?? [];
    if (is_array($rawServiceAreas)) {
        foreach ($rawServiceAreas as $area) {
            $cleanArea = trim((string) $area);
            if ($cleanArea !== '' && !in_array($cleanArea, $formValues['service_areas'], true)) {
                $formValues['service_areas'][] = $cleanArea;
            }
        }
    }

    $requiredFieldLabels = [
        'ngo_name' => 'NGO / Organization Name',
        'registration_number' => 'Registration Number',
        'established_date' => 'Established Date',
        'ngo_type' => 'NGO Type',
        'primary_cause' => 'Primary Cause',
        'description' => 'Organization Description',
        'official_email' => 'Official Email Address',
        'phone' => 'Official Phone Number',
        'contact_person' => 'Contact Person Name',
        'designation' => 'Contact Person Designation',
        'address' => 'Address',
        'city' => 'City',
        'state' => 'State',
        'pincode' => 'Pincode',
        'mission' => 'Mission Statement',
        'impact' => 'Brief Impact / Achievements'
    ];

    foreach ($requiredFieldLabels as $fieldKey => $fieldLabel) {
        if ($formValues[$fieldKey] === '') {
            $formErrors[] = $fieldLabel . ' is required.';
        }
    }

    if ($formValues['official_email'] !== '' && !filter_var($formValues['official_email'], FILTER_VALIDATE_EMAIL)) {
        $formErrors[] = 'Please enter a valid official email address.';
    }

    if ($formValues['phone'] !== '' && !preg_match('/^\d{10}$/', $formValues['phone'])) {
        $formErrors[] = 'Official phone number must be exactly 10 digits.';
    }

    if ($formValues['established_date'] !== '') {
        $estDateObj = DateTime::createFromFormat('Y-m-d', $formValues['established_date']);
        $todayStr = date('Y-m-d');
        if (!$estDateObj || $estDateObj->format('Y-m-d') !== $formValues['established_date']) {
            $formErrors[] = 'Please enter a valid established date.';
        } elseif ($formValues['established_date'] > $todayStr) {
            $formErrors[] = 'Established date cannot be in the future.';
        }
    }

    if ($formValues['ngo_type'] !== '' && !array_key_exists($formValues['ngo_type'], $ngoTypeLabels)) {
        $formErrors[] = 'Please select a valid NGO type.';
    }

    if ($formValues['website'] !== '') {
        $scheme = strtolower((string) parse_url($formValues['website'], PHP_URL_SCHEME));
        if (!filter_var($formValues['website'], FILTER_VALIDATE_URL) || !in_array($scheme, ['http', 'https'], true)) {
            $formErrors[] = 'Please enter a valid website URL starting with http:// or https://.';
        }
    }

    if ($formValues['pincode'] !== '' && !preg_match('/^[0-9]{4,10}$/', $formValues['pincode'])) {
        $formErrors[] = 'Please enter a valid numeric pincode.';
    }

    if (empty($formValues['service_areas'])) {
        $formErrors[] = 'Please select at least one service area.';
    } else {
        foreach ($formValues['service_areas'] as $selectedArea) {
            if (mb_strlen($selectedArea, 'UTF-8') > 100) {
                $formErrors[] = 'Each service area must be 100 characters or fewer.';
                break;
            }
        }
    }

    if (!isset($_POST['confirm_info'])) {
        $formErrors[] = 'Please confirm that the updated information and documents are accurate.';
    }

    if ($formValues['official_email'] !== '' && empty($formErrors)) {
        $emailCheckStmt = mysqli_prepare($con, 'SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1');
        if ($emailCheckStmt) {
            mysqli_stmt_bind_param($emailCheckStmt, 'si', $formValues['official_email'], $userId);
            mysqli_stmt_execute($emailCheckStmt);
            $emailCheckRes = mysqli_stmt_get_result($emailCheckStmt);
            if ($emailCheckRes && mysqli_num_rows($emailCheckRes) > 0) {
                $formErrors[] = 'This email address is already registered to another account.';
            }
            mysqli_stmt_close($emailCheckStmt);
        } else {
            $formErrors[] = 'Unable to verify email uniqueness right now.';
        }
    }

    if ($formValues['registration_number'] !== '' && $formValues['official_email'] !== '' && empty($formErrors)) {
        $ngoCheckStmt = mysqli_prepare($con, 'SELECT id FROM ngos WHERE (registration_number = ? OR official_email = ?) AND id <> ? LIMIT 1');
        if ($ngoCheckStmt) {
            mysqli_stmt_bind_param($ngoCheckStmt, 'ssi', $formValues['registration_number'], $formValues['official_email'], $ngoId);
            mysqli_stmt_execute($ngoCheckStmt);
            $ngoCheckRes = mysqli_stmt_get_result($ngoCheckStmt);
            if ($ngoCheckRes && mysqli_num_rows($ngoCheckRes) > 0) {
                $formErrors[] = 'Another NGO with this registration number or official email already exists.';
            }
            mysqli_stmt_close($ngoCheckStmt);
        } else {
            $formErrors[] = 'Unable to verify NGO registration details right now.';
        }
    }

    $docFieldConfigs = [
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
        'pdf' => ['application/pdf'],
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png']
    ];

    $validatedUploads = [];

    foreach ($docFieldConfigs as $fieldName => $docCfg) {
        $hasUpload = isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] !== UPLOAD_ERR_NO_FILE;
        if (!$hasUpload) {
            if ($docCfg['required'] && $docsBySlot[$fieldName] === null) {
                $formErrors[] = 'Please upload the required document: ' . $docCfg['label'] . '.';
            }
            continue;
        }

        $file = $_FILES[$fieldName];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $formErrors[] = 'Error uploading ' . $docCfg['label'] . '.';
            continue;
        }

        if ((int) $file['size'] <= 0) {
            $formErrors[] = $docCfg['label'] . ' file is empty.';
            continue;
        }

        if ((int) $file['size'] > 5 * 1024 * 1024) {
            $formErrors[] = $docCfg['label'] . ' must be smaller than 5 MB.';
            continue;
        }

        $origFileName = basename((string) $file['name']);
        $ext = strtolower(pathinfo($origFileName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExtensions, true)) {
            $formErrors[] = 'Invalid file type for ' . $docCfg['label'] . '. Only PDF, JPG, JPEG, and PNG files are allowed.';
            continue;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = $finfo ? finfo_file($finfo, $file['tmp_name']) : false;
        if ($finfo) {
            finfo_close($finfo);
        }

        if (!$mimeType || !isset($allowedMimeTypes[$ext]) || !in_array($mimeType, $allowedMimeTypes[$ext], true)) {
            $formErrors[] = 'Invalid file format detected for ' . $docCfg['label'] . '.';
            continue;
        }

        $docType = $docCfg['default_type'];
        if ($fieldName === 'tax_document') {
            $docType = (stripos($origFileName, 'pan') !== false) ? 'pan_card' : 'other';
        }

        $validatedUploads[$fieldName] = [
            'orig_name' => $origFileName,
            'ext' => $ext,
            'tmp_name' => $file['tmp_name'],
            'size' => (int) $file['size'],
            'mime_type' => $mimeType,
            'doc_type' => $docType,
            'label' => $docCfg['label']
        ];
    }

    if (empty($formErrors)) {
        $uploadDir = __DIR__ . '/../uploads/ngo_documents/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            $formErrors[] = 'Unable to prepare document storage directory.';
        } else {
            $newlyMovedFiles = [];
            $oldRelativePathsToDelete = [];

            mysqli_begin_transaction($con);

            try {
                $websiteVal = $formValues['website'] !== '' ? $formValues['website'] : null;

                $updateNgoStmt = mysqli_prepare(
                    $con,
                    "UPDATE ngos
                     SET name = ?,
                         registration_number = ?,
                         established_date = ?,
                         ngo_type = ?,
                         primary_cause = ?,
                         description = ?,
                         mission = ?,
                         impact_summary = ?,
                         official_email = ?,
                         phone = ?,
                         website = ?,
                         contact_person = ?,
                         contact_designation = ?,
                         address = ?,
                         city = ?,
                         state = ?,
                         pincode = ?,
                         verification_status = 'pending',
                         rejection_reason = NULL,
                         verified_at = NULL,
                         updated_at = NOW()
                     WHERE id = ? AND user_id = ? AND verification_status = 'rejected'"
                );

                if (!$updateNgoStmt) {
                    throw new Exception('Unable to prepare NGO application update.');
                }

                mysqli_stmt_bind_param(
                    $updateNgoStmt,
                    'sssssssssssssssssii',
                    $formValues['ngo_name'],
                    $formValues['registration_number'],
                    $formValues['established_date'],
                    $formValues['ngo_type'],
                    $formValues['primary_cause'],
                    $formValues['description'],
                    $formValues['mission'],
                    $formValues['impact'],
                    $formValues['official_email'],
                    $formValues['phone'],
                    $websiteVal,
                    $formValues['contact_person'],
                    $formValues['designation'],
                    $formValues['address'],
                    $formValues['city'],
                    $formValues['state'],
                    $formValues['pincode'],
                    $ngoId,
                    $userId
                );

                if (!mysqli_stmt_execute($updateNgoStmt)) {
                    mysqli_stmt_close($updateNgoStmt);
                    throw new Exception('Failed to update NGO application details.');
                }

                $ngoUpdatedRows = mysqli_stmt_affected_rows($updateNgoStmt);
                mysqli_stmt_close($updateNgoStmt);

                if ($ngoUpdatedRows !== 1) {
                    throw new Exception('Application status is no longer eligible for resubmission.');
                }

                $updateUserStmt = mysqli_prepare(
                    $con,
                    "UPDATE users
                     SET name = ?,
                         email = ?,
                         phone = ?,
                         status = 'pending',
                         updated_at = NOW()
                     WHERE id = ? AND role = 'ngo'"
                );

                if (!$updateUserStmt) {
                    throw new Exception('Unable to prepare user account update.');
                }

                mysqli_stmt_bind_param(
                    $updateUserStmt,
                    'sssi',
                    $formValues['contact_person'],
                    $formValues['official_email'],
                    $formValues['phone'],
                    $userId
                );

                if (!mysqli_stmt_execute($updateUserStmt)) {
                    mysqli_stmt_close($updateUserStmt);
                    throw new Exception('Failed to update NGO account record.');
                }
                mysqli_stmt_close($updateUserStmt);

                $deleteAreasStmt = mysqli_prepare($con, 'DELETE FROM ngo_service_areas WHERE ngo_id = ?');
                if (!$deleteAreasStmt) {
                    throw new Exception('Unable to prepare service area cleanup.');
                }
                mysqli_stmt_bind_param($deleteAreasStmt, 'i', $ngoId);
                if (!mysqli_stmt_execute($deleteAreasStmt)) {
                    mysqli_stmt_close($deleteAreasStmt);
                    throw new Exception('Failed to update NGO service areas.');
                }
                mysqli_stmt_close($deleteAreasStmt);

                $insertAreaStmt = mysqli_prepare($con, 'INSERT INTO ngo_service_areas (ngo_id, service_area, created_at) VALUES (?, ?, NOW())');
                if (!$insertAreaStmt) {
                    throw new Exception('Unable to prepare service area insert.');
                }
                foreach ($formValues['service_areas'] as $areaVal) {
                    mysqli_stmt_bind_param($insertAreaStmt, 'is', $ngoId, $areaVal);
                    if (!mysqli_stmt_execute($insertAreaStmt)) {
                        mysqli_stmt_close($insertAreaStmt);
                        throw new Exception('Failed to save NGO service area.');
                    }
                }
                mysqli_stmt_close($insertAreaStmt);

                foreach ($validatedUploads as $fieldName => $uploadData) {
                    $safeFileName = 'ngo_' . $ngoId . '_' . bin2hex(random_bytes(8)) . '.' . $uploadData['ext'];
                    $destPath = $uploadDir . $safeFileName;
                    $dbFilePath = 'uploads/ngo_documents/' . $safeFileName;

                    if (!move_uploaded_file($uploadData['tmp_name'], $destPath)) {
                        throw new Exception('Unable to store uploaded file for ' . $uploadData['label'] . '.');
                    }
                    $newlyMovedFiles[] = $destPath;

                    $existingDocId = 0;
                    $existingOldPath = '';

                    if ($fieldName === 'tax_document') {
                        $findDocStmt = mysqli_prepare(
                            $con,
                            "SELECT id, file_path
                             FROM ngo_documents
                             WHERE ngo_id = ? AND document_type IN ('pan_card', 'other')
                             ORDER BY created_at DESC, id DESC
                             LIMIT 1"
                        );
                        if (!$findDocStmt) {
                            throw new Exception('Unable to look up existing tax document.');
                        }
                        mysqli_stmt_bind_param($findDocStmt, 'i', $ngoId);
                    } else {
                        $findDocStmt = mysqli_prepare(
                            $con,
                            "SELECT id, file_path
                             FROM ngo_documents
                             WHERE ngo_id = ? AND document_type = ?
                             ORDER BY created_at DESC, id DESC
                             LIMIT 1"
                        );
                        if (!$findDocStmt) {
                            throw new Exception('Unable to look up existing document.');
                        }
                        mysqli_stmt_bind_param($findDocStmt, 'is', $ngoId, $uploadData['doc_type']);
                    }

                    mysqli_stmt_execute($findDocStmt);
                    $findDocRes = mysqli_stmt_get_result($findDocStmt);
                    if ($findDocRes && ($existingRow = mysqli_fetch_assoc($findDocRes))) {
                        $existingDocId = (int) $existingRow['id'];
                        $existingOldPath = (string) ($existingRow['file_path'] ?? '');
                    }
                    mysqli_stmt_close($findDocStmt);

                    if ($existingDocId > 0) {
                        $updateDocStmt = mysqli_prepare(
                            $con,
                            "UPDATE ngo_documents
                             SET document_type = ?,
                                 file_path = ?,
                                 file_name = ?,
                                 file_size = ?,
                                 mime_type = ?,
                                 created_at = NOW()
                             WHERE id = ? AND ngo_id = ?"
                        );
                        if (!$updateDocStmt) {
                            throw new Exception('Unable to prepare document update.');
                        }
                        mysqli_stmt_bind_param(
                            $updateDocStmt,
                            'sssisii',
                            $uploadData['doc_type'],
                            $dbFilePath,
                            $uploadData['orig_name'],
                            $uploadData['size'],
                            $uploadData['mime_type'],
                            $existingDocId,
                            $ngoId
                        );
                        if (!mysqli_stmt_execute($updateDocStmt)) {
                            mysqli_stmt_close($updateDocStmt);
                            throw new Exception('Failed to update document record.');
                        }
                        mysqli_stmt_close($updateDocStmt);

                        if ($existingOldPath !== '') {
                            $oldRelativePathsToDelete[] = $existingOldPath;
                        }
                    } else {
                        $insertDocStmt = mysqli_prepare(
                            $con,
                            "INSERT INTO ngo_documents
                             (ngo_id, document_type, document_number, file_path, file_name, file_size, mime_type, created_at)
                             VALUES (?, ?, '', ?, ?, ?, ?, NOW())"
                        );
                        if (!$insertDocStmt) {
                            throw new Exception('Unable to prepare document record.');
                        }
                        mysqli_stmt_bind_param(
                            $insertDocStmt,
                            'isssis',
                            $ngoId,
                            $uploadData['doc_type'],
                            $dbFilePath,
                            $uploadData['orig_name'],
                            $uploadData['size'],
                            $uploadData['mime_type']
                        );
                        if (!mysqli_stmt_execute($insertDocStmt)) {
                            mysqli_stmt_close($insertDocStmt);
                            throw new Exception('Failed to save document record.');
                        }
                        mysqli_stmt_close($insertDocStmt);
                    }
                }

                mysqli_commit($con);

                $_SESSION['user_name'] = $formValues['contact_person'];
                $_SESSION['user_email'] = $formValues['official_email'];

                $uploadsBaseReal = realpath(__DIR__ . '/../uploads/ngo_documents');
                $newRealPaths = array_filter(array_map('realpath', $newlyMovedFiles));
                if ($uploadsBaseReal !== false) {
                    $uploadsPrefix = rtrim($uploadsBaseReal, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
                    foreach ($oldRelativePathsToDelete as $oldRelPath) {
                        $normalizedRel = ltrim(str_replace('\\', '/', $oldRelPath), '/');
                        if (strpos($normalizedRel, 'uploads/ngo_documents/') !== 0) {
                            continue;
                        }
                        $oldFullReal = realpath(__DIR__ . '/../' . $normalizedRel);
                        if (
                            $oldFullReal !== false &&
                            strpos($oldFullReal, $uploadsPrefix) === 0 &&
                            !in_array($oldFullReal, $newRealPaths, true) &&
                            is_file($oldFullReal)
                        ) {
                            @unlink($oldFullReal);
                        }
                    }
                }

                $_SESSION['app_status_success'] = 'Your application has been updated and resubmitted for administrative review.';
                header('Location: ' . baseUrl('ngo/application-status.php'));
                exit;
            } catch (Throwable $e) {
                mysqli_rollback($con);
                foreach ($newlyMovedFiles as $movedPath) {
                    if (is_file($movedPath)) {
                        @unlink($movedPath);
                    }
                }
                $formErrors[] = $e->getMessage();
            }
        }
    }
}

$flashSuccess = $_SESSION['app_status_success'] ?? '';
$flashError = $_SESSION['app_status_error'] ?? '';
unset($_SESSION['app_status_success'], $_SESSION['app_status_error']);

$csrfToken = getCsrfToken();

$applicationIdLabel = 'NGO-' . str_pad((string) $ngoId, 3, '0', STR_PAD_LEFT);
$applicationDateLabel = !empty($ngo['created_at']) ? date('d F Y', strtotime($ngo['created_at'])) : 'N/A';
$establishedDateLabel = !empty($ngo['established_date']) ? date('d F Y', strtotime($ngo['established_date'])) : 'Not provided';
$ngoTypeLabel = $ngoTypeLabels[$ngo['ngo_type'] ?? 'other'] ?? 'Other';

$fullAddressParts = array_filter([
    $ngo['address'] ?? '',
    trim(($ngo['city'] ?? '') . (!empty($ngo['state']) ? ', ' . $ngo['state'] : ''))
]);
$fullAddressText = implode(', ', $fullAddressParts);
if (!empty($ngo['pincode'])) {
    $fullAddressText .= ($fullAddressText !== '' ? ' - ' : '') . $ngo['pincode'];
}

$websiteUrl = trim((string) ($ngo['website'] ?? ''));
$websiteScheme = strtolower((string) parse_url($websiteUrl, PHP_URL_SCHEME));
$isSafeWebsiteUrl = ($websiteUrl !== '' && filter_var($websiteUrl, FILTER_VALIDATE_URL) && in_array($websiteScheme, ['http', 'https'], true));

$causeOptionsList = $primaryCauseOptions;
if ($formValues['primary_cause'] !== '' && !in_array($formValues['primary_cause'], $causeOptionsList, true)) {
    $causeOptionsList[] = $formValues['primary_cause'];
}

$serviceAreaChoices = $availableServiceAreas;
foreach ($formValues['service_areas'] as $existingAreaOption) {
    if (!in_array($existingAreaOption, $serviceAreaChoices, true)) {
        $serviceAreaChoices[] = $existingAreaOption;
    }
}

ob_start();
?>

<div class="min-h-screen bg-ivory py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-12">

        <div class="text-center">
            <h1 class="text-3xl md:text-4xl md:text-5xl font-serif font-bold text-forest mb-4">NGO Application Status</h1>
            <p class="text-charcoal-light text-lg max-w-2xl mx-auto">Track the status of your NGO registration and verification application.</p>
        </div>

        <?php if ($flashSuccess !== ''): ?>
            <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl shadow-sm">
                <p class="text-sm font-medium"><?= e($flashSuccess) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($flashError !== ''): ?>
            <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-2xl shadow-sm">
                <p class="text-sm font-medium"><?= e($flashError) ?></p>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-3xl shadow-sm border border-gold/20 overflow-hidden relative">
            <?php if ($status === 'pending'): ?>
                <div class="absolute top-0 left-0 w-full h-2 bg-yellow-400"></div>
            <?php elseif ($status === 'approved'): ?>
                <div class="absolute top-0 left-0 w-full h-2 bg-forest"></div>
            <?php elseif ($status === 'rejected'): ?>
                <div class="absolute top-0 left-0 w-full h-2 bg-red-500"></div>
            <?php endif; ?>

            <div class="p-4 md:p-6 md:p-10 border-b border-charcoal/10">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 md:p-6 text-center md:text-left">
                    <div>
                        <?php if ($status === 'pending'): ?>
                            <h2 class="text-xl md:text-3xl font-serif font-bold text-forest mb-2">Pending Review</h2>
                            <p class="text-charcoal-light text-sm max-w-xl">Your application has been submitted successfully and is currently being reviewed by the KarmaSetu administration team.</p>
                        <?php elseif ($status === 'approved'): ?>
                            <h2 class="text-xl md:text-3xl font-serif font-bold text-forest mb-2">Application Approved</h2>
                            <p class="text-charcoal-light text-sm max-w-xl">Congratulations! Your NGO has been verified. You can now access your NGO dashboard.</p>
                        <?php elseif ($status === 'rejected'): ?>
                            <h2 class="text-xl md:text-3xl font-serif font-bold text-red-600 mb-2">Application Requires Changes</h2>
                            <p class="text-charcoal-light text-sm max-w-xl">Please review the feedback below and update your application.</p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <?php if ($status === 'pending'): ?>
                            <div class="inline-flex flex-wrap items-center gap-2 bg-yellow-50 px-5 py-2.5 rounded-full border border-yellow-200">
                                <div class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></div>
                                <span class="text-sm font-bold text-yellow-700 uppercase tracking-wider">Pending Review</span>
                            </div>
                        <?php elseif ($status === 'approved'): ?>
                            <a href="<?= baseUrl('ngo/dashboard.php') ?>" class="inline-block bg-forest text-white px-8 py-3 rounded-full font-bold shadow-md hover:shadow-lg transition-all hover:bg-forest-dark">Go to Dashboard</a>
                        <?php elseif ($status === 'rejected'): ?>
                            <a href="<?= baseUrl('ngo/application-status.php?edit=1#update-application') ?>" class="inline-block bg-red-600 text-white px-8 py-3 rounded-full font-bold shadow-md hover:shadow-lg transition-all hover:bg-red-700">Update Application</a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($status === 'rejected'): ?>
                    <div class="mt-8 bg-red-50 p-4 md:p-6 rounded-xl border border-red-100">
                        <h3 class="text-red-800 font-bold mb-2">Reviewer Feedback:</h3>
                        <?php if (!empty($ngo['rejection_reason'])): ?>
                            <p class="text-red-700 text-sm leading-relaxed"><?= nl2br(e($ngo['rejection_reason'])) ?></p>
                        <?php else: ?>
                            <p class="text-red-700 text-sm">Your application requires updates before approval. Please review your details and documents below.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="p-4 md:p-6 md:p-10 bg-ivory/30">
                <h3 class="text-sm font-bold text-charcoal uppercase tracking-wider mb-6">Application Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-6 gap-x-8">
                    <div>
                        <p class="text-xs text-charcoal/60 mb-1">Organization</p>
                        <p class="text-sm font-bold text-forest"><?= e($ngo['name'] ?? '') ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-charcoal/60 mb-1">Application ID</p>
                        <p class="text-sm font-bold text-charcoal"><?= e($applicationIdLabel) ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-charcoal/60 mb-1">Application Date</p>
                        <p class="text-sm font-bold text-charcoal"><?= e($applicationDateLabel) ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-charcoal/60 mb-1">NGO Type</p>
                        <p class="text-sm font-medium text-charcoal"><?= e($ngoTypeLabel) ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-charcoal/60 mb-1">Primary Cause</p>
                        <p class="text-sm font-medium text-charcoal"><?= e($ngo['primary_cause'] ?? '') ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-charcoal/60 mb-1">Contact</p>
                        <p class="text-sm font-medium text-charcoal">
                            <?= e($ngo['contact_person'] ?? '') ?><br>
                            <span class="text-xs"><?= e($ngo['official_email'] ?? '') ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gold/20 p-4 md:p-6 md:p-10">
            <h3 class="text-sm font-bold text-charcoal uppercase tracking-wider mb-8 text-center md:text-left">Application Progress</h3>

            <div class="relative">
                <div class="hidden md:block absolute top-4 md:p-6 left-12 right-12 h-1 bg-charcoal/10 -z-10"></div>
                <div class="md:hidden absolute top-4 md:p-6 bottom-6 left-6 w-1 bg-charcoal/10 -z-10"></div>

                <div class="flex flex-col md:flex-row justify-between space-y-8 md:space-y-0">

                    <div class="flex md:flex-col items-center md:text-center gap-4 md:gap-3 relative z-10 w-full md:w-1/4">
                        <div class="w-12 h-12 rounded-full bg-forest text-white flex items-center justify-center shadow-md flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-forest">Application Submitted</p>
                        </div>
                    </div>

                    <div class="flex md:flex-col items-center md:text-center gap-4 md:gap-3 relative z-10 w-full md:w-1/4">
                        <div class="w-12 h-12 rounded-full <?= !empty($documents) ? 'bg-forest text-white' : 'bg-yellow-400 text-white' ?> flex items-center justify-center shadow-md flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold <?= !empty($documents) ? 'text-forest' : 'text-yellow-600' ?>">Documents Submitted</p>
                        </div>
                    </div>

                    <div class="flex md:flex-col items-center md:text-center gap-4 md:gap-3 relative z-10 w-full md:w-1/4">
                        <?php if ($status === 'pending'): ?>
                            <div class="w-12 h-12 rounded-full bg-yellow-400 text-white flex items-center justify-center shadow-md flex-shrink-0 border-4 border-white">
                                <div class="w-3 h-3 bg-white rounded-full"></div>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-yellow-600">Under Review</p>
                            </div>
                        <?php else: ?>
                            <div class="w-12 h-12 rounded-full <?= $status === 'rejected' ? 'bg-red-500' : 'bg-forest' ?> text-white flex items-center justify-center shadow-md flex-shrink-0">
                                <?php if ($status === 'rejected'): ?>
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                <?php else: ?>
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <?php endif; ?>
                            </div>
                            <div>
                                <p class="text-sm font-bold <?= $status === 'rejected' ? 'text-red-600' : 'text-forest' ?>">
                                    <?= $status === 'rejected' ? 'Changes Requested' : 'Review Complete' ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex md:flex-col items-center md:text-center gap-4 md:gap-3 relative z-10 w-full md:w-1/4">
                        <?php if ($status === 'approved'): ?>
                            <div class="w-12 h-12 rounded-full bg-forest text-white flex items-center justify-center shadow-md flex-shrink-0 border-4 border-white">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-forest">Approved</p>
                            </div>
                        <?php else: ?>
                            <div class="w-12 h-12 rounded-full bg-white border-2 border-charcoal/20 text-charcoal/40 flex items-center justify-center flex-shrink-0">
                            </div>
                            <div>
                                <p class="text-sm font-bold text-charcoal/40">Approved</p>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        <div class="flex flex-col lg:grid lg:grid-cols-3 gap-8 w-full">
            <div class="lg:col-span-2 space-y-8">

                <div class="bg-white rounded-3xl shadow-sm border border-gold/20 p-4 md:p-6 md:p-10">
                    <h3 class="text-xl font-serif font-bold text-forest mb-6">Application Information</h3>

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:p-6 pb-6 border-b border-charcoal/10">
                            <div>
                                <p class="text-xs text-charcoal/60 mb-1">Registration Number</p>
                                <p class="text-sm font-medium text-charcoal"><?= e($ngo['registration_number'] ?? '') ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-charcoal/60 mb-1">Established Date</p>
                                <p class="text-sm font-medium text-charcoal"><?= e($establishedDateLabel) ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-charcoal/60 mb-1">Contact Person</p>
                                <p class="text-sm font-medium text-charcoal">
                                    <?= e($ngo['contact_person'] ?? '') ?>
                                    <?php if (!empty($ngo['contact_designation'])): ?>
                                        (<?= e($ngo['contact_designation']) ?>)
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-charcoal/60 mb-1">Official Email</p>
                                <p class="text-sm font-medium text-charcoal break-words"><?= e($ngo['official_email'] ?? '') ?></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:p-6 pb-6 border-b border-charcoal/10">
                            <div>
                                <p class="text-xs text-charcoal/60 mb-1">Phone</p>
                                <p class="text-sm font-medium text-charcoal"><?= e($ngo['phone'] ?? '') ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-charcoal/60 mb-1">Website</p>
                                <?php if ($isSafeWebsiteUrl): ?>
                                    <a href="<?= e($websiteUrl) ?>" target="_blank" rel="noopener noreferrer" class="text-sm font-medium text-forest hover:text-gold break-words"><?= e($websiteUrl) ?></a>
                                <?php else: ?>
                                    <p class="text-sm font-medium text-charcoal/60">Not provided</p>
                                <?php endif; ?>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-xs text-charcoal/60 mb-1">Address</p>
                                <p class="text-sm font-medium text-charcoal"><?= e($fullAddressText !== '' ? $fullAddressText : 'Not provided') ?></p>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-charcoal/60 mb-1">Organization Description</p>
                            <p class="text-sm font-medium text-charcoal leading-relaxed"><?= nl2br(e($ngo['description'] ?? '')) ?></p>
                        </div>

                        <div>
                            <p class="text-xs text-charcoal/60 mb-1">Mission</p>
                            <p class="text-sm font-medium text-charcoal leading-relaxed"><?= nl2br(e($ngo['mission'] ?? '')) ?></p>
                        </div>

                        <div>
                            <p class="text-xs text-charcoal/60 mb-1">Impact Description</p>
                            <p class="text-sm font-medium text-charcoal leading-relaxed">
                                <?= !empty($ngo['impact_summary']) ? nl2br(e($ngo['impact_summary'])) : 'Not provided' ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-charcoal/60 mb-2">Service Areas</p>
                            <?php if (!empty($serviceAreas)): ?>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($serviceAreas as $areaItem): ?>
                                        <span class="inline-block bg-ivory border border-gold/30 text-charcoal text-xs px-3 py-1.5 rounded-full"><?= e($areaItem) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-sm text-charcoal/60">No service areas listed.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-gold/20 p-4 md:p-6 md:p-10">
                    <h3 class="text-xl font-serif font-bold text-forest mb-6">Verification Documents</h3>

                    <?php if (!empty($documents)): ?>
                        <div class="space-y-4">
                            <?php foreach ($documents as $document): ?>
                                <?php
                                $docTypeKey = $document['document_type'] ?? '';
                                $docLabel = $documentTypeLabels[$docTypeKey] ?? 'Verification Document';
                                $docPath = ltrim((string) ($document['file_path'] ?? ''), '/');
                                ?>
                                <div class="flex flex-wrap items-center justify-between gap-4 p-4 bg-ivory/50 border border-charcoal/10 rounded-xl">
                                    <div class="flex items-center space-x-3 min-w-0">
                                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-forest shadow-sm border border-charcoal/5 flex-shrink-0">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-charcoal"><?= e($docLabel) ?></p>
                                            <p class="text-xs text-charcoal/60 break-words"><?= e($document['file_name'] ?? '') ?></p>
                                            <?php if (!empty($document['created_at'])): ?>
                                                <p class="text-xs text-charcoal/50 mt-0.5">Uploaded <?= e(date('d M Y', strtotime($document['created_at']))) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-bold text-forest bg-forest/10 px-3 py-1 rounded-full uppercase tracking-wider">Submitted</span>
                                        <?php if ($docPath !== ''): ?>
                                            <a href="<?= e(baseUrl($docPath)) ?>" target="_blank" rel="noopener noreferrer" class="text-xs font-bold text-forest hover:text-gold underline whitespace-nowrap">View Document</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-charcoal/60">No verification documents found.</p>
                    <?php endif; ?>
                </div>

            </div>

            <div class="space-y-8">
                <?php if ($status === 'pending'): ?>
                    <div class="bg-blue-50/50 rounded-3xl border border-blue-100 p-4 md:p-8">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-blue-900 font-bold mb-2">Under Review</h3>
                                <p class="text-sm text-blue-800/80 leading-relaxed">Our team is reviewing the information and documents submitted with your application. Check back here for updates.</p>
                            </div>
                        </div>
                    </div>
                <?php elseif ($status === 'rejected'): ?>
                    <div class="bg-red-50 rounded-3xl border border-red-100 p-4 md:p-8">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-red-900 font-bold mb-2">Action Required</h3>
                                <p class="text-sm text-red-800/80 leading-relaxed mb-4">Your application needs corrections before it can be approved. Update your details or replace documents below and resubmit.</p>
                                <a href="<?= baseUrl('ngo/application-status.php?edit=1#update-application') ?>" class="inline-block text-xs font-bold text-red-700 hover:text-red-900 underline">Edit &amp; Resubmit Application &rarr;</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-green-50 rounded-3xl border border-green-200 p-4 md:p-8">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-green-100 text-forest rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-forest font-bold mb-2">Verified &amp; Active</h3>
                                <p class="text-sm text-charcoal-light leading-relaxed mb-3">
                                    Your NGO profile is verified and active.
                                    <?php if (!empty($ngo['verified_at'])): ?>
                                        Approved on <?= e(date('d M Y', strtotime($ngo['verified_at']))) ?>.
                                    <?php endif; ?>
                                </p>
                                <a href="<?= baseUrl('ngo/dashboard.php') ?>" class="inline-block text-xs font-bold text-forest hover:text-gold underline">Open NGO Dashboard &rarr;</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="bg-white rounded-3xl shadow-sm border border-gold/20 p-4 md:p-8">
                    <h3 class="text-lg font-serif font-bold text-forest mb-6">What Happens Next?</h3>

                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-full bg-ivory border border-gold/30 flex items-center justify-center text-sm font-bold text-forest flex-shrink-0">1</div>
                            <div>
                                <p class="text-sm font-bold text-charcoal mb-1">Application Review</p>
                                <p class="text-xs text-charcoal-light leading-relaxed">Our team verifies your organization details and documents.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-full bg-ivory border border-gold/30 flex items-center justify-center text-sm font-bold text-forest flex-shrink-0">2</div>
                            <div>
                                <p class="text-sm font-bold text-charcoal mb-1">Approval Decision</p>
                                <p class="text-xs text-charcoal-light leading-relaxed">The administration team approves or requests changes after review.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-full bg-ivory border border-gold/30 flex items-center justify-center text-sm font-bold text-forest flex-shrink-0">3</div>
                            <div>
                                <p class="text-sm font-bold text-charcoal mb-1">NGO Profile Activation</p>
                                <p class="text-xs text-charcoal-light leading-relaxed">Once approved, your NGO profile and fundraising features become available on KarmaSetu.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-forest text-white rounded-3xl shadow-sm p-4 md:p-8 text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-6 -mr-6 w-24 h-24 bg-white/10 rounded-full blur-xl z-0 pointer-events-none"></div>
                    <div class="relative z-10">
                        <h3 class="text-lg font-serif font-bold mb-3">Need Help?</h3>
                        <p class="text-sm text-ivory/80 mb-6 leading-relaxed">If you have questions about your application, you can review KarmaSetu guidelines or contact support.</p>
                        <a href="<?= baseUrl('?page=about') ?>" class="inline-block bg-white text-forest hover:bg-ivory px-6 py-3 rounded-full text-sm font-bold transition-all shadow-sm w-full">About KarmaSetu</a>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($status === 'rejected' && $showEditForm): ?>
            <div id="update-application" class="bg-white rounded-3xl shadow-sm border border-gold/20 overflow-hidden">
                <div class="p-6 md:p-10 border-b border-charcoal/10 bg-ivory/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-serif font-bold text-forest">Update &amp; Resubmit Application</h2>
                        <p class="text-sm text-charcoal-light mt-1">Correct the requested details or replace documents below, then resubmit for review.</p>
                    </div>
                    <a href="<?= baseUrl('ngo/application-status.php') ?>" class="text-sm font-bold text-charcoal hover:text-forest">Cancel</a>
                </div>

                <?php if (!empty($formErrors)): ?>
                    <div class="mx-6 md:mx-10 mt-6 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl">
                        <p class="text-sm font-bold mb-2">Please fix the following errors:</p>
                        <ul class="list-disc list-inside text-sm space-y-1">
                            <?php foreach ($formErrors as $errItem): ?>
                                <li><?= e($errItem) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= baseUrl('ngo/application-status.php?edit=1#update-application') ?>" method="POST" enctype="multipart/form-data" class="p-6 md:p-10 space-y-10 validation-form" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                    <section>
                        <div class="border-b border-charcoal/10 pb-4 mb-6">
                            <h3 class="text-xl font-serif font-bold text-forest">Organization Details</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="ngo_name" class="block text-sm font-bold text-charcoal mb-2">NGO / Organization Name *</label>
                                <input type="text" id="ngo_name" name="ngo_name" required data-no-name-val="true" data-validate="required" value="<?= e($formValues['ngo_name']) ?>" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold bg-ivory/30 focus:bg-white text-charcoal">
                            </div>

                            <div>
                                <label for="registration_number" class="block text-sm font-bold text-charcoal mb-2">Registration Number *</label>
                                <input type="text" id="registration_number" name="registration_number" required data-no-name-val="true" data-validate="required" value="<?= e($formValues['registration_number']) ?>" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold bg-ivory/30 focus:bg-white text-charcoal">
                            </div>

                            <div>
                                <label for="established_date" class="block text-sm font-bold text-charcoal mb-2">Established Date *</label>
                                <input type="date" id="established_date" name="established_date" required data-validate="required" value="<?= e($formValues['established_date']) ?>" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold bg-ivory/30 focus:bg-white text-charcoal">
                            </div>

                            <div>
                                <label for="ngo_type" class="block text-sm font-bold text-charcoal mb-2">NGO Type *</label>
                                <select id="ngo_type" name="ngo_type" required data-validate="required" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold bg-ivory/30 focus:bg-white text-charcoal">
                                    <option value="" disabled <?= $formValues['ngo_type'] === '' ? 'selected' : '' ?>>Select NGO Type</option>
                                    <?php foreach ($ngoTypeLabels as $typeVal => $typeText): ?>
                                        <option value="<?= e($typeVal) ?>" <?= $formValues['ngo_type'] === $typeVal ? 'selected' : '' ?>><?= e($typeText) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label for="primary_cause" class="block text-sm font-bold text-charcoal mb-2">Primary Cause *</label>
                                <select id="primary_cause" name="primary_cause" required data-validate="required" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold bg-ivory/30 focus:bg-white text-charcoal">
                                    <option value="" disabled <?= $formValues['primary_cause'] === '' ? 'selected' : '' ?>>Select Primary Cause</option>
                                    <?php foreach ($causeOptionsList as $causeOpt): ?>
                                        <option value="<?= e($causeOpt) ?>" <?= $formValues['primary_cause'] === $causeOpt ? 'selected' : '' ?>><?= e($causeOpt) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-bold text-charcoal mb-2">Organization Description *</label>
                                <textarea id="description" name="description" rows="4" required maxlength="500" data-validate="required" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold bg-ivory/30 focus:bg-white text-charcoal resize-none"><?= e($formValues['description']) ?></textarea>
                            </div>
                        </div>
                    </section>

                    <section>
                        <div class="border-b border-charcoal/10 pb-4 mb-6">
                            <h3 class="text-xl font-serif font-bold text-forest">Contact Information</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="official_email" class="block text-sm font-bold text-charcoal mb-2">Official Email Address *</label>
                                <input type="email" id="official_email" name="official_email" required data-validate="required email" value="<?= e($formValues['official_email']) ?>" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold bg-ivory/30 focus:bg-white text-charcoal">
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-bold text-charcoal mb-2">Official Phone Number *</label>
                                <input type="tel" id="phone" name="phone" required data-validate="required numeric" value="<?= e($formValues['phone']) ?>" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold bg-ivory/30 focus:bg-white text-charcoal">
                            </div>

                            <div class="md:col-span-2">
                                <label for="website" class="block text-sm font-bold text-charcoal mb-2">Website</label>
                                <input type="url" id="website" name="website" value="<?= e($formValues['website']) ?>" placeholder="https://www.yourngo.org" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold bg-ivory/30 focus:bg-white text-charcoal">
                            </div>

                            <div>
                                <label for="contact_person" class="block text-sm font-bold text-charcoal mb-2">Contact Person Name *</label>
                                <input type="text" id="contact_person" name="contact_person" required data-validate="required alpha" value="<?= e($formValues['contact_person']) ?>" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold bg-ivory/30 focus:bg-white text-charcoal">
                            </div>

                            <div>
                                <label for="designation" class="block text-sm font-bold text-charcoal mb-2">Contact Person Designation *</label>
                                <input type="text" id="designation" name="designation" required data-no-name-val="true" data-validate="required" value="<?= e($formValues['designation']) ?>" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold bg-ivory/30 focus:bg-white text-charcoal">
                            </div>
                        </div>
                    </section>

                    <section>
                        <div class="border-b border-charcoal/10 pb-4 mb-6">
                            <h3 class="text-xl font-serif font-bold text-forest">Organization Address</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-bold text-charcoal mb-2">Address *</label>
                                <textarea id="address" name="address" rows="2" required data-validate="required" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold bg-ivory/30 focus:bg-white text-charcoal resize-none"><?= e($formValues['address']) ?></textarea>
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-bold text-charcoal mb-2">City *</label>
                                <input type="text" id="city" name="city" required data-validate="required alpha" value="<?= e($formValues['city']) ?>" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold bg-ivory/30 focus:bg-white text-charcoal">
                            </div>

                            <div>
                                <label for="state" class="block text-sm font-bold text-charcoal mb-2">State *</label>
                                <input type="text" id="state" name="state" required data-validate="required alpha" value="<?= e($formValues['state']) ?>" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold bg-ivory/30 focus:bg-white text-charcoal">
                            </div>

                            <div>
                                <label for="pincode" class="block text-sm font-bold text-charcoal mb-2">Pincode *</label>
                                <input type="text" id="pincode" name="pincode" required data-no-name-val="true" data-validate="required numeric" value="<?= e($formValues['pincode']) ?>" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold bg-ivory/30 focus:bg-white text-charcoal">
                            </div>
                        </div>
                    </section>

                    <section>
                        <div class="border-b border-charcoal/10 pb-4 mb-6">
                            <h3 class="text-xl font-serif font-bold text-forest">Verification Documents</h3>
                            <p class="text-xs text-charcoal-light mt-1">Existing uploaded documents will remain unchanged unless you upload a replacement file (PDF, JPG, JPEG, PNG up to 5 MB).</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php
                            $uploadSlots = [
                                'registration_certificate' => 'NGO Registration Certificate',
                                'tax_document' => 'PAN / Tax Document',
                                'address_proof' => 'Address Proof',
                                'supporting_document' => 'Supporting Document (Optional)'
                            ];
                            foreach ($uploadSlots as $slotInput => $slotTitle):
                                $existingSlotDoc = $docsBySlot[$slotInput] ?? null;
                                $existingSlotPath = $existingSlotDoc ? ltrim((string) ($existingSlotDoc['file_path'] ?? ''), '/') : '';
                            ?>
                                <div class="bg-ivory/30 p-5 rounded-xl border border-charcoal/10">
                                    <label for="<?= e($slotInput) ?>" class="block text-sm font-bold text-charcoal mb-2"><?= e($slotTitle) ?></label>
                                    <?php if ($existingSlotDoc): ?>
                                        <div class="mb-3 text-sm text-forest">
                                            <p class="font-medium break-words">Current: <?= e($existingSlotDoc['file_name'] ?? '') ?></p>
                                            <?php if ($existingSlotPath !== ''): ?>
                                                <a href="<?= e(baseUrl($existingSlotPath)) ?>" target="_blank" rel="noopener noreferrer" class="inline-block mt-1 text-xs font-bold text-gold hover:text-forest underline">View Current Document</a>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="mb-3 text-xs text-charcoal-light">No document currently on file.</p>
                                    <?php endif; ?>
                                    <input type="file" id="<?= e($slotInput) ?>" name="<?= e($slotInput) ?>" accept=".pdf,.jpg,.jpeg,.png" data-validate="file" data-filetypes="pdf,jpg,jpeg,png" data-filesize="5120" class="block w-full text-sm text-charcoal-light file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-gold/10 file:text-forest hover:file:bg-gold/20 transition-colors">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <section>
                        <div class="border-b border-charcoal/10 pb-4 mb-6">
                            <h3 class="text-xl font-serif font-bold text-forest">Mission, Service Areas &amp; Impact</h3>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <label for="mission" class="block text-sm font-bold text-charcoal mb-2">Mission Statement *</label>
                                <textarea id="mission" name="mission" rows="3" required data-validate="required" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold bg-ivory/30 focus:bg-white text-charcoal resize-none"><?= e($formValues['mission']) ?></textarea>
                            </div>

                            <div>
                                <span class="block text-sm font-bold text-charcoal mb-3">Areas You Serve *</span>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    <?php foreach ($serviceAreaChoices as $areaIdx => $areaChoice): ?>
                                        <label class="flex items-center space-x-3 p-3 border border-charcoal/10 rounded-xl cursor-pointer hover:bg-ivory/50 transition-colors">
                                            <input type="checkbox" name="service_areas[]" value="<?= e($areaChoice) ?>" <?= in_array($areaChoice, $formValues['service_areas'], true) ? 'checked' : '' ?> <?= $areaIdx === 0 ? 'data-validate="required min-items" data-min-items="1"' : '' ?> class="h-4 w-4 text-forest focus:ring-gold border-charcoal/30 rounded accent-forest">
                                            <span class="text-sm text-charcoal font-medium"><?= e($areaChoice) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div>
                                <label for="impact" class="block text-sm font-bold text-charcoal mb-2">Brief Impact / Achievements *</label>
                                <textarea id="impact" name="impact" rows="3" required data-validate="required" class="w-full px-4 py-3 border border-charcoal/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold bg-ivory/30 focus:bg-white text-charcoal resize-none"><?= e($formValues['impact']) ?></textarea>
                            </div>
                        </div>
                    </section>

                    <section class="pt-4 border-t border-charcoal/10">
                        <label class="flex items-start space-x-3 cursor-pointer">
                            <input type="checkbox" id="confirm_info" name="confirm_info" required data-validate="required" class="h-5 w-5 text-forest focus:ring-gold border-charcoal/30 rounded accent-forest mt-0.5">
                            <span class="text-sm text-charcoal font-medium leading-relaxed">I confirm that the updated information and documents are accurate and complete for re-verification. *</span>
                        </label>
                    </section>

                    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-4 pt-6 border-t border-gold/20">
                        <a href="<?= baseUrl('ngo/application-status.php') ?>" class="w-full sm:w-auto px-6 py-3 text-sm font-bold text-charcoal bg-ivory border border-charcoal/20 hover:bg-charcoal/5 rounded-full text-center transition-colors">Cancel</a>
                        <button type="submit" name="resubmit_application" value="1" class="w-full sm:w-auto px-10 py-3 text-sm font-bold text-white bg-forest hover:bg-forest-dark shadow-md hover:shadow-lg rounded-full transition-all">Resubmit Application</button>
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
