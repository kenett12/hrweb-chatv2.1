<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/tsr/tickets.css') ?>?v=<?= time() ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
        <h1 class="text-2xl font-bold text-gray-800">Global Ticket Directory</h1>
        <p class="text-gray-500 text-sm">Read-only oversight of all generated support tickets.</p>
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
                             <?= ($ticket['status'] === 'In Progress' || $ticket['status'] === 'Resolved' || $ticket['status'] === 'Closed') ? esc($ticket['staff_name'] ?? 'Unknown TSR') : 'Pending' ?>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="<?= base_url('superadmin/tickets/view/'.$ticket['id']) ?>" class="btn btn-info !px-4 !py-1.5 !text-xs">Review Thread</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">No tickets found in the system.</td></tr>
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
    socket.on('global_ticket_change', (data) => {
        fetch(window.location.href)
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newContent = doc.querySelector('.fade-in');
                if (newContent) {
                    document.querySelector('.fade-in').innerHTML = newContent.innerHTML;
                }
            });
    });
    console.log("Admin Global Ticket Queue Real-Time Sync Active (Seamless).");
</script>
<?= $this->endSection() ?>
