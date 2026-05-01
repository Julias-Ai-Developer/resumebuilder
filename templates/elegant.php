<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($resume['title']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            background: #f9f9f9;
            color: #333;
        }
        
        .container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            display: flex;
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
        }
        
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #004346 0%, #005f63 100%);
            color: white;
            padding: 40px 30px;
        }
        
        .main-content {
            flex: 1;
            padding: 40px;
        }
        
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #F0EDE5;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: bold;
            color: #004346;
        }
        
        .name {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
            border-bottom: 2px solid #F0EDE5;
            padding-bottom: 15px;
        }
        
        .sidebar-section {
            margin-bottom: 30px;
        }
        
        .sidebar-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(240,237,229,0.3);
            padding-bottom: 8px;
        }
        
        .contact-item {
            margin-bottom: 12px;
            font-size: 13px;
            word-break: break-all;
        }
        
        .contact-icon {
            margin-right: 8px;
        }
        
        .skill-item {
            margin-bottom: 15px;
        }
        
        .skill-name {
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .skill-bar {
            height: 6px;
            background: rgba(240,237,229,0.3);
            border-radius: 3px;
            overflow: hidden;
        }
        
        .skill-fill {
            height: 100%;
            background: #F0EDE5;
        }
        
        .main-title {
            font-size: 32px;
            color: #004346;
            margin-bottom: 10px;
        }
        
        .main-subtitle {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
            font-style: italic;
        }
        
        .section {
            margin-bottom: 35px;
        }
        
        .section-title {
            font-size: 22px;
            color: #004346;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #F0EDE5;
            position: relative;
        }
        
        .section-title:before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
            background: #004346;
        }
        
        .item {
            margin-bottom: 25px;
            padding-left: 20px;
            border-left: 3px solid #F0EDE5;
        }
        
        .item-header {
            margin-bottom: 8px;
        }
        
        .item-title {
            font-size: 18px;
            font-weight: bold;
            color: #004346;
        }
        
        .item-company {
            font-size: 16px;
            color: #555;
            margin-top: 3px;
        }
        
        .item-date {
            font-size: 14px;
            color: #888;
            font-style: italic;
            margin-top: 3px;
        }
        
        .item-description {
            margin-top: 10px;
            color: #555;
            line-height: 1.7;
            font-size: 14px;
        }
        
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            body {
                background: white;
            }

            .container {
                box-shadow: none;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <?php if ($personal_info): ?>
            <div class="profile-img">
                <?php echo strtoupper(substr($personal_info['full_name'], 0, 1)); ?>
            </div>
            
            <div class="name"><?php echo htmlspecialchars($personal_info['full_name']); ?></div>
            
            <div class="sidebar-section">
                <div class="sidebar-title">Contact</div>
                <?php if ($personal_info['email']): ?>
                <div class="contact-item">
                    <span class="contact-icon">✉</span>
                    <?php echo htmlspecialchars($personal_info['email']); ?>
                </div>
                <?php endif; ?>
                <?php if ($personal_info['phone']): ?>
                <div class="contact-item">
                    <span class="contact-icon">☎</span>
                    <?php echo htmlspecialchars($personal_info['phone']); ?>
                </div>
                <?php endif; ?>
                <?php if ($personal_info['address']): ?>
                <div class="contact-item">
                    <span class="contact-icon">📍</span>
                    <?php echo htmlspecialchars($personal_info['address']); ?>
                </div>
                <?php endif; ?>
                <?php if ($personal_info['linkedin']): ?>
                <div class="contact-item">
                    <span class="contact-icon">🔗</span>
                    <?php echo htmlspecialchars($personal_info['linkedin']); ?>
                </div>
                <?php endif; ?>
                <?php if ($personal_info['website']): ?>
                <div class="contact-item">
                    <span class="contact-icon">🌐</span>
                    <?php echo htmlspecialchars($personal_info['website']); ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($skills_list)): ?>
            <div class="sidebar-section">
                <div class="sidebar-title">Skills</div>
                <?php 
                $skill_percentages = [
                    'Beginner' => 25,
                    'Intermediate' => 50,
                    'Advanced' => 75,
                    'Expert' => 100
                ];
                foreach ($skills_list as $skill): 
                    $percentage = $skill_percentages[$skill['proficiency']] ?? 50;
                ?>
                <div class="skill-item">
                    <div class="skill-name"><?php echo htmlspecialchars($skill['skill_name']); ?></div>
                    <div class="skill-bar">
                        <div class="skill-fill" style="width: <?php echo $percentage; ?>%;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($certifications_list)): ?>
            <div class="sidebar-section">
                <div class="sidebar-title">Certifications</div>
                <?php foreach ($certifications_list as $cert): ?>
                <div style="margin-bottom: 15px;">
                    <div style="font-size: 14px; font-weight: bold;">
                        <?php echo htmlspecialchars($cert['certification_name']); ?>
                    </div>
                    <div style="font-size: 12px; margin-top: 3px;">
                        <?php echo htmlspecialchars($cert['issuing_organization']); ?>
                    </div>
                    <div style="font-size: 11px; margin-top: 2px; opacity: 0.8;">
                        <?php echo date('Y', strtotime($cert['issue_date'])); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="main-content">
            <?php if ($personal_info && $personal_info['summary']): ?>
            <div class="section">
                <div class="main-subtitle" style="margin-top: 0;">
                    <?php echo nl2br(htmlspecialchars($personal_info['summary'])); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($experience_list)): ?>
            <div class="section">
                <div class="section-title">Professional Experience</div>
                <?php foreach ($experience_list as $exp): ?>
                <div class="item">
                    <div class="item-header">
                        <div class="item-title"><?php echo htmlspecialchars($exp['position']); ?></div>
                        <div class="item-company">
                            <?php echo htmlspecialchars($exp['company']); ?>
                            <?php if ($exp['location']): ?>
                                | <?php echo htmlspecialchars($exp['location']); ?>
                            <?php endif; ?>
                        </div>
                        <div class="item-date">
                            <?php echo date('F Y', strtotime($exp['start_date'])); ?> - 
                            <?php echo $exp['current_job'] ? 'Present' : date('F Y', strtotime($exp['end_date'])); ?>
                        </div>
                    </div>
                    <?php if ($exp['description']): ?>
                    <div class="item-description">
                        <?php echo nl2br(htmlspecialchars($exp['description'])); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($education_list)): ?>
            <div class="section">
                <div class="section-title">Education</div>
                <?php foreach ($education_list as $edu): ?>
                <div class="item">
                    <div class="item-header">
                        <div class="item-title">
                            <?php echo htmlspecialchars($edu['degree']); ?>
                            <?php if ($edu['field_of_study']): ?>
                                in <?php echo htmlspecialchars($edu['field_of_study']); ?>
                            <?php endif; ?>
                        </div>
                        <div class="item-company"><?php echo htmlspecialchars($edu['institution']); ?></div>
                        <div class="item-date">
                            <?php echo date('F Y', strtotime($edu['start_date'])); ?> - 
                            <?php echo date('F Y', strtotime($edu['end_date'])); ?>
                        </div>
                    </div>
                    <?php if ($edu['description']): ?>
                    <div class="item-description">
                        <?php echo nl2br(htmlspecialchars($edu['description'])); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($projects_list)): ?>
            <div class="section">
                <div class="section-title">Projects</div>
                <?php foreach ($projects_list as $project): ?>
                <div class="item">
                    <div class="item-header">
                        <div class="item-title"><?php echo htmlspecialchars($project['project_name']); ?></div>
                        <?php if ($project['technologies']): ?>
                        <div class="item-company">Technologies: <?php echo htmlspecialchars($project['technologies']); ?></div>
                        <?php endif; ?>
                        <?php if ($project['start_date'] && $project['end_date']): ?>
                        <div class="item-date">
                            <?php echo date('F Y', strtotime($project['start_date'])); ?> - 
                            <?php echo date('F Y', strtotime($project['end_date'])); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($project['description']): ?>
                    <div class="item-description">
                        <?php echo nl2br(htmlspecialchars($project['description'])); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>