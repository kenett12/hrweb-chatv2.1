<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<!-- SAP Fiori Page Header -->
<div class="fiori-page-header">
    <div>
        <h1 class="fiori-page-title">Global Ticket Directory</h1>
        <p class="fiori-page-subtitle">Read-only oversight of all generated support tickets across all clients</p>
    </div>
</div>

<?php
// Build a URL filter for client_id if present
$clientFilter = request()->getGet('client_id');
?>

<?php if ($clientFilter): ?>
<div class="mb-4 flex items-center gap-3 px-3 py-2 rounded text-sm font-medium" style="background:var(--fiori-blue-light); border:1px solid #b3d4fb; border-radius:4px; color:var(--fiori-blue); max-width:fit-content;">
    <span class="material-symbols-outlined text-[16px]">filter_alt</span>
    Filtered by client ID: <?= (int)$clientFilter ?>
    <a href="<?= base_url('superadmin/tickets') ?>" class="ml-2 underline text-xs" style="color:var(--fiori-blue);">Clear filter</a>
</div>
<?php endif; ?>

<!-- Tab Navigation -->
<div class="fiori-tabs mb-6 overflow-x-auto pb-1">

    <button class="fiori-tab-link <?= request()->getGet('tab') === 'manager' ? '' : (request()->getGet('tab') === 'closing_requests' ? '' : 'active') ?>" onclick="switchTab(event, 'directory')">
        <span class="material-symbols-outlined text-[18px]">list_alt</span>
        Global Directory
    </button>
    <button class="fiori-tab-link <?= request()->getGet('tab') === 'closing_requests' ? 'active' : '' ?>" onclick="switchTab(event, 'closing_requests')">
        <span class="material-symbols-outlined text-[18px]">rule</span>
        Closing Requests
    </button>
    <button class="fiori-tab-link <?= request()->getGet('tab') === 'manager' ? 'active' : '' ?>" onclick="switchTab(event, 'manager')">
        <span class="material-symbols-outlined text-[18px]">settings_suggest</span>
        Ticket Manager
    </button>
</div>

