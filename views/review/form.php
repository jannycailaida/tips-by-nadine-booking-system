<!-- Public Review Form -->
<section class="page-header auth-header review-header">
    <div class="container">
        <p class="eyebrow">Your recent visit</p>
        <h1 class="page-title">How did your appointment go?</h1>
        <p class="page-subtitle">Your review helps future clients choose with confidence.</p>
    </div>
</section>

<section class="review-section">
    <div class="container">
        <div class="review-card">
            <div class="review-appointment-summary">
                <div>
                    <span class="review-summary-label">Appointment</span>
                    <h2><?php echo htmlspecialchars($request['service_name']); ?></h2>
                    <p>
                        <?php echo date('F j, Y', strtotime($request['booking_date'])); ?> ·
                        <?php echo date('g:i A', strtotime($request['start_time'])); ?> - <?php echo date('g:i A', strtotime($request['end_time'])); ?>
                    </p>
                    <?php if (!empty($request['design_name'])): ?>
                        <p class="review-design-note">Design: <?php echo htmlspecialchars($request['design_name']); ?></p>
                    <?php endif; ?>
                </div>
                <span class="review-pill">Private link</span>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error" role="alert">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" class="review-form">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div class="form-group">
                    <label class="form-label">Your Rating</label>
                    <div class="rating-options" role="radiogroup" aria-label="Your rating">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <label class="rating-option">
                                <input type="radio" name="rating" value="<?php echo $i; ?>" <?php echo (isset($old['rating']) && (int)$old['rating'] === $i) ? 'checked' : ''; ?> required>
                                <span><?php echo str_repeat('★', $i); ?><span class="rating-muted"><?php echo str_repeat('★', 5 - $i); ?></span></span>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="client_name" class="form-label">Display Name (Optional)</label>
                    <input type="text" name="client_name" id="client_name" class="form-input" value="<?php echo htmlspecialchars($old['client_name'] ?? trim(($request['first_name'] ?? '') . ' ' . ($request['last_name'] ?? ''))); ?>" maxlength="150">
                    <small class="form-hint">This name appears with your review if the salon publishes it.</small>
                </div>

                <div class="form-group">
                    <label for="review_text" class="form-label">What did you love or want us to improve?</label>
                    <textarea name="review_text" id="review_text" class="form-input form-textarea" rows="6" maxlength="2000" required placeholder="Share a few words about your visit..."><?php echo htmlspecialchars($old['review_text'] ?? ''); ?></textarea>
                </div>

                <div class="review-actions">
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                    <a href="<?php echo base_url(); ?>" class="btn btn-secondary">Back to Home</a>
                </div>
            </form>
        </div>
    </div>
</section>
