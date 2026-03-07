<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('styles') ?>
<style>
.msg-text img {
    max-width: 100%;
    border-radius: 8px;
    margin-top: 10px;
    cursor: pointer;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-8 max-w-6xl mx-auto">
    <div class="mb-4">
        <a href="<?= base_url('client/tickets') ?>" class="text-xs font-semibold" style="color:var(--fiori-blue); display:flex; align-items:center; gap:4px;">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Back to My Tickets
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-4">
            
            <div class="fiori-card p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded mb-2 inline-block" style="background:var(--fiori-surface); color:var(--fiori-text-muted);">
                            <?= $ticket['ticket_number'] ?>
                        </span>
                        <h1 class="fiori-page-title text-xl mb-0">
                            <?= esc($ticket['subject']) ?>
                        </h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <?php if ($ticket['status'] === 'Closed' && empty($ticket['feedback_rating'])): ?>
                            <button onclick="openFeedbackModal(<?= $ticket['id'] ?>, '<?= esc($ticket['ticket_number']) ?>')" class="fiori-button !bg-emerald-50 !text-emerald-600 border-emerald-100 hover:!bg-emerald-100 !text-[10px] h-8 px-4">
                                <span class="material-symbols-outlined text-[16px]">rate_review</span> Rate our Service
                            </button>
                        <?php endif; ?>

                        <?php if ($ticket['status'] !== 'Closed' && !$ticket['close_requested']): ?>
                            <form action="<?= base_url('client/tickets/request-closure/' . $ticket['id']) ?>" method="POST" onsubmit="return confirm('Request to close this ticket?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="fiori-button !bg-slate-50 !text-slate-600 border-slate-200 hover:!bg-slate-100 !text-[10px] h-8 px-4">
                                    <span class="material-symbols-outlined text-[16px]">close_fullscreen</span> Request Closure
                                </button>
                            </form>
                        <?php elseif ($ticket['close_requested'] && $ticket['status'] !== 'Closed'): ?>
                            <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-100">
                                <span class="material-symbols-outlined text-[14px] align-middle mr-1">history_toggle_off</span> Closure Requested
                            </span>
                        <?php endif; ?>

                        <?php
                            if ($ticket['status'] === 'Closed') echo '<span class="fiori-status fiori-status--neutral">Closed</span>';
                            elseif ($ticket['status'] === 'In Progress') echo '<span class="fiori-status fiori-status--information">In Progress</span>';
                            else echo '<span class="fiori-status fiori-status--warning">' . esc($ticket['status']) . '</span>';
                        ?>
                    </div>
                </div>

                <p class="text-sm leading-relaxed mb-6" style="color:var(--fiori-text-primary);">
                    <?= nl2br(esc($ticket['description'])) ?>
                </p>

                <?php 
                    $attachments = !empty($ticket['attachments']) ? json_decode($ticket['attachments'], true) : [];
                    $links = !empty($ticket['external_links']) ? json_decode($ticket['external_links'], true) : [];
                ?>

                <?php if (!empty($attachments) || !empty($ticket['attachment'])): ?>
                    <div class="pt-6 border-t border-gray-50">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Evidence Gallery</p>
                        <div class="flex flex-wrap gap-4">
                            <?php 
                            // Include legacy attachment if it exists and isn't in the new list
                            $allAttachments = $attachments;
                            if (!empty($ticket['attachment']) && !in_array($ticket['attachment'], $allAttachments)) {
                                array_unshift($allAttachments, $ticket['attachment']);
                            }
                            ?>
                            <?php foreach ($allAttachments as $index => $file): ?>
                                <div class="relative w-32 h-32 group cursor-zoom-in"
                                    onclick="openPhotoModal('<?= base_url('uploads/tickets/' . $file) ?>')">
                                    <img src="<?= base_url('uploads/tickets/' . $file) ?>"
                                        class="w-full h-full object-cover rounded-xl border border-gray-200 shadow-sm group-hover:opacity-90 transition-all">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/10 rounded-xl">
                                        <span class="material-symbols-outlined text-white">zoom_in</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($links)): ?>
                    <div class="pt-6 border-t border-gray-50 mt-6">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">External Resources</p>
                        <div class="space-y-2">
                            <?php foreach ($links as $link): ?>
                                <a href="<?= esc($link) ?>" target="_blank" class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 border border-gray-100 hover:bg-blue-50 hover:border-blue-100 transition-all group">
                                    <span class="material-symbols-outlined text-blue-500 group-hover:scale-110 transition-transform">link</span>
                                    <span class="text-xs font-semibold text-gray-700 truncate"><?= esc($link) ?></span>
                                    <span class="material-symbols-outlined text-[16px] text-gray-300 ml-auto">open_in_new</span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="fiori-card p-0 flex flex-col overflow-hidden">
                
                <div class="fiori-card__header flex justify-between items-center" style="border-bottom:1px solid var(--fiori-border);">
                    <div class="flex flex-col">
                        <span class="fiori-card__title">Conversation History</span>
                        <span class="flex items-center gap-1 text-[10px] font-semibold" style="color:var(--fiori-positive);">
                            <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background:var(--fiori-positive);"></span> Active Preview
                        </span>
                    </div>

                    <a href="<?= base_url('client/chat/' . $ticket['id']) ?>" class="btn btn-accent text-[11px] px-3 h-8">
                        <span class="material-symbols-outlined text-[14px]">open_in_full</span> Open Full Chat
                    </a>
                </div>

                <div id="chat-preview-container" class="p-6 space-y-4 max-h-[450px] overflow-y-auto bg-white scrollbar-hide">
                    <?php if (empty($replies)): ?>
                        <div class="py-10 text-center" id="empty-state">
                            <p class="text-xs text-gray-400 italic">No messages yet. Our team will assist you shortly.</p>
                        </div>
                    <?php else: ?>
                        <?php
                        // Identify current user to align bubbles
                        $currentUserId = session()->get('id') ?? session()->get('user_id');
                        ?>
                        <?php foreach ($replies as $reply): ?>
                            <?php 
                            // Standardized Logic: 
                            // 1. If is_bot is 1 -> Left (Bot)
                            // 2. If user_id is NOT mine -> Left (Staff/TSR)
                            // 3. If user_id IS mine -> Right (You)
                            $isBot = (isset($reply['is_bot']) && $reply['is_bot'] == 1);
                            $isMe = (!$isBot && $reply['user_id'] == $currentUserId); 
                            ?>

                            <div class="flex <?= $isMe ? 'justify-end' : 'justify-start' ?> w-full mb-1">
                                <div class="max-w-[80%] p-3" style="border-radius:4px; <?= $isMe
                                    ? 'background:var(--fiori-blue); color:white; margin-left:auto;'
                                    : 'background:white; border:1px solid var(--fiori-border); color:var(--fiori-text-primary); margin-right:auto;' ?>">

                                    <div class="flex items-center gap-3 mb-1 <?= $isMe ? 'flex-row-reverse' : '' ?>">
                                        <span class="text-[9px] font-black uppercase opacity-60">
                                            <?php 
                                            if ($isBot) {
                                                echo "HRWeb Bot";
                                            } elseif ($isMe) {
                                                echo "You";
                                            } else {
                                                $roleLabel = (isset($reply['role']) && $reply['role'] === 'superadmin') ? 'HRWeb Administrator' : 'Support Agent (TSR)';
                                                echo esc($reply['username'] ?? $roleLabel) . " <span class='opacity-50 inline-block ml-1'>[{$roleLabel}]</span>";
                                            }
                                            ?>
                                        </span>
                                        <span class="text-[8px] opacity-40">
                                            <?= date('H:i', strtotime($reply['created_at'])) ?>
                                        </span>
                                    </div>
                                    <?php if ($isBot): 
                                        $parsedMsg = html_entity_decode($reply['message']);
                                        $parsedMsg = preg_replace_callback('/\[(.*?)\]\((.*?)\)/', function($m) {
                                            $url = trim($m[2]);
                                            if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
                                                $url = "https://" . ltrim($url, '/');
                                            }
                                            return '<a href="' . $url . '" target="_blank" class="text-blue-500 underline font-semibold hover:text-blue-700">' . $m[1] . '</a>';
                                        }, $parsedMsg);
                                    ?>
                                        <div class="text-sm leading-relaxed msg-text pb-2"><?= nl2br($parsedMsg) ?></div>
                                    <?php else: ?>
                                        <p class="text-sm leading-relaxed"><?= nl2br(esc($reply['message'])) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if (strtolower($ticket['status']) !== 'closed'): ?>
                    <div class="p-4" style="background:var(--fiori-surface); border-top:1px solid var(--fiori-border);">
                        <form id="quick-reply-form" action="<?= base_url('client/chat/send/' . $ticket['id']) ?>" method="POST" class="flex gap-2">
                            <?= csrf_field() ?>
                            <input type="text" name="message" id="quick-input" required class="fiori-input w-full" placeholder="Type a quick reply...">
                            <button type="submit" class="btn btn-accent" style="width:40px; padding:0; flex-shrink:0;">
                                <span class="material-symbols-outlined text-[18px]">send</span>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="space-y-4">
            <div class="fiori-card p-6 sticky top-8">
                <h3 class="fiori-card__title mb-4 uppercase tracking-wider text-[11px]">Ticket Metadata</h3>
                <div class="space-y-3 text-xs" style="color:var(--fiori-text-secondary);">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold">Category</span>
                        <span style="color:var(--fiori-blue); font-weight:700; text-transform:uppercase;">
                            <?= !empty($ticket['category']) ? esc($ticket['category']) : 'General' ?>
                            <?php if (!empty($ticket['subcategory'])): ?>
                                <span class="text-gray-400 mx-1">/</span>
                                <span class="text-gray-600 text-[10px]"><?= esc($ticket['subcategory']) ?></span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-semibold">Priority</span>
                        <span style="color:var(--fiori-text-primary); font-weight:600;"><?= esc($ticket['priority']) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-semibold">Staff Assigned</span>
                        <span style="color:var(--fiori-text-primary); font-weight:600;">
                            <?= $ticket['staff_name'] ?? 'Searching for Agent...' ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center pt-3 mt-3 border-t" style="border-color:var(--fiori-border);">
                        <span class="font-semibold">Last Updated</span>
                        <span>
                            <?= date('M d, Y H:i', strtotime($ticket['updated_at'])) ?>
                        </span>
                    </div>
                    <?php if ($ticket['fixed_at']): ?>
                        <div class="flex justify-between items-center pt-3 mt-3 border-t text-emerald-600" style="border-color:var(--fiori-border);">
                            <span class="font-semibold">Closed On</span>
                            <span class="font-bold">
                                <?= date('M d, Y H:i', strtotime($ticket['fixed_at'])) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
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