<!-- Tab: Global Directory & Closing Requests -->
<div id="directory" class="tab-content <?= request()->getGet('tab') === 'manager' ? 'hidden' : 'block' ?>">
    <?php if (request()->getGet('tab') !== 'closing_requests'): ?>
    <div class="mb-4 bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
        <form id="filterForm" action="<?= base_url('superadmin/tickets') ?>" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
            <input type="hidden" name="tab" value="<?= esc(request()->getGet('tab') ?: 'directory') ?>" id="current-tab-input">
            
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Search Tickets</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                    <input type="text" name="search" value="<?= esc(request()->getGet('search')) ?>" 
                           placeholder="Ticket #, subject, client..." 
                           class="fiori-input !pl-10 !h-10 text-xs auto-apply">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Status</label>
                <select name="status" class="fiori-input !h-10 text-xs auto-apply">
                    <option value="">All Statuses</option>
                    <?php 
                    $statuses = ['Open', 'In Progress', 'Closed'];
                    foreach($statuses as $s): ?>
                        <option value="<?= $s ?>" <?= request()->getGet('status') == $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Created From</label>
                <input type="date" name="date_from" value="<?= esc(request()->getGet('date_from')) ?>" class="fiori-input !h-10 text-xs auto-apply">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Created To</label>
                <input type="date" name="date_to" value="<?= esc(request()->getGet('date_to')) ?>" class="fiori-input !h-10 text-xs auto-apply">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="fiori-button fiori-button--primary !h-10 flex-1">
                    Apply
                </button>
                <a href="<?= base_url('superadmin/tickets') ?>" class="fiori-button !bg-slate-50 !text-slate-500 !border-slate-200 !h-10 px-3 flex items-center justify-center" title="Clear Filters">
                    <span class="material-symbols-outlined">refresh</span>
                </a>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <div class="fiori-card overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="fiori-table w-full">
                <thead>
                    <tr>
                        <th class="w-16">NO</th>
                        <th class="w-40">DATE/TIME</th>
                        <th class="w-48">Client(s)</th>
                        <th class="w-48">TYPE</th>
                        <th class="w-64">CONCERN(S)</th>
                        <th class="w-40">REFERENCES</th>
                        <th class="w-40">FIXED DATE/TIME</th>
                        <th class="w-40">DUE DATE</th>
                        <th class="w-48">STATUS</th>
                        <th class="w-40">ATTENDED BY</th>
                        <th class="w-72">REMARKS</th>
                        <th class="sticky right-0 bg-white shadow-[-4px_0_8px_rgba(0,0,0,0.05)] w-24 text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="ticket-queue-body">
                    <?php if (!empty($tickets)): ?>
                        <?php foreach ($tickets as $ticket): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="font-mono text-xs text-slate-500">#<?= (int)$ticket['id'] ?></td>
                            <td class="text-xs">
                                <div class="font-medium"><?= date('M d, Y', strtotime($ticket['created_at'])) ?></div>
                                <div class="text-[10px] text-slate-400"><?= date('h:i A', strtotime($ticket['created_at'])) ?></div>
                            </td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-700 text-xs"><?= esc($ticket['client_name'] ?? 'Guest') ?></span>
                                    <span class="text-[10px] text-slate-400"><?= esc($ticket['creator_name'] ?? '') ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded w-fit"><?= esc($ticket['category']) ?></span>
                                    <span class="text-xs text-slate-500"><?= esc($ticket['subcategory'] ?? 'General') ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="max-w-[240px]">
                                    <div class="font-bold text-xs truncate" title="<?= esc($ticket['subject']) ?>"><?= esc($ticket['subject']) ?></div>
                                    <div class="text-[10px] text-slate-400 line-clamp-1 mt-0.5"><?= esc($ticket['description'] ?? '') ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-1.5">
                                    <?php 
                                        $atts = json_decode($ticket['attachments'] ?? '[]', true);
                                        $links = json_decode($ticket['external_links'] ?? '[]', true);
                                        if (empty($atts) && empty($links)) echo '<span class="text-slate-300 text-[10px] italic">None</span>';
                                        
                                        if (!empty($atts)): ?>
                                            <button type="button" onclick="event.stopPropagation(); openAttachmentModal(<?= htmlspecialchars(json_encode($atts)) ?>, '<?= esc($ticket['ticket_number']) ?>')" 
                                                    class="flex items-center gap-0.5 text-blue-500 bg-blue-50 px-1.5 py-0.5 rounded text-[10px] font-bold hover:bg-blue-100 transition-colors cursor-pointer"
                                                    title="Click to view attachments">
                                                <span class="material-symbols-outlined text-[12px]">image</span> <?= count($atts) ?>
                                            </button>
                                        <?php endif; ?>
                                        <?php if (!empty($links)): ?>
                                            <button type="button" onclick="event.stopPropagation(); openLinkModal(<?= htmlspecialchars(json_encode($links)) ?>, '<?= esc($ticket['ticket_number']) ?>')" 
                                                    class="flex items-center gap-0.5 text-purple-500 bg-purple-50 px-1.5 py-0.5 rounded text-[10px] font-bold hover:bg-purple-100 transition-colors cursor-pointer"
                                                    title="Click to view external links">
                                                <span class="material-symbols-outlined text-[12px]">link</span> <?= count($links) ?>
                                            </button>
                                        <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-xs text-slate-500">
                                <?= $ticket['fixed_at'] ? date('M d, Y h:i A', strtotime($ticket['fixed_at'])) : '<span class="text-slate-300 italic">Pending</span>' ?>
                            </td>
                            <td class="text-xs font-medium text-slate-600">
                                <?= $ticket['due_date'] ? date('M d, Y', strtotime($ticket['due_date'])) : '<button onclick="openEditTicketModal('.$ticket['id'].')" class="text-[10px] text-blue-500 hover:underline">Set Due</button>' ?>
                            </td>
                            <td>
                                <?php
                                    $s = $ticket['status'];
                                    $cls = 'fiori-status--neutral';
                                    if ($s === 'Closed') $cls = 'fiori-status--neutral';
                                    elseif ($s === 'In Progress') $cls = 'fiori-status--positive';
                                    elseif ($s === 'Open') $cls = 'fiori-status--information';
                                    else $cls = 'fiori-status--warning';
                                    
                                    if ($ticket['close_requested'] && $s !== 'Closed') {
                                        echo '<div class="flex flex-col gap-1"><span class="fiori-status '.$cls.' whitespace-nowrap w-fit">'.esc($s).'</span>';
                                        echo '<span class="text-[9px] font-bold uppercase text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded animate-pulse whitespace-nowrap w-fit">Review Req.</span></div>';
                                    } else {
                                        echo '<span class="fiori-status '.$cls.' whitespace-nowrap">'.esc($s).'</span>';
                                    }
                                ?>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                        <span class="material-symbols-outlined text-[14px]">person</span>
                                    </div>
                                    <span class="text-xs text-slate-600 font-medium whitespace-nowrap">
                                        <?= (in_array($ticket['status'], ['In Progress','Closed'])) ? esc($ticket['staff_name'] ?? 'Unknown TSR') : '<span class="text-slate-300 italic">Pending</span>' ?>
                                    </span>
                                </div>
                            </td>
                            <!-- Remarks Collapsible -->
                            <td class="align-top">
                                <details class="group bg-slate-50 border border-slate-200 rounded-lg p-1 w-72">
                                    <summary class="text-[10px] font-bold text-slate-500 uppercase cursor-pointer list-none flex justify-between items-center px-2 py-1 hover:text-blue-600 transition-colors">
                                        View Remarks
                                        <span class="material-symbols-outlined text-[14px] group-open:rotate-180 transition-transform">expand_more</span>
                                    </summary>
                                    <div class="mt-2 text-xs text-slate-600 px-2 pb-2 space-y-2 border-t border-slate-200 pt-2">
                                        <?php if($ticket['dev_remarks_1']): ?><div><strong class="block text-[9px] text-slate-400">DEV 1:</strong> <?= esc($ticket['dev_remarks_1']) ?></div><?php endif; ?>
                                        <?php if($ticket['support_remarks']): ?><div><strong class="block text-[9px] text-slate-400">SUPPORT:</strong> <?= esc($ticket['support_remarks']) ?></div><?php endif; ?>
                                        <?php if($ticket['dev_remarks_2']): ?><div><strong class="block text-[9px] text-slate-400">DEV 2:</strong> <?= esc($ticket['dev_remarks_2']) ?></div><?php endif; ?>
                                        <?php if($ticket['reoccurrence_remarks']): ?><div><strong class="block text-[9px] text-slate-400">RE-OCCURRENCE:</strong> <?= esc($ticket['reoccurrence_remarks']) ?></div><?php endif; ?>
                                        <?php if(!$ticket['dev_remarks_1'] && !$ticket['support_remarks'] && !$ticket['dev_remarks_2'] && !$ticket['reoccurrence_remarks']): ?>
                                            <span class="italic text-slate-400 text-[10px]">No remarks added.</span>
                                        <?php endif; ?>
                                    </div>
                                </details>
                            </td>
                            
                            <td class="sticky right-0 bg-white shadow-[-4px_0_8px_rgba(0,0,0,0.05)] text-center">
                                <div class="flex justify-center gap-1 px-2">
                                    <a href="<?= base_url('superadmin/tickets/view/'.$ticket['id']) ?>" class="w-8 h-8 rounded-lg flex items-center justify-center text-blue-500 hover:bg-blue-50 transition-colors" title="View Thread">
                                        <span class="material-symbols-outlined text-[18px]">forum</span>
                                    </a>
                                    <button onclick="openEditTicketModal(<?= htmlspecialchars(json_encode($ticket)) ?>)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Quick Edit Remarks/Due Date">
                                        <span class="material-symbols-outlined text-[18px]">edit_note</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="15" class="py-16 text-center">
                            <span class="material-symbols-outlined text-4xl block mb-3" style="color:var(--fiori-border);">confirmation_number</span>
                            <p class="text-sm font-medium" style="color:var(--fiori-text-secondary);">No tickets found</p>
                            <p class="text-xs mt-1" style="color:var(--fiori-text-muted);">Tickets will appear here once clients submit support requests.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Ticket Manager -->
