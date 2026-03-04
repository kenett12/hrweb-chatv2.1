<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('styles') ?>
<style>
    /* Using your main.css variables */
    .chat-container { height: calc(100vh - 350px); min-height: 400px; }
    .bubble { max-width: 85%; }
    .bubble-staff { background-color: var(--clr-blue); color: white; border-bottom-right-radius: 2px; }
    .bubble-superadmin { background-color: #6366f1; color: white; border-bottom-right-radius: 2px; }
    .bubble-client { background-color: #f3f4f6; color: var(--primary-text); border-bottom-left-radius: 2px; }
    .bubble-bot { background-color: rgba(105, 108, 111, 0.05); color: var(--clr-charcoal); font-style: italic; }

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
<div class="max-w-6xl mx-auto">
    
    <div class="fiori-page-header mb-4" style="padding: 1rem 1.5rem;">
        <div class="flex items-center gap-4">
            <a href="<?= base_url('tsr/tickets') ?>" class="w-8 h-8 flex items-center justify-center rounded-full transition-colors hover:bg-black/5 mr-2" style="color:var(--fiori-text-base);">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div>
                <h1 class="fiori-page-title text-xl">Ticket #<?= esc($ticket['id']) ?></h1>
                <p class="fiori-page-subtitle"><?= esc($ticket['subject']) ?></p>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <button type="button" onclick="toggleFullChat()" id="toggle-chat-btn" class="btn btn-outline flex items-center gap-2 transition-colors" style="height:28px; padding:0 12px; font-size:0.75rem;">
                <span class="material-symbols-outlined text-[16px]">fullscreen</span>
                <span>View Full Chat</span>
            </button>
            
            <div class="h-5 w-px bg-gray-300"></div>

            <span class="text-xs font-semibold" style="color:var(--fiori-text-secondary);">STATUS</span>
            <?php
                $statusClass = 'fiori-status--neutral';
                if ($ticket['status'] === 'Open') $statusClass = 'fiori-status--information';
                if ($ticket['status'] === 'In Progress') $statusClass = 'fiori-status--positive';
                if ($ticket['status'] === 'Resolved') $statusClass = 'fiori-status--critical'; 
            ?>
            <span class="fiori-status <?= $statusClass ?> font-bold px-3 py-1 text-sm">
                <?= esc($ticket['status']) ?>
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div id="chat-column" class="lg:col-span-2 space-y-4 transition-all duration-300">
            <div class="fiori-card flex flex-col overflow-hidden" style="padding:0;">
                <div class="chat-container overflow-y-auto p-6 flex flex-col gap-4" style="background:var(--fiori-page-bg);">
                    
                    <div class="bubble bubble-client p-4 rounded shadow-sm self-start border" style="border-color:var(--fiori-border);">
                        <p class="text-[10px] font-bold mb-1 uppercase tracking-widest" style="color:var(--fiori-text-muted);">Initial Request</p>
                        <div class="text-sm" style="color:var(--fiori-text-base);"><?= nl2br(esc($ticket['description'])) ?></div>
                        <?php if(!empty($ticket['attachment'])): ?>
                            <div class="mt-4 pt-4 border-t" style="border-color:var(--fiori-border);">
                                <?php 
                                    $ext = strtolower(pathinfo($ticket['attachment'], PATHINFO_EXTENSION));
                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                ?>
                                <?php if ($isImage): ?>
                                    <div class="mb-3">
                                        <img src="<?= base_url('uploads/tickets/' . $ticket['attachment']) ?>" alt="Attachment" class="max-w-full h-auto rounded-lg shadow-sm border border-gray-200 cursor-pointer hover:opacity-90 transition-opacity" onclick="window.open(this.src, '_blank')">
                                    </div>
                                <?php endif; ?>
                                <a href="<?= base_url('uploads/tickets/' . $ticket['attachment']) ?>" target="_blank" class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 font-semibold bg-blue-50 px-4 py-2 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">attachment</span>
                                    <?= $isImage ? 'View Full Image' : 'View Attachment' ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <p class="text-[9px] mt-2" style="color:var(--fiori-text-muted);"><?= date('M d, h:i A', strtotime($ticket['created_at'])) ?></p>
                    </div>

                    <?php foreach ($replies as $reply): ?>
                        <?php 
                            $role = $reply['role'] ?? '';
                            $isSuper = in_array($role, ['admin', 'superadmin']);
                            $staffRoles = ['admin', 'superadmin', 'tsr', 'tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it'];
                            $isStaff = in_array($role, $staffRoles);
                            $isBot = (bool)$reply['is_bot'];
                            
                            $bubbleClass = 'bubble-client self-start border';
                            if ($isBot) {
                                $bubbleClass = 'bubble-bot self-start border border-dashed border-gray-300';
                            } elseif ($isStaff) {
                                $bubbleClass = 'bubble-staff self-end text-right';
                            }
                        ?>
                        <div class="flex flex-col mb-4 <?= strpos($bubbleClass, 'self-end') !== false ? 'items-end' : 'items-start' ?>">
                            <!-- Sender Name Above Bubble -->
                            <?php if (!$isBot): ?>
                                <p class="text-[10px] font-bold mb-1 uppercase tracking-widest px-1" style="color:var(--fiori-text-muted);">
                                    <?php 
                                        $label = 'Staff';
                                        if ($role === 'superadmin') $label = 'Superadmin';
                                        elseif ($role === 'admin') $label = 'Admin';
                                        elseif (!$isStaff) $label = 'Client';
                                        
                                        $icon = ($role === 'admin' || $role === 'superadmin') ? '<span class="material-symbols-outlined text-[12px] align-middle mr-1">shield_person</span> ' : '';
                                        echo $icon . $label;
                                    ?>: <?= esc($reply['username'] ?? 'User') ?>
                                </p>
                            <?php elseif ($isBot): ?>
                                <p class="text-[10px] font-bold mb-1 uppercase tracking-widest px-1" style="color:var(--fiori-text-muted);">
                                    <span class="material-symbols-outlined text-[12px] align-middle mr-1">robot_2</span> HRWeb Bot
                                </p>
                            <?php endif; ?>
                            
                            <!-- The Chat Bubble -->
                            <div class="bubble <?= $bubbleClass ?> p-3.5 rounded-2xl shadow-sm" <?= strpos($bubbleClass, 'bubble-client') !== false ? 'style="border-color:var(--fiori-border);"' : '' ?>>
                                <div class="msg-text text-sm" <?= $isBot ? 'onclick="handleImageClick(event)"' : '' ?> style="<?= strpos($bubbleClass, 'bubble-staff') === false ? 'color:var(--fiori-text-base);' : '' ?>">
                                    <?php if($isBot): ?>
                                        <?= nl2br(html_entity_decode($reply['message'])) ?>
                                    <?php else: ?>
                                        <?= nl2br(esc($reply['message'])) ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Timestamp Below Bubble -->
                            <p class="text-[9px] mt-1 px-1" style="color:var(--fiori-text-muted);"><?= date('h:i A', strtotime($reply['created_at'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="p-4 border-t" style="border-color:var(--fiori-border); background:#fff;">
                    <form id="reply-form" action="<?= base_url('tsr/tickets/reply/' . $ticket['id']) ?>" method="post">
                        <textarea name="message" id="reply-message" required
                            class="fiori-input" 
                            style="height:80px; resize:none;"
                            placeholder="Write your message here..."
                            onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); this.form.querySelector('button[type=\'submit\']').click(); }"></textarea>
                        <div class="flex justify-end mt-3">
                            <button type="submit" class="btn btn-accent">
                                <span class="material-symbols-outlined text-[16px]">send</span>
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="details-column" class="space-y-6 transition-opacity duration-300">
            <div class="fiori-card p-0">
                <div class="fiori-card__header">
                    <div>
                        <h2 class="fiori-card__title">Ticket Details</h2>
                    </div>
                </div>
                <div class="fiori-card__content space-y-5" style="padding-top:0;">
                    <div class="pb-4 border-b" style="border-color:var(--fiori-border);">
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--fiori-text-secondary);">Client Name</label>
                        <p class="text-sm font-semibold" style="color:var(--fiori-text-base);"><?= esc($ticket['client_name']) ?></p>
                    </div>
                    <?php if (!empty($ticket['superadmin_id'])): ?>
                    <div class="pb-4 border-b" style="border-color:var(--fiori-border);">
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1 flex items-center gap-1" style="color:var(--fiori-blue);">
                            <span class="material-symbols-outlined text-[14px]">shield_person</span> Group Superadmin
                        </label>
                        <p class="text-sm font-semibold" style="color:var(--fiori-text-base);"><?= esc($ticket['superadmin_name']) ?></p>
                    </div>
                    <?php endif; ?>
                    <div class="pb-4 border-b" style="border-color:var(--fiori-border);">
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--fiori-text-secondary);">Staff Assigned</label>
                        <p class="text-sm font-semibold <?= empty($ticket['staff_name']) ? 'italic opacity-60' : 'text-blue-600' ?>">
                            <?= $ticket['staff_name'] ?? 'Not Claimed' ?>
                        </p>
                    </div>
                    <div class="pb-2">
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--fiori-text-secondary);">Category / Priority</label>
                        <div class="flex gap-2">
                            <span class="fiori-status fiori-status--neutral"><?= esc($ticket['category']) ?></span>
                            <span class="fiori-status fiori-status--critical"><?= esc($ticket['priority']) ?></span>
                        </div>
                    </div>
                </div>
                
                <?php if (empty($ticket['assigned_to'])): ?>
                    <div class="border-t p-4" style="border-color:var(--fiori-border); background:#fafafa;">
                        <a href="<?= base_url('tsr/tickets/claim/' . $ticket['id']) ?>" class="btn btn-accent w-full text-center items-center justify-center flex">Assign to Me</a>
                    </div>
                <?php else: ?>
                    <div class="border-t p-4 flex flex-col gap-2" style="border-color:var(--fiori-border); background:#fafafa;">
                        <button onclick="toggleModal('forwardTicketModal')" class="btn btn-outline w-full text-center items-center justify-center flex">
                            <span class="material-symbols-outlined text-[16px] mr-1">forward_to_inbox</span> Forward / Escalate
                        </button>
                    </div>
                <?php endif; ?>
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

