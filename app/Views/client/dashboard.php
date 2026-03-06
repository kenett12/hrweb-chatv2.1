<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('styles') ?>
<!-- Dashboard specific styles removed to match Fiori -->
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Fiori Page Header -->
<div class="fiori-page-header">
    <div>
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded flex items-center justify-center text-white text-xs font-semibold" style="background:var(--fiori-blue); border-radius:4px;">
                <?= strtoupper(substr($company_name ?? 'C', 0, 1)) ?>
            </div>
            <h1 class="fiori-page-title"><?= esc($company_name ?? 'Dashboard') ?></h1>
        </div>
        <p class="fiori-page-subtitle">Manage your corporate support inquiries and chat history</p>
    </div>
    <!-- HR Representatives -->
    <div class="flex items-center gap-3 px-4 py-2.5 border rounded" style="border-color:var(--fiori-border); background:var(--fiori-surface); border-radius:4px; min-width:200px;">
        <span class="material-symbols-outlined text-[18px] flex-none" style="color:var(--fiori-blue);">badge</span>
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-wider mb-0.5" style="color:var(--fiori-text-muted);">HR Representatives</p>
            <?php
                $contacts = json_decode($hr_contact ?? 'null', true);
                if (is_array($contacts) && !empty($contacts)) {
                    $names = array_values(array_filter($contacts));
                    echo '<p class="text-xs font-semibold" style="color:var(--fiori-text-base);">' . implode(' · ', array_map('esc', $names)) . '</p>';
                } else {
                    echo '<p class="text-xs font-semibold" style="color:var(--fiori-text-base);">' . esc($hr_contact ?? 'Unassigned') . '</p>';
                }
            ?>
        </div>
    </div>
</div>

<div id="dashboard-content">

<!-- Analytics KPI Tiles -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="fiori-tile">
        <div class="fiori-tile__header">
            <span class="material-symbols-outlined text-[16px]" style="color:var(--fiori-positive);">group</span>
            Visitors
        </div>
        <div class="flex items-baseline gap-2">
            <div class="fiori-tile__value"><?= $visitors_today ?? 0 ?></div>
            <span class="text-xs font-semibold <?= ($visitors_trend ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                <?= ($visitors_trend ?? 0) >= 0 ? '↑' : '↓' ?><?= abs(round($visitors_trend ?? 0, 1)) ?>%
            </span>
        </div>
        <div class="fiori-tile__footer">Today · Last 7d: <?= round($visitors_last_7 ?? 0, 1) ?></div>
    </div>

    <div class="fiori-tile">
        <div class="fiori-tile__header">
            <span class="material-symbols-outlined text-[16px]" style="color:var(--fiori-blue);">chat</span>
            Chats
        </div>
        <div class="fiori-tile__value"><?= $chats_today ?? 0 ?></div>
        <div class="fiori-tile__footer">Answered today · Last 7d: <?= round($chats_last_7 ?? 0, 1) ?></div>
    </div>

    <div class="fiori-tile">
        <div class="fiori-tile__header">
            <span class="material-symbols-outlined text-[16px]" style="color:var(--fiori-warning);">visibility</span>
            Page Views
        </div>
        <div class="flex items-baseline gap-2">
            <div class="fiori-tile__value"><?= $views_today ?? 0 ?></div>
            <span class="text-xs font-semibold <?= ($views_trend ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                <?= ($views_trend ?? 0) >= 0 ? '↑' : '↓' ?><?= abs(round($views_trend ?? 0, 1)) ?>%
            </span>
        </div>
        <div class="fiori-tile__footer">Today · Last 7d: <?= round($views_last_7 ?? 0, 1) ?></div>
    </div>

    <div class="fiori-tile">
        <div class="fiori-tile__header">
            <span class="material-symbols-outlined text-[16px]" style="color:var(--fiori-blue);">monitoring</span>
            Reporting
        </div>
        <div class="space-y-2 mt-2">
            <div class="flex items-center justify-between text-xs">
                <span style="color:var(--fiori-text-secondary);">Positive Sentiment</span>
                <span class="font-semibold <?= ($sentiment ?? 0) > 50 ? 'text-green-600' : 'text-red-600' ?>"><?= $sentiment ?? 0 ?>%</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span style="color:var(--fiori-text-muted);">Availability</span>
                <span class="font-semibold" style="color:var(--fiori-positive);">100%</span>
            </div>
        </div>
    </div>
