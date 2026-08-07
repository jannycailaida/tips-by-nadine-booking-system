<!-- Hero Section -->
<section class="hero" aria-labelledby="hero-title">
    <div class="container">
        <div class="hero-content">
            <h1 id="hero-title" class="hero-title">Beautiful Nails, <span class="hero-accent">Effortlessly Booked</span></h1>
            <p class="hero-subtitle">Discover stunning nail designs, book appointments online, and get AI-powered design recommendations tailored to your style.</p>
            <div class="hero-actions">
                <a href="<?php echo base_url('booking.php'); ?>" class="btn btn-primary btn-lg">Book Appointment</a>
                <a href="<?php echo base_url('gallery.php'); ?>" class="btn btn-secondary btn-lg">Browse Designs</a>
            </div>
        </div>
        <div class="hero-visual" aria-hidden="true">
            <div class="hero-card">
                <div class="hero-card-inner">
                    <svg width="120" height="120" viewBox="0 0 120 120" fill="none" aria-hidden="true">
                        <circle cx="60" cy="60" r="50" stroke="#d4a574" stroke-width="3" stroke-dasharray="314" stroke-dashoffset="314" class="hero-ring"/>
                        <path d="M60 30C43.4315 30 30 43.4315 30 60C30 76.5685 43.4315 90 60 90C76.5685 90 90 76.5685 90 60C90 43.4315 76.5685 30 60 30Z" stroke="#d4a574" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" opacity="0.3"/>
                        <path d="M60 40V60L75 70" stroke="#d4a574" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="features" aria-labelledby="features-title">
    <div class="container">
        <header class="section-header">
            <h2 id="features-title" class="section-title">Why Choose Tips by Nadine</h2>
            <p class="section-subtitle">Modern nail care meets smart technology</p>
        </header>

        <div class="features-grid">
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <h3 class="feature-title">Easy Online Booking</h3>
                <p class="feature-description">Check real-time availability and book your appointment in seconds. No more phone calls or waiting for callbacks.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 4 23 10 23 16"/>
                        <polyline points="1 4 1 10 1 16"/>
                        <path d="M21 21H3"/>
                        <path d="M9 21V16a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v5"/>
                    </svg>
                </div>
                <h3 class="feature-title">AI Design Recommendations</h3>
                <p class="feature-description">Upload a reference photo and get personalized nail design suggestions powered by AI technology.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </div>
                <h3 class="feature-title">Email Notifications</h3>
                <p class="feature-description">Receive instant confirmations and timely reminders so you never miss an appointment.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 6v6l4 2"/>
                    </svg>
                </div>
                <h3 class="feature-title">Flexible Scheduling</h3>
                <p class="feature-description">Book appointments that fit your schedule with convenient time slots throughout the week.</p>
            </article>
        </div>
    </div>
</section>

<!-- Featured Designs -->
<section class="featured-designs" aria-labelledby="featured-title">
    <div class="container">
        <header class="section-header">
            <h2 id="featured-title" class="section-title">Featured Designs</h2>
            <p class="section-subtitle">Popular styles our clients love</p>
        </header>

        <div class="designs-grid">
            <?php foreach ($featuredDesigns as $design): ?>
                <article class="design-card">
                    <div class="design-image">
                        <?php if ($design['image_path']): ?>
                            <img src="<?php echo base_url(htmlspecialchars($design['image_path'])); ?>" alt="<?php echo htmlspecialchars($design['name']); ?>" loading="lazy">
                        <?php else: ?>
                            <div class="design-placeholder" aria-hidden="true">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                        <span class="design-category"><?php echo htmlspecialchars($design['category_name'] ?? 'Nail Art'); ?></span>
                    </div>
                    <div class="design-info">
                        <h3 class="design-name"><?php echo htmlspecialchars($design['name']); ?></h3>
                        <p class="design-price">₱<?php echo number_format($design['price'], 2); ?></p>
                        <a href="<?php echo base_url('design.php?id=' . $design['id']); ?>" class="btn btn-sm btn-outline">View Details</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="section-cta">
            <a href="<?php echo base_url('gallery.php'); ?>" class="btn btn-secondary">View All Designs</a>
        </div>
    </div>
</section>

<!-- Services Preview -->
<section class="services-preview" aria-labelledby="services-title">
    <div class="container">
        <header class="section-header">
            <h2 id="services-title" class="section-title">Our Services</h2>
            <p class="section-subtitle">Professional nail care tailored to you</p>
        </header>

        <div class="services-grid">
            <?php foreach ($services as $service): ?>
                <article class="service-card">
                    <div class="service-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5"/>
                            <path d="M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <h3 class="service-name"><?php echo htmlspecialchars($service['name']); ?></h3>
                    <p class="service-duration"><?php echo $service['duration_minutes']; ?> minutes</p>
                    <p class="service-price">₱<?php echo number_format($service['price'], 2); ?></p>
                    <p class="service-description"><?php echo htmlspecialchars($service['description']); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section" aria-labelledby="cta-title">
    <div class="container">
        <div class="cta-content">
            <h2 id="cta-title" class="cta-title">Ready for Your Perfect Nails?</h2>
            <p class="cta-subtitle">Book your appointment today and experience the Tips by Nadine difference.</p>
            <a href="<?php echo base_url('booking.php'); ?>" class="btn btn-primary btn-lg">Book Now</a>
        </div>
    </div>
</section>