<?php
session_start();
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

// Handle clear cart
if (isset($_POST["clear"])) {
    $_SESSION["cart"] = [];
    echo json_encode(["success" => true, "subtotal" => 0, "total" => 0]);
    exit;
}

$index = intval($_POST["index"] ?? -1);
$quantity = intval($_POST["quantity"] ?? 0);

if ($index >= 0 && isset($_SESSION["cart"][$index])) {
    if ($quantity > 0) {
        $_SESSION["cart"][$index]["quantity"] = $quantity;
    } else {
        array_splice($_SESSION["cart"], $index, 1);
    }
}

// Recalculate totals
$total = 0;
foreach ($_SESSION["cart"] as $item) {
    $total += $item["price"] * $item["quantity"];
}

$subtotal = ($index >= 0 && isset($_SESSION["cart"][$index]))
    ? $_SESSION["cart"][$index]["price"] * $_SESSION["cart"][$index]["quantity"]
    : 0;

echo json_encode([
    "success" => true,
    "subtotal" => $subtotal,
    "total" => $total
]);
