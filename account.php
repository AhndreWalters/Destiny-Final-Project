<?php
session_start();
require_once "config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = $error = "";

// Fetch current user data
$sql = "SELECT username, email, name, created_at FROM Users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $username, $email, $name, $created_at);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Basic validation
    if (empty($new_name) || empty($new_email)) {
        $error = "Name and email are required.";
    } else {
        // Check if email is already taken by another user
        $check_sql = "SELECT id FROM Users WHERE email = ? AND id != ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "si", $new_email, $user_id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = "Email is already taken by another account.";
        } else {
            // Handle password change if provided
            if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
                // Verify current password
                $verify_sql = "SELECT password FROM Users WHERE id = ?";
                $verify_stmt = mysqli_prepare($conn, $verify_sql);
                mysqli_stmt_bind_param($verify_stmt, "i", $user_id);
                mysqli_stmt_execute($verify_stmt);
                mysqli_stmt_bind_result($verify_stmt, $hashed_password);
                mysqli_stmt_fetch($verify_stmt);
                mysqli_stmt_close($verify_stmt);
                
                if (!password_verify($current_password, $hashed_password)) {
                    $error = "Current password is incorrect.";
                } elseif ($new_password !== $confirm_password) {
                    $error = "New passwords do not match.";
                } elseif (strlen($new_password) < 6) {
                    $error = "New password must be at least 6 characters.";
                } else {
                    // Update with new password
                    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_sql = "UPDATE Users SET name = ?, email = ?, password = ? WHERE id = ?";
                    $update_stmt = mysqli_prepare($conn, $update_sql);
                    mysqli_stmt_bind_param($update_stmt, "sssi", $new_name, $new_email, $hashed_new_password, $user_id);
                }
            } else {
                // Update without password change
                $update_sql = "UPDATE Users SET name = ?, email = ? WHERE id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "ssi", $new_name, $new_email, $user_id);
            }
            
            // Execute update if no errors
            if (empty($error) && isset($update_stmt)) {
                if (mysqli_stmt_execute($update_stmt)) {
                    $success = "Profile updated successfully!";
                    // Update session and local variables
                    $_SESSION['name'] = $new_name;
                    $_SESSION['email'] = $new_email;
                    $name = $new_name;
                    $email = $new_email;
                } else {
                    $error = "Update failed. Please try again.";
                }
                mysqli_stmt_close($update_stmt);
            }
        }
        mysqli_stmt_close($check_stmt);
    }
}

// Get user stats
$ticket_sql = "SELECT COUNT(*) as ticket_count FROM Tickets WHERE user_id = ?";
$ticket_stmt = mysqli_prepare($conn, $ticket_sql);
mysqli_stmt_bind_param($ticket_stmt, "i", $user_id);
mysqli_stmt_execute($ticket_stmt);
$ticket_result = mysqli_stmt_get_result($ticket_stmt);
$ticket_data = mysqli_fetch_assoc($ticket_result);
mysqli_stmt_close($ticket_stmt);

// Get upcoming events count
$upcoming_sql = "SELECT COUNT(*) as upcoming_count 
                 FROM Tickets T 
                 JOIN Events E ON T.event_id = E.id 
                 WHERE T.user_id = ? AND E.date >= CURDATE()";
