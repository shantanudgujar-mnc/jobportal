if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
<?php require_once 'config/database.php';
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'employer') { header("Location: login.php"); exit(); }
$job_id = $_GET['job_id'] ?? 0;
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'], $_POST['app_id'])) {
    $status = $_POST['action'] == 'approve' ? 'approved' : 'rejected';
    $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
    $stmt->execute([$status, $_POST['app_id']]);
    header("Location: manage-applicants.php?job_id=$job_id");
    exit();
}
$stmt = $pdo->prepare("SELECT jobs.title, applications.*, users.name, users.email FROM applications JOIN jobs ON applications.job_id = jobs.id JOIN users ON applications.job_seeker_id = users.id WHERE jobs.employer_id = ? AND applications.job_id = ?");
$stmt->execute([$_SESSION['user_id'], $job_id]);
$applicants = $stmt->fetchAll();
if(!$applicants) { echo "No applicants found."; exit(); }
$jobTitle = $applicants[0]['title'];
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Applicants</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-6"><h1 class="text-2xl font-bold mb-2">Applicants for: <?= htmlspecialchars($jobTitle) ?></h1>
<?php foreach($applicants as $app): ?>
<div class="border-b pb-4 mb-4"><p><strong><?= htmlspecialchars($app['name']) ?></strong> (<?= $app['email'] ?>)</p><p class="mt-2 text-gray-700"><strong>Cover Letter:</strong><br><?= nl2br(htmlspecialchars($app['cover_letter'])) ?></p><p class="mt-2">Status: <span class="font-semibold <?= $app['status']=='approved'?'text-green-600':($app['status']=='rejected'?'text-red-600':'text-yellow-600') ?>"><?= ucfirst($app['status']) ?></span></p>
<form method="POST" class="mt-2 space-x-2"><input type="hidden" name="app_id" value="<?= $app['id'] ?>"><button name="action" value="approve" class="bg-green-500 text-white px-3 py-1 rounded">Approve</button><button name="action" value="reject" class="bg-red-500 text-white px-3 py-1 rounded">Reject</button></form></div>
<?php endforeach; ?>
<a href="dashboard.php" class="inline-block mt-4 text-indigo-600">← Back to Dashboard</a></div></body></html>