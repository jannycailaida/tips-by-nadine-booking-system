<!-- Admin Analytics (Funnel) -->
<div class="admin-page-header">
    <h1 class="admin-page-title">Analytics</h1>
    <p class="admin-page-subtitle">
        The booking funnel in numbers — from first visit to confirmed seat. Every figure below is real event data, no estimates.
    </p>
</div>

<div class="stats-grid">
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
            <span class="stat-value"><?php echo (int)$funnel['booking_started']; ?></span>
            <span class="stat-label">Booking Started</span>
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
            <span class="stat-value"><?php echo (int)$funnel['booking_completed']; ?></span>
            <span class="stat-label">Booking Completed</span>
        </div>
    </article>

    <article class="stat-card">
        <div class="stat-icon success" aria-hidden="true">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 12l2 2 4-4"/>
                <circle cx="12" cy="12" r="10"/>
            </svg>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?php echo (int)$funnel['booking_confirmed']; ?></span>
            <span class="stat-label">Confirmed</span>
        </div>
    </article>

    <article class="stat-card">
        <div class="stat-icon warning" aria-hidden="true">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?php echo (int)$funnel['booking_cancelled']; ?></span>
            <span class="stat-label">Cancelled</span>
        </div>
    </article>

    <article class="stat-card">
        <div class="stat-icon" aria-hidden="true">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
            </svg>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?php echo (int)$funnel['lead_captured']; ?></span>
            <span class="stat-label">Leads Captured</span>
        </div>
    </article>
</div>

<!-- Conversion insight + 14-day activity -->
<div class="admin-detail-grid">
    <section class="admin-section detail-main">
        <div class="section-header">
            <h2 class="section-title">Start → Booking Conversion</h2>
        </div>
        <div class="detail-card">
            <p class="detail-card-subtitle">
                Of everyone who reached the booking form, <strong><?php echo $funnel['completion_rate']; ?>%</strong> completed a booking.
                Watch this number — it's your clearest signal for small improvements.
            </p>
            <div class="funnel-bar-wrap">
                <div class="funnel-bar" style="transform: scaleX(<?php echo min(1, (int)$funnel['completion_rate'] / 100); ?>);"></div>
            </div>
            <p class="funnel-hint">
                <?php if ($funnel['booking_started'] < 5): ?>
                    Not enough data yet — complete a booking or two and this starts telling its story within a week.
                <?php elseif ($funnel['completion_rate'] >= 50): ?>
                    Healthy conversion — visitors who reach the form are booking. Double down on what's driving them there.
                <?php else: ?>
                    A lot of visitors start but don't finish. Try placing the confirmation details earlier or fewer steps.
                <?php endif; ?>
            </p>
        </div>
    </section>

    <section class="admin-section detail-sidebar">
        <div class="section-header">
            <h2 class="section-title">Last 14 Days</h2>
        </div>
        <div class="detail-card">
            <ul class="funnel-daily">
                <?php foreach ($daily as $row): ?>
                    <li class="<?php echo $row['is_today'] ? 'is-today' : ''; ?>">
                        <span class="funnel-daily-label"><?php echo $row['is_today'] ? 'Today' : $row['label']; ?></span>
                        <span class="funnel-daily-track" aria-hidden="true">
                            <span class="funnel-daily-fill" style="transform: scaleX(<?php echo min(1, $row['count'] * 0.2); ?>);"></span>
                        </span>
                        <span class="funnel-daily-count"><?php echo $row['count']; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
</div>

<!-- Recent activity -->
<section class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Recent Activity</h2>
    </div>
    <div class="table-container">
        <table class="admin-table" role="table">
            <thead>
                <tr>
                    <th scope="col">Event</th>
                    <th scope="col">Client</th>
                    <th scope="col">Booking</th>
                    <th scope="col">When</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentEvents)): ?>
                    <tr>
                        <td colspan="4" class="text-center">No activity yet — events log automatically as visitors book.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentEvents as $ev): ?>
                        <tr>
                            <td><span class="status-badge event-<?php echo htmlspecialchars($ev['event']); ?>"><?php echo htmlspecialchars(str_replace('_', ' ', $ev['event'])); ?></span></td>
                            <td>
                                <?php if ($ev['first_name']): ?>
                                    <span class="client-name"><?php echo htmlspecialchars(trim($ev['first_name'] . ' ' . ($ev['last_name'] ?? ''))); ?></span>
                                    <span class="client-email"><?php echo htmlspecialchars($ev['email'] ?? ''); ?></span>
                                <?php else: ?>
                                    <span class="client-email">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $ev['booking_id'] ? '#' . $ev['booking_id'] : '—'; ?></td>
                            <td><?php echo date('M j, Y · g:i A', strtotime($ev['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>