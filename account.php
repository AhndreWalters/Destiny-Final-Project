<?php
session_start();
require_once "config.php"; // DB connection

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$userId = $_SESSION["user_id"];

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $user_password = trim($_POST["user_password"]);

    if (!empty($name) && !empty($email)) {
        if (!empty($user_password)) {
            $hashedPassword = password_hash($user_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE Users SET name=?, email=?, password=? WHERE id=?");
            $stmt->bind_param("sssi", $name, $email, $hashedPassword, $userId);
        } else {
            $stmt = $conn->prepare("UPDATE Users SET name=?, email=? WHERE id=?");
            $stmt->bind_param("ssi", $name, $email, $userId);
        }
        $stmt->execute();
        $stmt->close();
        $success = "Account updated successfully!";
    } else {
        $error = "Name and email cannot be empty.";
    }
}

// Fetch current user info
$stmt = $conn->prepare("SELECT name, email FROM Users WHERE id=?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>StagePass - My Account</title>
  <link rel="stylesheet" href="styles/account.css">
  
</head>
<body>
  <nav id="top-menu">
    <ul>
      <li><a href="index.php">Home</a></li>
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

  <div class="container">
    <h2>My Account</h2>

    <?php if (!empty($success)): ?>
      <p class="success"><?php echo $success; ?></p>
    <?php elseif (!empty($error)): ?>
      <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="post" action="account.php">
      <label for="name">Name</label>
      <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

      <label for="user_password">New Password (leave blank to keep current)</label>
      <input type="password" id="user_password" name="user_password">

      <button type="submit">Update Account</button>
    </form>
  </div>

  <footer>© 2025 StagePass | <a href="contact.php">Contact Us</a> | Privacy Policy</footer>
</body>
</html>
