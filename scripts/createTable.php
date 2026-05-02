<?php
// Script to create and populate tblUser
// Student: Vutivi & Karabo

$conn = require_once 'DBConn.php';

function dropTable($conn, $tableName) {
    $sql = "DROP TABLE IF EXISTS `$tableName`";
    if (mysqli_query($conn, $sql)) {
        echo "Table `$tableName` dropped successfully.<br>";
        return true;
    } else {
        echo "Error dropping table: " . mysqli_error($conn) . "<br>";
        return false;
    }
}

function createUserTable($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS `tblUser` (
        `user_id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL,
        `surname` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) NOT NULL UNIQUE,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password_hash` VARCHAR(255) NOT NULL,
        `phone` VARCHAR(20),
        `delivery_address` TEXT,
        `is_verified` TINYINT(1) DEFAULT 0,
        `is_seller_verified` TINYINT(1) DEFAULT 0,
        `role` ENUM('user', 'admin') DEFAULT 'user',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `last_login` TIMESTAMP NULL,
        PRIMARY KEY (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if (mysqli_query($conn, $sql)) {
        echo "Table 'tblUser' created successfully.<br>";
        return true;
    } else {
        echo "Error creating table: " . mysqli_error($conn) . "<br>";
        return false;
    }
}

function loadUserData($conn, $filename) {
    $filepath = __DIR__ . '/../data/' . $filename;
    
    if (!file_exists($filepath)) {
        echo "Data file not found: $filepath<br>";
        return false;
    }
    
    $lines = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $count = 0;
    
    $stmt = mysqli_prepare($conn, "INSERT INTO `tblUser` (`name`, `surname`, `email`, `username`, `password_hash`, `is_verified`, `is_seller_verified`) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($lines as $line) {
        $data = explode('|', $line);
        if (count($data) >= 7) {
            mysqli_stmt_bind_param($stmt, "sssssii", $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6]);
            if (mysqli_stmt_execute($stmt)) {
                $count++;
            }
        }
    }
    
    mysqli_stmt_close($stmt);
    echo "Loaded $count records into tblUser.<br>";
    return true;
}

echo "<h2>Creating and Populating tblUser</h2>";
dropTable($conn, 'tblUser');
if (createUserTable($conn)) {
    loadUserData($conn, 'userData.txt');
}

echo "<br><a href='../index.php'>Return to Homepage</a>";
?>