<div id="manager" class="tab-content hidden">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-bold" style="color:var(--fiori-text-base);">Category Management</h2>
            <p class="text-xs" style="color:var(--fiori-text-secondary);">Manage ticket classification tree (Categories > Subcategories)</p>
        </div>
        <button onclick="openCategoryModal()" class="fiori-button fiori-button--primary flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">add</span>
            New Primary Category
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php foreach ($categories as $cat): ?>
            <div class="fiori-card p-0 flex flex-col h-[320px] group overflow-hidden border-slate-200 hover:border-blue-300 transition-all duration-300 shadow-sm hover:shadow-md bg-white">
                <!-- Category Header -->
                <div class="p-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shadow-sm">
                            <span class="material-symbols-outlined">category</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-slate-800 leading-tight"><?= esc($cat['name']) ?></h3>
                            <p class="text-[10px] text-slate-500 font-medium uppercase tracking-wider">Primary Category</p>
                        </div>
                    </div>
                    <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="openCategoryModal(<?= $cat['id'] ?>, '<?= esc($cat['name']) ?>', '<?= esc($cat['description']) ?>')" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </button>
                        <button onclick="confirmDeleteCategory(<?= $cat['id'] ?>)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </div>

                <!-- Subcategories -->
                <div class="flex-1 p-4 bg-white">
                    <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-50">
                        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Subcategories (<?= count($cat['subcategories']) ?>)</h4>
                        <button onclick="openCategoryModal(null, '', '', <?= $cat['id'] ?>)" class="text-[10px] font-bold text-blue-600 hover:text-blue-700 bg-blue-50 px-2 py-1 rounded-md transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">add</span> Add
                        </button>
                    </div>
                    
                    <div class="space-y-1.5 overflow-y-auto max-h-[180px] scrollbar-thin">
                        <?php if (!empty($cat['subcategories'])): ?>
                            <?php foreach ($cat['subcategories'] as $sub): ?>
                                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-all group/sub">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[14px] text-slate-300">subdirectory_arrow_right</span>
                                        <span class="text-xs font-medium text-slate-600"><?= esc($sub['name']) ?></span>
                                    </div>
                                    <div class="flex gap-1 opacity-0 group-hover/sub:opacity-100 transition-opacity">
                                        <button onclick="openCategoryModal(<?= $sub['id'] ?>, '<?= esc($sub['name']) ?>', '<?= esc($sub['description']) ?>', <?= $cat['id'] ?>)" class="w-6 h-6 rounded-md flex items-center justify-center text-slate-300 hover:text-blue-500 hover:bg-white transition-colors">
                                            <span class="material-symbols-outlined text-[14px]">edit</span>
                                        </button>
                                        <button onclick="confirmDeleteCategory(<?= $sub['id'] ?>)" class="w-6 h-6 rounded-md flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-white transition-colors">
                                            <span class="material-symbols-outlined text-[14px]">delete</span>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="py-6 text-center border-2 border-dashed border-slate-50 rounded-xl">
                                <span class="material-symbols-outlined text-slate-200">folder_open</span>
                                <p class="text-[10px] text-slate-400 font-medium mt-1">No subcategories yet</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Floating Closing Requests Overlay -->
