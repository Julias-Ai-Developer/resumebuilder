<?php
require_once 'includes/db_connect.php';
require_once 'includes/resume_renderer.php';
requireLogin();

$user_id = getCurrentUserId();
$resume_id = intval($_GET['id'] ?? 0);

$verify_stmt = $conn->prepare("SELECT r.*, t.name as template_name FROM resumes r LEFT JOIN templates t ON r.template_id = t.id WHERE r.id = ? AND r.user_id = ?");
$verify_stmt->bind_param("ii", $resume_id, $user_id);
$verify_stmt->execute();
$resume = $verify_stmt->get_result()->fetch_assoc();
$verify_stmt->close();

if (!$resume) {
    header("Location: dashboard.php");
    exit();
}

$data = [
    'resume' => $resume,
    'personal_info' => $conn->query("SELECT * FROM personal_info WHERE resume_id = $resume_id")->fetch_assoc(),
    'education_list' => $conn->query("SELECT * FROM education WHERE resume_id = $resume_id ORDER BY start_date DESC")->fetch_all(MYSQLI_ASSOC),
    'experience_list' => $conn->query("SELECT * FROM experience WHERE resume_id = $resume_id ORDER BY start_date DESC")->fetch_all(MYSQLI_ASSOC),
    'skills_list' => $conn->query("SELECT * FROM skills WHERE resume_id = $resume_id ORDER BY proficiency DESC")->fetch_all(MYSQLI_ASSOC),
    'projects_list' => $conn->query("SELECT * FROM projects WHERE resume_id = $resume_id ORDER BY start_date DESC")->fetch_all(MYSQLI_ASSOC),
    'certifications_list' => $conn->query("SELECT * FROM certifications WHERE resume_id = $resume_id ORDER BY issue_date DESC")->fetch_all(MYSQLI_ASSOC),
];

$toolbar = '
<div class="toolbar">
    <a href="resume_form.php?id=' . $resume_id . '"><i class="bi bi-pencil"></i> Edit</a>
    <a href="download.php?id=' . $resume_id . '"><i class="bi bi-filetype-pdf"></i> Download PDF</a>
    <button onclick="printOnePage()"><i class="bi bi-printer"></i> Print</button>
</div>';

echo renderResumeDocument($data, [
    'toolbar' => $toolbar,
]);
?>
