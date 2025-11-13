<?php
require_once 'includes/db_connect.php';
requireLogin();

$user_id = getCurrentUserId();
$resume_id = intval($_GET['id'] ?? 0);

// Verify resume belongs to user
$verify_stmt = $conn->prepare("SELECT r.*, t.name as template_name FROM resumes r LEFT JOIN templates t ON r.template_id = t.id WHERE r.id = ? AND r.user_id = ?");
$verify_stmt->bind_param("ii", $resume_id, $user_id);
$verify_stmt->execute();
$resume = $verify_stmt->get_result()->fetch_assoc();
$verify_stmt->close();

if (!$resume) {
    die("Resume not found");
}

// Fetch all resume data
$personal_info = $conn->query("SELECT * FROM personal_info WHERE resume_id = $resume_id")->fetch_assoc();
$education_list = $conn->query("SELECT * FROM education WHERE resume_id = $resume_id ORDER BY start_date DESC")->fetch_all(MYSQLI_ASSOC);
$experience_list = $conn->query("SELECT * FROM experience WHERE resume_id = $resume_id ORDER BY start_date DESC")->fetch_all(MYSQLI_ASSOC);
$skills_list = $conn->query("SELECT * FROM skills WHERE resume_id = $resume_id ORDER BY proficiency DESC")->fetch_all(MYSQLI_ASSOC);
$projects_list = $conn->query("SELECT * FROM projects WHERE resume_id = $resume_id ORDER BY start_date DESC")->fetch_all(MYSQLI_ASSOC);
$certifications_list = $conn->query("SELECT * FROM certifications WHERE resume_id = $resume_id ORDER BY issue_date DESC")->fetch_all(MYSQLI_ASSOC);

// Generate HTML content
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            background-color: #004346;
            color: white;
            padding: 30px;
            text-align: center;
            margin: -20px -20px 20px -20px;
        }
        h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
        }
        .contact-info {
            font-size: 14px;
            margin-top: 10px;
        }
        .section-title {
            color: #004346;
            border-bottom: 2px solid #004346;
            padding-bottom: 5px;
            margin-top: 25px;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: bold;
        }
        .item {
            margin-bottom: 15px;
        }
        .item-title {
            font-weight: bold;
            font-size: 16px;
        }
        .item-subtitle {
            color: #666;
            margin: 3px 0;
        }
        .item-date {
            color: #999;
            font-size: 14px;
            font-style: italic;
            margin: 3px 0;
        }
        .skills-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .skill-item {
            background-color: #F0EDE5;
            padding: 8px 12px;
            border-left: 3px solid #004346;
            display: inline-block;
        }
    </style>
