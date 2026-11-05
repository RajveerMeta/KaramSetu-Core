
<?php
require_once __DIR__ . "/../../../includes/functions.php";
require_once __DIR__ . "/../../db_config.php";

if (isset($_POST["register"])) {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["password_confirmation"] ?? "";

    if ($name == "" || $email == "" || $phone == "" || $password == "") {
        exit("All fields are required.");
    }

    if ($password != $confirmPassword) {
        exit("Passwords do not match.");
    }

    if (!isset($_POST["terms"])) {
        exit("Please accept the Terms and Conditions.");
    }

    $sql = "SELECT id FROM users WHERE email = '$email'";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0) {
        exit("Email already registered.");
    }

    mysqli_begin_transaction($con);

    $sql = "INSERT INTO users (name, email, phone, password_hash, role, status)
            VALUES ('$name', '$email', '$phone', '$password', 'user', 'active')";

    if (mysqli_query($con, $sql)) {

        $userId = mysqli_insert_id($con);

        $sql = "INSERT INTO user_profiles (user_id) VALUES ('$userId')";

        if (mysqli_query($con, $sql)) {
            mysqli_commit($con);
            header("Location: " . baseUrl('auth/login.php'));
            exit();
        }
    }

    mysqli_rollback($con);
    exit("Registration failed.");

} else {
    header("Location: " . baseUrl('auth/register.php'));
    exit();
}
?>
