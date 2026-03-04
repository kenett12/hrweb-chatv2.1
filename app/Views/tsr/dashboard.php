<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<!-- Fiori Page Header -->
<div class="fiori-page-header">
    <div>
        <h1 class="fiori-page-title">Support Dashboard</h1>
        <p class="fiori-page-subtitle">Manage technical inquiries and active client sessions</p>
    </div>
    <span class="text-xs" style="color:var(--fiori-text-muted);"><?= date('l, F j, Y') ?></span>
</div>

<!-- KPI Tiles -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="fiori-tile">
        <div class="fiori-tile__header">
            <span class="material-symbols-outlined text-[16px]" style="color:var(--fiori-blue);">forum</span>
            Active Chats
        </div>
        <div class="fiori-tile__value"><?= $active_chats ?? 0 ?></div>
        <div class="fiori-tile__footer">Live sessions now</div>
    </div>
    <div class="fiori-tile">
        <div class="fiori-tile__header">
            <span class="material-symbols-outlined text-[16px]" style="color:var(--fiori-warning);">confirmation_number</span>
            Open Tickets
        </div>
        <div class="fiori-tile__value"><?= $open_tickets ?? 0 ?></div>
        <div class="fiori-tile__footer">Pending resolution</div>
    </div>
    <div class="fiori-tile">
        <div class="fiori-tile__header">
            <span class="material-symbols-outlined text-[16px]" style="color:var(--fiori-positive);">schedule</span>
            Avg Response
        </div>
        <div class="fiori-tile__value text-2xl">-- m</div>
        <div class="fiori-tile__footer">Average first reply time</div>
    </div>
    <div class="fiori-tile">
        <div class="fiori-tile__header">
            <span class="material-symbols-outlined text-[16px]" style="color:var(--fiori-positive);">task_alt</span>
            Resolved Today
        </div>
        <div class="fiori-tile__value"><?= $resolved_today ?? 0 ?></div>
        <div class="fiori-tile__footer">Closed tickets today</div>
    </div>
</div>

<!-- Current Sessions -->
<div class="fiori-card overflow-hidden">
    <div class="fiori-card__header">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]" style="color:var(--fiori-blue);">support_agent</span>
            <span class="fiori-card__title">Current Support Sessions</span>
        </div>
    </div>
    <?php if (empty($current_sessions)): ?>
    <div class="fiori-card__content py-16 text-center">
        <span class="material-symbols-outlined text-4xl block mb-3" style="color:var(--fiori-border);">headset_off</span>
        <p class="text-sm font-medium" style="color:var(--fiori-text-secondary);">No Active Sessions</p>
        <p class="text-xs mt-1" style="color:var(--fiori-text-muted);">When clients initiate a chat, they appear here for you to accept.</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="fiori-table">
            <thead>
                <tr>
                    <th>Ticket</th>
                    <th>Client</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($current_sessions as $session): ?>
                <tr>
                    <td class="font-semibold" style="color:var(--fiori-blue);"><?= esc($session['ticket_number']) ?></td>
                    <td style="color:var(--fiori-text-secondary);"><?= esc($session['client_name']) ?></td>
                    <td class="max-w-xs truncate"><?= esc($session['subject']) ?></td>
                    <td>
                        <?php if ($session['status'] === 'Open'): ?>
                            <span class="fiori-status fiori-status--warning">Open</span>
                        <?php elseif ($session['status'] === 'In Progress'): ?>
                            <span class="fiori-status fiori-status--information">In Progress</span>
                        <?php else: ?>
                            <span class="fiori-status fiori-status--neutral"><?= esc($session['status']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <a href="<?= base_url('tsr/tickets/view/' . $session['id']) ?>" class="btn btn-outline" style="height:28px; padding:0 12px; font-size:0.75rem;">View Chat</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
    const socket = io('http://localhost:3001');
    socket.on('global_ticket_change', () => {
        fetch(window.location.href).then(r => r.text()).then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const nc = doc.querySelector('.fade-in');
            if (nc) document.querySelector('.fade-in').innerHTML = nc.innerHTML;
        });
    });
</script>
<?= $this->endSection() ?>