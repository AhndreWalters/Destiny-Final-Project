<?php
session_start();
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// Example events array (replace later with DB query if needed)
$events = [
  ["id"=>1,"title"=>"Beach Party Blast","date"=>"2025-12-20","venue"=>"Grand Anse Beach","price"=>40,"image"=>"images/spring.jpeg"],
  ["id"=>2,"title"=>"Neon glow Night","date"=>"2025-12-31","venue"=>"Club Inferno","price"=>50,"image"=>"images/neon glow.jpeg"],
  ["id"=>3,"title"=>"Masquerade Ball","date"=>"2026-01-15","venue"=>"Royal Banquet Hall","price"=>75,"image"=>"images/masquerade.jpeg"],
  ["id"=>4,"title"=>"Carnival Warmup","date"=>"2026-02-10","venue"=>"Town Square","price"=>30,"image"=>"images/carnival warmup.jpeg"],
  ["id"=>5,"title"=>"Foam Party","date"=>"2026-03-01","venue"=>"Skyline Rooftop Venue","price"=>45,"image"=>"images/foam party.jpeg"],
  ["id"=>6,"title"=>"Silent Disco","date"=>"2026-03-20","venue"=>"Innovation Hud Auditorium","price"=>35,"image"=>"images/silent disco.jpeg"],
  ["id"=>7,"title"=>"Tropical Luau","date"=>"2026-04-05","venue"=>"Luxury Hotel Poolside","price"=>25,"image"=>"images/tropical luau.jpeg"],
  ["id"=>8,"title"=>"Retro 90s Night","date"=>"2026-04-18","venue"=>"Stadium","price"=>30,"image"=>"images/disco.jpeg"],
  ["id"=>9,"title"=>"White Party","date"=>"2026-05-02","venue"=>"Community Hall","price"=>60,"image"=>"images/white party.jpeg"],
  ["id"=>10,"title"=>"Boat Cruis Party","date"=>"2026-12-10","venue"=>"Main Plaza","price"=>60,"image"=>"images/cruise.jpeg"],
];

// Handle Add to Cart
if (isset($_GET['add'])) {
  $id = $_GET['add'];
  foreach ($events as $e) {
    if ($e['id'] == $id) {
      $_SESSION['cart'][] = $e;
      break;
    }
  }
  header("Location: cart.php");
  exit;
}
?>
<!DOCTYPE html>
<html>

<head>
  <head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/events.css">
    <title>Events</title>
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

<h1>Events</h1>
<section class="events-gallery">
<?php foreach ($events as $e): ?>
  <figure class="event">
    <img src="<?php echo htmlspecialchars($e['image']); ?>" alt="<?php echo htmlspecialchars($e['title']); ?>" width="200">
    <figcaption>
      <strong><?php echo htmlspecialchars($e['title']); ?></strong><br>
      <?php echo htmlspecialchars($e['date']); ?> @ <?php echo htmlspecialchars($e['venue']); ?><br>
      $<?php echo number_format($e['price'], 2); ?><br>
      <a href="events.php?add=<?php echo $e['id']; ?>">Add to Cart</a>
    </figcaption>
  </figure>
<?php endforeach; ?>
</section>

<footer>© 2025 StagePass | <a href="contact.php">Contact Us</a> | Privacy Policy</footer>
</body>
</html>
