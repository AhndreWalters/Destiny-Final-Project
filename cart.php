<?php
session_start();
require_once "config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Remove item from cart
if (isset($_GET['remove'])) {
    $index = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1);
    }
    header("Location: cart.php");
    exit;
}

// Clear cart
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
}

// Update quantities
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantities'])) {
    foreach ($_POST['quantity'] as $index => $quantity) {
        $quantity = intval($quantity);
        if ($quantity > 0 && isset($_SESSION['cart'][$index])) {
            $_SESSION['cart'][$index]['quantity'] = $quantity;
        } elseif ($quantity <= 0 && isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
        }
    }
    // Re-index array
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - StagePass</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/cart.css">
</head>
<body>
    <?php include 'navigation.php'; ?>
    
    <main>
        <div class="cart-header">
            <h1>Your Shopping Cart</h1>
            <?php if (count($cart) > 0): ?>
                <p><?php echo count($cart); ?> item(s) in cart</p>
            <?php endif; ?>
        </div>
        
        <?php if (empty($cart)): ?>
            <div class="empty-cart">
                <h2>Your cart is empty</h2>
                <p>Browse our <a href="events.php">events</a> to add tickets!</p>
            </div>
        <?php else: ?>
            <form method="POST" action="cart.php">
                <div class="cart-items">
                    <table>
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
                            <?php foreach ($cart as $index => $item): 
                                $subtotal = $item['price'] * ($item['quantity'] ?? 1);
                                $total += $subtotal;
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($item['date'])); ?></td>
                                <td><?php echo htmlspecialchars($item['venue']); ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <input type="number" name="quantity[<?php echo $index; ?>]" 
                                           value="<?php echo $item['quantity'] ?? 1; ?>" min="1" max="10" class="quantity-input">
                                </td>
                                <td>$<?php echo number_format($subtotal, 2); ?></td>
                                <td>
                                    <a href="?remove=<?php echo $index; ?>" class="remove-link" 
                                       onclick="return confirm('Remove this item?')">Remove</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="cart-actions">
                    <button type="submit" name="update_quantities" class="btn-update">Update Cart</button>
                    <button type="submit" name="clear_cart" class="btn-clear" 
                            onclick="return confirm('Clear entire cart?')">Clear Cart</button>
                </div>
            </form>
            
            <div class="cart-summary">
                <div class="summary-card">
                    <h3>Order Summary</h3>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Service Fee</span>
                        <span>$<?php echo number_format($total * 0.05, 2); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>$<?php echo number_format($total * 1.05, 2); ?></span>
                    </div>
                    
                    <div class="checkout-section">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
                        <?php else: ?>
                            <a href="login.php" class="btn-checkout">Login to Checkout</a>
                        <?php endif; ?>
                        <a href="events.php" class="btn-continue">Continue Shopping</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
    
    <?php include 'footer.php'; ?>
</body>
</html>