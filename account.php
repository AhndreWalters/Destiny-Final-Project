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
            // Update with new password
            $hashedPassword = password_hash($user_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE Users SET name=?, email=?, password=? WHERE id=?");
            $stmt->bind_param("sssi", $name, $email, $hashedPassword, $userId);
        } else {
            // Update without changing password
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
  <link rel="stylesheet" href="index.css">
  <style>
    body { font-family: Arial, sans-serif; background: #111; color: #fff; }
    .container { max-width: 500px; margin: 40px auto; background: #222; padding: 20px; border-radius: 8px; }
    label { display: block; margin-top: 10px; }
    input { width: 100%; padding: 10px; margin-top: 5px; border-radius: 4px; border: none; }
    button { margin-top: 15px; padding: 10px 15px; background: #ff0080; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
    button:hover { background: #7928ca; }
    .success { color: #0f0; margin-top: 10px; }
    .error { color: #f00; margin-top: 10px; }
  </style>
</head>
<body>
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
</body>
</html>
