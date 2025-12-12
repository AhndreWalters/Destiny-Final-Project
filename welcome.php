<?php
session_start();
require_once "config.php";

// Redirect if not logged in
if (!isset($_SESSION["user_id"])) {
    header("location: login.php");
    exit;
}

// Get user info
$user_id = $_SESSION["user_id"];
$sql = "SELECT name, username, email, created_at FROM Users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $name, $username, $email, $created_at);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Get upcoming events count
$events_sql = "SELECT COUNT(*) as count FROM Events WHERE date >= CURDATE()";
$events_result = mysqli_query($conn, $events_sql);
$events_count = mysqli_fetch_assoc($events_result)['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to StagePass</title>
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .welcome-wrapper {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            width: 100%;
        }

        .welcome-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .confetti {
            position: fixed;
            width: 15px;
            height: 15px;
            background: #ff66cc;
            border-radius: 50%;
            animation: confetti-fall linear forwards;
            z-index: 1000;
        }

        @keyframes confetti-fall {
            to { transform: translateY(100vh) rotate(360deg); opacity: 0; }
        }

        .welcome-card {
            background: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 4rem;
            border: 2px solid rgba(255, 102, 204, 0.3);
            box-shadow: 
                0 40px 80px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(255, 102, 204, 0.1) inset;
            position: relative;
            overflow: hidden;
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,102,204,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .welcome-header {
            position: relative;
            z-index: 1;
            margin-bottom: 3rem;
        }

        .welcome-header h1 {
            font-size: 3.5rem;
            background: linear-gradient(135deg, #ff66cc, #9b59b6, #ff66cc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            animation: welcome-text 2s ease;
        }

        @keyframes welcome-text {
            0% { transform: translateY(-30px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        .welcome-header p {
            font-size: 1.3rem;
            color: #b8b8b8;
            margin-bottom: 0.5rem;
        }

        .username {
            color: #ff66cc;
            font-weight: 700;
            font-size: 1.4rem;
        }

        .member-since {
            color: #9b59b6;
            font-size: 1rem;
            margin-top: 1rem;
        }

        .welcome-content {
            position: relative;
            z-index: 1;
            margin-bottom: 3rem;
        }

        .welcome-icon {
            font-size: 5rem;
            color: #ff66cc;
            margin-bottom: 2rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .success-message {
            font-size: 1.2rem;
            color: #e0e0e0;
            line-height: 1.8;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1.5rem;
            margin: 3rem auto;
            max-width: 600px;
        }

        .stat-box {
            background: rgba(255, 102, 204, 0.1);
            padding: 1.5rem;
            border-radius: 15px;
            border: 1px solid rgba(255, 102, 204, 0.2);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #ff66cc;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #b8b8b8;
            font-size: 0.9rem;
        }

        .welcome-actions {
            position: relative;
            z-index: 1;
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 3rem;
        }

        .action-btn {
            padding: 1.2rem 2.5rem;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            min-width: 200px;
            justify-content: center;
        }

        .action-btn.primary {
            background: linear-gradient(135deg, #ff66cc, #9b59b6);
            color: white;
            box-shadow: 0 8px 25px rgba(255, 102, 204, 0.4);
        }

        .action-btn.secondary {
            background: transparent;
            border: 2px solid #9b59b6;
            color: #9b59b6;
        }

        .action-btn:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 30px rgba(255, 102, 204, 0.5);
        }

        .quick-links {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .quick-links h3 {
            color: #ff66cc;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
        }

        .links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            max-width: 500px;
            margin: 0 auto;
        }

        .link-item {
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            color: #b8b8b8;
            text-decoration: none;
            transition: all 0.3s ease;
            text-align: center;
        }

        .link-item:hover {
            background: rgba(255, 102, 204, 0.1);
            color: #ff66cc;
            transform: translateY(-3px);
        }

        @media (max-width: 768px) {
            .welcome-card {
                padding: 2.5rem 1.5rem;
                margin: 1rem;
            }
            
            .welcome-header h1 {
                font-size: 2.5rem;
            }
            
            .welcome-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .action-btn {
                width: 100%;
                max-width: 300px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .welcome-icon {
                font-size: 4rem;
            }
        }

        @media (max-width: 480px) {
            .welcome-header h1 {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .links-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'navigation.php'; ?>
        
        <main class="welcome-wrapper">
            <div class="welcome-container">
                <div class="welcome-card">
                    <div class="welcome-header">
                        <div class="welcome-icon">🎉</div>
                        <h1>Welcome to StagePass!</h1>
                        <p>Congratulations <span class="username"><?php echo htmlspecialchars($name); ?></span>!</p>
                        <p>Your account has been successfully created.</p>
                        <p class="member-since">
                            Member since <?php echo date('F j, Y', strtotime($created_at)); ?>
                        </p>
                    </div>
                    
                    <div class="welcome-content">
                        <p class="success-message">
                            🎊 Welcome to the StagePass family! You now have access to exclusive events, 
                            VIP experiences, and premium features. Start exploring and book your first 
                            unforgettable experience today!
                        </p>
                        
                        <div class="stats-grid">
                            <div class="stat-box">
                                <div class="stat-number"><?php echo $events_count; ?></div>
                                <div class="stat-label">Upcoming Events</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">50+</div>
                                <div class="stat-label">Event Categories</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">⭐</div>
                                <div class="stat-label">VIP Access</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">24/7</div>
                                <div class="stat-label">Support</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="welcome-actions">
                        <a href="events.php" class="action-btn primary">
                            🎭 Browse Events
                        </a>
                        <a href="featured.php" class="action-btn secondary">
                            ⭐ Featured Events
                        </a>
                    </div>
                    
                    <div class="quick-links">
                        <h3>Quick Start Guide</h3>
                        <div class="links-grid">
                            <a href="account.php" class="link-item">👤 Your Profile</a>
                            <a href="faq.php" class="link-item">❓ Help Center</a>
                            <a href="contact.php" class="link-item">📞 Contact Us</a>
                            <a href="categories.php" class="link-item">📁 Categories</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <?php include 'footer.php'; ?>
    </div>

    <script>
    // Create confetti effect
    function createConfetti() {
        const colors = ['#ff66cc', '#9b59b6', '#8e44ad', '#3498db', '#2ecc71'];
        for (let i = 0; i < 150; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + 'vw';
            confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
            confetti.style.animationDelay = Math.random() * 5 + 's';
            document.body.appendChild(confetti);
            
            // Remove confetti after animation
            setTimeout(() => {
                confetti.remove();
            }, 8000);
        }
    }
    
    // Start confetti
    setTimeout(createConfetti, 500);
    
    // Play welcome sound (optional)
    function playWelcomeSound() {
        // In a real app, you might play a sound here
        console.log('🎉 Welcome sound played!');
    }
    
    playWelcomeSound();
    </script>
</body>
</html>