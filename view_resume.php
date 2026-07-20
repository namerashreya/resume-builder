<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];
$resume_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$resume_id) {
    header('Location: resume_list.php');
    exit;
}

// Fetch resume owned by current user
$sql = "SELECT id, data, template_id, created_at, updated_at FROM resumes WHERE id = ? AND user_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $resume_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header('Location: resume_list.php');
    exit;
}

$row = $result->fetch_assoc();
$resume_data = json_decode($row['data'], true);
$template_id = $row['template_id'] ?? 1;
$stmt->close();

// Template styles based on template_id
$template_styles = [
    1 => ['name' => 'Elegant Professional', 'color' => '#2c3e50', 'accent' => '#3498db'],
    2 => ['name' => 'Modern Minimalist', 'color' => '#34495e', 'accent' => '#e74c3c'],
    3 => ['name' => 'Executive Elite', 'color' => '#1a252f', 'accent' => '#f39c12'],
    4 => ['name' => 'Tech Innovator', 'color' => '#2c3e50', 'accent' => '#00d4aa'],
    5 => ['name' => 'Creative Vision', 'color' => '#8e44ad', 'accent' => '#e67e22'],
    6 => ['name' => 'Healthcare Excellence', 'color' => '#27ae60', 'accent' => '#2ecc71'],
    7 => ['name' => 'Finance Master', 'color' => '#2c3e50', 'accent' => '#f1c40f'],
    8 => ['name' => 'Sales Champion', 'color' => '#e74c3c', 'accent' => '#c0392b'],
    9 => ['name' => 'Academic Scholar', 'color' => '#8e44ad', 'accent' => '#9b59b6'],
    10 => ['name' => 'Marketing Pro', 'color' => '#f39c12', 'accent' => '#e67e22'],
    11 => ['name' => 'Neutral Elegance', 'color' => '#6c757d', 'accent' => '#495057'],
    12 => ['name' => 'Accounting Professional', 'color' => '#2c3e50', 'accent' => '#34495e'],
    13 => ['name' => 'Corporate Modern', 'color' => '#34495e', 'accent' => '#2c3e50'],
    14 => ['name' => 'Executive Premium', 'color' => '#1a252f', 'accent' => '#2c3e50'],
    15 => ['name' => 'Clean Excellence', 'color' => '#495057', 'accent' => '#6c757d']
];

$current_template = $template_styles[$template_id] ?? $template_styles[1];

