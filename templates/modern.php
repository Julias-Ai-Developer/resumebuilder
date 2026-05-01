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
            font-family: 'Segoe UI', 'Helvetica Neue', sans-serif;
            background: #e8e8e8;
            color: #2c3e50;
        }
        
        .resume {
            max-width: 850px;
            margin: 30px auto;
            background: white;
            box-shadow: 0 5px 30px rgba(0,0,0,0.15);
        }
        
        .header {
            background: linear-gradient(135deg, #004346 0%, #006b70 100%);
            padding: 50px;
            position: relative;
            overflow: hidden;
        }
        
        .header:before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(240,237,229,0.1);
            border-radius: 50%;
        }
        
        .header:after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 200px;
            height: 200px;
            background: rgba(240,237,229,0.08);
            border-radius: 50%;
        }
        
        .header-content {
            position: relative;
            z-index: 1;
        }
        
        .name {
            font-size: 42px;
            font-weight: 700;
            color: white;
            margin-bottom: 10px;
            letter-spacing: -1px;
        }
        
        .tagline {
            font-size: 18px;
            color: #F0EDE5;
            margin-bottom: 25px;
        }
        
        .contact-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            color: white;
            font-size: 14px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .icon {
            width: 16px;
            height: 16px;
            display: inline-block;
        }
        
        .content {
            padding: 45px 50px;
        }
        
        .section {
            margin-bottom: 40px;
        }
        
        .section-title {
            font-size: 24px;
            color: #004346;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 600;
        }
        
        .section-title:after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(to right, #004346, transparent);
        }
        
        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .item {
            margin-bottom: 25px;
            position: relative;
            padding-left: 25px;
        }
        
        .item:before {
            content: '';
            position: absolute;
            left: 0;
            top: 5px;
            width: 10px;
            height: 10px;
            background: #004346;
            border-radius: 50%;
        }
        
        .item-title {
            font-size: 17px;
            font-weight: 600;
            color: #004346;
            margin-bottom: 5px;
        }
        
        .item-subtitle {
            font-size: 15px;
            color: #555;
            margin-bottom: 5px;
        }
        
        .item-meta {
            font-size: 13px;
            color: #888;
            margin-bottom: 10px;
        }
        
        .item-description {
            font-size: 14px;
            color: #666;
            line-height: 1.7;
        }
        
        .skills-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
        }
        
        .skill-card {
            background: linear-gradient(135deg, #F0EDE5 0%, #e5e2da 100%);
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #004346;
        }
        
        .skill-name {
            font-weight: 600;
            color: #004346;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .skill-level {
            font-size: 12px;
            color: #666;
        }
        
        .summary-box {
            background: #f8f9fa;
            padding: 25px;
            border-left: 5px solid #004346;
            border-radius: 5px;
            margin-bottom: 40px;
        }
        
        .summary-text {
            font-size: 15px;
            line-height: 1.8;
            color: #555;
        }
        
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            body {
                background: white;
            }

            .resume {
                box-shadow: none;
                margin: 0;
            }
        }
        
        @media (max-width: 768px) {
            .two-column {
                grid-template-columns: 1fr;
            }
            
            .skills-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="resume">
        <?php if ($personal_info): ?>
        <div class="header">
            <div class="header-content">
                <div class="name"><?php echo htmlspecialchars($personal_info['full_name']); ?></div>
                <div class="contact-bar">
                    <?php if ($personal_info['email']): ?>
                    <div class="contact-item">
                        <span class="icon">✉</span>
                        <?php echo htmlspecialchars($personal_info['email']); ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($personal_info['phone']): ?>
                    <div class="contact-item">
                        <span class="icon">☎</span>
                        <?php echo htmlspecialchars($personal_info['phone']); ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($personal_info['address']): ?>
                    <div class="contact-item">
                        <span class="icon">📍</span>
                        <?php echo htmlspecialchars($personal_info['address']); ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($personal_info['linkedin']): ?>
                    <div class="contact-item">
                        <span class="icon">🔗</span>
                        LinkedIn
                    </div>
                    <?php endif; ?>
                    <?php if ($personal_info['website']): ?>
                    <div class="contact-item">
                        <span class="icon">🌐</span>
                        Portfolio
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="content">
            <?php if ($personal_info['summary']): ?>
            <div class="summary-box">
                <div class="summary-text">
                    <?php echo nl2br(htmlspecialchars($personal_info['summary'])); ?>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
            
            <?php if (!empty($experience_list)): ?>
            <div class="section">
                <div class="section-title">💼 Experience</div>
                <?php foreach ($experience_list as $exp): ?>
                <div class="item">
                    <div class="item-title"><?php echo htmlspecialchars($exp['position']); ?></div>
                    <div class="item-subtitle">
                        <?php echo htmlspecialchars($exp['company']); ?>
                        <?php if ($exp['location']): ?>
                            • <?php echo htmlspecialchars($exp['location']); ?>
                        <?php endif; ?>
                    </div>
                    <div class="item-meta">
                        <?php echo date('M Y', strtotime($exp['start_date'])); ?> - 
                        <?php echo $exp['current_job'] ? 'Present' : date('M Y', strtotime($exp['end_date'])); ?>
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
            
            <div class="two-column">
                <?php if (!empty($education_list)): ?>
                <div class="section">
                    <div class="section-title">🎓 Education</div>
                    <?php foreach ($education_list as $edu): ?>
                    <div class="item">
                        <div class="item-title">
                            <?php echo htmlspecialchars($edu['degree']); ?>
                        </div>
                        <div class="item-subtitle"><?php echo htmlspecialchars($edu['institution']); ?></div>
                        <div class="item-meta">
                            <?php echo date('Y', strtotime($edu['start_date'])); ?> - 
                            <?php echo date('Y', strtotime($edu['end_date'])); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($certifications_list) && count($certifications_list) <= 3): ?>
                <div class="section">
                    <div class="section-title">🏆 Certifications</div>
                    <?php foreach ($certifications_list as $cert): ?>
                    <div class="item">
                        <div class="item-title"><?php echo htmlspecialchars($cert['certification_name']); ?></div>
                        <div class="item-subtitle"><?php echo htmlspecialchars($cert['issuing_organization']); ?></div>
                        <div class="item-meta"><?php echo date('Y', strtotime($cert['issue_date'])); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($skills_list)): ?>
            <div class="section">
                <div class="section-title">⚡ Skills</div>
                <div class="skills-grid">
                    <?php foreach ($skills_list as $skill): ?>
                    <div class="skill-card">
                        <div class="skill-name"><?php echo htmlspecialchars($skill['skill_name']); ?></div>
                        <div class="skill-level"><?php echo htmlspecialchars($skill['proficiency']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($projects_list)): ?>
            <div class="section">
                <div class="section-title">🚀 Projects</div>
                <?php foreach ($projects_list as $project): ?>
                <div class="item">
                    <div class="item-title"><?php echo htmlspecialchars($project['project_name']); ?></div>
                    <?php if ($project['technologies']): ?>
                    <div class="item-subtitle"><?php echo htmlspecialchars($project['technologies']); ?></div>
                    <?php endif; ?>
                    <?php if ($project['description']): ?>
                    <div class="item-description">
                        <?php echo nl2br(htmlspecialchars($project['description'])); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($certifications_list) && count($certifications_list) > 3): ?>
            <div class="section">
                <div class="section-title">🏆 Certifications</div>
                <?php foreach ($certifications_list as $cert): ?>
                <div class="item">
                    <div class="item-title"><?php echo htmlspecialchars($cert['certification_name']); ?></div>
                    <div class="item-subtitle"><?php echo htmlspecialchars($cert['issuing_organization']); ?></div>
                    <div class="item-meta"><?php echo date('M Y', strtotime($cert['issue_date'])); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>