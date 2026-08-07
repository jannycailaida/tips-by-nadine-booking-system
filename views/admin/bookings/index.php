<!-- Admin Bookings List -->
<div class="admin-page-header">
    <h1 class="admin-page-title">Manage Bookings</h1>
    <p class="admin-page-subtitle">View and manage all client appointments</p>
</div>

<div class="admin-filters">
    <form method="GET" class="filter-form">
        <div class="filter-group">
            <label for="status" class="visually-hidden">Filter by Status</label>
            <select name="status" id="status" class="select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="pending" <?php echo $currentStatus === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="confirmed" <?php echo $currentStatus === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                <option value="cancelled" <?php echo $currentStatus === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                <option value="completed" <?php echo $currentStatus === 'completed' ? 'selected' : ''; ?>>Completed</option>
            </select>
        </div>
    </form>
</div>

<div class="table-container">
    <table class="admin-table" role="table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Client</th>
                <th scope="col">Contact</th>
                <th scope="col">Service</th>
                <th scope="col">Design</th>
                <th scope="col">Date</th>
                <th scope="col">Time</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bookings)): ?>
                <tr>
                    <td colspan="9" class="text-center">No bookings found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td>#<?php echo $booking['id']; ?></td>
                        <td>
                            <div class="client-info">
                                <span class="client-name"><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="client-contact">
                                <span><?php echo htmlspecialchars($booking['email']); ?></span>
                                <?php if ($booking['phone']): ?>
                                    <span><?php echo htmlspecialchars($booking['phone']); ?></span>
                                <?php endif; ?>
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
                            <div class="action-buttons">
                                <a href="<?php echo base_url('admin/booking.php?id=' . $booking['id']); ?>" class="btn btn-sm btn-secondary">View</a>
                                <form method="POST" action="<?php echo base_url('admin/booking/update-status.php'); ?>" class="status-form" style="display:inline;">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <select name="status" class="select select-sm" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        <option value="completed" <?php echo $booking['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    </select>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>