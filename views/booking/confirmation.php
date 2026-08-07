<!-- Booking Confirmation Page -->
<section class="page-header auth-header">
    <div class="container">
        <h1 class="page-title">Booking Confirmed!</h1>
        <p class="page-subtitle">Your appointment has been scheduled</p>
    </div>
</section>

<section class="confirmation-section">
    <div class="container">
        <div class="confirmation-card">
            <div class="confirmation-icon" aria-hidden="true">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <h2>Appointment Confirmed</h2>
            <p class="confirmation-message">We've sent a confirmation email to <strong><?php echo htmlspecialchars($booking['email']); ?></strong></p>

            <div class="booking-summary">
                <h3>Booking Details</h3>
                <dl class="summary-list">
                    <div class="summary-item">
                        <dt>Booking Reference</dt>
                        <dd>#<?php echo $booking['id']; ?></dd>
                    </div>
                    <div class="summary-item">
                        <dt>Service</dt>
                        <dd><?php echo htmlspecialchars($booking['service_name']); ?></dd>
                    </div>
                    <div class="summary-item">
                        <dt>Date</dt>
                        <dd><?php echo date('l, F j, Y', strtotime($booking['booking_date'])); ?></dd>
                    </div>
                    <div class="summary-item">
                        <dt>Time</dt>
                        <dd><?php echo date('g:i A', strtotime($booking['start_time'])); ?> - <?php echo date('g:i A', strtotime($booking['end_time'])); ?></dd>
                    </div>
                    <?php if ($booking['design_name']): ?>
                    <div class="summary-item">
                        <dt>Nail Design</dt>
                        <dd><?php echo htmlspecialchars($booking['design_name']); ?></dd>
                    </div>
                    <?php endif; ?>
                    <div class="summary-item total">
                        <dt>Total</dt>
                        <dd>₱<?php echo number_format($booking['service_price'] + ($booking['design_price'] ?? 0), 2); ?></dd>
                    </div>
                </dl>
            </div>

            <?php if (!empty($ai_recommendations)): ?>
                <div class="ai-recommendations-confirmation">
                    <h3>AI Design Recommendations</h3>
                    <p>Based on your reference photo, you might also like:</p>
                    <div class="ai-rec-grid">
                        <?php foreach ($ai_recommendations as $rec): ?>
                            <article class="ai-rec-card">
                                <?php if ($rec['image_path']): ?>
                                    <img src="<?php echo base_url(htmlspecialchars($rec['image_path'])); ?>" alt="<?php echo htmlspecialchars($rec['name']); ?>" loading="lazy">
                                <?php endif; ?>
                                <div class="ai-rec-info">
                                    <h4><?php echo htmlspecialchars($rec['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($rec['category']); ?> • ₱<?php echo number_format($rec['price'], 2); ?></p>
                                    <span class="ai-confidence"><?php echo round($rec['confidence'] * 100); ?>% match</span>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="confirmation-actions">
                <a href="<?php echo base_url('dashboard.php'); ?>" class="btn btn-primary">View My Bookings</a>
                <a href="<?php echo base_url('gallery.php'); ?>" class="btn btn-secondary">Browse More Designs</a>
            </div>

            <p class="confirmation-note">
                <strong>Need to make changes?</strong> You can reschedule or cancel up to 24 hours before your appointment from your dashboard.
            </p>
        </div>
    </div>
</section>