<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="fiori-page-header mb-4">
    <div class="flex items-center justify-between w-full">
        <div>
            <h1 class="fiori-page-title text-xl">My Support Tickets</h1>
            <p class="fiori-page-subtitle">View and track the status of your reported issues</p>
        </div>
        <a href="<?= base_url('client/tickets/create') ?>" class="btn btn-accent">
            <span class="material-symbols-outlined text-[18px]">add</span>
            New Ticket
        </a>
    </div>
</div>

<div class="mb-4 bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
    <form id="filterForm" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
        <div class="md:col-span-2">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Search Tickets</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" name="search" value="<?= esc(request()->getGet('search')) ?>" 
                       placeholder="Ticket #, subject, category..." 
                       class="fiori-input !pl-10 !h-10 text-xs auto-apply"
                       oninput="clearTimeout(this.delay); this.delay = setTimeout(() => this.form.submit(), 500)">
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Status</label>
            <select name="status" class="fiori-input !h-10 text-xs auto-apply" onchange="this.form.submit()">
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
            <input type="date" name="date_from" value="<?= esc(request()->getGet('date_from')) ?>" class="fiori-input !h-10 text-xs auto-apply" onchange="this.form.submit()">
        </div>

        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Created To</label>
            <input type="date" name="date_to" value="<?= esc(request()->getGet('date_to')) ?>" class="fiori-input !h-10 text-xs auto-apply" onchange="this.form.submit()">
        </div>

        <div class="flex gap-2">
            <button type="submit" class="fiori-button fiori-button--primary !h-10 flex-1">
                Apply
            </button>
            <a href="<?= base_url('client/tickets') ?>" class="fiori-button !bg-slate-50 !text-slate-500 !border-slate-200 !h-10 px-3 flex items-center justify-center" title="Clear Filters">
                <span class="material-symbols-outlined">refresh</span>
            </a>
        </div>
    </form>
</div>

<div class="fiori-card p-0 overflow-hidden">
    <table class="fiori-table">
        <thead>
            <tr>
                <th>Ticket #</th>
                <th>Subject</th>
                <th>Category</th>
                <th>Created</th>
                <th>Status</th>
                <th class="text-right">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($tickets)):
                foreach ($tickets as $t): 
                    $s = $t['status'];
                    $rowColor = '';
                    if ($s === 'Open') $rowColor = 'bg-blue-100 hover:bg-blue-200';
                    elseif ($s === 'In Progress') $rowColor = 'bg-emerald-100 hover:bg-emerald-200';
                    elseif ($s === 'Closed') $rowColor = 'bg-slate-100 hover:bg-slate-200 opacity-90';

                    if (!empty($t['close_requested']) && $s !== 'Closed') {
                        $rowColor = 'bg-yellow-200 hover:bg-yellow-300 animate-pulse';
                    }
                ?>
                    <tr class="group <?= $rowColor ?> transition-colors">
                        <td onclick="window.location.href='<?= base_url('client/tickets/view/' . $t['id']) ?>'" class="cursor-pointer">
                            <span class="font-medium group-hover:text-blue-600 transition-colors" style="color:var(--fiori-text-base);"><?= esc($t['ticket_number']) ?></span>
                        </td>
                        <td onclick="window.location.href='<?= base_url('client/tickets/view/' . $t['id']) ?>'" class="cursor-pointer">
                            <span class="font-medium group-hover:text-blue-600 transition-colors" style="color:var(--fiori-text-base);"><?= esc(strlen($t['subject']) > 60 ? substr($t['subject'], 0, 60) . '…' : $t['subject']) ?></span>
                        </td>
                        <td>
                            <span class="text-sm" style="color:var(--fiori-text-muted);"><?= esc($t['category']) ?></span>
                        </td>
                        <td>
                            <div class="text-[11px] font-medium" style="color:var(--fiori-text-base);"><?= date('M d, Y', strtotime($t['created_at'])) ?></div>
                            <div class="text-[10px]" style="color:var(--fiori-text-muted);"><?= date('H:i', strtotime($t['created_at'])) ?></div>
                        </td>
                        <td>
                            <?php
                                if ($t['status'] === 'Closed') echo '<span class="fiori-status fiori-status--neutral">Closed</span>';
                                elseif ($t['status'] === 'In Progress') echo '<span class="fiori-status fiori-status--positive">In Progress</span>';
                                else echo '<span class="fiori-status fiori-status--information">Open</span>';
                            ?>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-3">
                                <?php if ($t['status'] === 'Closed' && empty($t['feedback_rating'])): ?>
                                    <button onclick="event.stopPropagation(); openFeedbackModal(<?= $t['id'] ?>, '<?= esc($t['ticket_number']) ?>')" class="fiori-button !bg-emerald-50 !text-emerald-600 border-emerald-100 hover:!bg-emerald-100 !text-[10px] h-7 px-3">
                                        <span class="material-symbols-outlined text-[14px]">rate_review</span> Rate Service
                                    </button>
                                <?php endif; ?>
                                <a href="<?= base_url('client/tickets/view/' . $t['id']) ?>" class="text-xs font-semibold" style="color:var(--fiori-blue);">View →</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="5" class="py-12 text-center">
                        <span class="material-symbols-outlined text-4xl block mb-3" style="color:var(--fiori-border);">history_toggle_off</span>
                        <p class="text-sm font-medium" style="color:var(--fiori-text-secondary);">No tickets found</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<!-- Feedback Modal -->
