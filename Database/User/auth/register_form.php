
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
        $_SESSION["register_error"] = "All fields are required.";
        header("Location: " . baseUrl('auth/register.php'));
        exit();
    }

    if ($password != $confirmPassword) {
        $_SESSION["register_error"] = "Passwords do not match.";
        header("Location: " . baseUrl('auth/register.php'));
        exit();
    }

    if (!isset($_POST["terms"])) {
        $_SESSION["register_error"] = "Please accept the Terms and Conditions.";
        header("Location: " . baseUrl('auth/register.php'));
        exit();
    }

    $sql = "SELECT id FROM users WHERE email = '$email'";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION["register_error"] = "Email already registered.";
        header("Location: " . baseUrl('auth/register.php'));
        exit();
    }

    mysqli_begin_transaction($con);

    $sql = "INSERT INTO users (name, email, phone, password_hash, role, status)
            VALUES ('$name', '$email', '$phone', '$password', 'user', 'active')";

    if (mysqli_query($con, $sql)) {

        $userId = mysqli_insert_id($con);

        $sql = "INSERT INTO user_profiles (user_id) VALUES ('$userId')";

        if (mysqli_query($con, $sql)) {
            mysqli_commit($con);
            $_SESSION["register_success"] = "Account created successfully. Please login.";
            header("Location: " . baseUrl('auth/login.php'));
            exit();
        }
    }

    mysqli_rollback($con);
    $_SESSION["register_error"] = "Registration failed.";
    header("Location: " . baseUrl('auth/register.php'));
    exit();

} else {
    header("Location: " . baseUrl('auth/register.php'));
    exit();
}
?>
