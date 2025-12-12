<?php
session_start();
require_once "config.php";

// Get featured events
$featured_sql = "SELECT * FROM Events WHERE featured = TRUE ORDER BY date LIMIT 3";
$featured_result = mysqli_query($conn, $featured_sql);

// Get upcoming events
$upcoming_sql = "SELECT * FROM Events WHERE date >= CURDATE() ORDER BY date LIMIT 6";
$upcoming_result = mysqli_query($conn, $upcoming_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StagePass - Premium Event Ticketing</title>
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .hero {
            text-align: center;
            padding: 5rem 2rem;
            margin: 2rem auto;
            max-width: 900px;
            background: rgba(26, 26, 26, 0.8);
            border-radius: 20px;
            border: 1px solid rgba(255, 102, 204, 0.2);
        }

        .hero h1 {
            font-size: 3.5rem;
            color: #ff66cc;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }

        .hero .tagline {
            font-size: 1.5rem;
            color: #9b59b6;
            margin-bottom: 1.5rem;
            font-weight: 300;
        }

        .hero .description {
            font-size: 1.1rem;
            color: #b8b8b8;
            max-width: 600px;
            margin: 0 auto 3rem;
            line-height: 1.7;
        }

        .hero-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            width: 100%;
            max-width: 1200px;
            margin: 3rem auto;
        }

        .feature-card {
            background: rgba(40, 40, 40, 0.8);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 102, 204, 0.3);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: #ff66cc;
            display: inline-block;
        }

        .feature-card h3 {
            color: #ff66cc;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .feature-card p {
            color: #b8b8b8;
            line-height: 1.6;
        }

        .events-preview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            width: 100%;
            max-width: 1200px;
            margin: 3rem auto;
        }

        .event-preview-card {
            background: rgba(40, 40, 40, 0.8);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .event-preview-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 102, 204, 0.3);
        }

        .event-preview-card h3 {
            color: #fff;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .event-preview-card p {
            color: #b8b8b8;
            margin: 0.5rem 0;
        }

        .event-preview-card .price {
            color: #ff66cc;
            font-weight: 700;
            font-size: 1.3rem;
            margin: 1rem 0;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            width: 100%;
            max-width: 1000px;
            margin: 4rem auto;
            text-align: center;
        }

        .stat-item {
            padding: 2rem;
            background: rgba(40, 40, 40, 0.8);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: #ff66cc;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .hero {
                padding: 3rem 1.5rem;
                margin: 1rem auto;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero .tagline {
                font-size: 1.2rem;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .features-grid,
            .events-preview {
                grid-template-columns: 1fr;
                padding: 0 1rem;
            }
            
            .stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            .hero h1 {
                font-size: 2rem;
            }
            
            .stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'navigation.php'; ?>
        
        <main>
            <!-- Hero Section -->
            <section class="hero">
                <h1>STAGE PASS</h1>
                <p class="tagline">Exclusive Access to Premium Events</p>
                <p class="description">
                    Discover premium concerts, sports events, theater performances, 
                    and exclusive gatherings. Secure your spot with our seamless 
                    booking platform.
                </p>
                
                <div class="hero-buttons">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="events.php" class="btn">
                            Browse Events
                        </a>
                        <a href="tickets.php" class="btn btn-outline">
                            View My Tickets
                        </a>
                    <?php else: ?>
                        <a href="register.php" class="btn">
                            Register Now
                        </a>
                        <a href="login.php" class="btn btn-outline">
                            Login
                        </a>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Features -->
            <section class="section">
                <h2>Why Choose StagePass?</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">✓</div>
                        <h3>Curated Selection</h3>
                        <p>Handpicked premium events for discerning tastes</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">✓</div>
                        <h3>Secure Booking</h3>
                        <p>Military-grade encryption for all transactions</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">✓</div>
                        <h3>Digital Tickets</h3>
                        <p>Instant access, mobile-friendly, no printing needed</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">✓</div>
                        <h3>VIP Experience</h3>
                        <p>Exclusive perks and priority access</p>
                    </div>
                </div>
            </section>

            <!-- Upcoming Events -->
            <section class="section">
                <h2>Featured Events</h2>
                <?php if (mysqli_num_rows($featured_result) > 0): ?>
                <div class="events-preview">
                    <?php while ($event = mysqli_fetch_assoc($featured_result)): ?>
                    <div class="event-preview-card">
                        <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p style="color: #9b59b6;">
                            <?php echo date('F j, Y', strtotime($event['date'])); ?>
                        </p>
                        <p style="color: #b8b8b8;">
                            <?php echo htmlspecialchars($event['venue']); ?>
                        </p>
                        <p class="price">
                            $<?php echo number_format($event['price'], 2); ?>
                        </p>
                        <a href="events.php" class="btn btn-small">
                            View Details
                        </a>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
            </section>

            <!-- Stats -->
            <section class="section">
                <h2>Our Impact</h2>
                <div class="stats">
                    <div class="stat-item">
                        <div class="stat-number">50K+</div>
                        <p style="color: #b8b8b8;">Happy Customers</p>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <p style="color: #b8b8b8;">Premium Events</p>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">99.9%</div>
                        <p style="color: #b8b8b8;">Satisfaction Rate</p>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <p style="color: #b8b8b8;">Support Available</p>
                    </div>
                </div>
            </section>

            <!-- All Events Preview -->
            <section class="section">
                <h2>Upcoming Events</h2>
                <?php if (mysqli_num_rows($upcoming_result) > 0): ?>
                <div class="grid grid-3">
                    <?php while ($event = mysqli_fetch_assoc($upcoming_result)): ?>
                    <div class="card">
                        <h3 style="font-size: 1.2rem;"><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p style="color: #9b59b6; margin: 0.5rem 0;">
                            <?php echo date('M j, Y', strtotime($event['date'])); ?>
                        </p>
                        <p style="color: #b8b8b8; font-size: 0.9rem; margin: 0.5rem 0;">
                            <?php echo htmlspecialchars($event['venue']); ?>
                        </p>
                        <p style="color: #ff66cc; font-weight: 700; margin: 1rem 0;">
                            $<?php echo number_format($event['price'], 2); ?>
                        </p>
                        <a href="events.php" class="btn btn-small">
                            Learn More
                        </a>
                    </div>
                    <?php endwhile; ?>
                </div>
                <div style="text-align: center; margin-top: 2rem;">
                    <a href="events.php" class="btn">
                        View All Events
                    </a>
                </div>
                <?php else: ?>
                <p style="color: #b8b8b8; text-align: center; padding: 2rem;">
                    No upcoming events scheduled. New events added daily!
                </p>
                <?php endif; ?>
            </section>

            <!-- CTA Section -->
            <section class="section" style="background: rgba(255, 102, 204, 0.05);">
                <h2>Ready for Your Next Experience?</h2>
                <p style="color: #e0e0e0; max-width: 600px; margin: 1rem auto 2rem;">
                    Join thousands of members enjoying exclusive access to premium events. 
                    Your unforgettable experience starts here.
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="register.php" class="btn">
                            Get Started Free
                        </a>
                    <?php endif; ?>
                    <a href="contact.php" class="btn btn-outline">
                        Contact Our Team
                    </a>
                </div>
            </section>
        </main>
        
        <?php include 'footer.php'; ?>
    </div>
</body>
</html>