<?php
$growth = array_merge([
    'review_requested' => 0,
    'review_opened' => 0,
    'review_submitted' => 0,
    'email_queued' => 0,
    'email_sent' => 0,
    'email_failed' => 0,
    'referral_converted' => 0,
    'rebook_started' => 0,
    'rebook_completed' => 0,
    'review_completion_rate' => 0,
    'email_success_rate' => 0,
], $growth ?? []);
$reviewRequests = array_merge(['pending' => 0, 'opened' => 0, 'submitted' => 0, 'expired' => 0, 'total' => 0], $reviewRequests ?? []);
$emailQueue = array_merge(['pending' => 0, 'processing' => 0, 'sent' => 0, 'failed' => 0, 'cancelled' => 0, 'total' => 0], $emailQueue ?? []);
$referrals = array_merge(['registered' => 0, 'converted' => 0, 'credited' => 0, 'total' => 0], $referrals ?? []);
$credits = array_merge(['pending' => 0, 'approved' => 0, 'redeemed' => 0, 'void' => 0, 'total' => 0], $credits ?? []);
$recentReviewRequests = $recentReviewRequests ?? [];
$recentEmails = $recentEmails ?? [];
$recentReferrals = $recentReferrals ?? [];
$recentCredits = $recentCredits ?? [];
?>

<!-- Admin Analytics (Funnel + Tier 3 Growth) -->
<div class="admin-page-header">
    <h1 class="admin-page-title">Analytics</h1>
    <p class="admin-page-subtitle">
        The booking funnel and Tier 3 growth loop in numbers — reviews, queued emails, referrals, and rebookings from real event data.
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

<section class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Tier 3 Growth Loop</h2>
    </div>
    <div class="stats-grid growth-stats-grid">
        <article class="stat-card growth-stat-card">
            <div class="stat-content">
                <span class="stat-value"><?php echo (int)$growth['review_requested']; ?></span>
                <span class="stat-label">Review Requests</span>
            </div>
        </article>
        <article class="stat-card growth-stat-card">
            <div class="stat-content">
                <span class="stat-value"><?php echo (int)$growth['review_submitted']; ?></span>
                <span class="stat-label">Reviews Submitted</span>
            </div>
        </article>
        <article class="stat-card growth-stat-card">
            <div class="stat-content">
                <span class="stat-value"><?php echo $growth['review_completion_rate']; ?>%</span>
                <span class="stat-label">Review Completion</span>
            </div>
        </article>
        <article class="stat-card growth-stat-card">
            <div class="stat-content">
                <span class="stat-value"><?php echo (int)$growth['email_queued']; ?></span>
                <span class="stat-label">Emails Queued</span>
            </div>
        </article>
        <article class="stat-card growth-stat-card">
            <div class="stat-content">
                <span class="stat-value"><?php echo $growth['email_success_rate']; ?>%</span>
                <span class="stat-label">Email Success</span>
            </div>
        </article>
        <article class="stat-card growth-stat-card">
            <div class="stat-content">
                <span class="stat-value"><?php echo (int)$growth['referral_converted']; ?></span>
                <span class="stat-label">Referral Conversions</span>
            </div>
        </article>
        <article class="stat-card growth-stat-card">
            <div class="stat-content">
                <span class="stat-value"><?php echo (int)$growth['rebook_started']; ?></span>
                <span class="stat-label">Rebooks Started</span>
            </div>
        </article>
        <article class="stat-card growth-stat-card">
            <div class="stat-content">
                <span class="stat-value"><?php echo (int)$growth['rebook_completed']; ?></span>
                <span class="stat-label">Rebooks Completed</span>
            </div>
        </article>
    </div>
</section>

<div class="admin-detail-grid growth-detail-grid">
    <section class="admin-section detail-main">
        <div class="section-header">
            <h2 class="section-title">Review Request Pipeline</h2>
        </div>
        <div class="detail-card">
            <dl class="detail-list growth-metric-list">
                <div class="detail-item"><dt>Total</dt><dd><?php echo (int)$reviewRequests['total']; ?></dd></div>
                <div class="detail-item"><dt>Pending</dt><dd><?php echo (int)$reviewRequests['pending']; ?></dd></div>
                <div class="detail-item"><dt>Opened</dt><dd><?php echo (int)$reviewRequests['opened']; ?></dd></div>
                <div class="detail-item"><dt>Submitted</dt><dd><?php echo (int)$reviewRequests['submitted']; ?></dd></div>
                <div class="detail-item"><dt>Expired</dt><dd><?php echo (int)$reviewRequests['expired']; ?></dd></div>
            </dl>
            <p class="funnel-hint">Completing a booking from the admin panel creates one tokenized request and queues one review email.</p>
        </div>
    </section>

    <section class="admin-section detail-sidebar">
        <div class="section-header">
            <h2 class="section-title">Email Queue Health</h2>
        </div>
        <div class="detail-card">
            <dl class="detail-list growth-metric-list">
                <div class="detail-item"><dt>Total</dt><dd><?php echo (int)$emailQueue['total']; ?></dd></div>
                <div class="detail-item"><dt>Pending</dt><dd><?php echo (int)$emailQueue['pending']; ?></dd></div>
                <div class="detail-item"><dt>Processing</dt><dd><?php echo (int)$emailQueue['processing']; ?></dd></div>
                <div class="detail-item"><dt>Sent</dt><dd><?php echo (int)$emailQueue['sent']; ?></dd></div>
                <div class="detail-item"><dt>Failed</dt><dd><?php echo (int)$emailQueue['failed']; ?></dd></div>
            </dl>
            <p class="funnel-hint">The queue stores review and referral follow-ups. Sending remains controlled by the queue runner.</p>
        </div>
    </section>
