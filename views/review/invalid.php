<!-- Review Link Unavailable -->
<section class="page-header auth-header review-header">
    <div class="container">
        <p class="eyebrow">Review link</p>
        <h1 class="page-title"><?php echo htmlspecialchars($title); ?></h1>
        <p class="page-subtitle">We could not open this review request.</p>
    </div>
</section>

<section class="review-section">
    <div class="container">
        <div class="review-card review-state-card">
            <div class="review-state-icon" aria-hidden="true">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <h2><?php echo htmlspecialchars($title); ?></h2>
            <p><?php echo htmlspecialchars($message); ?></p>
            <div class="review-actions centered">
                <a href="<?php echo base_url(); ?>" class="btn btn-primary">Back to Home</a>
                <a href="<?php echo base_url('booking.php'); ?>" class="btn btn-secondary">Book Again</a>
            </div>
        </div>
    </div>
</section>
