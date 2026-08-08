<!-- Admin Reviews Management -->
<div class="admin-page-header">
    <div class="header-actions">
        <h1 class="admin-page-title">Manage Reviews</h1>
        <button type="button" class="btn btn-primary" id="add-review-btn">Add Review</button>
    </div>
    <p class="admin-page-subtitle">
        Client testimonials shown as social proof on the landing page.
        <?php if ($reviewCount): ?>
            <strong class="review-rating-summary"><?php echo $avgRating; ?>/5</strong> from <?php echo $reviewCount; ?> active review<?php echo $reviewCount === 1 ? '' : 's'; ?>.
        <?php else: ?>
            Nothing published yet — add the first one and it appears on the homepage.
        <?php endif; ?>
    </p>
</div>

<!-- Add/Edit Review Modal -->
<div class="modal" id="review-modal" role="dialog" aria-labelledby="review-modal-title" aria-modal="true" hidden>
    <div class="modal-overlay"></div>
    <div class="modal-container">
        <div class="modal-header">
            <h2 id="review-modal-title" class="modal-title">Add Review</h2>
            <button type="button" class="modal-close" aria-label="Close modal">&times;</button>
        </div>
        <form method="POST" action="<?php echo base_url('admin/reviews/create.php'); ?>" class="modal-form" id="review-form">
            <input type="hidden" name="review_id" id="review_id">
            <div class="form-row">
                <div class="form-group">
                    <label for="review_client_name" class="form-label">Client Name *</label>
                    <input type="text" name="client_name" id="review_client_name" class="form-input" required placeholder="e.g. Maria Santos">
                </div>
                <div class="form-group">
                    <label for="review_rating" class="form-label">Rating *</label>
                    <select name="rating" id="review_rating" class="form-input" required>
                        <option value="">Select stars</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?> ★</option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="review_text" class="form-label">Review *</label>
                <textarea name="review_text" id="review_text" class="form-input form-textarea" rows="4" required placeholder="What did the client love about their visit?"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="review_service" class="form-label">Service (optional)</label>
                    <input type="text" name="service_name" id="review_service" class="form-input" placeholder="e.g. Gel Manicure">
                </div>
                <div class="form-group">
                    <label for="review_design" class="form-label">Design (optional)</label>
                    <select name="design_id" id="review_design" class="form-input">
                        <option value="">General</option>
                        <?php foreach ($designs as $design): ?>
                            <option value="<?php echo $design['id']; ?>"><?php echo htmlspecialchars($design['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span>Publish on the website</span>
                </label>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary modal-cancel">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Review</button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($errors) && !empty($errors)): ?>
    <div class="alert alert-error" role="alert">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="table-container">
    <table class="admin-table" role="table">
        <thead>
            <tr>
                <th scope="col">Client</th>
                <th scope="col">Rating</th>
                <th scope="col">Review</th>
                <th scope="col">Service</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reviews)): ?>
                <tr>
                    <td colspan="6" class="text-center">No reviews yet. Click "Add Review" to publish the first one.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td>
                            <span class="design-name"><?php echo htmlspecialchars($review['client_name']); ?></span>
                            <?php if ($review['design_id']): ?>
                                <div class="review-design-tag"><?php echo htmlspecialchars($review['design_name'] ?? ''); ?></div>
                            <?php endif; ?>
                        </td>
                        <td><span class="review-stars" aria-label="<?php echo (int)$review['rating']; ?> out of 5 stars"><?php echo str_repeat('★', (int)$review['rating']); ?><span class="review-stars-muted"><?php echo str_repeat('★', 5 - (int)$review['rating']); ?></span></span></td>
                        <td class="review-text-cell"><?php echo htmlspecialchars($review['review_text']); ?></td>
                        <td><?php echo htmlspecialchars($review['service_name'] ?? '—'); ?></td>
                        <td>
                            <span class="status-badge <?php echo $review['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $review['is_active'] ? 'Published' : 'Hidden'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-sm btn-secondary edit-review-btn" data-review='<?php echo htmlspecialchars(json_encode($review)); ?>'>Edit</button>
                                <form method="POST" action="<?php echo base_url('admin/reviews/toggle.php'); ?>" style="display:inline;">
                                    <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                    <input type="hidden" name="is_active" value="<?php echo $review['is_active'] ? '0' : '1'; ?>">
                                    <button type="submit" class="btn btn-sm btn-ghost" aria-label="<?php echo $review['is_active'] ? 'Hide' : 'Publish'; ?>">
                                        <?php echo $review['is_active'] ? 'Hide' : 'Publish'; ?>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>