<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/tsr/tickets.css') ?>?v=<?= time() ?>">
<style>
    :root {
        --bubble-radius: 12px;
        --clr-blue: #1e72af;
        --bg-chat: #f8fafc;
    }

    /* ─── Message Row ────────────────────────────────────── */
    .msg-row {
        display: flex;
        align-items: flex-end;
        gap: 12px;
        max-width: 85%;
        margin-bottom: 2px;
    }

    .msg-row--bot {
        align-self: flex-start;
    }

    .msg-row--user {
        align-self: flex-end;
        flex-direction: row-reverse;
        margin-left: auto;
    }

    /* ─── Avatar ─────────────────────────────────────────── */
    .msg-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-bottom: 2px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.08);
    }

    .msg-avatar--bot {
        background: white;
        color: #10b981;
        border: 1px solid #e2e8f0;
    }

    .msg-avatar--staff {
        background: #fef3c7;
        color: #d97706;
        border: 1px solid #fde68a;
    }

    .msg-avatar--super {
        background: #e0e7ff;
        color: #4f46e5;
        border: 1px solid #c7d2fe;
    }

    .msg-avatar--user {
        background: white;
        color: #1e72af;
        border: 1px solid #e2e8f0;
    }

    .msg-avatar.invisible {
        visibility: hidden;
    }

    /* ─── Bubble Wrapper ─────────────────────────────────── */
    .msg-bubble-wrap {
        display: flex;
        flex-direction: column;
        gap: 3px;
        width: 100%;
    }

    .msg-row--user .msg-bubble-wrap {
        align-items: flex-end;
    }

    /* ─── Message Bubbles ────────────────────────────────── */
    .msg-bubble {
        padding: 12px 18px;
        border-radius: var(--bubble-radius);
        font-size: 14.5px;
        line-height: 1.6;
        position: relative;
        word-break: break-word;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }

    .chat-staff {
        background-color: #fffbeb;
        border: 1.5px solid #fde68a;
        color: #92400e;
        border-bottom-right-radius: 4px;
    }

    .chat-superadmin {
        background-color: #f5f3ff;
        border: 1.5px solid #ddd6fe;
        color: #4c1d95;
        border-bottom-right-radius: 4px;
    }

    .chat-client {
        background-color: white;
        border: 1.5px solid #ecfdf5;
        border-left: 4px solid #10b981;
        color: #1a2332;
        border-bottom-left-radius: 4px;
    }

    /* Grouping Adjustments */
    .msg-bubble.first-in-group { border-top-left-radius: var(--bubble-radius) !important; border-top-right-radius: var(--bubble-radius) !important; }
    
    .msg-time {
        font-size: 10px;
        color: #94a3b8;
        font-weight: 600;
        margin-top: 4px;
    }

    .msg-sender-name {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 2px;
        margin-left: 4px;
    }

    .msg-row--user .msg-sender-name {
        margin-left: 0;
        margin-right: 4px;
        text-align: right;
    }

    #thread-container {
        background: #f8fafc;
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
        <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-0.5" id="thread-container" style="scroll-behavior: smooth; background: #f8fafc;">
            
            <?php
            $lastSenderId = null;
            $currentUserId = session()->get('id') ?? session()->get('user_id');

            // Merge initial description with replies for unified grouping logic
            // We'll treat the initial description as the first message from the client
            $initialMsg = [
                'user_id'    => $ticket['client_id'],
                'username'   => $ticket['client_name'],
                'role'       => 'client',
                'message'    => $ticket['description'],
                'is_bot'     => 0,
                'created_at' => $ticket['created_at'],
                'is_initial' => true
            ];
            
            $allMessages = array_merge([$initialMsg], $replies);

            foreach ($allMessages as $i => $msg):
                $isBot = (isset($msg['is_bot']) && $msg['is_bot'] == 1);
                $isSuper = (isset($msg['role']) && in_array($msg['role'], ['admin', 'superadmin']));
                $staffRoles = ['admin', 'superadmin', 'tsr', 'tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it'];
                $isStaff = (isset($msg['role']) && in_array($msg['role'], $staffRoles));
                
                // POV: Staff/Admins on Right, Client/Bot on Left
                $isRight = $isStaff; 
                $isMe = (!$isBot && $msg['user_id'] == $currentUserId);

                $senderKey = $isBot ? 'bot' : ($msg['user_id'] ?? 'unknown');
                $isNewGroup = ($lastSenderId !== $senderKey);
                $lastSenderId = $senderKey;

                // For grouping bubble corners
                $nextMsg = $allMessages[$i + 1] ?? null;
                $nextSenderKey = $nextMsg ? ($nextMsg['is_bot'] ? 'bot' : ($nextMsg['user_id'] ?? 'unknown')) : null;
                $isLastInGroup = ($nextSenderKey !== $senderKey);
                
                $groupClass = $isNewGroup ? 'first-in-group' : ($isLastInGroup ? 'last-in-group' : 'mid-in-group');
            ?>

                <div class="msg-row <?= $isRight ? 'msg-row--user' : 'msg-row--bot' ?> <?= $isNewGroup ? 'mt-6' : 'mt-1' ?>">
                    <!-- Avatar -->
                    <div class="msg-avatar <?= $isRight ? ($isSuper ? 'msg-avatar--super' : 'msg-avatar--staff') : 'msg-avatar--user' ?> <?= !$isNewGroup ? 'invisible' : '' ?>">
                        <?php if($isBot): ?>
                            <span class="material-symbols-outlined text-xl">robot_2</span>
                        <?php elseif($isSuper): ?>
                            <span class="material-symbols-outlined text-xl">shield_person</span>
                        <?php elseif($isStaff): ?>
                            <span class="material-symbols-outlined text-xl">support_agent</span>
                        <?php else: ?>
                            <span class="material-symbols-outlined text-xl">person</span>
                        <?php endif; ?>
                    </div>

                    <div class="msg-bubble-wrap">
                        <?php if ($isNewGroup): ?>
                            <div class="msg-sender-name <?= $isSuper ? 'text-indigo-600' : ($isStaff ? 'text-amber-600' : 'text-emerald-600') ?>">
                                <?php 
                                    if ($isBot) {
                                        echo 'HRWeb Bot';
                                    } elseif ($isMe) {
                                        echo 'You (' . ($isSuper ? 'Administrator' : 'Staff') . ')';
                                    } else {
                                        $label = $isSuper ? 'Administrator' : ($isStaff ? 'Support Team' : 'Client');
                                        echo esc($msg['username'] ?? $label) . " <span class='opacity-40 ml-1'>[{$label}]</span>";
                                    }
                                ?>
                            </div>
                        <?php endif; ?>

                        <?php 
                            $bubbleClass = $isSuper ? 'chat-superadmin' : ($isStaff ? 'chat-staff' : 'chat-client');
                        ?>
                        <div class="msg-bubble <?= $groupClass ?> <?= $bubbleClass ?>">
                            <?php if (isset($msg['is_initial']) && $msg['is_initial']): ?>
                                <div class="mb-2 text-[10px] font-bold text-emerald-600 uppercase tracking-widest border-b border-emerald-50 pb-1.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">description</span> Initial Request
                                </div>
                            <?php endif; ?>

                            <div class="msg-text" <?= $isBot ? 'onclick="handleImageClick(event)"' : '' ?>>
                                <?php if($isBot): ?>
                                    <?= nl2br(html_entity_decode($msg['message'])) ?>
                                <?php else: ?>
                                    <?= nl2br(esc($msg['message'])) ?>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($msg['attachment']) && isset($msg['is_initial'])): ?>
                                <div class="mt-3 pt-3 border-t border-emerald-50/50">
                                    <?php 
                                        $ext = strtolower(pathinfo($msg['attachment'], PATHINFO_EXTENSION));
                                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    ?>
                                    <?php if ($isImage): ?>
                                        <img src="<?= base_url('uploads/tickets/' . $msg['attachment']) ?>" class="max-w-full h-auto rounded-lg mb-2 shadow-sm cursor-zoom-in" onclick="window.open(this.src, '_blank')">
                                    <?php endif; ?>
                                    <a href="<?= base_url('uploads/tickets/' . $msg['attachment']) ?>" target="_blank" class="text-[10px] font-bold text-blue-600 hover:underline flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">attachment</span> View Attachment
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="msg-time flex <?= $isRight ? 'justify-end ml-auto' : 'justify-start mr-auto' ?> opacity-40">
                                <?= date('h:i A', strtotime($msg['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
<?php endforeach; ?>
        </div>
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
        const container = document.getElementById('thread-container');
        let lastSenderId = "<?= $lastSenderId ?>";
        
        socket.emit('join_ticket', ticketId);

        function appendMessageToUI(data, isOptimistic = false) {
            if(!container) return;
            
            const isBot = data.is_bot;
            const senderId = isBot ? 'bot' : (data.sender_id || 'unknown');
            const isNewGroup = (lastSenderId !== senderId);
            lastSenderId = senderId;

            const staffRoles = ['admin', 'superadmin', 'tsr', 'tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it'];
            const isStaff = staffRoles.includes(data.sender_role);
            const isSuper = data.sender_role === 'superadmin' || data.sender_role === 'admin';
            const isMe = (data.sender_id == currentUserId && !isBot);
            const isRight = isStaff; // Staff POV: Staff/Admin on Right

            if (isNewGroup) {
                const row = document.createElement('div');
                row.className = `msg-row ${isRight ? 'msg-row--user' : 'msg-row--bot'} mt-6`;
                
                const avatarClass = isRight ? (isSuper ? 'msg-avatar--super' : 'msg-avatar--staff') : 'msg-avatar--user';
                const avatarIcon = isBot ? 'robot_2' : (isSuper ? 'shield_person' : (isRight ? 'support_agent' : 'person'));
                
                const senderColor = isSuper ? 'text-indigo-600' : (isStaff ? 'text-amber-600' : 'text-emerald-600');
                const roleLabel = isSuper ? 'Administrator' : (isStaff ? 'Support Team' : 'Client');
                const displayName = isBot ? 'HRWeb Bot' : (isMe ? 'You ('+roleLabel+')' : (data.sender_name || 'User') + ' <span class="opacity-40 ml-1">['+roleLabel+']</span>');

                const bubbleClass = isSuper ? 'chat-superadmin' : (isStaff ? 'chat-staff' : 'chat-client');

                row.innerHTML = `
                    <div class="msg-avatar ${avatarClass}">
                        <span class="material-symbols-outlined text-xl">${avatarIcon}</span>
                    </div>
                    <div class="msg-bubble-wrap">
                        <div class="msg-sender-name ${senderColor}">${displayName}</div>
                        <div class="msg-bubble first-in-group last-in-group ${bubbleClass}">
                            <div class="msg-text">${isBot ? data.message : data.message.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\n/g, "<br>")}</div>
                            <div class="msg-time flex ${isRight ? 'justify-end ml-auto' : 'justify-start mr-auto'} opacity-40">${data.time || 'Just now'}</div>
                        </div>
                    </div>
                `;
                container.appendChild(row);
            } else {
                const rows = container.querySelectorAll('.msg-row');
                const lastRow = rows[rows.length - 1];
                const wrap = lastRow.querySelector('.msg-bubble-wrap');
                const bubbles = wrap.querySelectorAll('.msg-bubble');
                const lastBubble = bubbles[bubbles.length - 1];

                lastBubble.classList.remove('last-in-group');
                
                const bubbleClass = isSuper ? 'chat-superadmin' : (isStaff ? 'chat-staff' : 'chat-client');
                const newBubble = document.createElement('div');
                newBubble.className = `msg-bubble last-in-group ${bubbleClass}`;
                newBubble.innerHTML = `
                    <div class="msg-text">${isBot ? data.message : data.message.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\n/g, "<br>")}</div>
                    <div class="msg-time flex ${isRight ? 'justify-end ml-auto' : 'justify-start mr-auto'} opacity-40">${data.time || 'Just now'}</div>
                `;
                wrap.appendChild(newBubble);
            }

            container.scrollTop = container.scrollHeight;
        }

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

            appendMessageToUI(data);
        });
        
        // AJAX Form Submission to prevent reload
        const replyForm = document.getElementById('reply-form');
        if (replyForm) {
            replyForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const msgInput = document.getElementById('reply-message');
                const message = msgInput.value.trim();
                const submitBtn = this.querySelector('button[type="submit"]');
                if (!message) return;
                
                // Optimistically append
                const now = new Date();
                const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
                appendMessageToUI({
                    sender_id: currentUserId,
                    sender_name: "You",
                    sender_role: "<?= session()->get('role') ?>",
                    message: message,
                    is_bot: 0,
                    time: timeStr
                }, true);

                // Capture FormData BEFORE clearing input
                const fd = new FormData(replyForm);
                fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                
                msgInput.value = '';
                if(submitBtn) submitBtn.disabled = true;

                try {
                    await fetch(replyForm.action, {
                        method: 'POST',
                        body: fd,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if(submitBtn) submitBtn.disabled = false;
                } catch (err) {
                    console.error("Failed to send:", err);
                    if(submitBtn) submitBtn.disabled = false;
                }
            });
        }
    }
</script>
<?= $this->endSection() ?>
