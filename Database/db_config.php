
<?php

try {

    $con = mysqli_connect("localhost", "root", "");

    if (!$con) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }

    $sql = "CREATE DATABASE IF NOT EXISTS karmasetu";

    if (mysqli_query($con, $sql)) {

        mysqli_select_db($con, "karmasetu");

    } else {
        throw new Exception("Database creation failed: " . mysqli_error($con));
    }

    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fullname VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        phone VARCHAR(15) NOT NULL,
        password VARCHAR(255) NOT NULL,
        date_of_birth DATE NULL,
        city VARCHAR(100) NULL,
        state VARCHAR(100) NULL,
        gender VARCHAR(20) NULL,
        address TEXT NULL,
        profile_photo VARCHAR(255) NULL,
        role ENUM('user', 'ngo', 'admin') NOT NULL DEFAULT 'user',
        status ENUM('active', 'inactive', 'pending', 'blocked')
            NOT NULL DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if (!mysqli_query($con, $sql)) {
        throw new Exception("Table creation failed: " . mysqli_error($con));
    }

} catch (Exception $e) {

    echo $e->getMessage();

}

?>
