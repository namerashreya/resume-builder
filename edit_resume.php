<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];
$resume_id = isset($_GET['id']) ? $_GET['id'] : null;
$success_msg = $error_msg = '';

if (!$resume_id) {
    header('Location: resume_list.php');
    exit;
}

// Fetch existing resume data
$sql = "SELECT data FROM resumes WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $resume_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    header('Location: resume_list.php');
    exit;
}

$row = $result->fetch_assoc();
$resume_data = json_decode($row['data'], true);
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $updated_data = [
        'personal' => [
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'phone' => trim($_POST['phone']),
            'address' => trim($_POST['address'])
        ],
        'summary' => trim($_POST['summary']),
        'education' => [
            'school' => trim($_POST['school']),
            'degree' => trim($_POST['degree']),
            'year' => trim($_POST['year'])
        ],
        'experience' => [
            'company' => trim($_POST['company']),
            'role' => trim($_POST['role']),
            'duration' => trim($_POST['duration']),
            'description' => trim($_POST['exp_desc'])
        ],
        'skills' => trim($_POST['skills']),
        'projects' => [
            'title' => trim($_POST['proj_title']),
            'desc' => trim($_POST['proj_desc']),
            'link' => trim($_POST['proj_link'])
        ]
    ];
    
    $json_data = json_encode($updated_data);
    
    // Update the resume
    $update_sql = "UPDATE resumes SET data = ? WHERE id = ? AND user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('sii', $json_data, $resume_id, $user_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['edit_success'] = 'Resume updated successfully!';
        header('Location: view_resume.php?id=' . $resume_id);
        exit;
    } else {
        $error_msg = 'Something went wrong. Please try again.';
    }
    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Resume | Resume Builder</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .resume-form-section { 
            background: #f7fafc; 
            border-radius: 14px; 
            padding: 1.5rem 1.2rem; }
        .resume-form-section h3 { color: #2d3a4b; margin-bottom: 1rem; font-size: 1.2rem; }
        .resume-form-group { margin-bottom: 1rem; }
        .resume-form-group label { display: block; color: #3a4a5d; margin-bottom: 0.3rem; font-size: 1rem; }
        .resume-form-group input, .resume-form-group textarea, .resume-form-group select { width: 100%; padding: 0.7rem 0.9rem; border: 1px solid #cfd8dc; border-radius: 8px; font-size: 1rem; background: #fff; transition: border 0.2s; }
        .resume-form-group input:focus, .resume-form-group textarea:focus { border-color: #6ca0dc; outline: none; }
        .resume-form-group textarea { min-height: 70px; resize: vertical; }
        .resume-form-btn { width: 100%; padding: 0.9rem; background: #6ca0dc; color: #fff; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; margin-top: 0.7rem; }
        .resume-form-btn:hover { background: #4f83c4; }
        .back-link { display: inline-flex; align-items: center; gap: .4rem; padding: .5rem .75rem; border: 1px solid #cfd8dc; border-radius: 8px; color: #2f6fb3; background: #f7fbff; text-decoration: none; font-weight: 600; margin-bottom: .8rem; }
        .back-link:hover { background: #eef6ff; border-color: #b7c9dc; color: #225ea8; }
        .back-link.primary { color: #ffffff; background: #2f6fb3; border-color: #2b63a0; }
        .back-link.primary:hover { background: #285f99; border-color: #244f80; color: #ffffff; }
        .error-msg { color: #e57373; font-size: 1rem; margin-bottom: 1rem; }
        
        /* Responsive Design for Edit Resume Form */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem 0.8rem;
                margin: 0.5rem auto;
            }
            
            .resume-form-section {
                padding: 1.2rem 1rem;
                margin-bottom: 1.2rem;
            }
            
            .resume-form-section h3 {
                font-size: 1.1rem;
                margin-bottom: 0.8rem;
            }
            
            .resume-form-group {
                margin-bottom: 0.8rem;
            }
            
            .resume-form-group input, 
            .resume-form-group textarea {
                padding: 0.8rem 0.9rem;
                font-size: 16px; /* Prevents zoom on iOS */
            }
            
            .resume-form-group textarea {
                min-height: 80px;
            }
            
            .resume-form-btn {
                padding: 1rem;
                font-size: 1rem;
            }
            
            .back-link {
                padding: 0.6rem 0.8rem;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 480px) {
            .dashboard-container {
                padding: 0.8rem 0.6rem;
                margin: 0.25rem auto;
            }
            
            .resume-form-section {
                padding: 1rem 0.8rem;
                margin-bottom: 1rem;
            }
            
            .resume-form-section h3 {
                font-size: 1rem;
            }
            
            .resume-form-group input, 
            .resume-form-group textarea {
                padding: 0.7rem 0.8rem;
            }
            
            .resume-form-btn {
                padding: 0.9rem;
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div style="display:flex; gap:.5rem; flex-wrap:wrap; align-items:center;">
            <a href="view_resume.php?id=<?php echo $resume_id; ?>" class="back-link"><i class="fa fa-eye"></i> Back to View</a>
            <a href="dashboard.php" class="back-link primary"><i class="fa fa-home"></i> Back to Dashboard</a>
        </div>
        <h2 style="color:#2d3a4b; margin-bottom:1.5rem;">Edit Your Resume</h2>
        <?php if ($error_msg) echo '<div class="error-msg">' . $error_msg . '</div>'; ?>
        <form method="post" autocomplete="off">
            <div class="resume-form-section">
                <h3>Personal Information</h3>
                <div class="resume-form-group">
                    <label>Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($resume_data['personal']['name'] ?? ''); ?>" required>
                </div>
                <div class="resume-form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($resume_data['personal']['email'] ?? ''); ?>" required>
                </div>
                <div class="resume-form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($resume_data['personal']['phone'] ?? ''); ?>" required>
                </div>
                <div class="resume-form-group">
                    <label>Address</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($resume_data['personal']['address'] ?? ''); ?>">
                </div>
            </div>
            <div class="resume-form-section">
                <h3>Summary / About Me</h3>
                <div class="resume-form-group">
                    <textarea name="summary" placeholder="A short professional summary..." required><?php echo htmlspecialchars($resume_data['summary'] ?? ''); ?></textarea>
                </div>
            </div>
            <div class="resume-form-section">
                <h3>Education</h3>
                <div class="resume-form-group">
                    <label>School / University</label>
                    <input type="text" name="school" value="<?php echo htmlspecialchars($resume_data['education']['school'] ?? ''); ?>" required>
                </div>
                <div class="resume-form-group">
                    <label>Degree</label>
                    <input type="text" name="degree" value="<?php echo htmlspecialchars($resume_data['education']['degree'] ?? ''); ?>" required>
                </div>
                <div class="resume-form-group">
                    <label>Year</label>
                    <input type="text" name="year" value="<?php echo htmlspecialchars($resume_data['education']['year'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="resume-form-section">
                <h3>Experience</h3>
                <div class="resume-form-group">
                    <label>Company</label>
                    <input type="text" name="company" value="<?php echo htmlspecialchars($resume_data['experience']['company'] ?? ''); ?>" required>
                </div>
                <div class="resume-form-group">
                    <label>Role</label>
                    <input type="text" name="role" value="<?php echo htmlspecialchars($resume_data['experience']['role'] ?? ''); ?>" required>
                </div>
                <div class="resume-form-group">
                    <label>Duration</label>
                    <input type="text" name="duration" value="<?php echo htmlspecialchars($resume_data['experience']['duration'] ?? ''); ?>" required>
                </div>
                <div class="resume-form-group">
                    <label>Description</label>
                    <textarea name="exp_desc" required><?php echo htmlspecialchars($resume_data['experience']['description'] ?? ''); ?></textarea>
                </div>
            </div>
            <div class="resume-form-section">
                <h3>Skills</h3>
                <div class="resume-form-group">
                    <input type="text" name="skills" placeholder="e.g. HTML, CSS, PHP, MySQL" value="<?php echo htmlspecialchars($resume_data['skills'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="resume-form-section">
                <h3>Projects</h3>
                <div class="resume-form-group">
                    <label>Project Title</label>
                    <input type="text" name="proj_title" value="<?php echo htmlspecialchars($resume_data['projects']['title'] ?? ''); ?>" required>
                </div>
                <div class="resume-form-group">
                    <label>Description</label>
                    <textarea name="proj_desc" required><?php echo htmlspecialchars($resume_data['projects']['desc'] ?? ''); ?></textarea>
                </div>
                <div class="resume-form-group">
                    <label>Link</label>
                    <input type="text" name="proj_link" value="<?php echo htmlspecialchars($resume_data['projects']['link'] ?? ''); ?>">
                </div>
            </div>
            <button type="submit" class="resume-form-btn">Save Changes</button>
        </form>
    </div>
</body>
</html>