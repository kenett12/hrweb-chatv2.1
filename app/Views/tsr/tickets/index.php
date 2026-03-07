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

<div class="mb-4 bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
    <form id="filterForm" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
        <div class="md:col-span-2">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Search Tickets</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" name="search" value="<?= esc(request()->getGet('search')) ?>" 
                       placeholder="Ticket #, subject, client..." 
                       class="fiori-input !pl-10 !h-10 text-xs auto-apply">
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Status</label>
            <select name="status" class="fiori-input !h-10 text-xs auto-apply">
                <option value="">All Statuses</option>
                <?php 
                $statuses = ['Open', 'In Progress', 'Closed'];
                $currentStatus = request()->getGet('status') ?? 'Open';
                foreach($statuses as $s): ?>
                    <option value="<?= $s ?>" <?= $currentStatus === $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Created From</label>
            <input type="date" name="date_from" value="<?= esc(request()->getGet('date_from')) ?>" class="fiori-input !h-10 text-xs auto-apply">
        </div>

        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Created To</label>
            <input type="date" name="date_to" value="<?= esc(request()->getGet('date_to')) ?>" class="fiori-input !h-10 text-xs auto-apply">
        </div>

        <div class="flex gap-2">
            <button type="submit" class="fiori-button fiori-button--primary !h-10 flex-1">
                Apply
            </button>
            <a href="<?= base_url('tsr/tickets') ?>" class="fiori-button !bg-slate-50 !text-slate-500 !border-slate-200 !h-10 px-3 flex items-center justify-center" title="Clear Filters">
                <span class="material-symbols-outlined">refresh</span>
            </a>
        </div>
    </form>
</div>

