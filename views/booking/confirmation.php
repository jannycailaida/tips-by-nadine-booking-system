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
    $canRebook = $status !== 'cancelled' && ($status === 'completed' || ($booking['booking_date'] ?? '') < date('Y-m-d'));
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

            <?php if (in_array($status, ['confirmed', 'pending'])): ?>
                <?php $hoursLine = 'Mon–Sat 9:00 AM – 7:00 PM · Sun 10:00 AM – 5:00 PM'; ?>
                <div class="confirmation-extras">
                    <h3>Save It · Find Us · Share It</h3>
                    <div class="extras-row">
                        <a href="<?php echo htmlspecialchars($calendarUrl); ?>" target="_blank" rel="noopener" class="extras-chip">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            Add to Calendar
                        </a>
                        <a href="<?php echo htmlspecialchars($icsUrl); ?>" class="extras-chip">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            Download .ics
                        </a>
                        <a href="<?php echo htmlspecialchars($mapsUrl); ?>" target="_blank" rel="noopener" class="extras-chip">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            Get Directions
                        </a>
                    </div>
                    <p class="extras-location">
                        <strong><?php echo htmlspecialchars($locationLabel); ?></strong>
                        <span>· <?php echo $hoursLine; ?></span>
                    </p>
                </div>
            <?php endif; ?>

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
                <?php elseif ($canRebook): ?>
                    <a href="<?php echo base_url('booking.php?rebook=' . $booking['id']); ?>" class="btn btn-secondary">Book Again</a>
                    <a href="<?php echo base_url('gallery.php'); ?>" class="btn btn-ghost">Browse More Designs</a>
                <?php else: ?>
                    <a href="<?php echo base_url('gallery.php'); ?>" class="btn btn-secondary">Browse More Designs</a>
                <?php endif; ?>
            </div>

            <?php if (in_array($status, ['confirmed', 'pending'])): ?>
                <p class="confirmation-note">
                    <strong>Need to make changes?</strong> You can cancel up to 24 hours before your appointment from your dashboard.
                </p>
            <?php endif; ?>

            <?php if ($status !== 'cancelled'): ?>
                <div class="confirmation-share">
                    <p class="confirmation-share-text">
                        Love seeing yourself styled? Tag <strong>#TipsByNadine</strong> and we'll feature your nails —
                        and a friend deserves the same glow. Bring someone along next time; two chairs, double the relaxing.
                    </p>
                    <?php if (!empty($referralLink)): ?>
                        <div class="referral-mini">
                            <span>Your referral link</span>
                            <input type="text" class="form-input referral-link-input" value="<?php echo htmlspecialchars($referralLink); ?>" readonly aria-label="Your referral link">
                            <a href="mailto:?subject=Book%20with%20Tips%20by%20Nadine&body=<?php echo rawurlencode('Book your nail appointment with Tips by Nadine: ' . $referralLink); ?>" class="btn btn-sm btn-secondary">Share</a>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty(array_filter($social))): ?>
                        <div class="confirmation-social" aria-label="Follow Tips by Nadine">
                            <?php if (!empty($social['instagram'])): ?>
                                <a href="<?php echo htmlspecialchars($social['instagram']); ?>" class="footer-social-link" rel="noopener nofollow" target="_blank" aria-label="Tips by Nadine on Instagram">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($social['facebook'])): ?>
                                <a href="<?php echo htmlspecialchars($social['facebook']); ?>" class="footer-social-link" rel="noopener nofollow" target="_blank" aria-label="Tips by Nadine on Facebook">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($social['tiktok'])): ?>
                                <a href="<?php echo htmlspecialchars($social['tiktok']); ?>" class="footer-social-link" rel="noopener nofollow" target="_blank" aria-label="Tips by Nadine on TikTok">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>