<?php
require_once 'includes/db_connect.php';
requireLogin();

$user_id = getCurrentUserId();

// Fetch available templates
$stmt = $conn->query("SELECT * FROM templates WHERE is_active = 1 ORDER BY id");
$templates = $stmt->fetch_all(MYSQLI_ASSOC);

// Handle template selection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $template_id = intval($_POST['template_id'] ?? 1);
    $resume_title = sanitize($_POST['resume_title'] ?? 'My Resume');
    
    $insert_stmt = $conn->prepare("INSERT INTO resumes (user_id, title, template_id) VALUES (?, ?, ?)");
    $insert_stmt->bind_param("isi", $user_id, $resume_title, $template_id);
    
    if ($insert_stmt->execute()) {
        $resume_id = $insert_stmt->insert_id;
        header("Location: resume_form.php?id=$resume_id&created=1");
        exit();
    }
    $insert_stmt->close();
}

$pageTitle = "Choose Template - Resume Builder";
include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold" style="color: var(--primary-color);">
            <i class="bi bi-palette"></i> Choose a Template
        </h2>
        <p class="text-muted">Select a template to start building your resume</p>
    </div>
</div>

<form method="POST" action="">
    <div class="row mb-4">
        <div class="col-md-6">
            <label for="resume_title" class="form-label fw-bold">Resume Title</label>
            <input type="text" class="form-control" id="resume_title" name="resume_title" 
                   placeholder="e.g., Software Engineer Resume" required>
        </div>
    </div>
    
    <div class="row g-4">
        <?php foreach ($templates as $template): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 template-card">
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="template_id" 
                                   id="template<?php echo $template['id']; ?>" 
                                   value="<?php echo $template['id']; ?>" 
                                   <?php echo $template['id'] === 1 ? 'checked' : ''; ?>>
                            <label class="form-check-label w-100" for="template<?php echo $template['id']; ?>">
                                <div class="template-preview mb-3 p-4 border rounded" 
                                     style="height: 200px; background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);">
                                    <div class="d-flex flex-column h-100 justify-content-center align-items-center">
                                        <i class="bi bi-file-earmark-text display-1 text-muted"></i>
                                        <small class="text-muted mt-2">Template Preview</small>
                                    </div>
                                </div>
                                <h5 class="card-title"><?php echo htmlspecialchars($template['name']); ?></h5>
                                <p class="card-text small text-muted">
                                    <?php echo htmlspecialchars($template['description']); ?>
                                </p>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-check-circle"></i> Continue with Selected Template
            </button>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-lg ms-2">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
        </div>
    </div>
</form>

<style>
    .template-card {
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }
    
    .template-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .template-card .form-check-input:checked ~ .form-check-label {
        color: var(--primary-color);
        font-weight: bold;
    }
    
    .template-preview {
        background-color: #f8f9fa;
    }
</style>

<?php include 'includes/footer.php'; ?>