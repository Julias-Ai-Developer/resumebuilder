<?php
function resumeText($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function resumeDate($date, $format = 'M Y') {
    if (empty($date)) {
        return '';
    }

    $time = strtotime($date);
    return $time ? date($format, $time) : '';
}

function resumePhotoSrc($photoPath, $embed = false) {
    if (empty($photoPath)) {
        return '';
    }

    $fullPath = __DIR__ . '/../' . $photoPath;
    if (!is_file($fullPath)) {
        return '';
    }

    if (!$embed) {
        return $photoPath;
    }

    $mimeType = function_exists('mime_content_type') ? mime_content_type($fullPath) : '';
    if (!$mimeType) {
        $imageInfo = getimagesize($fullPath);
        $mimeType = $imageInfo['mime'] ?? '';
    }

    $imageData = file_get_contents($fullPath);
    if (!$mimeType || $imageData === false) {
        return '';
    }

    return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
}

function resumeSectionTitle($label, $class = '') {
    return '<h2 class="section-title ' . resumeText($class) . '">' . resumeText($label) . '</h2>';
}

function renderResumeSections($data, $includeSummary = true) {
    $html = '';
    $personal = $data['personal_info'];

    if ($includeSummary && $personal && !empty($personal['summary'])) {
        $html .= resumeSectionTitle('About Me', 'summary-title');
        $html .= '<p class="summary-text">' . nl2br(resumeText($personal['summary'])) . '</p>';
    }

    if (!empty($data['experience_list'])) {
        $html .= resumeSectionTitle('Experience', 'experience-title');
        foreach ($data['experience_list'] as $exp) {
            $endDate = !empty($exp['current_job']) ? 'Present' : resumeDate($exp['end_date']);
            $html .= '<div class="timeline-item">';
            $html .= '<div class="item-title">' . resumeText($exp['position']) . '</div>';
            $html .= '<div class="item-subtitle">' . resumeText($exp['company']);
            if (!empty($exp['location'])) {
                $html .= ' - ' . resumeText($exp['location']);
            }
            $html .= '</div>';
            $html .= '<div class="item-date">' . resumeText(resumeDate($exp['start_date']) . ($endDate ? ' - ' . $endDate : '')) . '</div>';
            if (!empty($exp['description'])) {
                $html .= '<p>' . nl2br(resumeText($exp['description'])) . '</p>';
            }
            $html .= '</div>';
        }
    }

    if (!empty($data['education_list'])) {
        $html .= resumeSectionTitle('Education', 'education-title');
        foreach ($data['education_list'] as $edu) {
            $html .= '<div class="timeline-item">';
            $html .= '<div class="item-title">' . resumeText($edu['degree']);
            if (!empty($edu['field_of_study'])) {
                $html .= ' in ' . resumeText($edu['field_of_study']);
            }
            $html .= '</div>';
            $html .= '<div class="item-subtitle">' . resumeText($edu['institution']) . '</div>';
            $html .= '<div class="item-date">' . resumeText(resumeDate($edu['start_date']) . ' - ' . resumeDate($edu['end_date'])) . '</div>';
            if (!empty($edu['description'])) {
                $html .= '<p>' . nl2br(resumeText($edu['description'])) . '</p>';
            }
            $html .= '</div>';
        }
    }

    if (!empty($data['skills_list'])) {
        $html .= resumeSectionTitle('Skills', 'skills-title');
        $html .= '<div class="skills-list">';
        foreach ($data['skills_list'] as $skill) {
            $html .= '<div class="skill-row"><span>' . resumeText($skill['skill_name']) . '</span><small>' . resumeText($skill['proficiency']) . '</small></div>';
        }
        $html .= '</div>';
    }

    if (!empty($data['projects_list'])) {
        $html .= resumeSectionTitle('Projects', 'projects-title');
        foreach ($data['projects_list'] as $project) {
            $html .= '<div class="timeline-item">';
            $html .= '<div class="item-title">' . resumeText($project['project_name']) . '</div>';
            if (!empty($project['technologies'])) {
                $html .= '<div class="item-subtitle">Technologies: ' . resumeText($project['technologies']) . '</div>';
            }
            if (!empty($project['start_date']) || !empty($project['end_date'])) {
                $html .= '<div class="item-date">' . resumeText(resumeDate($project['start_date']) . ' - ' . resumeDate($project['end_date'])) . '</div>';
            }
            if (!empty($project['description'])) {
                $html .= '<p>' . nl2br(resumeText($project['description'])) . '</p>';
            }
            if (!empty($project['url'])) {
                $html .= '<p class="link-line">URL: ' . resumeText($project['url']) . '</p>';
            }
            $html .= '</div>';
        }
    }

    if (!empty($data['certifications_list'])) {
        $html .= resumeSectionTitle('Certifications', 'certifications-title');
        foreach ($data['certifications_list'] as $cert) {
            $html .= '<div class="timeline-item">';
            $html .= '<div class="item-title">' . resumeText($cert['certification_name']) . '</div>';
            $html .= '<div class="item-subtitle">' . resumeText($cert['issuing_organization']) . '</div>';
            $html .= '<div class="item-date">Issued: ' . resumeText(resumeDate($cert['issue_date']));
            if (!empty($cert['expiry_date'])) {
                $html .= ' | Expires: ' . resumeText(resumeDate($cert['expiry_date']));
            }
            $html .= '</div>';
            if (!empty($cert['credential_id'])) {
                $html .= '<p>Credential ID: ' . resumeText($cert['credential_id']) . '</p>';
            }
            if (!empty($cert['credential_url'])) {
                $html .= '<p class="link-line">Certificate: ' . resumeText($cert['credential_url']) . '</p>';
            }
            $html .= '</div>';
        }
    }

    return $html;
}

function renderSidebar($data, $photoSrc, $includeSummary = true) {
    $personal = $data['personal_info'];
    $html = '';

    if ($photoSrc) {
        $html .= '<img src="' . resumeText($photoSrc) . '" alt="Profile photo" class="profile-photo">';
    }

    if ($includeSummary && $personal && !empty($personal['summary'])) {
        $html .= '<div class="side-section"><h3>About Me</h3><p>' . nl2br(resumeText($personal['summary'])) . '</p></div>';
    }

    if ($personal) {
        $html .= '<div class="side-section"><h3>Contact</h3>';
        foreach ([
            'Phone' => $personal['phone'] ?? '',
            'Email' => $personal['email'] ?? '',
            'Address' => $personal['address'] ?? '',
            'LinkedIn' => $personal['linkedin'] ?? '',
            'Website' => $personal['website'] ?? '',
        ] as $label => $value) {
            if ($value !== '') {
                $html .= '<div class="contact-line"><strong>' . resumeText($label) . ':</strong> ' . resumeText($value) . '</div>';
            }
        }
        $html .= '</div>';
    }

    if (!empty($data['skills_list'])) {
        $html .= '<div class="side-section"><h3>Skills</h3><ul>';
        foreach ($data['skills_list'] as $skill) {
            $html .= '<li>' . resumeText($skill['skill_name']) . ' <small>' . resumeText($skill['proficiency']) . '</small></li>';
        }
        $html .= '</ul></div>';
    }

    return $html;
}

function renderResumeDocument($data, $options = []) {
    $resume = $data['resume'];
    $personal = $data['personal_info'];
    $templateId = max(1, min(4, (int) ($resume['template_id'] ?? 1)));
    $embedImages = !empty($options['embed_images']);
    $autoPrint = !empty($options['auto_print']);
    $toolbar = $options['toolbar'] ?? '';
    $displayName = $personal && !empty($personal['full_name']) ? $personal['full_name'] : $resume['title'];
    $subtitle = $resume['title'];
    $photoSrc = resumePhotoSrc($personal['photo_path'] ?? '', $embedImages);
    $hasContent = $personal || !empty($data['education_list']) || !empty($data['experience_list']) || !empty($data['skills_list']) || !empty($data['projects_list']) || !empty($data['certifications_list']);

    $colors = [
        'header' => $resume['header_color'] ?? '#004346',
        'section' => $resume['section_color'] ?? '#004346',
        'accent' => $resume['accent_color'] ?? '#F0EDE5',
        'text' => $resume['text_color'] ?? '#333333',
        'summary' => $resume['summary_color'] ?? '#004346',
        'experience' => $resume['experience_color'] ?? '#004346',
        'education' => $resume['education_color'] ?? '#004346',
        'skills' => $resume['skills_color'] ?? '#004346',
        'projects' => $resume['projects_color'] ?? '#004346',
        'certifications' => $resume['certifications_color'] ?? '#004346',
    ];

    ob_start();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo resumeText($resume['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --header: <?php echo resumeText($colors['header']); ?>;
            --section: <?php echo resumeText($colors['section']); ?>;
            --accent: <?php echo resumeText($colors['accent']); ?>;
            --text: <?php echo resumeText($colors['text']); ?>;
            --summary: <?php echo resumeText($colors['summary']); ?>;
            --experience: <?php echo resumeText($colors['experience']); ?>;
            --education: <?php echo resumeText($colors['education']); ?>;
            --skills: <?php echo resumeText($colors['skills']); ?>;
            --projects: <?php echo resumeText($colors['projects']); ?>;
            --certifications: <?php echo resumeText($colors['certifications']); ?>;
        }
        * { box-sizing: border-box; }
        body { margin: 0; background: #d8d8d8; color: var(--text); font-family: "Segoe UI", Arial, sans-serif; }
        .toolbar { position: fixed; top: 16px; right: 16px; display: flex; gap: 8px; background: #fff; padding: 10px; border-radius: 6px; box-shadow: 0 2px 12px rgba(0,0,0,.2); z-index: 20; }
        .toolbar a, .toolbar button { border: 1px solid #ccd; background: white; padding: 8px 10px; border-radius: 4px; color: #123; text-decoration: none; cursor: pointer; font: inherit; }
        .resume-page { width: 210mm; min-height: 297mm; margin: 24px auto; background: #fff; box-shadow: 0 0 24px rgba(0,0,0,.22); overflow: hidden; position: relative; }
        .resume-grid { display: grid; grid-template-columns: 38% 62%; min-height: 225mm; align-items: stretch; }
        .sidebar { background: #1c1c1c; color: white; padding: 18mm 11mm 14mm; min-width: 0; }
        .main { padding: 16mm 13mm; min-width: 0; }
        .hero { background: var(--header); color: white; padding: 20mm 16mm; display: flex; align-items: center; gap: 16mm; }
        .hero h1 { margin: 0; font-size: 30pt; line-height: 1; text-transform: uppercase; letter-spacing: 1px; overflow-wrap: anywhere; }
        .hero .subtitle { font-size: 15pt; margin-top: 6mm; }
        .profile-photo { width: 43mm; height: 43mm; object-fit: cover; border-radius: 50%; border: 4px solid var(--accent); display: block; margin: 0 auto 10mm; }
        .side-section { margin: 0 0 10mm; break-inside: avoid; }
        .side-section h3 { font-size: 15pt; margin: 0 0 5mm; }
        .side-section p, .side-section li, .contact-line { font-size: 10pt; line-height: 1.42; }
        .side-section ul { margin: 0; padding-left: 6mm; }
        .contact-line { margin-bottom: 3mm; overflow-wrap: anywhere; }
        .section-title { color: var(--section); border-bottom: 2px solid currentColor; font-size: 14pt; margin: 0 0 6mm; padding-bottom: 2mm; text-transform: uppercase; letter-spacing: .5px; break-after: avoid; }
        .summary-title { color: var(--summary); } .experience-title { color: var(--experience); } .education-title { color: var(--education); } .skills-title { color: var(--skills); } .projects-title { color: var(--projects); } .certifications-title { color: var(--certifications); }
        .timeline-item { margin-bottom: 7mm; break-inside: avoid; }
        .item-title { font-weight: 800; font-size: 12pt; overflow-wrap: anywhere; }
        .item-subtitle { color: #444; font-weight: 600; margin-top: 1mm; }
        .item-date { color: #777; font-size: 10pt; margin: 1mm 0 2mm; }
        .timeline-item p, .summary-text { line-height: 1.45; margin: 2mm 0; overflow-wrap: anywhere; }
        .skills-list { display: grid; grid-template-columns: repeat(2, 1fr); gap: 3mm; }
        .skill-row { background: var(--accent); border-left: 4px solid var(--skills); padding: 3mm 4mm; display: flex; justify-content: space-between; align-items: center; gap: 4px; border-radius: 4px; overflow: hidden; min-width: 0; }
        .skill-row span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1; min-width: 0; }
        .skill-row small { white-space: nowrap; flex-shrink: 0; opacity: .75; }
        .link-line { overflow-wrap: anywhere; }
        .empty { min-height: 160mm; display: grid; place-items: center; text-align: center; color: #666; padding: 20mm; }

        .template-1 .hero { position: absolute; top: 34mm; left: 0; right: 0; z-index: 2; min-height: 58mm; padding: 15mm 16mm 12mm 42%; }
        .template-1 .resume-grid { min-height: 297mm; }
        .template-1 .sidebar { padding-top: 94mm; }
        .template-1 .main { padding-top: 98mm; }
        .template-1 .profile-photo { position: absolute; top: 21mm; left: 22mm; width: 58mm; height: 58mm; z-index: 3; border-color: var(--header); background: #111; }
        .template-1 .section-title { background: var(--header); color: white; border: 0; border-radius: 20px; text-align: center; padding: 3mm 8mm; width: min(88mm, 100%); }
        .template-1 .hero h1 { color: #fff; font-size: 31pt; }
        .template-1 .skill-row { border-left-color: var(--header); }

        .template-2 .hero { background: white; color: #2f3a4d; padding: 32mm 16mm 12mm; min-height: 82mm; }
        .template-2 .resume-grid { min-height: 215mm; }
        .template-2 .sidebar { background: #303b4f; padding-top: 18mm; }
        .template-2 .main { padding-top: 10mm; }
        .template-2 .profile-photo { width: 62mm; height: 62mm; border-color: #fff; }
        .template-2 .section-title { border: 0; font-size: 17pt; color: #303b4f; margin-bottom: 7mm; }
        .template-2 .timeline-item { border-left: 2px solid #303b4f; padding-left: 8mm; position: relative; }
        .template-2 .timeline-item::before { content: ""; position: absolute; left: -3.2mm; top: 1mm; width: 5mm; height: 5mm; border-radius: 50%; background: #303b4f; }
        .template-2 .hero h1 { font-size: 33pt; letter-spacing: 3px; }

        .template-3::before { content: ""; position: absolute; top: 0; left: 0; right: 0; height: 24mm; background: linear-gradient(135deg, #ff2b2f 0 72%, #191919 72%); z-index: 1; }
        .template-3 .hero { position: relative; z-index: 2; background: white; color: #050505; padding: 34mm 16mm 14mm 78mm; min-height: 82mm; display: block; }
        .template-3 .hero h1 { font-size: 30pt; line-height: 1.05; font-weight: 900; color: #000; }
        .template-3 .hero .subtitle { margin-top: 4mm; color: #111; font-size: 14pt; text-transform: uppercase; letter-spacing: 1px; }
        .template-3 .resume-grid { grid-template-columns: 39% 61%; min-height: 215mm; padding-top: 0; }
        .template-3 .sidebar { background: #191919; padding: 12mm 10mm 14mm; }
        .template-3 .main { padding: 13mm 13mm 16mm; }
        .template-3 .profile-photo { border-radius: 8px; border-color: #ff5a00; width: 58mm; height: 68mm; margin-top: -58mm; margin-bottom: 12mm; position: relative; z-index: 4; }
        .template-3 .side-section h3 { background: #ff2b2f; color: white; margin-left: -10mm; margin-right: -10mm; padding: 3mm 10mm; text-align: center; text-transform: uppercase; }
        .template-3 .section-title { background: #ff2b2f; color: #fff; border: 0; text-align: center; padding: 3mm 8mm; margin-bottom: 7mm; }
        .template-3 .timeline-item { text-align: left; border-right: 0; border-left: 3px solid #ff2b2f; padding-left: 6mm; padding-right: 0; }
        .template-3 .item-title { font-size: 13pt; color: #000; }
        .template-3 .item-subtitle { color: #222; }
        .template-3 .skill-row { background: #f3f3f3; border-left-color: #ff2b2f; }

        .template-4 .resume-grid { grid-template-columns: 36% 64%; min-height: 297mm; }
        .template-4 .sidebar { background: #202020; padding-top: 18mm; }
        .template-4 .main { padding: 0 0 16mm; }
        .template-4 .hero { background: white; color: #202020; text-align: center; display: block; padding: 18mm 16mm 12mm; }
        .template-4 .hero h1 { font-size: 28pt; letter-spacing: 7px; }
        .template-4 .profile-photo { width: 66mm; height: 66mm; border-color: #ff8b45; background: #ff8b45; }
        .template-4 .section-title { background: #202020; color: white; border: 0; border-radius: 0 18px 18px 0; padding: 4mm 10mm; margin: 0 0 7mm; width: 92%; }
        .template-4 .timeline-item, .template-4 .skills-list { padding-left: 13mm; padding-right: 13mm; }
        .template-4 .side-section h3 { color: #ff8b45; border-bottom: 1px solid #eee; padding-bottom: 3mm; }
        .template-4 .skill-row { background: transparent; border: 0; border-bottom: 6px solid #ff8b45; color: white; padding-left: 0; }

        @page { size: A4; margin: 0; }
        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            body { background: white; }
            .toolbar { display: none; }
            .resume-page { width: 210mm; min-height: 297mm; margin: 0; box-shadow: none; }
        }
        @media screen and (max-width: 900px) {
            .toolbar { position: static; margin: 10px auto; width: fit-content; flex-wrap: wrap; }
            .resume-page { transform: scale(.76); transform-origin: top center; margin-top: 10px; margin-bottom: -65mm; }
        }
        @media screen and (max-width: 680px) {
            .resume-page { transform: scale(.55); margin-bottom: -130mm; }
        }
    </style>
</head>
<body>
<?php echo $toolbar; ?>
<div class="resume-page template-<?php echo $templateId; ?>">
    <?php if (!$hasContent): ?>
        <div class="empty">
            <div><h2>No resume details yet</h2><p>Add your information in the editor, then preview again.</p></div>
        </div>
    <?php elseif ($templateId === 4): ?>
        <div class="resume-grid">
            <aside class="sidebar">
                <?php echo $photoSrc ? '<img src="' . resumeText($photoSrc) . '" alt="Profile photo" class="profile-photo">' : ''; ?>
                <h1><?php echo resumeText($displayName); ?></h1>
                <p class="subtitle"><?php echo resumeText($subtitle); ?></p>
                <?php echo renderSidebar($data, '', false); ?>
            </aside>
            <main class="main">
                <section class="hero">
                    <h1>About Me</h1>
                    <?php echo $personal && !empty($personal['summary']) ? '<p class="summary-text">' . nl2br(resumeText($personal['summary'])) . '</p>' : ''; ?>
                </section>
                <?php echo renderResumeSections($data, false); ?>
            </main>
        </div>
    <?php else: ?>
        <div class="hero">
            <div>
                <h1><?php echo resumeText($displayName); ?></h1>
                <div class="subtitle"><?php echo resumeText($subtitle); ?></div>
            </div>
        </div>
        <div class="resume-grid">
            <aside class="sidebar">
                <?php echo renderSidebar($data, $photoSrc, true); ?>
            </aside>
            <main class="main">
                <?php echo renderResumeSections($data, false); ?>
            </main>
        </div>
    <?php endif; ?>
</div>
<script>
function printOnePage() {
    var page = document.querySelector('.resume-page');
    if (!page) { window.print(); return; }

    // Temporarily expose overflow to measure real content height
    var prevOverflow = page.style.overflow;
    page.style.overflow = 'visible';
    var contentH = page.scrollHeight;
    page.style.overflow = prevOverflow;

    // A4 height in CSS pixels at 96 dpi (~1122 px)
    var a4Px = 297 / 25.4 * 96;

    if (contentH > a4Px) {
        page.style.zoom = a4Px / contentH;
    }

    window.addEventListener('afterprint', function cleanup() {
        page.style.zoom = '';
        window.removeEventListener('afterprint', cleanup);
    });

    window.print();
}
</script>
<?php if ($autoPrint): ?>
<script>
window.addEventListener('load', function () {
    printOnePage();
});
</script>
<?php endif; ?>
</body>
</html>
    <?php
    return ob_get_clean();
}
?>
