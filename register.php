<?php require_once 'config/database.php';
if(isset($_SESSION['user_id'])) header("Location: dashboard.php");
?>
<!DOCTYPE html>
<html>
<head><title>Register</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gradient-to-r from-indigo-600 to-purple-600 flex items-center justify-center h-screen">
<div class="bg-white p-8 rounded-2xl shadow-xl w-96">
    <h2 class="text-3xl font-bold text-center text-indigo-600 mb-6">Create Account</h2>
    <?php
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_POST['name'], $_POST['email'], $hashed, $_POST['role']]);
            header("Location: login.php");
            exit();
        } catch(PDOException $e) {
            echo "<div class='bg-red-100 text-red-700 p-3 rounded-lg mb-4'>Email already exists!</div>";
        }
    }
    ?>
    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required class="w-full p-3 border rounded-lg mb-3">
        <input type="email" name="email" placeholder="Email" required class="w-full p-3 border rounded-lg mb-3">
        <input type="password" name="password" placeholder="Password" required class="w-full p-3 border rounded-lg mb-3">
        <select name="role" class="w-full p-3 border rounded-lg mb-4">
            <option value="job_seeker">Job Seeker</option>
            <option value="employer">Employer</option>
        </select>
        <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700">Register</button>
    </form>
    <p class="text-center mt-4">Already have account? <a href="login.php" class="text-indigo-600">Login</a></p>
</div>
</body>
</html>