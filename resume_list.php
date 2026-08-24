<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];

// Fetch user's resumes
$sql = "SELECT id, data, created_at FROM resumes WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$show_success = false;
$show_error = false;
$success_msg = '';
$error_msg = '';

if (isset($_SESSION['resume_success'])) {
    $show_success = true;
    $success_msg = $_SESSION['resume_success'];
    unset($_SESSION['resume_success']);
}

if (isset($_SESSION['delete_success'])) {
    $show_success = true;
    $success_msg = $_SESSION['delete_success'];
    unset($_SESSION['delete_success']);
}

if (isset($_SESSION['delete_error'])) {
    $show_error = true;
    $error_msg = $_SESSION['delete_error'];
    unset($_SESSION['delete_error']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Resumes | Resume Builder</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .back-link { display: inline-flex; align-items: center; gap: .4rem; padding: .5rem .75rem; border: 1px solid #cfd8dc; border-radius: 8px; color: #2f6fb3; background: #f7fbff; text-decoration: none; font-weight: 600; margin-bottom: .8rem; }
        .back-link:hover { background: #eef6ff; border-color: #b7c9dc; color: #225ea8; }
        .back-link.primary { color: #ffffff; background: #2f6fb3; border-color: #2b63a0; }
        .back-link.primary:hover { background: #285f99; border-color: #244f80; color: #ffffff; }
        .resume-item {
            background: #f7fafc;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(108,160,220,0.07);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .resume-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(108,160,220,0.12);
        }
        .resume-info h3 {
            color: #2d3a4b;
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }
        .resume-date {
            color: #6b7a8f;
            font-size: 0.9rem;
        }
        .resume-actions a {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            margin-left: 0.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .btn-view {
            background: #6ca0dc;
            color: #fff;
        }
        .btn-view:hover {
            background: #4f83c4;
        }
        .btn-edit {
            background: #f7fafc;
            color: #6b7a8f;
            border: 1px solid #cfd8dc;
        }
        .btn-edit:hover {
            background: #e0eafc;
        }
        .btn-delete {
            background: #ffebee;
            color: #e57373;
            border: 1px solid #ffcdd2;
        }
        .btn-delete:hover {
            background: #ffcdd2;
        }
        .no-resumes {
            text-align: center;
            color: #6b7a8f;
            padding: 3rem 1rem;
        }
        .no-resumes h3 {
            margin-bottom: 1rem;
            color: #2d3a4b;
        }
        .create-first-btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: #6ca0dc;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .create-first-btn:hover {
            background: #4f83c4;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .page-title {
            color: #2d3a4b;
            font-size: 1.8rem;
            font-weight: 600;
        }
        .create-new-btn {
            display: inline-block;
            padding: 0.7rem 1.2rem;
            background: #6ca0dc;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: background 0.2s;
        }
        .create-new-btn:hover {
            background: #4f83c4;
        }
        .success-msg {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            text-align: center;
        }
        .error-msg {
            color: #e57373;
            font-size: 1rem;
            margin-bottom: 1rem;
        }
        
        /* Responsive Design for Resume List */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem 0.8rem;
                margin: 0.5rem auto;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                margin-bottom: 1.5rem;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .create-new-btn {
                padding: 0.8rem 1rem;
                font-size: 0.9rem;
            }
            
            .resume-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                padding: 1.2rem;
            }
            
            .resume-actions {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .resume-actions a {
                flex: 1;
                text-align: center;
                margin-left: 0;
                padding: 0.7rem 0.8rem;
                font-size: 0.85rem;
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
            
            .page-title {
                font-size: 1.3rem;
            }
            
            .create-new-btn {
                padding: 0.7rem 0.9rem;
                font-size: 0.85rem;
            }
            
            .resume-item {
                padding: 1rem;
            }
            
            .resume-info h3 {
                font-size: 1.1rem;
            }
            
            .resume-date {
                font-size: 0.8rem;
            }
            
            .resume-actions {
                flex-direction: column;
            }
            
            .resume-actions a {
                flex: none;
                padding: 0.6rem 0.7rem;
                font-size: 0.8rem;
            }
            
            .no-resumes {
                padding: 2rem 0.5rem;
            }
            
            .no-resumes h3 {
                font-size: 1.2rem;
            }
            
            .create-first-btn {
                padding: 0.7rem 1.2rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <a href="dashboard.php" class="back-link primary"><i class="fa fa-home"></i> Back to Dashboard</a>
        
        <div class="page-header">
            <h2 class="page-title">My Resumes</h2>
            <a href="build_resume.php" class="create-new-btn"><i class="fa fa-plus"></i> Create New Resume</a>
        </div>
        
        <?php if ($show_success) echo '<div class="success-msg">' . $success_msg . '</div>'; ?>
        <?php if ($show_error) echo '<div class="error-msg">' . $error_msg . '</div>'; ?>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): 
                $resume_data = json_decode($row['data'], true);
                $name = $resume_data['personal']['name'] ?? 'Untitled Resume';
                $created_date = date('M j, Y', strtotime($row['created_at']));
            ?>
                <div class="resume-item">
                    <div class="resume-info">
                        <h3><?php echo htmlspecialchars($name); ?></h3>
                        <div class="resume-date">Created: <?php echo $created_date; ?></div>
                    </div>
                    <div class="resume-actions">
                        <a href="view_resume.php?id=<?php echo $row['id']; ?>" class="btn-view">
                            <i class="fa fa-eye"></i> View
                        </a>
                        <a href="edit_resume.php?id=<?php echo $row['id']; ?>" class="btn-edit">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="delete_resume.php?id=<?php echo $row['id']; ?>" class="btn-delete" 
                           onclick="return confirm('Are you sure you want to delete this resume?')">
                            <i class="fa fa-trash"></i> Delete
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-resumes">
                <h3>No Resumes Yet</h3>
                <p>You haven't created any resumes yet. Start building your professional resume now!</p>
                <a href="build_resume.php" class="create-first-btn">
                    <i class="fa fa-plus"></i> Create Your First Resume
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 