<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];
$selected_template = isset($_GET['template']) ? intval($_GET['template']) : 1;

// Handle template selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['template_id'])) {
    $template_id = intval($_POST['template_id']);
    $_SESSION['selected_template'] = $template_id;
    header('Location: build_resume.php?template=' . $template_id);
    exit;
}

// Professional ATS-Friendly Resume Templates
$templates = [
    1 => [
        'name' => 'Elegant Professional',
        'description' => 'Sophisticated design with clean lines and professional appeal',
        'image' => 'https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=400&h=500&fit=crop',
        'features' => ['ATS Optimized', 'Elegant', 'Professional'],
        'category' => 'Corporate',
        'color' => '#2c3e50',
        'accent' => '#3498db'
    ],
    2 => [
        'name' => 'Modern Minimalist',
        'description' => 'Clean, contemporary design with perfect balance and readability',
        'image' => 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?w=400&h=500&fit=crop',
        'features' => ['ATS Optimized', 'Modern', 'Minimalist'],
        'category' => 'Creative',
        'color' => '#34495e',
        'accent' => '#e74c3c'
    ],
    3 => [
        'name' => 'Executive Elite',
        'description' => 'Premium design for senior executives and leadership positions',
        'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=500&fit=crop',
        'features' => ['ATS Optimized', 'Executive', 'Premium'],
        'category' => 'Executive',
        'color' => '#1a252f',
        'accent' => '#f39c12'
    ],
    4 => [
        'name' => 'Tech Innovator',
        'description' => 'Cutting-edge design perfect for technology and innovation roles',
        'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=400&h=500&fit=crop',
        'features' => ['ATS Optimized', 'Tech', 'Innovative'],
        'category' => 'Technology',
        'color' => '#2c3e50',
        'accent' => '#00d4aa'
    ],
    5 => [
        'name' => 'Creative Vision',
        'description' => 'Artistic design for creative professionals and designers',
        'image' => 'https://images.unsplash.com/photo-1467232004584-a241de8bcf5d?w=400&h=500&fit=crop',
        'features' => ['ATS Optimized', 'Creative', 'Artistic'],
        'category' => 'Creative',
        'color' => '#8e44ad',
        'accent' => '#e67e22'
    ],
    6 => [
        'name' => 'Healthcare Excellence',
        'description' => 'Trustworthy design for healthcare and medical professionals',
        'image' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=400&h=500&fit=crop',
        'features' => ['ATS Optimized', 'Healthcare', 'Trustworthy'],
        'category' => 'Healthcare',
        'color' => '#27ae60',
        'accent' => '#2ecc71'
    ],
    7 => [
        'name' => 'Finance Master',
        'description' => 'Professional design for finance and banking executives',
        'image' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=400&h=500&fit=crop',
        'features' => ['ATS Optimized', 'Finance', 'Executive'],
        'category' => 'Finance',
        'color' => '#2c3e50',
        'accent' => '#f1c40f'
    ],
    8 => [
        'name' => 'Sales Champion',
        'description' => 'Dynamic design that highlights achievements and results',
        'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=500&fit=crop',
        'features' => ['ATS Optimized', 'Sales', 'Achievement Focused'],
        'category' => 'Sales',
        'color' => '#e74c3c',
        'accent' => '#c0392b'
    ],
    9 => [
        'name' => 'Academic Scholar',
        'description' => 'Distinguished design for education and research professionals',
        'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=500&fit=crop',
        'features' => ['ATS Optimized', 'Academic', 'Scholarly'],
        'category' => 'Education',
        'color' => '#8e44ad',
        'accent' => '#9b59b6'
    ],
    10 => [
        'name' => 'Marketing Pro',
        'description' => 'Creative yet professional design for marketing specialists',
        'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400&h=500&fit=crop',
        'features' => ['ATS Optimized', 'Marketing', 'Creative Professional'],
        'category' => 'Marketing',
        'color' => '#f39c12',
        'accent' => '#e67e22'
    ],
    11 => [
        'name' => 'Neutral Elegance',
        'description' => 'Sophisticated neutral design with modern typography',
        'image' => 'https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=400&h=500&fit=crop',
        'features' => ['ATS Optimized', 'Neutral', 'Elegant'],
        'category' => 'Corporate',
        'color' => '#6c757d',
        'accent' => '#495057'
    ],
    12 => [
        'name' => 'Accounting Professional',
        'description' => 'Conservative design perfect for accounting professionals',
        'image' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=400&h=500&fit=crop',
        'features' => ['ATS Optimized', 'Conservative', 'Professional'],
        'category' => 'Finance',
        'color' => '#2c3e50',
        'accent' => '#34495e'
    ],
    13 => [
        'name' => 'Corporate Modern',
        'description' => 'Contemporary corporate design with clean aesthetics',
        'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=500&fit=crop',
        'features' => ['ATS Optimized', 'Corporate', 'Modern'],
        'category' => 'Corporate',
        'color' => '#34495e',
        'accent' => '#2c3e50'
    ],
    14 => [
        'name' => 'Executive Premium',
        'description' => 'Luxury design for C-level executives and senior management',
        'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=500&fit=crop',
        'features' => ['ATS Optimized', 'Executive', 'Luxury'],
        'category' => 'Executive',
        'color' => '#1a252f',
        'accent' => '#2c3e50'
    ],
    15 => [
        'name' => 'Clean Excellence',
        'description' => 'Ultra-clean design that emphasizes content and readability',
        'image' => 'https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=400&h=500&fit=crop',
        'features' => ['ATS Optimized', 'Clean', 'Readable'],
        'category' => 'Corporate',
        'color' => '#495057',
        'accent' => '#6c757d'
    ]
];

