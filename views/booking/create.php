<!-- Booking Page -->
<section class="page-header auth-header">
    <div class="container">
        <h1 class="page-title">Book Appointment</h1>
        <p class="page-subtitle">Choose your service, design, and preferred time</p>
    </div>
</section>

<section class="booking-section">
    <div class="container">
        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-error" role="alert">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" class="booking-form" enctype="multipart/form-data" id="booking-form">
            <!-- Step 1: Service Selection -->
            <fieldset class="booking-step" data-step="1">
                <legend class="step-title">
                    <span class="step-number">1</span>
                    Select Service
                </legend>
                <div class="service-options">
                    <?php foreach ($services as $service): ?>
                        <label class="service-option">
                            <input type="radio" name="service_id" value="<?php echo $service['id']; ?>" required <?php echo (isset($old['service_id']) && $old['service_id'] == $service['id']) ? 'checked' : ''; ?>>
                            <div class="service-option-content">
                                <h4><?php echo htmlspecialchars($service['name']); ?></h4>
                                <p><?php echo htmlspecialchars($service['description']); ?></p>
                                <div class="service-meta">
                                    <span><?php echo $service['duration_minutes']; ?> min</span>
                                    <span>₱<?php echo number_format($service['price'], 2); ?></span>
                                </div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <!-- Step 2: Design Selection (Optional) -->
            <fieldset class="booking-step" data-step="2">
                <legend class="step-title">
                    <span class="step-number">2</span>
                    Choose Design <span class="optional-badge">Optional</span>
                </legend>
                <div class="design-selector">
                    <label class="design-option no-design">
                        <input type="radio" name="design_id" value="" <?php echo (!isset($old['design_id']) || empty($old['design_id'])) ? 'checked' : ''; ?>>
                        <div class="design-option-content">
                            <div class="design-option-placeholder" aria-hidden="true">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="15" y1="9" x2="9" y2="15"/>
                                    <line x1="9" y1="9" x2="15" y2="15"/>
                                </svg>
                            </div>
                            <h4>No Specific Design</h4>
                            <p>I'll decide at the salon</p>
                        </div>
                    </label>

                    <?php foreach ($designs as $design): ?>
                        <label class="design-option">
                            <input type="radio" name="design_id" value="<?php echo $design['id']; ?>" <?php echo (isset($old['design_id']) && $old['design_id'] == $design['id']) ? 'checked' : ''; ?>>
                            <div class="design-option-content">
                                <?php if ($design['image_path']): ?>
                                    <img src="<?php echo base_url(htmlspecialchars($design['image_path'])); ?>" alt="" loading="lazy">
                                <?php else: ?>
                                    <div class="design-option-placeholder" aria-hidden="true">
                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                                            <circle cx="8.5" cy="8.5" r="1.5"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h4><?php echo htmlspecialchars($design['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($design['category_name'] ?? 'Nail Art'); ?> • ₱<?php echo number_format($design['price'], 2); ?></p>
                                </div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <!-- Step 3: Date & Time -->
            <fieldset class="booking-step" data-step="3">
                <legend class="step-title">
                    <span class="step-number">3</span>
                    Select Date & Time
                </legend>
                <div class="form-row">
                    <div class="form-group">
                        <label for="booking_date" class="form-label">Date</label>
                        <input type="date" name="booking_date" id="booking_date" class="form-input" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($old['booking_date'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="time_slot_id" class="form-label">Time</label>
                        <select name="time_slot_id" id="time_slot_id" class="form-input" required disabled>
                            <option value="">Select a date first</option>
                        </select>
                    </div>
                </div>
            </fieldset>

            <!-- Step 4: Reference Image & AI Recommendations -->
            <fieldset class="booking-step" data-step="4">
                <legend class="step-title">
                    <span class="step-number">4</span>
                    Reference Photo <span class="optional-badge">Optional</span>
                </legend>
                <div class="upload-area" id="upload-area">
                    <input type="file" name="reference_image" id="reference_image" accept="image/jpeg,image/png,image/webp" hidden>
                    <div class="upload-placeholder" aria-hidden="true">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                    </div>
                    <p class="upload-text">Click or drag to upload a reference photo</p>
                    <p class="upload-hint">Get AI design recommendations based on your photo</p>
                    <button type="button" class="btn btn-secondary" id="upload-btn">Choose File</button>
                </div>
                <div class="upload-preview" id="upload-preview" hidden></div>

                <!-- AI Recommendations Display -->
                <div class="ai-recommendations" id="ai-recommendations" hidden>
                    <h4>AI Design Recommendations</h4>
                    <p class="ai-hint">Based on your uploaded photo, we suggest:</p>
                    <div class="ai-recommendation-cards" id="ai-recommendation-cards"></div>
                </div>
            </fieldset>

            <!-- Step 5: Notes & Submit -->
            <fieldset class="booking-step" data-step="5">
                <legend class="step-title">
                    <span class="step-number">5</span>
                    Additional Notes
                </legend>
                <div class="form-group">
                    <label for="notes" class="form-label">Special Requests or Notes</label>
                    <textarea name="notes" id="notes" class="form-input form-textarea" rows="4" placeholder="Any specific requests, allergies, or preferences..."><?php echo htmlspecialchars($old['notes'] ?? ''); ?></textarea>
                </div>
            </fieldset>

            <div class="booking-actions">
                <button type="button" class="btn btn-secondary btn-prev" id="prev-btn" hidden>Previous</button>
                <button type="submit" class="btn btn-primary btn-submit" id="submit-btn">Book Appointment</button>
            </div>
        </form>
    </div>
</section>