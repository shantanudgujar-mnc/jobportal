<?php require_once 'config/database.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$isEmployer = ($_SESSION['user_role'] == 'employer');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | JobPortal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">

<nav class="bg-white shadow-md p-4">
    <div class="container mx-auto flex justify-between">
        <a href="dashboard.php" class="text-2xl font-bold text-indigo-600">JobPortal</a>
        <div class="space-x-4">
            <span class="text-gray-700">👋 <?= htmlspecialchars($_SESSION['user_name']) ?> (<?= $_SESSION['user_role'] ?>)</span>
            <a href="logout.php" class="text-red-500">Logout</a>
        </div>
    </div>
</nav>

<div class="container mx-auto px-6 py-10">
    <h1 class="text-4xl font-bold text-gray-800 mb-2">Dashboard</h1>
    <p class="text-gray-600 mb-8">Manage your activity from here</p>

    <?php if($isEmployer): 
        $stmt = $pdo->prepare("SELECT jobs.*, COUNT(applications.id) as app_count 
                               FROM jobs LEFT JOIN applications ON jobs.id = applications.job_id 
                               WHERE jobs.employer_id = ? GROUP BY jobs.id");
        $stmt->execute([$_SESSION['user_id']]);
        $jobs = $stmt->fetchAll();
        $totalJobs = count($jobs);
        $totalApps = array_sum(array_column($jobs, 'app_count'));
    ?>
        <div class="grid md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white p-6 rounded-xl shadow-md"><i class="fas fa-briefcase text-3xl text-indigo-600"></i><h3 class="text-2xl font-bold"><?= $totalJobs ?></h3><p>Jobs Posted</p></div>
            <div class="bg-white p-6 rounded-xl shadow-md"><i class="fas fa-users text-3xl text-green-600"></i><h3 class="text-2xl font-bold"><?= $totalApps ?></h3><p>Total Applications</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex justify-between items-center mb-4"><h2 class="text-2xl font-bold">📌 Your Jobs</h2><a href="post-job.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg">+ Post New Job</a></div>
            <?php if(empty($jobs)): ?>
                <p class="text-gray-500">No jobs posted yet. <a href="post-job.php" class="text-indigo-600">Create your first job</a></p>
            <?php else: ?>
                <div class="overflow-x-auto"><table class="min-w-full"><thead class="bg-gray-100"><tr><th class="p-3 text-left">Title</th><th>Location</th><th>Applications</th><th>Action</th></tr></thead><tbody>
                <?php foreach($jobs as $job): ?>
                    <tr class="border-b"><td class="p-3"><?= htmlspecialchars($job['title']) ?></td><td><?= htmlspecialchars($job['location']) ?></td><td><?= $job['app_count'] ?></td><td><a href="manage-applicants.php?job_id=<?= $job['id'] ?>" class="bg-blue-500 text-white px-3 py-1 rounded text-sm">View Applicants</a></td></tr>
                <?php endforeach; ?>
                </tbody></table></div>
            <?php endif; ?>
        </div>
    <?php else: 
        $stmt = $pdo->prepare("SELECT applications.*, jobs.title, jobs.company, jobs.location FROM applications JOIN jobs ON applications.job_id = jobs.id WHERE job_seeker_id = ? ORDER BY applied_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $applications = $stmt->fetchAll();
        $statusCounts = ['pending'=>0,'approved'=>0,'rejected'=>0];
        foreach($applications as $app) $statusCounts[$app['status']]++;
    ?>
        <div class="grid md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white p-6 rounded-xl shadow-md"><i class="fas fa-clock text-yellow-500 text-3xl"></i><h3 class="text-2xl font-bold"><?= $statusCounts['pending'] ?></h3><p>Pending</p></div>
            <div class="bg-white p-6 rounded-xl shadow-md"><i class="fas fa-check-circle text-green-500 text-3xl"></i><h3 class="text-2xl font-bold"><?= $statusCounts['approved'] ?></h3><p>Approved</p></div>
            <div class="bg-white p-6 rounded-xl shadow-md"><i class="fas fa-times-circle text-red-500 text-3xl"></i><h3 class="text-2xl font-bold"><?= $statusCounts['rejected'] ?></h3><p>Rejected</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6"><h2 class="text-2xl font-bold mb-4">📄 My Applications</h2>
            <?php if(empty($applications)): ?>
                <p class="text-gray-500">You haven't applied to any jobs yet. <a href="jobs.php" class="text-indigo-600">Browse Jobs</a></p>
            <?php else: ?>
                <div class="space-y-4"><?php foreach($applications as $app): ?>
                    <div class="border rounded-lg p-4 flex justify-between items-center"><div><h3 class="font-bold"><?= htmlspecialchars($app['title']) ?></h3><p class="text-sm text-gray-600"><?= htmlspecialchars($app['company']) ?> - <?= htmlspecialchars($app['location']) ?></p></div>
                    <span class="px-3 py-1 rounded-full text-sm <?= $app['status']=='approved'?'bg-green-100 text-green-800':($app['status']=='rejected'?'bg-red-100 text-red-800':'bg-yellow-100 text-yellow-800') ?>"><?= ucfirst($app['status']) ?></span></div>
                <?php endforeach; ?></div>
            <?php endif; ?>
            <div class="mt-6"><a href="jobs.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg inline-block">Browse More Jobs</a></div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>