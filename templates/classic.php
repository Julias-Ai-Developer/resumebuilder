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
            font-family: 'Georgia', serif;
            line-height: 1.6;
            color: #2c3e50;
            background: #f5f5f5;
        }
        
        .page {
            max-width: 850px;
            margin: 20px auto;
            background: white;
            padding: 50px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            border-bottom: 3px double #004346;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .name {
            font-size: 36px;
            font-weight: bold;
            color: #004346;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .contact {
            font-size: 14px;
            color: #666;
        }
        
        .contact span {
            margin: 0 10px;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 20px;
            color: #004346;
            border-bottom: 2px solid #004346;
            padding-bottom: 5px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .item {
            margin-bottom: 20px;
        }
        
        .item-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .item-title {
            font-weight: bold;
            font-size: 16px;
            color: #2c3e50;
        }
        
        .item-date {
            font-style: italic;
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .item-subtitle {
            color: #34495e;
            margin-bottom: 5px;
        }
        
        .item-description {
            color: #555;
            text-align: justify;
            font-size: 14px;
        }
        
        .skills-list {
            columns: 2;
            column-gap: 30px;
        }
        
        .skill-item {
            margin-bottom: 8px;
            break-inside: avoid;
        }
        
        .skill-name {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .skill-level {
            color: #7f8c8d;
            font-size: 13px;
        }
        
        @media print {
            body {
                background: white;
            }
            
            .page {
                margin: 0;
                box-shadow: none;
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <?php if ($personal_info): ?>
        <div class="header">
            <div class="name"><?php echo htmlspecialchars($personal_info['full_name']); ?></div>
            <div class="contact">
                <?php if ($personal_info['email']): ?>
                    <span><?php echo htmlspecialchars($personal_info['email']); ?></span>
                <?php endif; ?>
                <?php if ($personal_info['phone']): ?>
                    <span>•</span>
                    <span><?php echo htmlspecialchars($personal_info['phone']); ?></span>
                <?php endif; ?>
                <?php if ($personal_info['address']): ?>
                    <span>•</span>
                    <span><?php echo htmlspecialchars($personal_info['address']); ?></span>
                <?php endif; ?>
            </div>
            <?php if ($personal_info['linkedin'] || $personal_info['website']): ?>
            <div class="contact" style="margin-top: 5px;">
                <?php if ($personal_info['linkedin']): ?>
                    <span><?php echo htmlspecialchars($personal_info['linkedin']); ?></span>
                <?php endif; ?>
                <?php if ($personal_info['website']): ?>
                    <?php if ($personal_info['linkedin']): ?><span>•</span><?php endif; ?>
                    <span><?php echo htmlspecialchars($personal_info['website']); ?></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($personal_info['summary']): ?>
        <div class="section">
            <div class="section-title">Professional Summary</div>
            <p class="item-description"><?php echo nl2br(htmlspecialchars($personal_info['summary'])); ?></p>
        </div>
        <?php endif; ?>
        <?php endif; ?>
        
        <?php if (!empty($experience_list)): ?>
        <div class="section">
            <div class="section-title">Professional Experience</div>
            <?php foreach ($experience_list as $exp): ?>
            <div class="item">
                <div class="item-header">
                    <div class="item-title"><?php echo htmlspecialchars($exp['position']); ?></div>
                    <div class="item-date">
                        <?php echo date('M Y', strtotime($exp['start_date'])); ?> - 
                        <?php echo $exp['current_job'] ? 'Present' : date('M Y', strtotime($exp['end_date'])); ?>
                    </div>
                </div>
                <div class="item-subtitle">
                    <strong><?php echo htmlspecialchars($exp['company']); ?></strong>
                    <?php if ($exp['location']): ?>
                        | <?php echo htmlspecialchars($exp['location']); ?>
                    <?php endif; ?>
                </div>
                <?php if ($exp['description']): ?>
                <p class="item-description"><?php echo nl2br(htmlspecialchars($exp['description'])); ?></p>
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
                    <div class="item-date">
                        <?php echo date('M Y', strtotime($edu['start_date'])); ?> - 
                        <?php echo date('M Y', strtotime($edu['end_date'])); ?>
                    </div>
                </div>
                <div class="item-subtitle"><?php echo htmlspecialchars($edu['institution']); ?></div>
                <?php if ($edu['description']): ?>
                <p class="item-description"><?php echo nl2br(htmlspecialchars($edu['description'])); ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($skills_list)): ?>
        <div class="section">
            <div class="section-title">Skills</div>
            <div class="skills-list">
                <?php foreach ($skills_list as $skill): ?>
                <div class="skill-item">
                    <span class="skill-name"><?php echo htmlspecialchars($skill['skill_name']); ?></span>
                    <span class="skill-level">(<?php echo htmlspecialchars($skill['proficiency']); ?>)</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($projects_list)): ?>
        <div class="section">
            <div class="section-title">Projects</div>
            <?php foreach ($projects_list as $project): ?>
            <div class="item">
                <div class="item-header">
                    <div class="item-title"><?php echo htmlspecialchars($project['project_name']); ?></div>
                    <?php if ($project['start_date'] && $project['end_date']): ?>
                    <div class="item-date">
                        <?php echo date('M Y', strtotime($project['start_date'])); ?> - 
                        <?php echo date('M Y', strtotime($project['end_date'])); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if ($project['technologies']): ?>
                <div class="item-subtitle">Technologies: <?php echo htmlspecialchars($project['technologies']); ?></div>
                <?php endif; ?>
                <?php if ($project['description']): ?>
                <p class="item-description"><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($certifications_list)): ?>
        <div class="section">
            <div class="section-title">Certifications</div>
            <?php foreach ($certifications_list as $cert): ?>
            <div class="item">
                <div class="item-header">
                    <div class="item-title"><?php echo htmlspecialchars($cert['certification_name']); ?></div>
                    <div class="item-date">
                        <?php echo date('M Y', strtotime($cert['issue_date'])); ?>
                        <?php if ($cert['expiry_date']): ?>
                            - <?php echo date('M Y', strtotime($cert['expiry_date'])); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="item-subtitle"><?php echo htmlspecialchars($cert['issuing_organization']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>