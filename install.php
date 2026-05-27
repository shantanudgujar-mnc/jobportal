<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec("CREATE DATABASE IF NOT EXISTS job_portal");
    $pdo->exec("USE job_portal");
    
    // Users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('job_seeker', 'employer') DEFAULT 'job_seeker',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Jobs table
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
    
    // Applications table
    $pdo->exec("CREATE TABLE IF NOT EXISTS applications (
        id INT PRIMARY KEY AUTO_INCREMENT,
        job_id INT NOT NULL,
        job_seeker_id INT NOT NULL,
        cover_letter TEXT,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
        FOREIGN KEY (job_seeker_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    
    // Test accounts (password = 123456)
    $hashed = password_hash('123456', PASSWORD_DEFAULT);
    
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute(['employer@test.com']);
    if(!$check->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Demo Employer', 'employer@test.com', $hashed, 'employer']);
    }
    
    $check->execute(['seeker@test.com']);
    if(!$check->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Demo Seeker', 'seeker@test.com', $hashed, 'job_seeker']);
    }
    
    echo "<!DOCTYPE html><html><head><title>Installation Done</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-gray-100 flex items-center justify-center h-screen'><div class='bg-white p-8 rounded-2xl shadow-xl text-center max-w-md'><h1 class='text-3xl font-bold text-green-600'>✅ Installation Complete!</h1><p class='mt-4 text-gray-700'>Database & tables created.</p><div class='mt-6 bg-gray-50 p-4 rounded-lg'><p class='font-semibold'>Test Logins:</p><p>📧 employer@test.com / 123456</p><p>📧 seeker@test.com / 123456</p></div><a href='login.php' class='mt-6 inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700'>Go to Login →</a></div></body></html>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>Make sure MySQL is running in XAMPP.";
}
?>