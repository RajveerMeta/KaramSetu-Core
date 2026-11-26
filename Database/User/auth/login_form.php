<?php
require_once __DIR__ . "/../../../includes/functions.php";
require_once __DIR__ . "/../../db_config.php";

if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST" && isset($_POST["login"])) {
    if (!verifyCsrfToken($_POST["csrf_token"] ?? "")) {
        $_SESSION["login_error"] = "Invalid security token. Please refresh and try again.";
        header("Location: " . baseUrl("auth/login.php"));
        exit();
    }

    $email = trim((string) ($_POST["email"] ?? ""));
    $password = (string) ($_POST["password"] ?? "");

    if ($email === "" || $password === "") {
        $_SESSION["login_error"] = "Please fill in all fields.";
        header("Location: " . baseUrl("auth/login.php"));
        exit();
    }

    $stmt = mysqli_prepare($con, "SELECT id, role, name, email, password_hash, status FROM users WHERE LOWER(email) = LOWER(?) LIMIT 1");
    if (!$stmt) {
        $_SESSION["login_error"] = "Unable to process login right now. Please try again.";
        header("Location: " . baseUrl("auth/login.php"));
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result ? mysqli_fetch_assoc($result) : null;
    mysqli_stmt_close($stmt);

    if (!$row || !hash_equals((string) ($row["password_hash"] ?? ""), $password)) {
        $_SESSION["login_error"] = "Invalid email or password!";
        header("Location: " . baseUrl("auth/login.php"));
        exit();
    }

    $status = strtolower(trim((string) ($row["status"] ?? "")));
    $role = strtolower(trim((string) ($row["role"] ?? "")));

    if ($role === "ngo") {
        $ngo = getNgoRecordForUser((int) $row["id"]);
        if (!$ngo) {
            $_SESSION["login_error"] = "NGO application record not found. Please contact support.";
            header("Location: " . baseUrl("auth/login.php"));
            exit();
        }

        $ngoStatus = strtolower(trim((string) ($ngo["verification_status"] ?? "")));
        if ($status === "suspended" || $ngoStatus === "suspended") {
            $_SESSION["login_error"] = "Account is suspended!";
            header("Location: " . baseUrl("auth/login.php"));
            exit();
        }

        if ($status === "active" && $ngoStatus === "approved") {
            session_regenerate_id(true);
            $_SESSION["user_id"] = (int) $row["id"];
            $_SESSION["user_name"] = $row["name"] ?? $row["fullname"] ?? "";
            $_SESSION["user_email"] = $row["email"];
            $_SESSION["user_role"] = $row["role"];
            header("Location: " . baseUrl("ngo/dashboard.php"));
            exit();
        }

        if (in_array($status, ["pending", "active"], true) && in_array($ngoStatus, ["pending", "rejected"], true)) {
            session_regenerate_id(true);
            $_SESSION["user_id"] = (int) $row["id"];
            $_SESSION["user_name"] = $row["name"] ?? $row["fullname"] ?? "";
            $_SESSION["user_email"] = $row["email"];
            $_SESSION["user_role"] = $row["role"];
            header("Location: " . baseUrl("ngo/application-status.php"));
            exit();
        }

        $_SESSION["login_error"] = "Account is not active!";
        header("Location: " . baseUrl("auth/login.php"));
        exit();
    }

    if ($status === "active") {
        session_regenerate_id(true);
        $_SESSION["user_id"] = (int) $row["id"];
        $_SESSION["user_name"] = $row["name"] ?? $row["fullname"] ?? "";
        $_SESSION["user_email"] = $row["email"];
        $_SESSION["user_role"] = $row["role"];

        $page = $role === "admin" ? "admin/dashboard.php" : "user/dashboard.php";
        header("Location: " . baseUrl($page));
        exit();
    }

    if ($status === "suspended") {
        $_SESSION["login_error"] = "Account is suspended!";
    } elseif ($status === "pending") {
        $_SESSION["login_error"] = "Account is pending approval!";
    } else {
        $_SESSION["login_error"] = "Account is not active!";
    }

    header("Location: " . baseUrl("auth/login.php"));
    exit();
}

header("Location: " . baseUrl("auth/login.php"));
exit();
