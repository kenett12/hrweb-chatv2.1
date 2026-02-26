<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="container mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Support Dashboard</h1>
        <p class="text-slate-500">Managing technical inquiries and active client sessions.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 transition hover:shadow-md">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i class="fas fa-comments text-blue-600 text-xl"></i>
                </div>
                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-md uppercase">Live</span>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Active Chats</p>
            <h3 class="text-2xl font-black text-slate-800"><?= $active_chats ?></h3>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 transition hover:shadow-md">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-amber-100 p-3 rounded-xl">
                    <i class="fas fa-ticket-alt text-amber-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Open Tickets</p>
            <h3 class="text-2xl font-black text-slate-800"><?= $open_tickets ?></h3>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 transition hover:shadow-md">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-green-100 p-3 rounded-xl">
                    <i class="fas fa-clock text-green-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Avg Response</p>
            <h3 class="text-2xl font-black text-slate-800">-- m</h3>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 transition hover:shadow-md">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-purple-100 p-3 rounded-xl">
                    <i class="fas fa-check-double text-purple-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Resolved Today</p>
            <h3 class="text-2xl font-black text-slate-800"><?= $resolved_today ?></h3>
        </div>

    </div>

    <div class="mt-10 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h4 class="font-bold text-slate-800 uppercase text-xs tracking-widest">Current Support Sessions</h4>
            <button class="text-blue-600 text-xs font-bold hover:underline">Refresh List</button>
        </div>
        <?php if (empty($current_sessions)): ?>
            <div class="p-16 text-center">
                <div class="inline-block p-6 rounded-full bg-slate-50 mb-4">
                    <i class="fas fa-headset text-slate-300 text-4xl"></i>
                </div>
                <h5 class="text-slate-800 font-bold">No Active Sessions</h5>
                <p class="text-slate-400 text-sm max-w-xs mx-auto">When clients initiate a chat from the HRWeb portal, they will appear here for you to accept.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                            <th class="px-6 py-4">Ticket</th>
                            <th class="px-6 py-4">Client</th>
                            <th class="px-6 py-4">Subject</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach($current_sessions as $session): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-800 text-sm"><?= esc($session['ticket_number']) ?></span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    <?= esc($session['client_name']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-slate-700 block truncate max-w-[200px]"><?= esc($session['subject']) ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($session['status'] === 'Open'): ?>
                                        <span class="bg-amber-100 text-amber-700 px-2 py-1 rounded-md text-xs font-bold">Open</span>
                                    <?php elseif ($session['status'] === 'In Progress'): ?>
                                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-md text-xs font-bold">In Progress</span>
                                    <?php else: ?>
                                        <span class="bg-slate-100 text-slate-700 px-2 py-1 rounded-md text-xs font-bold"><?= esc($session['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="<?= base_url('tsr/tickets/view/' . $session['id']) ?>" 
                                       class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg text-xs font-bold transition-colors">
                                        View Chat
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
    // ── REAL TIME DASHBOARD SYNC ──
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

    console.log("TSR Dashboard Real-Time Sync Active (Seamless).");
</script>
<?= $this->endSection() ?>