<div class="fiori-card overflow-hidden">

    <div class="overflow-x-auto scrollbar-thin">
        <table class="fiori-table min-w-[1500px]">
            <thead>
                <tr>
                    <th class="w-16 text-left">NO</th>
                    <th class="w-40 text-left">DATE/TIME</th>
                    <th class="w-48 text-left">Client(s)</th>
                    <th class="w-40 text-left">TYPE</th>
                    <th class="w-64 text-left">CONCERN(S)</th>
                    <th class="w-32 text-left">FIXED ON</th>
                    <th class="w-32 text-left">DUE DATE</th>
                    <th class="w-48 text-left">STATUS</th>
                    <th class="w-40 text-left">ATTENDED BY</th>
                    <th class="sticky right-0 bg-white shadow-[-4px_0_8px_rgba(0,0,0,0.05)] w-24 text-center">ACTIONS</th>
                </tr>
            </thead>
            <tbody id="ticket-queue-body">
                <?php if (!empty($tickets)): ?>
                    <?php foreach ($tickets as $ticket): 
                    $s = $ticket['status'];
                    $rowColor = '';
                    if ($s === 'Open') $rowColor = 'bg-blue-100 hover:bg-blue-200';
                    elseif ($s === 'In Progress') $rowColor = 'bg-emerald-100 hover:bg-emerald-200';
                    elseif ($s === 'Closed') $rowColor = 'bg-slate-100 hover:bg-slate-200 opacity-90';

                    if (!empty($ticket['close_requested']) && $s !== 'Closed') {
                        $rowColor = 'bg-yellow-200 hover:bg-yellow-300 animate-pulse';
                    }
                ?>
                    <tr class="<?= $rowColor ?> transition-colors cursor-pointer" onclick="window.location='<?= base_url('tsr/tickets/view/' . $ticket['id']) ?>'">
                        <td class="font-mono text-xs text-slate-500">#<?= (int)$ticket['id'] ?></td>
                        <td class="text-xs">
                            <div class="font-bold text-slate-700"><?= date('M d, Y', strtotime($ticket['created_at'])) ?></div>
                            <div class="text-[10px] text-slate-400"><?= date('h:i A', strtotime($ticket['created_at'])) ?></div>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded bg-blue-50 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[16px] text-blue-500">corporate_fare</span>
                                </div>
                                <div class="overflow-hidden">
                                    <div class="text-[11px] font-bold text-slate-700 truncate"><?= esc($ticket['client_name'] ?? 'Guest') ?></div>
                                    <div class="text-[10px] text-slate-400 truncate"><?= esc($ticket['creator_name'] ?? 'System') ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                <?= esc($ticket['category'] ?: 'General') ?>
                            </span>
                        </td>
                        <td>
                            <div class="text-[11px] font-bold text-slate-800 line-clamp-1"><?= esc($ticket['subject']) ?></div>
                            <div class="text-[10px] text-slate-400 line-clamp-2 mt-0.5"><?= esc($ticket['description']) ?></div>
                        </td>
                        <td class="text-xs text-slate-500">
                            <?= $ticket['fixed_at'] ? date('M d, H:i', strtotime($ticket['fixed_at'])) : '<span class="text-slate-300 italic">---</span>' ?>
                        </td>
                        <td class="text-xs font-medium text-slate-600">
                            <?= $ticket['due_date'] ? date('M d, Y', strtotime($ticket['due_date'])) : '<span class="text-slate-300 italic">---</span>' ?>
                        </td>
                        <td>
                            <?php
                                $statusClass = 'fiori-status--neutral';
                                if ($ticket['status'] === 'Open') $statusClass = 'fiori-status--information';
                                if ($ticket['status'] === 'In Progress') $statusClass = 'fiori-status--positive';
                                if ($ticket['status'] === 'Closed') $statusClass = 'fiori-status--neutral'; 
                            ?>
                            <div class="flex flex-col gap-1">
                                <span class="fiori-status <?= $statusClass ?> whitespace-nowrap w-fit">
                                    <?= esc($ticket['status']) ?>
                                </span>
                                <?php if ($ticket['close_requested'] && $ticket['status'] !== 'Closed'): ?>
                                    <span class="text-[9px] font-bold uppercase text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded animate-pulse whitespace-nowrap w-fit">Review Req.</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-[14px]">person</span>
                                </div>
                                <span class="text-[10px] text-slate-600 font-medium whitespace-nowrap">
                                    <?= esc($ticket['staff_name'] ?? 'Pending') ?>
                                </span>
                            </div>
                        </td>
                        <td class="sticky right-0 shadow-[-4px_0_8px_rgba(0,0,0,0.05)] text-center" style="background-color: inherit;">
                            <div class="flex justify-center gap-1" onclick="event.stopPropagation()">
                                <a href="<?= base_url('tsr/tickets/view/' . $ticket['id']) ?>" class="p-1.5 hover:bg-white/50 rounded text-blue-600 transition-colors" title="View Details">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="11" class="text-center py-12 text-slate-400 italic">No tickets found.</td></tr>
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

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
        }

        function formatTime(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
        }

        function fetchLiveQueue() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            fetch("<?= base_url('tsr/tickets/live-queue') ?>?" + params.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        queueBody.innerHTML = '';
                        data.forEach(ticket => {
                            const tr = document.createElement('tr');
                            tr.className = 'hover:bg-slate-50/50 transition-colors';
                            tr.style.cursor = 'pointer';
                            tr.onclick = () => window.location = `${viewBaseUrl}${ticket.id}`;
                            
                            let statusClass = 'fiori-status--neutral';
                            if (ticket.status === 'Open') statusClass = 'fiori-status--information';
                            if (ticket.status === 'In Progress') statusClass = 'fiori-status--positive';
                            if (ticket.status === 'Closed') statusClass = 'fiori-status--neutral';
                            
                            const idTd = `<td class="font-mono text-xs text-slate-500">#${ticket.id}</td>`;
                            const dateTd = `<td class="text-xs">
                                <div class="font-bold text-slate-700">${formatDate(ticket.created_at)}</div>
                                <div class="text-[10px] text-slate-400">${formatTime(ticket.created_at)}</div>
                            </td>`;
                            const clientTd = `<td>
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded bg-blue-50 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[16px] text-blue-500">corporate_fare</span>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="text-[11px] font-bold text-slate-700 truncate">${ticket.client_name || 'Guest'}</div>
                                        <div class="text-[10px] text-slate-400 truncate">${ticket.creator_name || 'System'}</div>
                                    </div>
                                </div>
                            </td>`;
                            const typeTd = `<td><span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">${ticket.category || 'General'}</span></td>`;
                            const subjectTd = `<td>
                                <div class="text-[11px] font-bold text-slate-800 line-clamp-1">${ticket.subject}</div>
                                <div class="text-[10px] text-slate-400 line-clamp-2 mt-0.5">${ticket.description || ''}</div>
                            </td>`;
                            const fixedTd = `<td class="text-xs text-slate-500">${ticket.fixed_at ? ticket.fixed_at : '<span class="text-slate-300 italic">---</span>'}</td>`;
                            const dueTd = `<td class="text-xs font-medium text-slate-600">${ticket.due_date ? ticket.due_date : '<span class="text-slate-300 italic">---</span>'}</td>`;
                            const reviewReqBadge = (ticket.close_requested == 1 && ticket.status !== 'Closed') 
                                ? `<span class="text-[9px] font-bold uppercase text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded animate-pulse whitespace-nowrap w-fit">Review Req.</span>` 
                                : '';
                            const statusTd = `<td><div class="flex flex-col gap-1"><span class="fiori-status ${statusClass} whitespace-nowrap w-fit">${ticket.status}</span>${reviewReqBadge}</div></td>`;
                            const staffTd = `<td>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                        <span class="material-symbols-outlined text-[14px]">person</span>
                                    </div>
                                    <span class="text-[10px] text-slate-600 font-medium whitespace-nowrap">${ticket.staff_name || 'Pending'}</span>
                                </div>
                            </td>`;

                            const actionTd = `<td class="sticky right-0 bg-white shadow-[-4px_0_8px_rgba(0,0,0,0.05)] text-center">
                                <div class="flex justify-center gap-1" onclick="event.stopPropagation()">
                                    <a href="${viewBaseUrl}${ticket.id}" class="p-1.5 hover:bg-blue-50 rounded text-blue-600 transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                </div>
                            </td>`;
                            
                            tr.innerHTML = idTd + dateTd + clientTd + typeTd + subjectTd + fixedTd + dueTd + statusTd + staffTd + actionTd;
                            queueBody.appendChild(tr);
                        });
                    } else {
                        queueBody.innerHTML = `<tr><td colspan="11" class="text-center py-12 text-slate-400 italic">No tickets found.</td></tr>`;
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
            socket.on('new_ticket_message', (data) => {
                fetchLiveQueue();
            });
            console.log("TSR Ticket Queue Real-Time Sync Active.");
        }

        // --- AUTO-APPLY FILTERS ---
        let debounceTimer;
        document.querySelectorAll('.auto-apply').forEach(input => {
            const events = input.tagName === 'SELECT' || input.type === 'date' ? ['change'] : ['keyup'];
            
            events.forEach(action => {
                input.addEventListener(action, () => {
                    clearTimeout(debounceTimer);
                    const delay = (input.tagName === 'INPUT' && input.type === 'text') ? 500 : 0;
                    
                    debounceTimer = setTimeout(() => {
                        // Update URL without reload
                        const form = document.getElementById('filterForm');
                        const formData = new FormData(form);
                        const params = new URLSearchParams(formData);
                        const url = new URL(window.location.href);
                        params.forEach((value, key) => {
                            if (value) url.searchParams.set(key, value);
                            else url.searchParams.delete(key);
                        });
                        window.history.pushState({}, '', url);

                        // Trigger AJAX update
                        fetchLiveQueue();
                    }, delay);
                });
            });
        });
    });
</script>
<?= $this->endSection() ?>