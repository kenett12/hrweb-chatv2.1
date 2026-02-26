<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support | HRWeb Inc.</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/client/chat-ui.css') ?>">

    <script>
        const BASE_URL = '<?= rtrim(base_url(), '/') ?>';
        const CSRF_NAME = '<?= csrf_token() ?>';
        const CSRF_TOKEN = '<?= csrf_hash() ?>';
        const activeTicketId = "<?= $active_ticket['id'] ?? '' ?>"; // Direct ticket relationship
        tailwind.config = { theme: { extend: { colors: { "clr-blue": "#1e72af", "clr-cyan": "#3297ca" } } } }
    </script>
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
</head>

<body class="h-screen flex p-4 gap-4 overflow-hidden bg-[#f4f6f9]">

    <aside class="w-16 bg-white border border-gray-100 rounded-[1.5rem] flex flex-col items-center py-6 gap-6 shrink-0 shadow-sm">
        <a href="<?= base_url('client/dashboard') ?>" class="w-10 h-10 bg-[#1e293b] rounded-xl flex items-center justify-center text-white transition-all hover:scale-110 hover:bg-clr-blue">
            <i class="fas fa-th-large text-sm"></i>
        </a>
        <div class="h-px w-6 bg-gray-100"></div>
        <button class="w-10 h-10 bg-blue-50 text-clr-blue rounded-xl flex items-center justify-center">
            <i class="fas fa-comment-dots text-sm"></i>
        </button>
    </aside>

    <main class="flex-1 flex flex-col chat-session-card min-w-0">

        <header class="px-6 py-4 border-b border-gray-50 flex items-center justify-between shrink-0" style="background: white;">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-[#1e72af] flex items-center justify-center text-white shadow-md">
                        <i class="fas fa-robot text-sm"></i>
                    </div>
                    <span id="connection-dot-main" class="absolute bottom-0 right-0 h-3 w-3 bg-gray-400 rounded-full border-2 border-white transition-colors duration-300"></span>
                </div>
                <div>
                    <h1 class="text-[15px] font-bold text-gray-900 leading-tight" style="font-family:'DM Sans',sans-serif">HRWeb Support</h1>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span id="connection-dot" class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                        <span id="connection-label" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Connecting...</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div class="hidden md:flex flex-col items-end mr-4">
                    <span class="text-[10px] font-black text-gray-300 uppercase tracking-tighter">Current Session</span>
                    <span class="text-[11px] font-bold text-clr-blue"><?= !empty($active_ticket) ? esc($active_ticket['ticket_number']) : 'No active ticket' ?></span>
                </div>
            </div>
        </header>

        <div id="chat-box" class="flex-1 overflow-y-auto scrollbar-hide" style="background:#f4f6f9; padding: 1.5rem; display:flex; flex-direction:column; gap:2px;">

            <?php
            $lastSenderId = null;
            $currentUserId = session()->get('id') ?? session()->get('user_id');

            foreach ($history as $i => $msg):
                $isBot = (isset($msg['is_bot']) && $msg['is_bot'] == 1);
                $isMe = (!$isBot && $msg['user_id'] == $currentUserId);
                $senderKey = $isBot ? 'bot' : $msg['user_id'];
                $isNewGroup = ($lastSenderId !== $senderKey);
                $lastSenderId = $senderKey;
                ?>

                <div class="flex items-end gap-2 max-w-[78%] <?= $isMe ? 'ml-auto flex-row-reverse' : '' ?> <?= $isNewGroup ? 'mt-4' : 'mt-0.5' ?>">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-[8px] font-black shrink-0 mb-0.5 <?= ($isBot || !$isMe) ? 'bg-white border border-blue-100 text-clr-blue shadow-sm' : 'bg-[#1e72af] text-white' ?> <?= !$isNewGroup ? 'invisible' : '' ?>">
                        <?php if($isBot): ?> <i class="fas fa-robot text-[9px]"></i> <?php elseif($isMe): ?> YOU <?php else: ?> TSR <?php endif; ?>
                    </div>
                    <div class="flex flex-col gap-0.5 <?= $isMe ? 'items-end' : '' ?>">
                        <?php if ($isNewGroup): ?>
                            <span class="text-[10px] font-semibold text-gray-400 px-1 <?= $isMe ? 'text-right' : '' ?>" style="font-family:'DM Sans',sans-serif">
                                <?php 
                                    if ($isBot) {
                                        echo 'HRWeb Bot';
                                    } elseif ($isMe) {
                                        echo 'You';
                                    } else {
                                        $roleLabel = (isset($msg['role']) && $msg['role'] === 'superadmin') ? 'HRWeb Administrator' : 'Support Agent (TSR)';
                                        echo esc($msg['username'] ?? $roleLabel) . " <span class='opacity-50 inline-block ml-1'>[{$roleLabel}]</span>";
                                    }
                                ?>
                            </span>
                        <?php endif; ?>
                        <div class="msg-bubble <?= ($isBot || !$isMe) ? 'msg-bot' : 'msg-user' ?>">
                            <div class="msg-text" <?= $isBot ? 'onclick="handleImageClick(event)"' : '' ?>>
                                <?php if($isBot): ?>
                                    <?= html_entity_decode($msg['message']) ?>
                                <?php else: ?>
                                    <?= nl2br(esc($msg['message'])) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div id="typing-indicator" class="hidden flex items-end gap-2 mt-4 max-w-[78%]">
                <div class="w-7 h-7 rounded-full bg-white border border-blue-100 flex items-center justify-center text-clr-blue shadow-sm">
                    <i class="fas fa-user-edit text-[9px]"></i>
                </div>
                <div class="flex flex-col gap-0.5">
                    <span id="typing-name" class="text-[10px] font-semibold text-gray-400 px-1">Support is typing...</span>
                    <div class="msg-bubble msg-bot px-4 py-3">
                        <div class="flex gap-1 items-center h-2">
                            <span class="w-1 h-1 bg-gray-400 rounded-full animate-bounce"></span>
                            <span class="w-1 h-1 bg-gray-400 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                            <span class="w-1 h-1 bg-gray-400 rounded-full animate-bounce [animation-delay:0.4s]"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="shrink-0 px-5 py-4 bg-white border-t border-gray-50">
            <form id="chat-form" class="flex items-center gap-2" style="background:#f4f6f9; border:1.5px solid #e8edf3; border-radius:1.25rem; padding:6px 6px 6px 12px;">
                <textarea id="user-input" class="flex-1 bg-transparent text-sm resize-none scrollbar-hide focus:outline-none border-0" 
                    style="min-height:36px; max-height:120px; padding:6px 4px; line-height:1.6;" 
                    placeholder="Type a message…" rows="1"></textarea>
                <button type="submit" class="chat-action-btn chat-btn-send shrink-0"><i class="fas fa-paper-plane text-xs"></i></button>
            </form>
        </div>
    </main>

    <aside class="w-80 flex flex-col bg-white border border-gray-100 rounded-[2rem] shrink-0 overflow-hidden shadow-sm">
        <div class="px-6 py-5 border-b border-gray-50 shrink-0">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-2 h-2 rounded-full bg-[#3297ca]"></div>
                <h2 class="text-[10px] font-black text-gray-800 uppercase tracking-widest">Knowledge Base</h2>
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
                <div class="kb-guide-card p-4 rounded-2xl border border-gray-50 bg-gray-50/50 hover:bg-white hover:border-clr-blue/30 hover:shadow-md transition-all cursor-pointer group" onclick="sendQuickQuery('<?= addslashes($tip['question']) ?>')">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center shadow-sm border border-gray-100 group-hover:bg-clr-blue group-hover:text-white group-hover:border-clr-blue transition-colors">
                            <i class="fas fa-lightbulb text-[10px]"></i>
                        </div>
                        <div class="flex-1 mt-0.5">
                            <h4 class="kb-question text-[11px] font-bold text-gray-800 leading-tight group-hover:text-clr-blue transition-colors"><?= esc($tip['question']) ?></h4>
                            <p class="text-[9px] text-gray-400 font-medium mt-1.5 uppercase tracking-tighter">Click to ask bot</p>
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

    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
    <script>
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

                    window._appendMessage('tsr', data.message, null, data.sender_name);
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

            // ── UPGRADED: _appendMessage now accepts articleId to generate feedback buttons ──
            window._appendMessage = (sender, text, articleId = null, senderName = null) => {
                const isMe = sender === 'user';
                const isBot = sender === 'bot';
                const wrapper = document.createElement('div');
                wrapper.className = `flex items-end gap-2 ${isMe ? 'ml-auto flex-row-reverse' : ''} mt-4 max-w-[78%]`;
                
                const safeText = isMe 
                    ? document.createTextNode(text || "").textContent.replace(/\n/g, '<br>')
                    : (text || "");

                // Build Feedback HTML if the sender is a bot AND an article ID was matched
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

                let iconHtml = isBot ? '<i class="fas fa-robot"></i>' : (isMe ? 'YOU' : 'TSR');
                const displayName = senderName ? senderName : (isBot ? 'HRWeb Bot' : (isMe ? 'You' : 'TSR'));

                wrapper.innerHTML = `
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-[8px] font-black shrink-0 mb-0.5 ${isMe ? 'bg-[#1e72af] text-white' : 'bg-white border text-clr-blue'}">
                        ${iconHtml}
                    </div>
                    <div class="flex flex-col gap-0.5 ${isMe ? 'items-end' : ''}">
                        <span class="text-[10px] font-semibold text-gray-400 px-1 ${isMe ? 'text-right' : ''}" style="font-family:'DM Sans',sans-serif">
                            ${displayName}
                        </span>
                        <div class="msg-bubble ${isMe ? 'msg-user' : 'msg-bot'}">
                            <div class="msg-text" ${!isMe ? 'onclick="handleImageClick(event)"' : ''}>${safeText}</div>
                            ${feedbackHTML}
                        </div>
                    </div>`;

                chatBox.insertBefore(wrapper, typingIndicator);
                chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
            };

            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const text = ui.value.trim();
                if (!text) return;
                
                const isQuickQuery = chatForm.dataset.isQuickQuery === 'true';
                chatForm.dataset.isQuickQuery = 'false';

                window._appendMessage('user', text);
                ui.value = '';
                
                const fd = new FormData();
                fd.append('message', text);
                fd.append('is_quick_query', isQuickQuery ? '1' : '0');
                fd.append(CSRF_NAME, CSRF_TOKEN);
                try {
                    const res = await fetch(`${BASE_URL}/client/chat/handleBotQuery/${activeTicketId}`, {
                        method: 'POST',
                        body: fd,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    
                    if (!data.bypassed_bot) {
                        // Pass data.article_id to append the feedback buttons
                        window._appendMessage('bot', data.reply, data.article_id);
                    }
                } catch (err) { console.error("Chat Error:", err); }
            });

            chatBox.scrollTo({ top: chatBox.scrollHeight });
        });

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
</body>
</html>