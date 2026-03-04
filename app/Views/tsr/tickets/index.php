<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/tsr/tickets.css') ?>?v=<?= time() ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- SAP Fiori Page Header -->
<div class="fiori-page-header">
    <div>
        <h1 class="fiori-page-title">Ticket Management Queue</h1>
        <p class="fiori-page-subtitle">Review and manage all incoming support requests.</p>
    </div>
</div>

<div class="fiori-card overflow-hidden">

    <div class="overflow-x-auto">
        <table class="fiori-table">
            <thead>
                <tr>
                    <th>ID</th>
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
                        <td class="font-semibold text-gray-700">#<?= $ticket['id'] ?></td>
                        <td><?= esc($ticket['client_name'] ?? 'Guest') ?></td>
                        <td class="font-medium" style="color:var(--fiori-text-base);"><?= esc($ticket['subject']) ?></td>
                        <td>
                            <?php
                                $statusClass = 'fiori-status--neutral';
                                if ($ticket['status'] === 'Open') $statusClass = 'fiori-status--information';
                                if ($ticket['status'] === 'In Progress') $statusClass = 'fiori-status--positive';
                                if ($ticket['status'] === 'Resolved') $statusClass = 'fiori-status--critical'; 
                                // Actually, standard is: Open=Info/Warning, In Progress=Info/Base, Resolved=Positive.
                            ?>
                            <span class="fiori-status <?= $statusClass ?>">
                                <?= $ticket['status'] ?>
                            </span>
                        </td>
                        <td>
                             <?= $ticket['status'] === 'In Progress' ? esc($ticket['staff_name'] ?? 'Unassigned') : '<span class="italic text-xs" style="color:var(--fiori-text-muted);">Pending</span>' ?>
                        </td>
                        <td class="text-right">
                            <a href="<?= base_url('tsr/tickets/view/'.$ticket['id']) ?>" class="btn btn-outline" style="height:28px; padding:0 12px; font-size:0.75rem;">View Thread</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center py-10" style="color:var(--fiori-text-muted);">No tickets found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const queueBody = document.getElementById('ticket-queue-body');
        const viewBaseUrl = "<?= base_url('tsr/tickets/view') ?>/";

        function fetchLiveQueue() {
            fetch("<?= base_url('tsr/tickets/live-queue') ?>")
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        queueBody.innerHTML = '';
                        data.forEach(ticket => {
                            const tr = document.createElement('tr');
                            
                            const idTd = `<td class="font-semibold text-gray-700">#${ticket.id}</td>`;
                            const clientTd = `<td>${ticket.client_name ? ticket.client_name : 'Guest'}</td>`;
                            const subjectTd = `<td class="font-medium" style="color:var(--fiori-text-base);">${ticket.subject}</td>`;
                            
                            let statusClass = 'fiori-status--neutral';
                            if (ticket.status === 'Open') statusClass = 'fiori-status--information';
                            if (ticket.status === 'In Progress') statusClass = 'fiori-status--positive';
                            if (ticket.status === 'Resolved') statusClass = 'fiori-status--critical';
                            
                            const statusTd = `<td><span class="fiori-status ${statusClass}">${ticket.status}</span></td>`;
                            
                            const assignedTo = ticket.status === 'In Progress' ? (ticket.staff_name || 'Unassigned') : '<span class="italic text-xs" style="color:var(--fiori-text-muted);">Pending</span>';
                            const assignedTd = `<td>${assignedTo}</td>`;
                            
                            const actionTd = `<td class="text-right"><a href="${viewBaseUrl}${ticket.id}" class="btn btn-outline" style="height:28px; padding:0 12px; font-size:0.75rem;">View Thread</a></td>`;
                            
                            tr.innerHTML = idTd + clientTd + subjectTd + statusTd + assignedTd + actionTd;
                            queueBody.appendChild(tr);
                        });
                    } else {
                        queueBody.innerHTML = `<tr><td colspan="6" class="text-center py-10" style="color:var(--fiori-text-muted);">No tickets found.</td></tr>`;
                    }
                })
                .catch(error => console.error('Error fetching live queue:', error));
        }

        // Initial Fetch
        fetchLiveQueue();

        // ── REAL TIME SOCKET PUSH LISTENER ──
        if (typeof io !== 'undefined') {
            const socket = io('http://localhost:3001');
            socket.on('global_ticket_change', (data) => {
                // Instantly fetch the fresh queue from the database when a ticket event fires anywhere
                fetchLiveQueue();
            });
            console.log("TSR Ticket Queue Real-Time Sync Active.");
        }
    });
</script>
<?= $this->endSection() ?>