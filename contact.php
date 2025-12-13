<?php
session_start();
require_once "config.php"; // DB connection

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $message = trim($_POST["message"]);

    if (!empty($name) && !empty($email) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO ContactMessages (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $name, $email, $message);
        $stmt->execute();
        $stmt->close();

        $success = "Thank you for contacting us! We'll get back to you soon.";
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>StagePass - Contact Us</title>
  <link rel="stylesheet" href="styles/contact.css">
</head>
<body>
    <nav id="top-menu">
      <ul>
        <li><a href="tickets.php">My tickets</a></li>
        <li><a href="events.php">Events</a></li> 
        <li><a href="bookings.php">Book Ticket</a></li>
        <li><a href="cart.php">Cart</a></li>
        <li><a href="contact.php">Contact Us</a></li>
        <li><a href="account.php">Account</a></li>
        <li>
          <?php if (isset($_SESSION['user_id'])): ?>
            <button onclick="window.location.href='logout.php'">LOGOUT</button>
          <?php else: ?>
            <button onclick="window.location.href='login.php'">LOGIN</button>
          <?php endif; ?>
        </li>
      </ul>
    </nav>

  <header>
    <h1>Contact Us</h1>
  </header>

  <div class="container">
    <p>Have questions or feedback? Send us a message below.</p>

    <?php if (!empty($success)): ?>
      <p class="success"><?php echo $success; ?></p>
    <?php elseif (!empty($error)): ?>
      <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="post" action="contact.php">
      <label for="name">Your Name</label>
      <input type="text" id="name" name="name" required>

      <label for="email">Your Email</label>
      <input type="email" id="email" name="email" required>

      <label for="message">Message</label>
      <textarea id="message" name="message" rows="5" required></textarea>

      <button type="submit">Send Message</button>
    </form>
  </div>
  <footer>© 2025 StagePass | <a href="contact.php">Contact Us</a> | Privacy Policy</footer>
</body>
</html>