<!-- Forward / Escalate Ticket Modal -->
<div id="forwardTicketModal" class="fiori-overlay hidden">
    <div class="fiori-dialog">
        <div class="fiori-dialog__header">
            <h3 class="fiori-dialog__title">Forward / Escalate Ticket</h3>
            <button onclick="toggleModal('forwardTicketModal')" class="w-7 h-7 flex items-center justify-center rounded transition-colors" style="color:var(--fiori-text-muted);" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background=''">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <form action="<?= base_url('tsr/tickets/forward/' . $ticket['id']) ?>" method="POST">
            <?= csrf_field() ?>
            <div class="fiori-dialog__body space-y-4">
                <p class="text-sm" style="color:var(--fiori-text-secondary);">Select the appropriate staff member to forward this ticket to. Note: The matrix is restricted based on your role.</p>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Select Staff</label>
                    <select name="forward_to_user_id" class="fiori-input w-full bg-white" required>
                        <option value="">-- Choose Staff --</option>
                        <?php if (isset($forwardable_staff) && !empty($forwardable_staff)): ?>
                            <?php foreach ($forwardable_staff as $staff): ?>
                                <option value="<?= esc($staff['id']) ?>"><?= esc($staff['full_name']) ?> (<?= esc($staff['role']) ?>)</option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No eligible staff available to forward to</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="fiori-dialog__footer">
                <button type="button" onclick="toggleModal('forwardTicketModal')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-accent">Forward Ticket</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
    // Auto scroll to latest reply
    document.addEventListener("DOMContentLoaded", function() {
        const container = document.querySelector('.chat-container');
        if(container) {
            container.scrollTop = container.scrollHeight;
        }
    });

    // ── LAYOUT TOGGLE LOGIC ──
    function toggleFullChat() {
        const chatCol = document.getElementById('chat-column');
        const detailsCol = document.getElementById('details-column');
        const btnSpan = document.querySelector('#toggle-chat-btn span:last-child');
        const btnIcon = document.querySelector('#toggle-chat-btn span:first-child');

        if (detailsCol.classList.contains('hidden')) {
            // Restore Split View
            detailsCol.classList.remove('hidden');
            chatCol.classList.remove('lg:col-span-3');
            chatCol.classList.add('lg:col-span-2');
            btnSpan.textContent = 'View Full Chat';
            btnIcon.textContent = 'fullscreen';
        } else {
            // Full Chat View
            detailsCol.classList.add('hidden');
            chatCol.classList.remove('lg:col-span-2');
            chatCol.classList.add('lg:col-span-3');
            btnSpan.textContent = 'Exit Full Chat';
            btnIcon.textContent = 'fullscreen_exit';
        }
    }

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
        const container = document.querySelector('.chat-container');
        
        socket.emit('join_ticket', ticketId);

        socket.on('new_ticket_message', function(data) {
            // Ignore if sender is me (though I shouldn't receive it back from PHP due to my logic, but just in case)
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


            if(container) {
                const div = document.createElement('div');
                
                if (data.is_bot) {
                    div.className = `flex flex-col mb-4 items-start`;
                    div.innerHTML = `
                        <p class="text-[10px] font-bold mb-1 uppercase tracking-widest px-1" style="color:var(--fiori-text-muted);">
                            <span class="material-symbols-outlined text-[12px] align-middle mr-1">robot_2</span> HRWeb Bot
                        </p>
                        <div class="bubble bubble-bot p-3.5 rounded-2xl shadow-sm self-start border border-dashed border-gray-300">
                            <div class="msg-text text-sm" style="color:var(--fiori-text-base);">
                                ${data.message}
                            </div>
                        </div>
                        <p class="text-[9px] mt-1 px-1" style="color:var(--fiori-text-muted);">${data.time || 'Just now'}</p>
                    `;
                } else if (data.sender_role === 'client') {
                    div.className = `flex flex-col mb-4 items-start`;
                    div.innerHTML = `
                        <p class="text-[10px] font-bold mb-1 uppercase tracking-widest px-1" style="color:var(--fiori-text-muted);">
                            Client: ${data.sender_name}
                        </p>
                        <div class="bubble bubble-client self-start p-3.5 rounded-2xl shadow-sm border" style="border-color:var(--fiori-border);">
                            <div class="msg-text text-sm" style="color:var(--fiori-text-base);">
                                ${data.message.replace(/\n/g, '<br>')}
                            </div>
                        </div>
                        <p class="text-[9px] mt-1 px-1" style="color:var(--fiori-text-muted);">${data.time || 'Just now'}</p>
                    `;
                } else {
                    const isSuper = data.sender_role === 'superadmin';
                    const isAdmin = data.sender_role === 'admin';
                    const label = isSuper ? 'Superadmin' : (isAdmin ? 'Admin' : 'Staff');
                    const icon = (isSuper || isAdmin) ? '<span class="material-symbols-outlined text-[12px] align-middle mr-1">shield_person</span> ' : '';
                    
                    div.className = `flex flex-col mb-4 items-end`;
                    div.innerHTML = `
                        <p class="text-[10px] font-bold mb-1 uppercase tracking-widest px-1" style="color:rgba(255,255,255,0.8);">
                            ${icon}${label}: ${data.sender_name}
                        </p>
                        <div class="bubble bubble-staff self-end text-right p-3.5 rounded-2xl shadow-sm">
                            <div class="msg-text text-sm">
                                ${data.message.replace(/\n/g, '<br>')}
                            </div>
                        </div>
                        <p class="text-[9px] mt-1 px-1" style="color:rgba(255,255,255,0.7);">${data.time || 'Just now'}</p>
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
                const now = new Date();
                const time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
                const div = document.createElement('div');
                div.className = `flex flex-col mb-4 items-end`;
                div.innerHTML = `
                    <p class="text-[10px] font-bold mb-1 uppercase tracking-widest px-1" style="color:rgba(255,255,255,0.8);">
                        Staff: You
                    </p>
                    <div class="bubble bubble-staff self-end text-right p-3.5 rounded-2xl shadow-sm">
                        <div class="msg-text text-sm">
                            ${message.replace(/\n/g, '<br>')}
                        </div>
                    </div>
                    <p class="text-[9px] mt-1 px-1" style="color:rgba(255,255,255,0.7);">${time}</p>
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