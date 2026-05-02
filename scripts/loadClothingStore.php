<?php
// Script to load complete database
// Student: Vutivi & Karabo

$conn = require_once 'DBConn.php';

// Read and execute SQL file
$sql_file = __DIR__ . '/myClothingStore.sql';
$sql_content = file_get_contents($sql_file);

// Split queries by semicolon
$queries = explode(';', $sql_content);

foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        if (mysqli_query($conn, $query)) {
            echo "Query executed successfully.<br>";
        } else {
            echo "Error: " . mysqli_error($conn) . "<br>";
        }
    }
}

echo "<h2>Database loaded successfully!</h2>";
echo "<a href='../index.php'>Go to Homepage</a><br>";
echo "<a href='../admin/index.php'>Go to Admin Panel</a>";
?>