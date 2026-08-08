<!-- Design Detail Page -->
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo base_url(); ?>">Home</a>
            <span aria-hidden="true">/</span>
            <a href="<?php echo base_url('gallery.php'); ?>">Gallery</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page"><?php echo htmlspecialchars($design['name']); ?></span>
        </nav>
        <h1 class="page-title"><?php echo htmlspecialchars($design['name']); ?></h1>
        <p class="page-subtitle"><?php echo htmlspecialchars($design['category_name'] ?? 'Nail Design'); ?> • ₱<?php echo number_format($design['price'], 2); ?></p>
    </div>
</section>

<section class="design-detail">
    <div class="container">
        <div class="design-detail-grid">
            <div class="design-detail-image reveal-slide-left">
                <?php if ($design['image_path']): ?>
                    <img src="<?php echo base_url(htmlspecialchars($design['image_path'])); ?>" alt="<?php echo htmlspecialchars($design['name']); ?>">
                <?php else: ?>
                    <img src="<?php echo base_url('assets/images/design-fallback.jpg'); ?>" alt="<?php echo htmlspecialchars($design['name']); ?>" class="design-fallback-img large" loading="lazy">
                <?php endif; ?>
            </div>

            <div class="design-detail-info reveal-slide-right">
                <div class="design-meta">
                    <span class="design-category"><?php echo htmlspecialchars($design['category_name'] ?? 'Nail Art'); ?></span>
                    <span class="design-price-large">₱<?php echo number_format($design['price'], 2); ?></span>
                </div>

                <?php if ($design['description']): ?>
                    <div class="design-description">
                        <h3>Description</h3>
                        <p><?php echo htmlspecialchars($design['description']); ?></p>
                    </div>
                <?php endif; ?>

                <div class="design-features">
                    <h3>Features</h3>
                    <ul>
                        <li>Professional application</li>
                        <li>Long-lasting finish</li>
                        <li>Customizable colors</li>
                        <li>Gel or regular polish options</li>
                    </ul>
                </div>

                <div class="design-actions">
                    <a href="<?php echo base_url('booking.php?design=' . $design['id']); ?>" class="btn btn-primary btn-lg">Book This Design</a>
                    <a href="<?php echo base_url('gallery.php'); ?>" class="btn btn-secondary btn-lg">← Back to Gallery</a>
                </div>
            </div>
        </div>
    </div>
</section>