<?php require_once 'config/database.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobPortal – Find Your Dream Job</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">

    <!-- Navbar -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold text-indigo-600">🎯 JobPortal</a>
            <div class="space-x-4">
                <a href="index.php" class="text-gray-700 hover:text-indigo-600">Home</a>
                <a href="jobs.php" class="text-gray-700 hover:text-indigo-600">Jobs</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="text-gray-700 hover:text-indigo-600">Dashboard</a>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Login</a>
                    <a href="register.php" class="border border-indigo-600 text-indigo-600 px-4 py-2 rounded-lg hover:bg-indigo-50">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-20">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-5xl font-bold mb-4">Find Your Dream Job Today</h1>
            <p class="text-xl mb-8">Thousands of jobs from top companies – apply now!</p>
            <a href="jobs.php" class="bg-white text-indigo-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition">Browse Jobs →</a>
        </div>
    </section>

    <!-- Latest Jobs -->
    <div class="container mx-auto px-6 py-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">🔥 Latest Opportunities</h2>
        <div class="grid md:grid-cols-3 gap-6">
            <?php
            $jobs = $pdo->query("SELECT * FROM jobs ORDER BY created_at DESC LIMIT 3")->fetchAll();
            foreach($jobs as $job): ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($job['title']) ?></h3>
                        <p class="text-indigo-600 font-semibold mt-1"><?= htmlspecialchars($job['company']) ?></p>
                        <p class="text-gray-500 text-sm mt-2"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['location']) ?></p>
                        <p class="text-gray-500 text-sm"><i class="fas fa-clock"></i> <?= $job['job_type'] ?></p>
                        <a href="job-details.php?id=<?= $job['id'] ?>" class="mt-4 inline-block bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm">View Details →</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>