
<?php
require_once __DIR__ . "/../../../includes/functions.php";
require_once __DIR__ . "/../../db_config.php";

if (isset($_POST["login"])) {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email == "" || $password == "") {
        $_SESSION["login_error"] = "Please fill in all fields.";
    } else {

        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($con, $sql);

        if ($result && mysqli_num_rows($result) > 0) {

            $row = mysqli_fetch_assoc($result);

            if ($password == $row["password_hash"]) {

                $status = strtolower(trim($row["status"] ?? ""));

                if ($status == "active") {

                    $_SESSION["user_id"] = $row["id"];
                    $_SESSION["user_name"] = $row["name"] ?? $row["fullname"] ?? "";
                    $_SESSION["user_email"] = $row["email"];
                    $_SESSION["user_role"] = $row["role"];

                    if ($row["role"] == "admin") {
                        $page = "admin/dashboard.php";
                    } elseif ($row["role"] == "ngo") {
                        $page = "ngo/dashboard.php";
                    } else {
                        $page = "user/dashboard.php";
                    }

                    header("Location: " . baseUrl($page));
                    exit();

                } elseif ($status == "suspended") {
                    $_SESSION["login_error"] = "Account is suspended!";
                } elseif ($status == "pending") {
                    $_SESSION["login_error"] = "Account is pending approval!";
                } else {
                    $_SESSION["login_error"] = "Account is not active!";
                }

            } else {
                $_SESSION["login_error"] = "Invalid password!";
            }

        } else {
            $_SESSION["login_error"] = "Email not registered!";
        }
    }

    header("Location: " . baseUrl("auth/login.php"));
    exit();

} else {
    header("Location: " . baseUrl("auth/login.php"));
    exit();
}
?>
