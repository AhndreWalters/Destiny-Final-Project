<?php
session_start();
require_once "config.php";

$success = $error = "";
$name = $email = $subject = $message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Validation
    if (empty($name) || empty($email) || empty($message)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($message) < 10) {
        $error = "Message must be at least 10 characters long.";
    } else {
        // Insert into database
        $sql = "INSERT INTO ContactMessages (name, email, subject, message) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $subject, $message);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Thank you for your message! We'll get back to you within 24 hours.";
            // Clear form
            $name = $email = $subject = $message = "";
        } else {
            $error = "Sorry, there was an error sending your message. Please try again.";
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
    <title>Contact Us - StagePass</title>
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .contact-wrapper {
            width: 100%;
            padding: 2rem 0;
        }

        .contact-header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem;
        }

        .contact-header h1 {
            font-size: 2.5rem;
            color: #ff66cc;
            margin-bottom: 1rem;
        }

        .contact-header p {
            font-size: 1.1rem;
            color: #b8b8b8;
            max-width: 700px;
            margin: 0 auto;
        }

        .contact-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        @media (max-width: 992px) {
            .contact-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }

        .contact-info {
            padding: 2rem;
            background: rgba(30, 30, 30, 0.8);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .contact-info h2 {
            color: #ff66cc;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        .info-cards {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .info-card {
            padding: 1.5rem;
            background: rgba(40, 40, 40, 0.5);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .info-card h3 {
            color: #fff;
            margin-bottom: 0.8rem;
            font-size: 1.1rem;
        }

        .info-card p {
            color: #b8b8b8;
            line-height: 1.6;
            margin: 0.3rem 0;
        }

        .contact-form {
            padding: 2rem;
            background: rgba(30, 30, 30, 0.8);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .contact-form h2 {
            color: #ff66cc;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
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

        .required::after {
            content: '*';
            color: #ff66cc;
            margin-left: 0.2rem;
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

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
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

        .response-time {
            text-align: center;
            color: #9b59b6;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            padding: 1rem;
            background: rgba(155, 89, 182, 0.1);
            border-radius: 8px;
            border: 1px solid rgba(155, 89, 182, 0.2);
        }

        @media (max-width: 768px) {
            .contact-header h1 {
                font-size: 2rem;
            }
            
            .contact-content {
                padding: 0;
            }
            
            .contact-info,
            .contact-form {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'navigation.php'; ?>
        
        <main class="contact-wrapper">
            <div class="contact-header">
                <h1>Contact Us</h1>
                <p>Have questions, feedback, or need assistance? Our team is here to help you.</p>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success" style="max-width: 800px; margin: 2rem auto;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error" style="max-width: 800px; margin: 2rem auto;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="contact-content">
                <!-- Contact Information -->
                <div class="contact-info">
                    <h2>Contact Information</h2>
                    <div class="info-cards">
                        <div class="info-card">
                            <h3>Our Office</h3>
                            <p>
                                123 Premium Event Street<br>
                                Suite 500, Entertainment District<br>
                                New York, NY 10001
                            </p>
                        </div>
                        
                        <div class="info-card">
                            <h3>Phone Support</h3>
                            <p>
                                <strong>General Inquiries:</strong> +1 (234) 567-8900<br>
                                <strong>VIP Support:</strong> +1 (234) 567-8901<br>
                                <strong>Hours:</strong> 24/7
                            </p>
                        </div>
                        
                        <div class="info-card">
                            <h3>Email Addresses</h3>
                            <p>
                                <strong>General:</strong> info@stagepass.com<br>
                                <strong>Support:</strong> support@stagepass.com<br>
                                <strong>Events:</strong> events@stagepass.com
                            </p>
                        </div>
                        
                        <div class="info-card">
                            <h3>Business Hours</h3>
                            <p>
                                <strong>Customer Service:</strong> 24/7<br>
                                <strong>Office Hours:</strong> Mon-Fri, 9AM-6PM EST<br>
                                <strong>Event Days:</strong> Extended hours
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="contact-form">
                    <h2>Send Us a Message</h2>
                    <form method="POST" action="contact.php">
                        <div class="form-group">
                            <label for="name" class="required">Your Name</label>
                            <input type="text" id="name" name="name" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($name); ?>" 
                                   placeholder="Enter your full name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="required">Email Address</label>
                            <input type="email" id="email" name="email" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($email); ?>" 
                                   placeholder="Enter your email address" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($subject); ?>" 
                                   placeholder="What is this regarding?">
                        </div>
                        
                        <div class="form-group">
                            <label for="message" class="required">Your Message</label>
                            <textarea id="message" name="message" 
                                      class="form-control" 
                                      placeholder="Please provide details about your inquiry..." 
                                      required><?php echo htmlspecialchars($message); ?></textarea>
                        </div>
                        
                        <button type="submit" class="submit-btn">
                            Send Message
                        </button>
                        
                        <div class="response-time">
                            Average response time: 2 hours during business hours
                        </div>
                    </form>
                </div>
            </div>

            <!-- FAQ Preview -->
            <section class="section" style="margin-top: 4rem;">
                <h2>Frequently Asked Questions</h2>
                <p style="color: #b8b8b8; text-align: center; margin-bottom: 2rem;">
                    Quick answers to common questions
                </p>
                <div class="grid grid-2" style="max-width: 1000px;">
                    <div class="card">
                        <h3 style="font-size: 1.1rem;">How do I get support for an event?</h3>
                        <p style="color: #b8b8b8; margin: 1rem 0;">
                            Contact our event support team at events@stagepass.com or call our 24/7 event hotline.
                        </p>
                    </div>
                    <div class="card">
                        <h3 style="font-size: 1.1rem;">What's your refund policy?</h3>
                        <p style="color: #b8b8b8; margin: 1rem 0;">
                            Full refunds available up to 7 days before event. See our refund policy for details.
                        </p>
                    </div>
                </div>
                <div style="text-align: center; margin-top: 2rem;">
                    <a href="faq.php" class="btn btn-outline">
                        View All FAQ's
                    </a>
                </div>
            </section>
        </main>
        
        <?php include 'footer.php'; ?>
    </div>
</body>
</html>