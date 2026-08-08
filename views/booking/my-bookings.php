<!-- My Bookings Page -->
<section class="page-header auth-header">
    <div class="container">
        <h1 class="page-title">My Bookings</h1>
        <p class="page-subtitle">View and manage your appointments</p>
    </div>
</section>

<section class="bookings-section">
    <div class="container">
        <?php if (!empty($referralLink)): ?>
            <div class="referral-panel">
                <div>
                    <p class="eyebrow">Share your glow</p>
                    <h2>Invite a friend to Tips by Nadine</h2>
                    <p>Send your personal booking link. Referral credits are reviewed manually by the salon team.</p>
                </div>
                <div class="referral-link-wrap">
                    <input type="text" class="form-input referral-link-input" value="<?php echo htmlspecialchars($referralLink); ?>" readonly aria-label="Your referral link">
                    <a href="mailto:?subject=Book%20with%20Tips%20by%20Nadine&body=<?php echo rawurlencode('Book your nail appointment with Tips by Nadine: ' . $referralLink); ?>" class="btn btn-secondary">Share</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($bookings)): ?>
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <h2 class="empty-state-title">No bookings yet</h2>
                <p>Start by booking your first appointment</p>
                <a href="<?php echo base_url('booking.php'); ?>" class="btn btn-primary">Book Now</a>
            </div>
        <?php else: ?>
            <?php $today = date('Y-m-d'); ?>

            <!-- Status filter tabs -->
            <div class="bookings-filter" role="tablist" aria-label="Filter bookings">
                <button type="button" class="filter-tab is-active" role="tab" aria-selected="true" data-filter="all">All</button>
                <button type="button" class="filter-tab" role="tab" aria-selected="false" data-filter="upcoming">Upcoming</button>
                <button type="button" class="filter-tab" role="tab" aria-selected="false" data-filter="past">Past</button>
                <button type="button" class="filter-tab" role="tab" aria-selected="false" data-filter="cancelled">Cancelled</button>
            </div>

            <div class="bookings-list">
                <?php foreach ($bookings as $booking):
                    $canCancel = in_array($booking['status'], ['pending', 'confirmed']) && $booking['booking_date'] >= $today;
                    $canRebook = $booking['status'] !== 'cancelled' && ($booking['status'] === 'completed' || $booking['booking_date'] < $today);
                    $dataState = $booking['status'] === 'cancelled' ? 'cancelled' : ($canCancel ? 'upcoming' : 'past');
                    $whenLabel = date('D, M j', strtotime($booking['booking_date'])) . ' · ' . date('g:i A', strtotime($booking['start_time']));
                ?>
                    <article class="booking-card" data-state="<?php echo $dataState; ?>" <?php echo $booking['status'] === 'cancelled' ? 'data-cancelled' : ''; ?>>
                        <div class="booking-header">
                            <div class="booking-status status-<?php echo $booking['status']; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </div>
                            <div class="booking-ref">#<?php echo $booking['id']; ?></div>
                        </div>

                        <div class="booking-content">
                            <div class="booking-main">
                                <?php if ($booking['design_image']): ?>
                                    <div class="booking-thumb" aria-hidden="true">
                                        <img src="<?php echo base_url(htmlspecialchars($booking['design_image'])); ?>" alt="" loading="lazy">
                                    </div>
                                <?php endif; ?>
                                <div class="booking-headline">
                                    <h2 class="booking-service"><?php echo htmlspecialchars($booking['service_name']); ?></h2>
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
                            <div class="booking-price">
                                <span class="booking-total">₱<?php echo number_format($booking['service_price'] + ($booking['design_price'] ?? 0), 2); ?></span>
                                <span class="booking-breakdown">
                                    <?php echo htmlspecialchars($booking['service_name']); ?> ₱<?php echo number_format($booking['service_price'], 2); ?>
                                    <?php if ($booking['design_price']): ?>+ design ₱<?php echo number_format($booking['design_price'], 2); ?><?php endif; ?>
                                </span>
                            </div>
                            <div class="booking-actions">
                                <?php if ($canCancel): ?>
                                    <button type="button" class="btn btn-sm btn-cancel" data-booking-id="<?php echo $booking['id']; ?>" data-when="<?php echo htmlspecialchars($whenLabel); ?>">Cancel</button>
                                <?php endif; ?>
                                <?php if ($canRebook): ?>
                                    <a href="<?php echo base_url('booking.php?rebook=' . $booking['id']); ?>" class="btn btn-sm btn-primary">Book Again</a>
                                <?php endif; ?>
                                <a href="<?php echo base_url('booking/confirmation.php?id=' . $booking['id']); ?>" class="btn btn-sm btn-secondary">Details</a>
                            </div>
                        </div>

                        <?php if ($canCancel): ?>
                        <div class="cancel-confirm" hidden>
                            <p class="cancel-confirm-title">Cancel this appointment?</p>
                            <p class="cancel-confirm-booking" data-confirm-target></p>
                            <p class="cancel-confirm-note">We'll open the slot for someone else — and we'd love to see you again soon.</p>
                            <div class="cancel-confirm-actions">
                                <button type="button" class="btn btn-sm btn-secondary cancel-keep">Keep Booking</button>
                                <button type="button" class="btn btn-sm btn-danger confirm-cancel" data-booking-id="<?php echo $booking['id']; ?>">Yes, Cancel</button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>

            <p class="bookings-filter-empty" hidden>No bookings match this view.</p>
        <?php endif; ?>
    </div>
</section>
