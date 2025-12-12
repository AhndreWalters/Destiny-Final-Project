<?php
session_start();
require_once "config.php";

// Get all unique categories
$category_sql = "SELECT DISTINCT category FROM Events WHERE category IS NOT NULL ORDER BY category";
$category_result = mysqli_query($conn, $category_sql);

// Get events for specific category if selected
$selected_category = isset($_GET['cat']) ? $_GET['cat'] : null;
if ($selected_category) {
    $events_sql = "SELECT * FROM Events WHERE category = ? ORDER BY date";
    $stmt = mysqli_prepare($conn, $events_sql);
    mysqli_stmt_bind_param($stmt, "s", $selected_category);
    mysqli_stmt_execute($stmt);
    $events_result = mysqli_stmt_get_result($stmt);
} else {
    $events_sql = "SELECT * FROM Events ORDER BY date";
    $events_result = mysqli_query($conn, $events_sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Categories - StagePass</title>
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .categories-wrapper {
            width: 100%;
            padding: 2rem 0;
        }

        .categories-header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem;
        }

        .categories-header h1 {
            font-size: 3.5rem;
            background: linear-gradient(135deg, #ff66cc, #9b59b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }

        .categories-header p {
            font-size: 1.2rem;
            color: #b8b8b8;
            max-width: 700px;
            margin: 0 auto;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto 4rem;
            padding: 0 1rem;
        }

        .category-card {
            background: rgba(40, 40, 40, 0.8);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.4s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .category-card:hover {
            transform: translateY(-10px) scale(1.02);
            border-color: rgba(255, 102, 204, 0.3);
            box-shadow: 0 20px 40px rgba(155, 89, 182, 0.25);
        }

        .category-card.active {
            background: linear-gradient(135deg, rgba(255, 102, 204, 0.2), rgba(155, 89, 182, 0.2));
            border-color: rgba(255, 102, 204, 0.4);
            transform: translateY(-5px);
        }

        .category-icon {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .category-card:hover .category-icon {
            transform: scale(1.2) rotate(5deg);
        }

        .category-card h3 {
            color: #fff;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .category-stats {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .stat {
            text-align: center;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #ff66cc;
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #b8b8b8;
            display: block;
        }

        .events-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(255, 102, 204, 0.2);
        }

        .section-header h2 {
            color: #ff66cc;
            font-size: 2rem;
            margin: 0;
        }

        .clear-filter {
            color: #9b59b6;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            border: 1px solid rgba(155, 89, 182, 0.3);
            transition: all 0.3s ease;
        }

        .clear-filter:hover {
            background: rgba(155, 89, 182, 0.1);
            transform: translateY(-2px);
        }

        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .event-card-mini {
            background: rgba(30, 30, 30, 0.8);
            border-radius: 15px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .event-card-mini:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 102, 204, 0.2);
            background: rgba(255, 102, 204, 0.05);
        }

        .event-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .event-header h4 {
            color: #fff;
            margin: 0;
            font-size: 1.1rem;
            flex: 1;
        }

        .event-price {
            background: rgba(255, 102, 204, 0.1);
            color: #ff66cc;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .event-details {
            color: #b8b8b8;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .event-details p {
            margin: 0.3rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .event-actions {
            display: flex;
            gap: 0.8rem;
        }

        .action-btn {
            flex: 1;
            padding: 0.6rem;
            border-radius: 10px;
            border: none;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
        }

        .action-btn.primary {
            background: linear-gradient(135deg, #ff66cc, #9b59b6);
            color: white;
        }

        .action-btn.outline {
            background: transparent;
            border: 1px solid #9b59b6;
            color: #9b59b6;
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }

        .no-events {
            text-align: center;
            padding: 4rem 2rem;
            color: #b8b8b8;
            font-size: 1.1rem;
            grid-column: 1 / -1;
        }

        .no-events h3 {
            color: #ff66cc;
            margin-bottom: 1rem;
        }

        .category-description {
            text-align: center;
            max-width: 800px;
            margin: 0 auto 3rem;
            padding: 2rem;
            background: rgba(40, 40, 40, 0.5);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        @media (max-width: 768px) {
            .categories-header h1 {
                font-size: 2.5rem;
            }
            
            .category-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            
            .category-card {
                padding: 1.5rem;
            }
            
            .category-icon {
                font-size: 2.5rem;
            }
            
            .events-grid {
                grid-template-columns: 1fr;
            }
            
            .section-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .category-grid {
                grid-template-columns: 1fr;
            }
            
            .category-stats {
                flex-direction: column;
                gap: 1rem;
            }
            
            .event-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'navigation.php'; ?>
        
        <main class="categories-wrapper">
            <!-- Header -->
            <div class="categories-header">
                <h1>Browse By Category</h1>
                <p>Discover events that match your interests. From music concerts to formal galas, find your perfect experience.</p>
            </div>

            <!-- Category Grid -->
            <div class="category-grid">
                <?php 
                $category_icons = [
                    'Music' => ['🎵', 'Live concerts and music festivals'],
                    'Nightlife' => ['🌙', 'Club nights and late parties'],
                    'Formal' => ['👔', 'Galas, balls, and black-tie events'],
                    'Festival' => ['🎪', 'Multi-day festivals and celebrations'],
                    'Party' => ['🎉', 'Social gatherings and celebrations'],
                    'Themed' => ['🎭', 'Costume and themed parties'],
                    'Premium' => ['💎', 'VIP and luxury experiences'],
                    'Retro' => ['📼', 'Nostalgic and throwback events']
                ];
                
                while ($category_row = mysqli_fetch_assoc($category_result)): 
                    $category = $category_row['category'];
                    $icon = $category_icons[$category][0] ?? '🎫';
                    $desc = $category_icons[$category][1] ?? 'Various events';
                    
                    // Count events in category
                    $count_sql = "SELECT COUNT(*) as count FROM Events WHERE category = ?";
                    $count_stmt = mysqli_prepare($conn, $count_sql);
                    mysqli_stmt_bind_param($count_stmt, "s", $category);
                    mysqli_stmt_execute($count_stmt);
                    $count_result = mysqli_stmt_get_result($count_stmt);
                    $count_data = mysqli_fetch_assoc($count_result);
                    mysqli_stmt_close($count_stmt);
                ?>
                <div class="category-card <?php echo $selected_category == $category ? 'active' : ''; ?>" 
                     onclick="window.location.href='?cat=<?php echo urlencode($category); ?>'">
                    <div class="category-icon"><?php echo $icon; ?></div>
                    <h3><?php echo htmlspecialchars($category); ?></h3>
                    <p style="color: #b8b8b8; font-size: 0.9rem; min-height: 40px;">
                        <?php echo $desc; ?>
                    </p>
                    <div class="category-stats">
                        <div class="stat">
                            <span class="stat-number"><?php echo $count_data['count']; ?></span>
                            <span class="stat-label">Events</span>
                        </div>
                        <div class="stat">
                            <span class="stat-number">⭐</span>
                            <span class="stat-label">Featured</span>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <!-- Events Section -->
            <div class="events-section">
                <div class="section-header">
                    <h2>
                        <?php if ($selected_category): ?>
                            <?php echo htmlspecialchars($selected_category); ?> Events
                        <?php else: ?>
                            All Events
                        <?php endif; ?>
                    </h2>
                    <?php if ($selected_category): ?>
                        <a href="categories.php" class="clear-filter">
                            ✕ Clear Filter
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($selected_category && !empty($category_icons[$selected_category])): ?>
                    <div class="category-description">
                        <p style="color: #e0e0e0; font-size: 1.1rem; line-height: 1.6;">
                            <?php echo $category_icons[$selected_category][1]; ?>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if (mysqli_num_rows($events_result) > 0): ?>
                    <div class="events-grid">
                        <?php while ($event = mysqli_fetch_assoc($events_result)): 
                            $is_upcoming = strtotime($event['date']) > time();
                        ?>
                        <div class="event-card-mini">
                            <div class="event-header">
                                <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                                <span class="event-price">
                                    $<?php echo number_format($event['price'], 2); ?>
                                </span>
                            </div>
                            
                            <div class="event-details">
                                <p>📍 <?php echo htmlspecialchars($event['venue']); ?></p>
                                <p>📅 <?php echo date('M j, Y', strtotime($event['date'])); ?></p>
                                <p>🎟️ <?php echo $event['available_tickets']; ?> tickets left</p>
                            </div>
                            
                            <div class="event-actions">
                                <?php if ($is_upcoming): ?>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <a href="?add_to_cart=<?php echo $event['id']; ?>" 
                                           class="action-btn primary">
                                            🛒 Book
                                        </a>
                                    <?php else: ?>
                                        <a href="login.php" class="action-btn primary">
                                            🔐 Login
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button class="action-btn primary" disabled>
                                        Ended
                                    </button>
                                <?php endif; ?>
                                <button class="action-btn outline" 
                                        onclick="showEventDetails(<?php echo htmlspecialchars(json_encode($event)); ?>)">
                                    👁️ View
                                </button>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="no-events">
                        <h3>No Events Found</h3>
                        <p>
                            <?php if ($selected_category): ?>
                                No events found in the <?php echo htmlspecialchars($selected_category); ?> category.
                            <?php else: ?>
                                No events available at the moment.
                            <?php endif; ?>
                        </p>
                        <p style="margin-top: 1rem;">
                            <a href="events.php" class="btn btn-small">
                                Browse All Events
                            </a>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Category Statistics -->
            <section class="section" style="margin-top: 4rem;">
                <h2>Category Popularity</h2>
                <p style="color: #b8b8b8; text-align: center; margin-bottom: 2rem;">
                    See which event categories our members love the most
                </p>
                
                <div class="grid grid-4">
                    <?php 
                    // Reset category result pointer
                    mysqli_data_seek($category_result, 0);
                    $max_count = 0;
                    $category_counts = [];
                    
                    while ($cat = mysqli_fetch_assoc($category_result)) {
                        $count_sql = "SELECT COUNT(*) as count FROM Events WHERE category = ?";
                        $count_stmt = mysqli_prepare($conn, $count_sql);
                        mysqli_stmt_bind_param($count_stmt, "s", $cat['category']);
                        mysqli_stmt_execute($count_stmt);
                        $count_res = mysqli_stmt_get_result($count_stmt);
                        $count = mysqli_fetch_assoc($count_res)['count'];
                        $category_counts[$cat['category']] = $count;
                        $max_count = max($max_count, $count);
                        mysqli_stmt_close($count_stmt);
                    }
                    
                    foreach ($category_counts as $cat_name => $count):
                        $percentage = $max_count > 0 ? ($count / $max_count) * 100 : 0;
                        $icon = $category_icons[$cat_name][0] ?? '🎫';
                    ?>
                    <div class="card">
                        <div style="font-size: 2rem; margin-bottom: 1rem;">
                            <?php echo $icon; ?>
                        </div>
                        <h3 style="font-size: 1.2rem;"><?php echo $cat_name; ?></h3>
                        <div style="height: 8px; background: rgba(255,255,255,0.1); border-radius: 4px; margin: 1rem 0; overflow: hidden;">
                            <div style="height: 100%; width: <?php echo $percentage; ?>%; background: linear-gradient(90deg, #ff66cc, #9b59b6); border-radius: 4px;"></div>
                        </div>
                        <p style="color: #ff66cc; font-weight: 600; margin: 0;">
                            <?php echo $count; ?> events
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
        
        <?php include 'footer.php'; ?>
    </div>

    <script>
    function showEventDetails(event) {
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.9);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            padding: 1rem;
        `;
        
        const isUpcoming = new Date(event.date) > new Date();
        
        modal.innerHTML = `
            <div style="background: #1a1a1a; padding: 2.5rem; border-radius: 20px; max-width: 500px; width: 100%; border: 1px solid #ff66cc; position: relative;">
                <button onclick="this.parentElement.parentElement.remove()" 
                        style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">
                    ✕
                </button>
                
                <h3 style="color: #ff66cc; margin-bottom: 1.5rem; text-align: center;">${event.title}</h3>
                
                <div style="color: #b8b8b8; line-height: 1.6;">
                    <p><strong>Category:</strong> ${event.category}</p>
                    <p><strong>Venue:</strong> ${event.venue}</p>
                    <p><strong>Date:</strong> ${new Date(event.date).toLocaleDateString()}</p>
                    <p><strong>Price:</strong> $${parseFloat(event.price).toFixed(2)}</p>
                    <p><strong>Tickets:</strong> ${event.available_tickets} available</p>
                    <p><strong>Description:</strong> ${event.description}</p>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    ${isUpcoming && event.available_tickets > 0 ? 
                        `${<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?> ? 
                            `<a href="?add_to_cart=${event.id}" 
                               style="flex: 1; padding: 1rem; background: linear-gradient(135deg, #ff66cc, #9b59b6); color: white; border-radius: 10px; text-decoration: none; text-align: center;">
                                Add to Cart
                            </a>` : 
                            `<a href="login.php" 
                               style="flex: 1; padding: 1rem; background: linear-gradient(135deg, #ff66cc, #9b59b6); color: white; border-radius: 10px; text-decoration: none; text-align: center;">
                                Login to Book
                            </a>`
                        }` : 
                        '<button style="flex: 1; padding: 1rem; background: #666; color: #ccc; border-radius: 10px; border: none;" disabled>Not Available</button>'
                    }
                    <button onclick="this.parentElement.parentElement.parentElement.remove()"
                            style="padding: 1rem 2rem; background: transparent; border: 1px solid #9b59b6; color: #9b59b6; border-radius: 10px; cursor: pointer;">
                        Close
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
    }
    </script>
</body>
</html>