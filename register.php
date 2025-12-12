<?php
session_start();
require_once "config.php";

// Redirect if already logged in
if (isset($_SESSION["user_id"])) {
    header("location: index.php");
    exit;
}

$username = $email = $name = $password = $confirm_password = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    $username = trim($_POST["username"]);
    if (empty($username)) {
        $errors['username'] = "Username is required";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $errors['username'] = "Username must be 3-20 characters (letters, numbers, underscores)";
    } else {
        $check_sql = "SELECT id FROM Users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors['username'] = "Username already taken";
        }
        mysqli_stmt_close($stmt);
    }
    
    // Validate email
    $email = trim($_POST["email"]);
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    } else {
        $check_sql = "SELECT id FROM Users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors['email'] = "Email already registered";
        }
        mysqli_stmt_close($stmt);
    }
    
    // Validate name
    $name = trim($_POST["name"]);
    if (empty($name)) {
        $errors['name'] = "Full name is required";
    } elseif (strlen($name) < 2) {
        $errors['name'] = "Name must be at least 2 characters";
    }
    
    // Validate password
    $password = $_POST["password"];
    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters";
    }
    
    // Validate confirm password
    $confirm_password = $_POST["confirm_password"];
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }
    
    // If no errors, insert user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO Users (username, email, name, password) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $name, $hashed_password);
        
        if (mysqli_stmt_execute($stmt)) {
            // Get the new user ID
            $user_id = mysqli_insert_id($conn);
            
            // Auto-login after registration
            session_regenerate_id(true);
            $_SESSION["user_id"] = $user_id;
            $_SESSION["username"] = $username;
            $_SESSION["name"] = $name;
            $_SESSION["email"] = $email;
            $_SESSION["loggedin"] = true;
            
            header("location: welcome.php");
            exit;
        } else {
            $errors['general'] = "Registration failed. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - StagePass</title>
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .register-wrapper {
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            width: 100%;
        }

        .register-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        .register-card {
            width: 100%;
            background: rgba(26, 26, 26, 0.9);
            border-radius: 15px;
            padding: 2.5rem;
            border: 1px solid rgba(255, 102, 204, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .register-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .register-header h2 {
            color: #ff66cc;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }

        .register-header p {
            color: #b8b8b8;
            font-size: 1rem;
        }

        .register-form .form-group {
            margin-bottom: 1.5rem;
        }

        .register-form label {
            display: block;
            margin-bottom: 0.5rem;
            color: #e0e0e0;
            font-weight: 500;
        }

        .required::after {
            content: '*';
            color: #ff66cc;
            margin-left: 0.2rem;
        }

        .register-form input {
            width: 100%;
            padding: 1rem;
            background: rgba(50, 50, 50, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .register-form input:focus {
            outline: none;
            border-color: #9b59b6;
            box-shadow: 0 0 0 2px rgba(155, 89, 182, 0.2);
        }

        .error {
            color: #ff4d94;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            display: block;
        }

        small {
            display: block;
            margin-top: 0.3rem;
            color: #888;
            font-size: 0.85rem;
        }

        .register-btn {
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

        .register-btn:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #ff4da6, #8e44ad);
        }

        .register-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .register-links {
            margin-top: 1.5rem;
            text-align: center;
            color: #b8b8b8;
        }

        .register-links a {
            color: #ff66cc;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .register-links a:hover {
            text-decoration: underline;
        }

        .terms-agreement {
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
            margin: 1.5rem 0;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        .terms-agreement input[type="checkbox"] {
            width: auto;
            margin-top: 0.2rem;
        }

        .terms-agreement label {
            color: #b8b8b8;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .terms-agreement a {
            color: #ff66cc;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .register-card {
                padding: 2rem;
                margin: 0 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'navigation.php'; ?>
        
        <main class="register-wrapper">
            <div class="register-container">
                <div class="register-card">
                    <div class="register-header">
                        <h2>Create Account</h2>
                        <p>Fill in your details to get started</p>
                    </div>

                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                            <?php echo $errors['general']; ?>
                        </div>
                    <?php endif; ?>

                    <form class="register-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group">
                            <label for="name" class="required">Full Name</label>
                            <input type="text" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($name); ?>" 
                                   placeholder="Enter your full name" required>
                            <?php if (isset($errors['name'])): ?>
                                <span class="error"><?php echo $errors['name']; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="username" class="required">Username</label>
                            <input type="text" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($username); ?>" 
                                   placeholder="Choose a username (3-20 characters)" required>
                            <?php if (isset($errors['username'])): ?>
                                <span class="error"><?php echo $errors['username']; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="required">Email Address</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($email); ?>" 
                                   placeholder="Enter your email address" required>
                            <?php if (isset($errors['email'])): ?>
                                <span class="error"><?php echo $errors['email']; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="required">Password</label>
                            <input type="password" id="password" name="password" 
                                   placeholder="Create a password (min 6 characters)" required>
                            <?php if (isset($errors['password'])): ?>
                                <span class="error"><?php echo $errors['password']; ?></span>
                            <?php endif; ?>
                            <small>At least 6 characters</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password" class="required">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" 
                                   placeholder="Re-enter your password" required>
                            <?php if (isset($errors['confirm_password'])): ?>
                                <span class="error"><?php echo $errors['confirm_password']; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="terms-agreement">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">
                                I agree to the <a href="terms.php">Terms of Service</a> and 
                                <a href="privacy.php">Privacy Policy</a>.
                            </label>
                        </div>
                        
                        <button type="submit" class="register-btn">
                            Create Account
                        </button>
                        
                        <div class="register-links">
                            <p>
                                Already have an account? 
                                <a href="login.php">Sign in here</a>
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