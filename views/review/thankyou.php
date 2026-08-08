<!-- Review Thank You -->
<section class="page-header auth-header review-header">
    <div class="container">
        <p class="eyebrow">Review received</p>
        <h1 class="page-title">Thank you for sharing your glow.</h1>
        <p class="page-subtitle">Your feedback helps Tips by Nadine keep every appointment personal.</p>
    </div>
</section>

<section class="review-section">
    <div class="container">
        <div class="review-card review-state-card">
            <div class="review-state-icon success" aria-hidden="true">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <h2>We appreciate your review.</h2>
            <p>
                Thanks for reviewing your <?php echo htmlspecialchars($request['service_name']); ?> appointment.
                The salon team can now use your feedback for social proof and service improvements.
            </p>
            <div class="review-actions centered">
                <a href="<?php echo base_url(); ?>" class="btn btn-primary">Back to Home</a>
                <a href="<?php echo base_url('booking.php'); ?>" class="btn btn-secondary">Book Again</a>
            </div>
        </div>
    </div>
</section>
