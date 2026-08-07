<!-- Admin Designs Management -->
<div class="admin-page-header">
    <div class="header-actions">
        <h1 class="admin-page-title">Manage Nail Designs</h1>
        <button type="button" class="btn btn-primary" id="add-design-btn">Add Design</button>
    </div>
    <p class="admin-page-subtitle">Manage your nail design gallery</p>
</div>

<!-- Add/Edit Design Modal -->
<div class="modal" id="design-modal" role="dialog" aria-labelledby="modal-title" aria-modal="true" hidden>
    <div class="modal-overlay"></div>
    <div class="modal-container">
        <div class="modal-header">
            <h2 id="modal-title" class="modal-title">Add New Design</h2>
            <button type="button" class="modal-close" aria-label="Close modal">&times;</button>
        </div>
        <form method="POST" action="<?php echo base_url('admin/designs/create.php'); ?>" class="modal-form" id="design-form" enctype="multipart/form-data">
            <input type="hidden" name="design_id" id="design_id">
            <div class="form-group">
                <label for="design_name" class="form-label">Design Name *</label>
                <input type="text" name="name" id="design_name" class="form-input" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="design_category" class="form-label">Category *</label>
                    <select name="category_id" id="design_category" class="form-input" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="design_price" class="form-label">Price (₱) *</label>
                    <input type="number" name="price" id="design_price" class="form-input" step="0.01" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label for="design_description" class="form-label">Description</label>
                <textarea name="description" id="design_description" class="form-input form-textarea" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="design_image" class="form-label">Design Image</label>
                <input type="file" name="image" id="design_image" class="form-input" accept="image/jpeg,image/png,image/webp">
                <small class="form-hint">JPG, PNG, or WebP. Max 5MB.</small>
                <div class="image-preview" id="image-preview" hidden></div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary modal-cancel">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Design</button>
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
                <th scope="col">Image</th>
                <th scope="col">Name</th>
                <th scope="col">Category</th>
                <th scope="col">Price</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($designs)): ?>
                <tr>
                    <td colspan="6" class="text-center">No designs yet. Click "Add Design" to create one.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($designs as $design): ?>
                    <tr>
                        <td>
                            <?php if ($design['image_path']): ?>
                                <img src="<?php echo base_url(htmlspecialchars($design['image_path'])); ?>" alt="" class="table-thumb">
                            <?php else: ?>
                                <div class="table-thumb placeholder" aria-hidden="true">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="design-name"><?php echo htmlspecialchars($design['name']); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($design['category_name'] ?? 'Uncategorized'); ?></td>
                        <td>₱<?php echo number_format($design['price'], 2); ?></td>
                        <td>
                            <span class="status-badge <?php echo $design['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $design['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-sm btn-secondary edit-design-btn" data-design='<?php echo json_encode($design); ?>'>Edit</button>
                                <form method="POST" action="<?php echo base_url('admin/designs/toggle.php'); ?>" style="display:inline;">
                                    <input type="hidden" name="design_id" value="<?php echo $design['id']; ?>">
                                    <input type="hidden" name="is_active" value="<?php echo $design['is_active'] ? '0' : '1'; ?>">
                                    <button type="submit" class="btn btn-sm btn-ghost" aria-label="<?php echo $design['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                        <?php echo $design['is_active'] ? 'Deactivate' : 'Activate'; ?>
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