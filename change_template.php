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

// Fetch current resume data
$sql = "SELECT data, template_id FROM resumes WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $resume_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header('Location: resume_list.php');
    exit;
}

$row = $result->fetch_assoc();
$current_template_id = $row['template_id'];
$stmt->close();

// Handle template change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['template_id'])) {
    $new_template_id = intval($_POST['template_id']);
    
    // Update template in database
    $update_sql = "UPDATE resumes SET template_id = ? WHERE id = ? AND user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('iii', $new_template_id, $resume_id, $user_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['template_changed'] = 'Template updated successfully!';
        header('Location: view_resume.php?id=' . $resume_id);
        exit;
    }
    $update_stmt->close();
}

// Template definitions (same as templates.php)
$templates = [
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Template | Resume Builder</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .template-change-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3a4b;
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            color: #6b7a8f;
            font-size: 1rem;
        }
        
        .current-template {
            background: #f8f9fa;
            border: 2px solid #6ca0dc;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .current-template h3 {
            color: #2d3a4b;
            margin-bottom: 0.5rem;
        }
        
        .templates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .template-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }
        
        .template-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .template-card.selected {
            border-color: #6ca0dc;
            box-shadow: 0 0 0 3px rgba(108, 160, 220, 0.2);
        }
        
        .template-preview {
            height: 200px;
            background: #ffffff;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .template-preview-content {
            padding: 15px;
            height: 100%;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
        }
        
        .preview-header {
            background: linear-gradient(135deg, var(--template-color) 0%, var(--template-accent) 100%);
            color: white;
            padding: 8px 12px;
            margin: -15px -15px 10px -15px;
            text-align: center;
        }
        
        .preview-name {
            font-weight: bold;
            font-size: 11px;
            margin: 0;
        }
        
        .preview-title {
            font-size: 8px;
            opacity: 0.9;
            margin: 2px 0 0 0;
        }
        
        .preview-section {
            margin-bottom: 8px;
        }
        
        .preview-section-title {
            font-weight: bold;
            color: var(--template-color);
            font-size: 9px;
            margin-bottom: 3px;
            text-transform: uppercase;
            border-bottom: 1px solid var(--template-accent);
            padding-bottom: 1px;
        }
        
        .preview-text {
            font-size: 8px;
            color: #555;
            margin: 0;
        }
        
        .preview-contact {
            display: flex;
            gap: 8px;
            font-size: 7px;
            color: #666;
            margin-bottom: 8px;
        }
        
        .preview-item {
            margin-bottom: 5px;
        }
        
        .preview-item-title {
            font-weight: bold;
            font-size: 8px;
            color: var(--template-color);
        }
        
        .preview-item-company {
            font-size: 7px;
            color: var(--template-accent);
        }
        
        .template-info {
            padding: 1.5rem;
        }
        
        .template-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3a4b;
            margin-bottom: 0.5rem;
        }
        
        .template-description {
            color: #6b7a8f;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .template-colors {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .color-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.6rem 1rem;
            border: 1px solid #cfd8dc;
            border-radius: 8px;
            color: #2f6fb3;
            background: #f7fbff;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            background: #eef6ff;
            border-color: #b7c9dc;
            color: #225ea8;
        }
        
        .change-btn {
            width: 100%;
            background: #6ca0dc;
            color: white;
            border: none;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-top: 1rem;
        }
        
        .change-btn:hover {
            background: #4f83c4;
        }
        
        .change-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        @media (max-width: 768px) {
            .template-change-container {
                padding: 1rem;
            }
            
            .templates-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="template-change-container">
        <a href="view_resume.php?id=<?php echo $resume_id; ?>" class="back-link">
            <i class="fa fa-arrow-left"></i> Back to Resume
        </a>
        
        <div class="page-header">
            <h1 class="page-title">Change Resume Template</h1>
            <p class="page-subtitle">Select a new template design for your resume. Your content will remain unchanged.</p>
        </div>
        
        <div class="current-template">
            <h3>Current Template</h3>
            <p><strong><?php echo $templates[$current_template_id]['name']; ?></strong></p>
        </div>
        
        <form method="post" id="templateForm">
            <input type="hidden" name="template_id" id="selectedTemplate" value="<?php echo $current_template_id; ?>">
            
            <div class="templates-grid">
                <?php foreach ($templates as $id => $template): ?>
                    <div class="template-card <?php echo $id == $current_template_id ? 'selected' : ''; ?>" 
                         data-template-id="<?php echo $id; ?>">
                        <div class="template-preview" style="--template-color: <?php echo $template['color']; ?>; --template-accent: <?php echo $template['accent']; ?>;">
                            <div class="template-preview-content">
                                <div class="preview-header">
                                    <h3 class="preview-name">John Smith</h3>
                                    <p class="preview-title">Software Developer</p>
                                </div>
                                
                                <div class="preview-contact">
                                    <span>📧 john@email.com</span>
                                    <span>📱 (555) 123-4567</span>
                                    <span>📍 New York, NY</span>
                                </div>
                                
                                <div class="preview-section">
                                    <div class="preview-section-title">Professional Summary</div>
                                    <p class="preview-text">Experienced developer with 5+ years in web development...</p>
                                </div>
                                
                                <div class="preview-section">
                                    <div class="preview-section-title">Experience</div>
                                    <div class="preview-item">
                                        <div class="preview-item-title">Senior Developer</div>
                                        <div class="preview-item-company">Tech Corp • 2020-2023</div>
                                    </div>
                                </div>
                                
                                <div class="preview-section">
                                    <div class="preview-section-title">Education</div>
                                    <div class="preview-item">
                                        <div class="preview-item-title">Computer Science</div>
                                        <div class="preview-item-company">University • 2018</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="template-info">
                            <h3 class="template-name"><?php echo $template['name']; ?></h3>
                            <p class="template-description">Professional ATS-optimized design</p>
                            <div class="template-colors">
                                <div class="color-dot" style="background: <?php echo $template['color']; ?>;"></div>
                                <div class="color-dot" style="background: <?php echo $template['accent']; ?>;"></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button type="submit" class="change-btn" id="changeBtn" disabled>
                <i class="fa fa-check"></i> Apply Selected Template
            </button>
        </form>
    </div>
    
    <script>
        let selectedTemplate = <?php echo $current_template_id; ?>;
        
        document.querySelectorAll('.template-card').forEach(card => {
            card.addEventListener('click', function() {
                // Remove selected class from all cards
                document.querySelectorAll('.template-card').forEach(c => c.classList.remove('selected'));
                
                // Add selected class to clicked card
                this.classList.add('selected');
                
                // Update selected template
                selectedTemplate = this.dataset.templateId;
                document.getElementById('selectedTemplate').value = selectedTemplate;
                
                // Enable change button
                document.getElementById('changeBtn').disabled = false;
            });
        });
        
        // Initialize button state
        document.getElementById('changeBtn').disabled = selectedTemplate == <?php echo $current_template_id; ?>;
    </script>
</body>
</html>
