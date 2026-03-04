<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<!-- SAP Fiori Page Header -->
<div class="fiori-page-header">
    <div>
        <h1 class="fiori-page-title">Global Ticket Directory</h1>
        <p class="fiori-page-subtitle">Read-only oversight of all generated support tickets across all clients</p>
    </div>
</div>

<?php
// Build a URL filter for client_id if present
$clientFilter = request()->getGet('client_id');
?>

<?php if ($clientFilter): ?>
<div class="mb-4 flex items-center gap-3 px-3 py-2 rounded text-sm font-medium" style="background:var(--fiori-blue-light); border:1px solid #b3d4fb; border-radius:4px; color:var(--fiori-blue); max-width:fit-content;">
    <span class="material-symbols-outlined text-[16px]">filter_alt</span>
    Filtered by client ID: <?= (int)$clientFilter ?>
    <a href="<?= base_url('superadmin/tickets') ?>" class="ml-2 underline text-xs" style="color:var(--fiori-blue);">Clear filter</a>
</div>
<?php endif; ?>

<div class="fiori-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="fiori-table">
            <thead>
                <tr>
                    <th>Ticket #</th>
                    <th>Client</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="ticket-queue-body">
                <?php if (!empty($tickets)): ?>
                    <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td>
                            <span class="fiori-status fiori-status--information font-mono">#<?= (int)$ticket['id'] ?></span>
                        </td>
                        <td style="color:var(--fiori-text-secondary);"><?= esc($ticket['client_name'] ?? 'Guest') ?></td>
                        <td>
                            <span class="font-medium text-sm" style="color:var(--fiori-text-base);"><?= esc(strlen($ticket['subject']) > 55 ? substr($ticket['subject'],0,55).'…' : $ticket['subject']) ?></span>
                        </td>
                        <td>
                            <?php
                                $s = $ticket['status'];
                                if ($s === 'Resolved' || $s === 'Closed') echo '<span class="fiori-status fiori-status--positive">' . esc($s) . '</span>';
                                elseif ($s === 'In Progress') echo '<span class="fiori-status fiori-status--information">In Progress</span>';
                                elseif ($s === 'Open') echo '<span class="fiori-status fiori-status--warning">Open</span>';
                                else echo '<span class="fiori-status fiori-status--neutral">' . esc($s) . '</span>';
                            ?>
                        </td>
                        <td style="color:var(--fiori-text-secondary);">
                            <?= (in_array($ticket['status'], ['In Progress','Resolved','Closed'])) ? esc($ticket['staff_name'] ?? 'Unknown TSR') : '<span style="color:var(--fiori-text-muted); font-style:italic;">Pending</span>' ?>
                        </td>
                        <td class="text-right">
                            <a href="<?= base_url('superadmin/tickets/view/'.$ticket['id']) ?>" class="btn btn-outline" style="height:28px; padding:0 12px; font-size:0.75rem;">
                                Review Thread
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6" class="py-16 text-center">
                        <span class="material-symbols-outlined text-4xl block mb-3" style="color:var(--fiori-border);">confirmation_number</span>
                        <p class="text-sm font-medium" style="color:var(--fiori-text-secondary);">No tickets found</p>
                        <p class="text-xs mt-1" style="color:var(--fiori-text-muted);">Tickets will appear here once clients submit support requests.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
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
