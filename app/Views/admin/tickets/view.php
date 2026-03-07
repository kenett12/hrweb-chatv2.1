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
    .msg-row { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; width: 100%; animation: fadeIn 0.15s ease both; }

    /* ─── Avatar ─────────────────────────────────────────── */
    .msg-avatar { width: 36px; height: 36px; border-radius: 4px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    
    .msg-avatar.invisible { visibility: hidden; }

    /* ─── Content Area ───────────────────────────────────── */
    .msg-content { flex: 1; display: flex; flex-direction: column; gap: 4px; padding: 12px 16px; border-radius: 4px; font-size: 0.875rem; line-height: 1.5; border: 1px solid var(--fiori-border, #e5e7eb); }
    
    .msg-client { background: var(--fiori-surface, #ffffff); border-left: 3px solid var(--fiori-blue, #0a6ed1); }
    .msg-bot { background: var(--fiori-surface, #ffffff); border-left: 3px solid var(--fiori-neutral, #89919a); color: var(--fiori-text-secondary, #556b82); }
    .msg-staff { background: var(--fiori-blue-light, #eef5fc); border-left: 3px solid var(--fiori-blue, #0a6ed1); }
    .msg-superadmin { background: #f5f3ff; border-left: 3px solid #6366f1; border-color: #ddd6fe; }

    .msg-row--right {
        flex-direction: row-reverse;
        align-self: flex-end;
        margin-left: auto;
    }

    .msg-row--right .msg-content {
        border-left: 1px solid var(--fiori-border, #e5e7eb);
        border-right: 3px solid var(--fiori-blue, #0a6ed1);
    }

    .msg-row--right .msg-bot { border-right-color: var(--fiori-neutral, #89919a); }
    .msg-row--right .msg-superadmin { border-right-color: #6366f1; border-color: #ddd6fe; }

    .msg-time { font-size: 10px; color: #94a3b8; font-weight: 600; margin-top: 4px; }

    #thread-container { background: #f8fafc; }



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

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="fiori-title flex items-center gap-3">
            Ticket #<?= $ticket['id'] ?>: <?= esc($ticket['subject']) ?>
            <span class="fiori-status fiori-status--neutral"><?= $ticket['status'] ?></span>
        </h1>
        <div class="text-sm font-semibold tracking-wider mt-1" style="color:var(--fiori-text-secondary);">
            Client: <span style="color:var(--fiori-text-base);"><?= esc($ticket['client_name']) ?></span> | 
            Assigned TSR: <span style="color:var(--fiori-text-base);"><?= esc($ticket['staff_name'] ?? 'Unassigned') ?></span> | 
            Superadmin: <span style="color:var(--fiori-text-base);"><?= esc($ticket['superadmin_name'] ?? 'None') ?></span>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <button type="button" onclick="toggleFullChat()" id="toggle-chat-btn" class="btn btn-outline flex items-center gap-2">
            <span class="material-symbols-outlined text-[16px]">fullscreen</span>
            <span>View Full Chat</span>
        </button>
        <a href="<?= base_url('superadmin/tickets') ?>" class="btn btn-outline" style="border-color:transparent; background:var(--fiori-border);">Back to Queue</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[calc(100vh-240px)] transition-all duration-300">
    <!-- Thread Panel -->
    <div id="chat-column" class="lg:col-span-2 transition-all duration-300 h-full min-h-0">
        <div class="fiori-card flex flex-col overflow-hidden h-full relative" style="padding:0;">
            
            <!-- Floating Exit Fullscreen Button -->
            <button type="button" onclick="toggleFullChat()" id="floating-exit-btn" class="hidden absolute top-3 right-3 z-[110] btn btn-outline bg-white flex items-center gap-2 shadow-md" style="border-color:var(--fiori-border);">
                <span class="material-symbols-outlined text-[16px]">fullscreen_exit</span>
                <span>Exit Full Chat</span>
            </button>
            
            <!-- Superadmin Monitoring Details -->
            <div class="bg-indigo-50 text-indigo-700 text-xs font-bold uppercase tracking-widest py-2 text-center relative z-10 w-full flex-shrink-0 shadow-sm border-b border-indigo-100 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[14px]">shield_person</span>
                3-Way Group Chat Active
            </div>
            
            <!-- Conversation Area -->
            <div class="chat-container flex-1 overflow-y-auto p-6 flex flex-col gap-4" id="thread-container" style="scroll-behavior: smooth; background:var(--fiori-page-bg);">
            
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
                
                // POV: Admins on Right, TSR/Client/Bot on Left
                $isRight = $isSuper;
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

                <div class="msg-row <?= $isRight ? 'msg-row--right' : '' ?> <?= $isNewGroup ? 'mt-6' : 'mt-1' ?>">
                    <!-- Avatar -->
                    <div class="msg-avatar <?= !$isNewGroup ? 'invisible' : '' ?>" style="background:var(--fiori-surface, #ffffff); color:var(--fiori-blue, #0a6ed1); border:1px solid var(--fiori-border, #e5e7eb);">
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

                    <?php 
                        $msgClass = $isSuper ? 'msg-superadmin' : ($isStaff ? 'msg-staff' : 'msg-client');
                        if($isBot) $msgClass = 'msg-bot';
                    ?>
                    
                    <div class="msg-content <?= $msgClass ?>">
                        <?php if ($isNewGroup): ?>
                            <div class="flex items-center justify-between mb-1 <?= $isRight ? 'flex-row-reverse' : '' ?>">
                                <span class="text-[10px] font-bold uppercase tracking-widest px-1 <?= $isSuper ? 'text-indigo-600' : ($isStaff ? 'text-amber-600' : 'text-emerald-600') ?>">
                                    <?php 
                                        if ($isBot) {
                                            echo 'HRWeb Bot';
                                        } elseif ($isMe) {
                                            echo 'You <span class="opacity-50 inline-block ml-1">[' . ($isSuper ? 'Administrator' : 'Staff') . ']</span>';
                                        } else {
                                            $label = $isSuper ? 'Administrator' : ($isStaff ? 'Support Team' : 'Client');
                                            echo esc($msg['username'] ?? $label) . " <span class='opacity-50 inline-block ml-1'>[{$label}]</span>";
                                        }
                                    ?>
                                </span>
                                <span class="text-[9px]" style="color:var(--fiori-text-muted, #89919a);"><?= date('h:i A', strtotime($msg['created_at'])) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($msg['is_initial']) && $msg['is_initial']): ?>
                            <div class="mb-2 text-[10px] font-bold text-emerald-600 uppercase tracking-widest border-b border-emerald-50 pb-1.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">description</span> Initial Request
                            </div>
                        <?php endif; ?>

                        <div class="msg-text text-sm" <?= $isBot ? 'onclick="handleImageClick(event)"' : '' ?> style="color:var(--fiori-text-base, #1d2d3e);">
                            <?php if($isBot): 
                                $parsedMsg = html_entity_decode($msg['message']);
                                $parsedMsg = preg_replace_callback('/\[(.*?)\]\((.*?)\)/', function($m) {
                                    $url = trim($m[2]);
                                    if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
                                        $url = "https://" . ltrim($url, '/');
                                    }
                                    return '<a href="' . $url . '" target="_blank" class="text-blue-500 underline font-semibold hover:text-blue-700">' . $m[1] . '</a>';
                                }, $parsedMsg);
                                echo nl2br($parsedMsg);
                            else: ?>
                                <?= nl2br(esc($msg['message'])) ?>
                            <?php endif; ?>
                        </div>

                        <?php 
                            $msgAttachments = !empty($msg['attachments']) ? json_decode($msg['attachments'], true) : [];
                            $msgLinks = !empty($msg['external_links']) ? json_decode($msg['external_links'], true) : [];
                            $isInitial = isset($msg['is_initial']) && $msg['is_initial'];
                        ?>

                        <?php if ($isInitial): ?>
                            <?php 
                                $ticketAttachments = !empty($ticket['attachments']) ? json_decode($ticket['attachments'], true) : [];
                                $allInitialAttachments = $ticketAttachments;
                                if (!empty($ticket['attachment']) && !in_array($ticket['attachment'], $allInitialAttachments)) {
                                    array_unshift($allInitialAttachments, $ticket['attachment']);
                                }
                                $ticketLinks = !empty($ticket['external_links']) ? json_decode($ticket['external_links'], true) : [];
                            ?>
                            <?php if (!empty($allInitialAttachments)): ?>
                                <div class="mt-3 pt-3 border-t border-emerald-50/50">
                                    <p class="text-[9px] font-bold text-emerald-600/50 uppercase tracking-widest mb-2">Evidence Gallery</p>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($allInitialAttachments as $file): ?>
                                            <?php 
                                                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            ?>
                                            <?php if ($isImage): ?>
                                                <div class="relative group w-20 h-20">
                                                    <img src="<?= base_url('uploads/tickets/' . $file) ?>" class="w-full h-full object-cover rounded-lg shadow-sm cursor-zoom-in border border-emerald-100/50" onclick="window.open(this.src, '_blank')">
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="flex flex-col gap-1 mt-2">
                                        <?php foreach ($allInitialAttachments as $file): ?>
                                            <a href="<?= base_url('uploads/tickets/' . $file) ?>" target="_blank" class="text-[9px] font-bold text-blue-600 hover:underline flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[12px]">attachment</span> <?= esc($file) ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($ticketLinks)): ?>
                                <div class="mt-3 pt-3 border-t border-emerald-50/50">
                                    <p class="text-[9px] font-bold text-emerald-600/50 uppercase tracking-widest mb-2">External Resources</p>
                                    <div class="space-y-1">
                                        <?php foreach ($ticketLinks as $link): ?>
                                            <a href="<?= esc($link) ?>" target="_blank" class="flex items-center gap-2 p-1.5 rounded bg-emerald-50/30 border border-emerald-100/20 hover:bg-blue-50 transition-colors group">
                                                <span class="material-symbols-outlined text-[14px] text-blue-500">link</span>
                                                <span class="text-[10px] font-medium text-gray-600 truncate flex-1"><?= esc($link) ?></span>
                                                <span class="material-symbols-outlined text-[12px] text-gray-300">open_in_new</span>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if (!empty($msgAttachments)): ?>
                                <div class="mt-3 pt-3 border-t border-gray-100/50">
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($msgAttachments as $file): ?>
                                            <div class="relative group w-20 h-20">
                                                <img src="<?= base_url('uploads/tickets/' . $file) ?>" 
                                                     class="w-full h-full object-cover rounded-lg shadow-sm cursor-zoom-in border border-gray-100/50" 
                                                     onclick="window.open(this.src, '_blank')">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($msgLinks)): ?>
                                <div class="mt-2 space-y-1">
                                    <?php foreach ($msgLinks as $link): ?>
                                        <a href="<?= esc($link) ?>" target="_blank" class="flex items-center gap-2 p-1.5 rounded bg-white/50 border border-gray-100/30 hover:bg-white transition-colors group">
                                            <span class="material-symbols-outlined text-[14px] text-blue-500">link</span>
                                            <span class="text-[10px] font-medium text-gray-600 truncate flex-1"><?= esc($link) ?></span>
                                            <span class="material-symbols-outlined text-[12px] text-gray-300">open_in_new</span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
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
                        <form id="reply-form" class="flex-1" action="<?= base_url('superadmin/tickets/reply/' . $ticket['id']) ?>" method="post">
                            <textarea name="message" id="reply-message"
                                class="fiori-input w-full" 
                                style="height:80px; resize:none;"
                                placeholder="Write a message to the group..."
                                onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); this.form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true })); }"></textarea>
                            <div class="flex justify-between items-center mt-3">
                                <span class="text-xs text-gray-400 font-medium italic"><span class="text-indigo-500 font-bold">Note:</span> Your message will be visible to everyone.</span>
                                <button type="submit" class="btn btn-accent flex items-center gap-2">
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

    <!-- Ticket Meta Panel -->
    <div id="details-column" class="transition-opacity duration-300 hidden lg:block h-full">
        <div class="fiori-card p-0 flex flex-col h-full overflow-hidden">
            <div class="fiori-card__header flex-shrink-0">
                <div>
                    <h2 class="fiori-card__title">Ticket Information</h2>
                </div>
            </div>
            
            <div class="fiori-card__content flex-1 overflow-y-auto space-y-5" style="padding-top:0;">
                <div class="pb-4 border-b" style="border-color:var(--fiori-border);">
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--fiori-text-secondary);">Registration Status</label>
                    <span class="fiori-status fiori-status--neutral"><?= $ticket['status'] ?></span>
                </div>

                <div class="pb-4 border-b" style="border-color:var(--fiori-border);">
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--fiori-text-secondary);">Ticket Number</label>
                    <p class="text-sm font-semibold" style="color:var(--fiori-text-base);"><?= esc($ticket['ticket_number']) ?></p>
                </div>

                <div class="pb-4 border-b" style="border-color:var(--fiori-border);">
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--fiori-text-secondary);">Client Name</label>
                    <p class="text-sm font-semibold" style="color:var(--fiori-text-base);"><?= esc($ticket['client_name']) ?></p>
                </div>

                <div class="pb-4 border-b" style="border-color:var(--fiori-border);">
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--fiori-text-secondary);">Staff Assigned</label>
                    <p class="text-sm font-semibold <?= empty($ticket['staff_name']) ? 'italic opacity-60' : 'text-blue-600' ?>">
                        <?= $ticket['staff_name'] ?? 'Not Claimed' ?>
                    </p>
                </div>

                <?php if (!empty($ticket['superadmin_id'])): ?>
                <div class="pb-4 border-b" style="border-color:var(--fiori-border);">
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1 flex items-center gap-1" style="color:var(--fiori-blue);">
                        <span class="material-symbols-outlined text-[14px]">shield_person</span> Monitoring Superadmin
                    </label>
                    <p class="text-sm font-semibold" style="color:var(--fiori-text-base);"><?= esc($ticket['superadmin_name']) ?></p>
                </div>
                <?php endif; ?>

                <div class="pb-4 border-b" style="border-color:var(--fiori-border);">
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--fiori-text-secondary);">Category / Priority</label>
                    <div class="flex flex-wrap gap-2 items-center">
                        <span class="fiori-status fiori-status--neutral"><?= esc($ticket['category']) ?></span>
                        <?php if (!empty($ticket['subcategory'])): ?>
                            <span class="material-symbols-outlined text-[12px] text-gray-400">arrow_forward_ios</span>
                            <span class="fiori-status fiori-status--neutral" style="background:#f0f7ff; color:#1e72af; border-color:#d0e7ff;"><?= esc($ticket['subcategory']) ?></span>
                        <?php endif; ?>
                        <span class="fiori-status <?= $ticket['priority'] === 'Urgent' || $ticket['priority'] === 'High' ? 'fiori-status--critical' : 'fiori-status--positive' ?>"><?= esc($ticket['priority']) ?></span>
                    </div>
                </div>

                <div class="pb-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--fiori-text-secondary);">Date Created</label>
                    <p class="text-sm font-medium" style="color:var(--fiori-text-base);"><?= date('F d, Y h:i A', strtotime($ticket['created_at'])) ?></p>
                </div>

                <!-- Advanced Management Section -->
                <details class="group pt-4 border-t" style="border-color:var(--fiori-border);">
                    <summary class="text-[10px] font-bold uppercase text-indigo-600 tracking-[0.2em] flex items-center gap-2 cursor-pointer list-none hover:text-indigo-800 transition-colors focus:outline-none">
                        <span class="material-symbols-outlined text-[16px]">settings_suggest</span> Advanced Management
                        <span class="material-symbols-outlined text-[16px] ml-auto group-open:rotate-180 transition-transform">expand_more</span>
                    </summary>
                    <div class="space-y-4 pt-4">

                    <form action="<?= base_url('superadmin/tickets/updateTicket') ?>" method="POST" class="space-y-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $ticket['id'] ?>">
                        
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5 flex justify-between">Due Date <span class="material-symbols-outlined text-[14px] text-slate-300">event</span></label>
                            <input type="date" name="due_date" value="<?= $ticket['due_date'] ? date('Y-m-d', strtotime($ticket['due_date'])) : '' ?>" class="fiori-input text-xs">
                        </div>



                        <button type="submit" class="w-full fiori-button fiori-button--primary !text-[10px] h-9">Apply Management Changes</button>
                    </form>

                    <?php if ($ticket['status'] !== 'Closed'): ?>
                    <form action="<?= base_url('superadmin/tickets/resolveTicket/' . $ticket['id']) ?>" method="POST" class="pt-2 border-t" style="border-color:var(--fiori-border);">
                        <?= csrf_field() ?>
                        <div class="flex gap-2">
                            <button type="submit" name="status" value="Closed" class="flex-1 btn btn-accent !bg-emerald-600 border-emerald-700 hover:!bg-emerald-700 text-center justify-center py-2">
                                <span class="material-symbols-outlined text-[18px]">check_circle</span> Mark Closed
                            </button>
                            <button type="submit" name="status" value="Closed" class="flex-1 btn btn-outline border-slate-300 text-slate-600 hover:bg-slate-100 text-center justify-center py-2">
                                <span class="material-symbols-outlined text-[18px]">lock</span> Close Ticket
                            </button>
                        </div>
                        <?php if ($ticket['close_requested']): ?>
                            <p class="text-[9px] text-amber-600 font-bold uppercase tracking-widest mt-2 text-center animate-pulse">
                                <span class="material-symbols-outlined text-[10px] align-middle">warning</span> TSR has requested closure
                            </p>
                        <?php endif; ?>
                    </form>
                    <?php endif; ?>
                    </div>
                </details>
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
                chatCol.classList.add('lg:col-span-2');
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
                chatCol.classList.remove('lg:col-span-2', 'lg:col-span-3');
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
            const isRight = isSuper; // Admin POV: Admin/Superadmin on Right

            if (isNewGroup) {
                const row = document.createElement('div');
                row.className = `msg-row ${isRight ? 'msg-row--right' : ''} mt-6`;
                
                const avatarIcon = isBot ? 'robot_2' : (isSuper ? 'shield_person' : (isRight ? 'support_agent' : 'person'));
                
                const senderColor = isSuper ? 'text-indigo-600' : (isStaff ? 'text-amber-600' : 'text-emerald-600');
                const roleLabel = isSuper ? 'Administrator' : (isStaff ? 'Support Team' : 'Client');
                const displayName = isBot ? 'HRWeb Bot' : (isMe ? 'You <span class="opacity-50 inline-block ml-1">['+roleLabel+']</span>' : (data.sender_name || 'User') + ' <span class="opacity-50 inline-block ml-1">['+roleLabel+']</span>');

                let msgClass = 'msg-client';
                if(isBot) msgClass = 'msg-bot';
                else if(isSuper) msgClass = 'msg-superadmin';
                else if(isStaff) msgClass = 'msg-staff';

                row.innerHTML = `
                    <div class="msg-avatar" style="background:var(--fiori-surface, #ffffff); color:var(--fiori-blue, #0a6ed1); border:1px solid var(--fiori-border, #e5e7eb);">
                        <span class="material-symbols-outlined text-xl">${avatarIcon}</span>
                    </div>
                    <div class="msg-content ${msgClass}">
                        <div class="flex items-center justify-between mb-1 ${isRight ? 'flex-row-reverse' : ''}">
                            <span class="text-[10px] font-bold uppercase tracking-widest px-1 ${senderColor}">
                                ${displayName}
                            </span>
                            <span class="text-[9px]" style="color:var(--fiori-text-muted, #89919a);">${data.time || 'Just now'}</span>
                        </div>
                        <div class="msg-text text-sm" style="color:var(--fiori-text-base, #1d2d3e);">
                            ${(() => {
                                let parsedMsg = data.message;
                                if (isBot) {
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
                container.appendChild(row);
            } else {
                const rows = container.querySelectorAll('.msg-row');
                const lastRow = rows[rows.length - 1];
                const contentArea = lastRow.querySelector('.msg-content');

                // Append the incoming message segment under the existing content wrapper without the header
                const newSegment = document.createElement('div');
                newSegment.className = "mt-2 pt-2 border-t border-gray-100 flex flex-col gap-1";
                newSegment.innerHTML = `
                    <div class="msg-text text-sm" style="color:var(--fiori-text-base, #1d2d3e);">
                        ${(() => {
                            let parsedMsg = data.message;
                            if (isBot) {
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
                        <div class="mt-2 pt-2 border-t border-gray-100/30">
                            <div class="flex flex-wrap gap-1">
                                ${data.attachments.map(file => `
                                    <div class="relative group w-12 h-12">
                                        <img src="${BASE_URL}/uploads/tickets/${file}" 
                                             class="w-full h-full object-cover rounded-md shadow-xs cursor-zoom-in border border-gray-100/30" 
                                             onclick="window.open(this.src, '_blank')">
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                    ${data.external_links && data.external_links.length > 0 ? `
                        <div class="mt-1 space-y-0.5">
                            ${data.external_links.map(link => `
                                <a href="${link}" target="_blank" class="flex items-center gap-1.5 p-1 rounded hover:bg-gray-50 transition-colors group">
                                    <span class="material-symbols-outlined text-[12px] text-blue-500">link</span>
                                    <span class="text-[9px] font-medium text-gray-500 truncate flex-1">${link}</span>
                                </a>
                            `).join('')}
                        </div>
                    ` : ''}
                    <div class="text-[9px] flex justify-end" style="color:var(--fiori-text-muted, #89919a);">${data.time || 'Just now'}</div>
                `;
                contentArea.appendChild(newSegment);
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

        socket.on('global_ticket_change', function(data) {
            // If a ticket is closed or modified, fetch the latest HTML and swap out the sidebar and title area
            fetch(window.location.href).then(r => r.text()).then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                
                // Refresh Sidebar Metadata
                const newSidebar = doc.querySelector('.fiori-card.p-6.sticky.top-8');
                const currentSidebar = document.querySelector('.fiori-card.p-6.sticky.top-8');
                if (newSidebar && currentSidebar) {
                    currentSidebar.innerHTML = newSidebar.innerHTML;
                }

                // Refresh Header Badges/Buttons (Closure status, tags)
                const newHeaderActions = doc.querySelector('.flex.items-center.gap-3');
                const currentHeaderActions = document.querySelector('.flex.items-center.gap-3');
                if (newHeaderActions && currentHeaderActions) {
                    currentHeaderActions.innerHTML = newHeaderActions.innerHTML;
                }
            });
        });
        
        // --- NEW: ADMIN REPLY HELPERS ---
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
                const submitBtn = this.querySelector('button[type="submit"]');

                const hasFiles = attachmentInput.files && attachmentInput.files.length > 0;
                const hasLinks = Array.from(linkInputs).some(input => input.value.trim() !== '');

                if (!message && !hasFiles && !hasLinks) return;
                
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
                    
                    // Clear extras on success
                    document.getElementById('reply-attachments-preview').innerHTML = '';
                    document.getElementById('reply-links-container').innerHTML = '';
                    attachmentInput.value = '';
                    if (!document.getElementById('reply-extras').classList.contains('hidden')) {
                        toggleReplyExtras();
                    }
                    
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
