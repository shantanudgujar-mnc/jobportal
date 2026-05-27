<?php
// setup.php - Run this file once to setup database
$host = 'localhost';
$dbname = 'job_portal';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    echo "✅ Database created successfully<br>";
    
    // Use database
    $pdo->exec("USE $dbname");
    
    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('job_seeker', 'employer') DEFAULT 'job_seeker',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Users table created<br>";
    
    // Create jobs table
    $pdo->exec("CREATE TABLE IF NOT EXISTS jobs (
        id INT PRIMARY KEY AUTO_INCREMENT,
        employer_id INT NOT NULL,
        title VARCHAR(200) NOT NULL,
        company VARCHAR(200) NOT NULL,
        location VARCHAR(200) NOT NULL,
        salary VARCHAR(100),
        job_type ENUM('Full-time', 'Part-time', 'Contract', 'Remote') DEFAULT 'Full-time',
        description TEXT NOT NULL,
        requirements TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "✅ Jobs table created<br>";
    
    // Create applications table
    $pdo->exec("CREATE TABLE IF NOT EXISTS applications (
        id INT PRIMARY KEY AUTO_INCREMENT,
        job_id INT NOT NULL,
        job_seeker_id INT NOT NULL,
        resume VARCHAR(500),
        cover_letter TEXT,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
        FOREIGN KEY (job_seeker_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "✅ Applications table created<br>";
    
    echo "<br><strong>🎉 Database setup completed successfully!</strong><br>";
    echo "<a href='index.php'>Go to Homepage →</a>";
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>