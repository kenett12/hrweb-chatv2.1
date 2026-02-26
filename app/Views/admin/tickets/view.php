<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/tsr/tickets.css') ?>?v=<?= time() ?>">
<style>
    .chat-bubble {
        max-width: 80%;
        padding: 1rem 1.25rem;
        border-radius: 1.25rem;
        font-size: 0.95rem;
        line-height: 1.5;
        position: relative;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    
    .chat-bot {
        background: linear-gradient(135deg, #1e72af, #3297ca);
        color: white;
        border-bottom-left-radius: 0.25rem;
    }

    .chat-ticket-description {
        background-color: white;
        color: #1f2937;
        border: 1px solid #f3f4f6;
        border-bottom-left-radius: 0.25rem;
        border-left: 4px solid #10b981;
    }
    
    .chat-client {
        background-color: white;
        color: #1f2937;
        border: 1px solid #f3f4f6;
        border-bottom-right-radius: 0.25rem;
    }
    
    .chat-staff {
        background-color: #f8fafc;
        color: #1f2937;
        border: 1px solid #e2e8f0;
        border-bottom-left-radius: 0.25rem;
        border-left: 4px solid #f59e0b;
    }

    .chat-superadmin {
        background-color: #f4f8ff;
        color: #1f2937;
        border: 1px solid #e0e7ff;
        border-bottom-left-radius: 0.25rem;
        border-left: 4px solid #6366f1;
    }

    .meta-text {
        font-size: 0.75rem;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 0.5rem;
    }

    /* Block standard browser image dragging to prioritize panning */
    .msg-text img { 
        cursor: zoom-in; 
        transition: transform 0.2s ease;
        border-radius: 0.5rem;
        margin-top: 0.5rem;
        -webkit-user-drag: none !important;
        user-drag: none !important;
        user-select: none !important;
        -webkit-user-select: none !important;
    }

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

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
            Ticket #<?= $ticket['id'] ?>: <?= esc($ticket['subject']) ?>
            <span class="badge status-<?= strtolower(str_replace(' ', '_', $ticket['status'])) ?> text-sm px-3 py-1"><?= $ticket['status'] ?></span>
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Client: <strong><?= esc($ticket['client_name']) ?></strong> | 
            Assigned TSR: <strong><?= esc($ticket['staff_name'] ?? 'Unassigned') ?></strong> | 
            Superadmin: <strong><?= esc($ticket['superadmin_name'] ?? 'None') ?></strong>
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?= base_url('superadmin/tickets') ?>" class="btn btn-outline">Back to Queue</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[calc(100vh-140px)]">
    <!-- Thread Panel -->
    <div class="lg:col-span-2 flex flex-col bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">

        <!-- Superadmin Monitoring Details -->
        <div class="bg-indigo-50 text-indigo-700 text-xs font-bold uppercase tracking-widest py-2 text-center relative z-10 w-full shadow-sm border-b border-indigo-100 flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-[14px]">shield_person</span>
            3-Way Group Chat Active
        </div>
        
        <!-- Conversation Area -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6" id="thread-container" style="scroll-behavior: smooth;">
            
            <!-- INITIAL TICKET DESCRIPTION -->
            <div class="flex flex-col items-start ticket-reply">
                <div class="flex items-end gap-3 w-full">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 border border-emerald-100 shadow-sm" style="background-color: #ecfdf5; color: #10b981;">
                        <span class="material-symbols-outlined text-xl">person</span>
                    </div>
                    <div class="chat-bubble chat-ticket-description w-full">
                        <div class="mb-2 text-xs font-bold text-emerald-600 uppercase tracking-wider border-b border-gray-100 pb-2">Initial Request</div>
                        <?= nl2br(esc($ticket['description'])) ?>
                        <?php if(!empty($ticket['attachment'])): ?>
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <a href="<?= base_url('uploads/tickets/' . $ticket['attachment']) ?>" target="_blank" class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 font-semibold bg-blue-50 px-4 py-2 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">attachment</span>
                                    View Attachment
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="meta-text ml-14">
                    <?= esc($ticket['client_name']) ?> (Client) • <?= date('M d, Y h:i A', strtotime($ticket['created_at'])) ?>
                </div>
            </div>

            <!-- REPLIES LOOP -->
            <?php foreach($replies as $reply): ?>
                <?php 
                    $isBot = $reply['is_bot'];
                    $isStaff = ($reply['role'] === 'superadmin' || $reply['role'] === 'tsr');
                ?>

                <?php if($isBot): ?>
                    <!-- BOT REPLY -->
                    <div class="flex flex-col items-start ticket-reply">
                        <div class="flex items-end gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-50 shrink-0 flex items-center justify-center border border-blue-100 p-1.5 shadow-sm">
                                <img src="<?= base_url('assets/img/logo-icon.png') ?>" alt="Bot" class="w-full h-full object-contain mix-blend-multiply">
                            </div>
                            <div class="chat-bubble chat-bot">
                                <div class="msg-text" onclick="handleImageClick(event)">
                                    <?= html_entity_decode($reply['message']) ?>
                                </div>
                            </div>
                        </div>
                        <div class="meta-text ml-14">HRWeb Bot • <?= date('M d, Y h:i A', strtotime($reply['created_at'])) ?></div>
                    </div>

                <?php elseif($isStaff): ?>
                    <!-- STAFF/ADMIN REPLY -->
                    <?php $isSuper = ($reply['role'] === 'superadmin'); ?>
                    <div class="flex flex-col items-start ticket-reply">
                        <div class="flex items-end gap-3 w-full">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 shadow-sm <?= $isSuper ? 'bg-indigo-50 text-indigo-500 border border-indigo-100' : 'bg-amber-50 text-amber-500 border border-amber-100' ?>">
                                <span class="material-symbols-outlined text-xl"><?= $isSuper ? 'shield_person' : 'support_agent' ?></span>
                            </div>
                            <div class="chat-bubble w-full <?= $isSuper ? 'chat-superadmin' : 'chat-staff' ?>">
                                <div class="mb-2 text-[10px] font-bold uppercase tracking-wider border-b pb-2 <?= $isSuper ? 'text-indigo-600 border-indigo-100' : 'text-amber-600 border-gray-200' ?>">
                                    <?= $isSuper ? 'Superadmin Response' : 'Support Response' ?>
                                </div>
                                <?= nl2br(esc($reply['message'])) ?>
                            </div>
                        </div>
                        <div class="meta-text ml-14">
                            <?= esc($reply['username'] ?? 'Staff') ?> (<?= ucfirst($reply['role']) ?>) • <?= date('M d, Y h:i A', strtotime($reply['created_at'])) ?>
                        </div>
                    </div>
                
                <?php else: ?>
                    <!-- CLIENT REPLY -->
                    <div class="flex flex-col items-end ticket-reply">
                        <div class="flex items-end justify-end gap-3 w-full">
                            <div class="chat-bubble chat-client w-full text-right">
                                <?= nl2br(esc($reply['message'])) ?>
                            </div>
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 border border-gray-200 shadow-sm bg-gray-50 text-gray-400">
                                <span class="material-symbols-outlined text-xl">person</span>
                            </div>
                        </div>
                        <div class="meta-text mr-14 items-end">
                            <?= date('M d, Y h:i A', strtotime($reply['created_at'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        
        <!-- Reply Form -->
        <div class="p-4 border-t border-gray-100 bg-white shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] relative z-10 w-full">
            <form id="reply-form" action="<?= base_url('superadmin/tickets/reply/' . $ticket['id']) ?>" method="post">
                <textarea name="message" id="reply-message" rows="3" required
                    class="w-full p-4 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all text-sm" 
                    placeholder="Write a message to the group..."></textarea>
                <div class="flex justify-between items-center mt-3">
                    <span class="text-xs text-gray-400 font-medium italic"><span class="text-indigo-500 font-bold">Note:</span> Your message will be visible to everyone.</span>
                    <button type="submit" class="btn btn-primary !py-2.5 !px-8 !rounded-xl flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">send</span>
                        Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Ticket Meta Panel -->
    <div class="bg-gray-50 rounded-2xl border border-gray-100 p-6 shadow-sm overflow-y-auto hidden lg:block">
        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest mb-6 border-b border-gray-200 pb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-600 text-xl">info</span> Ticket Information
        </h3>
        
        <div class="space-y-6">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Status</p>
                <div class="inline-block"><span class="badge status-<?= strtolower(str_replace(' ', '_', $ticket['status'])) ?>"><?= $ticket['status'] ?></span></div>
            </div>

            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Ticket Number</p>
                <p class="font-bold text-gray-800"><?= esc($ticket['ticket_number']) ?></p>
            </div>

            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Category</p>
                <p class="font-medium text-gray-800"><?= esc($ticket['category'] ?? 'General') ?></p>
            </div>
            
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Priority</p>
                <p class="font-medium text-gray-800">
                    <?php if($ticket['priority'] === 'Urgent'): ?>
                        <span class="text-red-600 font-bold flex items-center gap-1"><span class="material-symbols-outlined text-sm">warning</span> Urgent</span>
                    <?php elseif($ticket['priority'] === 'High'): ?>
                        <span class="text-orange-500 font-bold flex items-center gap-1"><span class="material-symbols-outlined text-sm">priority_high</span> High</span>
                    <?php elseif($ticket['priority'] === 'Medium'): ?>
                        <span class="text-amber-500 font-bold flex items-center gap-1"><span class="material-symbols-outlined text-sm">remove</span> Medium</span>
                    <?php else: ?>
                        <span class="text-blue-500 font-bold flex items-center gap-1"><span class="material-symbols-outlined text-sm">arrow_downward</span> <?= esc($ticket['priority'] ?? 'Low') ?></span>
                    <?php endif; ?>
                </p>
            </div>

            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Assigned Support Staff</p>
                <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-sm">support_agent</span>
                    </div>
                    <span class="font-medium text-sm text-gray-800"><?= esc($ticket['staff_name'] ?? 'Unassigned') ?></span>
                </div>
            </div>
            
            <?php if (!empty($ticket['superadmin_id'])): ?>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Monitoring Superadmin</p>
                <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-sm">shield_person</span>
                    </div>
                    <span class="font-medium text-sm text-gray-800"><?= esc($ticket['superadmin_name']) ?></span>
                </div>
            </div>
            <?php endif; ?>

            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Date Created</p>
                <p class="text-sm font-medium text-gray-600"><?= date('F d, Y h:i A', strtotime($ticket['created_at'])) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Image Viewer Modal -->
<div id="image-modal" class="fixed inset-0 z-[9999]">
    <div class="fixed top-0 left-0 right-0 p-6 flex justify-between items-center z-[1001] pointer-events-none">
        <div class="flex items-center gap-4 pointer-events-auto">
            <div class="w-10 h-10 rounded-full bg-clr-blue flex items-center justify-center text-white shadow-xl" style="background-color: var(--clr-blue);">
                <span class="material-symbols-outlined text-[20px]">robot_2</span>
            </div>
            <span class="text-white font-black text-sm tracking-tight drop-shadow-md">Instructional Preview</span>
        </div>
        <div class="flex items-center gap-2 pointer-events-auto">
            <button onclick="downloadImg(event)" class="glass-tool w-12 h-12 rounded-full text-white hover:bg-white hover:text-slate-900 transition-all shadow-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">download</span>
            </button>
            <button onclick="closeImage()" class="bg-red-500/20 border border-red-500/40 w-12 h-12 rounded-full text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
    </div>
    <div class="fixed bottom-10 left-1/2 -translate-x-1/2 flex items-center glass-tool p-2 rounded-[2.5rem] z-[1001] shadow-2xl" onclick="event.stopPropagation()">
        <button onclick="zoomBtn(-0.5)" class="w-12 h-12 rounded-full text-white hover:bg-white/10 transition-colors flex items-center justify-center"><span class="material-symbols-outlined">remove</span></button>
        <div class="h-6 w-px bg-white/10 mx-2"></div>
        <button onclick="resetZoom()" class="px-6 text-white text-[10px] font-black uppercase tracking-[0.3em] hover:text-blue-400 transition-colors">Reset <span id="zoom-lvl" class="ml-2 text-blue-400">100%</span></button>
        <div class="h-6 w-px bg-white/10 mx-2"></div>
        <button onclick="zoomBtn(0.5)" class="w-12 h-12 rounded-full text-white hover:bg-white/10 transition-colors flex items-center justify-center"><span class="material-symbols-outlined">add</span></button>
    </div>
    <div id="modal-img-wrapper">
        <div id="pan-shield" onmousedown="startPan(event)" onmousemove="doPan(event)" onmouseup="endPan()" onmouseleave="endPan()" onclick="handleShieldClick(event)"></div>
        <img id="modal-img" src="" draggable="false" class="rounded-2xl">
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
    // Auto scroll to latest reply
    document.addEventListener("DOMContentLoaded", function() {
        const container = document.getElementById('thread-container');
        if(container) {
            container.scrollTop = container.scrollHeight;
        }
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

    // ── REAL-TIME CHAT SYNC ──
    if (typeof io !== 'undefined') {
        const socket = io('http://localhost:3001');
        const ticketId = "<?= $ticket['id'] ?>";
        const currentUserId = "<?= session()->get('id') ?? session()->get('user_id') ?>";
        
        socket.emit('join_ticket', ticketId);

        socket.on('new_ticket_message', function(data) {
            // If it's my own Form Submission via the Superadmin thread reply, skip it back from socket
            if (data.sender_id == currentUserId && !data.is_bot) return;

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
                window.showToast("New Chat Activity", title + " sent a message via Ticket #" + ticketId, null);
            }

            const container = document.getElementById('thread-container');
            if(container) {
                const div = document.createElement('div');
                const isSuper = data.sender_name && data.sender_name === 'Superadmin';
                const isClient = data.sender_name === 'User' || data.sender_name === 'Client';
                
                // Extremely simple DOM append to show live message without page reload
                if (data.is_bot) {
                    div.className = "flex flex-col items-start ticket-reply";
                    div.innerHTML = `
                        <div class="flex items-end gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-50 shrink-0 flex items-center justify-center border border-blue-100 p-1.5 shadow-sm">
                                <span class="material-symbols-outlined text-xl text-blue-500">robot_2</span>
                            </div>
                            <div class="chat-bubble chat-bot">
                                <div class="msg-text">${data.message}</div>
                            </div>
                        </div>
                        <div class="meta-text ml-14">HRWeb Bot • Just now</div>
                    `;
                } else if (isClient) {
                     div.className = "flex flex-col items-end ticket-reply";
                     div.innerHTML = `
                        <div class="flex items-end justify-end gap-3 w-full">
                            <div class="chat-bubble chat-client w-full text-right">
                                ${data.message.replace(/\\n/g, '<br>')}
                            </div>
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 border border-gray-200 shadow-sm bg-gray-50 text-gray-400">
                                <span class="material-symbols-outlined text-xl">person</span>
                            </div>
                        </div>
                        <div class="meta-text mr-14 items-end">Just now</div>
                     `;
                } else {
                     div.className = "flex flex-col items-start ticket-reply";
                     div.innerHTML = `
                        <div class="flex items-end gap-3 w-full">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 shadow-sm bg-amber-50 text-amber-500 border border-amber-100">
                                <span class="material-symbols-outlined text-xl">support_agent</span>
                            </div>
                            <div class="chat-bubble w-full chat-staff">
                                <div class="mb-2 text-[10px] font-bold uppercase tracking-wider border-b pb-2 text-amber-600 border-gray-200">
                                    Support Response
                                </div>
                                ${data.message.replace(/\\n/g, '<br>')}
                            </div>
                        </div>
                        <div class="meta-text ml-14">${data.sender_name} • Just now</div>
                    `;
                }

                container.appendChild(div);
                container.scrollTop = container.scrollHeight;
            }
        });
        
        // AJAX Form Submission to prevent reload
        const replyForm = document.getElementById('reply-form');
        if (replyForm) {
            replyForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const msgInput = document.getElementById('reply-message');
                const message = msgInput.value.trim();
                if (!message) return;
                
                // Optimistically append the message to UI
                const container = document.getElementById('thread-container');
                const div = document.createElement('div');
                div.className = "flex flex-col items-start ticket-reply";
                div.innerHTML = `
                    <div class="flex items-end gap-3 w-full">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 shadow-sm bg-indigo-50 text-indigo-500 border border-indigo-100">
                            <span class="material-symbols-outlined text-xl">shield_person</span>
                        </div>
                        <div class="chat-bubble w-full chat-superadmin">
                            <div class="mb-2 text-[10px] font-bold uppercase tracking-wider border-b pb-2 text-indigo-600 border-indigo-100">
                                Superadmin Response
                            </div>
                            ${message.replace(/\\n/g, '<br>')}
                        </div>
                    </div>
                    <div class="meta-text ml-14">You (Superadmin) • Just now</div>
                `;
                container.appendChild(div);
                container.scrollTop = container.scrollHeight;
                
                // Submit via AJAX (Capture FormData BEFORE clearing input!)
                const fd = new FormData(replyForm);
                fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                
                msgInput.value = ''; // clear textarea
                
                try {
                    await fetch(replyForm.action, {
                        method: 'POST',
                        body: fd,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                } catch(err) {
                    console.error('Failed to send reply', err);
                }
            });
        }
    }
</script>
<?= $this->endSection() ?>
