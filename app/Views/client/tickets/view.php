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
                    <?php
                        if ($ticket['status'] === 'Resolved') echo '<span class="fiori-status fiori-status--positive">Resolved</span>';
                        elseif ($ticket['status'] === 'In Progress') echo '<span class="fiori-status fiori-status--information">In Progress</span>';
                        elseif ($ticket['status'] === 'Closed') echo '<span class="fiori-status" style="background:#e0e0e0; color:#606060;">Closed</span>';
                        else echo '<span class="fiori-status fiori-status--warning">' . esc($ticket['status']) . '</span>';
                    ?>
                </div>

                <p class="text-sm leading-relaxed mb-6" style="color:var(--fiori-text-primary);">
                    <?= nl2br(esc($ticket['description'])) ?>
                </p>

                <?php if (!empty($ticket['attachment'])): ?>
                    <div class="pt-6 border-t border-gray-50">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Attached Evidence</p>
                        <div class="relative w-40 h-40 group cursor-zoom-in"
                            onclick="openPhotoModal('<?= base_url('uploads/tickets/' . $ticket['attachment']) ?>')">
                            <img src="<?= base_url('uploads/tickets/' . $ticket['attachment']) ?>"
                                class="w-full h-full object-cover rounded-xl border border-gray-200 shadow-sm group-hover:opacity-90 transition-all">
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/10 rounded-xl">
                                <span class="material-symbols-outlined text-white">zoom_in</span>
                            </div>
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
                                    <?php if ($isBot): ?>
                                        <div class="text-sm leading-relaxed msg-text pb-2"><?= $reply['message'] ?></div>
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
                </div>
            </div>
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
        
        const bubbleHtml = `
            <div class="flex ${isMe ? 'justify-end' : 'justify-start'} w-full mb-1">
                <div class="max-w-[80%] ${isMe ? 'bg-[#1e72af] text-white ml-auto rounded-l-xl rounded-tr-xl' : 'bg-gray-100 text-gray-800 mr-auto rounded-r-xl rounded-tl-xl'} p-4 shadow-sm">
                    </div>
                    ${data.is_bot ? 
                        `<div class="text-sm leading-relaxed msg-text pb-2">${data.message}</div>` : 
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

    // Event listener for dynamically viewing bot images in the modal
    document.addEventListener('click', function(e) {
        if (e.target && e.target.tagName === 'IMG' && e.target.closest('.msg-text')) {
            openPhotoModal(e.target.src);
        }
    });
</script>
<?= $this->endSection() ?>