</div>

<!-- Recent Support History -->
<div class="fiori-card overflow-hidden">
    <div class="fiori-card__header">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]" style="color:var(--fiori-blue);">history</span>
            <span class="fiori-card__title">Recent Support History</span>
        </div>
        <a href="<?= base_url('client/tickets') ?>" class="text-xs font-semibold" style="color:var(--fiori-blue);">View All →</a>
    </div>

    <?php if (!empty($recent_tickets)): ?>
    <div class="overflow-x-auto">
        <table class="fiori-table">
            <tbody>
                <?php foreach ($recent_tickets as $ticket): ?>
                <?php 
                    $isClosed = ($ticket['status'] === 'Closed');
                    $hasFeedback = !empty($ticket['feedback_rating']);
                ?>
                <tr class="group">
                    <td onclick="window.location.href='<?= base_url('client/tickets/view/' . $ticket['id']) ?>'" class="cursor-pointer">
                        <p class="font-medium text-sm group-hover:text-blue-600 transition-colors" style="color:var(--fiori-text-base);"><?= esc(strlen($ticket['subject']) > 60 ? substr($ticket['subject'], 0, 60) . '…' : $ticket['subject']) ?></p>
                        <p class="text-xs mt-0.5 font-medium" style="color:var(--fiori-text-muted);"><?= esc($ticket['ticket_number']) ?> · <?= date('M d, Y', strtotime($ticket['updated_at'])) ?></p>
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-3">
                            <?php if ($isClosed && !$hasFeedback): ?>
                                <button onclick="openFeedbackModal(<?= $ticket['id'] ?>, '<?= esc($ticket['ticket_number']) ?>')" class="fiori-button !bg-emerald-50 !text-emerald-600 border-emerald-100 hover:!bg-emerald-100 !text-[10px] h-7 px-3">
                                    <span class="material-symbols-outlined text-[14px]">rate_review</span> Rate Service
                                </button>
                            <?php endif; ?>
                            
                            <?php
                                if ($ticket['status'] === 'Closed') echo '<span class="fiori-status fiori-status--neutral">Closed</span>';
                                elseif ($ticket['status'] === 'In Progress') echo '<span class="fiori-status fiori-status--information">In Progress</span>';
                                else echo '<span class="fiori-status fiori-status--warning">' . esc($ticket['status']) . '</span>';
                            ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="fiori-card__content py-16 text-center">
        <span class="material-symbols-outlined text-4xl block mb-3" style="color:var(--fiori-border);">history_toggle_off</span>
        <p class="text-sm font-medium" style="color:var(--fiori-text-secondary);">No recent tickets found</p>
        <p class="text-xs mt-1" style="color:var(--fiori-text-muted);">Your support history will appear here.</p>
    </div>
    <?php endif; ?>
</div>
</div>

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
                    <button type="button" onclick="closeFeedbackModal()" class="flex-1 fiori-button !bg-slate-100 !text-slate-600 hover:!bg-slate-200">
                        Maybe Later
                    </button>
                    <button type="submit" class="flex-1 fiori-button fiori-button--primary">
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
    const socket = io('http://localhost:3001');
    socket.on('global_ticket_change', () => {
        fetch(window.location.href).then(r => r.text()).then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const nc = doc.querySelector('#dashboard-content');
            if (nc) document.querySelector('#dashboard-content').innerHTML = nc.innerHTML;
        });
    });

    function openFeedbackModal(ticketId, ticketNumber) {
        const modal = document.getElementById('feedback-modal');
        const form = document.getElementById('feedback-form');
        const ticketNumSpan = document.getElementById('feedback-ticket-number');
        
        ticketNumSpan.textContent = ticketNumber;
        form.action = `<?= base_url('client/submit-feedback') ?>/${ticketId}`;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeFeedbackModal() {
        const modal = document.getElementById('feedback-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
<?= $this->endSection() ?>