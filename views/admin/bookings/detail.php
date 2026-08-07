<!-- Admin Booking Detail -->
<div class="admin-page-header">
    <h1 class="admin-page-title">Booking #<?php echo $booking['id']; ?></h1>
    <p class="admin-page-subtitle">View and manage booking details</p>
</div>

<div class="admin-detail-grid">
    <div class="detail-main">
        <section class="detail-card">
            <h2 class="detail-card-title">Client Information</h2>
            <dl class="detail-list">
                <div class="detail-item">
                    <dt>Name</dt>
                    <dd><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Email</dt>
                    <dd><?php echo htmlspecialchars($booking['email']); ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Phone</dt>
                    <dd><?php echo htmlspecialchars($booking['phone'] ?? '—'); ?></dd>
                </div>
            </dl>
        </section>

        <section class="detail-card">
            <h2 class="detail-card-title">Appointment Details</h2>
            <dl class="detail-list">
                <div class="detail-item">
                    <dt>Service</dt>
                    <dd><?php echo htmlspecialchars($booking['service_name']); ?> (<?php echo $booking['duration_minutes']; ?> min)</dd>
                </div>
                <div class="detail-item">
                    <dt>Nail Design</dt>
                    <dd><?php echo htmlspecialchars($booking['design_name'] ?? 'No specific design'); ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Date</dt>
                    <dd><?php echo date('l, F j, Y', strtotime($booking['booking_date'])); ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Time</dt>
                    <dd><?php echo date('g:i A', strtotime($booking['start_time'])); ?> - <?php echo date('g:i A', strtotime($booking['end_time'])); ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Status</dt>
                    <dd>
                        <span class="status-badge status-<?php echo $booking['status']; ?>">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                    </dd>
                </div>
                <div class="detail-item">
                    <dt>Booked On</dt>
                    <dd><?php echo date('M j, Y g:i A', strtotime($booking['created_at'])); ?></dd>
                </div>
            </dl>
        </section>

        <?php if ($booking['notes']): ?>
        <section class="detail-card">
            <h2 class="detail-card-title">Client Notes</h2>
            <p><?php echo nl2br(htmlspecialchars($booking['notes'])); ?></p>
        </section>
        <?php endif; ?>

        <?php if ($booking['reference_image_path']): ?>
        <section class="detail-card">
            <h2 class="detail-card-title">Reference Image</h2>
            <img src="<?php echo base_url(htmlspecialchars($booking['reference_image_path'])); ?>" alt="Client reference image" class="reference-image">
        </section>
        <?php endif; ?>
    </div>

    <div class="detail-sidebar">
        <section class="detail-card">
            <h2 class="detail-card-title">Actions</h2>
            <form method="POST" action="<?php echo base_url('admin/booking/update-status.php'); ?>" class="status-update-form">
                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                <div class="form-group">
                    <label for="status" class="form-label">Update Status</label>
                    <select name="status" id="status" class="form-input">
                        <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        <option value="completed" <?php echo $booking['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Update Status</button>
            </form>

            <div class="action-divider"></div>

            <a href="<?php echo base_url('admin/bookings.php'); ?>" class="btn btn-secondary btn-block">← Back to Bookings</a>
        </section>

        <?php if (!empty($ai_recommendations)): ?>
        <section class="detail-card">
            <h2 class="detail-card-title">AI Recommendations</h2>
            <p class="detail-card-subtitle">Generated from client's reference photo</p>
            <div class="ai-rec-list">
                <?php foreach ($ai_recommendations as $rec): ?>
                    <article class="ai-rec-item">
                        <?php if ($rec['image_path']): ?>
                            <img src="<?php echo base_url(htmlspecialchars($rec['image_path'])); ?>" alt="" class="ai-rec-thumb">
                        <?php endif; ?>
                        <div class="ai-rec-info">
                            <h4><?php echo htmlspecialchars($rec['name']); ?></h4>
                            <p><?php echo htmlspecialchars($rec['category']); ?> • ₱<?php echo number_format($rec['price'], 2); ?></p>
                            <span class="ai-confidence"><?php echo round($rec['confidence'] * 100); ?>% match</span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <section class="detail-card">
            <h2 class="detail-card-title">Pricing</h2>
            <dl class="detail-list pricing-list">
                <div class="detail-item">
                    <dt>Service</dt>
                    <dd>₱<?php echo number_format($booking['service_price'], 2); ?></dd>
                </div>
                <?php if ($booking['design_price']): ?>
                <div class="detail-item">
                    <dt>Design</dt>
                    <dd>₱<?php echo number_format($booking['design_price'], 2); ?></dd>
                </div>
                <?php endif; ?>
                <div class="detail-item total">
                    <dt>Total</dt>
                    <dd>₱<?php echo number_format($booking['service_price'] + ($booking['design_price'] ?? 0), 2); ?></dd>
                </div>
            </dl>
        </section>
    </div>
</div>