// Flash messages
$created_msg = isset($_SESSION['resume_success']) ? $_SESSION['resume_success'] : '';
$edited_msg = isset($_SESSION['edit_success']) ? $_SESSION['edit_success'] : '';
unset($_SESSION['resume_success'], $_SESSION['edit_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Resume | Resume Builder</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .page-container { max-width: 920px; margin: 0 auto; padding: 1.25rem; }
        .actions { display: flex; gap: .6rem; flex-wrap: wrap; margin: .25rem 0 1rem 0; }
        .action-group { display: flex; gap: .5rem; flex-wrap: wrap; margin: .4rem 0 1rem 0; }
        .btn { padding: .6rem .9rem; border-radius: 8px; border: 1px solid #cfd8dc; background: #fff; cursor: pointer; font-weight: 600; color: #2d3a4b; }
        .btn.primary { background: #6ca0dc; color: #fff; border-color: #6ca0dc; }
        .btn:hover { filter: brightness(0.97); }
        .flash { margin: .8rem 0; padding: .8rem 1rem; border-radius: 8px; }
        .flash.success { background: #e8f5e9; color: #256029; border: 1px solid #c8e6c9; }
        /* Canva-Style Resume Layout */
        .resume-card { 
            background: #ffffff; 
            border: 1px solid #e3e7eb; 
            border-radius: 8px; 
            padding: 0; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.4;
        }
        
        .resume-header { 
            background: linear-gradient(135deg, <?php echo $current_template['color']; ?> 0%, <?php echo $current_template['accent']; ?> 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        
        .resume-name { 
            font-size: 2.5rem; 
            font-weight: 700; 
            color: white; 
            margin: 0 0 0.5rem 0;
            letter-spacing: 1px;
        }
        
        .resume-title {
            font-size: 1.2rem;
            color: rgba(255,255,255,0.9);
            margin: 0 0 1.5rem 0;
            font-weight: 300;
        }
        
        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255,255,255,0.9);
            font-size: 0.95rem;
        }
        
        .contact-item i {
            width: 16px;
            text-align: center;
        }
        
        .resume-content {
            padding: 2rem;
        }
        
        .section { 
            margin-bottom: 2rem;
        }
        
        .section h3 { 
            margin: 0 0 1rem 0; 
            color: <?php echo $current_template['color']; ?>; 
            font-size: 1.3rem; 
            font-weight: 600; 
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid <?php echo $current_template['accent']; ?>;
            padding-bottom: 0.5rem;
        }
        
        .experience-item, .education-item, .project-item {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .experience-item:last-child, .education-item:last-child, .project-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }
        
        .item-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: <?php echo $current_template['color']; ?>;
            margin: 0;
        }
        
        .item-company, .item-institution {
            color: <?php echo $current_template['accent']; ?>;
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        .item-duration, .item-year {
            color: #666;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .item-description {
            color: #555;
            line-height: 1.6;
            margin-top: 0.5rem;
        }
        
        .skills-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .skill-tag {
            background: <?php echo $current_template['accent']; ?>;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .summary-text {
            color: #555;
            line-height: 1.6;
            font-size: 1rem;
        }
        
        .project-link {
            color: <?php echo $current_template['accent']; ?>;
            text-decoration: none;
            font-weight: 500;
        }
        
        .project-link:hover {
            text-decoration: underline;
        }
        .back-link { display: inline-flex; align-items: center; gap: .4rem; padding: .5rem .75rem; border: 1px solid #cfd8dc; border-radius: 8px; color: #2f6fb3; background: #f7fbff; text-decoration: none; font-weight: 600; margin-bottom: .8rem; }
        .back-link:hover { background: #eef6ff; border-color: #b7c9dc; color: #225ea8; }
        .back-link i { color: #3b6bb1; }
        .back-link.primary { color: #ffffff; background: #2f6fb3; border-color: #2b63a0; }
        .back-link.primary:hover { background: #285f99; border-color: #244f80; color: #ffffff; }
        .action-link { display: inline-flex; align-items: center; gap: .4rem; padding: .5rem .75rem; border: 1px solid #cfd8dc; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .action-link.primary { color: #1e4e8c; background: #eff6ff; border-color: #cfe0f3; }
        .action-link.primary:hover { background: #e2efff; border-color: #bcd4ee; }
        .action-link.neutral { color: #35506e; background: #f9fbfd; }
        .action-link.neutral:hover { background: #f0f6fb; }
        .action-link.danger { color: #a12727; background: #fff5f5; border-color: #f2c4c4; }
        .action-link.danger:hover { background: #ffecec; border-color: #ebb3b3; }
        /* Responsive Design for Canva-Style Resume */
        @media (max-width: 768px) {
            .page-container {
                padding: 1rem 0.8rem;
                margin: 0.5rem auto;
            }
            
            .resume-card {
                max-width: 100%;
            }
            
            .resume-header {
                padding: 1.5rem;
            }
            
            .resume-name {
                font-size: 2rem;
            }
            
            .resume-title {
                font-size: 1rem;
            }
            
            .contact-info {
                grid-template-columns: 1fr;
                gap: 0.8rem;
            }
            
            .resume-content {
                padding: 1.5rem;
            }
            
            .section h3 {
                font-size: 1.1rem;
            }
            
            .item-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .item-duration, .item-year {
                align-self: flex-start;
            }
            
            .action-group {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .action-link {
                padding: 0.7rem 1rem;
                text-align: center;
                font-size: 0.9rem;
            }
            
            .back-link {
                padding: 0.6rem 0.8rem;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 480px) {
            .page-container {
                padding: 0.8rem 0.6rem;
                margin: 0.25rem auto;
            }
            
            .resume-header {
                padding: 1rem;
            }
            
            .resume-name {
                font-size: 1.8rem;
            }
            
            .resume-title {
                font-size: 0.9rem;
            }
            
            .resume-content {
                padding: 1rem;
            }
            
            .section h3 {
                font-size: 1rem;
            }
            
            .skills-list {
                gap: 0.3rem;
            }
            
            .skill-tag {
                font-size: 0.8rem;
                padding: 0.25rem 0.6rem;
            }
            
            .action-group {
                gap: 0.4rem;
            }
            
            .action-link {
                padding: 0.6rem 0.8rem;
                font-size: 0.85rem;
            }
        }
        
        @media print {
            body { background: #fff; font-size: 12px; }
            .actions, .back-link { display: none !important; }
            .page-container { max-width: 100%; padding: 0; }
            .resume-card { border: none; box-shadow: none; }
        }
    </style>
    <script>
        function downloadAsHtml() {
            const html = document.documentElement.outerHTML;
            const blob = new Blob(["\uFEFF" + html], { type: 'text/html;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'resume-<?php echo $resume_id; ?>.html';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    </script>
    </head>
<body>
    <div class="page-container">
        <div style="display:flex; gap:.5rem; flex-wrap:wrap; align-items:center;">
            <a href="resume_list.php" class="back-link"><i class="fa fa-arrow-left"></i> Back to Resumes</a>
            <a href="dashboard.php" class="back-link primary"><i class="fa fa-home"></i> Back to Dashboard</a>
            <a href="build_resume.php" class="back-link primary"><i class="fa fa-plus"></i> Create New Resume</a>
        </div>
        <div style="height:.35rem"></div>
        <?php if ($created_msg): ?>
            <div class="flash success"><?php echo htmlspecialchars($created_msg); ?></div>
        <?php endif; ?>
        <?php if ($edited_msg): ?>
            <div class="flash success"><?php echo htmlspecialchars($edited_msg); ?></div>
        <?php endif; ?>

        <div class="action-group">
            <a href="view_resume.php?id=<?php echo $resume_id; ?>" class="action-link neutral"><i class="fa fa-eye"></i> View Resume</a>
            <a href="edit_resume.php?id=<?php echo $resume_id; ?>" class="action-link primary"><i class="fa fa-pen"></i> Edit Resume</a>
            <a href="change_template.php?id=<?php echo $resume_id; ?>" class="action-link neutral"><i class="fa fa-palette"></i> Change Template</a>
            <a href="delete_resume.php?id=<?php echo $resume_id; ?>" class="action-link danger" onclick="return confirm('Are you sure you want to delete this resume?');"><i class="fa fa-trash"></i> Delete Resume</a>
            <a href="resume_list.php" class="action-link neutral"><i class="fa fa-list"></i> All Resumes</a>
            <a href="#" class="action-link neutral" onclick="window.print()"><i class="fa fa-print"></i> Print</a>
            <a href="#" class="action-link neutral" onclick="downloadAsHtml()"><i class="fa fa-download"></i> Download</a>
        </div>
        
        <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid <?php echo $current_template['accent']; ?>;">
            <strong>Template:</strong> <?php echo $current_template['name']; ?> 
            <span style="color: #6b7a8f; font-size: 0.9rem;">(ATS-Optimized)</span>
        </div>

        <div class="resume-card">
            <!-- Header Section with Gradient Background -->
            <div class="resume-header">
                <h1 class="resume-name"><?php echo htmlspecialchars($resume_data['personal']['name'] ?? ''); ?></h1>
                <p class="resume-title">Professional Resume</p>
                
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fa fa-envelope"></i>
                        <span><?php echo htmlspecialchars($resume_data['personal']['email'] ?? ''); ?></span>
                    </div>
                    <div class="contact-item">
                        <i class="fa fa-phone"></i>
                        <span><?php echo htmlspecialchars($resume_data['personal']['phone'] ?? ''); ?></span>
                    </div>
                    <div class="contact-item">
                        <i class="fa fa-map-marker-alt"></i>
                        <span><?php echo htmlspecialchars($resume_data['personal']['address'] ?? ''); ?></span>
                    </div>
                </div>
            </div>

            <!-- Content Section -->
            <div class="resume-content">
                <?php if (!empty(trim($resume_data['summary'] ?? ''))): ?>
                    <div class="section">
                        <h3>Professional Summary</h3>
                        <p class="summary-text"><?php echo htmlspecialchars($resume_data['summary']); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($resume_data['experience'])): ?>
                    <div class="section">
                        <h3>Professional Experience</h3>
                        <div class="experience-item">
                            <div class="item-header">
                                <div>
                                    <h4 class="item-title"><?php echo htmlspecialchars($resume_data['experience']['role'] ?? ''); ?></h4>
                                    <p class="item-company"><?php echo htmlspecialchars($resume_data['experience']['company'] ?? ''); ?></p>
                                </div>
                                <span class="item-duration"><?php echo htmlspecialchars($resume_data['experience']['duration'] ?? ''); ?></span>
                            </div>
                            <?php if (!empty(trim($resume_data['experience']['description'] ?? ''))): ?>
                                <p class="item-description"><?php echo htmlspecialchars($resume_data['experience']['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($resume_data['education'])): ?>
                    <div class="section">
                        <h3>Education</h3>
                        <div class="education-item">
                            <div class="item-header">
                                <div>
                                    <h4 class="item-title"><?php echo htmlspecialchars($resume_data['education']['degree'] ?? ''); ?></h4>
                                    <p class="item-institution"><?php echo htmlspecialchars($resume_data['education']['school'] ?? ''); ?></p>
                                </div>
                                <span class="item-year"><?php echo htmlspecialchars($resume_data['education']['year'] ?? ''); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty(trim($resume_data['skills'] ?? ''))): ?>
                    <div class="section">
                        <h3>Skills</h3>
                        <div class="skills-list">
                            <?php 
                            $skills = explode(',', $resume_data['skills']);
                            foreach ($skills as $skill): 
                                $skill = trim($skill);
                                if (!empty($skill)):
                            ?>
                                <span class="skill-tag"><?php echo htmlspecialchars($skill); ?></span>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($resume_data['projects'])): ?>
                    <div class="section">
                        <h3>Projects</h3>
                        <div class="project-item">
                            <div class="item-header">
                                <div>
                                    <h4 class="item-title"><?php echo htmlspecialchars($resume_data['projects']['title'] ?? ''); ?></h4>
                                    <?php if (!empty(trim($resume_data['projects']['link'] ?? ''))): ?>
                                        <a href="<?php echo htmlspecialchars($resume_data['projects']['link']); ?>" target="_blank" class="project-link">View Project</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!empty(trim($resume_data['projects']['desc'] ?? ''))): ?>
                                <p class="item-description"><?php echo htmlspecialchars($resume_data['projects']['desc']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>


