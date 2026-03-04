<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<!-- SAP Fiori Page Header -->
<div class="fiori-page-header">
    <div>
        <h1 class="fiori-page-title">Dashboard</h1>
        <p class="fiori-page-subtitle">Real-time overview of HR operations and registry metrics</p>
    </div>
    <span class="text-xs" style="color:var(--fiori-text-muted);"><?= date('l, F j, Y') ?></span>
</div>

<!-- KPI Tiles -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">

    <div class="fiori-tile">
        <div class="fiori-tile__header">
            <span class="material-symbols-outlined text-[16px]" style="color:var(--fiori-blue);">admin_panel_settings</span>
            TSR Accounts
        </div>
        <div class="fiori-tile__value"><?= number_format($total_tsr ?? 0) ?></div>
        <div class="fiori-tile__footer">Total active support representatives</div>
    </div>

    <div class="fiori-tile">
        <div class="fiori-tile__header">
            <span class="material-symbols-outlined text-[16px]" style="color:var(--fiori-blue);">domain</span>
            Registered Clients
        </div>
        <div class="fiori-tile__value"><?= number_format($total_clients ?? 0) ?></div>
        <div class="fiori-tile__footer">Corporate partners in the registry</div>
    </div>

    <div class="fiori-tile">
        <div class="fiori-tile__header">
            <span class="material-symbols-outlined text-[16px]" style="color:var(--fiori-positive);">health_and_safety</span>
            System Health
        </div>
        <div class="flex items-center gap-3 mt-2">
            <span class="badge-active fiori-status fiori-status--positive">
                <span class="w-2 h-2 rounded-full animate-pulse" style="background:var(--fiori-positive);"></span>
                Operational
            </span>
        </div>
        <div class="fiori-tile__footer">All services running normally</div>
    </div>

</div>

