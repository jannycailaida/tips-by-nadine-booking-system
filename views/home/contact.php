<!-- Contact Page -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Contact Us</h1>
        <p class="page-subtitle">We'd love to hear from you</p>
    </div>
</section>

<section class="contact-section">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-info reveal-slide-right">
                <h2>Get in Touch</h2>
                <p>Have questions about our services or need assistance with a booking? Contact us and we'll respond as soon as possible.</p>

                <div class="contact-items">
                    <div class="contact-item">
                        <div class="contact-icon" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div>
                            <h3>Address</h3>
                            <p>123 Beauty Street<br>Manila, Philippines</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </div>
                        <div>
                            <h3>Phone</h3>
                            <p><a href="tel:+6321234567">+63 2 123 4567</a></p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </div>
                        <div>
                            <h3>Email</h3>
                            <p><a href="mailto:info@tipsbynadine.com">info@tipsbynadine.com</a></p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div>
                            <h3>Business Hours</h3>
                            <p>Monday - Saturday<br>9:00 AM - 6:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-form-card reveal-slide-left">
                <h2>Send a Message</h2>

                <?php if (isset($errors) && !empty($errors)): ?>
                    <div class="alert alert-error" role="alert">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo base_url('contact.php'); ?>" class="auth-form" id="contact-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" name="name" id="name" class="form-input" required autocomplete="name" value="<?php echo htmlspecialchars($old['name'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Your Email</label>
                            <input type="email" name="email" id="email" class="form-input" required autocomplete="email" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" name="subject" id="subject" class="form-input" required autocomplete="off" value="<?php echo htmlspecialchars($old['subject'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="message" class="form-label">Message</label>
                        <textarea name="message" id="message" class="form-input form-textarea" rows="5" required><?php echo htmlspecialchars($old['message'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>