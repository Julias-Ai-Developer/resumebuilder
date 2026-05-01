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
    header("Location: dashboard.php");
    exit();
}

// Fetch all resume data
$personal_info = $conn->query("SELECT * FROM personal_info WHERE resume_id = $resume_id")->fetch_assoc();
$education_list = $conn->query("SELECT * FROM education WHERE resume_id = $resume_id ORDER BY start_date DESC")->fetch_all(MYSQLI_ASSOC);
$experience_list = $conn->query("SELECT * FROM experience WHERE resume_id = $resume_id ORDER BY start_date DESC")->fetch_all(MYSQLI_ASSOC);
$skills_list = $conn->query("SELECT * FROM skills WHERE resume_id = $resume_id ORDER BY proficiency DESC")->fetch_all(MYSQLI_ASSOC);
$projects_list = $conn->query("SELECT * FROM projects WHERE resume_id = $resume_id ORDER BY start_date DESC")->fetch_all(MYSQLI_ASSOC);
$certifications_list = $conn->query("SELECT * FROM certifications WHERE resume_id = $resume_id ORDER BY issue_date DESC")->fetch_all(MYSQLI_ASSOC);
$is_print_preview = isset($_GET['print']);
$has_resume_content = $personal_info
    || !empty($education_list)
    || !empty($experience_list)
    || !empty($skills_list)
    || !empty($projects_list)
    || !empty($certifications_list);
$display_name = $personal_info && !empty($personal_info['full_name'])
    ? $personal_info['full_name']
    : $resume['title'];
