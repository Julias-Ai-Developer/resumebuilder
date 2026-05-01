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
                                   <?php echo (int) $template['id'] === 1 ? 'checked' : ''; ?>>
                            <label class="form-check-label w-100" for="template<?php echo $template['id']; ?>">
                                <div class="template-preview template-preview-<?php echo $template['id']; ?> mb-3">
                                    <div class="preview-photo"></div>
                                    <div class="preview-title"></div>
                                    <div class="preview-subtitle"></div>
                                    <div class="preview-sidebar"></div>
                                    <div class="preview-lines">
                                        <span></span><span></span><span></span><span></span>
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
        position: relative;
        height: 220px;
        overflow: hidden;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #fff;
    }

    .preview-photo {
        position: absolute;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #cfd6df;
        z-index: 2;
    }

    .preview-title,
    .preview-subtitle,
    .preview-lines span {
        position: absolute;
        display: block;
        background: #2f3a4d;
        border-radius: 999px;
    }

    .preview-title {
        width: 95px;
        height: 12px;
    }

    .preview-subtitle {
        width: 65px;
        height: 7px;
        opacity: 0.65;
    }

    .preview-sidebar {
        position: absolute;
        left: 0;
        bottom: 0;
        width: 38%;
        height: 100%;
        background: #1d1d1d;
    }

    .preview-lines span {
        left: 48%;
        width: 42%;
        height: 8px;
        background: #d9dde3;
    }

    .preview-lines span:nth-child(1) { top: 104px; }
    .preview-lines span:nth-child(2) { top: 128px; width: 36%; }
    .preview-lines span:nth-child(3) { top: 154px; width: 42%; }
    .preview-lines span:nth-child(4) { top: 178px; width: 30%; }

    .template-preview-1::before {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        top: 44px;
        height: 58px;
        background: #0a438f;
        z-index: 1;
    }

    .template-preview-1 .preview-photo {
        top: 18px;
        left: 28px;
        width: 78px;
        height: 78px;
        border: 5px solid #0a438f;
    }

    .template-preview-1 .preview-title {
        top: 58px;
        left: 118px;
        background: #fff;
        z-index: 2;
    }

    .template-preview-1 .preview-subtitle {
        top: 78px;
        left: 132px;
        background: #fff;
        z-index: 2;
    }

    .template-preview-2 .preview-sidebar {
        background: #303b4f;
    }

    .template-preview-2 .preview-photo {
        top: 24px;
        left: 23px;
        width: 82px;
        height: 82px;
        border: 5px solid #fff;
    }

    .template-preview-2 .preview-title {
        top: 50px;
        left: 125px;
        width: 110px;
        height: 16px;
    }

    .template-preview-2 .preview-subtitle {
        top: 77px;
        left: 125px;
        width: 90px;
    }

    .template-preview-3::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 32px;
        background: linear-gradient(135deg, #ff2b2f 0 74%, #191919 74%);
    }

    .template-preview-3 .preview-sidebar {
        top: 82px;
        height: 138px;
        background: #191919;
    }

    .template-preview-3 .preview-photo {
        top: 48px;
        left: 18px;
        width: 76px;
        height: 76px;
        border-radius: 8px;
        border: 4px solid #ff5a00;
    }

    .template-preview-3 .preview-title {
        top: 56px;
        left: 118px;
        width: 112px;
        height: 18px;
        background: #000;
    }

    .template-preview-3 .preview-subtitle {
        top: 84px;
        left: 118px;
        width: 100px;
        background: #111;
    }

    .template-preview-3 .preview-lines span {
        background: #ff2b2f;
    }

    .template-preview-4 .preview-sidebar {
        width: 35%;
        background: #202020;
    }

    .template-preview-4 .preview-photo {
        top: 16px;
        left: 20px;
        width: 82px;
        height: 82px;
        border: 8px solid #ff8b45;
    }

    .template-preview-4 .preview-title {
        top: 34px;
        left: 128px;
        width: 115px;
        height: 18px;
        background: #202020;
    }

    .template-preview-4 .preview-subtitle {
        top: 64px;
        left: 130px;
        width: 86px;
        background: #202020;
    }

    .template-preview-4 .preview-lines span {
        background: #ff8b45;
    }
</style>

<?php include 'includes/footer.php'; ?>