<div id="photoModal"
    class="hidden fixed inset-0 z-[100] bg-black/90 backdrop-blur-sm flex items-center justify-center p-4 overflow-hidden"
    onclick="closePhotoModal()">
    <button class="absolute top-6 right-6 text-white hover:rotate-90 transition-transform duration-300">
        <span class="material-symbols-outlined text-4xl">close</span>
    </button>
    <img id="modalImage" src=""
        class="max-w-full max-h-full rounded-lg shadow-2xl object-contain transform scale-95 transition-transform duration-300">
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
    // 1. Auto-scroll to latest message
    const chatContainer = document.getElementById('chat-preview-container');
    if (chatContainer) chatContainer.scrollTop = chatContainer.scrollHeight;

    // 2. Socket.io Integration (Matching server.js Port 3001)
    const socket = io('http://localhost:3001'); 
    const ticketId = "<?= $ticket['id'] ?>";
    const currentUserId = "<?= session()->get('id') ?? session()->get('user_id') ?>";

    // Join the room for this specific ticket
    socket.emit('join_ticket', ticketId);

    // Listen for incoming messages (Bot or TSR)
    socket.on('new_ticket_message', function(data) {
        if (data.ticket_id == ticketId) {
            appendMessageToUI(data);
        }
    });

    socket.on('global_ticket_change', function(data) {
        // If ticket is updated by TSR/Admin, refresh the sidebar and header dynamically
        fetch(window.location.href).then(r => r.text()).then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            
            // Refresh Sidebar Metadata
            const newSidebar = doc.querySelector('.fiori-card.p-6.sticky.top-8');
            const currentSidebar = document.querySelector('.fiori-card.p-6.sticky.top-8');
            if (newSidebar && currentSidebar) {
                currentSidebar.innerHTML = newSidebar.innerHTML;
            }

            // Refresh Header Actions & Badges
            const newHeaderActions = doc.querySelector('.flex.items-center.gap-3');
            const currentHeaderActions = document.querySelector('.flex.items-center.gap-3');
            if (newHeaderActions && currentHeaderActions) {
                currentHeaderActions.innerHTML = newHeaderActions.innerHTML;
            }
        });
    });

    /**
     * appendMessageToUI
     * Adds a new message bubble to the preview without refreshing the page.
     */
    function appendMessageToUI(data) {
        const container = document.getElementById('chat-preview-container');
        const emptyHint = document.getElementById('empty-state');
        if (emptyHint) emptyHint.remove();

        // Determine if message is from me or others
        const isMe = (data.sender_id == currentUserId && !data.is_bot);
        
        let parsedMsg = data.message;
        if (data.is_bot) {
            parsedMsg = parsedMsg.replace(/\[(.*?)\]\((.*?)\)/g, (match, p1, p2) => {
                let url = p2.trim();
                if (!/^https?:\/\//i.test(url)) {
                    url = 'https://' + url.replace(/^\/+/, '');
                }
                return '<a href="' + url + '" target="_blank" class="text-blue-500 underline font-semibold hover:text-blue-700 w-full truncate inline-block">' + p1 + '</a>';
            });
        }

        const bubbleHtml = `
            <div class="flex ${isMe ? 'justify-end' : 'justify-start'} w-full mb-1">
                <div class="max-w-[80%] ${isMe ? 'bg-[#1e72af] text-white ml-auto rounded-l-xl rounded-tr-xl' : 'bg-gray-100 text-gray-800 mr-auto rounded-r-xl rounded-tl-xl'} p-4 shadow-sm">
                    </div>
                    ${data.is_bot ? 
                        `<div class="text-sm leading-relaxed msg-text pb-2">${parsedMsg}</div>` : 
                        `<p class="text-sm leading-relaxed">${data.message.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\n/g, "<br>")}</p>`
                    }
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', bubbleHtml);
        container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
    }

    // Modal Controls
    function openPhotoModal(src) {
        const modal = document.getElementById('photoModal');
        const img = document.getElementById('modalImage');
        img.src = src;
        modal.classList.remove('hidden');
        setTimeout(() => img.classList.remove('scale-95'), 10);
    }
    function closePhotoModal() {
        const modal = document.getElementById('photoModal');
        const img = document.getElementById('modalImage');
        img.classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

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

    // Event listener for dynamically viewing bot images in the modal
    document.addEventListener('click', function(e) {
        if (e.target && e.target.tagName === 'IMG' && e.target.closest('.msg-text')) {
            openPhotoModal(e.target.src);
        }
    });
</script>
<?= $this->endSection() ?>