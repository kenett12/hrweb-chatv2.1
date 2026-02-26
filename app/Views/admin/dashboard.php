<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto">
    <header class="mb-10">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Dashboard</h1>
        <div class="h-1.5 w-12 rounded-full mt-2" style="background-color: var(--clr-cyan);"></div>
        <p class="text-gray-500 mt-3 font-medium">Real-time overview of your HR operations and registry metrics.</p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center transition-colors" 
                     style="background-color: rgba(50, 151, 202, 0.1); color: var(--clr-cyan);">
                    <span class="material-symbols-outlined text-[32px]">admin_panel_settings</span>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Admin Accounts</p>
                    <h3 class="text-3xl font-black text-gray-900 mt-0.5"><?= number_format($total_tsr ?? 0) ?></h3>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center" 
                     style="background-color: rgba(30, 114, 175, 0.1); color: var(--clr-blue);">
                    <span class="material-symbols-outlined text-[32px]">corporate_fare</span>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Registered Clients</p>
                    <h3 class="text-3xl font-black text-gray-900 mt-0.5"><?= number_format($total_clients ?? 0) ?></h3>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-center">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">System Health</p>
            <div class="flex items-center justify-between">
                <span class="inline-flex items-center gap-2 px-5 py-2 rounded-full font-bold uppercase tracking-wider text-[11px] border" 
                      style="background-color: rgba(32, 174, 92, 0.05); border-color: rgba(32, 174, 92, 0.2); color: var(--clr-green);">
                    <span class="w-2.5 h-2.5 rounded-full animate-pulse" style="background-color: var(--clr-green);"></span>
                    Operational
                </span>
                <span class="text-[11px] text-gray-400 font-bold italic">Verified Now</span>
            </div>
        </div>

    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4" 
             style="background-color: rgba(30, 114, 175, 0.02);">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[#1e72af]">history</span>
                <h3 class="text-lg font-bold text-gray-900">Recent Activity</h3>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <?php
                    $filters = [
                        'all' => ['label' => 'All', 'icon' => 'dynamic_feed'],
                        'ticket' => ['label' => 'Tickets', 'icon' => 'confirmation_number'],
                        'reply' => ['label' => 'Replies', 'icon' => 'forum'],
                        'user' => ['label' => 'Users', 'icon' => 'person_add'],
                        'kb' => ['label' => 'KB Docs', 'icon' => 'menu_book'],
                        'feedback' => ['label' => 'Feedback', 'icon' => 'thumbs_up_down']
                    ];
                    $currentFilter = $current_filter ?? 'all';
                ?>
                
                <?php foreach($filters as $key => $filter): ?>
                    <a href="<?= base_url('superadmin/dashboard' . ($key !== 'all' ? '?type=' . $key : '')) ?>" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all border <?= $currentFilter === $key 
                            ? 'bg-blue-500 text-white border-blue-500 shadow-sm' 
                            : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50 hover:text-gray-900' ?>">
                        <span class="material-symbols-outlined text-[14px]"><?= $filter['icon'] ?></span>
                        <?= $filter['label'] ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php if (empty($recent_activity)): ?>
            <div class="py-24 flex flex-col items-center justify-center text-center">
                <div class="w-24 h-24 rounded-full flex items-center justify-center mb-8" 
                     style="background-color: rgba(255, 195, 56, 0.05); color: var(--clr-yellow);">
                    <span class="material-symbols-outlined text-5xl">cloud_done</span>
                </div>
                <h4 class="text-2xl font-bold text-gray-900">Everything looks clear</h4>
                <p class="text-gray-500 text-sm max-w-sm mx-auto mt-3 leading-relaxed font-medium">
                    Your system audit logs are currently clear. When users perform actions, they will appear here color-coded by event type.
                </p>
            </div>
        <?php else: ?>
            <div class="divide-y divide-gray-100">
                <?php foreach($recent_activity as $act): ?>
                    <div class="p-6 hover:bg-gray-50 transition-colors flex items-start gap-4">
                        <?php if ($act['type'] === 'ticket'): ?>
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 mt-1" 
                                 style="background-color: rgba(32, 174, 92, 0.1); color: var(--clr-green);">
                                <span class="material-symbols-outlined text-xl">confirmation_number</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">New ticket <span class="text-blue-600"><?= esc($act['ticket_number']) ?></span> was created.</p>
                                <p class="text-xs text-gray-500 mt-1 truncate max-w-lg">Subject: <?= esc($act['message']) ?></p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-2"><?= date('M d, Y h:i A', strtotime($act['created_at'])) ?></p>
                            </div>
                        <?php elseif ($act['type'] === 'reply'): ?>
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 mt-1" 
                                 style="background-color: rgba(30, 114, 175, 0.1); color: var(--clr-blue);">
                                <span class="material-symbols-outlined text-xl">forum</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">New reply on ticket <span class="text-blue-600"><?= esc($act['ticket_number']) ?></span> from <?= esc($act['user_email'] ?? 'User') ?>.</p>
                                <p class="text-xs text-gray-500 mt-1 truncate max-w-lg">"<?= strip_tags($act['message']) ?>"</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-2"><?= date('M d, Y h:i A', strtotime($act['created_at'])) ?></p>
                            </div>
                        <?php elseif ($act['type'] === 'user'): ?>
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 mt-1" 
                                 style="background-color: rgba(168, 85, 247, 0.1); color: #a855f7;">
                                <span class="material-symbols-outlined text-xl">person_add</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">New account registered: <span class="text-purple-600 uppercase text-xs"><?= esc($act['ticket_number']) ?></span></p>
                                <p class="text-xs text-gray-500 mt-1 truncate max-w-lg"><?= esc($act['message']) ?></p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-2"><?= date('M d, Y h:i A', strtotime($act['created_at'])) ?></p>
                            </div>
                        <?php elseif ($act['type'] === 'kb'): ?>
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 mt-1" 
                                 style="background-color: rgba(234, 179, 8, 0.1); color: #eab308;">
                                <span class="material-symbols-outlined text-xl">menu_book</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">New KB Article added to <span class="text-yellow-600"><?= esc($act['ticket_number']) ?></span>.</p>
                                <p class="text-xs text-gray-500 mt-1 truncate max-w-lg">Q: <?= esc($act['message']) ?></p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-2"><?= date('M d, Y h:i A', strtotime($act['created_at'])) ?></p>
                            </div>
                        <?php elseif ($act['type'] === 'feedback'): ?>
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 mt-1" 
                                 style="background-color: rgba(249, 115, 22, 0.1); color: #f97316;">
                                <span class="material-symbols-outlined text-xl">thumbs_up_down</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">Feedback submitted by <span class="text-orange-600"><?= esc($act['user_email'] ?? 'User') ?></span>.</p>
                                <p class="text-xs text-gray-500 mt-1 truncate max-w-lg">Status: <span class="font-bold"><?= esc($act['message']) ?></span> | Article: <?= esc($act['article_name'] ?? 'Unknown') ?></p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-2"><?= date('M d, Y h:i A', strtotime($act['created_at'])) ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (in_array($act['type'], ['ticket', 'reply'])): ?>
                            <a href="<?= base_url('superadmin/tickets/view/' . $act['id']) ?>" class="btn btn-outline text-xs px-4 py-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                View
                            </a>
                        <?php elseif ($act['type'] === 'kb' || $act['type'] === 'feedback'): ?>
                            <a href="<?= base_url('superadmin/kb') ?>" class="btn btn-outline text-xs px-4 py-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                View KB
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
    const socket = io('http://localhost:3001');
    socket.on('global_ticket_change', (data) => {
        fetch(window.location.href)
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newContent = doc.querySelector('.fade-in');
                if (newContent) {
                    document.querySelector('.fade-in').innerHTML = newContent.innerHTML;
                }
            });
    });
    console.log("Admin Dashboard Real-Time Sync Active (Seamless).");
</script>
<?= $this->endSection() ?>