<?php
session_start();
require_once "config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

if (!isset($_SESSION["cart"]) || empty($_SESSION["cart"])) {
    echo json_encode(["success" => false, "message" => "Cart is empty"]);
    exit;
}

$userId = $_SESSION["user_id"];
$cart = $_SESSION["cart"];

foreach ($cart as $item) {
    $eventId = $item["id"];
    $quantity = $item["quantity"];

    // Deduct tickets
    $stmt = $conn->prepare("UPDATE Events SET available_tickets = available_tickets - ? WHERE id = ? AND available_tickets >= ?");
    $stmt->bind_param("iii", $quantity, $eventId, $quantity);
    $stmt->execute();
    $stmt->close();

    // Record purchase
    $stmt = $conn->prepare("INSERT INTO Tickets (user_id, event_id, quantity, status) VALUES (?, ?, ?, 'confirmed')");
    $stmt->bind_param("iii", $userId, $eventId, $quantity);
    $stmt->execute();
    $stmt->close();
}

// Clear cart
$_SESSION["cart"] = [];

echo json_encode(["success" => true, "message" => "Checkout complete"]);
?>