</head>
<body>
    <?php if ($personal_info): ?>
        <div class="header">
            <h1><?php echo htmlspecialchars($personal_info['full_name']); ?></h1>
            <div class="contact-info">
                <?php if ($personal_info['email']): ?>
                    <?php echo htmlspecialchars($personal_info['email']); ?> |
                <?php endif; ?>
                <?php if ($personal_info['phone']): ?>
                    <?php echo htmlspecialchars($personal_info['phone']); ?> |
                <?php endif; ?>
                <?php if ($personal_info['address']): ?>
                    <?php echo htmlspecialchars($personal_info['address']); ?>
                <?php endif; ?>
                <?php if ($personal_info['linkedin'] || $personal_info['website']): ?>
                    <br>
                    <?php if ($personal_info['linkedin']): ?>
                        LinkedIn: <?php echo htmlspecialchars($personal_info['linkedin']); ?>
                    <?php endif; ?>
                    <?php if ($personal_info['website']): ?>
                        | Website: <?php echo htmlspecialchars($personal_info['website']); ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($personal_info['summary']): ?>
            <div class="section-title">PROFESSIONAL SUMMARY</div>
            <p><?php echo nl2br(htmlspecialchars($personal_info['summary'])); ?></p>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php if (!empty($experience_list)): ?>
        <div class="section-title">WORK EXPERIENCE</div>
        <?php foreach ($experience_list as $exp): ?>
            <div class="item">
                <div class="item-title"><?php echo htmlspecialchars($exp['position']); ?></div>
                <div class="item-subtitle">
                    <?php echo htmlspecialchars($exp['company']); ?>
                    <?php if ($exp['location']): ?>
                        - <?php echo htmlspecialchars($exp['location']); ?>
                    <?php endif; ?>
                </div>
                <div class="item-date">
                    <?php echo date('F Y', strtotime($exp['start_date'])); ?> - 
                    <?php echo $exp['current_job'] ? 'Present' : date('F Y', strtotime($exp['end_date'])); ?>
                </div>
                <?php if ($exp['description']): ?>
                    <p><?php echo nl2br(htmlspecialchars($exp['description'])); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if (!empty($education_list)): ?>
        <div class="section-title">EDUCATION</div>
        <?php foreach ($education_list as $edu): ?>
            <div class="item">
                <div class="item-title">
                    <?php echo htmlspecialchars($edu['degree']); ?>
                    <?php if ($edu['field_of_study']): ?>
                        in <?php echo htmlspecialchars($edu['field_of_study']); ?>
                    <?php endif; ?>
                </div>
                <div class="item-subtitle"><?php echo htmlspecialchars($edu['institution']); ?></div>
                <div class="item-date">
                    <?php echo date('F Y', strtotime($edu['start_date'])); ?> - 
                    <?php echo date('F Y', strtotime($edu['end_date'])); ?>
                </div>
                <?php if ($edu['description']): ?>
                    <p><?php echo nl2br(htmlspecialchars($edu['description'])); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if (!empty($skills_list)): ?>
        <div class="section-title">SKILLS</div>
        <div class="skills-container">
            <?php foreach ($skills_list as $skill): ?>
                <div class="skill-item">
                    <strong><?php echo htmlspecialchars($skill['skill_name']); ?></strong>
                    (<?php echo htmlspecialchars($skill['proficiency']); ?>)
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($projects_list)): ?>
        <div class="section-title">PROJECTS</div>
        <?php foreach ($projects_list as $project): ?>
            <div class="item">
                <div class="item-title"><?php echo htmlspecialchars($project['project_name']); ?></div>
                <?php if ($project['technologies']): ?>
                    <div class="item-subtitle">
                        Technologies: <?php echo htmlspecialchars($project['technologies']); ?>
                    </div>
                <?php endif; ?>
                <?php if ($project['start_date'] && $project['end_date']): ?>
                    <div class="item-date">
                        <?php echo date('F Y', strtotime($project['start_date'])); ?> - 
                        <?php echo date('F Y', strtotime($project['end_date'])); ?>
                    </div>
                <?php endif; ?>
                <?php if ($project['description']): ?>
                    <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                <?php endif; ?>
                <?php if ($project['url']): ?>
                    <p>URL: <?php echo htmlspecialchars($project['url']); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if (!empty($certifications_list)): ?>
        <div class="section-title">CERTIFICATIONS</div>
        <?php foreach ($certifications_list as $cert): ?>
            <div class="item">
                <div class="item-title"><?php echo htmlspecialchars($cert['certification_name']); ?></div>
                <div class="item-subtitle"><?php echo htmlspecialchars($cert['issuing_organization']); ?></div>
                <div class="item-date">
                    Issued: <?php echo date('F Y', strtotime($cert['issue_date'])); ?>
                    <?php if ($cert['expiry_date']): ?>
                        | Expires: <?php echo date('F Y', strtotime($cert['expiry_date'])); ?>
                    <?php endif; ?>
                </div>
                <?php if ($cert['credential_id']): ?>
                    <p>Credential ID: <?php echo htmlspecialchars($cert['credential_id']); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
<?php
$html = ob_get_clean();

// Set headers for HTML download
$filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $resume['title']) . '.html';
header('Content-Type: text/html');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

echo $html;
?>