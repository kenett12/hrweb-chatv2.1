<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/client/chat-ui.css') ?>">
<style>
    /* Block standard browser image dragging to prioritize panning */
    img {
        -webkit-user-drag: none !important;
        user-drag: none !important;
        user-select: none !important;
        -webkit-user-select: none !important;
    }

    .msg-text img { cursor: zoom-in; transition: transform 0.2s ease; }

    /* Solid Viber-Style Modal Background */
    #image-modal { 
        display: none; 
        overflow: hidden; 
        background-color: #0f172a; 
        touch-action: none;
        user-select: none;
    }

    #modal-img-wrapper {
        width: 100vw;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: grab;
        position: relative;
        z-index: 10;
    }
    
    #modal-img-wrapper:active { cursor: grabbing; }

    #modal-img { 
        transition: transform 0.1s ease-out; 
        transform-origin: center;
        max-width: 90%;
        max-height: 85vh;
        pointer-events: none; 
        box-shadow: 0 0 100px rgba(0, 0, 0, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    #pan-shield {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        z-index: 20;
    }

    .glass-tool {
        background: rgba(30, 41, 59, 0.85);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="h-[calc(100vh-140px)] flex gap-4 max-w-7xl mx-auto">

    <div class="flex-1 flex flex-col min-w-0 fiori-card" style="padding:0;">

        <div class="fiori-card__header flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white shadow-md cursor-pointer hover:opacity-90 transition-opacity" style="background:var(--fiori-blue);" onclick="window.location.href='<?= base_url('client/chat') ?>'" title="Back to Directory">
                        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    </div>
                    <span id="connection-dot-main" class="absolute bottom-0 right-0 h-3 w-3 bg-gray-400 rounded-full border-2 border-white transition-colors duration-300"></span>
                </div>
                <div>
                    <h1 class="fiori-card__title leading-tight">HRWeb Support</h1>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span id="connection-dot" class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                        <span id="connection-label" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Connecting...</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div class="hidden md:flex flex-col items-end mr-4">
                    <span class="text-[10px] font-black text-gray-300 uppercase tracking-tighter">Current Session</span>
                    <span class="text-[11px] font-bold" style="color:var(--fiori-blue);"><?= !empty($active_ticket) ? esc($active_ticket['ticket_number']) : 'No active ticket' ?></span>
                </div>
            </div>
        </div>

        <div id="chat-box" class="flex-1 overflow-y-auto scrollbar-hide p-6 flex flex-col gap-0.5" style="background:var(--fiori-page-bg);">

            <?php
            $lastSenderId = null;
            $currentUserId = session()->get('id') ?? session()->get('user_id');

            foreach ($history as $i => $msg):
                $isBot = (isset($msg['is_bot']) && $msg['is_bot'] == 1);
                $staffRoles = ['superadmin', 'tsr', 'tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it'];
                $isStaff = in_array($msg['role'] ?? '', $staffRoles);
                $isSuper = in_array($msg['role'] ?? '', ['admin', 'superadmin']);
                $isMe = (!$isBot && $msg['user_id'] == $currentUserId);

                // Standard Chat POV: My messages on the RIGHT, their messages on the LEFT
                $isRight = $isMe; 
                
                $senderKey = $isBot ? 'bot' : $msg['user_id'];
                $isNewGroup = ($lastSenderId !== $senderKey);
                $lastSenderId = $senderKey;
                
                // Determine Avatar Colors based strictly on role, not alignment
                $avatarBgClass = 'bg-white border border-[#ecfdf5] text-[#10b981]'; // Client
                if ($isSuper) {
                    $avatarBgClass = 'bg-[#e0e7ff] text-[#4f46e5] border border-[#c7d2fe]';
                } elseif ($isStaff || $isBot) {
                    $avatarBgClass = 'bg-[#fef3c7] text-[#d97706] border border-[#fde68a]';
                }
                ?>

                <div class="msg-row <?= $isRight ? 'msg-row--right' : '' ?> <?= $isNewGroup ? 'mt-6' : 'mt-1' ?>" style="max-width:85%;">
                    <div class="msg-avatar shadow-sm <?= !$isNewGroup ? 'invisible' : '' ?> <?= $avatarBgClass ?>">
                        <?php if($isBot): ?> 
                            <span class="material-symbols-outlined text-[20px]">robot_2</span>
                        <?php elseif($isSuper): ?> 
                            <span class="material-symbols-outlined text-[20px]">shield_person</span>
                        <?php elseif($isStaff): ?> 
                            <span class="material-symbols-outlined text-[20px]">support_agent</span>
                        <?php else: ?> 
                            <span class="material-symbols-outlined text-[20px]">person</span>
                        <?php endif; ?>
                    </div>

                    <?php 
                        $msgClass = $isSuper ? 'msg-superadmin' : ($isStaff ? 'msg-staff' : 'msg-client');
                        if($isBot) $msgClass = 'msg-bot';
                    ?>
                    <div class="msg-content <?= $msgClass ?> shadow-sm">
                        <?php if ($isNewGroup): ?>
                            <div class="flex items-center justify-between mb-2 <?= $isRight ? 'flex-row-reverse' : '' ?>">
                                <span class="text-[10px] font-bold uppercase tracking-widest px-1 <?= $isSuper ? 'text-indigo-600' : ($isStaff ? 'text-amber-600' : 'text-emerald-600') ?>" style="font-family:'Inter',sans-serif">
                                    <?php 
                                        if ($isBot) {
                                            echo 'HRWeb Bot <span class="opacity-50 inline-block ml-1">[Automated]</span>';
                                        } elseif ($isStaff) {
                                            $roleLabel = ($msg['role'] === 'superadmin') ? 'Administrator' : 'Support Team';
                                            echo esc($msg['username'] ?? $roleLabel) . " <span class='opacity-50 inline-block ml-1'>[{$roleLabel}]</span>";
                                        } else {
                                            echo ($isMe ? 'You' : esc($msg['username'] ?? 'User'));
                                        }
                                    ?>
                                </span>
                                <span class="text-[9px]" style="color:var(--fiori-text-muted, #89919a);"><?= date('h:i A', strtotime($msg['created_at'])) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="msg-text text-sm" <?= $isBot ? 'onclick="handleImageClick(event)"' : '' ?> style="color:var(--fiori-text-base, #1d2d3e);">
                            <?php if($isBot): ?>
                                <?= nl2br(html_entity_decode($msg['message'])) ?>
                            <?php else: ?>
                                <?= nl2br(esc($msg['message'])) ?>
                            <?php endif; ?>
                        </div>

                        <?php 
                            $attachments = !empty($msg['attachments']) ? json_decode($msg['attachments'], true) : [];
                            $links = !empty($msg['external_links']) ? json_decode($msg['external_links'], true) : [];
                        ?>

                        <?php if (!empty($attachments)): ?>
                            <div class="mt-3 pt-3 border-t border-gray-100/50">
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($attachments as $file): ?>
                                        <div class="relative group w-20 h-20">
                                            <img src="<?= base_url('uploads/tickets/' . $file) ?>" 
                                                 class="w-full h-full object-cover rounded-lg shadow-sm cursor-zoom-in border border-gray-100/50" 
                                                 onclick="handleImageClick({target: this})">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($links)): ?>
                            <div class="mt-2 space-y-1">
                                <?php foreach ($links as $link): ?>
                                    <a href="<?= esc($link) ?>" target="_blank" class="flex items-center gap-2 p-1.5 rounded bg-white/50 border border-gray-100/30 hover:bg-white transition-colors group">
                                        <span class="material-symbols-outlined text-[14px] text-blue-500">link</span>
                                        <span class="text-[10px] font-medium text-gray-600 truncate flex-1"><?= esc($link) ?></span>
                                        <span class="material-symbols-outlined text-[12px] text-gray-300">open_in_new</span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if(!$isNewGroup): ?>
                            <div class="text-[9px] mt-1 <?= $isRight ? 'text-left' : 'text-right' ?>" style="color:var(--fiori-text-muted, #89919a);"><?= date('h:i A', strtotime($msg['created_at'])) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div id="typing-indicator" class="hidden msg-row mt-6" style="max-width:85%;">
                <div class="msg-avatar shadow-sm bg-[#fef3c7] text-[#d97706] border border-[#fde68a]">
                    <span id="typing-icon" class="material-symbols-outlined text-[20px]">support_agent</span>
                </div>
                <div class="msg-content msg-staff shadow-sm" id="typing-bubble">
                    <div class="flex items-center justify-between mb-2">
                        <span id="typing-name" class="text-[10px] font-bold uppercase tracking-widest px-1 text-amber-600">Support is typing...</span>
                    </div>
                    <div class="msg-text px-2 py-1">
                        <div class="flex gap-1 items-center h-2">
                            <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-bounce"></span>
                            <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                            <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-bounce [animation-delay:0.4s]"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="reply-extras" class="hidden px-5 py-3 border-t border-gray-50 bg-gray-50/50">
            <div class="flex flex-col gap-3">
                <!-- Attachments Preview -->
                <div id="reply-attachments-preview" class="flex flex-wrap gap-2"></div>
                
                <!-- External Links List -->
                <div id="reply-links-container" class="space-y-2"></div>
                
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('reply-attachments').click()" class="text-[10px] font-bold text-blue-600 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 hover:bg-blue-100 transition-colors flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[14px]">add_photo_alternate</span> Add Photos
                    </button>
                    <button type="button" onclick="addReplyLinkField()" class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100 hover:bg-emerald-100 transition-colors flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[14px]">link</span> Add Link
                    </button>
                </div>
                <input type="file" id="reply-attachments" name="attachments[]" multiple class="hidden" accept="image/*" onchange="previewReplyAttachments(this)">
            </div>
        </div>

        <div class="shrink-0 px-5 py-4 bg-white border-t border-gray-50">
            <div class="flex items-center gap-2">
                <button type="button" onclick="toggleReplyExtras()" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all" title="Add attachments or links">
                    <span class="material-symbols-outlined" id="extras-toggle-icon">add_circle</span>
                </button>
                <form id="chat-form" class="flex-1 flex items-center gap-2" style="background:#f4f6f9; border:1.5px solid #e8edf3; border-radius:1.25rem; padding:6px 6px 6px 12px;">
                    <textarea id="user-input" class="flex-1 bg-transparent text-sm resize-none scrollbar-hide focus:outline-none border-0" 
                        style="min-height:36px; max-height:120px; padding:6px 4px; line-height:1.6;" 
                        placeholder="Type a message…" rows="1"></textarea>
                    <button type="submit" class="chat-action-btn chat-btn-send shrink-0"><i class="fas fa-paper-plane text-xs"></i></button>
                </form>
            </div>
        </div>
    </div>

    <aside class="w-80 flex flex-col fiori-card shrink-0 overflow-hidden" style="padding:0;">
        <div class="fiori-card__header flex-col items-start gap-4">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full" style="background:var(--fiori-blue);"></div>
                <h2 class="fiori-card__title uppercase tracking-widest text-[11px]">Knowledge Base</h2>
            </div>
            
            <div class="relative group">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] transition-colors duration-300 group-focus-within:text-clr-blue"></i>
                <input id="kb-search" type="text" autocomplete="off"
                       class="w-full pl-9 pr-4 py-3 text-xs font-medium text-gray-700 placeholder-gray-400 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:outline-none focus:border-clr-blue focus:ring-4 focus:ring-clr-blue/10 transition-all shadow-sm" 
                       placeholder="Search tutorials...">
            </div>
        </div>
        
        <div id="kb-list" class="flex-1 overflow-y-auto p-5 space-y-3 scrollbar-hide relative">
            <?php if (!empty($quick_tips)): foreach ($quick_tips as $tip): ?>
                <div class="fiori-card p-4 hover:shadow-md transition-shadow cursor-pointer group" onclick="sendQuickQuery('<?= addslashes($tip['question']) ?>')">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded shrink-0 flex items-center justify-center border transition-colors" style="background:var(--fiori-surface); border-color:var(--fiori-border); color:var(--fiori-text-muted);">
                            <span class="material-symbols-outlined text-[16px]">lightbulb</span>
                        </div>
                        <div class="flex-1 mt-0.5">
                            <h4 class="kb-question text-[11px] font-bold leading-tight transition-colors" style="color:var(--fiori-text-primary);"><?= esc($tip['question']) ?></h4>
                            <p class="text-[9px] font-semibold mt-1.5 uppercase tracking-tighter" style="color:var(--fiori-text-secondary);">Click to ask bot</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <div class="text-center py-10">
                    <div class="mb-3 text-gray-200"><i class="fas fa-book-open text-4xl"></i></div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">No guides found</p>
                </div>
            <?php endif; ?>
            
            <div id="kb-empty-state" class="hidden text-center py-10 absolute inset-0 bg-white">
                <div class="mb-3 text-gray-200"><i class="fas fa-search text-3xl"></i></div>
                <p class="text-[10px] font-bold text-gray-400 uppercase">No matches found</p>
            </div>
        </div>

        <?php if (empty($active_ticket['assigned_to'])): ?>
        <div class="shrink-0 p-4 border-t" style="border-color:var(--fiori-border);">
            <button onclick="requestHumanAgent()" class="fiori-btn btn-outline w-full flex items-center justify-center gap-2" id="btn-request-agent">
                <span class="material-symbols-outlined text-[16px]">support_agent</span>
                Talk to Agent
            </button>
        </div>
        <?php endif; ?>

    </aside>

    <div id="image-modal" class="fixed inset-0 z-[999]">
        <div class="fixed top-0 left-0 right-0 p-6 flex justify-between items-center z-[1001] pointer-events-none">
            <div class="flex items-center gap-4 pointer-events-auto">
                <div class="w-10 h-10 rounded-full bg-clr-blue flex items-center justify-center text-white shadow-xl">
                    <i class="fas fa-robot"></i>
                </div>
                <span class="text-white font-black text-sm tracking-tight drop-shadow-md">Instructional Preview</span>
            </div>
            <div class="flex items-center gap-2 pointer-events-auto">
                <button onclick="downloadImg(event)" class="glass-tool w-12 h-12 rounded-full text-white hover:bg-white hover:text-slate-900 transition-all shadow-lg">
                    <i class="fas fa-download text-sm"></i>
                </button>
                <button onclick="closeImage()" class="bg-red-500/20 border border-red-500/40 w-12 h-12 rounded-full text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-lg">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>
        <div class="fixed bottom-10 left-1/2 -translate-x-1/2 flex items-center glass-tool p-2 rounded-[2.5rem] z-[1001] shadow-2xl" onclick="event.stopPropagation()">
            <button onclick="zoomBtn(-0.5)" class="w-12 h-12 rounded-full text-white hover:bg-white/10 transition-colors"><i class="fas fa-minus"></i></button>
            <div class="h-6 w-px bg-white/10 mx-2"></div>
            <button onclick="resetZoom()" class="px-6 text-white text-[10px] font-black uppercase tracking-[0.3em] hover:text-clr-blue transition-colors">Reset <span id="zoom-lvl" class="ml-2 text-clr-blue">100%</span></button>
            <div class="h-6 w-px bg-white/10 mx-2"></div>
            <button onclick="zoomBtn(0.5)" class="w-12 h-12 rounded-full text-white hover:bg-white/10 transition-colors"><i class="fas fa-plus"></i></button>
        </div>
        <div id="modal-img-wrapper">
            <div id="pan-shield" onmousedown="startPan(event)" onmousemove="doPan(event)" onmouseup="endPan()" onmouseleave="endPan()" onclick="handleShieldClick(event)"></div>
            <img id="modal-img" src="" draggable="false" class="rounded-2xl">
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
    <script>
        const activeTicketId = "<?= $active_ticket['id'] ?? '' ?>";
        const BASE_URL       = "<?= base_url() ?>";
        const CSRF_NAME      = "<?= csrf_token() ?>";
        const CSRF_TOKEN     = "<?= csrf_hash() ?>";

        document.addEventListener('DOMContentLoaded', () => {
            const chatBox = document.getElementById('chat-box');
            const chatForm = document.getElementById('chat-form');
            const ui = chatForm.querySelector('#user-input');

            // Status Indicator Elements
            const dot = document.getElementById('connection-dot');
            const dotMain = document.getElementById('connection-dot-main');
            const label = document.getElementById('connection-label');
            const typingIndicator = document.getElementById('typing-indicator');
            const typingName = document.getElementById('typing-name');

            // Initialize Socket.io Connection
            const socket = io('http://localhost:3001');

            socket.on('connect', () => {
                dot.className = "w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block shadow-[0_0_5px_rgba(16,185,129,0.5)]";
                dotMain.className = "absolute bottom-0 right-0 h-3 w-3 bg-emerald-500 rounded-full border-2 border-white animate-pulse";
                label.innerText = "Connected to chat server";
                label.className = "text-[10px] font-bold text-emerald-500 uppercase tracking-widest";
                if(activeTicketId) socket.emit('join_ticket', activeTicketId);
            });

            socket.on('disconnect', () => {
                dot.className = "w-1.5 h-1.5 rounded-full bg-red-500 inline-block";
                dotMain.className = "absolute bottom-0 right-0 h-3 w-3 bg-red-500 rounded-full border-2 border-white";
                label.innerText = "Offline";
                label.className = "text-[10px] font-bold text-red-500 uppercase tracking-widest";
            });

            // Typing Logic
            let typingTimer;
            socket.on('ticket_typing', (data) => {
                if (data.ticket_id == activeTicketId && data.is_typing) {
                    typingName.innerText = `${data.username} is typing...`;
                    typingIndicator.classList.remove('hidden');
                    chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
                } else {
                    typingIndicator.classList.add('hidden');
                }
            });

            // Incoming Chat Event Listener (For TSR/Admin Live Replies)
            socket.on('new_ticket_message', (data) => {
                if (data.ticket_id == activeTicketId) {
                    const currentUserId = "<?= session()->get('id') ?? session()->get('user_id') ?>";
                    const isMe = (data.sender_id == currentUserId && !data.is_bot);
                    
                    // Ignore messages we already appended manually (our own messages or the direct bot response)
                    if (isMe || data.is_bot) return;

                    // Trigger OS Notification if permitted
                    if ("Notification" in window) {
                        if (Notification.permission === "granted") {
                            const title = data.is_bot ? "HRWeb Bot" : (data.sender_name || "New Message");
                            new Notification(title, { body: data.message.replace(/<[^>]*>?/gm, ''), icon: '<?= base_url("assets/img/logo-icon.png") ?>' });
                        } else if (Notification.permission !== "denied") {
                            Notification.requestPermission();
                        }
                    }

                    // Always trigger the in-app toast for visibility
                    if (window.showToast) {
                        const title = data.is_bot ? "HRWeb Bot" : (data.sender_name || "New Message");
                        window.showToast("New Chat Activity", title + " just sent you a message.", null);
                    }

                    window._appendMessage('tsr', data.message, null, data.sender_name, data.time, data.sender_role || 'tsr', data.attachments || [], data.external_links || []);
                }
            });

            ui.addEventListener('input', () => {
                clearTimeout(typingTimer);
                socket.emit('ticket_typing', { ticket_id: activeTicketId, username: 'Client', is_typing: true });
                typingTimer = setTimeout(() => {
                    socket.emit('ticket_typing', { ticket_id: activeTicketId, is_typing: false });
                }, 3000);
            });

            // KB Search filter logic
            document.getElementById('kb-search').addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                let hasVisibleCards = false;
                
                document.querySelectorAll('.kb-guide-card').forEach(card => {
                    const question = card.querySelector('.kb-question').innerText.toLowerCase();
                    if (question.includes(term)) {
                        card.style.display = 'block';
                        hasVisibleCards = true;
                    } else {
                        card.style.display = 'none';
                    }
                });

                const emptyState = document.getElementById('kb-empty-state');
                if (emptyState) {
                    emptyState.style.display = hasVisibleCards ? 'none' : 'block';
                }
            });

            ui.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    chatForm.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }
            });

            window.sendQuickQuery = (question) => {
                ui.value = question;
                chatForm.dataset.isQuickQuery = 'true';
                chatForm.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
            };

            window.toggleReplyExtras = () => {
                const extras = document.getElementById('reply-extras');
                const icon = document.getElementById('extras-toggle-icon');
                extras.classList.toggle('hidden');
                icon.innerText = extras.classList.contains('hidden') ? 'add_circle' : 'cancel';
                icon.style.color = extras.classList.contains('hidden') ? '' : '#ef4444';
            };

            window.addReplyLinkField = () => {
                const container = document.getElementById('reply-links-container');
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2 group animate-in fade-in slide-in-from-left-2 duration-300';
                div.innerHTML = `
                    <div class="flex-1 relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-emerald-500 text-[14px]">link</span>
                        <input type="url" name="external_links[]" placeholder="https://..." 
                            class="w-full pl-9 pr-4 py-2 bg-white border border-emerald-100 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-50 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                `;
                container.appendChild(div);
            };

            window.previewReplyAttachments = (input) => {
                const preview = document.getElementById('reply-attachments-preview');
                preview.innerHTML = '';
                if (input.files) {
                    Array.from(input.files).forEach(file => {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            const div = document.createElement('div');
                            div.className = 'relative group w-12 h-12';
                            div.innerHTML = `
                                <img src="${e.target.result}" class="w-full h-full object-cover rounded-lg border border-blue-100 shadow-sm">
                                <span class="absolute -top-1 -right-1 bg-blue-500 text-white rounded-full p-0.5 shadow-md flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[10px]">check</span>
                                </span>
                            `;
                            preview.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    });
                }
            };

            // ── NEW: Feedback Submission Logic ──
            window.sendBotFeedback = async (articleId, isHelpful, btnElement) => {
                // Change UI immediately to show feedback was received
                const container = btnElement.closest('.feedback-container');
                container.innerHTML = `<span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest pl-1"><i class="fas fa-check-circle text-emerald-500 mr-1"></i> Feedback saved</span>`;
                
                const fd = new FormData();
                fd.append('ticket_id', activeTicketId);
                fd.append('article_id', articleId);
                fd.append('is_helpful', isHelpful);
                fd.append(CSRF_NAME, CSRF_TOKEN);

                try {
                    await fetch(`${BASE_URL}/client/chat/submitFeedback`, {
                        method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                } catch(e) { console.error("Feedback failed", e); }
            };

            // ── UPGRADED: _appendMessage now accepts articleId, time, attachments, and links ──
            window._appendMessage = (sender, text, articleId = null, senderName = null, time = null, role = 'user', attachments = [], links = []) => {
                const isMe = sender === 'user';
                const isBot = sender === 'bot';
                const isSuper = role === 'superadmin' || role === 'admin';
                const isStaff = ['admin', 'superadmin', 'tsr', 'tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it'].includes(role);
                
                const isRight = isMe;
                
                const wrapper = document.createElement('div');
                wrapper.className = `msg-row ${isRight ? 'msg-row--right' : ''} mt-6`;
                wrapper.style.maxWidth = '85%';
                
                const safeText = isMe 
                    ? document.createTextNode(text || "").textContent.replace(/\n/g, '<br>')
                    : (text || "");

                const displayTime = time || new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                // --- ATTACHMENTS GALLERY ---
                let attachmentsHTML = '';
                if (attachments && attachments.length > 0) {
                    attachmentsHTML = `
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <div class="flex flex-wrap gap-2">
                                ${attachments.map(file => `
                                    <div class="relative group w-20 h-20">
                                        <img src="${BASE_URL}/uploads/tickets/${file}" 
                                             class="w-full h-full object-cover rounded-lg shadow-sm cursor-zoom-in border border-gray-100" 
                                             onclick="handleImageClick({target: this})">
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                }

                // --- EXTERNAL LINKS ---
                let linksHTML = '';
                if (links && links.length > 0) {
                    linksHTML = `
                        <div class="mt-2 space-y-1">
                            ${links.map(link => `
                                <a href="${link}" target="_blank" class="flex items-center gap-2 p-1.5 rounded bg-white/50 border border-gray-100 hover:bg-white transition-colors group">
                                    <span class="material-symbols-outlined text-[14px] text-blue-500">link</span>
                                    <span class="text-[10px] font-medium text-gray-600 truncate flex-1">${link}</span>
                                    <span class="material-symbols-outlined text-[12px] text-gray-300">open_in_new</span>
                                </a>
                            `).join('')}
                        </div>
                    `;
                }

                let feedbackHTML = '';
                if (isBot && articleId) {
                    feedbackHTML = `
                        <div class="feedback-container mt-3 flex items-center gap-2 border-t border-slate-200/60 pt-2.5 pb-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Was this helpful?</span>
                            <button onclick="sendBotFeedback(${articleId}, 1, this)" class="w-6 h-6 rounded-full bg-white border border-slate-200 text-slate-400 hover:bg-emerald-50 hover:text-emerald-500 hover:border-emerald-200 transition-colors flex items-center justify-center shadow-sm" title="Yes"><i class="fas fa-thumbs-up text-[10px]"></i></button>
                            <button onclick="sendBotFeedback(${articleId}, 0, this)" class="w-6 h-6 rounded-full bg-white border border-slate-200 text-slate-400 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-colors flex items-center justify-center shadow-sm" title="No"><i class="fas fa-thumbs-down text-[10px]"></i></button>
                        </div>
                    `;
                }

                let iconName = isBot ? 'robot_2' : (isSuper ? 'shield_person' : (isStaff ? 'support_agent' : 'person'));
                let avatarClass = 'bg-white border border-[#ecfdf5] text-[#10b981]'; // Client
                if (isSuper) avatarClass = 'bg-[#e0e7ff] text-[#4f46e5] border border-[#c7d2fe]';
                else if (isStaff || isBot) avatarClass = 'bg-[#fef3c7] text-[#d97706] border border-[#fde68a]';

                const displayName = senderName ? senderName : (isBot ? 'HRWeb Bot' : (isMe ? 'You' : 'Support Team'));
                const senderColor = isSuper ? 'text-indigo-600' : (isStaff ? 'text-amber-600' : 'text-emerald-600');
                const roleLabel = isBot ? 'Automated' : (isSuper ? 'Administrator' : (isStaff ? 'Support Team' : 'User'));

                let msgClass = 'msg-client';
                if(isBot) msgClass = 'msg-bot';
                else if(isSuper) msgClass = 'msg-superadmin';
                else if(isStaff) msgClass = 'msg-staff';

                wrapper.innerHTML = `
                    <div class="msg-avatar shadow-sm ${avatarClass}">
                        <span class="material-symbols-outlined text-[20px]">${iconName}</span>
                    </div>
                    <div class="msg-content ${msgClass} shadow-sm">
                        <div class="flex items-center justify-between mb-2 ${isRight ? 'flex-row-reverse' : ''}">
                            <span class="text-[10px] font-bold uppercase tracking-widest px-1 ${senderColor}" style="font-family:'Inter',sans-serif">
                                ${displayName} ${!isMe ? '<span class="opacity-50 inline-block ml-1">['+roleLabel+']</span>' : ''}
                            </span>
                            <span class="text-[9px]" style="color:var(--fiori-text-muted, #89919a);">${displayTime}</span>
                        </div>
                        <div class="msg-text text-sm" ${!isMe ? 'onclick="handleImageClick(event)"' : ''} style="color:var(--fiori-text-base, #1d2d3e);">${safeText}</div>
                        ${attachmentsHTML}
                        ${linksHTML}
                        ${feedbackHTML}
                    </div>`;

                chatBox.insertBefore(wrapper, typingIndicator);
                chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
            };

            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const text = ui.value.trim();
                const attachmentInput = document.getElementById('reply-attachments');
                const hasFiles = attachmentInput.files && attachmentInput.files.length > 0;
                const linkInputs = document.querySelectorAll('input[name="external_links[]"]');
                const hasLinks = Array.from(linkInputs).some(input => input.value.trim() !== '');

                if (!text && !hasFiles && !hasLinks) return;
                
                const isQuickQuery = chatForm.dataset.isQuickQuery === 'true';
                chatForm.dataset.isQuickQuery = 'false';

                // Local Preview of our message (simplified for files)
                window._appendMessage('user', text, null, null, null, 'user', [], []); 
                ui.value = '';
                
                const typingNameLabel = document.getElementById('typing-name');
                const typingIcon = document.getElementById('typing-icon');
                const typingBubble = document.getElementById('typing-bubble');
                typingNameLabel.innerText = "HRWeb Bot is typing...";
                typingNameLabel.className = "text-[10px] font-bold uppercase tracking-widest px-1 text-emerald-600";
                typingIcon.innerText = "robot_2";
                typingBubble.className = "msg-content msg-bot shadow-sm";
                typingIndicator.classList.remove('hidden');
                chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
                
                const fd = new FormData();
                fd.append('message', text);
                fd.append('is_quick_query', isQuickQuery ? '1' : '0');
                fd.append(CSRF_NAME, CSRF_TOKEN);

                // Add Attachments
                if (hasFiles) {
                    Array.from(attachmentInput.files).forEach(file => {
                        fd.append('attachments[]', file);
                    });
                }

                // Add Links
                linkInputs.forEach(input => {
                    if (input.value.trim() !== '') {
                        fd.append('external_links[]', input.value.trim());
                    }
                });
                
                try {
                    const res = await fetch(`${BASE_URL}/client/chat/handleBotQuery/${activeTicketId}`, {
                        method: 'POST',
                        body: fd,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    
                    if (!data.bypassed_bot) {
                        window._appendMessage('bot', data.reply, data.article_id, null, data.time);
                    } else {
                        // If it was a human message, we might want to refresh slightly or just wait for socket,
                        // but since we optimistically appended, we just clear the extra inputs
                        document.getElementById('reply-attachments-preview').innerHTML = '';
                        document.getElementById('reply-links-container').innerHTML = '';
                        attachmentInput.value = '';
                        if (!document.getElementById('reply-extras').classList.contains('hidden')) {
                            toggleReplyExtras();
                        }
                    }
                } catch (err) { 
                    console.error("Chat Error:", err); 
                } finally {
                    typingIndicator.classList.add('hidden');
                }
            });

            chatBox.scrollTo({ top: chatBox.scrollHeight });
        });

        // ── NEW: Request Agent Logic ──
        window.requestHumanAgent = async () => {
             const btn = document.getElementById('btn-request-agent');
             if(btn) btn.disabled = true;

             const fd = new FormData();
             fd.append(CSRF_NAME, CSRF_TOKEN);

             try {
                 const res = await fetch(`${BASE_URL}/client/chat/requestAgent/${activeTicketId}`, {
                     method: 'POST',
                     body: fd,
                     headers: { 'X-Requested-With': 'XMLHttpRequest' }
                 });
                 const data = await res.json();
                 
                 if (data.status === 'success') {
                     // Optimistically insert user message locally
                     window._appendMessage('user', 'I would like to speak to a human agent, please.', null, null, data.time);
                     if(btn) btn.style.display = 'none'; 
                 } else {
                     alert(data.msg || 'There was an issue processing your request.');
                     if(btn) btn.disabled = false;
                 }
             } catch(err) {
                 console.error(err);
                 if(btn) btn.disabled = false;
             }
        };

        // ── VIEWER LOGIC ──
        let scale = 1, pointX = 0, pointY = 0, startX = 0, startY = 0, isPanning = false;

        function handleImageClick(e) {
            if (e.target.tagName === 'IMG') {
                const modal = document.getElementById('image-modal');
                const modalImg = document.getElementById('modal-img');
                resetZoom();
                modalImg.src = e.target.src;
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
        }
        function handleShieldClick(e) { if (scale === 1 && !isPanning) closeImage(); }
        function startPan(e) { if (scale === 1) return; isPanning = true; startX = e.clientX - pointX; startY = e.clientY - pointY; }
        function doPan(e) { if (!isPanning) return; e.preventDefault(); pointX = e.clientX - startX; pointY = e.clientY - startY; updateViewer(); }
        function endPan() { isPanning = false; }
        function zoomBtn(delta) { scale = Math.min(Math.max(1, scale + delta), 5); if (scale === 1) { pointX = 0; pointY = 0; } updateViewer(); }
        function resetZoom() { scale = 1; pointX = 0; pointY = 0; updateViewer(); }
        function updateViewer() {
            const img = document.getElementById('modal-img');
            const zoomLbl = document.getElementById('zoom-lvl');
            img.style.transform = `translate(${pointX}px, ${pointY}px) scale(${scale})`;
            zoomLbl.innerText = Math.round(scale * 100) + '%';
        }
        function downloadImg(e) {
            e.stopPropagation();
            const link = document.createElement('a');
            link.href = document.getElementById('modal-img').src;
            link.download = 'Instructional_Image.png';
            link.click();
        }
        function closeImage() {
            document.getElementById('image-modal').style.display = 'none';
            document.body.style.overflow = '';
        }
        document.addEventListener('dragstart', (e) => { if (e.target.tagName === 'IMG') e.preventDefault(); });
    </script>
<?= $this->endSection() ?>