$header_color = $resume['header_color'] ?? '#004346';
$section_color = $resume['section_color'] ?? '#004346';
$accent_color = $resume['accent_color'] ?? '#F0EDE5';
$text_color = $resume['text_color'] ?? '#333333';
$summary_color = $resume['summary_color'] ?? $section_color;
$experience_color = $resume['experience_color'] ?? $section_color;
$education_color = $resume['education_color'] ?? $section_color;
$skills_color = $resume['skills_color'] ?? $section_color;
$projects_color = $resume['projects_color'] ?? $section_color;
$certifications_color = $resume['certifications_color'] ?? $section_color;
$photo_path = ($personal_info && !empty($personal_info['photo_path']) && is_file(__DIR__ . '/' . $personal_info['photo_path']))
    ? $personal_info['photo_path']
    : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($resume['title']); ?> - Preview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: <?php echo htmlspecialchars($section_color); ?>;
            --header-color: <?php echo htmlspecialchars($header_color); ?>;
            --accent-color: <?php echo htmlspecialchars($accent_color); ?>;
            --text-color: <?php echo htmlspecialchars($text_color); ?>;
            --summary-color: <?php echo htmlspecialchars($summary_color); ?>;
            --experience-color: <?php echo htmlspecialchars($experience_color); ?>;
            --education-color: <?php echo htmlspecialchars($education_color); ?>;
            --skills-color: <?php echo htmlspecialchars($skills_color); ?>;
            --projects-color: <?php echo htmlspecialchars($projects_color); ?>;
            --certifications-color: <?php echo htmlspecialchars($certifications_color); ?>;
        }
        
        body {
            background-color: #e0e0e0;
            color: var(--text-color);
        }
        
        .resume-container {
            width: 210mm;
            max-width: calc(100vw - 40px);
            min-height: 297mm;
            margin: 20px auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
        }
        
        .resume-header {
            background-color: var(--header-color);
            color: white;
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            text-align: left;
        }

        .resume-photo {
            width: 128px;
            height: 128px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255,255,255,0.9);
            flex: 0 0 auto;
        }

        .resume-heading {
            min-width: 0;
        }
        
        .resume-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .resume-subtitle {
            opacity: 0.82;
            margin: 0;
        }
        
        .contact-info {
            display: flex;
            justify-content: flex-start;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 15px;
        }
        
        .contact-info span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .contact-label {
            font-weight: 700;
        }
        
        .resume-content {
            padding: 40px;
        }
        
        .section-title {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 5px;
            margin-bottom: 20px;
            margin-top: 30px;
            font-size: 1.3rem;
            font-weight: bold;
        }
        
        .section-title:first-child {
            margin-top: 0;
        }
        
        .experience-item, .education-item, .project-item, .certification-item {
            margin-bottom: 20px;
        }
        
        .item-title {
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }
        
        .item-subtitle {
            color: #666;
            margin-bottom: 5px;
        }
        
        .item-date {
            color: #999;
            font-size: 0.9rem;
            font-style: italic;
            margin-bottom: 10px;
        }
        
        .skills-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }
        
        .skill-item {
            background-color: var(--accent-color);
            padding: 10px 15px;
            border-radius: 5px;
            border-left: 3px solid var(--primary-color);
        }

        .summary-title {
            color: var(--summary-color);
            border-bottom-color: var(--summary-color);
        }

        .experience-title {
            color: var(--experience-color);
            border-bottom-color: var(--experience-color);
        }

        .education-title {
            color: var(--education-color);
            border-bottom-color: var(--education-color);
        }

        .skills-title {
            color: var(--skills-color);
            border-bottom-color: var(--skills-color);
        }

        .projects-title {
            color: var(--projects-color);
            border-bottom-color: var(--projects-color);
        }

        .certifications-title {
            color: var(--certifications-color);
            border-bottom-color: var(--certifications-color);
        }

        .empty-preview {
            min-height: 160mm;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #666;
        }

        .empty-preview i {
            color: var(--primary-color);
            font-size: 3rem;
            margin-bottom: 12px;
        }
        
        .toolbar {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .print-preview-note {
            max-width: 210mm;
            margin: 18px auto 0;
            color: #555;
            font-size: 0.95rem;
        }

        .preview-warning {
            max-width: 210mm;
            margin: 18px auto 0;
        }

        @page {
            size: A4;
            margin: 12mm;
        }
        
        @media print {
            body {
                background: white;
            }
            
            .resume-container {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
                width: 100%;
                min-height: auto;
            }
            
            .toolbar {
                display: none;
            }

            .print-preview-note {
                display: none;
            }

            .preview-warning {
                display: none;
            }
        }

        @media (max-width: 640px) {
            .resume-header {
                display: block;
                text-align: center;
            }

            .resume-photo {
                margin-bottom: 16px;
            }

            .contact-info {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="resume_form.php?id=<?php echo $resume_id; ?>" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="download.php?id=<?php echo $resume_id; ?>" class="btn btn-sm btn-success">
            <i class="bi bi-download"></i> Download
        </a>
        <a href="preview.php?id=<?php echo $resume_id; ?>&print=1" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-file-earmark-text"></i> Print Preview
        </a>
        <button onclick="window.print()" class="btn btn-sm btn-info">
            <i class="bi bi-printer"></i> Print
        </button>
    </div>

    <?php if ($is_print_preview): ?>
        <div class="print-preview-note">
            Print preview uses an A4 page frame. Use the Print button, then choose Save as PDF or your printer.
        </div>
    <?php endif; ?>

    <?php if (!$personal_info): ?>
        <div class="alert alert-warning preview-warning">
            <i class="bi bi-exclamation-triangle"></i>
            Personal Information has not been saved yet. The preview is only showing sections you have saved, such as Projects.
            <a href="resume_form.php?id=<?php echo $resume_id; ?>" class="alert-link">Add your name, email, phone, photo, and summary</a>.
        </div>
    <?php endif; ?>

    <div class="resume-container">
        <div class="resume-header">
            <?php if ($photo_path): ?>
                <img src="<?php echo htmlspecialchars($photo_path); ?>" alt="Profile photo" class="resume-photo">
            <?php endif; ?>
            <div class="resume-heading">
                <h1><?php echo htmlspecialchars($display_name); ?></h1>
                <?php if (!$personal_info): ?>
                    <p class="resume-subtitle">Personal information not saved yet</p>
                <?php else: ?>
                    <div class="contact-info">
                        <?php if ($personal_info['email']): ?>
                            <span><i class="bi bi-envelope"></i> <span class="contact-label">Email:</span> <?php echo htmlspecialchars($personal_info['email']); ?></span>
                        <?php endif; ?>
                        <?php if ($personal_info['phone']): ?>
                            <span><i class="bi bi-telephone"></i> <span class="contact-label">Phone:</span> <?php echo htmlspecialchars($personal_info['phone']); ?></span>
                        <?php endif; ?>
                        <?php if ($personal_info['address']): ?>
                            <span><i class="bi bi-geo-alt"></i> <span class="contact-label">Address:</span> <?php echo htmlspecialchars($personal_info['address']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="contact-info">
                        <?php if ($personal_info['linkedin']): ?>
                            <span><i class="bi bi-linkedin"></i> <span class="contact-label">LinkedIn:</span> <?php echo htmlspecialchars($personal_info['linkedin']); ?></span>
                        <?php endif; ?>
                        <?php if ($personal_info['website']): ?>
                            <span><i class="bi bi-globe"></i> <span class="contact-label">Website:</span> <?php echo htmlspecialchars($personal_info['website']); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="resume-content">
                <?php if (!$has_resume_content): ?>
                    <div class="empty-preview">
                        <div>
                            <i class="bi bi-file-earmark-text"></i>
                            <h2 class="h4">No resume details yet</h2>
                            <p class="mb-3">Add personal information, education, experience, skills, projects, or certifications to see them here.</p>
                            <a href="resume_form.php?id=<?php echo $resume_id; ?>" class="btn btn-primary">
                                <i class="bi bi-pencil"></i> Add Details
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($personal_info && $personal_info['summary']): ?>
                    <h2 class="section-title summary-title">Professional Summary</h2>
                    <p><?php echo nl2br(htmlspecialchars($personal_info['summary'])); ?></p>
                <?php endif; ?>
        
                <?php if (!empty($experience_list)): ?>
                    <h2 class="section-title experience-title"><i class="bi bi-briefcase"></i> Work Experience</h2>
                    <?php foreach ($experience_list as $exp): ?>
                        <div class="experience-item">
                            <div class="item-title"><?php echo htmlspecialchars($exp['position']); ?></div>
                            <div class="item-subtitle">
                                <strong><?php echo htmlspecialchars($exp['company']); ?></strong>
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
                    <h2 class="section-title education-title"><i class="bi bi-mortarboard"></i> Education</h2>
                    <?php foreach ($education_list as $edu): ?>
                        <div class="education-item">
                            <div class="item-title"><?php echo htmlspecialchars($edu['degree']); ?> 
                                <?php if ($edu['field_of_study']): ?>
                                    in <?php echo htmlspecialchars($edu['field_of_study']); ?>
                                <?php endif; ?>
                            </div>
                            <div class="item-subtitle"><strong><?php echo htmlspecialchars($edu['institution']); ?></strong></div>
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
                    <h2 class="section-title skills-title"><i class="bi bi-tools"></i> Skills</h2>
                    <div class="skills-grid">
                        <?php foreach ($skills_list as $skill): ?>
                            <div class="skill-item">
                                <strong><?php echo htmlspecialchars($skill['skill_name']); ?></strong>
                                <br><small><?php echo htmlspecialchars($skill['proficiency']); ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($projects_list)): ?>
                    <h2 class="section-title projects-title"><i class="bi bi-kanban"></i> Projects</h2>
                    <?php foreach ($projects_list as $project): ?>
                        <div class="project-item">
                            <div class="item-title"><?php echo htmlspecialchars($project['project_name']); ?></div>
                            <?php if ($project['technologies']): ?>
                                <div class="item-subtitle"><strong>Technologies:</strong> <?php echo htmlspecialchars($project['technologies']); ?></div>
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
                                <p><i class="bi bi-link-45deg"></i> <a href="<?php echo htmlspecialchars($project['url']); ?>" target="_blank"><?php echo htmlspecialchars($project['url']); ?></a></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if (!empty($certifications_list)): ?>
                    <h2 class="section-title certifications-title"><i class="bi bi-award"></i> Certifications</h2>
                    <?php foreach ($certifications_list as $cert): ?>
                        <div class="certification-item">
                            <div class="item-title"><?php echo htmlspecialchars($cert['certification_name']); ?></div>
                            <div class="item-subtitle"><strong><?php echo htmlspecialchars($cert['issuing_organization']); ?></strong></div>
                            <div class="item-date">
                                Issued: <?php echo date('F Y', strtotime($cert['issue_date'])); ?>
                                <?php if ($cert['expiry_date']): ?>
                                    | Expires: <?php echo date('F Y', strtotime($cert['expiry_date'])); ?>
                                <?php endif; ?>
                            </div>
                            <?php if ($cert['credential_id']): ?>
                                <p>Credential ID: <?php echo htmlspecialchars($cert['credential_id']); ?></p>
                            <?php endif; ?>
                            <?php if ($cert['credential_url']): ?>
                                <p><i class="bi bi-link-45deg"></i> <a href="<?php echo htmlspecialchars($cert['credential_url']); ?>" target="_blank">View Certificate</a></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