<!-- Recent Activity Card -->
<div class="fiori-card">
    <div class="fiori-card__header">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]" style="color:var(--fiori-blue);">history</span>
            <span class="fiori-card__title">Recent Activity</span>
        </div>
        <!-- Filter bar -->
        <div class="flex items-center flex-wrap gap-1.5">
            <?php
                $filters = [
                    'all'      => ['label' => 'All',      'icon' => 'dynamic_feed'],
                    'ticket'   => ['label' => 'Tickets',  'icon' => 'confirmation_number'],
                    'reply'    => ['label' => 'Replies',  'icon' => 'forum'],
                    'user'     => ['label' => 'Users',    'icon' => 'person_add'],
                    'kb'       => ['label' => 'KB Docs',  'icon' => 'menu_book'],
                    'feedback' => ['label' => 'Feedback', 'icon' => 'thumbs_up_down'],
                ];
                $currentFilter = $current_filter ?? 'all';
            ?>
            <?php foreach($filters as $key => $filter): ?>
            <a href="<?= base_url('superadmin/dashboard' . ($key !== 'all' ? '?type='.$key : '')) ?>"
               class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium border rounded transition-colors"
               style="border-radius:4px; <?= $currentFilter === $key
                   ? 'background:var(--fiori-blue); color:#fff; border-color:var(--fiori-blue);'
                   : 'background:#fff; color:var(--fiori-text-secondary); border-color:var(--fiori-border);' ?>">
                <span class="material-symbols-outlined text-[13px]"><?= $filter['icon'] ?></span>
                <?= $filter['label'] ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (empty($recent_activity)): ?>
    <div class="fiori-card__content py-16 text-center">
        <span class="material-symbols-outlined text-5xl block mb-4" style="color:var(--fiori-border);">cloud_done</span>
        <h4 class="text-base font-semibold" style="color:var(--fiori-text-base);">Everything looks clear</h4>
        <p class="text-sm mt-2 max-w-sm mx-auto" style="color:var(--fiori-text-secondary);">Your system audit logs are currently empty. Actions will appear here as they happen.</p>
    </div>
    <?php else: ?>
    <div class="divide-y" style="border-color:var(--fiori-border);">
        <?php foreach($recent_activity as $act): ?>
        <?php
            $typeConfig = [
                'ticket'   => ['icon'=>'confirmation_number', 'color'=>'var(--fiori-positive)'],
                'reply'    => ['icon'=>'forum',                'color'=>'var(--fiori-blue)'],
                'user'     => ['icon'=>'person_add',           'color'=>'#6e4b9e'],
                'kb'       => ['icon'=>'menu_book',            'color'=>'var(--fiori-warning)'],
                'feedback' => ['icon'=>'thumbs_up_down',       'color'=>'var(--fiori-warning)'],
            ];
            $tc = $typeConfig[$act['type']] ?? ['icon'=>'info','color'=>'var(--fiori-neutral)'];
        ?>
        <div class="px-4 py-3 flex items-start gap-4 group hover:bg-gray-50 transition-colors">
            <div class="w-8 h-8 flex items-center justify-center rounded flex-none mt-0.5" style="background:<?= str_replace('var(','rgba('.str_replace(')',',0.1)',$tc['color']).')',$tc['color']) ?>10; color:<?= $tc['color'] ?>; border-radius:4px;">
                <span class="material-symbols-outlined text-[18px]"><?= $tc['icon'] ?></span>
            </div>
            <div class="flex-1 min-w-0">
                <?php if ($act['type'] === 'ticket'): ?>
                <p class="text-sm" style="color:var(--fiori-text-base);">New ticket <a href="<?= base_url('superadmin/tickets/view/'.$act['id']) ?>" class="font-semibold" style="color:var(--fiori-blue);"><?= esc($act['ticket_number']) ?></a> was created.</p>
                <p class="text-xs mt-0.5 truncate" style="color:var(--fiori-text-secondary);">Subject: <?= esc($act['message']) ?></p>
                <?php elseif ($act['type'] === 'reply'): ?>
                <p class="text-sm" style="color:var(--fiori-text-base);">New reply on ticket <span class="font-semibold" style="color:var(--fiori-blue);"><?= esc($act['ticket_number']) ?></span> from <?= esc($act['user_email'] ?? 'User') ?>.</p>
                <p class="text-xs mt-0.5 truncate" style="color:var(--fiori-text-secondary);">"<?= strip_tags($act['message']) ?>"</p>
                <?php elseif ($act['type'] === 'user'): ?>
                <p class="text-sm" style="color:var(--fiori-text-base);">New account registered: <span class="font-semibold" style="color:#6e4b9e;"><?= esc($act['ticket_number']) ?></span></p>
                <p class="text-xs mt-0.5 truncate" style="color:var(--fiori-text-secondary);"><?= esc($act['message']) ?></p>
                <?php elseif ($act['type'] === 'kb'): ?>
                <p class="text-sm" style="color:var(--fiori-text-base);">New KB article added to <span class="font-semibold" style="color:var(--fiori-warning);"><?= esc($act['ticket_number']) ?></span>.</p>
                <p class="text-xs mt-0.5 truncate" style="color:var(--fiori-text-secondary);">Q: <?= esc($act['message']) ?></p>
                <?php elseif ($act['type'] === 'feedback'): ?>
                <p class="text-sm" style="color:var(--fiori-text-base);">Feedback submitted by <span class="font-semibold" style="color:var(--fiori-warning);"><?= esc($act['user_email'] ?? 'User') ?></span>.</p>
                <p class="text-xs mt-0.5 truncate" style="color:var(--fiori-text-secondary);">Status: <?= esc($act['message']) ?></p>
                <?php endif; ?>
                <p class="text-[11px] mt-1 font-medium" style="color:var(--fiori-text-muted);"><?= date('M d, Y · h:i A', strtotime($act['created_at'])) ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
    const socket = io('http://localhost:3001');
    socket.on('global_ticket_change', () => {
        fetch(window.location.href)
            .then(r => r.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newContent = doc.querySelector('.fade-in');
                if (newContent) document.querySelector('.fade-in').innerHTML = newContent.innerHTML;
            });
    });
</script>
<?= $this->endSection() ?>