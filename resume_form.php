<?php
require_once 'includes/db_connect.php';
requireLogin();

$user_id = getCurrentUserId();
$resume_id = intval($_GET['id'] ?? 0);

// Verify resume belongs to user
$verify_stmt = $conn->prepare("SELECT id, title, template_id FROM resumes WHERE id = ? AND user_id = ?");
$verify_stmt->bind_param("ii", $resume_id, $user_id);
$verify_stmt->execute();
$resume = $verify_stmt->get_result()->fetch_assoc();
$verify_stmt->close();

if (!$resume) {
    header("Location: dashboard.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section = $_POST['section'] ?? '';
    
    // Personal Information
    if ($section === 'personal') {
        $full_name = sanitize($_POST['full_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address']);
        $linkedin = sanitize($_POST['linkedin']);
        $website = sanitize($_POST['website']);
        $summary = sanitize($_POST['summary']);
        
        // Check if personal info exists
        $check = $conn->prepare("SELECT id FROM personal_info WHERE resume_id = ?");
        $check->bind_param("i", $resume_id);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();
        
        if ($exists) {
            $stmt = $conn->prepare("UPDATE personal_info SET full_name=?, email=?, phone=?, address=?, linkedin=?, website=?, summary=? WHERE resume_id=?");
            $stmt->bind_param("sssssssi", $full_name, $email, $phone, $address, $linkedin, $website, $summary, $resume_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO personal_info (resume_id, full_name, email, phone, address, linkedin, website, summary) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssss", $resume_id, $full_name, $email, $phone, $address, $linkedin, $website, $summary);
        }
        $stmt->execute();
        $stmt->close();
    }
    
    // Education
    if ($section === 'education') {
        $institution = sanitize($_POST['institution']);
        $degree = sanitize($_POST['degree']);
        $field_of_study = sanitize($_POST['field_of_study']);
        $start_date = sanitize($_POST['start_date']);
        $end_date = sanitize($_POST['end_date']);
        $description = sanitize($_POST['description']);
        
        $stmt = $conn->prepare("INSERT INTO education (resume_id, institution, degree, field_of_study, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $resume_id, $institution, $degree, $field_of_study, $start_date, $end_date, $description);
        $stmt->execute();
        $stmt->close();
    }
    
    // Experience
    if ($section === 'experience') {
        $company = sanitize($_POST['company']);
        $position = sanitize($_POST['position']);
        $location = sanitize($_POST['location']);
        $start_date = sanitize($_POST['exp_start_date']);
        $end_date = sanitize($_POST['exp_end_date']);
        $current_job = isset($_POST['current_job']) ? 1 : 0;
        $description = sanitize($_POST['exp_description']);
        
        $stmt = $conn->prepare("INSERT INTO experience (resume_id, company, position, location, start_date, end_date, current_job, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $resume_id, $company, $position, $location, $start_date, $end_date, $current_job, $description);
        $stmt->execute();
        $stmt->close();
    }
    
    // Skills
    if ($section === 'skills') {
        $skill_name = sanitize($_POST['skill_name']);
        $proficiency = sanitize($_POST['proficiency']);
        
        $stmt = $conn->prepare("INSERT INTO skills (resume_id, skill_name, proficiency) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $resume_id, $skill_name, $proficiency);
        $stmt->execute();
        $stmt->close();
    }
    
    // Projects
    if ($section === 'projects') {
        $project_name = sanitize($_POST['project_name']);
        $description = sanitize($_POST['project_description']);
        $technologies = sanitize($_POST['technologies']);
        $url = sanitize($_POST['project_url']);
        $start_date = sanitize($_POST['project_start_date']);
        $end_date = sanitize($_POST['project_end_date']);
        
        $stmt = $conn->prepare("INSERT INTO projects (resume_id, project_name, description, technologies, url, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $resume_id, $project_name, $description, $technologies, $url, $start_date, $end_date);
        $stmt->execute();
        $stmt->close();
    }
    
    // Certifications
    if ($section === 'certifications') {
        $certification_name = sanitize($_POST['certification_name']);
        $issuing_organization = sanitize($_POST['issuing_organization']);
        $issue_date = sanitize($_POST['issue_date']);
        $expiry_date = sanitize($_POST['expiry_date']);
        $credential_id = sanitize($_POST['credential_id']);
        $credential_url = sanitize($_POST['credential_url']);
        
        $stmt = $conn->prepare("INSERT INTO certifications (resume_id, certification_name, issuing_organization, issue_date, expiry_date, credential_id, credential_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $resume_id, $certification_name, $issuing_organization, $issue_date, $expiry_date, $credential_id, $credential_url);
        $stmt->execute();
        $stmt->close();
    }
    
    header("Location: resume_form.php?id=$resume_id&saved=1");
    exit();
}

// Handle deletions
if (isset($_GET['delete'])) {
    $delete_section = $_GET['delete_section'] ?? '';
    $delete_id = intval($_GET['delete']);
    
    $tables = ['education', 'experience', 'skills', 'projects', 'certifications'];
    if (in_array($delete_section, $tables)) {
        $stmt = $conn->prepare("DELETE FROM $delete_section WHERE id = ? AND resume_id = ?");
        $stmt->bind_param("ii", $delete_id, $resume_id);
        $stmt->execute();
        $stmt->close();
    }
    
    header("Location: resume_form.php?id=$resume_id&deleted=1");
    exit();
}

// Fetch existing data
$personal_info = $conn->query("SELECT * FROM personal_info WHERE resume_id = $resume_id")->fetch_assoc();
$education_list = $conn->query("SELECT * FROM education WHERE resume_id = $resume_id ORDER BY start_date DESC")->fetch_all(MYSQLI_ASSOC);
$experience_list = $conn->query("SELECT * FROM experience WHERE resume_id = $resume_id ORDER BY start_date DESC")->fetch_all(MYSQLI_ASSOC);
$skills_list = $conn->query("SELECT * FROM skills WHERE resume_id = $resume_id ORDER BY proficiency DESC")->fetch_all(MYSQLI_ASSOC);
$projects_list = $conn->query("SELECT * FROM projects WHERE resume_id = $resume_id ORDER BY start_date DESC")->fetch_all(MYSQLI_ASSOC);
$certifications_list = $conn->query("SELECT * FROM certifications WHERE resume_id = $resume_id ORDER BY issue_date DESC")->fetch_all(MYSQLI_ASSOC);

$pageTitle = "Edit Resume - " . htmlspecialchars($resume['title']);
include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold" style="color: var(--primary-color);">
            <i class="bi bi-pencil-square"></i> <?php echo htmlspecialchars($resume['title']); ?>
        </h2>
        <p class="text-muted">Fill in the sections below to build your resume</p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="preview.php?id=<?php echo $resume_id; ?>" class="btn btn-success" target="_blank">
            <i class="bi bi-eye"></i> Preview
        </a>
        <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> Changes saved successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-info alert-dismissible fade show">
        <i class="bi bi-trash"></i> Item deleted successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['created'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> Resume created! Start adding your information below.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Personal Information Section -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-person"></i> Personal Information</h5>
        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#personalSection">
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="personalSection">
        <div class="card-body">
            <form method="POST" action="">
                <input type="hidden" name="section" value="personal">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Full Name *</label>
                        <input type="text" class="form-control" name="full_name" 
                               value="<?php echo htmlspecialchars($personal_info['full_name'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Email *</label>
                        <input type="email" class="form-control" name="email" 
                               value="<?php echo htmlspecialchars($personal_info['email'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Phone</label>
                        <input type="tel" class="form-control" name="phone" 
                               value="<?php echo htmlspecialchars($personal_info['phone'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Address</label>
                        <input type="text" class="form-control" name="address" 
                               value="<?php echo htmlspecialchars($personal_info['address'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">LinkedIn</label>
                        <input type="url" class="form-control" name="linkedin" 
                               value="<?php echo htmlspecialchars($personal_info['linkedin'] ?? ''); ?>" 
                               placeholder="https://linkedin.com/in/yourprofile">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Website/Portfolio</label>
                        <input type="url" class="form-control" name="website" 
                               value="<?php echo htmlspecialchars($personal_info['website'] ?? ''); ?>" 
                               placeholder="https://yourwebsite.com">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Professional Summary</label>
                        <textarea class="form-control" name="summary" rows="4" 
                                  placeholder="Write a brief summary about yourself and your career objectives"><?php echo htmlspecialchars($personal_info['summary'] ?? ''); ?></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Save Personal Info
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Education Section -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-mortarboard"></i> Education</h5>
        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#educationSection">
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="educationSection">
        <div class="card-body">
            <?php if (!empty($education_list)): ?>
                <div class="mb-4">
                    <?php foreach ($education_list as $edu): ?>
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($edu['degree']); ?> in <?php echo htmlspecialchars($edu['field_of_study']); ?></h6>
                                    <p class="mb-1"><strong><?php echo htmlspecialchars($edu['institution']); ?></strong></p>
                                    <p class="mb-1 small text-muted">
                                        <?php echo date('M Y', strtotime($edu['start_date'])); ?> - 
                                        <?php echo date('M Y', strtotime($edu['end_date'])); ?>
                                    </p>
                                    <?php if ($edu['description']): ?>
                                        <p class="mb-0 small"><?php echo nl2br(htmlspecialchars($edu['description'])); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <a href="resume_form.php?id=<?php echo $resume_id; ?>&delete=<?php echo $edu['id']; ?>&delete_section=education" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Delete this education entry?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <button class="btn btn-outline-primary btn-sm mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#addEducation">
                <i class="bi bi-plus-circle"></i> Add Education
            </button>
            
            <div class="collapse" id="addEducation">
                <form method="POST" action="">
                    <input type="hidden" name="section" value="education">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Institution *</label>
                            <input type="text" class="form-control" name="institution" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Degree *</label>
                            <input type="text" class="form-control" name="degree" placeholder="e.g., Bachelor of Science" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Field of Study</label>
                            <input type="text" class="form-control" name="field_of_study" placeholder="e.g., Computer Science">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Start Date *</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">End Date *</label>
                            <input type="date" class="form-control" name="end_date" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Achievements, coursework, etc."></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Education
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Experience Section -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-briefcase"></i> Work Experience</h5>
        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#experienceSection">
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="experienceSection">
        <div class="card-body">
            <?php if (!empty($experience_list)): ?>
                <div class="mb-4">
                    <?php foreach ($experience_list as $exp): ?>
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($exp['position']); ?></h6>
                                    <p class="mb-1"><strong><?php echo htmlspecialchars($exp['company']); ?></strong> 
                                        <?php if ($exp['location']): ?>
                                            - <span class="text-muted"><?php echo htmlspecialchars($exp['location']); ?></span>
                                        <?php endif; ?>
                                    </p>
                                    <p class="mb-1 small text-muted">
                                        <?php echo date('M Y', strtotime($exp['start_date'])); ?> - 
                                        <?php echo $exp['current_job'] ? 'Present' : date('M Y', strtotime($exp['end_date'])); ?>
                                    </p>
                                    <?php if ($exp['description']): ?>
                                        <p class="mb-0 small"><?php echo nl2br(htmlspecialchars($exp['description'])); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <a href="resume_form.php?id=<?php echo $resume_id; ?>&delete=<?php echo $exp['id']; ?>&delete_section=experience" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Delete this experience entry?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <button class="btn btn-outline-primary btn-sm mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#addExperience">
                <i class="bi bi-plus-circle"></i> Add Experience
            </button>
            
            <div class="collapse" id="addExperience">
                <form method="POST" action="">
                    <input type="hidden" name="section" value="experience">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company *</label>
                            <input type="text" class="form-control" name="company" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Position *</label>
                            <input type="text" class="form-control" name="position" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" name="location" placeholder="City, State/Country">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Start Date *</label>
                            <input type="date" class="form-control" name="exp_start_date" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="exp_end_date" id="exp_end_date">
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="current_job" id="current_job" 
                                       onclick="document.getElementById('exp_end_date').disabled = this.checked;">
                                <label class="form-check-label" for="current_job">
                                    I currently work here
                                </label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="exp_description" rows="4" 
                                      placeholder="Describe your responsibilities and achievements"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Experience
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Skills Section -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-tools"></i> Skills</h5>
        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#skillsSection">
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="skillsSection">
        <div class="card-body">
            <?php if (!empty($skills_list)): ?>
                <div class="mb-4">
                    <div class="row g-2">
                        <?php foreach ($skills_list as $skill): ?>
                            <div class="col-md-4">
                                <div class="border rounded p-2 bg-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($skill['skill_name']); ?></strong>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($skill['proficiency']); ?></small>
                                    </div>
                                    <a href="resume_form.php?id=<?php echo $resume_id; ?>&delete=<?php echo $skill['id']; ?>&delete_section=skills" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Delete this skill?');">
                                        <i class="bi bi-x"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <button class="btn btn-outline-primary btn-sm mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#addSkill">
                <i class="bi bi-plus-circle"></i> Add Skill
            </button>
            
            <div class="collapse" id="addSkill">
                <form method="POST" action="">
                    <input type="hidden" name="section" value="skills">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Skill Name *</label>
                            <input type="text" class="form-control" name="skill_name" placeholder="e.g., JavaScript, Project Management" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Proficiency *</label>
                            <select class="form-select" name="proficiency" required>
                                <option value="Beginner">Beginner</option>
                                <option value="Intermediate" selected>Intermediate</option>
                                <option value="Advanced">Advanced</option>
                                <option value="Expert">Expert</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Skill
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Projects Section -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-kanban"></i> Projects</h5>
        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#projectsSection">
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="projectsSection">
        <div class="card-body">
            <?php if (!empty($projects_list)): ?>
                <div class="mb-4">
                    <?php foreach ($projects_list as $project): ?>
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($project['project_name']); ?></h6>
                                    <?php if ($project['technologies']): ?>
                                        <p class="mb-1 small"><strong>Technologies:</strong> <?php echo htmlspecialchars($project['technologies']); ?></p>
                                    <?php endif; ?>
                                    <?php if ($project['start_date'] && $project['end_date']): ?>
                                        <p class="mb-1 small text-muted">
                                            <?php echo date('M Y', strtotime($project['start_date'])); ?> - 
                                            <?php echo date('M Y', strtotime($project['end_date'])); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($project['description']): ?>
                                        <p class="mb-1 small"><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                                    <?php endif; ?>
                                    <?php if ($project['url']): ?>
                                        <p class="mb-0 small">
                                            <a href="<?php echo htmlspecialchars($project['url']); ?>" target="_blank" class="text-decoration-none">
                                                <i class="bi bi-link-45deg"></i> View Project
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <a href="resume_form.php?id=<?php echo $resume_id; ?>&delete=<?php echo $project['id']; ?>&delete_section=projects" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Delete this project?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <button class="btn btn-outline-primary btn-sm mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#addProject">
                <i class="bi bi-plus-circle"></i> Add Project
            </button>
            
            <div class="collapse" id="addProject">
                <form method="POST" action="">
                    <input type="hidden" name="section" value="projects">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Project Name *</label>
                            <input type="text" class="form-control" name="project_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Project URL</label>
                            <input type="url" class="form-control" name="project_url" placeholder="https://...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Technologies Used</label>
                            <input type="text" class="form-control" name="technologies" placeholder="React, Node.js, MongoDB">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="project_start_date">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="project_end_date">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="project_description" rows="3" 
                                      placeholder="Describe the project, your role, and achievements"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Project
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Certifications Section -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-award"></i> Certifications</h5>
        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#certificationsSection">
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="certificationsSection">
        <div class="card-body">
            <?php if (!empty($certifications_list)): ?>
                <div class="mb-4">
                    <?php foreach ($certifications_list as $cert): ?>
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($cert['certification_name']); ?></h6>
                                    <p class="mb-1"><strong><?php echo htmlspecialchars($cert['issuing_organization']); ?></strong></p>
                                    <p class="mb-1 small text-muted">
                                        Issued: <?php echo date('M Y', strtotime($cert['issue_date'])); ?>
                                        <?php if ($cert['expiry_date']): ?>
                                            | Expires: <?php echo date('M Y', strtotime($cert['expiry_date'])); ?>
                                        <?php endif; ?>
                                    </p>
                                    <?php if ($cert['credential_id']): ?>
                                        <p class="mb-1 small">Credential ID: <?php echo htmlspecialchars($cert['credential_id']); ?></p>
                                    <?php endif; ?>
                                    <?php if ($cert['credential_url']): ?>
                                        <p class="mb-0 small">
                                            <a href="<?php echo htmlspecialchars($cert['credential_url']); ?>" target="_blank" class="text-decoration-none">
                                                <i class="bi bi-link-45deg"></i> View Certificate
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <a href="resume_form.php?id=<?php echo $resume_id; ?>&delete=<?php echo $cert['id']; ?>&delete_section=certifications" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Delete this certification?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <button class="btn btn-outline-primary btn-sm mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#addCertification">
                <i class="bi bi-plus-circle"></i> Add Certification
            </button>
            
            <div class="collapse" id="addCertification">
                <form method="POST" action="">
                    <input type="hidden" name="section" value="certifications">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Certification Name *</label>
                            <input type="text" class="form-control" name="certification_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Issuing Organization *</label>
                            <input type="text" class="form-control" name="issuing_organization" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Issue Date *</label>
                            <input type="date" class="form-control" name="issue_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" name="expiry_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Credential ID</label>
                            <input type="text" class="form-control" name="credential_id">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Credential URL</label>
                            <input type="url" class="form-control" name="credential_url" placeholder="https://...">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Certification
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card bg-light">
    <div class="card-body text-center">
        <h5 class="mb-3">Ready to see your resume?</h5>
        <a href="preview.php?id=<?php echo $resume_id; ?>" class="btn btn-success btn-lg" target="_blank">
            <i class="bi bi-eye"></i> Preview & Download Resume
        </a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
