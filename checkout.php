<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if (!isset($_SESSION["cart"]) || empty($_SESSION["cart"])) {
    echo "Your cart is empty.";
    exit;
}

$cart = $_SESSION["cart"];
$total = array_sum(array_map(fn($i)=>$i["price"]*$i["quantity"], $cart));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout</title>
  <link rel="stylesheet" href="index.css">
</head>
<body>
  <h2>Checkout</h2>
  <table>
    <thead>
      <tr>
        <th>Event</th>
        <th>Date</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($cart as $item): ?>
        <tr>
          <td><?php echo htmlspecialchars($item["title"]); ?></td>
          <td><?php echo htmlspecialchars($item["date"]); ?></td>
          <td>$<?php echo number_format($item["price"], 2); ?></td>
          <td><?php echo htmlspecialchars($item["quantity"]); ?></td>
          <td>$<?php echo number_format($item["price"] * $item["quantity"], 2); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <p><strong>Total:</strong> $<?php echo number_format($total, 2); ?></p>

  
  <button id="checkout-btn">Confirm Checkout</button>

<div id="checkout-message"></div>

<script>
document.getElementById("checkout-btn").addEventListener("click", function() {
  fetch("process_checkout.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "checkout=1"
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      document.getElementById("checkout-message").textContent = "✅ Tickets purchased successfully!";
      // Optionally redirect to My Tickets page
      window.location.href = "mytickets.php";
    } else {
      document.getElementById("checkout-message").textContent = "❌ " + data.message;
    }
  });
});
</script>

</body>
</html>
