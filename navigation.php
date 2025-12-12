<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav id="top-menu">
    <ul>
        <li>
            <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                Home
            </a>
        </li>
        <li>
            <a href="events.php" class="<?php echo $current_page == 'events.php' ? 'active' : ''; ?>">
                Events
            </a>
        </li>
        <li>
            <a href="featured.php" class="<?php echo $current_page == 'featured.php' ? 'active' : ''; ?>">
                Featured
            </a>
        </li>
        <li>
            <a href="categories.php" class="<?php echo $current_page == 'categories.php' ? 'active' : ''; ?>">
                Categories
            </a>
        </li>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <li>
                <a href="tickets.php" class="<?php echo $current_page == 'tickets.php' ? 'active' : ''; ?>">
                    My Tickets
                </a>
            </li>
            <li>
                <a href="cart.php" class="<?php echo $current_page == 'cart.php' ? 'active' : ''; ?>">
                    🛒 Cart 
                    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="cart-count">
                            <?php echo count($_SESSION['cart']); ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
        <?php endif; ?>
        
        <li>
            <a href="contact.php" class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">
                Contact
            </a>
        </li>
        <li>
            <a href="faq.php" class="<?php echo $current_page == 'faq.php' ? 'active' : ''; ?>">
                FAQ
            </a>
        </li>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <li>
                <a href="account.php" class="<?php echo $current_page == 'account.php' ? 'active' : ''; ?>">
                    Account
                </a>
            </li>
            <li>
                <button onclick="window.location.href='logout.php'">
                    Logout
                </button>
            </li>
        <?php else: ?>
            <li>
                <button onclick="window.location.href='login.php'">
                    Login
                </button>
            </li>
        <?php endif; ?>
    </ul>
</nav>