<?php
session_start();
require_once "config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process checkout
    mysqli_begin_transaction($conn);
    
    try {
        foreach ($_SESSION['cart'] as $item) {
            // Check ticket availability
            $check_sql = "SELECT available_tickets FROM Events WHERE id = ?";
            $check_stmt = mysqli_prepare($conn, $check_sql);
            mysqli_stmt_bind_param($check_stmt, "i", $item['id']);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_bind_result($check_stmt, $available);
            mysqli_stmt_fetch($check_stmt);
            mysqli_stmt_close($check_stmt);
            
            if ($available < ($item['quantity'] ?? 1)) {
                throw new Exception("Not enough tickets available for: " . $item['title']);
            }
            
            // Update available tickets
            $update_sql = "UPDATE Events SET available_tickets = available_tickets - ? WHERE id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            $quantity = $item['quantity'] ?? 1;
            mysqli_stmt_bind_param($update_stmt, "ii", $quantity, $item['id']);
            mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);
            
            // Create ticket record
            $ticket_sql = "INSERT INTO Tickets (user_id, event_id, quantity) VALUES (?, ?, ?)";
            $ticket_stmt = mysqli_prepare($conn, $ticket_sql);
            mysqli_stmt_bind_param($ticket_stmt, "iii", $user_id, $item['id'], $quantity);
            mysqli_stmt_execute($ticket_stmt);
            mysqli_stmt_close($ticket_stmt);
        }
        
        // Commit transaction
        mysqli_commit($conn);
        $success = true;
        $_SESSION['cart'] = []; // Clear cart
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $errors[] = $e->getMessage();
    }
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * ($item['quantity'] ?? 1);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - StagePass</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/checkout.css">
</head>
<body>
    <?php include 'navigation.php'; ?>
    
    <main>
        <div class="checkout-container">
            <h1>Checkout</h1>
            
            <?php if ($success): ?>
                <div class="checkout-success">
                    <h2>🎉 Order Confirmed!</h2>
                    <p>Thank you for your purchase. Your tickets have been booked successfully.</p>
                    <p>You can view and download your tickets from the <a href="tickets.php">My Tickets</a> page.</p>
                    <p>A confirmation email has been sent to your registered email address.</p>
                    <a href="tickets.php" class="btn-view-tickets">View My Tickets</a>
                    <a href="events.php" class="btn-browse-more">Browse More Events</a>
                </div>
            <?php else: ?>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <div class="checkout-content">
                    <div class="order-summary">
                        <h2>Order Summary</h2>
                        <div class="summary-items">
                            <?php foreach ($_SESSION['cart'] as $item): 
                                $quantity = $item['quantity'] ?? 1;
                                $subtotal = $item['price'] * $quantity;
                            ?>
                            <div class="summary-item">
                                <div>
                                    <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                    <p><?php echo date('M j, Y', strtotime($item['date'])); ?> • <?php echo $quantity; ?> ticket(s)</p>
                                </div>
                                <span>$<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="summary-totals">
                            <div class="total-row">
                                <span>Subtotal</span>
                                <span>$<?php echo number_format($total, 2); ?></span>
                            </div>
                            <div class="total-row">
                                <span>Service Fee</span>
                                <span>$<?php echo number_format($total * 0.05, 2); ?></span>
                            </div>
                            <div class="total-row grand-total">
                                <span>Total</span>
                                <span>$<?php echo number_format($total * 1.05, 2); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="payment-form">
                        <h2>Payment Information</h2>
                        <form method="POST" action="checkout.php">
                            <div class="form-group">
                                <label for="card_name">Name on Card</label>
                                <input type="text" id="card_name" name="card_name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="card_number">Card Number</label>
                                <input type="text" id="card_number" name="card_number" 
                                       placeholder="1234 5678 9012 3456" required>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="expiry">Expiry Date</label>
                                    <input type="text" id="expiry" name="expiry" 
                                           placeholder="MM/YY" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="cvv">CVV</label>
                                    <input type="text" id="cvv" name="cvv" 
                                           placeholder="123" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email_receipt">Email for Receipt</label>
                                <input type="email" id="email_receipt" name="email_receipt" 
                                       value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" required>
                            </div>
                            
                            <div class="terms-agreement">
                                <input type="checkbox" id="terms" name="terms" required>
                                <label for="terms">
                                    I agree to the <a href="terms.php">Terms of Service</a> and understand this is a demo purchase.
                                </label>
                            </div>
                            
                            <button type="submit" class="btn-pay">
                                Pay $<?php echo number_format($total * 1.05, 2); ?>
                            </button>
                            
                            <p class="demo-note">
                                <strong>Note:</strong> This is a demo checkout. No real payment will be processed.
                            </p>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>
</body>
</html>