<?php if (!empty($pending_closures)): ?>
<div id="closing-requests-overlay" class="fixed bottom-6 right-6 z-[2000] w-80 bg-white rounded-xl shadow-2xl border border-slate-200 transition-all duration-300 flex flex-col max-h-[400px]">
    <div class="flex items-center justify-between p-3 border-b border-slate-100 bg-amber-50 rounded-t-xl cursor-pointer" onclick="toggleOverlay()">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-600 animate-pulse text-[18px]">rule</span>
            <span class="text-xs font-bold text-amber-800 uppercase tracking-wider">Review Requests (<?= count($pending_closures) ?>)</span>
        </div>
        <div class="flex items-center gap-1">
            <button id="overlay-toggle-btn" class="w-6 h-6 rounded hover:bg-amber-100 flex items-center justify-center text-amber-600 transition-colors">
                <span class="material-symbols-outlined text-[18px]">keyboard_arrow_down</span>
            </button>
        </div>
    </div>
    
    <div id="overlay-content" class="flex-1 overflow-y-auto p-2 space-y-2 scrollbar-thin">
        <?php foreach ($pending_closures as $req): ?>
        <a href="<?= base_url('superadmin/tickets/view/' . $req['id']) ?>" class="block p-2 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-all group">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-mono font-bold text-blue-600">
                    #<?= (int)$req['id'] ?><?= $req['ticket_number'] ? ' (' . esc($req['ticket_number']) . ')' : '' ?>
                </span>
                <span class="text-[9px] text-slate-400"><?= date('M d, H:i', strtotime($req['updated_at'])) ?></span>
            </div>
            <div class="text-[11px] font-bold text-slate-700 line-clamp-1 group-hover:text-blue-600 transition-colors"><?= esc($req['subject']) ?></div>
            <div class="text-[10px] text-slate-400 line-clamp-1 italic"><?= esc($req['category']) ?></div>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function toggleOverlay() {
        const overlay = document.getElementById('closing-requests-overlay');
        const content = document.getElementById('overlay-content');
        const btn = document.getElementById('overlay-toggle-btn');
        const icon = btn.querySelector('.material-symbols-outlined');
        
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            overlay.classList.remove('h-auto');
            icon.innerText = 'keyboard_arrow_down';
            sessionStorage.setItem('overlayCollapsed', 'false');
        } else {
            content.classList.add('hidden');
            overlay.classList.add('h-auto');
            icon.innerText = 'keyboard_arrow_up';
            sessionStorage.setItem('overlayCollapsed', 'true');
        }
    }

    // Restore state on load
    window.addEventListener('DOMContentLoaded', () => {
        if (sessionStorage.getItem('overlayCollapsed') === 'true') {
            const content = document.getElementById('overlay-content');
            const overlay = document.getElementById('closing-requests-overlay');
            const btn = document.getElementById('overlay-toggle-btn');
            if (content && overlay && btn) {
                content.classList.add('hidden');
                overlay.classList.add('h-auto');
                btn.querySelector('.material-symbols-outlined').innerText = 'keyboard_arrow_up';
            }
        }
    });
