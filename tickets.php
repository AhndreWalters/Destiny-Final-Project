<?php
session_start();
require_once "config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

$sql = "SELECT T.id, T.created_at, E.title, E.date, E.venue, E.price
        FROM Tickets T
        JOIN Events E ON T.event_id = E.id
        WHERE T.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
   <link rel="stylesheet" href="styles/tickets.css">
  <title>My Tickets</title>
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

  <h1>My Tickets</h1>
  <table border="1">
    <tr>
      <th>Date Purchased</th>
      <th>Event</th>
      <th>Date</th>
      <th>Venue</th>
      <th>Price</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
        <td><?php echo htmlspecialchars($row['title']); ?></td>
        <td><?php echo htmlspecialchars($row['date']); ?></td>
        <td><?php echo htmlspecialchars($row['venue']); ?></td>
        <td>$<?php echo number_format($row['price'], 2); ?></td>
      </tr>
    <?php endwhile; ?>
  </table>

  <footer>© 2025 StagePass | <a href="contact.php">Contact Us</a> | Privacy Policy</footer>
</body>
</html>
