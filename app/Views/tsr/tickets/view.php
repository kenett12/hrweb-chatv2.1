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
    
    <div class="flex justify-between items-center bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <a href="<?= base_url('tsr/tickets') ?>" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span class="text-sm font-medium">Back to Queue</span>
        </a>
        
        <div class="flex items-center gap-4">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Current Status:</span>
            <span class="px-3 py-1.5 rounded-xl text-xs font-bold border 
                <?= $ticket['status'] === 'Open' ? 'bg-amber-50 text-amber-600 border-amber-200' : 
                   ($ticket['status'] === 'In Progress' ? 'bg-blue-50 text-blue-600 border-blue-200' : 
                   ($ticket['status'] === 'Resolved' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 
                   'bg-slate-50 text-slate-600 border-slate-200')) ?>">
                <?= esc($ticket['status']) ?>
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col overflow-hidden">
                <div class="chat-container overflow-y-auto p-6 flex flex-col gap-4 bg-gray-50/30">
                    
                    <div class="bubble bubble-client p-4 rounded-2xl shadow-sm self-start">
                        <p class="text-[10px] font-bold text-gray-400 mb-1 uppercase tracking-tighter">Initial Request</p>
                        <div class="text-sm"><?= nl2br(esc($ticket['description'])) ?></div>
                        <p class="text-[9px] mt-2 opacity-50"><?= date('M d, h:i A', strtotime($ticket['created_at'])) ?></p>
                    </div>

                    <?php foreach ($replies as $reply): ?>
                        <?php 
                            $isSuper = ($reply['role'] === 'superadmin');
                            $isStaff = ($reply['role'] === 'tsr');
                            $isBot = (bool)$reply['is_bot'];
                            
                            $bubbleClass = 'bubble-client self-start';
                            if ($isBot) {
                                $bubbleClass = 'bubble-bot';
                            } elseif ($isSuper) {
                                $bubbleClass = 'bubble-superadmin self-end text-right';
                            } elseif ($isStaff) {
                                $bubbleClass = 'bubble-staff self-end text-right';
                            }
                        ?>
                        <div class="bubble <?= $bubbleClass ?> p-4 rounded-2xl shadow-sm">
                            <?php if (!$isBot): ?>
                                <p class="text-[10px] font-bold mb-1 opacity-70 uppercase tracking-widest">
                                    <?= $isSuper ? '<span class="material-symbols-outlined text-[12px] align-middle mr-1">shield_person</span> Superadmin' : ($isStaff ? 'Staff' : 'Client') ?>: <?= esc($reply['username']) ?>
                                </p>
                            <?php endif; ?>
                            <div class="msg-text text-sm" <?= $isBot ? 'onclick="handleImageClick(event)"' : '' ?>>
                                <?php if($isBot): ?>
                                    <?= html_entity_decode($reply['message']) ?>
                                <?php else: ?>
                                    <?= nl2br(esc($reply['message'])) ?>
                                <?php endif; ?>
                            </div>
                            <p class="text-[9px] mt-1 opacity-60"><?= date('h:i A', strtotime($reply['created_at'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="p-4 border-t border-gray-100 bg-white">
                    <form id="reply-form" action="<?= base_url('tsr/tickets/reply/' . $ticket['id']) ?>" method="post">
                        <textarea name="message" id="reply-message" rows="3" required
                            class="w-full p-4 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all text-sm" 
                            placeholder="Write your message here..."></textarea>
                        <div class="flex justify-end mt-3">
                            <button type="submit" class="btn btn-accent !py-2.5 !px-8 !rounded-xl">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-gray-400">info</span> 
                    Ticket Details
                </h3>
                <div class="space-y-5">
                    <div class="pb-4 border-b border-gray-50">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Client Name</label>
                        <p class="text-sm font-semibold mt-1 text-gray-700"><?= esc($ticket['client_name']) ?></p>
                    </div>
                    <?php if (!empty($ticket['superadmin_id'])): ?>
                    <div class="pb-4 border-b border-gray-50">
                        <label class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">shield_person</span> Group Superadmin
                        </label>
                        <p class="text-sm font-semibold mt-1 text-indigo-600"><?= esc($ticket['superadmin_name']) ?></p>
                    </div>
                    <?php endif; ?>
                    <div class="pb-4 border-b border-gray-50">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Staff Assigned</label>
                        <p class="text-sm font-semibold mt-1 <?= empty($ticket['staff_name']) ? 'text-orange-500 italic' : 'text-blue-600' ?>">
                            <?= $ticket['staff_name'] ?? 'Not Claimed' ?>
                        </p>
                    </div>
                    <div class="pb-4 border-b border-gray-50">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Category / Priority</label>
                        <div class="flex gap-2 mt-1">
                            <span class="badge badge-pending !bg-opacity-20"><?= esc($ticket['category']) ?></span>
                            <span class="badge badge-active !bg-opacity-20"><?= esc($ticket['priority']) ?></span>
                        </div>
                    </div>
                </div>
                
                <?php if (empty($ticket['assigned_to'])): ?>
                    <div class="mt-6">
                        <a href="<?= base_url('tsr/tickets/claim/' . $ticket['id']) ?>" class="btn btn-success w-full !rounded-xl">Claim Ticket</a>
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

            const container = document.querySelector('.chat-container');
            if(container) {
                const div = document.createElement('div');
                
                if (data.is_bot) {
                    div.className = `bubble bubble-bot p-4 rounded-2xl shadow-sm`;
                    div.innerHTML = `
                        <div class="msg-text text-sm">
                            ${data.message}
                        </div>
                        <p class="text-[9px] mt-1 opacity-60">Just now</p>
                    `;
                } else if (data.sender_name === 'User' || data.sender_name === 'Client') {
                    div.className = `bubble bubble-client self-start p-4 rounded-2xl shadow-sm`;
                    div.innerHTML = `
                        <p class="text-[10px] font-bold mb-1 opacity-70 uppercase tracking-widest">
                            Client: ${data.sender_name}
                        </p>
                        <div class="msg-text text-sm">
                            ${data.message.replace(/\\n/g, '<br>')}
                        </div>
                        <p class="text-[9px] mt-1 opacity-60">Just now</p>
                    `;
                } else {
                    div.className = `bubble bubble-staff self-end text-right p-4 rounded-2xl shadow-sm`;
                    div.innerHTML = `
                        <p class="text-[10px] font-bold mb-1 opacity-70 uppercase tracking-widest">
                            Staff: ${data.sender_name}
                        </p>
                        <div class="msg-text text-sm">
                            ${data.message.replace(/\\n/g, '<br>')}
                        </div>
                        <p class="text-[9px] mt-1 opacity-60">Just now</p>
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
                const container = document.querySelector('.chat-container');
                const div = document.createElement('div');
                div.className = `bubble bubble-staff self-end text-right p-4 rounded-2xl shadow-sm`;
                div.innerHTML = `
                    <p class="text-[10px] font-bold mb-1 opacity-70 uppercase tracking-widest">
                        Staff: You
                    </p>
                    <div class="msg-text text-sm">
                        ${message.replace(/\\n/g, '<br>')}
                    </div>
                    <p class="text-[9px] mt-1 opacity-60">Just now</p>
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