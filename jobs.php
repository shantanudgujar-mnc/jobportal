<?php
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$jobs = $pdo->query("SELECT * FROM jobs ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Jobs</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <h1 class="text-3xl font-bold mb-4">All Jobs</h1>
    <a href="dashboard.php" class="text-indigo-600">← Back to Dashboard</a>
    <div class="mt-6 space-y-4">
        <?php foreach($jobs as $job): ?>
            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-xl font-bold"><?= htmlspecialchars($job['title']) ?></h2>
                <p><?= htmlspecialchars($job['company']) ?> - <?= htmlspecialchars($job['location']) ?></p>
                <a href="job-details.php?id=<?= $job['id'] ?>" class="text-indigo-600">View Details →</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>