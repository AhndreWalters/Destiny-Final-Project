<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - StagePass</title>
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .faq-wrapper {
            width: 100%;
            padding: 2rem 0;
        }

        .faq-header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem;
        }

        .faq-header h1 {
            font-size: 2.5rem;
            color: #ff66cc;
            margin-bottom: 1rem;
        }

        .faq-header p {
            font-size: 1.1rem;
            color: #b8b8b8;
            max-width: 700px;
            margin: 0 auto;
        }

        .search-box {
            max-width: 600px;
            margin: 2rem auto;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            background: rgba(40, 40, 40, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
        }

        .search-input:focus {
            outline: none;
            border-color: #9b59b6;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9b59b6;
        }

        .categories {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin: 2rem 0 3rem;
        }

        .category-btn {
            padding: 0.8rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            color: #b8b8b8;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .category-btn:hover,
        .category-btn.active {
            background: rgba(255, 102, 204, 0.2);
            color: #ff66cc;
            border-color: rgba(255, 102, 204, 0.3);
        }

        .faq-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .faq-category {
            margin-bottom: 3rem;
        }

        .category-title {
            color: #ff66cc;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(255, 102, 204, 0.2);
        }

        .faq-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .faq-item {
            background: rgba(40, 40, 40, 0.5);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            overflow: hidden;
        }

        .faq-question {
            padding: 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
        }

        .faq-question:hover {
            background: rgba(255, 102, 204, 0.05);
        }

        .faq-toggle {
            color: #9b59b6;
            transition: transform 0.3s ease;
        }

        .faq-answer {
            padding: 0 1.5rem;
            max-height: 0;
            overflow: hidden;
            color: #b8b8b8;
            line-height: 1.6;
            transition: all 0.3s ease;
        }

        .faq-item.active .faq-answer {
            padding: 0 1.5rem 1.5rem;
            max-height: 1000px;
        }

        .faq-item.active .faq-toggle {
            transform: rotate(45deg);
        }

        .contact-cta {
            text-align: center;
            padding: 3rem 2rem;
            margin: 4rem auto;
            background: rgba(255, 102, 204, 0.05);
            border-radius: 15px;
            border: 1px solid rgba(255, 102, 204, 0.2);
            max-width: 800px;
        }

        .contact-cta h2 {
            color: #ff66cc;
            margin-bottom: 1rem;
        }

        .contact-cta p {
            color: #b8b8b8;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .no-results {
            text-align: center;
            padding: 3rem 2rem;
            color: #b8b8b8;
            font-size: 1.1rem;
            display: none;
        }

        .no-results.show {
            display: block;
        }

        @media (max-width: 768px) {
            .faq-header h1 {
                font-size: 2rem;
            }
            
            .categories {
                gap: 0.5rem;
            }
            
            .category-btn {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
            }
            
            .faq-question {
                padding: 1.2rem;
                font-size: 1rem;
            }
            
            .contact-cta {
                padding: 2rem 1rem;
                margin: 2rem auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'navigation.php'; ?>
        
        <main class="faq-wrapper">
            <div class="faq-header">
                <h1>Frequently Asked Questions</h1>
                <p>Find answers to common questions about StagePass.</p>
                
                <div class="search-box">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-input" id="searchInput" 
                           placeholder="Search for answers...">
                </div>
            </div>

            <!-- Categories -->
            <div class="categories">
                <button class="category-btn active" data-category="all">All Questions</button>
                <button class="category-btn" data-category="tickets">Tickets</button>
                <button class="category-btn" data-category="account">Account</button>
                <button class="category-btn" data-category="events">Events</button>
                <button class="category-btn" data-category="payments">Payments</button>
            </div>

            <!-- FAQ Container -->
            <div class="faq-container">
                <!-- Tickets Category -->
                <div class="faq-category" data-category="tickets">
                    <h2 class="category-title">Tickets</h2>
                    
                    <div class="faq-list">
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>How do I receive my tickets?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                Tickets are delivered digitally via email immediately after purchase. You can also access them anytime from your "My Tickets" page. Each ticket includes a unique QR code for entry.
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>Can I transfer my ticket to someone else?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                Yes! You can transfer tickets through your account dashboard up to 24 hours before the event. The new attendee will receive a new QR code via email.
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>How do VIP tickets differ from regular tickets?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                VIP tickets include premium seating/standing areas, expedited entry, dedicated bars and restrooms, and sometimes meet-and-greet opportunities with performers.
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>What should I do if I lose my ticket?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                You can re-download your ticket anytime from your account. Each QR code can only be scanned once, so your original ticket becomes invalid when you download a new one.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Category -->
                <div class="faq-category" data-category="account">
                    <h2 class="category-title">Account</h2>
                    
                    <div class="faq-list">
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>How do I update my account information?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                You can update your name, email, and password anytime from your Account page. Some changes may require verification via email.
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>Can I delete my account?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                Yes, you can request account deletion through your Account settings. Please note that this action is permanent and cannot be undone.
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>How do I reset my password?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                Click "Forgot password" on the login page and enter your email address. You'll receive a password reset link within 5 minutes.
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>Is my personal information secure?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                Yes, we use bank-level encryption to protect all your personal data. We never share your information with third parties without your consent.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Events Category -->
                <div class="faq-category" data-category="events">
                    <h2 class="category-title">Events</h2>
                    
                    <div class="faq-list">
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>What happens if an event is cancelled?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                If we cancel an event, you will receive a full refund automatically within 5-7 business days. You may also choose to transfer your ticket to another event.
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>Is there an age restriction for events?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                Age requirements vary by event. Please check the event details page. Most club events are 18+, while formal events may be 21+. Family events are all ages.
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>How early should I arrive at an event?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                We recommend arriving 30-45 minutes before the event start time. VIP ticket holders can arrive up to 15 minutes before general admission.
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>Can I bring guests to VIP events?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                VIP events typically require individual tickets for each attendee. Some corporate or private events may allow plus-ones - check the event details.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payments Category -->
                <div class="faq-category" data-category="payments">
                    <h2 class="category-title">Payments</h2>
                    
                    <div class="faq-list">
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>What is your refund policy?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                Full refunds are available up to 7 days before the event. Within 7 days, refunds are subject to a 20% service fee. No refunds within 24 hours of the event.
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>What payment methods do you accept?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                We accept all major credit/debit cards (Visa, MasterCard, Amex), PayPal, Apple Pay, and Google Pay. All payments are securely encrypted.
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>When will I be charged for my purchase?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                Your payment method is charged immediately upon purchase confirmation. You'll receive an email receipt within minutes of your purchase.
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>Do you offer payment plans?</span>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                For events over $200, we offer 2-3 month payment plans. These must be selected at checkout and require a credit check for new customers.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No Results Message -->
            <div class="no-results" id="noResults">
                <h2 style="color: #ff66cc; margin-bottom: 1rem;">No Results Found</h2>
                <p>We couldn't find any questions matching your search. Try different keywords.</p>
            </div>

            <!-- Contact CTA -->
            <div class="contact-cta">
                <h2>Still Need Help?</h2>
                <p>Our support team is available 24/7 to assist you with any questions.</p>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="contact.php" class="btn">
                        Contact Support
                    </a>
                    <a href="index.php" class="btn btn-outline">
                        Return Home
                    </a>
                </div>
            </div>
        </main>
        
        <?php include 'footer.php'; ?>
    </div>

    <script>
    // FAQ Toggle Functionality
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', () => {
            const faqItem = question.parentElement;
            const isActive = faqItem.classList.contains('active');
            
            // Close all other FAQs
            document.querySelectorAll('.faq-item.active').forEach(item => {
                if (item !== faqItem) {
                    item.classList.remove('active');
                }
            });
            
            // Toggle current FAQ
            faqItem.classList.toggle('active', !isActive);
        });
    });
    
    // Category Filtering
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const category = btn.dataset.category;
            
            // Update active button
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Show/hide categories
            document.querySelectorAll('.faq-category').forEach(cat => {
                if (category === 'all' || cat.dataset.category === category) {
                    cat.style.display = 'block';
                } else {
                    cat.style.display = 'none';
                }
            });
            
            // Hide no results message
            document.getElementById('noResults').classList.remove('show');
        });
    });
    
    // Search Functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase().trim();
        const faqItems = document.querySelectorAll('.faq-item');
        const categories = document.querySelectorAll('.faq-category');
        let hasResults = false;
        
        if (searchTerm === '') {
            // Show all when search is empty
            faqItems.forEach(item => {
                item.style.display = 'block';
                item.parentElement.parentElement.style.display = 'block';
            });
            document.getElementById('noResults').classList.remove('show');
            return;
        }
        
        // Search through FAQs
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
            
            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
                item.parentElement.parentElement.style.display = 'block';
                hasResults = true;
            } else {
                item.style.display = 'none';
                // Hide category if all items are hidden
                const category = item.parentElement.parentElement;
                const visibleItems = category.querySelectorAll('.faq-item[style*="display: block"]');
                if (visibleItems.length === 0) {
                    category.style.display = 'none';
                }
            }
        });
        
        // Show/hide no results message
        document.getElementById('noResults').classList.toggle('show', !hasResults);
    });
    </script>
</body>
</html>