<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('styles') ?>
<style>
    /* Using your main.css variables */
    .msg-row { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; width: 100%; animation: fadeIn 0.15s ease both; }
    .msg-avatar { width: 36px; height: 36px; border-radius: 4px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .msg-content { flex: 1; display: flex; flex-direction: column; gap: 4px; padding: 12px 16px; border-radius: 4px; font-size: 0.875rem; line-height: 1.5; border: 1px solid var(--fiori-border); }
    
    .msg-client { background: var(--fiori-surface); border-left: 3px solid var(--fiori-blue); }
    .msg-bot { background: var(--fiori-surface); border-left: 3px solid var(--fiori-neutral); color: var(--fiori-text-secondary); }
    .msg-staff { background: var(--fiori-blue-light); border-left: 3px solid var(--fiori-blue); }
    .msg-superadmin { background: #f5f3ff; border-left: 3px solid #6366f1; border-color: #ddd6fe; }

    .msg-row--right {
        flex-direction: row-reverse;
        align-self: flex-end;
        margin-left: auto;
    }
    .msg-row--right .msg-content {
        border-left: 1px solid var(--fiori-border);
        border-right: 3px solid var(--fiori-blue);
    }
    .msg-row--right .msg-bot { border-right-color: var(--fiori-neutral); }
    .msg-row--right .msg-superadmin { border-right-color: #6366f1; border-color: #ddd6fe; }

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
            if ($ticket['status'] === 'Closed') $statusClass = 'fiori-status--neutral'; 
            ?>
            <span class="fiori-status <?= $statusClass ?> font-bold px-3 py-1 text-sm">
                <?= esc($ticket['status']) ?>
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[calc(100vh-240px)]">
        
        <div id="chat-column" class="lg:col-span-2 transition-all duration-300 h-full min-h-0">
            <div class="fiori-card flex flex-col overflow-hidden h-full relative" style="padding:0;">
                
                <!-- Floating Exit Fullscreen Button -->
                <button type="button" onclick="toggleFullChat()" id="floating-exit-btn" class="hidden absolute top-3 right-3 z-[110] btn btn-outline bg-white flex items-center gap-2 shadow-md" style="border-color:var(--fiori-border);">
                    <span class="material-symbols-outlined text-[16px]">fullscreen_exit</span>
                    <span>Exit Full Chat</span>
                </button>
                
                <div class="chat-container flex-1 overflow-y-auto p-6 flex flex-col gap-4" style="background:var(--fiori-page-bg);">
                    
                    <div class="msg-row">
                        <div class="msg-avatar" style="background:var(--fiori-surface); color:var(--fiori-blue); border:1px solid var(--fiori-border);">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                        </div>
                        <div class="msg-content msg-client">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-bold uppercase tracking-widest px-1" style="color:var(--fiori-text-muted);">Initial Request: <?= esc($ticket['client_name']) ?></span>
                                <span class="text-[9px]" style="color:var(--fiori-text-muted);"><?= date('M d, h:i A', strtotime($ticket['created_at'])) ?></span>
                            </div>
                            <div class="msg-text text-sm" style="color:var(--fiori-text-base);"><?= nl2br(esc($ticket['description'])) ?></div>
                            <?php 
                                $attachments = !empty($ticket['attachments']) ? json_decode($ticket['attachments'], true) : [];
                                $links = !empty($ticket['external_links']) ? json_decode($ticket['external_links'], true) : [];
                                
                                // Include legacy attachment if it exists and isn't in the new list
                                $allAttachments = $attachments;
                                if (!empty($ticket['attachment']) && !in_array($ticket['attachment'], $allAttachments)) {
                                    array_unshift($allAttachments, $ticket['attachment']);
                                }
                            ?>

                            <?php if(!empty($allAttachments)): ?>
                                <div class="mt-4 pt-4 border-t" style="border-color:var(--fiori-border);">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Evidence Gallery</p>
                                    <div class="flex flex-wrap gap-3">
                                        <?php foreach ($allAttachments as $file): ?>
                                            <?php 
                                                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            ?>
                                            <div class="relative group">
                                                <?php if ($isImage): ?>
                                                    <img src="<?= base_url('uploads/tickets/' . $file) ?>" alt="Attachment" class="w-24 h-24 object-cover rounded-lg shadow-sm border border-gray-200 cursor-pointer hover:opacity-90 transition-opacity" onclick="window.open(this.src, '_blank')">
                                                <?php else: ?>
                                                    <div class="w-24 h-24 flex flex-col items-center justify-center bg-gray-100 rounded-lg border border-gray-200 text-gray-400">
                                                        <span class="material-symbols-outlined">description</span>
                                                        <span class="text-[9px] uppercase font-bold mt-1"><?= $ext ?></span>
                                                    </div>
                                                <?php endif; ?>
                                                <a href="<?= base_url('uploads/tickets/' . $file) ?>" target="_blank" class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/20 rounded-lg">
                                                    <span class="material-symbols-outlined text-white text-[20px]">open_in_new</span>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if(!empty($links)): ?>
                                <div class="mt-4 pt-4 border-t" style="border-color:var(--fiori-border);">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">External Links</p>
                                    <div class="space-y-2">
                                        <?php foreach ($links as $link): ?>
                                            <a href="<?= esc($link) ?>" target="_blank" class="flex items-center gap-2 p-2 rounded bg-gray-50 border border-gray-100 hover:bg-blue-50 transition-colors group">
                                                <span class="material-symbols-outlined text-[16px] text-blue-500">link</span>
                                                <span class="text-[11px] font-medium text-gray-600 truncate flex-1"><?= esc($link) ?></span>
                                                <span class="material-symbols-outlined text-[14px] text-gray-300">open_in_new</span>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php foreach ($replies as $reply): ?>
                        <?php 
                            $role = $reply['role'] ?? '';
                            $isSuper = in_array($role, ['admin', 'superadmin']);
                            $staffRoles = ['admin', 'superadmin', 'tsr', 'tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it'];
                            $isStaff = in_array($role, $staffRoles);
                            $isBot = (bool)$reply['is_bot'];
                            
                            $msgClass = 'msg-client';
                            $avatarIcon = 'person';
                            $senderName = esc($reply['username'] ?? 'User');
                            $roleLabel = 'Client';

                            if ($isBot) {
                                $msgClass = 'msg-bot';
                                $avatarIcon = 'robot_2';
                                $senderName = 'HRWeb Bot';
                                $roleLabel = 'Automated';
                            } elseif ($isSuper) {
                                $msgClass = 'msg-superadmin';
                                $avatarIcon = 'shield_person';
                                $roleLabel = 'Administrator';
                            } elseif ($isStaff) {
                                $msgClass = 'msg-staff';
                                $avatarIcon = 'support_agent';
                                $roleLabel = 'Support Team';
                            }
                            
                            $isRight = $isStaff && !$isSuper;
                        ?>
                        <div class="msg-row <?= $isRight ? 'msg-row--right' : '' ?>">
                            <div class="msg-avatar" style="background:var(--fiori-surface); color:var(--fiori-blue); border:1px solid var(--fiori-border);">
                                <span class="material-symbols-outlined text-[20px]"><?= $avatarIcon ?></span>
                            </div>
                            <div class="msg-content <?= $msgClass ?>">
                                <div class="flex items-center justify-between mb-1 <?= $isRight ? 'flex-row-reverse' : '' ?>">
                                    <span class="text-[10px] font-bold uppercase tracking-widest px-1" style="color:var(--fiori-text-muted);">
                                        <?= $senderName ?> <span class="opacity-50 inline-block ml-1">[<?= $roleLabel ?>]</span>
                                    </span>
                                    <span class="text-[9px]" style="color:var(--fiori-text-muted);"><?= date('h:i A', strtotime($reply['created_at'])) ?></span>
                                </div>
                                
                                <div class="msg-text text-sm" <?= $isBot ? 'onclick="handleImageClick(event)"' : '' ?> style="color:var(--fiori-text-base);">
                                    <?php if($isBot): 
                                        $parsedMsg = html_entity_decode($reply['message']);
                                        $parsedMsg = preg_replace_callback('/\[(.*?)\]\((.*?)\)/', function($m) {
                                            $url = trim($m[2]);
                                            if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
                                                $url = "https://" . ltrim($url, '/');
                                            }
                                            return '<a href="' . $url . '" target="_blank" class="text-blue-500 underline font-semibold hover:text-blue-700">' . $m[1] . '</a>';
                                        }, $parsedMsg);
                                        echo nl2br($parsedMsg);
                                    else: ?>
                                        <?= nl2br(esc($reply['message'])) ?>
                                    <?php endif; ?>
                                </div>

                                <?php 
                                    $replyAttachments = !empty($reply['attachments']) ? json_decode($reply['attachments'], true) : [];
                                    $replyLinks = !empty($reply['external_links']) ? json_decode($reply['external_links'], true) : [];
                                ?>

                                <?php if (!empty($replyAttachments)): ?>
                                    <div class="mt-3 pt-3 border-t border-gray-100/50">
                                        <div class="flex flex-wrap gap-2">
                                            <?php foreach ($replyAttachments as $file): ?>
                                                <div class="relative group w-20 h-20">
                                                    <img src="<?= base_url('uploads/tickets/' . $file) ?>" 
                                                         class="w-full h-full object-cover rounded-lg shadow-sm cursor-zoom-in border border-gray-100/50" 
                                                         onclick="window.open(this.src, '_blank')">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($replyLinks)): ?>
                                    <div class="mt-2 space-y-1">
                                        <?php foreach ($replyLinks as $link): ?>
                                            <a href="<?= esc($link) ?>" target="_blank" class="flex items-center gap-2 p-1.5 rounded bg-white/50 border border-gray-100/30 hover:bg-white transition-colors group">
                                                <span class="material-symbols-outlined text-[14px] text-blue-500">link</span>
                                                <span class="text-[10px] font-medium text-gray-600 truncate flex-1"><?= esc($link) ?></span>
                                                <span class="material-symbols-outlined text-[12px] text-gray-300">open_in_new</span>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($ticket['status'] !== 'Closed'): ?>
                <div id="reply-extras" class="hidden px-5 py-3 border-t border-gray-50 bg-gray-50/50">
                    <div class="flex flex-col gap-3">
                        <div id="reply-attachments-preview" class="flex flex-wrap gap-2"></div>
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

                <div class="p-4 border-t" style="border-color:var(--fiori-border); background:#fff;">
                    <div class="flex items-start gap-3">
                        <button type="button" onclick="toggleReplyExtras()" class="mt-2 text-gray-400 hover:text-blue-600 transition-colors" title="Add attachments or links">
                            <span class="material-symbols-outlined" id="extras-toggle-icon">add_circle</span>
                        </button>
                        <form id="reply-form" class="flex-1" action="<?= base_url('tsr/tickets/reply/' . $ticket['id']) ?>" method="post">
                            <textarea name="message" id="reply-message"
                                class="fiori-input" 
                                style="height:80px; resize:none;"
                                placeholder="Write your message here..."
                                onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); this.form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true })); }"></textarea>
                            <div class="flex justify-end mt-3">
                                <button type="submit" class="btn btn-accent">
                                    <span class="material-symbols-outlined text-[16px]">send</span>
                                    Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php else: ?>
                <div class="p-6 border-t bg-slate-50 text-center" style="border-color:var(--fiori-border);">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-1">Session Closed</p>
                    <p class="text-xs text-slate-500 font-medium italic">This ticket is resolved and the chat is now read-only.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div id="details-column" class="transition-opacity duration-300 h-full">
            <div class="fiori-card p-0 flex flex-col h-full overflow-hidden">
                <div class="fiori-card__header flex-shrink-0">
                    <div>
                        <h2 class="fiori-card__title">Ticket Details</h2>
                    </div>
                </div>
                <div class="fiori-card__content flex-1 overflow-y-auto space-y-5" style="padding-top:0;">
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
                    <div class="pb-4 border-b" style="border-color:var(--fiori-border);">
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--fiori-text-secondary);">Category / Priority</label>
                        <div class="flex gap-2 items-center">
                            <span class="fiori-status fiori-status--neutral"><?= esc($ticket['category']) ?></span>
                            <?php if (!empty($ticket['subcategory'])): ?>
                                <span class="material-symbols-outlined text-[12px] text-gray-400">arrow_forward_ios</span>
                                <span class="fiori-status fiori-status--neutral" style="background:#f0f7ff; color:#1e72af; border-color:#d0e7ff;"><?= esc($ticket['subcategory']) ?></span>
                            <?php endif; ?>
                            <span class="fiori-status fiori-status--critical"><?= esc($ticket['priority']) ?></span>
                        </div>
                    </div>


                </div>
                
                <?php if (empty($ticket['assigned_to'])): ?>
                    <div class="border-t p-4" style="border-color:var(--fiori-border); background:#fafafa;">
                        <a href="<?= base_url('tsr/tickets/claim/' . $ticket['id']) ?>" class="btn btn-accent w-full text-center items-center justify-center flex">Assign to Me</a>
                    </div>
                <?php else: ?>
                    <div class="pt-6 mt-6 border-t space-y-3" style="border-color:var(--fiori-border);">
                        <?php if ($ticket['status'] !== 'Closed'): ?>
                        <?php if ($ticket['close_requested']): ?>
                            <div class="p-4 bg-amber-50 rounded border border-amber-200 mb-4 animate-pulse">
                                <div class="flex items-center gap-2 text-amber-800 font-bold text-[10px] uppercase tracking-widest">
                                    <span class="material-symbols-outlined text-[18px]">verified</span>
                                    Close Request Pending
                                </div>
                                <p class="text-[11px] text-amber-700 mt-1 font-medium italic">You already requested to mark this as closed.</p>
                            </div>
                        <?php else: ?>
                            <button onclick="confirmAction(event, '<?= base_url('tsr/tickets/requestClose/'.$ticket['id']) ?>', 'Request Mark as Closed?', 'This will notify the Admin that you have finished the task.', 'Request Close', 'var(--fiori-positive)')"
                                class="btn btn-outline w-full flex items-center justify-center gap-2 py-2.5 transition-all text-emerald-600 border-emerald-200 hover:bg-emerald-50">
                                <span class="material-symbols-outlined text-[18px]">done_all</span> Requst Mark as Closed
                            </button>
                        <?php endif; ?>

                        <button onclick="toggleModal('esc-modal')"
                            class="btn btn-outline w-full flex items-center justify-center gap-2 py-2.5 transition-all text-blue-600 border-blue-200 hover:bg-blue-50">
                            <span class="material-symbols-outlined text-[16px] mr-1">forward_to_inbox</span> Forward / Escalate
                        </button>
                        <?php else: ?>
                            <div class="p-4 bg-slate-50 rounded border border-slate-200 text-center">
                                <div class="flex items-center justify-center gap-2 text-slate-400 font-bold text-[10px] uppercase tracking-[0.2em]">
                                    <span class="material-symbols-outlined text-[18px]">lock</span>
                                    Status Locked
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1 font-medium italic">Ticket is resolved.</p>
                            </div>
                        <?php endif; ?>
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
        try {
            const chatCol = document.getElementById('chat-column');
            const detailsCol = document.getElementById('details-column');
            if (!chatCol || !detailsCol) return;
            
            const gridContainer = detailsCol.parentElement;
            const toggleBtn = document.getElementById('toggle-chat-btn');
            const btnSpan = toggleBtn ? toggleBtn.querySelector('span:last-child') : null;
            const btnIcon = toggleBtn ? toggleBtn.querySelector('span:first-child') : null;
            const floatingExitBtn = document.getElementById('floating-exit-btn');
            const mainWrapper = document.querySelector('.flex-1.flex.flex-col.min-w-0.overflow-hidden');

            if (detailsCol.style.display === 'none' || detailsCol.classList.contains('hidden')) {
                // Restore Split View
                detailsCol.style.display = '';
                detailsCol.classList.remove('hidden');
                chatCol.classList.remove('fixed', 'inset-0', 'z-[9999]', 'rounded-none');
                chatCol.classList.add('lg:col-span-2', 'rounded-2xl');
                chatCol.style.backgroundColor = '';
                chatCol.style.padding = '';
                if(btnSpan) btnSpan.textContent = 'View Full Chat';
                if(btnIcon) btnIcon.textContent = 'fullscreen';
                if(floatingExitBtn) floatingExitBtn.classList.add('hidden');
                if(mainWrapper) mainWrapper.style.overflow = '';
                
                // Move back inside the grid container
                if(gridContainer) gridContainer.insertBefore(chatCol, detailsCol);
            } else {
                // Full Chat View
                detailsCol.style.display = 'none';
                chatCol.classList.remove('lg:col-span-2', 'lg:col-span-3', 'rounded-2xl');
                chatCol.classList.add('fixed', 'inset-0', 'z-[9999]', 'rounded-none');
                chatCol.style.backgroundColor = 'var(--fiori-page-bg)';
                chatCol.style.padding = '1rem'; 
                if(btnSpan) btnSpan.textContent = 'Exit Full Chat';
                if(btnIcon) btnIcon.textContent = 'fullscreen_exit';
                if(floatingExitBtn) floatingExitBtn.classList.remove('hidden');
                if(mainWrapper) mainWrapper.style.overflow = 'visible';
                
                // Append to body root to completely break out of CSS transform containing blocks
                document.body.appendChild(chatCol);
            }
        } catch(e) {
            console.error('Fullscreen toggle error:', e);
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

            // Optional: If you want to also force-refresh the metadata sidebar on every single message (to update "Last Updated"), you can call this:
            // fetchSidebarUpdate();


                let msgClass = 'msg-client';
                let avatarIcon = 'person';
                let senderName = data.sender_name || 'User';
                let roleLabel = 'Client';

                let isRight = false;
                if (data.is_bot) {
                    msgClass = 'msg-bot';
                    avatarIcon = 'robot_2';
                    senderName = 'HRWeb Bot';
                    roleLabel = 'Automated';
                } else if (data.sender_role === 'superadmin' || data.sender_role === 'admin') {
                    msgClass = 'msg-superadmin';
                    avatarIcon = 'shield_person';
                    roleLabel = 'Administrator';
                    isRight = false;
                } else if (['tsr', 'tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it'].includes(data.sender_role)) {
                    msgClass = 'msg-staff';
                    avatarIcon = 'support_agent';
                    roleLabel = 'Support Team';
                    isRight = true;
                }

                if(container) {
                    const div = document.createElement('div');
                    div.className = `msg-row ${isRight ? 'msg-row--right' : ''}`;
                    div.innerHTML = `
                        <div class="msg-avatar" style="background:var(--fiori-surface); color:var(--fiori-blue); border:1px solid var(--fiori-border);">
                            <span class="material-symbols-outlined text-[20px]">${avatarIcon}</span>
                        </div>
                        <div class="msg-content ${msgClass}">
                            <div class="flex items-center justify-between mb-1 ${isRight ? 'flex-row-reverse' : ''}">
                                <span class="text-[10px] font-bold uppercase tracking-widest px-1" style="color:var(--fiori-text-muted);">
                                    ${senderName} <span class="opacity-50 inline-block ml-1">[${roleLabel}]</span>
                                </span>
                                <span class="text-[9px]" style="color:var(--fiori-text-muted);">${data.time || 'Just now'}</span>
                            </div>
                            <div class="msg-text text-sm" style="color:var(--fiori-text-base);">
                                ${(() => {
                                    let parsedMsg = data.message;
                                    if (data.is_bot) {
                                        parsedMsg = parsedMsg.replace(/\[(.*?)\]\((.*?)\)/g, (match, p1, p2) => {
                                            let url = p2.trim();
                                            if (!/^https?:\/\//i.test(url)) {
                                                url = 'https://' + url.replace(/^\/+/, '');
                                            }
                                            return '<a href="' + url + '" target="_blank" class="text-blue-500 underline font-semibold hover:text-blue-700 w-full truncate inline-block">' + p1 + '</a>';
                                        });
                                        return parsedMsg.replace(/\n/g, '<br>');
                                    }
                                    return parsedMsg.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\n/g, "<br>");
                                })()}
                            </div>
                            ${data.attachments && data.attachments.length > 0 ? `
                                <div class="mt-3 pt-3 border-t border-gray-100/50">
                                    <div class="flex flex-wrap gap-2">
                                        ${data.attachments.map(file => `
                                            <div class="relative group w-20 h-20">
                                                <img src="${BASE_URL}/uploads/tickets/${file}" 
                                                     class="w-full h-full object-cover rounded-lg shadow-sm cursor-zoom-in border border-gray-100/50" 
                                                     onclick="window.open(this.src, '_blank')">
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            ` : ''}
                            ${data.external_links && data.external_links.length > 0 ? `
                                <div class="mt-2 space-y-1">
                                    ${data.external_links.map(link => `
                                        <a href="${link}" target="_blank" class="flex items-center gap-2 p-1.5 rounded bg-white/50 border border-gray-100/30 hover:bg-white transition-colors group">
                                            <span class="material-symbols-outlined text-[14px] text-blue-500">link</span>
                                            <span class="text-[10px] font-medium text-gray-600 truncate flex-1">${link}</span>
                                            <span class="material-symbols-outlined text-[12px] text-gray-300">open_in_new</span>
                                        </a>
                                    `).join('')}
                                </div>
                            ` : ''}
                        </div>
                    `;
                    container.appendChild(div);
                    container.scrollTop = container.scrollHeight;
                }
        });

        socket.on('global_ticket_change', function(data) {
            fetchSidebarUpdate();
        });

        function fetchSidebarUpdate() {
            fetch(window.location.href).then(r => r.text()).then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                
                // Refresh Sidebar Metadata
                const newSidebar = doc.querySelector('.fiori-card.p-6.sticky.top-8');
                const currentSidebar = document.querySelector('.fiori-card.p-6.sticky.top-8');
                if (newSidebar && currentSidebar) {
                    currentSidebar.innerHTML = newSidebar.innerHTML;
                }

                // Refresh Header Badges (Closure status, tags)
                const newHeaderActions = doc.querySelector('.flex.items-center.gap-3');
                const currentHeaderActions = document.querySelector('.flex.items-center.gap-3');
                if (newHeaderActions && currentHeaderActions) {
                    currentHeaderActions.innerHTML = newHeaderActions.innerHTML;
                }
            });
        }
        
        // --- NEW: TSR REPLY HELPERS ---
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

        let replyFilesDT = new DataTransfer();

        window.previewReplyAttachments = (input) => {
            if (input.files && input.files.length > 0) {
                Array.from(input.files).forEach(file => {
                    replyFilesDT.items.add(file);
                });
                input.files = replyFilesDT.files;
            }
            renderReplyAttachments();
        };

        window.removeReplyAttachment = (indexToDel) => {
            const newDT = new DataTransfer();
            Array.from(replyFilesDT.files).forEach((file, index) => {
                if (index !== indexToDel) newDT.items.add(file);
            });
            replyFilesDT = newDT;
            document.getElementById('reply-attachments').files = replyFilesDT.files;
            renderReplyAttachments();
        };

        window.renderReplyAttachments = () => {
            const preview = document.getElementById('reply-attachments-preview');
            preview.innerHTML = '';
            if (replyFilesDT.files.length > 0) {
                Array.from(replyFilesDT.files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const div = document.createElement('div');
                        div.className = 'relative group w-12 h-12';
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-full object-cover rounded-lg border border-blue-100 shadow-sm">
                            <button type="button" onclick="removeReplyAttachment(${index})" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full p-0.5 shadow-md flex items-center justify-center hover:bg-red-600 transition-colors cursor-pointer z-10">
                                <span class="material-symbols-outlined text-[10px]">close</span>
                            </button>
                        `;
                        preview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            }
        };

        // AJAX Form Submission to prevent reload
        const replyForm = document.getElementById('reply-form');
        if (replyForm) {
            replyForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const msgInput = document.getElementById('reply-message');
                const message = msgInput.value.trim();
                const attachmentInput = document.getElementById('reply-attachments');
                const linkInputs = document.querySelectorAll('input[name="external_links[]"]');
                
                const hasFiles = attachmentInput.files && attachmentInput.files.length > 0;
                const hasLinks = Array.from(linkInputs).some(input => input.value.trim() !== '');

                if (!message && !hasFiles && !hasLinks) return;
                
                // Optimistically append the message to UI
                const now = new Date();
                const time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
                const div = document.createElement('div');
                div.className = `msg-row msg-row--right`;
                div.innerHTML = `
                    <div class="msg-avatar" style="background:var(--fiori-surface); color:var(--fiori-blue); border:1px solid var(--fiori-border);">
                        <span class="material-symbols-outlined text-[20px]">support_agent</span>
                    </div>
                    <div class="msg-content msg-staff">
                        <div class="flex items-center justify-between mb-1 flex-row-reverse">
                            <span class="text-[10px] font-bold uppercase tracking-widest px-1" style="color:var(--fiori-text-muted);">
                                You <span class="opacity-50 inline-block ml-1">[Staff]</span>
                            </span>
                            <span class="text-[9px]" style="color:var(--fiori-text-muted);">${time}</span>
                        </div>
                        <div class="msg-text text-sm" style="color:var(--fiori-text-base);">
                            ${message.replace(/\n/g, '<br>')}
                        </div>
                    </div>
                `;
                container.appendChild(div);
                container.scrollTop = container.scrollHeight;
                
                // Submit via AJAX
                const fd = new FormData(replyForm);
                fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                
                msgInput.value = ''; // clear textarea
                
                try {
                    await fetch(replyForm.action, {
                        method: 'POST',
                        body: fd,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    
                    // Clear extras on success
                    document.getElementById('reply-attachments-preview').innerHTML = '';
                    document.getElementById('reply-links-container').innerHTML = '';
                    attachmentInput.value = '';
                    if (!document.getElementById('reply-extras').classList.contains('hidden')) {
                        toggleReplyExtras();
                    }
                } catch(err) {
                    console.error('Failed to send reply', err);
                }
            });
        }
    }
</script>
<?= $this->endSection() ?>
