<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Tips by Nadine'); ?></title>
    <?php
        // Per-page SEO metadata. Controllers set $meta['description'] (and
        // optionally ['title']/['image']) with the shared layout supplying
        // brand-consistent fallbacks so no page ships blank.
        $metaTitle = $meta['title'] ?? ($pageTitle ?? 'Tips by Nadine - Nail Salon & Art Studio');
        $metaDescription = $meta['description'] ?? 'Book serene nail care online at Tips by Nadine — fresh gel manicures, acrylics, pedicures and art, with smart booking and AI design recommendations.';
        $metaImage = $meta['image'] ?? base_url('assets/images/hero-seating.webp');
    ?>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <?php if (!empty($noindex)): ?>
        <meta name="robots" content="noindex, nofollow">
    <?php else: ?>
        <meta name="robots" content="index, follow">
    <?php endif; ?>
    <!-- Open Graph (Facebook, IG story embeds, messenger shares) -->
    <meta property="og:site_name" content="Tips by Nadine">
    <meta property="og:type" content="<?php echo $meta['og_type'] ?? 'website'; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($metaTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($metaImage); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars(base_url($pageRoute ?? '')); ?>">
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($metaTitle); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($metaImage); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('css/app.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('css/components.css'); ?>">
    <?php if (isset($jsonLd)): ?>
        <script type="application/ld+json">
        <?php echo json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
        </script>
    <?php endif; ?>
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
                    <p class="footer-tagline">Serenity in every finish. Professional design, and artistic studio, crafted for your relaxation.</p>
                    <?php $socialLinks = require __DIR__ . '/../config/app.php'; $socialLinks = $socialLinks['business']['social'] ?? []; ?>
                    <?php if (!empty(array_filter($socialLinks))): ?>
                        <div class="footer-social" aria-label="Follow Tips by Nadine">
                            <?php if (!empty($socialLinks['instagram'])): ?>
                                <a href="<?php echo htmlspecialchars($socialLinks['instagram']); ?>" class="footer-social-link" rel="noopener nofollow" target="_blank" aria-label="Tips by Nadine on Instagram">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($socialLinks['facebook'])): ?>
                                <a href="<?php echo htmlspecialchars($socialLinks['facebook']); ?>" class="footer-social-link" rel="noopener nofollow" target="_blank" aria-label="Tips by Nadine on Facebook">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($socialLinks['tiktok'])): ?>
                                <a href="<?php echo htmlspecialchars($socialLinks['tiktok']); ?>" class="footer-social-link" rel="noopener nofollow" target="_blank" aria-label="Tips by Nadine on TikTok">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
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