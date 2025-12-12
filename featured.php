<?php
session_start();
require_once "config.php";

// Get only featured events
$sql = "SELECT * FROM Events WHERE featured = TRUE ORDER BY date";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Featured Events - StagePass</title>
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .featured-wrapper {
            width: 100%;
            padding: 2rem 0;
        }

        .featured-header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 3rem 2rem;
            background: rgba(255, 102, 204, 0.05);
            border-radius: 15px;
            border: 1px solid rgba(255, 102, 204, 0.2);
            max-width: 1000px;
            margin: 0 auto 3rem;
        }

        .featured-header h1 {
            font-size: 2.5rem;
            color: #ff66cc;
            margin-bottom: 1rem;
        }

        .featured-header p {
            font-size: 1.1rem;
            color: #e0e0e0;
            max-width: 700px;
            margin: 0 auto 1.5rem;
            line-height: 1.6;
        }

        .featured-count {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: rgba(255, 102, 204, 0.2);
            border-radius: 20px;
            color: #ff66cc;
            font-weight: 600;
            margin-top: 1rem;
        }

        .featured-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .featured-card {
            background: rgba(40, 40, 40, 0.8);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(255, 102, 204, 0.2);
            transition: all 0.3s ease;
            position: relative;
        }

        .featured-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 102, 204, 0.4);
            background: rgba(50, 50, 50, 0.9);
        }

        .featured-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ff66cc;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .featured-card h3 {
            color: #fff;
            font-size: 1.3rem;
            margin-bottom: 1rem;
            padding-right: 80px;
        }

        .featured-details {
            margin: 1.5rem 0;
            color: #b8b8b8;
            font-size: 0.95rem;
        }

        .detail-row {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 0.8rem;
        }

        .featured-price {
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

        .featured-actions {
            display: flex;
            gap: 1rem;
        }

        .featured-btn {
            flex: 1;
            padding: 1rem;
            background: linear-gradient(135deg, #ff66cc, #9b59b6);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
        }

        .featured-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #ff4da6, #8e44ad);
        }

        .featured-btn.outline {
            background: transparent;
            border: 1px solid #9b59b6;
            color: #9b59b6;
        }

        .featured-btn.outline:hover {
            background: rgba(155, 89, 182, 0.1);
        }

        .featured-btn:disabled {
            background: #666;
            color: #ccc;
            cursor: not-allowed;
        }

        .no-featured {
            text-align: center;
            padding: 4rem 2rem;
            color: #b8b8b8;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .no-featured h2 {
            color: #ff66cc;
            margin-bottom: 1.5rem;
        }

        .benefits {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1000px;
            margin: 4rem auto;
            padding: 2rem;
            background: rgba(30, 30, 30, 0.5);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .benefit-card {
            text-align: center;
            padding: 2rem;
            background: rgba(40, 40, 40, 0.8);
            border-radius: 10px;
            border: 1px solid rgba(255, 102, 204, 0.1);
        }

        .benefit-card h3 {
            color: #ff66cc;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        @media (max-width: 768px) {
            .featured-header {
                padding: 2rem 1rem;
                margin: 1rem auto 2rem;
            }
            
            .featured-header h1 {
                font-size: 2rem;
            }
            
            .featured-grid {
                grid-template-columns: 1fr;
                padding: 0 1rem;
                gap: 1.5rem;
            }
            
            .featured-actions {
                flex-direction: column;
            }
            
            .benefits {
                grid-template-columns: 1fr;
                padding: 1.5rem;
                margin: 2rem auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'navigation.php'; ?>
        
        <main class="featured-wrapper">
            <!-- Header -->
            <div class="featured-header">
                <h1>Featured Events</h1>
                <p>
                    Experience the extraordinary with our handpicked selection of premium events. 
                    These exclusive gatherings offer unforgettable experiences and VIP treatment.
                </p>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="featured-count">
                        <?php echo mysqli_num_rows($result); ?> Exclusive Events
                    </div>
                <?php endif; ?>
            </div>

            <!-- Benefits -->
            <div class="benefits">
                <div class="benefit-card">
                    <h3>VIP Access</h3>
                    <p style="color: #b8b8b8;">Priority entry and premium seating</p>
                </div>
                <div class="benefit-card">
                    <h3>Complimentary Services</h3>
                    <p style="color: #b8b8b8;">Open bars and gourmet catering</p>
                </div>
                <div class="benefit-card">
                    <h3>Exclusive Swag</h3>
                    <p style="color: #b8b8b8;">Limited edition merchandise</p>
                </div>
                <div class="benefit-card">
                    <h3>Meet & Greet</h3>
                    <p style="color: #b8b8b8;">Photo opportunities with stars</p>
                </div>
            </div>

            <!-- Featured Events Grid -->
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="featured-grid">
                    <?php while ($event = mysqli_fetch_assoc($result)): 
                        $is_upcoming = strtotime($event['date']) > time();
                    ?>
                    <div class="featured-card">
                        <div class="featured-badge">Featured</div>
                        
                        <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                        
                        <div class="featured-details">
                            <div class="detail-row">
                                <strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?>
                            </div>
                            <div class="detail-row">
                                <strong>Date:</strong> <?php echo date('F j, Y', strtotime($event['date'])); ?>
                            </div>
                            <div class="detail-row">
                                <strong>Time:</strong> 8:00 PM Start
                            </div>
                            <div class="detail-row">
                                <?php echo htmlspecialchars(substr($event['description'], 0, 120)); ?>...
                            </div>
                        </div>

                        <div class="featured-price">
                            $<?php echo number_format($event['price'], 2); ?>
                        </div>

                        <div class="featured-actions">
                            <?php if ($is_upcoming): ?>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="?add_to_cart=<?php echo $event['id']; ?>" 
                                       class="featured-btn">
                                        Book Now
                                    </a>
                                <?php else: ?>
                                    <a href="login.php" class="featured-btn">
                                        Login to Book
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="featured-btn" disabled>
                                    Event Passed
                                </button>
                            <?php endif; ?>
                            
                            <button class="featured-btn outline" 
                                    onclick="showEventDetails(<?php echo htmlspecialchars(json_encode($event)); ?>)">
                                Details
                            </button>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-featured">
                    <h2>No Featured Events Currently</h2>
                    <p>We're currently curating our next batch of premium featured events.</p>
                    <p>Check back soon or browse our <a href="events.php" style="color: #ff66cc;">regular events</a>.</p>
                    <div style="margin-top: 2rem;">
                        <a href="events.php" class="btn">
                            Browse All Events
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- CTA Section -->
            <section class="section" style="margin-top: 4rem; background: rgba(255, 102, 204, 0.05);">
                <h2>Want More Exclusive Access?</h2>
                <p style="color: #e0e0e0; max-width: 600px; margin: 1rem auto 2rem;">
                    Join our VIP membership program for early access to all featured events, 
                    exclusive discounts, and premium benefits.
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="register.php" class="btn">
                            Join VIP Program
                        </a>
                    <?php endif; ?>
                    <a href="contact.php" class="btn btn-outline">
                        Inquire About VIP
                    </a>
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
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            padding: 1rem;
        `;
        
        const isUpcoming = new Date(event.date) > new Date();
        
        modal.innerHTML = `
            <div style="background: #1a1a1a; padding: 2rem; border-radius: 15px; max-width: 500px; width: 100%; border: 1px solid #ff66cc; position: relative;">
                <button onclick="this.parentElement.parentElement.remove()" 
                        style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; color: #fff; font-size: 1.2rem; cursor: pointer;">
                    X
                </button>
                
                <h3 style="color: #ff66cc; margin-bottom: 1.5rem; text-align: center;">${event.title}</h3>
                
                <div style="color: #b8b8b8; line-height: 1.6; margin-bottom: 2rem;">
                    <p><strong>Venue:</strong> ${event.venue}</p>
                    <p><strong>Date:</strong> ${new Date(event.date).toLocaleDateString()}</p>
                    <p><strong>Price:</strong> $${parseFloat(event.price).toFixed(2)}</p>
                    <p><strong>Available Tickets:</strong> ${event.available_tickets}</p>
                    <p><strong>Description:</strong> ${event.description}</p>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    ${isUpcoming && event.available_tickets > 0 ? 
                        `${<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?> ? 
                            `<a href="?add_to_cart=${event.id}" 
                               style="flex: 1; padding: 1rem; background: linear-gradient(135deg, #ff66cc, #9b59b6); color: white; border-radius: 8px; text-decoration: none; text-align: center;">
                                Book Now
                            </a>` : 
                            `<a href="login.php" 
                               style="flex: 1; padding: 1rem; background: linear-gradient(135deg, #ff66cc, #9b59b6); color: white; border-radius: 8px; text-decoration: none; text-align: center;">
                                Login to Book
                            </a>`
                        }` : 
                        '<button style="flex: 1; padding: 1rem; background: #666; color: #ccc; border-radius: 8px; border: none;" disabled>Not Available</button>'
                    }
                    <button onclick="this.parentElement.parentElement.parentElement.remove()"
                            style="padding: 1rem 1.5rem; background: transparent; border: 1px solid #9b59b6; color: #9b59b6; border-radius: 8px; cursor: pointer;">
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