<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin - Tips by Nadine'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700&family=Geist+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('css/app.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('css/components.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('css/admin.css'); ?>">
</head>
<body data-base-url="<?php echo base_url(); ?>">
    <!-- Admin Header -->
    <header class="admin-header" role="banner">
        <div class="container">
            <div class="admin-header-inner">
                <a href="<?php echo base_url('admin/dashboard.php'); ?>" class="admin-brand" aria-label="Admin Dashboard">
                    <svg width="28" height="28" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                        <circle cx="16" cy="16" r="14" stroke="currentColor" stroke-width="2"/>
                        <path d="M16 8C11.5817 8 8 11.5817 8 16C8 20.4183 11.5817 24 16 24C20.4183 24 24 20.4183 24 16C24 11.5817 20.4183 8 16 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 12V16L20 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Tips by Nadine <span class="admin-badge">Admin</span></span>
                </a>

                <nav class="admin-nav" aria-label="Admin navigation">
                    <a href="<?php echo base_url('admin/dashboard.php'); ?>" class="admin-nav-link <?php echo (basename($_SERVER['PHP_SELF']) === 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
                    <a href="<?php echo base_url('admin/bookings.php'); ?>" class="admin-nav-link <?php echo (basename($_SERVER['PHP_SELF']) === 'bookings.php') ? 'active' : ''; ?>">Bookings</a>
                    <a href="<?php echo base_url('admin/designs.php'); ?>" class="admin-nav-link <?php echo (basename($_SERVER['PHP_SELF']) === 'designs.php') ? 'active' : ''; ?>">Designs</a>
                </nav>

                <div class="admin-user">
                    <span class="admin-user-name"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></span>
                    <a href="<?php echo base_url('admin/logout.php'); ?>" class="btn btn-ghost btn-sm">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Admin Sidebar (Mobile) -->
    <aside class="admin-sidebar" id="admin-sidebar" aria-label="Admin sidebar">
        <nav class="admin-sidebar-nav">
            <a href="<?php echo base_url('admin/dashboard.php'); ?>" class="admin-sidebar-link <?php echo (basename($_SERVER['PHP_SELF']) === 'dashboard.php') ? 'active' : ''; ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" rx="1"/>
                </svg>
                Dashboard
            </a>
            <a href="<?php echo base_url('admin/bookings.php'); ?>" class="admin-sidebar-link <?php echo (basename($_SERVER['PHP_SELF']) === 'bookings.php') ? 'active' : ''; ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                Bookings
            </a>
            <a href="<?php echo base_url('admin/designs.php'); ?>" class="admin-sidebar-link <?php echo (basename($_SERVER['PHP_SELF']) === 'designs.php') ? 'active' : ''; ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                </svg>
                Designs
            </a>
        </nav>
    </aside>

    <button class="admin-sidebar-toggle" id="admin-sidebar-toggle" aria-expanded="false" aria-controls="admin-sidebar" aria-label="Toggle sidebar">
        <span class="hamburger"></span>
    </button>

    <div class="admin-overlay" id="admin-overlay" aria-hidden="true"></div>

    <!-- Main Content -->
    <main class="admin-main" id="admin-main">
        <?php if (isset($flash)): ?>
            <div class="flash flash-<?php echo $flash['type']; ?>" role="alert">
                <div class="container">
                    <?php echo $flash['message']; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="container">
            <?php echo $content ?? ''; ?>
        </div>
    </main>

    <script src="<?php echo base_url('js/app.js'); ?>"></script>
    <script src="<?php echo base_url('js/admin.js'); ?>"></script>
    <?php if (isset($extraScripts)): ?>
        <?php foreach ($extraScripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>