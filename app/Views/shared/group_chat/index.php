<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('styles') ?>
<style>
    /* SAP Fiori Inspired Group Chat Layout */
    .fiori-container {
        display: flex;
        height: calc(100vh - 120px);
        background: #f4f5f6;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Sidebar - Room List */
    .fiori-sidebar {
        width: 320px;
        background: #ffffff;
        border-right: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
    }
    .fiori-sidebar-header {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .fiori-room-item {
        padding: 16px;
        border-bottom: 1px solid #f3f4f6;
        cursor: pointer;
        transition: background 0.2s;
    }
    .fiori-room-item:hover, .fiori-room-item.active {
        background: #f8fafc;
        border-left: 4px solid #0a6ed1;
    }
    .room-name {
        font-weight: 600;
        color: #1f2937;
    }

    /* Main Chat Area */
    .fiori-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #fafafa;
    }
    .fiori-main-header {
        padding: 16px 24px;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .chat-window {
        flex: 1;
        padding: 24px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* Fiori Conversational Threads */
    .thread-item {
        display: flex;
        gap: 12px;
        max-width: 85%;
    }
    .thread-item.me {
        align-self: flex-end;
        flex-direction: row-reverse;
    }
    .thread-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #475569;
        flex-shrink: 0;
    }
    .thread-content {
        background: #ffffff;
        padding: 12px 16px;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }
    .thread-item.me .thread-content {
        background: #eff6ff;
        border-color: #bfdbfe;
    }
    .thread-meta {
        font-size: 0.75rem;
        color: #64748b;
        margin-bottom: 4px;
        display: flex;
        gap: 8px;
    }
    .thread-item.me .thread-meta {
        justify-content: flex-end;
    }

    /* Input Area */
    .fiori-input-area {
        padding: 16px 24px;
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
    }
    .fiori-input-group {
        display: flex;
        gap: 12px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 24px;
        padding: 8px 16px;
        align-items: center;
    }
    .fiori-input-group input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        padding: 8px 0;
    }
    .btn-send {
        background: #0a6ed1;
        color: white;
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-send:hover {
        background: #0856a6;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight" id="pageTitle"><?= $activeTab === 'tickets' ? 'Support Tickets' : ($activeTab === 'manager' ? 'Group Manager' : 'Direct Chats') ?></h1>
            <p class="text-sm text-gray-500 mt-1" id="pageSubtitle"><?= $activeTab === 'tickets' ? 'Resume ticket-based support conversations' : ($activeTab === 'manager' ? 'Superadmin group oversight' : 'Collaborate with team members in real-time') ?></p>
        </div>

        <!-- Fiori-style Tab Segment -->
        <div class="flex bg-gray-100 p-1 rounded-xl border border-gray-200 shadow-sm self-stretch md:self-auto overflow-x-auto">
            <a href="<?= base_url('group-chat?tab=direct') ?>" id="tab-direct" class="flex-none px-5 py-2 rounded-lg text-sm font-bold transition-all duration-200 <?= $activeTab === 'direct' ? 'bg-white text-blue-700 shadow-sm border border-gray-200' : 'text-gray-500 hover:text-gray-700' ?>">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">person</span>
                    Direct
                </div>
            </a>
            <a href="<?= base_url('group-chat?tab=groups') ?>" id="tab-groups" class="flex-none px-5 py-2 rounded-lg text-sm font-bold transition-all duration-200 <?= $activeTab === 'groups' ? 'bg-white text-blue-700 shadow-sm border border-gray-200' : 'text-gray-500 hover:text-gray-700' ?>">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">groups</span>
                    Groups
                </div>
            </a>
            <a href="<?= base_url('group-chat?tab=tickets') ?>" id="tab-tickets" class="flex-none px-5 py-2 rounded-lg text-sm font-bold transition-all duration-200 <?= $activeTab === 'tickets' ? 'bg-white text-blue-700 shadow-sm border border-gray-200' : 'text-gray-500 hover:text-gray-700' ?>">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">confirmation_number</span>
                    Tickets
                </div>
            </a>
            <?php if ($isAdmin): ?>
            <a href="<?= base_url('group-chat?tab=manager') ?>" id="tab-manager" class="flex-none px-5 py-2 rounded-lg text-sm font-bold transition-all duration-200 <?= $activeTab === 'manager' ? 'bg-white text-blue-700 shadow-sm border border-gray-200' : 'text-gray-500 hover:text-gray-700' ?>">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">settings_suggest</span>
                    Manager
                </div>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- CHAT (DIRECT/GROUPS) TAB CONTENT -->
    <div id="content-chat" class="tab-content transition-all duration-300 <?= in_array($activeTab, ['direct', 'groups']) ? '' : 'hidden' ?>">

    <div class="fiori-container">
        <!-- Sidebar -->
        <div class="fiori-sidebar">
            <div class="fiori-sidebar-header">
                <h3 class="font-semibold text-gray-700"><?= $activeTab === 'direct' ? 'Direct Messages' : 'Project Groups' ?></h3>
                <?php if (in_array(session()->get('role'), ['admin', 'superadmin', 'tsr', 'tsr_level_1', 'tsr_level_2'])): ?>
                <button onclick="const m = document.getElementById('createGroupModal'); m.classList.remove('hidden'); m.classList.add('flex');" class="text-[#0a6ed1] hover:bg-blue-50 p-2 rounded-full transition-colors" title="<?= $activeTab === 'direct' ? 'New Message' : 'Create New Group' ?>">
                    <span class="material-symbols-outlined"><?= $activeTab === 'direct' ? 'chat_bubble' : 'group_add' ?></span>
                </button>
                <?php endif; ?>
            </div>
            
            <!-- Quick Search Sidebar -->
            <div class="px-4 py-2 border-bottom">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input type="text" id="roomSearch" onkeyup="filterRoomList()" placeholder="Filter <?= $activeTab === 'direct' ? 'people' : 'groups' ?>..." class="w-full pl-8 pr-4 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs outline-none focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            <div class="overflow-y-auto flex-1 h-full" id="roomList">
                
                <?php 
                    $roomsToDisplay = ($activeTab === 'direct') ? $directRooms : $groupRooms;
                    $pendingToDisplay = ($activeTab === 'groups') ? $pendingRooms : []; 
                ?>

                <div class="px-4 py-2 mt-2">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2"><?= $activeTab === 'direct' ? 'Recent Conversations' : 'Active Groups' ?></div>
                </div>

                <?php if (empty($roomsToDisplay) && empty($pendingToDisplay)): ?>
                    <div class="p-8 text-center text-gray-500 text-sm">
                        No <?= $activeTab === 'direct' ? 'messages' : 'groups' ?> found.
                    </div>
                <?php else: ?>
                    
                    <!-- Render Active Rooms -->
                    <?php foreach ($roomsToDisplay as $room): ?>
                        <div class="fiori-room-item" data-id="<?= $room['id'] ?>" 
                             onclick="loadRoom(<?= $room['id'] ?>, '<?= esc($room['name'] ?? 'Chat') ?>', '<?= !empty($room['room_image']) ? base_url('uploads/group_photos/' . $room['room_image']) : '' ?>')">
                            <div class="flex items-center gap-3">
                                <?php if (!empty($room['room_image'])): ?>
                                    <img src="<?= base_url('uploads/group_photos/' . $room['room_image']) ?>" class="w-10 h-10 rounded-full object-cover">
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                        <span class="material-symbols-outlined"><?= $activeTab === 'direct' ? 'person' : 'forum' ?></span>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="room-name"><?= esc($room['name'] ?? 'Chat') ?></div>
                                    <div class="text-xs text-gray-500 mt-0.5"><?= $activeTab === 'direct' ? 'Online' : 'Click to view' ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Render Pending Rooms (TSR view) - Only for Groups -->
                    <?php foreach ($pendingToDisplay as $room): ?>
                        <div class="fiori-room-item opacity-60 cursor-not-allowed">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 font-bold">
                                    <span class="material-symbols-outlined">pending</span>
                                </div>
                                <div>
                                    <div class="room-name text-gray-600"><?= esc($room['name'] ?? 'Pending Group') ?></div>
                                    <div class="text-[10px] text-amber-600 font-semibold mt-0.5 bg-amber-50 px-1.5 py-0.5 rounded inline-block">Pending Admin Approval</div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                <?php endif; ?>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="fiori-main">
            <!-- Header -->
            <div class="fiori-main-header hidden" id="chatHeader">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div id="activeRoomImageContainer">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                <span class="material-symbols-outlined">forum</span>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-800 leading-tight" id="activeRoomName">Select a <?= $activeTab === 'direct' ? 'Conversation' : 'Group' ?></h2>
                            <div class="text-xs text-emerald-600 font-medium flex items-center gap-1 mt-0.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Live Connection
                            </div>
                        </div>
                    </div>
                    <?php if ($isAdmin): ?>
                    <button onclick="confirmDeleteRoom()" class="text-red-500 hover:bg-red-50 p-2 rounded-full transition-colors" title="Delete Group Permanently">
                        <span class="material-symbols-outlined">delete_forever</span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="flex-1 flex flex-col items-center justify-center text-gray-400">
                <span class="material-symbols-outlined text-6xl mb-4 opacity-50">forum</span>
                <p>Select a group from the sidebar to start chatting</p>
            </div>

            <!-- Messages Window -->
            <div class="chat-window hidden" id="chatWindow">
                <!-- Messages injected here -->
            </div>

            <!-- Input Area -->
            <div class="fiori-input-area hidden" id="inputArea">
                <form id="groupChatForm" onsubmit="sendGroupMessage(event)">
                    <div class="fiori-input-group">
                        <button type="button" class="text-gray-400 hover:text-gray-600 p-1">
                            <span class="material-symbols-outlined">attach_file</span>
                        </button>
                        <input type="text" id="messageInput" placeholder="Type a message to the group..." autocomplete="off" required>
                        <button type="submit" class="btn-send shadow-sm">
                            <span class="material-symbols-outlined" style="font-size: 18px;">send</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div> <!-- End fiori-container -->
    </div> <!-- End content-direct -->

    <!-- TICKETS TAB CONTENT -->
    <div id="content-tickets" class="tab-content transition-all duration-300 <?= $activeTab === 'tickets' ? '' : 'hidden' ?>">
        <!-- Ticket Filter Bar (Copied & Adapted) -->
        <div class="mb-6 bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
            <form id="ticketFilterForm" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                <input type="hidden" name="tab" value="tickets">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Search Tickets</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <input type="text" name="search" value="<?= esc(request()->getGet('search')) ?>" 
                               placeholder="Ticket #, subject..." 
                               class="fiori-input !pl-10 !h-10 text-xs auto-apply-ticket"
                               oninput="clearTimeout(this.delay); this.delay = setTimeout(() => this.form.submit(), 500)">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Status</label>
                    <select name="status" class="fiori-input !h-10 text-xs auto-apply-ticket" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <?php 
                        $statuses = ['Open', 'In Progress', 'Closed'];
                        $currentStatus = request()->getGet('status');
                        if ($currentStatus === null) $currentStatus = 'Open';
                        foreach($statuses as $s): ?>
                            <option value="<?= $s ?>" <?= $currentStatus == $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">From Date</label>
                    <input type="date" name="date_from" value="<?= esc(request()->getGet('date_from')) ?>" class="fiori-input !h-10 text-xs auto-apply-ticket" onchange="this.form.submit()">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">To Date</label>
                    <input type="date" name="date_to" value="<?= esc(request()->getGet('date_to')) ?>" class="fiori-input !h-10 text-xs auto-apply-ticket" onchange="this.form.submit()">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="fiori-button fiori-button--primary !h-10 flex-1">
                        Apply
                    </button>
                    <a href="<?= base_url('group-chat?tab=tickets') ?>" class="fiori-button !bg-slate-50 !text-slate-500 !border-slate-200 !h-10 px-3 flex items-center justify-center" title="Clear Filters">
                        <span class="material-symbols-outlined">refresh</span>
                    </a>
                </div>
            </form>
        </div>

        <?php if (empty($tickets)): ?>
            <div class="fiori-card text-center py-16">
                <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4" style="background:var(--fiori-surface);">
                    <span class="material-symbols-outlined text-[32px]" style="color:var(--fiori-border);">confirmation_number</span>
                </div>
                <h3 class="fiori-card__title text-lg mb-2">No Ticket Conversations</h3>
                <p class="text-sm mb-4" style="color:var(--fiori-text-secondary);">There are no active ticket chats matching your criteria.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($tickets as $t): ?>
                    <?php 
                        $targetUrl = base_url('client/chat/' . $t['id']);
                        if (session()->get('role') !== 'client') {
                            $targetUrl = base_url('tsr/tickets/view/' . $t['id']);
                            if (in_array(session()->get('role'), ['admin', 'superadmin'])) {
                                $targetUrl = base_url('superadmin/tickets/view/' . $t['id']);
                            }
                        }
                    ?>
                    <a href="<?= $targetUrl ?>" 
                       class="fiori-card flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden p-5" style="cursor:pointer; display:flex; text-decoration:none; min-height: 180px;">
                        
                        <div>
                            <div class="flex justify-between items-start mb-3 relative">
                                <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded" style="background:var(--fiori-surface); color:var(--fiori-text-muted);">
                                    <?= $t['ticket_number'] ?: '#'.$t['id'] ?>
                                </span>
                                <?php
                                    if ($t['status'] === 'Closed') echo '<span class="fiori-status fiori-status--neutral">Closed</span>';
                                    elseif ($t['status'] === 'In Progress') echo '<span class="fiori-status fiori-status--information">In Progress</span>';
                                    else echo '<span class="fiori-status fiori-status--warning">' . esc($t['status']) . '</span>';
                                ?>
                            </div>

                            <h3 class="fiori-card__title text-base mb-2 line-clamp-2" style="color:var(--fiori-text-primary);">
                                <?= esc($t['subject'] ?: 'Support Request') ?>
                            </h3>
                            
                            <p class="fiori-card__content text-xs line-clamp-2 mb-4" style="color:var(--fiori-text-secondary); padding:0;">
                                <?= esc($t['description']) ?>
                            </p>
                        </div>

                        <div class="pt-3 border-t flex items-center justify-between mt-auto" style="border-color:var(--fiori-border);">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded flex items-center justify-center shrink-0" style="background:var(--fiori-surface); border:1px solid var(--fiori-border);">
                                    <?php if (empty($t['assigned_to'])): ?>
                                        <span class="material-symbols-outlined text-[14px]" style="color:var(--fiori-text-muted);">smart_toy</span>
                                    <?php else: ?>
                                        <span class="material-symbols-outlined text-[14px]" style="color:var(--fiori-blue);">support_agent</span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-semibold uppercase tracking-wider" style="color:var(--fiori-text-muted);">Handling Agent</span>
                                    <span class="text-[11px] font-medium truncate max-w-[100px]" style="color:var(--fiori-text-primary);">
                                        <?= empty($t['assigned_to']) ? 'HRWeb Bot' : esc($t['staff_name']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="text-right flex flex-col items-end gap-2">
                                <div>
                                    <span class="text-[9px] font-semibold uppercase tracking-wider" style="color:var(--fiori-text-muted);">Created</span>
                                    <div class="text-[10px] font-medium mt-0.5" style="color:var(--fiori-text-secondary);"><?= date('M d, H:i', strtotime($t['created_at'])) ?></div>
                                </div>
                                <div>
                                    <span class="text-[9px] font-semibold uppercase tracking-wider" style="color:var(--fiori-text-muted);">Last Update</span>
                                    <div class="text-[10px] font-medium mt-0.5" style="color:var(--fiori-text-secondary);"><?= date('M d, H:i', strtotime($t['updated_at'])) ?></div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div> <!-- End content-tickets -->

    <?php if ($isAdmin): ?>
    <!-- MANAGER TAB CONTENT -->
    <div id="content-manager" class="tab-content transition-all duration-300 <?= $activeTab === 'manager' ? '' : 'hidden' ?>">
        <!-- MANAGER TAB Content (Filter Bar) -->
        <div class="mb-4 bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
            <form id="chatFilterForm" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <input type="hidden" name="tab" value="manager">
                
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Search Groups</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <input type="text" name="search" value="<?= esc(request()->getGet('search')) ?>" 
                               placeholder="Group name or creator..." 
                               class="fiori-input !pl-10 !h-10 text-xs auto-apply-chat"
                               oninput="clearTimeout(this.delay); this.delay = setTimeout(() => this.form.submit(), 500)">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Status</label>
                    <select name="status" class="fiori-input !h-10 text-xs auto-apply-chat" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="approved" <?= request()->getGet('status') == 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="pending" <?= request()->getGet('status') == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="rejected" <?= request()->getGet('status') == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Category</label>
                    <select name="filter_category" class="fiori-input !h-10 text-xs auto-apply-chat" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <option value="general" <?= request()->getGet('filter_category') == 'general' ? 'selected' : '' ?>>General</option>
                        <option value="confidential" <?= request()->getGet('filter_category') == 'confidential' ? 'selected' : '' ?>>Confidential</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="fiori-button fiori-button--primary !h-10 flex-1">
                        Filter
                    </button>
                    <a href="<?= base_url('group-chat?tab=manager') ?>" class="fiori-button !bg-slate-50 !text-slate-500 !border-slate-200 !h-10 px-3 flex items-center justify-center">
                        <span class="material-symbols-outlined">refresh</span>
                    </a>
                </div>
            </form>
        </div>

        <div class="flex flex-wrap gap-4 mb-6">
            <div class="bg-blue-50/50 border border-blue-100 px-4 py-3 rounded-lg flex items-center gap-3">
                <span class="material-symbols-outlined text-blue-600">forum</span>
                <span class="text-sm font-bold text-blue-800"><?= count($allRoomsForManager) ?> Total Groups</span>
            </div>
            <div class="bg-amber-50/50 border border-amber-100 px-4 py-3 rounded-lg flex items-center gap-3">
                <span class="material-symbols-outlined text-amber-600">pending_actions</span>
                <span class="text-sm font-bold text-amber-800"><?= count(array_filter($allRoomsForManager, fn($r) => $r['approval_status'] === 'pending')) ?> Pending Review</span>
            </div>
        </div>

        <div class="fiori-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="fiori-table">
                    <thead>
                        <tr>
                            <th>Group Details</th>
                            <th>Category & Date</th>
                            <th>Creator</th>
                            <th>Participants</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach ($allRoomsForManager as $room): ?>
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <?php if (!empty($room['room_image'])): ?>
                                        <div class="w-8 h-8 rounded overflow-hidden flex-none ring-1 ring-gray-200">
                                            <img src="<?= base_url('uploads/group_photos/' . $room['room_image']) ?>" class="w-full h-full object-cover">
                                        </div>
                                    <?php else: ?>
                                        <div class="w-8 h-8 rounded flex items-center justify-center text-white text-xs font-semibold flex-none" style="background:var(--fiori-blue); border-radius:4px;">
                                            <?= strtoupper(substr($room['name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="font-medium" style="color:var(--fiori-text-base);"><?= esc($room['name'] ?? 'Unnamed') ?></div>
                                        <div class="text-[10px] font-mono" style="color:var(--fiori-text-muted);">ID: <?= $room['id'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm font-medium" style="color:var(--fiori-text-secondary);"><?= ucfirst(esc($room['category'])) ?></div>
                                <div class="text-[10px]" style="color:var(--fiori-text-muted);"><?= date('M j, Y', strtotime($room['created_at'])) ?></div>
                            </td>
                            <td>
                                <div class="text-sm font-medium" style="color:var(--fiori-text-base);"><?= esc($room['creator_name'] ?: 'System') ?></div>
                                <div class="text-[10px]" style="color:var(--fiori-text-muted);"><?= esc($room['creator_email']) ?></div>
                            </td>
                            <td>
                                <button onclick="viewMembers(<?= $room['id'] ?>, '<?= esc($room['name']) ?>')" class="btn btn-outline" style="height:28px; padding:0 12px; font-size:0.75rem;">
                                    <span class="material-symbols-outlined text-[14px] mr-1">group</span>
                                    <?= $room['member_count'] ?> Participants
                                </button>
                            </td>
                            <td class="text-center">
                                <?php
                                    $statusType = 'neutral';
                                    if ($room['approval_status'] === 'approved') $statusType = 'success';
                                    if ($room['approval_status'] === 'pending') $statusType = 'warning';
                                    if ($room['approval_status'] === 'rejected') $statusType = 'error';
                                ?>
                                <span class="fiori-status fiori-status--<?= $statusType ?>">
                                    <?= strtoupper(esc($room['approval_status'])) ?>
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <?php if ($room['approval_status'] === 'pending'): ?>
                                        <button onclick="handleApproval(<?= $room['id'] ?>, 'approve')" class="w-8 h-8 flex items-center justify-center rounded transition-colors" style="color:var(--fiori-text-muted);" onmouseover="this.style.background='var(--fiori-positive-light)'; this.style.color='var(--fiori-positive)';" onmouseout="this.style.background=''; this.style.color='var(--fiori-text-muted)';" title="Approve">
                                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                        </button>
                                        <button onclick="handleApproval(<?= $room['id'] ?>, 'reject')" class="w-8 h-8 flex items-center justify-center rounded transition-colors" style="color:var(--fiori-text-muted);" onmouseover="this.style.background='var(--fiori-negative-light)'; this.style.color='var(--fiori-negative)';" onmouseout="this.style.background=''; this.style.color='var(--fiori-text-muted)';" title="Reject">
                                            <span class="material-symbols-outlined text-[18px]">cancel</span>
                                        </button>
                                    <?php endif; ?>
                                    <button onclick="deleteRoom(<?= $room['id'] ?>)" class="w-8 h-8 flex items-center justify-center rounded transition-colors" style="color:var(--fiori-text-muted);" onmouseover="this.style.background='var(--fiori-negative-light)'; this.style.color='var(--fiori-negative)';" onmouseout="this.style.background=''; this.style.color='var(--fiori-text-muted)';" title="Permanently Delete">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($allRoomsForManager)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-16">
                                <span class="material-symbols-outlined text-4xl block mb-3" style="color:var(--fiori-border);">forum</span>
                                <p class="text-sm font-medium" style="color:var(--fiori-text-secondary);">No groups found in the system</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('modals') ?>
    <!-- Member Management Modal (Fiori Style) -->
    <div id="memberModal" class="fiori-overlay hidden">
        <div class="fiori-dialog fiori-dialog--lg !max-w-md">
            <div class="fiori-dialog__header">
                <div>
                    <h3 class="fiori-dialog__title" id="modalRoomName">Group Members</h3>
                    <p class="text-[9px] font-bold uppercase tracking-widest" style="color:var(--fiori-text-muted);">Participant Oversight</p>
                </div>
                <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="fiori-dialog__body !p-0 max-h-[70vh] overflow-y-auto scrollbar-hide" id="memberListContainer">
                <!-- Members dynamically injected -->
            </div>
        </div>
    </div>

    <!-- Create Group Modal -->
    <div id="createGroupModal" class="fiori-overlay hidden">
        <div class="fiori-dialog !max-w-md">
            <div class="fiori-dialog__header">
                <h3 class="fiori-dialog__title">Create New Group</h3>
                <button type="button" onclick="document.getElementById('createGroupModal').classList.add('hidden'); document.getElementById('createGroupModal').classList.remove('flex');" class="text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form onsubmit="createGroup(event)" class="fiori-dialog__body">
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Group Category</label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="group_category" value="direct" class="text-blue-600 focus:ring-blue-500 w-4 h-4" <?= $activeTab === 'direct' ? 'checked' : '' ?> onchange="filterMembersByCategory()">
                            <span class="ml-2 text-sm text-gray-700 font-medium">Direct (1:1)</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="group_category" value="general" class="text-blue-600 focus:ring-blue-500 w-4 h-4" <?= $activeTab === 'groups' ? 'checked' : '' ?> onchange="filterMembersByCategory()">
                            <span class="ml-2 text-sm text-gray-700 font-medium">General</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="group_category" value="confidential" class="text-blue-600 focus:ring-blue-500 w-4 h-4" onchange="filterMembersByCategory()">
                            <span class="ml-2 text-sm text-gray-700 font-medium">Confidential</span>
                        </label>
                    </div>
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Group Name</label>
                    <input type="text" id="newGroupName" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-shadow" placeholder="e.g. Project Alpha Team">
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Group Photo (Optional)</label>
                    <input type="file" id="groupPhoto" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Select Members</label>
                    <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-2 bg-gray-50 flex flex-col gap-1">
                        <?php foreach ($allUsers as $u): ?>
                            <label class="member-row flex items-center p-2 hover:bg-white rounded cursor-pointer transition-colors" data-client-role="<?= esc($u['client_role']) ?>">
                                <input type="checkbox" name="members[]" value="<?= $u['id'] ?>" class="member-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                                <span class="ml-3 text-sm text-gray-700 font-medium member-name">
                                    <?= !empty($u['full_name']) ? esc($u['full_name']) : esc($u['email']) ?>
                                    <?php if($u['role'] === 'client'): ?>
                                        <span class="text-xs text-gray-400 ml-1">(<?= esc($u['client_role'] ?? 'Client') ?>)</span>
                                    <?php else: ?>
                                        <span class="text-xs text-blue-500 font-semibold ml-1">[Staff]</span>
                                    <?php endif; ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('createGroupModal').classList.add('hidden'); document.getElementById('createGroupModal').classList.remove('flex');" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm">Create Group</button>
                </div>
            </form>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Socket.IO Client -->
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
    const currentUserId = <?= $currentUserId ?>;
    let currentRoomId = null;
let currentRoomCategory = null; // Tracks category of the active room
    let socket = null;

    // Initialize Socket Connection
    try {
        socket = io("http://localhost:3001", {
            path: '/socket.io',
            transports: ['websocket', 'polling']
        });

        socket.on("connect", () => {
            console.log("🟢 Connected to Group Chat Socket Server");
            socket.emit("user_connected", currentUserId);
        });

        // Listen for incoming group messages
        socket.on("group_chat_message", (data) => {
            console.log("Received Group Message:", data);
            if (currentRoomId && currentRoomId == data.room_id) {
                appendMessageToUI(data);
                scrollToBottom();
            }
        });

    } catch (e) {
        console.error("Socket.IO Initialization Error:", e);
    }

    async function createGroup(e) {
        e.preventDefault();
        const categoryInput = document.querySelector('input[name="group_category"]:checked').value;
        let nameInput = document.getElementById('newGroupName').value;
        const checkboxes = document.querySelectorAll('input[name="members[]"]:checked');
        const memberIds = Array.from(checkboxes).map(cb => cb.value);

        if (memberIds.length === 0) {
            alert("Please select at least one member.");
            return;
        }

        if (categoryInput === 'direct') {
            if (memberIds.length > 1) {
                alert("Direct chats are 1-on-1. Please select only one member.");
                return;
            }
            // Auto-generate name from the selected member
            const memberName = checkboxes[0].closest('.member-row').querySelector('.member-name').innerText.trim().split('(')[0].trim();
            nameInput = memberName;
        } else if (!nameInput) {
            alert("Please enter a group name.");
            return;
        }

        try {
            const formData = new FormData();
            formData.append('group_name', nameInput);
            formData.append('category', categoryInput);
            memberIds.forEach(id => formData.append('members[]', id));
            
            const photoFile = document.getElementById('groupPhoto').files[0];
            if (photoFile) {
                formData.append('group_photo', photoFile);
            }

            const res = await fetch('<?= base_url('group-chat/create') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            const data = await res.json();
            if (data.success) {
                window.location.reload(); // Reload to refresh sidebar
            } else {
                alert(data.error || "Failed to create group.");
            }
        } catch (err) {
            console.error("Create Group Error:", err);
        }
    }


    async function loadRoom(roomId, roomName, roomImageUrl) {
        currentRoomId = roomId;
        
        // Update UI State
        document.getElementById('emptyState').classList.add('hidden');
        document.getElementById('chatHeader').classList.remove('hidden');
        document.getElementById('chatWindow').classList.remove('hidden');
        document.getElementById('inputArea').classList.remove('hidden');
        document.getElementById('activeRoomName').innerText = roomName;

        // Update Header Image
        const imgContainer = document.getElementById('activeRoomImageContainer');
        if (roomImageUrl) {
            imgContainer.innerHTML = `<img src="${roomImageUrl}" class="w-10 h-10 rounded-full object-cover">`;
        } else {
            imgContainer.innerHTML = `
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                    <span class="material-symbols-outlined">forum</span>
                </div>
            `;
        }

        // Highlight Sidebar
        document.querySelectorAll('.fiori-room-item').forEach(el => el.classList.remove('active'));
        const sidebarItem = document.querySelector(`.fiori-room-item[data-id="${roomId}"]`);
        if (sidebarItem) sidebarItem.classList.add('active');

        // Join Socket Room for Real-Time broadcasts
        if (socket) {
            socket.emit("join_group_room", roomId);
        }

        // Fetch History
        try {
            const res = await fetch(`<?= base_url('group-chat/room/') ?>${roomId}`);
            const data = await res.json();
            
            const chatBox = document.getElementById('chatWindow');
            chatBox.innerHTML = ''; // Clear

            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => appendMessageToUI(msg));
                scrollToBottom();
            } else {
                chatBox.innerHTML = '<div class="text-center text-gray-400 mt-10 text-sm">No messages yet. Say hello!</div>';
            }
        } catch (err) {
            console.error("Load Room Error:", err);
        }
    }

    function confirmDeleteRoom() {
        if (!currentRoomId) return;
        deleteRoom(currentRoomId);
    }

    async function sendGroupMessage(e) {
        e.preventDefault();
        if (!currentRoomId) return;

        const input = document.getElementById('messageInput');
        const messageText = input.value.trim();
        if (!messageText) return;

        input.value = ''; // Clear input immediately for UX

        try {
            const formData = new FormData();
            formData.append('room_id', currentRoomId);
            formData.append('message', messageText);

            const res = await fetch('<?= base_url('group-chat/send') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            const data = await res.json();
            if (data.success) {
                const finalMsg = data.message;
                // Append locally
                appendMessageToUI(finalMsg);
                scrollToBottom();

                // Broadcast to Socket natively
                if (socket) {
                    socket.emit("group_chat_message", finalMsg);
                }
            } else {
                alert(data.error || "Failed to send.");
            }
        } catch (err) {
            console.error("Send Message Error:", err);
        }
    }

    function appendMessageToUI(msg) {
        const chatBox = document.getElementById('chatWindow');
        const isMe = parseInt(msg.user_id) === currentUserId;
        
        // Remove empty state text if exists
        if (chatBox.innerHTML.includes('No messages yet')) chatBox.innerHTML = '';

        const initials = (msg.display_name || "?").substring(0, 2).toUpperCase();
        
        let timeStr = "";
        if (msg.created_at) {
            const d = new Date(msg.created_at);
            timeStr = d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        const html = `
            <div class="thread-item ${isMe ? 'me' : ''}">
                <div class="thread-avatar text-xs">${initials}</div>
                <div class="flex flex-col ${isMe ? 'items-end' : 'items-start'}">
                    <div class="thread-meta">
                        <span class="font-semibold text-gray-700">${isMe ? 'You' : msg.display_name}</span>
                        <span>${timeStr}</span>
                    </div>
                    <div class="thread-content text-sm text-gray-800 break-words whitespace-pre-wrap leading-relaxed">${escapeHtml(msg.message)}</div>
                </div>
            </div>
        `;
        chatBox.insertAdjacentHTML('beforeend', html);
    }

    function scrollToBottom() {
        const chatBox = document.getElementById('chatWindow');
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function filterMembersByCategory() {
        const cat = document.querySelector('input[name="group_category"]:checked').value;
        const allowedConfidential = ['EXECOM', 'AUDITOR', 'PAYROLL 1', 'PAYROLL 2'];
        const nameInput = document.getElementById('newGroupName');
        
        // Auto-handle name for Direct
        if (cat === 'direct') {
            nameInput.placeholder = "Auto-generated from member name";
            // nameInput.disabled = true; // We'll handle this in createGroup
        } else {
            nameInput.placeholder = "e.g. Project Alpha Team";
            nameInput.disabled = false;
        }

        document.querySelectorAll('.member-row').forEach(row => {
            const role = row.getAttribute('data-client-role');
            const cb = row.querySelector('.member-checkbox');
            const nameEl = row.querySelector('.member-name');
            
            // If they are not a client (role is empty/null, indicating staff), they are always allowed.
            if (!role) {
                row.style.display = 'flex';
                cb.disabled = false;
                nameEl.classList.remove('opacity-50');
                return;
            }

            if (cat === 'confidential' && !allowedConfidential.includes(role)) {
                // Disable & gray out standard clients
                cb.checked = false;
                cb.disabled = true;
                nameEl.classList.add('opacity-50');
                row.classList.add('cursor-not-allowed', 'bg-gray-100');
            } else {
                // Enable for general, or if they are allowed in confidential
                cb.disabled = false;
                nameEl.classList.remove('opacity-50');
                row.classList.remove('cursor-not-allowed', 'bg-gray-100');
                row.style.display = 'flex';
            }
        });

        // Add event listener for single selection if direct
        document.querySelectorAll('.member-checkbox').forEach(cb => {
            cb.onclick = function() {
                if (cat === 'direct' && this.checked) {
                    // Uncheck others
                    document.querySelectorAll('.member-checkbox').forEach(other => {
                        if (other !== this) other.checked = false;
                    });
                }
            };
        });
    }

    // Initialize list states immediately on load
    filterMembersByCategory();

    function escapeHtml(unsafe) {
        return (unsafe || "").toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Note: switchTab is now mostly handled by server-side tab param for durability, 
    // but we'll keep the JS helper for smooth transitions if needed.
    function switchTab(tab) {
        window.location.href = `<?= base_url('group-chat?tab=') ?>${tab}`;
    }

    async function viewMembers(roomId, name) {
        const modal = document.getElementById('memberModal');
        const container = document.getElementById('memberListContainer');
        document.getElementById('modalRoomName').innerText = name + ' Members';
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        container.innerHTML = '<div class="flex justify-center p-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';

        try {
            const res = await fetch(`<?= base_url('group-chat/members/') ?>${roomId}`);
            const members = await res.json();
            
            let html = `
                <table class="fiori-table w-full">
                    <thead>
                        <tr>
                            <th class="w-2/3 !py-2 !text-[10px]">User</th>
                            <th class="w-1/3 text-right !py-2 !text-[10px]">Role</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            members.forEach(m => {
                const displayName = m.full_name && m.full_name.trim() !== "" ? m.full_name : m.email.split('@')[0];
                const initials = displayName.substring(0,2).toUpperCase();
                html += `
                    <tr>
                        <td class="!py-1.5">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-blue-100 text-blue-700 flex items-center justify-center rounded font-bold text-[10px] ring-1 ring-blue-50 flex-none">${initials}</div>
                                <div class="min-w-0">
                                    <div class="font-bold text-gray-800 text-[11px] truncate">${displayName}</div>
                                    <div class="text-[9px] text-gray-500 truncate">${m.email}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-right !py-1.5">
                            <span class="fiori-status text-[8px] uppercase font-bold ${m.role === 'admin' || m.role === 'superadmin' ? 'fiori-status--information' : 'fiori-status--neutral'}" style="padding: 1px 6px;">
                                ${m.role}
                            </span>
                        </td>
                    </tr>
                `;
            });
            html += '</tbody></table>';
            container.innerHTML = html;
        } catch (e) {
            container.innerHTML = '<p class="text-red-500 text-center text-xs py-4">Failed to load members.</p>';
        }
    }

    function closeModal() {
        const modal = document.getElementById('memberModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    async function handleApproval(roomId, action) {
        if (!confirm(`Are you sure you want to ${action} this group?`)) return;
        
        try {
            const formData = new FormData();
            formData.append('room_id', roomId);
            const res = await fetch(`<?= base_url('group-chat/') ?>${action}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await res.json();
            if (data.success) location.reload();
            else alert(data.error || 'Operation failed.');
        } catch (e) { console.error(e); }
    }

    async function deleteRoom(roomId) {
        if (!confirm('CRITICAL: Permanently delete this group and all its data?')) return;
        
        try {
            const formData = new FormData();
            formData.append('room_id', roomId);
            const res = await fetch('<?= base_url('group-chat/delete') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await res.json();
            if (data.success) location.reload();
            else alert(data.error || 'Deletion failed.');
        } catch (e) { console.error(e); }
    }

    function filterRoomList() {
        const input = document.getElementById('roomSearch');
        const filter = input.value.toLowerCase();
        const items = document.getElementsByClassName('fiori-room-item');
        
        for (let i = 0; i < items.length; i++) {
            const nameEl = items[i].getElementsByClassName('room-name')[0];
            if (!nameEl) continue;
            const name = nameEl.innerText;
            if (name.toLowerCase().indexOf(filter) > -1) {
                items[i].style.display = "";
            } else {
                items[i].style.display = "none";
            }
        }
    }

    // Auto-apply logic is now handled via inline onchange/oninput handlers for better reliability.
</script>
<?= $this->endSection() ?>