$upcoming_stmt = mysqli_prepare($conn, $upcoming_sql);
mysqli_stmt_bind_param($upcoming_stmt, "i", $user_id);
mysqli_stmt_execute($upcoming_stmt);
$upcoming_result = mysqli_stmt_get_result($upcoming_stmt);
$upcoming_data = mysqli_fetch_assoc($upcoming_result);
mysqli_stmt_close($upcoming_stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - StagePass</title>
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .account-wrapper {
            width: 100%;
            padding: 2rem 0;
        }

        .account-header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem;
        }

        .account-header h1 {
            font-size: 2.5rem;
            color: #ff66cc;
            margin-bottom: 1rem;
        }

        .account-header p {
            font-size: 1.1rem;
            color: #b8b8b8;
        }

        .account-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        @media (max-width: 900px) {
            .account-content {
                grid-template-columns: 1fr;
            }
        }

        .profile-section {
            background: rgba(30, 30, 30, 0.8);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .profile-section h2 {
            color: #ff66cc;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #e0e0e0;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            background: rgba(50, 50, 50, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #9b59b6;
            box-shadow: 0 0 0 2px rgba(155, 89, 182, 0.2);
        }

        .form-control:disabled {
            background: rgba(50, 50, 50, 0.5);
            color: #888;
            cursor: not-allowed;
        }

        small {
            display: block;
            margin-top: 0.3rem;
            color: #888;
            font-size: 0.85rem;
        }

        .account-stats {
            background: rgba(30, 30, 30, 0.8);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .account-stats h2 {
            color: #ff66cc;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            text-align: center;
            padding: 1.5rem;
            background: rgba(40, 40, 40, 0.5);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #ff66cc;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #b8b8b8;
            font-size: 0.9rem;
        }

        .account-actions {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .account-btn {
            padding: 1rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
        }

        .account-btn.primary {
            background: linear-gradient(135deg, #ff66cc, #9b59b6);
            color: white;
        }

        .account-btn.primary:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #ff4da6, #8e44ad);
        }

        .account-btn.secondary {
            background: transparent;
            border: 1px solid #9b59b6;
            color: #9b59b6;
        }

        .account-btn.secondary:hover {
            background: rgba(155, 89, 182, 0.1);
        }

        .account-btn.logout {
            background: transparent;
            border: 1px solid #e74c3c;
            color: #e74c3c;
        }

        .account-btn.logout:hover {
            background: rgba(231, 76, 60, 0.1);
        }

        .password-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .password-section h3 {
            color: #9b59b6;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            text-align: center;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #ff66cc, #9b59b6);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #ff4da6, #8e44ad);
        }

        @media (max-width: 768px) {
            .account-header h1 {
                font-size: 2rem;
            }
            
            .account-content {
                padding: 0;
            }
            
            .profile-section,
            .account-stats {
                padding: 1.5rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'navigation.php'; ?>
        
        <main class="account-wrapper">
            <div class="account-header">
                <h1>My Account</h1>
                <p>Manage your profile and preferences</p>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success" style="max-width: 800px; margin: 0 auto 2rem;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error" style="max-width: 800px; margin: 0 auto 2rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="account-content">
                <!-- Profile Information -->
                <div class="profile-section">
                    <h2>Profile Information</h2>
                    <form method="POST" action="account.php">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($name); ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($email); ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($username); ?>" 
                                   disabled>
                            <small>Username cannot be changed</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Member Since</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="<?php echo date('F j, Y', strtotime($created_at)); ?>" 
                                   disabled>
                        </div>

                        <!-- Password Change Section -->
                        <div class="password-section">
                            <h3>Change Password</h3>
                            
                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" id="current_password" name="current_password" 
                                       class="form-control" 
                                       placeholder="Leave blank to keep current password">
                            </div>
                            
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" id="new_password" name="new_password" 
                                       class="form-control" 
                                       placeholder="Enter new password (min 6 characters)">
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" 
                                       class="form-control" 
                                       placeholder="Confirm new password">
                            </div>
                        </div>

                        <button type="submit" class="submit-btn">
                            Save Changes
                        </button>
                    </form>
                </div>

                <!-- Account Stats & Actions -->
                <div class="account-stats">
                    <h2>Account Overview</h2>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $ticket_data['ticket_count']; ?></div>
                            <div class="stat-label">Total Tickets</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $upcoming_data['upcoming_count']; ?></div>
                            <div class="stat-label">Upcoming Events</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">Member</div>
                            <div class="stat-label">Account Type</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">
                                <?php 
                                $years = date('Y') - date('Y', strtotime($created_at));
                                echo $years > 0 ? $years . '+' : 'New';
                                ?>
                            </div>
                            <div class="stat-label">
                                <?php echo $years > 0 ? 'Years with us' : 'Member'; ?>
                            </div>
                        </div>
                    </div>

                    <div class="account-actions">
                        <a href="tickets.php" class="account-btn primary">
                            View My Tickets
                        </a>
                        <a href="events.php" class="account-btn secondary">
                            Browse Events
                        </a>
                        <a href="logout.php" class="account-btn logout" 
                           onclick="return confirm('Are you sure you want to logout?')">
                            Logout
                        </a>
                    </div>

                    <!-- Account Status -->
                    <div style="margin-top: 2rem; padding: 1.5rem; background: rgba(255, 102, 204, 0.1); border-radius: 10px; border: 1px solid rgba(255, 102, 204, 0.2);">
                        <h3 style="color: #ff66cc; margin-bottom: 0.5rem; font-size: 1.1rem; text-align: center;">
                            Account Status
                        </h3>
                        <p style="color: #b8b8b8; text-align: center; font-size: 0.9rem; margin: 0;">
                            <span style="color: #2ecc71;">✓ Active</span> • 
                            <span style="color: #2ecc71;">✓ Verified</span> • 
                            <span>Member since <?php echo date('M Y', strtotime($created_at)); ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </main>
        
        <?php include 'footer.php'; ?>
    </div>
</body>
</html>