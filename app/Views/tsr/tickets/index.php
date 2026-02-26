<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/tsr/tickets.css') ?>?v=<?= time() ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
        <h1 class="text-2xl font-bold text-gray-800">Ticket Management Queue</h1>
        <p class="text-gray-500 text-sm">Review and manage all incoming support requests.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-400 text-xs uppercase tracking-widest font-semibold">
                    <th class="px-6 py-4">ID</th>
                    <th class="px-6 py-4">Client</th>
                    <th class="px-6 py-4">Subject</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Assigned To</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="ticket-queue-body" class="divide-y divide-gray-100">
                <?php if (!empty($tickets)): ?>
                    <?php foreach ($tickets as $ticket): ?>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-700">#<?= $ticket['id'] ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= esc($ticket['client_name'] ?? 'Guest') ?></td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800"><?= esc($ticket['subject']) ?></td>
                        <td class="px-6 py-4">
                            <span class="badge status-<?= strtolower(str_replace(' ', '_', $ticket['status'])) ?>">
                                <?= $ticket['status'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                             <?= $ticket['status'] === 'In Progress' ? esc($ticket['staff_name'] ?? 'Unassigned') : 'Pending' ?>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="<?= base_url('tsr/tickets/view/'.$ticket['id']) ?>" class="btn btn-info !px-4 !py-1.5 !text-xs">View Thread</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">No tickets found.</td></tr>
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
                            tr.className = "hover:bg-gray-50/50 transition-colors";
                            
                            const idTd = `<td class="px-6 py-4 font-bold text-gray-700">#${ticket.id}</td>`;
                            const clientTd = `<td class="px-6 py-4 text-sm text-gray-600">${ticket.client_name ? ticket.client_name : 'Guest'}</td>`;
                            const subjectTd = `<td class="px-6 py-4 text-sm font-medium text-gray-800">${ticket.subject}</td>`;
                            
                            const statusLower = ticket.status.toLowerCase().replace(' ', '_');
                            const statusTd = `<td class="px-6 py-4"><span class="badge status-${statusLower}">${ticket.status}</span></td>`;
                            
                            const assignedTo = ticket.status === 'In Progress' ? (ticket.staff_name || 'Unassigned') : 'Pending';
                            const assignedTd = `<td class="px-6 py-4 text-sm text-gray-600">${assignedTo}</td>`;
                            
                            const actionTd = `<td class="px-6 py-4 text-right space-x-2"><a href="${viewBaseUrl}${ticket.id}" class="btn btn-info !px-4 !py-1.5 !text-xs">View Thread</a></td>`;
                            
                            tr.innerHTML = idTd + clientTd + subjectTd + statusTd + assignedTd + actionTd;
                            queueBody.appendChild(tr);
                        });
                    } else {
                        queueBody.innerHTML = `<tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">No tickets found.</td></tr>`;
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