<!-- Gallery Page -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Nail Design Gallery</h1>
        <p class="page-subtitle">Browse our collection of nail designs</p>
    </div>
</section>

<section class="gallery-section">
    <div class="container">
        <!-- Filters -->
        <div class="gallery-filters">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="category" class="visually-hidden">Filter by Category</label>
                    <select name="category" id="category" class="select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $selectedCategory == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="search" class="visually-hidden">Search Designs</label>
                    <input type="search" name="search" id="search" class="input" placeholder="Search designs..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                    <button type="submit" class="btn btn-secondary">Apply</button>
                </div>
            </form>
        </div>

        <div class="gallery-hashtag reveal-fade">
            <span class="gallery-hashtag-icon" aria-hidden="true">✦</span>
            A look you love? Book it, then tag <strong>#TipsByNadine</strong> — we feature client nails on our feed.
        </div>

        <!-- Designs Grid -->
        <div class="designs-grid gallery-grid">
            <?php if (empty($designs)): ?>
                <div class="empty-state">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                    <h3>No designs found</h3>
                    <p>Try adjusting your filters or search terms</p>
                </div>
            <?php else: ?>
                <?php foreach ($designs as $design): ?>
                    <article class="design-card reveal-stagger">
                        <div class="design-image">
                            <?php if ($design['image_path']): ?>
                                <img src="<?php echo base_url(htmlspecialchars($design['image_path'])); ?>" alt="<?php echo htmlspecialchars($design['name']); ?>" loading="lazy">
                            <?php else: ?>
                                <img src="<?php echo base_url('assets/images/design-fallback.jpg'); ?>" alt="<?php echo htmlspecialchars($design['name']); ?>" class="design-fallback-img" loading="lazy">
                            <?php endif; ?>
                            <span class="design-category"><?php echo htmlspecialchars($design['category_name'] ?? 'Nail Art'); ?></span>
                        </div>
                        <div class="design-info">
                            <h3 class="design-name"><?php echo htmlspecialchars($design['name']); ?></h3>
                            <?php if ($design['description']): ?>
                                <p class="design-description"><?php echo htmlspecialchars($design['description']); ?></p>
                            <?php endif; ?>
                            <div class="design-meta">
                                <span class="design-price">₱<?php echo number_format($design['price'], 2); ?></span>
                                <a href="<?php echo base_url('design.php?id=' . $design['id']); ?>" class="btn btn-sm btn-outline">View Details</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>