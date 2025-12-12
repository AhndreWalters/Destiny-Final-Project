<?php
session_start();
require_once "config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's tickets
$sql = "SELECT T.*, E.title, E.venue, E.date, E.price 
        FROM Tickets T 
        JOIN Events E ON T.event_id = E.id 
        WHERE T.user_id = ? 
        ORDER BY T.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tickets - StagePass</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/tickets.css">
</head>
<body>
    <?php include 'navigation.php'; ?>
    
    <main>
        <div class="tickets-header">
            <h1>My Tickets</h1>
            <p>All your upcoming and past event tickets</p>
        </div>
        
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="tickets-list">
                <?php while ($ticket = mysqli_fetch_assoc($result)): 
                    $is_upcoming = strtotime($ticket['date']) > time();
                ?>
                <div class="ticket-card <?php echo $is_upcoming ? 'upcoming' : 'past'; ?>">
                    <div class="ticket-header">
                        <h3><?php echo htmlspecialchars($ticket['title']); ?></h3>
                        <span class="ticket-status">
                            <?php echo $is_upcoming ? '🔵 Upcoming' : '⚫ Past'; ?>
                        </span>
                    </div>
                    
                    <div class="ticket-details">
                        <div class="detail">
                            <span class="label">Venue:</span>
                            <span class="value"><?php echo htmlspecialchars($ticket['venue']); ?></span>
                        </div>
                        <div class="detail">
                            <span class="label">Date:</span>
                            <span class="value"><?php echo date('F j, Y', strtotime($ticket['date'])); ?></span>
                        </div>
                        <div class="detail">
                            <span class="label">Ticket ID:</span>
                            <span class="value">#STG-<?php echo str_pad($ticket['id'], 6, '0', STR_PAD_LEFT); ?></span>
                        </div>
                        <div class="detail">
                            <span class="label">Purchased:</span>
                            <span class="value"><?php echo date('M j, Y', strtotime($ticket['created_at'])); ?></span>
                        </div>
                        <div class="detail">
                            <span class="label">Quantity:</span>
                            <span class="value"><?php echo $ticket['quantity']; ?> ticket(s)</span>
                        </div>
                        <div class="detail">
                            <span class="label">Total Paid:</span>
                            <span class="value price">$<?php echo number_format($ticket['price'] * $ticket['quantity'], 2); ?></span>
                        </div>
                    </div>
                    
                    <div class="ticket-actions">
                        <?php if ($is_upcoming): ?>
                            <button class="btn-view-ticket" onclick="viewTicket(<?php echo $ticket['id']; ?>)">
                                View Ticket
                            </button>
                        <?php endif; ?>
                        <button class="btn-download" onclick="downloadTicket(<?php echo $ticket['id']; ?>)">
                            Download PDF
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-tickets">
                <h2>No Tickets Yet</h2>
                <p>You haven't purchased any tickets yet.</p>
                <a href="events.php" class="btn-browse">Browse Events</a>
            </div>
        <?php endif; ?>
    </main>
    
    <?php include 'footer.php'; ?>
    
    <script>
    function viewTicket(ticketId) {
        alert('Ticket ' + ticketId + ' details would show here in a real application.');
        // In a real app, this would open a modal with ticket QR code
    }
    
    function downloadTicket(ticketId) {
        alert('Downloading ticket ' + ticketId + ' as PDF...');
        // In a real app, this would generate/download a PDF
    }
    </script>
</body>
</html>