<!-- My Bookings Page -->
<section class="page-header auth-header">
    <div class="container">
        <h1 class="page-title">My Bookings</h1>
        <p class="page-subtitle">View and manage your appointments</p>
    </div>
</section>

<section class="bookings-section">
    <div class="container">
        <?php if (empty($bookings)): ?>
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <h3>No bookings yet</h3>
                <p>Start by booking your first appointment</p>
                <a href="<?php echo base_url('booking.php'); ?>" class="btn btn-primary">Book Now</a>
            </div>
        <?php else: ?>
            <div class="bookings-list">
                <?php foreach ($bookings as $booking): ?>
                    <article class="booking-card">
                        <div class="booking-header">
                            <div class="booking-status status-<?php echo $booking['status']; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </div>
                            <div class="booking-ref">#<?php echo $booking['id']; ?></div>
                        </div>

                        <div class="booking-content">
                            <div class="booking-main">
                                <h3 class="booking-service"><?php echo htmlspecialchars($booking['service_name']); ?></h3>
                                <?php if ($booking['design_name']): ?>
                                    <p class="booking-design">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                            <circle cx="8.5" cy="8.5" r="1.5"/>
                                        </svg>
                                        <?php echo htmlspecialchars($booking['design_name']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div class="booking-meta">
                                <div class="meta-item">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    <span><?php echo date('l, F j, Y', strtotime($booking['booking_date'])); ?></span>
                                </div>
                                <div class="meta-item">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <span><?php echo date('g:i A', strtotime($booking['start_time'])); ?> - <?php echo date('g:i A', strtotime($booking['end_time'])); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="booking-footer">
                            <div class="booking-price">₱<?php echo number_format($booking['service_price'] + ($booking['design_price'] ?? 0), 2); ?></div>
                            <div class="booking-actions">
                                <?php if (in_array($booking['status'], ['pending', 'confirmed'])): ?>
                                    <button type="button" class="btn btn-sm btn-outline btn-cancel" data-booking-id="<?php echo $booking['id']; ?>">Cancel</button>
                                <?php endif; ?>
                                <a href="<?php echo base_url('booking/confirmation.php?id=' . $booking['id']); ?>" class="btn btn-sm btn-secondary">Details</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>