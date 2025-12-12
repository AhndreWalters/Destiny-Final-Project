<?php
session_start();
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

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
<head><title>Events</title></head>
<body>
<h1>Events</h1>
<section class="events-gallery">
<?php foreach ($events as $e): ?>
  <figure class="event">
    <img src="<?php echo $e['image']; ?>" alt="<?php echo $e['title']; ?>" width="200">
    <figcaption>
      <strong><?php echo $e['title']; ?></strong><br>
      <?php echo $e['date']; ?> @ <?php echo $e['venue']; ?><br>
      $<?php echo $e['price']; ?><br>
      <a href="events.php?add=<?php echo $e['id']; ?>">Add to Cart</a>
    </figcaption>
  </figure>
<?php endforeach; ?>
</section>
</body>
</html>
