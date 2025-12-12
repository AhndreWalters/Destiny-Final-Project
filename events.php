<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['add_to_cart'])) {
    $event_id = intval($_GET['add_to_cart']);
    $sql = "SELECT id, title, venue, date, price FROM Events WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $event_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($event = mysqli_fetch_assoc($result)) {
        $event['quantity'] = 1;
        $_SESSION['cart'][] = $event;
        $success = "Added to cart successfully!";
    }
    mysqli_stmt_close($stmt);
}

// Get all events
$sql = "SELECT * FROM Events ORDER BY date";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Events - StagePass</title>
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .events-wrapper {
            width: 100%;
            padding: 2rem 0;
        }

        .events-header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem;
        }

        .events-header h1 {
            font-size: 2.5rem;
            color: #ff66cc;
            margin-bottom: 1rem;
        }

        .events-header p {
            font-size: 1.1rem;
            color: #b8b8b8;
            max-width: 700px;
            margin: 0 auto 1.5rem;
        }

        .cart-indicator {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: rgba(255, 102, 204, 0.1);
            border-radius: 20px;
            color: #ff66cc;
            font-weight: 500;
            text-decoration: none;
            margin-top: 1rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 102, 204, 0.3);
        }

        .cart-indicator:hover {
            background: rgba(255, 102, 204, 0.2);
            transform: translateY(-2px);
        }

        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .event-card {
            background: rgba(30, 30, 30, 0.8);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 102, 204, 0.3);
            background: rgba(40, 40, 40, 0.9);
        }

        .event-card h3 {
            color: #fff;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }

        .event-details {
            margin-bottom: 1.5rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 0.8rem;
            color: #b8b8b8;
            font-size: 0.95rem;
        }

        .price-tag {
            font-size: 1.8rem;
            font-weight: 700;
            color: #ff66cc;
            text-align: center;
            margin: 1.5rem 0;
            padding: 1rem;
            background: rgba(255, 102, 204, 0.1);
            border-radius: 10px;
            border: 1px solid rgba(255, 102, 204, 0.2);
        }

        .ticket-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .available {
            color: #2ecc71;
        }

        .limited {
            color: #f39c12;
        }

        .low {
            color: #e74c3c;
        }

        .event-actions {
            display: flex;
            gap: 1rem;
        }

        .add-to-cart-btn {
            flex: 1;
            padding: 1rem;
            background: linear-gradient(135deg, #ff66cc, #9b59b6);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .add-to-cart-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #ff4da6, #8e44ad);
        }

        .add-to-cart-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .view-details-btn {
            flex: 1;
            padding: 1rem;
            background: transparent;
            border: 1px solid #9b59b6;
            color: #9b59b6;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .view-details-btn:hover {
            background: rgba(155, 89, 182, 0.1);
        }

        .no-events {
            text-align: center;
            padding: 4rem 2rem;
            color: #b8b8b8;
            font-size: 1.1rem;
        }

        .no-events h2 {
            color: #ff66cc;
            margin-bottom: 1rem;
        }

        .filters {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin: 2rem 0;
            padding: 1.5rem;
            background: rgba(40, 40, 40, 0.5);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .filter-btn {
            padding: 0.8rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            color: #b8b8b8;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: rgba(255, 102, 204, 0.2);
            color: #ff66cc;
            border-color: rgba(255, 102, 204, 0.3);
        }

        @media (max-width: 768px) {
            .events-grid {
                grid-template-columns: 1fr;
                padding: 0 1rem;
                gap: 1.5rem;
            }
            
            .events-header h1 {
                font-size: 2rem;
            }
            
            .filters {
                padding: 1rem;
            }
            
            .filter-btn {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
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
        
        <main class="events-wrapper">
            <div class="events-header">
                <h1>All Events</h1>
                <p>Browse our complete collection of premium experiences. Find your perfect event.</p>
                
                <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                    <a href="cart.php" class="cart-indicator">
                        View Cart (<?php echo count($_SESSION['cart']); ?> items)
                    </a>
                <?php endif; ?>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success" style="max-width: 600px; margin: 2rem auto;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="filters">
                <button class="filter-btn active" onclick="filterEvents('all')">All Events</button>
                <button class="filter-btn" onclick="filterEvents('featured')">Featured</button>
                <button class="filter-btn" onclick="filterEvents('upcoming')">Upcoming</button>
                <button class="filter-btn" onclick="filterEvents('music')">Music</button>
                <button class="filter-btn" onclick="filterEvents('formal')">Formal</button>
            </div>

            <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="events-grid">
                <?php while ($event = mysqli_fetch_assoc($result)): 
                    $is_upcoming = strtotime($event['date']) > time();
                    
                    // Determine ticket status
                    $ticket_status = 'available';
                    $ticket_text = "{$event['available_tickets']} available";
                    if ($event['available_tickets'] <= 0) {
                        $ticket_status = 'low';
                        $ticket_text = 'Sold Out';
                    } elseif ($event['available_tickets'] <= 20) {
                        $ticket_status = 'limited';
                        $ticket_text = "Only {$event['available_tickets']} left";
                    }
                ?>
                <div class="event-card" data-category="<?php echo strtolower($event['category']); ?>"
                     data-featured="<?php echo $event['featured'] ? 'true' : 'false'; ?>"
                     data-upcoming="<?php echo $is_upcoming ? 'true' : 'false'; ?>">
                    
                    <?php if ($event['featured']): ?>
                        <div style="display: inline-block; background: #ff66cc; color: white; padding: 0.3rem 0.8rem; border-radius: 12px; font-size: 0.8rem; margin-bottom: 1rem;">
                            Featured
                        </div>
                    <?php endif; ?>
                    
                    <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                    
                    <div class="event-details">
                        <div class="detail-item">
                            <strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>Date:</strong> <?php echo date('F j, Y', strtotime($event['date'])); ?>
                        </div>
                        <div class="detail-item">
                            <strong>Time:</strong> 7:00 PM Start
                        </div>
                        <div class="detail-item">
                            <?php echo htmlspecialchars(substr($event['description'], 0, 100)); ?>...
                        </div>
                    </div>

                    <div class="price-tag">
                        $<?php echo number_format($event['price'], 2); ?>
                    </div>

                    <div class="ticket-info">
                        <span>Tickets:</span>
                        <span class="<?php echo $ticket_status; ?>">
                            <?php echo $ticket_text; ?>
                        </span>
                    </div>

                    <div class="event-actions">
                        <?php if ($event['available_tickets'] > 0 && $is_upcoming): ?>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="?add_to_cart=<?php echo $event['id']; ?>" 
                                   class="add-to-cart-btn">
                                    Add to Cart
                                </a>
                            <?php else: ?>
                                <a href="login.php" class="add-to-cart-btn">
                                    Login to Book
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <button class="add-to-cart-btn" disabled>
                                <?php echo $is_upcoming ? 'Sold Out' : 'Event Passed'; ?>
                            </button>
                        <?php endif; ?>
                        
                        <button class="view-details-btn" 
                                onclick="showEventDetails(<?php echo htmlspecialchars(json_encode($event)); ?>)">
                            View Details
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="no-events">
                <h2>No Events Available</h2>
                <p>We're currently preparing some amazing experiences for you.</p>
                <p>Check back soon or subscribe to our newsletter for updates!</p>
                <a href="index.php" class="btn" style="margin-top: 2rem;">
                    Return to Home
                </a>
            </div>
            <?php endif; ?>
        </main>
        
        <?php include 'footer.php'; ?>
    </div>

    <script>
    function filterEvents(category) {
        const cards = document.querySelectorAll('.event-card');
        const filterBtns = document.querySelectorAll('.filter-btn');
        
        // Update active button
        filterBtns.forEach(btn => {
            btn.classList.remove('active');
            if (btn.textContent.toLowerCase().includes(category)) {
                btn.classList.add('active');
            }
        });
        
        // Filter cards
        cards.forEach(card => {
            let show = false;
            
            switch(category) {
                case 'all':
                    show = true;
                    break;
                case 'featured':
                    show = card.dataset.featured === 'true';
                    break;
                case 'upcoming':
                    show = card.dataset.upcoming === 'true';
                    break;
                default:
                    show = card.dataset.category === category;
            }
            
            card.style.display = show ? 'block' : 'none';
        });
    }
    
    function showEventDetails(event) {
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            padding: 1rem;
        `;
        
        const isUpcoming = new Date(event.date) > new Date();
        
        modal.innerHTML = `
            <div style="background: #1a1a1a; padding: 2.5rem; border-radius: 15px; max-width: 500px; width: 100%; border: 1px solid #ff66cc;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                    <h2 style="color: #ff66cc; margin: 0; font-size: 1.5rem;">${event.title}</h2>
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                            style="background: none; border: none; color: #fff; font-size: 1.2rem; cursor: pointer;">
                        X
                    </button>
                </div>
                <div style="color: #b8b8b8; line-height: 1.6;">
                    <p><strong>Venue:</strong> ${event.venue}</p>
                    <p><strong>Date:</strong> ${new Date(event.date).toLocaleDateString('en-US', { 
                        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
                    })}</p>
                    <p><strong>Price:</strong> $${parseFloat(event.price).toFixed(2)}</p>
                    <p><strong>Available Tickets:</strong> ${event.available_tickets}</p>
                    <p><strong>Description:</strong> ${event.description}</p>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    ${isUpcoming && event.available_tickets > 0 ? 
                        `${<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?> ? 
                            `<a href="?add_to_cart=${event.id}" 
                               style="flex: 1; padding: 1rem; background: linear-gradient(135deg, #ff66cc, #9b59b6); color: white; border-radius: 8px; text-decoration: none; text-align: center; font-weight: bold;">
                                Book Now
                            </a>` : 
                            `<a href="login.php" 
                               style="flex: 1; padding: 1rem; background: linear-gradient(135deg, #ff66cc, #9b59b6); color: white; border-radius: 8px; text-decoration: none; text-align: center; font-weight: bold;">
                                Login to Book
                            </a>`
                        }` : 
                        '<button style="flex: 1; padding: 1rem; background: #666; color: #ccc; border-radius: 8px; border: none; font-weight: bold;" disabled>Not Available</button>'
                    }
                    <button onclick="this.parentElement.parentElement.parentElement.remove()"
                            style="padding: 1rem 1.5rem; background: transparent; border: 1px solid #9b59b6; color: #9b59b6; border-radius: 8px; cursor: pointer; font-weight: bold;">
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