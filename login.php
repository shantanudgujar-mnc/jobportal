<?php require_once 'config/database.php';
// If already logged in, go to dashboard
if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | JobPortal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-indigo-600 to-purple-600 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-96">
        <h2 class="text-3xl font-bold text-center text-indigo-600 mb-6">Welcome Back</h2>
        <?php
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                header("Location: dashboard.php");
                exit();
            } else {
                echo "<div class='bg-red-100 text-red-700 p-3 rounded-lg mb-4'>Invalid credentials!</div>";
            }
        }
        ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email address" required class="w-full p-3 border rounded-lg mb-4 focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <input type="password" name="password" placeholder="Password" required class="w-full p-3 border rounded-lg mb-4 focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">Login</button>
        </form>
        <p class="text-center text-gray-500 mt-4">Don't have an account? <a href="register.php" class="text-indigo-600">Register</a></p>
        <div class="mt-4 text-xs text-center text-gray-400">Demo: employer@test.com / 123456</div>
    </div>
</body>
</html>