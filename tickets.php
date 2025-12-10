<?php 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // If user is NOT logged in, redirect to login page
    header("location: login.php");
    exit;
}
?>
