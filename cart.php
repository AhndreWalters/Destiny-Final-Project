<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}
$cart = $_SESSION["cart"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cart</title>
  <link rel="stylesheet" href="index.css">
  <style>
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
    input[type=number] { width: 60px; }
    button { padding: 6px 10px; margin: 2px; }
  </style>
</head>
<body>
  <h2>Your Cart</h2>
  <?php if (!empty($cart)): ?>
    <table id="cart-table">
      <thead>
        <tr>
          <th>Event</th>
          <th>Date</th>
          <th>Venue</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Subtotal</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cart as $index => $item): ?>
          <tr data-index="<?php echo $index; ?>">
            <td><?php echo htmlspecialchars($item["title"]); ?></td>
            <td><?php echo htmlspecialchars($item["date"]); ?></td>
            <td><?php echo htmlspecialchars($item["venue"]); ?></td>
            <td>$<?php echo htmlspecialchars($item["price"]); ?></td>
            <td>
              <input type="number" value="<?php echo $item["quantity"]; ?>" min="0"
                     onchange="updateQuantity(<?php echo $index; ?>, this.value)">
            </td>
            <td class="subtotal">$<?php echo $item["price"] * $item["quantity"]; ?></td>
            <td>
              <button onclick="removeItem(<?php echo $index; ?>)">Remove</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p><strong>Total:</strong> $<span id="total">
      <?php echo array_sum(array_map(fn($i)=>$i["price"]*$i["quantity"], $cart)); ?>
    </span></p>
    <button onclick="clearCart()">Clear Cart</button>
    <form method="post" action="checkout.php">
      <button type="submit">Checkout</button>
    </form>
  <?php else: ?>
    <p>Your cart is empty.</p>
  <?php endif; ?>

<script>
function updateQuantity(index, newQuantity) {
  fetch("update_cart.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "index=" + index + "&quantity=" + newQuantity
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const row = document.querySelector(`tr[data-index='${index}']`);
      if (newQuantity == 0) {
        row.remove();
      } else {
        row.querySelector(".subtotal").textContent = "$" + data.subtotal;
      }
      document.getElementById("total").textContent = data.total;
    }
  });
}

function removeItem(index) {
  fetch("update_cart.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "index=" + index + "&quantity=0"
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const row = document.querySelector(`tr[data-index='${index}']`);
      row.remove();
      document.getElementById("total").textContent = data.total;
    }
  });
}

function clearCart() {
  fetch("update_cart.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "clear=1"
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      document.querySelector("#cart-table tbody").innerHTML = "";
      document.getElementById("total").textContent = "0";
    }
  });
}

<button onclick="confirmClearCart()">Clear Cart</button>

function confirmClearCart() {
  if (confirm("Are you sure you want to clear your cart?")) {
    clearCart();
  }
}

function clearCart() {
  fetch("update_cart.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "clear=1"
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const tbody = document.querySelector("#cart-table tbody");
      if (tbody) tbody.innerHTML = "";
      document.getElementById("total").textContent = "0";
    }
  });
}
</script>
</body>
</html>