</script>
<?php endif; ?>

<!-- End Tab: Ticket Manager -->

<style>
    .fiori-tabs {
        display: flex;
        gap: 2px;
        background: #f4f6f8;
        padding: 4px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        overflow-x: auto;
        white-space: nowrap;
        scrollbar-width: none; /* Hide scrollbar for Chrome/Safari */
    }
    .fiori-tabs::-webkit-scrollbar {
        display: none;
    }
    .fiori-tab-link {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 16px;
        border: none;
        background: transparent;
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        min-width: fit-content;
    }
    .fiori-tab-link:hover {
        background: rgba(255,255,255,0.5);
        color: #3b82f6;
    }
    .fiori-tab-link.active {
        background: #ffffff;
        color: #2563eb;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
    }
    .scrollbar-thin::-webkit-scrollbar {
        width: 4px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<!-- Category Modal -->
<div id="category-modal" class="fiori-overlay hidden">
    <div class="fiori-dialog">
        <div class="fiori-dialog__header">
            <h3 id="modal-title" class="fiori-dialog__title">New Category</h3>
            <button onclick="closeCategoryModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="fiori-dialog__body">
            <form action="<?= base_url('superadmin/tickets/storeCategory') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="cat-id">
                <input type="hidden" name="parent_id" id="cat-parent-id">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Display Name</label>
                        <input type="text" name="name" id="cat-name" required placeholder="e.g., Billing Support"
                            class="fiori-input">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Description (Optional)</label>
                        <textarea name="description" id="cat-desc" rows="3" placeholder="Describe the purpose of this category..."
                            class="fiori-input h-32"></textarea>
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="closeCategoryModal()" class="flex-1 fiori-button !bg-slate-100 !text-slate-600 hover:!bg-slate-200">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 fiori-button fiori-button--primary">
                        Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Edit Ticket Modal -->
<div id="edit-ticket-modal" class="fiori-overlay hidden">
    <div class="fiori-dialog">
        <div class="fiori-dialog__header">
            <h3 class="fiori-dialog__title">Update Ticket Details</h3>
            <button onclick="closeEditTicketModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="fiori-dialog__body">
            <form action="<?= base_url('superadmin/tickets/updateTicket') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit-ticket-id">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Due Date</label>
                        <input type="date" name="due_date" id="edit-ticket-due" class="fiori-input">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Dev Remarks 1</label>
                            <textarea name="dev_remarks_1" id="edit-ticket-dev1" class="fiori-input h-24 text-xs" placeholder="Primary developer notes..."></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Support Remarks</label>
                            <textarea name="support_remarks" id="edit-ticket-support" class="fiori-input h-24 text-xs" placeholder="TSR/Support notes..."></textarea>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Dev Remarks 2</label>
                            <textarea name="dev_remarks_2" id="edit-ticket-dev2" class="fiori-input h-24 text-xs" placeholder="Secondary developer notes..."></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Re-occurrence Remarks</label>
                            <textarea name="reoccurrence_remarks" id="edit-ticket-reoccurrence" class="fiori-input h-24 text-xs" placeholder="Notes on issue persistence..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="closeEditTicketModal()" class="flex-1 fiori-button !bg-slate-100 !text-slate-600 hover:!bg-slate-200">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 fiori-button fiori-button--primary">
                        Apply Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Attachment Preview Modal -->
<div id="attachment-preview-modal" class="fiori-overlay hidden">
    <div class="fiori-dialog !max-w-2xl">
        <div class="fiori-dialog__header">
            <div>
                <h3 class="fiori-dialog__title">Ticket Attachments</h3>
                <p id="preview-ticket-number" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5"></p>
            </div>
            <button onclick="closeAttachmentModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="fiori-dialog__body">
            <div id="attachment-list" class="grid grid-cols-2 sm:grid-cols-3 gap-4 max-h-[60vh] overflow-y-auto p-1">
                <!-- Attachments dynamically inserted here -->
            </div>
        </div>
        <div class="fiori-dialog__footer flex justify-end p-4 border-t border-slate-100">
            <button onclick="closeAttachmentModal()" class="fiori-button !bg-slate-100 !text-slate-600 hover:!bg-slate-200">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Link Preview Modal -->
<div id="link-preview-modal" class="fiori-overlay hidden">
    <div class="fiori-dialog !max-w-xl">
        <div class="fiori-dialog__header">
            <div>
                <h3 class="fiori-dialog__title">External Resources</h3>
                <p id="preview-link-ticket-number" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5"></p>
            </div>
            <button onclick="closeLinkModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="fiori-dialog__body">
            <div id="link-list" class="space-y-3 max-h-[50vh] overflow-y-auto p-1">
                <!-- Links dynamically inserted here -->
            </div>
        </div>
        <div class="fiori-dialog__footer flex justify-end p-4 border-t border-slate-100">
            <button onclick="closeLinkModal()" class="fiori-button !bg-slate-100 !text-slate-600 hover:!bg-slate-200">
                Close
            </button>
        </div>
    </div>
</div>

<div id="photoModal"
    class="hidden fixed inset-0 z-[3000] bg-black/95 backdrop-blur-md flex items-center justify-center p-4 overflow-hidden"
    onclick="closePhotoModal()">
    <button class="absolute top-6 right-6 text-white hover:rotate-90 transition-transform duration-300">
        <span class="material-symbols-outlined text-4xl">close</span>
    </button>
    <img id="modalImage" src=""
        class="max-w-full max-h-full rounded-lg shadow-2xl object-contain transform scale-95 transition-transform duration-300">
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
    const socket = io('http://localhost:3001');
    socket.on('global_ticket_change', () => {
        // Always refresh the overlay if we receive a change
        fetch(window.location.href).then(r => r.text()).then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            
            // 1. Refresh Overlay
            const newOverlay = doc.querySelector('#closing-requests-overlay');
            const currentOverlay = document.getElementById('closing-requests-overlay');
            if (newOverlay) {
                if (currentOverlay) {
                    currentOverlay.innerHTML = newOverlay.innerHTML;
                    // Restore collapsed state if needed
                    if (sessionStorage.getItem('overlayCollapsed') === 'true') {
                        const content = currentOverlay.querySelector('#overlay-content');
                        if (content) content.classList.add('hidden');
                        currentOverlay.classList.add('h-auto');
                    }
                } else {
                    document.body.appendChild(newOverlay);
                }
            } else if (currentOverlay) {
                currentOverlay.remove();
            }

            // 2. Refresh Directory Table (if active)
            const activeTab = document.querySelector('.fiori-tab-link.active');
            if (activeTab && (activeTab.innerText.trim() === 'Global Directory' || activeTab.innerText.trim() === 'Closing Requests')) {
                const nc = doc.querySelector('#directory');
                if (nc) {
                    document.querySelector('#directory').innerHTML = nc.innerHTML;
                    initAutoApply();
                }
            }
        });
    });

    // --- TAB SWITCHING ---
    function switchTab(evt, tabName) {
        const tablinks = document.getElementsByClassName("fiori-tab-link");
        const tabcontent = document.getElementsByClassName("tab-content");

        // Handle Manager tab (static switch)
        if (tabName === 'manager') {
            for (let i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.add("hidden");
                tabcontent[i].classList.remove("block");
            }
            for (let i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("active");
            }
            document.getElementById('manager').classList.remove("hidden");
            document.getElementById('manager').classList.add("block");
            evt.currentTarget.classList.add("active");
            
            // Update URL without reload
            const url = new URL(window.location.href);
            url.searchParams.set('tab', 'manager');
            window.history.pushState({}, '', url);
            return;
        }

        // Handle Directory & Closing Requests (AJAX switch)
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tabName);
        window.history.pushState({}, '', url);

        // Update active tab UI
        for (let i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }
        evt.currentTarget.classList.add("active");

        // Show loading state (optional, but good for UX)
        const directoryTab = document.getElementById('directory');
        directoryTab.classList.remove("hidden");
        directoryTab.classList.add("block");
        document.getElementById('manager').classList.add("hidden");
        
        // Fetch new content
        fetch(url.toString())
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector('#directory');
                if (newContent) {
                    directoryTab.innerHTML = newContent.innerHTML;
                }
                
                // Also update the overlay if it exists in the response
                const newOverlay = doc.querySelector('#closing-requests-overlay');
                const currentOverlay = document.getElementById('closing-requests-overlay');
                if (newOverlay) {
                    if (currentOverlay) {
                        currentOverlay.innerHTML = newOverlay.innerHTML;
                        // Reset visibility if needed or keep current state?
                        // Let's keep the current minimized/maximized state if possible
                        const content = currentOverlay.querySelector('#overlay-content');
                        if (content && sessionStorage.getItem('overlayCollapsed') === 'true') {
                            content.classList.add('hidden');
                        }
                    } else {
                        // If it didn't exist before but now it does, append it to body or content
                        document.querySelector('main') || document.body.appendChild(newOverlay);
                    }
                } else if (currentOverlay) {
                    currentOverlay.remove();
                }

                initAutoApply();
            })
            .catch(error => console.error('Error switching tab:', error));
    }

    function initAutoApply() {
        let debounceTimer;
        document.querySelectorAll('.auto-apply').forEach(input => {
            const events = input.tagName === 'SELECT' || input.type === 'date' ? ['change'] : ['keyup'];
            events.forEach(action => {
                input.addEventListener(action, () => {
                    clearTimeout(debounceTimer);
                    const delay = (input.tagName === 'INPUT' && input.type === 'text') ? 500 : 0;
                    debounceTimer = setTimeout(() => {
                        const form = document.getElementById('filterForm');
                        if (form) {
                            const formData = new FormData(form);
                            const params = new URLSearchParams(formData);
                            const url = new URL(window.location.href);
                            params.forEach((value, key) => url.searchParams.set(key, value));
                            
                            window.history.pushState({}, '', url);
                            
                            fetch(url.toString())
                                .then(r => r.text())
                                .then(html => {
                                    const parser = new DOMParser();
                                    const doc = parser.parseFromString(html, 'text/html');
                                    const newTable = doc.querySelector('#directory');
                                    if (newTable) {
                                        document.getElementById('directory').innerHTML = newTable.innerHTML;
                                    }

                                    const newOverlay = doc.querySelector('#closing-requests-overlay');
                                    const currentOverlay = document.getElementById('closing-requests-overlay');
                                    if (newOverlay) {
                                        if (currentOverlay) {
                                            currentOverlay.innerHTML = newOverlay.innerHTML;
                                        } else {
                                            document.body.appendChild(newOverlay);
                                        }
                                    } else if (currentOverlay) {
                                        currentOverlay.remove();
                                    }

                                    initAutoApply(); // Re-bind
                                });
                        }
                    }, delay);
                });
            });
        });
    }

    // Initialize auto-apply on load
    window.addEventListener('DOMContentLoaded', initAutoApply);

    // Handle initial tab from URL
    window.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        const tab = params.get('tab');
        if (tab === 'manager') {
            const btn = document.querySelector('button[onclick*="manager"]');
            if (btn) btn.click();
        }
    });

    // --- CATEGORY MODAL ---
    function openCategoryModal(id = null, name = '', desc = '', parentId = null) {
        const modal = document.getElementById('category-modal');
        const title = document.getElementById('modal-title');
        const idInput = document.getElementById('cat-id');
        const nameInput = document.getElementById('cat-name');
        const descInput = document.getElementById('cat-desc');
        const parentIdInput = document.getElementById('cat-parent-id');

        idInput.value = id || '';
        nameInput.value = name;
        descInput.value = desc;
        parentIdInput.value = parentId || '';

        if (id) {
            title.innerText = parentId ? 'Edit Subcategory' : 'Edit Category';
        } else {
            title.innerText = parentId ? 'New Subcategory' : 'New Primary Category';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeCategoryModal() {
        const modal = document.getElementById('category-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function confirmDeleteCategory(id) {
        if (confirm('Are you sure you want to delete this category? Deleting a primary category will also remove all its subcategories.')) {
            window.location.href = `<?= base_url('superadmin/tickets/deleteCategory') ?>/${id}`;
        }
    }

    // --- QUICK EDIT TICKET ---
    function openEditTicketModal(ticketData) {
        const modal = document.getElementById('edit-ticket-modal');
        const idInput = document.getElementById('edit-ticket-id');
        const dueInput = document.getElementById('edit-ticket-due');
        const dev1Input = document.getElementById('edit-ticket-dev1');
        const supportInput = document.getElementById('edit-ticket-support');
        const dev2Input = document.getElementById('edit-ticket-dev2');
        const reoccurrenceInput = document.getElementById('edit-ticket-reoccurrence');

        if (typeof ticketData === 'number') {
            idInput.value = ticketData;
            dueInput.value = '';
            dev1Input.value = '';
            supportInput.value = '';
            dev2Input.value = '';
            reoccurrenceInput.value = '';
        } else {
            idInput.value = ticketData.id;
            dueInput.value = ticketData.due_date ? ticketData.due_date.split(' ')[0] : '';
            dev1Input.value = ticketData.dev_remarks_1 || '';
            supportInput.value = ticketData.support_remarks || '';
            dev2Input.value = ticketData.dev_remarks_2 || '';
            reoccurrenceInput.value = ticketData.reoccurrence_remarks || '';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeEditTicketModal() {
        const modal = document.getElementById('edit-ticket-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // --- ATTACHMENT PREVIEW ---
    function openAttachmentModal(attachments, ticketNumber) {
        console.log('Triggering Attachment Preview for:', ticketNumber);
        
        const modal = document.getElementById('attachment-preview-modal');
        const list = document.getElementById('attachment-list');
        const numSpan = document.getElementById('preview-ticket-number');
        
        if (!modal || !list) {
            console.error('Attachment modal components missing in DOM');
            return;
        }

        numSpan.textContent = 'Ticket ' + ticketNumber;
        list.innerHTML = '';
        
        const baseUrl = '<?= base_url('uploads/tickets/') ?>';
        
        attachments.forEach(file => {
            const isImage = /\.(jpg|jpeg|png|gif|webp)$/i.test(file);
            const item = document.createElement('div');
            item.className = 'group relative aspect-square bg-slate-50 rounded-xl border border-slate-100 overflow-hidden hover:border-blue-300 transition-all';
            
            if (isImage) {
                item.innerHTML = `
                    <img src="${baseUrl}${file}" class="w-full h-full object-cover cursor-zoom-in" onclick="openPhotoModal('${baseUrl}${file}')">
                    <div class="absolute inset-x-0 bottom-0 p-2 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="${baseUrl}${file}" download class="text-[10px] text-white font-bold hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">download</span> Download
                        </a>
                    </div>
                `;
            } else {
                item.innerHTML = `
                    <div class="w-full h-full flex flex-col items-center justify-center p-4">
                        <span class="material-symbols-outlined text-slate-300 text-3xl mb-2">description</span>
                        <span class="text-[10px] text-slate-500 truncate w-full text-center">${file}</span>
                        <a href="${baseUrl}${file}" target="_blank" class="mt-2 text-[10px] text-blue-500 font-bold hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">open_in_new</span> Open File
                        </a>
                    </div>
                `;
            }
            list.appendChild(item);
        });
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeAttachmentModal() {
        const modal = document.getElementById('attachment-preview-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // --- LINK PREVIEW ---
    function openLinkModal(links, ticketNumber) {
        console.log('Triggering Link Preview for:', ticketNumber);

        const modal = document.getElementById('link-preview-modal');
        const list = document.getElementById('link-list');
        const numSpan = document.getElementById('preview-link-ticket-number');
        
        if (!modal || !list) {
            console.error('Link modal components missing in DOM');
            return;
        }

        numSpan.textContent = 'Ticket ' + ticketNumber;
        list.innerHTML = '';
        
        links.forEach(url => {
            const item = document.createElement('div');
            item.innerHTML = `
                <a href="${url}" target="_blank" class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 border border-slate-100 hover:bg-blue-50 hover:border-blue-100 transition-all group">
                    <span class="material-symbols-outlined text-blue-500 group-hover:scale-110 transition-transform">link</span>
                    <span class="text-xs font-semibold text-slate-700 truncate flex-1">${url}</span>
                    <span class="material-symbols-outlined text-[16px] text-slate-300 ml-auto">open_in_new</span>
                </a>
            `;
            list.appendChild(item);
        });
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeLinkModal() {
        const modal = document.getElementById('link-preview-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // --- PHOTO MODAL (FOR FULL VIEW) ---
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
        if (!modal) return;
        img.classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    // --- KEYBOARD SUPPORT ---
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Close photo modal first if open
            const photoModal = document.getElementById('photoModal');
            if (photoModal && !photoModal.classList.contains('hidden')) {
                closePhotoModal();
                return;
            }
            
            // Otherwise close other modals
            closeAttachmentModal();
            closeLinkModal();
            closeCategoryModal();
            closeEditTicketModal();
        }
    });
</script>
<?= $this->endSection() ?>