</div>

<section class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Recent Review Requests</h2>
    </div>
    <div class="table-container">
        <table class="admin-table" role="table">
            <thead>
                <tr>
                    <th scope="col">Client</th>
                    <th scope="col">Service</th>
                    <th scope="col">Booking</th>
                    <th scope="col">Status</th>
                    <th scope="col">Sent</th>
                    <th scope="col">Expires</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentReviewRequests)): ?>
                    <tr><td colspan="6" class="text-center">No review requests yet — mark a booking completed to create one.</td></tr>
                <?php else: ?>
                    <?php foreach ($recentReviewRequests as $request): ?>
                        <tr>
                            <td>
                                <span class="client-name"><?php echo htmlspecialchars($request['client_name']); ?></span>
                                <span class="client-email"><?php echo htmlspecialchars($request['email']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($request['service_name']); ?></td>
                            <td>#<?php echo (int)$request['booking_id']; ?> · <?php echo date('M j, Y', strtotime($request['booking_date'])); ?></td>
                            <td><span class="status-badge status-<?php echo htmlspecialchars($request['status']); ?>"><?php echo htmlspecialchars($request['status']); ?></span></td>
                            <td><?php echo !empty($request['sent_at']) ? date('M j · g:i A', strtotime($request['sent_at'])) : '—'; ?></td>
                            <td><?php echo !empty($request['expires_at']) ? date('M j, Y', strtotime($request['expires_at'])) : '—'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Recent Queued Emails</h2>
    </div>
    <div class="table-container">
        <table class="admin-table" role="table">
            <thead>
                <tr>
                    <th scope="col">Recipient</th>
                    <th scope="col">Template</th>
                    <th scope="col">Status</th>
                    <th scope="col">Scheduled</th>
                    <th scope="col">Attempts</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentEmails)): ?>
                    <tr><td colspan="5" class="text-center">No queued emails yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($recentEmails as $email): ?>
                        <tr>
                            <td>
                                <span class="client-name"><?php echo htmlspecialchars($email['recipient_name'] ?: 'Client'); ?></span>
                                <span class="client-email"><?php echo htmlspecialchars($email['recipient_email']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars(str_replace('_', ' ', $email['template'])); ?></td>
                            <td><span class="status-badge status-<?php echo htmlspecialchars($email['status']); ?>"><?php echo htmlspecialchars($email['status']); ?></span></td>
                            <td><?php echo date('M j, Y · g:i A', strtotime($email['scheduled_at'])); ?></td>
                            <td><?php echo (int)$email['attempts']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<div class="admin-detail-grid growth-detail-grid">
    <section class="admin-section detail-main">
        <div class="section-header">
            <h2 class="section-title">Referral Conversions</h2>
        </div>
        <div class="detail-card growth-list-card">
            <dl class="detail-list growth-metric-list">
                <div class="detail-item"><dt>Total</dt><dd><?php echo (int)$referrals['total']; ?></dd></div>
                <div class="detail-item"><dt>Converted</dt><dd><?php echo (int)$referrals['converted']; ?></dd></div>
                <div class="detail-item"><dt>Credited</dt><dd><?php echo (int)$referrals['credited']; ?></dd></div>
            </dl>
            <div class="growth-mini-list">
                <?php if (empty($recentReferrals)): ?>
                    <p class="funnel-hint">No referrals recorded yet.</p>
                <?php else: ?>
                    <?php foreach ($recentReferrals as $referral): ?>
                        <div class="growth-mini-item">
                            <strong><?php echo htmlspecialchars($referral['referrer_name']); ?></strong>
                            <span>referred <?php echo htmlspecialchars($referral['referred_name'] ?: $referral['referred_email']); ?> · <?php echo htmlspecialchars($referral['status']); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="admin-section detail-sidebar">
        <div class="section-header">
            <h2 class="section-title">Manual Credits</h2>
        </div>
        <div class="detail-card growth-list-card">
            <dl class="detail-list growth-metric-list">
                <div class="detail-item"><dt>Total</dt><dd><?php echo (int)$credits['total']; ?></dd></div>
                <div class="detail-item"><dt>Pending</dt><dd><?php echo (int)$credits['pending']; ?></dd></div>
                <div class="detail-item"><dt>Approved</dt><dd><?php echo (int)$credits['approved']; ?></dd></div>
                <div class="detail-item"><dt>Redeemed</dt><dd><?php echo (int)$credits['redeemed']; ?></dd></div>
            </dl>
            <div class="growth-mini-list">
                <?php if (empty($recentCredits)): ?>
                    <p class="funnel-hint">No credits recorded yet.</p>
                <?php else: ?>
                    <?php foreach ($recentCredits as $credit): ?>
                        <div class="growth-mini-item">
                            <strong><?php echo htmlspecialchars($credit['client_name']); ?></strong>
                            <span><?php echo htmlspecialchars($credit['description']); ?> · <?php echo htmlspecialchars($credit['status']); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
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
