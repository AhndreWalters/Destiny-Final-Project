<?php
session_start();
require_once "config.php";

// Redirect if already logged in
if (isset($_SESSION["user_id"])) {
    header("location: index.php");
    exit;
}

$username = $password = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $sql = "SELECT id, username, password, name, email FROM Users WHERE username = ? OR email = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $username, $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $db_username, $hashed_password, $name, $email);
                mysqli_stmt_fetch($stmt);
                
                if (password_verify($password, $hashed_password)) {
                    session_regenerate_id(true);
                    $_SESSION["user_id"] = $id;
                    $_SESSION["username"] = $db_username;
                    $_SESSION["name"] = $name;
                    $_SESSION["email"] = $email;
                    $_SESSION["loggedin"] = true;
                    
                    // Set last login time
                    $_SESSION["last_login"] = time();
                    
                    header("location: index.php");
                    exit;
                } else {
                    $error = "Invalid username or password.";
                }
            } else {
                $error = "No account found with that username/email.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StagePass</title>
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .login-wrapper {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            width: 100%;
        }

        .login-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            align-items: center;
        }

        @media (max-width: 992px) {
            .login-container {
                grid-template-columns: 1fr;
                gap: 3rem;
            }
        }

        .login-hero {
            padding: 3rem;
            text-align: center;
        }

        .login-hero h1 {
            font-size: 3.5rem;
            background: linear-gradient(135deg, #ff66cc, #9b59b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
        }

        .login-hero p {
            color: #b8b8b8;
            font-size: 1.2rem;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .features-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(255, 102, 204, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(255, 102, 204, 0.1);
        }

        .feature-icon {
            font-size: 1.5rem;
            color: #ff66cc;
        }

        .login-card {
            width: 100%;
            background: rgba(26, 26, 26, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 3rem;
            border: 1px solid rgba(255, 102, 204, 0.2);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
            text-align: center;
        }

        .login-header {
            margin-bottom: 2.5rem;
        }

        .login-header h2 {
            font-size: 2.5rem;
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #b8b8b8;
            font-size: 1.1rem;
        }

        .login-form .form-group {
            margin-bottom: 1.8rem;
            text-align: left;
        }

        .login-form label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.8rem;
            color: #e0e0e0;
            font-weight: 500;
            font-size: 1.1rem;
        }

        .login-form input {
            width: 100%;
            padding: 1.2rem;
            background: rgba(50, 50, 50, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .login-form input:focus {
            outline: none;
            border-color: #9b59b6;
            box-shadow: 0 0 0 3px rgba(155, 89, 182, 0.2);
            transform: translateY(-2px);
        }

        .login-btn {
            width: 100%;
            padding: 1.2rem;
            background: linear-gradient(135deg, #ff66cc, #9b59b6);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
        }

        .login-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 10px 30px rgba(255, 102, 204, 0.4);
        }

        .login-links {
            margin-top: 2rem;
            text-align: center;
            color: #b8b8b8;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .login-links a {
            color: #ff66cc;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            display: inline-block;
        }

        .login-links a:hover {
            background: rgba(255, 102, 204, 0.1);
            transform: translateY(-2px);
        }

        .demo-box {
            margin-top: 2.5rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, rgba(255, 102, 204, 0.1), rgba(155, 89, 182, 0.1));
            border-radius: 15px;
            border: 1px solid rgba(255, 102, 204, 0.3);
            text-align: left;
        }

        .demo-box h4 {
            color: #ff66cc;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .demo-box p {
            margin: 0.8rem 0;
            color: #e0e0e0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .demo-box strong {
            color: #ff66cc;
            min-width: 120px;
            display: inline-block;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #b8b8b8;
        }

        .remember-me input[type="checkbox"] {
            width: auto;
            transform: scale(1.2);
        }

        @media (max-width: 768px) {
            .login-container {
                padding: 1rem;
            }
            
            .login-card {
                padding: 2rem;
                margin: 1rem;
            }
            
            .login-hero {
                padding: 2rem 1rem;
            }
            
            .login-hero h1 {
                font-size: 2.5rem;
            }
            
            .login-header h2 {
                font-size: 2rem;
            }
            
            .remember-forgot {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'navigation.php'; ?>
        
        <main class="login-wrapper">
            <div class="login-container">
                <!-- Hero Section -->
                <div class="login-hero">
                    <h1>Welcome Back</h1>
                    <p>Sign in to access exclusive events, manage your tickets, and unlock premium features.</p>
                    
                    <div class="features-list">
                        <div class="feature-item">
                            <span class="feature-icon">🎫</span>
                            <div>
                                <strong>Manage Tickets</strong>
                                <p style="color: #b8b8b8; font-size: 0.9rem; margin: 0.2rem 0 0;">Access all your bookings in one place</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">⭐</span>
                            <div>
                                <strong>VIP Access</strong>
                                <p style="color: #b8b8b8; font-size: 0.9rem; margin: 0.2rem 0 0;">Early access to premium events</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Login Form -->
                <div class="login-card">
                    <div class="login-header">
                        <h2>Member Login</h2>
                        <p>Enter your credentials to continue</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-error" style="margin-bottom: 2rem;">
                            ⚠️ <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form class="login-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group">
                            <label for="username">Username or Email</label>
                            <input type="text" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($username); ?>" 
                                   placeholder="Enter username or email" required
                                   autocomplete="username">
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" 
                                   placeholder="Enter your password" required
                                   autocomplete="current-password">
                        </div>
                        
                        <div class="remember-forgot">
                            <div class="remember-me">
                                <label for="remember" style="font-size: 0.95rem;"></label>
                            </div>
                        </div>
                        
                        <button type="submit" class="login-btn">
                            🔐 Sign In
                        </button>
                        
                        <div class="login-links">
                            <p>
                                Don't have an account? 
                                <a href="register.php">Create one now</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        
        <?php include 'footer.php'; ?>
    </div>
</body>
</html>