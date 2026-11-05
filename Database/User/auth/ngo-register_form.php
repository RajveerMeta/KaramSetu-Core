```php
<?php
require_once __DIR__ . "/../../../includes/functions.php";
require_once __DIR__ . "/../../db_config.php";

if (isset($_POST["register_ngo"])) {

    // Organization details
    $name = $_POST["ngo_name"];
    $registrationNumber = $_POST["registration_number"];
    $establishedDate = $_POST["established_date"];
    $ngoType = $_POST["ngo_type"];
    $primaryCause = $_POST["primary_cause"];
    $description = $_POST["description"];
    $mission = $_POST["mission"];
    $impact = $_POST["impact"];
    $website = $_POST["website"];

    // Account details
    $email = $_POST["official_email"];
    $phone = $_POST["phone"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    // Contact details
    $contactPerson = $_POST["contact_person"];
    $designation = $_POST["designation"];
    $address = $_POST["address"];
    $city = $_POST["city"];
    $state = $_POST["state"];
    $pincode = $_POST["pincode"];

    // Check required fields
    if (
        empty($name) || empty($registrationNumber) ||
        empty($establishedDate) || empty($ngoType) ||
        empty($primaryCause) || empty($description) ||
        empty($mission) || empty($impact) ||
        empty($email) || empty($phone) ||
        empty($password) || empty($confirmPassword) ||
        empty($contactPerson) || empty($designation) ||
        empty($address) || empty($city) ||
        empty($state) || empty($pincode)
    ) {
        exit("Please fill all required fields.");
    }

    if ($password != $confirmPassword) {
        exit("Passwords do not match.");
    }

    if (!isset($_POST["confirm_info"]) ||
        !isset($_POST["agree_guidelines"])) {
        exit("Please accept both declarations.");
    }

    if (empty($_POST["service_areas"])) {
        exit("Please select a service area.");
    }

    // Check email
    $sql = "SELECT id FROM users WHERE email = '$email'";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0) {
        exit("Email is already registered.");
    }

    // Insert user
    $sql = "INSERT INTO users
            (role, name, email, phone, password_hash, status)
            VALUES
            ('ngo', '$contactPerson', '$email', '$phone',
            '$password', 'pending')";

    if (mysqli_query($con, $sql)) {

        $userId = mysqli_insert_id($con);

        // Insert NGO details
        $sql = "INSERT INTO ngos
                (
                    user_id, name, registration_number,
                    established_date, ngo_type, primary_cause,
                    description, mission, impact_summary,
                    official_email, phone, website,
                    contact_person, contact_designation,
                    address, city, state, pincode,
                    verification_status
                )
                VALUES
                (
                    '$userId', '$name', '$registrationNumber',
                    '$establishedDate', '$ngoType', '$primaryCause',
                    '$description', '$mission', '$impact',
                    '$email', '$phone', '$website',
                    '$contactPerson', '$designation',
                    '$address', '$city', '$state', '$pincode',
                    'pending'
                )";

        if (mysqli_query($con, $sql)) {

            $ngoId = mysqli_insert_id($con);

            // Create upload folder
            $folder = __DIR__ . "/../../uploads/ngo_documents/";

            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }

            // Document fields
            $documents = [
                "registration_certificate" => "Registration Certificate",
                "tax_document" => "Tax Document",
                "address_proof" => "Address Proof",
                "supporting_document" => "Supporting Document"
            ];

            foreach ($documents as $field => $type) {

                if (!isset($_FILES[$field]) ||
                    $_FILES[$field]["error"] == UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                if ($_FILES[$field]["error"] == 0) {

                    $fileName = basename($_FILES[$field]["name"]);
                    $newFileName = uniqid() . "_" . $fileName;
                    $filePath = "uploads/ngo_documents/" . $newFileName;

                    if (move_uploaded_file(
                        $_FILES[$field]["tmp_name"],
                        $folder . $newFileName
                    )) {

                        $fileSize = $_FILES[$field]["size"];
                        $mimeType = $_FILES[$field]["type"];

                        $sql = "INSERT INTO ngo_documents
                                (
                                    ngo_id, document_type,
                                    document_number, file_path,
                                    file_name, file_size, mime_type
                                )
                                VALUES
                                (
                                    '$ngoId', '$type', '',
                                    '$filePath', '$newFileName',
                                    '$fileSize', '$mimeType'
                                )";

                        if (!mysqli_query($con, $sql)) {
                            exit("Document record failed: " . mysqli_error($con));
                        }

                    } else {
                        exit("Unable to upload document.");
                    }
                }
            }

            header("Location: " . baseUrl("auth/login.php"));
            exit();

        } else {
            exit("NGO registration failed: " . mysqli_error($con));
        }

    } else {
        exit("Account registration failed: " . mysqli_error($con));
    }

} else {
    exit("Invalid form submission.");
}
?>
```