$categories = array_unique(array_column($templates, 'category'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Template | Resume Builder</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .templates-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3a4b;
            margin-bottom: 1rem;
        }
        
        .page-subtitle {
            font-size: 1.1rem;
            color: #6b7a8f;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .ats-badge {
            display: inline-block;
            background: #27ae60;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .filter-tabs {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }
        
        .filter-tab {
            padding: 0.6rem 1.2rem;
            border: 2px solid #e0eafc;
            background: white;
            color: #6b7a8f;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .filter-tab.active,
        .filter-tab:hover {
            background: #6ca0dc;
            color: white;
            border-color: #6ca0dc;
        }
        
        .templates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .template-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }
        
        .template-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }
        
        .template-preview {
            height: 300px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            position: relative;
            overflow: hidden;
        }
        
        .template-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .template-card:hover .template-preview img {
            transform: scale(1.05);
        }
        
        .template-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(108, 160, 220, 0.9), rgba(76, 131, 196, 0.9));
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .template-card:hover .template-overlay {
            opacity: 1;
        }
        
        .preview-btn {
            background: white;
            color: #6ca0dc;
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .preview-btn:hover {
            background: #6ca0dc;
            color: white;
        }
        
        .template-info {
            padding: 1.5rem;
        }
        
        .template-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2d3a4b;
            margin-bottom: 0.5rem;
        }
        
        .template-description {
            color: #6b7a8f;
            font-size: 0.95rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        
        .template-features {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .feature-tag {
            background: #f7fafc;
            color: #6b7a8f;
            padding: 0.3rem 0.6rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .template-category {
            display: inline-block;
            background: #e0eafc;
            color: #6ca0dc;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .canva-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            margin-left: 0.5rem;
        }
        
        .select-template-btn {
            width: 100%;
            background: #6ca0dc;
            color: white;
            border: none;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .select-template-btn:hover {
            background: #4f83c4;
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
        
        .back-link.primary {
            color: #ffffff;
            background: #2f6fb3;
            border-color: #2b63a0;
        }
        
        .back-link.primary:hover {
            background: #285f99;
            border-color: #244f80;
            color: #ffffff;
        }
        
        .ats-info {
            background: #e8f5e9;
            border: 1px solid #c8e6c9;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .ats-info h3 {
            color: #2e7d32;
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }
        
        .ats-info p {
            color: #388e3c;
            margin: 0;
            font-size: 0.95rem;
        }
        
        .canva-highlight {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .canva-highlight h3 {
            color: white;
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }
        
        .canva-highlight p {
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            font-size: 0.95rem;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .templates-container {
                padding: 1rem;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .templates-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .filter-tabs {
                gap: 0.3rem;
            }
            
            .filter-tab {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 480px) {
            .page-title {
                font-size: 1.8rem;
            }
            
            .template-preview {
                height: 250px;
            }
            
            .template-info {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="templates-container">
        <a href="dashboard.php" class="back-link primary">
            <i class="fa fa-home"></i> Back to Dashboard
        </a>
        
        <div class="page-header">
            <div class="ats-badge">
                <i class="fa fa-check-circle"></i> All Templates are ATS-Friendly
            </div>
            <h1 class="page-title">Choose Your Resume Template</h1>
            <p class="page-subtitle">
                Select from our collection of professionally designed, ATS-optimized resume templates. 
                Each template is crafted to pass Applicant Tracking Systems while maintaining visual appeal.
            </p>
        </div>
        
        <div class="ats-info">
            <h3><i class="fa fa-shield-alt"></i> ATS-Optimized Design</h3>
            <p>All our templates are designed to work seamlessly with Applicant Tracking Systems, ensuring your resume gets past automated screening and reaches human recruiters.</p>
        </div>
        
        
        <div class="filter-tabs">
            <div class="filter-tab active" data-category="all">All Templates</div>
            <?php foreach ($categories as $category): ?>
                <div class="filter-tab" data-category="<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
                    <?php echo $category; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="templates-grid">
            <?php foreach ($templates as $id => $template): ?>
                <div class="template-card" data-category="<?php echo strtolower(str_replace(' ', '-', $template['category'])); ?>">
                    <div class="template-preview">
                        <img src="<?php echo $template['image']; ?>" alt="<?php echo $template['name']; ?> Preview">
                        <div class="template-overlay">
                            <a href="#" class="preview-btn" onclick="previewTemplate(<?php echo $id; ?>)">
                                <i class="fa fa-eye"></i> Preview
                            </a>
                        </div>
                    </div>
                    <div class="template-info">
                        <div class="template-category"><?php echo $template['category']; ?></div>
                        <h3 class="template-name"><?php echo $template['name']; ?></h3>
                        <p class="template-description"><?php echo $template['description']; ?></p>
                        <div class="template-features">
                            <?php foreach ($template['features'] as $feature): ?>
                                <span class="feature-tag"><?php echo $feature; ?></span>
                            <?php endforeach; ?>
                        </div>
                        <form method="post" style="margin: 0;">
                            <input type="hidden" name="template_id" value="<?php echo $id; ?>">
                            <button type="submit" class="select-template-btn">
                                <i class="fa fa-check"></i> Select This Template
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script>
        // Filter functionality
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Update active tab
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Filter templates
                const category = this.dataset.category;
                document.querySelectorAll('.template-card').forEach(card => {
                    if (category === 'all' || card.dataset.category === category) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
        
        // Preview functionality
        function previewTemplate(templateId) {
            // This would open a modal or new window with template preview
            alert('Template preview feature coming soon! Template ID: ' + templateId);
        }
        
        // Add smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
