<?php
require_once 'includes/db_connect.php';
requireLogin();

$user_id = getCurrentUserId();

// Handle resume deletion
if (isset($_GET['delete'])) {
    $resume_id = intval($_GET['delete']);
    $delete_stmt = $conn->prepare("DELETE FROM resumes WHERE id = ? AND user_id = ?");
    $delete_stmt->bind_param("ii", $resume_id, $user_id);
    $delete_stmt->execute();
    $delete_stmt->close();
    
    header("Location: dashboard.php?deleted=1");
    exit();
}

// Fetch user's resumes
$stmt = $conn->prepare("
    SELECT r.id, r.title, r.template_id, r.created_at, r.updated_at, t.name as template_name
    FROM resumes r
    LEFT JOIN templates t ON r.template_id = t.id
    WHERE r.user_id = ?
    ORDER BY r.updated_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$resumes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$pageTitle = "Dashboard - Resume Builder";
include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold" style="color: var(--primary-color);">
            <i class="bi bi-speedometer2"></i> My Dashboard
        </h2>
        <p class="text-muted">Welcome back, <?php echo htmlspecialchars(getCurrentUsername()); ?>!</p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="choose_template.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create New Resume
        </a>
    </div>
</div>

<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        Resume deleted successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['created'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        Resume created successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Total Resumes</h6>
                        <h2 class="mb-0 fw-bold"><?php echo count($resumes); ?></h2>
                    </div>
                    <i class="bi bi-file-earmark-text display-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-folder"></i> My Resumes</h5>
    </div>
    <div class="card-body">
        <?php if (empty($resumes)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">No resumes yet</h4>
                <p class="text-muted">Create your first resume to get started</p>
                <a href="choose_template.php" class="btn btn-primary mt-3">
                    <i class="bi bi-plus-circle"></i> Create Resume
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Template</th>
                            <th>Created</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resumes as $resume): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($resume['title']); ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?php echo htmlspecialchars($resume['template_name']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($resume['created_at'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($resume['updated_at'])); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="resume_form.php?id=<?php echo $resume['id']; ?>" 
                                           class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="preview.php?id=<?php echo $resume['id']; ?>" 
                                           class="btn btn-outline-success" title="Preview" target="_blank">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="download.php?id=<?php echo $resume['id']; ?>" 
                                           class="btn btn-outline-info" title="Download">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <a href="dashboard.php?delete=<?php echo $resume['id']; ?>" 
                                           class="btn btn-outline-danger" title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this resume?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
