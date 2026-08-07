<!-- Admin Dashboard -->
<div class="admin-page-header">
    <h1 class="admin-page-title">Dashboard</h1>
    <p class="admin-page-subtitle">Overview of your salon's bookings and designs</p>
</div>

<div class="stats-grid">
    <article class="stat-card">
        <div class="stat-icon" aria-hidden="true">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?php echo $stats['total_bookings']; ?></span>
            <span class="stat-label">Total Bookings</span>
        </div>
    </article>

    <article class="stat-card">
        <div class="stat-icon warning" aria-hidden="true">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?php echo $stats['pending_bookings']; ?></span>
            <span class="stat-label">Pending</span>
        </div>
    </article>

    <article class="stat-card">
        <div class="stat-icon success" aria-hidden="true">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?php echo $stats['confirmed_bookings']; ?></span>
            <span class="stat-label">Confirmed</span>
        </div>
    </article>

    <article class="stat-card">
        <div class="stat-icon" aria-hidden="true">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?php echo $stats['total_users']; ?></span>
            <span class="stat-label">Registered Clients</span>
        </div>
    </article>

    <article class="stat-card">
        <div class="stat-icon" aria-hidden="true">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
            </svg>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?php echo $stats['total_designs']; ?></span>
            <span class="stat-label">Active Designs</span>
        </div>
    </article>
</div>

<!-- Recent Bookings -->
<section class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Recent Bookings</h2>
        <a href="<?php echo base_url('admin/bookings.php'); ?>" class="btn btn-secondary btn-sm">View All</a>
    </div>

    <div class="table-container">
        <table class="admin-table" role="table">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Client</th>
                    <th scope="col">Service</th>
                    <th scope="col">Design</th>
                    <th scope="col">Date</th>
                    <th scope="col">Time</th>
                    <th scope="col">Status</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentBookings as $booking): ?>
                    <tr>
                        <td>#<?php echo $booking['id']; ?></td>
                        <td>
                            <div class="client-info">
                                <span class="client-name"><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></span>
                                <span class="client-email"><?php echo htmlspecialchars($booking['email']); ?></span>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['design_name'] ?? '—'); ?></td>
                        <td><?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></td>
                        <td><?php echo date('g:i A', strtotime($booking['start_time'])); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $booking['status']; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo base_url('admin/booking.php?id=' . $booking['id']); ?>" class="btn btn-sm btn-secondary">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>