<div id="feedback-modal" class="fiori-overlay hidden">
    <div class="fiori-dialog">
        <div class="fiori-dialog__header">
            <h3 class="fiori-dialog__title">Share Your Feedback</h3>
            <button onclick="closeFeedbackModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="fiori-dialog__body">
            <p class="text-xs text-slate-500 mb-6">How was your experience with Ticket <span id="feedback-ticket-number" class="font-bold text-slate-700"></span>? Your feedback helps us improve our service.</p>
            
            <form id="feedback-form" action="" method="POST">
                <?= csrf_field() ?>
                <div class="space-y-6">
                    <div class="text-center">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Your Rating</label>
                        <div class="flex justify-center gap-4">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <label class="cursor-pointer group">
                                    <input type="radio" name="rating" value="<?= $i ?>" class="hidden peer" required>
                                    <div class="w-12 h-12 rounded-xl border-2 border-slate-100 flex items-center justify-center text-slate-300 transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-600 group-hover:border-emerald-200">
                                        <span class="material-symbols-outlined text-[24px]"><?= $i <= 2 ? 'sentiment_dissatisfied' : ($i <= 3 ? 'sentiment_neutral' : 'sentiment_satisfied') ?></span>
                                    </div>
                                    <span class="text-[9px] font-bold mt-1 block text-slate-400 peer-checked:text-emerald-600 opacity-60 peer-checked:opacity-100"><?= $i ?></span>
                                </label>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Additional Comments</label>
                        <textarea name="comment" class="fiori-input text-xs h-24" placeholder="Any thoughts on how we can do better?"></textarea>
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="closeFeedbackModal()" class="flex-1 btn btn-outline border-slate-200 text-slate-600 hover:bg-slate-50">
                        Maybe Later
                    </button>
                    <button type="submit" class="flex-1 btn btn-accent">
                        Submit Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- REAL TIME SOCKET INTEGRATION ---
        if (typeof io !== 'undefined') {
            const socket = io('http://localhost:3001');
            
            function refreshClientTicketsUI() {
                fetch(window.location.href).then(r => r.text()).then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newTable = doc.querySelector('.fiori-table.w-full');
                    const currentTable = document.querySelector('.fiori-table.w-full');
                    if (newTable && currentTable) {
                        currentTable.innerHTML = newTable.innerHTML;
                    }
                });
            }

            socket.on('global_ticket_change', () => { refreshClientTicketsUI(); });
            socket.on('new_ticket_message', () => { refreshClientTicketsUI(); });
        }

        // Auto-apply logic is now handled via inline onchange/oninput handlers for better reliability.
        // --- FEEDBACK MODAL HANDLERS ---
        window.openFeedbackModal = function(ticketId, ticketNumber) {
            const modal = document.getElementById('feedback-modal');
            const form = document.getElementById('feedback-form');
            const ticketNumSpan = document.getElementById('feedback-ticket-number');
            
            ticketNumSpan.textContent = ticketNumber;
            form.action = `<?= base_url('client/submit-feedback') ?>/${ticketId}`;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };

        window.closeFeedbackModal = function() {
            const modal = document.getElementById('feedback-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };
    });
</script>
<?= $this->endSection() ?>
