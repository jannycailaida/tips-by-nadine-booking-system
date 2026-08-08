<!-- Admin Leads Management -->
<div class="admin-page-header">
    <div class="header-actions">
        <h1 class="admin-page-title">Leads</h1>
        <a href="<?php echo base_url('admin/leads.php?export=csv'); ?>" class="btn btn-primary">Export CSV</a>
    </div>
    <p class="admin-page-subtitle">
        Emails captured on the landing page — your clean contact list for future salon updates.
        <?php echo $total > 0 ? '<strong>' . number_format($total) . '</strong> signup' . ($total === 1 ? '' : 's') . ' so far.' : 'No signups yet — the form lives under the home-page CTA.'; ?>
    </p>
</div>

<div class="table-container">
    <table class="admin-table" role="table">
        <thead>
            <tr>
                <th scope="col">Email</th>
                <th scope="col">Source</th>
                <th scope="col">Captured At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($leads)): ?>
                <tr>
                    <td colspan="3" class="text-center">No leads yet. Sign up on the landing page and they'll appear here.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($leads as $lead): ?>
                    <tr>
                        <td><span class="lead-email"><?php echo htmlspecialchars($lead['email']); ?></span></td>
                        <td><span class="status-badge status-confirmed"><?php echo htmlspecialchars($lead['source']); ?></span></td>
                        <td><?php echo date('M j, Y · g:i A', strtotime($lead['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>