<?php
session_start();

//  Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$total = array_sum(array_map(fn($i)=>$i['price'], $cart));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="styles/cart.css">
  <title>Cart</title>
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

  <h1>Your Cart</h1>
  <?php if (empty($cart)): ?>
    <p>No items in cart.</p>
  <?php else: ?>
    <table border="1">
      <tr><th>Event</th><th>Date</th><th>Venue</th><th>Price</th></tr>
      <?php foreach ($cart as $item): ?>
        <tr>
          <td><?php echo htmlspecialchars($item['title']); ?></td>
          <td><?php echo htmlspecialchars($item['date']); ?></td>
          <td><?php echo htmlspecialchars($item['venue']); ?></td>
          <td>$<?php echo number_format($item['price'], 2); ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
    <p><strong>Total:</strong> $<?php echo number_format($total, 2); ?></p>
    <a href="checkout.php">Proceed to Checkout</a>
  <?php endif; ?>

  <footer>© 2025 StagePass | <a href="contact.php">Contact Us</a> | Privacy Policy</footer>
</body>
</html>
