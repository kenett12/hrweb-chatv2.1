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
    <div class="mb-6">
        <a href="<?= base_url('client/tickets') ?>"
            class="text-sm text-gray-500 hover:text-[#1e72af] flex items-center gap-2 transition-colors font-bold">
            <span class="material-symbols-outlined text-sm">arrow_back</span> Back to My Tickets
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 block">
                            <?= $ticket['ticket_number'] ?>
                        </span>
                        <h1 class="text-2xl font-bold text-gray-900 leading-tight">
                            <?= esc($ticket['subject']) ?>
                        </h1>
                    </div>
                    <span class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-[#1e72af] border border-blue-100">
                        <?= esc($ticket['status']) ?>
                    </span>
                </div>

                <p class="text-gray-600 leading-relaxed mb-8 text-[15px]">
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

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col overflow-hidden">
                
                <div class="bg-gray-50/50 p-4 border-b border-gray-100 flex justify-between items-center">
                    <div class="flex flex-col">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Conversation History</p>
                        <span class="flex items-center gap-1 text-[10px] font-bold text-green-600">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> Active Preview
                        </span>
                    </div>

                    <a href="<?= base_url('client/chat/' . $ticket['id']) ?>"
    class="flex items-center gap-2 bg-[#1e72af] text-white px-4 py-2 rounded-xl text-[11px] font-black uppercase tracking-tighter hover:bg-[#165a8a] transition-all shadow-md shadow-blue-100">
    <span class="material-symbols-outlined text-sm">open_in_full</span>
    Open Full Chat
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
                                <div class="max-w-[80%] <?= $isMe
                                    ? 'bg-[#1e72af] text-white rounded-l-xl rounded-tr-xl ml-auto'
                                    : 'bg-gray-100 text-gray-800 rounded-r-xl rounded-tl-xl mr-auto' ?> p-4 shadow-sm">

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
                    <div class="p-4 bg-gray-50/30 border-t border-gray-100">
                        <form id="quick-reply-form" action="<?= base_url('client/chat/send/' . $ticket['id']) ?>" method="POST" class="flex gap-2">
                            <?= csrf_field() ?>
                            <input type="text" name="message" id="quick-input" required
                                class="flex-1 bg-white border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 outline-none transition-all"
                                placeholder="Type a quick reply...">
                            <button type="submit" class="bg-[#1e72af] text-white p-2 rounded-xl hover:bg-[#165a8a] transition-all">
                                <span class="material-symbols-outlined">send</span>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm sticky top-8">
                <h3 class="font-bold text-gray-900 mb-4 text-sm uppercase tracking-wider">Ticket Metadata</h3>
                <div class="space-y-4 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-medium">Category</span>
                        <span class="text-[#1e72af] font-black uppercase tracking-wider">
                            <?= !empty($ticket['category']) ? esc($ticket['category']) : 'General' ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-medium">Priority</span>
                        <span class="text-gray-700 font-bold"><?= esc($ticket['priority']) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-medium">Staff Assigned</span>
                        <span class="text-gray-700 font-bold">
                            <?= $ticket['staff_name'] ?? 'Searching for Agent...' ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center pt-4 border-t border-gray-50">
                        <span class="text-gray-400 font-medium">Last Updated</span>
                        <span class="text-gray-500 italic">
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
                    <div class="flex items-center gap-3 mb-1 ${isMe ? 'flex-row-reverse' : ''}">
                        <span class="text-[9px] font-black uppercase opacity-60">
                            ${data.is_bot ? 'HRWeb Bot' : (isMe ? 'You' : data.sender_name)}
                        </span>
                        <span class="text-[8px] opacity-40">Just now</span>
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