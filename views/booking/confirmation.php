<!-- Booking Confirmation Page -->
<?php
    $status = $booking['status'] ?? 'confirmed';
    $statusMeta = [
        'cancelled' => ['title' => 'Appointment Cancelled', 'sub' => 'This booking is no longer active', 'label' => 'Cancelled'],
        'completed' => ['title' => 'Appointment Completed', 'sub' => 'Thanks for letting us pamper you', 'label' => 'Completed'],
        'confirmed' => ['title' => 'Booking Confirmed!', 'sub' => 'Your appointment has been scheduled', 'label' => 'Confirmed'],
        'pending'   => ['title' => 'Booking Received', 'sub' => 'We\'re confirming your appointment', 'label' => 'Pending'],
    ];
    $meta = $statusMeta[$status] ?? $statusMeta['confirmed'];
?>

<section class="page-header auth-header">
    <div class="container">
        <h1 class="page-title"><?php echo $meta['title']; ?></h1>
        <p class="page-subtitle"><?php echo $meta['sub']; ?></p>
    </div>
</section>

<section class="confirmation-section">
    <div class="container">
        <div class="confirmation-card status-<?php echo $status; ?>">
            <div class="confirmation-icon <?php echo $status === 'cancelled' ? 'icon-muted' : ''; ?>" aria-hidden="true">
                <?php if ($status === 'cancelled'): ?>
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                <?php else: ?>
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                <?php endif; ?>
            </div>
            <h2><?php echo $meta['title']; ?></h2>
            <?php if ($status === 'cancelled'): ?>
                <p class="confirmation-message">This appointment for <strong><?php echo htmlspecialchars($booking['service_name']); ?></strong> has been cancelled. We hope to see you again soon.</p>
            <?php elseif ($status === 'pending'): ?>
                <p class="confirmation-message">We've received your booking request and will confirm your slot shortly.</p>
            <?php else: ?>
                <p class="confirmation-message"><?php echo $status === 'completed' ? 'We hope you loved your visit.' : "We've sent a confirmation email to <strong>" . htmlspecialchars($booking['email']) . "</strong>."; ?></p>
            <?php endif; ?>

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
                <?php if ($status === 'cancelled'): ?>
                    <a href="<?php echo base_url('booking.php'); ?>" class="btn btn-secondary">Book a New Appointment</a>
                <?php else: ?>
                    <a href="<?php echo base_url('gallery.php'); ?>" class="btn btn-secondary">Browse More Designs</a>
                <?php endif; ?>
            </div>

            <?php if (in_array($status, ['confirmed', 'pending'])): ?>
                <p class="confirmation-note">
                    <strong>Need to make changes?</strong> You can cancel up to 24 hours before your appointment from your dashboard.
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>