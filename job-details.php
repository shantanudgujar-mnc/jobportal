<?php
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id <= 0) die("Invalid job ID.");

$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->execute([$id]);
$job = $stmt->fetch();

if(!$job) die("Job not found.");

$alreadyApplied = false;
if($_SESSION['user_role'] == 'job_seeker') {
    $check = $pdo->prepare("SELECT id FROM applications WHERE job_id = ? AND job_seeker_id = ?");
    $check->execute([$id, $_SESSION['user_id']]);
    $alreadyApplied = $check->fetch();
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['user_role'] == 'job_seeker' && !$alreadyApplied) {
    $cover = trim($_POST['cover_letter']);
    if($cover) {
        $insert = $pdo->prepare("INSERT INTO applications (job_id, job_seeker_id, cover_letter) VALUES (?, ?, ?)");
        $insert->execute([$id, $_SESSION['user_id'], $cover]);
        header("Location: job-details.php?id=$id&applied=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($job['title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md p-6">
        <h1 class="text-3xl font-bold"><?= htmlspecialchars($job['title']) ?></h1>
        <div class="my-4 p-4 bg-gray-50 rounded">
            <p><strong>Company:</strong> <?= htmlspecialchars($job['company']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
            <p><strong>Type:</strong> <?= $job['job_type'] ?></p>
            <p><strong>Salary:</strong> <?= htmlspecialchars($job['salary'] ?: 'Negotiable') ?></p>
        </div>
        <h2 class="text-xl font-bold mt-4">Description</h2>
        <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
        <h2 class="text-xl font-bold mt-4">Requirements</h2>
        <p><?= nl2br(htmlspecialchars($job['requirements'])) ?></p>

        <?php if($_SESSION['user_role'] == 'job_seeker'): ?>
            <?php if(isset($_GET['applied']) || $alreadyApplied): ?>
                <div class="mt-6 bg-green-100 p-3 rounded">✅ You have already applied for this job.</div>
            <?php else: ?>
                <form method="POST" class="mt-6">
                    <textarea name="cover_letter" rows="4" placeholder="Write your cover letter..." class="w-full p-2 border rounded" required></textarea>
                    <button type="submit" class="mt-2 bg-indigo-600 text-white px-4 py-2 rounded">Submit Application</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>

        <a href="jobs.php" class="inline-block mt-6 text-indigo-600">← Back to Jobs</a>
    </div>
</body>
</html>