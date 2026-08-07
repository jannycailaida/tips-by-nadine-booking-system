<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Tips by Nadine'; ?></title>
    <meta name="description" content="Tips by Nadine - Professional Nail Salon Booking System">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('css/app.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('css/components.css'); ?>">
</head>
<body data-base-url="<?php echo base_url(); ?>">
    <!-- Skip Link -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Navigation -->
    <nav class="nav" role="navigation" aria-label="Main navigation">
        <div class="container nav-container">
            <a href="<?php echo base_url(); ?>" class="nav-brand" aria-label="Tips by Nadine Home">
                <svg class="nav-logo" width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                    <circle cx="16" cy="16" r="14" stroke="currentColor" stroke-width="2"/>
                    <path d="M16 8C11.5817 8 8 11.5817 8 16C8 20.4183 11.5817 24 16 24C20.4183 24 24 20.4183 24 16C24 11.5817 20.4183 8 16 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 12V16L20 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="nav-brand-text">Tips by Nadine</span>
            </a>

            <div class="nav-links">
                <a href="<?php echo base_url('gallery.php'); ?>" class="nav-link">Gallery</a>
                <a href="<?php echo base_url('about.php'); ?>" class="nav-link">About</a>
                <a href="<?php echo base_url('contact.php'); ?>" class="nav-link">Contact</a>
            </div>

            <div class="nav-actions">
                <?php if (Auth::isUserLoggedIn()): ?>
                    <a href="<?php echo base_url('dashboard.php'); ?>" class="btn btn-secondary">My Bookings</a>
                    <a href="<?php echo base_url('booking.php'); ?>" class="btn btn-primary">Book Now</a>
                    <a href="<?php echo base_url('logout.php'); ?>" class="btn btn-ghost">Logout</a>
                <?php elseif (Auth::isAdminLoggedIn()): ?>
                    <a href="<?php echo base_url('admin/dashboard.php'); ?>" class="btn btn-primary">Admin Panel</a>
                    <a href="<?php echo base_url('admin/logout.php'); ?>" class="btn btn-ghost">Logout</a>
                <?php else: ?>
                    <a href="<?php echo base_url('login.php'); ?>" class="btn btn-ghost">Login</a>
                    <a href="<?php echo base_url('register.php'); ?>" class="btn btn-primary">Sign Up</a>
                <?php endif; ?>
            </div>

            <button class="nav-toggle" aria-expanded="false" aria-controls="nav-menu" aria-label="Toggle menu">
                <span class="hamburger"></span>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="nav-mobile" id="nav-menu" role="dialog" aria-label="Mobile menu">
        <div class="nav-mobile-inner">
            <a href="<?php echo base_url('gallery.php'); ?>" class="nav-mobile-link">Gallery</a>
            <a href="<?php echo base_url('about.php'); ?>" class="nav-mobile-link">About</a>
            <a href="<?php echo base_url('contact.php'); ?>" class="nav-mobile-link">Contact</a>
            <div class="nav-mobile-divider"></div>
            <?php if (Auth::isUserLoggedIn()): ?>
                <a href="<?php echo base_url('dashboard.php'); ?>" class="nav-mobile-link">My Bookings</a>
                <a href="<?php echo base_url('booking.php'); ?>" class="nav-mobile-link btn btn-primary" style="text-align:center; margin-top:8px;">Book Now</a>
                <a href="<?php echo base_url('logout.php'); ?>" class="nav-mobile-link">Logout</a>
            <?php elseif (Auth::isAdminLoggedIn()): ?>
                <a href="<?php echo base_url('admin/dashboard.php'); ?>" class="nav-mobile-link">Admin Panel</a>
                <a href="<?php echo base_url('admin/logout.php'); ?>" class="nav-mobile-link">Logout</a>
            <?php else: ?>
                <a href="<?php echo base_url('login.php'); ?>" class="nav-mobile-link">Login</a>
                <a href="<?php echo base_url('register.php'); ?>" class="nav-mobile-link btn btn-primary" style="text-align:center; margin-top:8px;">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content -->
    <main id="main-content">
        <?php if (isset($flash)): ?>
            <div class="flash flash-<?php echo $flash['type']; ?>" role="alert">
                <div class="container">
                    <?php echo $flash['message']; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php echo $content ?? ''; ?>
    </main>

    <!-- Footer -->
    <footer class="footer" role="contentinfo">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="<?php echo base_url(); ?>" class="footer-logo" aria-label="Tips by Nadine Home">
                        <svg width="28" height="28" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                            <circle cx="16" cy="16" r="14" stroke="currentColor" stroke-width="2"/>
                            <path d="M16 8C11.5817 8 8 11.5817 8 16C8 20.4183 11.5817 24 16 24C20.4183 24 24 20.4183 24 16C24 11.5817 20.4183 8 16 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 12V16L20 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Tips by Nadine</span>
                    </a>
                    <p class="footer-tagline">Serenity in every stroke. Professional nail care and artistic designs, crafted for your relaxation.</p>
                </div>

                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?php echo base_url('gallery.php'); ?>">Design Gallery</a></li>
                        <li><a href="<?php echo base_url('booking.php'); ?>">Book Appointment</a></li>
                        <li><a href="<?php echo base_url('about.php'); ?>">About Us</a></li>
                        <li><a href="<?php echo base_url('contact.php'); ?>">Contact</a></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="<?php echo base_url('gallery.php'); ?>">Manicure</a></li>
                        <li><a href="<?php echo base_url('gallery.php'); ?>">Pedicure</a></li>
                        <li><a href="<?php echo base_url('gallery.php'); ?>">Gel & Acrylic</a></li>
                        <li><a href="<?php echo base_url('gallery.php'); ?>">Nail Art</a></li>
                    </ul>
                </div>

                <div class="footer-contact">
                    <h4>Visit Us</h4>
                    <address>
                        <p>123 Beauty Street</p>
                        <p>Manila, Philippines</p>
                        <p><a href="tel:+6321234567">+63 2 123 4567</a></p>
                        <p><a href="mailto:info@tipsbynadine.com">info@tipsbynadine.com</a></p>
                    </address>
                    <h4 style="margin-top: var(--spacing-lg);">Hours</h4>
                    <address>
                        <p>Mon – Sat · 9:00 AM – 7:00 PM</p>
                        <p>Sunday · 10:00 AM – 5:00 PM</p>
                    </address>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Tips by Nadine. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="<?php echo base_url('js/app.js'); ?>"></script>
    <?php if (isset($extraScripts)): ?>
        <?php foreach ($extraScripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>