<?php
/**
 * Student Hub - Database Connection & Helper Utilities
 * CSPIT, CHARUSAT - Web Technology Project
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'studenthub');

/**
 * Get or initialize MySQL connection
 */
function getDbConnection() {
    static $conn = null;
    if ($conn !== null && @mysqli_ping($conn)) {
        return $conn;
    }

    // Attempt direct connection to database
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // If database doesn't exist, connect to server and initialize database
    if ($conn->connect_error) {
        $serverConn = @new mysqli(DB_HOST, DB_USER, DB_PASS);
        if ($serverConn->connect_error) {
            die("Database server connection failed: " . $serverConn->connect_error);
        }

        // Create database if not exists
        $serverConn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $serverConn->select_db(DB_NAME);

        // Run tables setup if database.sql exists
        $sqlPath = __DIR__ . '/database.sql';
        if (file_exists($sqlPath)) {
            $sqlContent = file_get_contents($sqlPath);
            $queries = explode(';', $sqlContent);
            foreach ($queries as $q) {
                $trimmed = trim($q);
                if (!empty($trimmed)) {
                    @$serverConn->query($trimmed);
                }
            }
        }
        $conn = $serverConn;
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}

/**
 * Synchronize MySQL students table into Students.json file
 */
function syncStudentsJson($conn = null) {
    if (!$conn) {
        $conn = getDbConnection();
    }
    $query = "SELECT roll_no, name, email, course, semester, '2nd Year' AS year, IFNULL(address, 'Gujarat') AS city, sgpa FROM students ORDER BY roll_no ASC";
    $result = $conn->query($query);
    if ($result) {
        $studentsList = [];
        while ($row = $result->fetch_assoc()) {
            $studentsList[] = [
                'roll_no'  => $row['roll_no'],
                'name'     => $row['name'],
                'email'    => $row['email'],
                'course'   => $row['course'],
                'semester' => (int)$row['semester'],
                'year'     => $row['year'],
                'city'     => $row['city'],
                'sgpa'     => (float)$row['sgpa']
            ];
        }
        $jsonPath = __DIR__ . '/Students.json';
        @file_put_contents($jsonPath, json_encode($studentsList, JSON_PRETTY_PRINT));
    }
}

