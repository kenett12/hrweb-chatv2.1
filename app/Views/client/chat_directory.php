<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="px-8 py-6 max-w-7xl mx-auto">
    <div class="fiori-page-header mb-6">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="fiori-page-title text-xl">Support Chats</h1>
                <p class="fiori-page-subtitle">Select an active context or past ticket to resume your conversation.</p>
            </div>
            <a href="<?= base_url('client/tickets/create') ?>" class="btn btn-accent">
                <span class="material-symbols-outlined text-[18px]">add</span> New Support Request
            </a>
        </div>
    </div>

    <?php if (empty($chats)): ?>
        <div class="fiori-card text-center py-16">
            <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4" style="background:var(--fiori-surface);">
                <span class="material-symbols-outlined text-[32px]" style="color:var(--fiori-border);">chat_bubble_outline</span>
            </div>
            <h3 class="fiori-card__title text-lg mb-2">No Active Conversations</h3>
            <p class="text-sm mb-4" style="color:var(--fiori-text-secondary);">You don't have any support chat history yet. If you need assistance, start a new request.</p>
            <a href="<?= base_url('client/tickets/create') ?>" class="text-xs font-semibold" style="color:var(--fiori-blue); hover:underline;">Start a Request →</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($chats as $chat): ?>
                <a href="<?= base_url('client/chat/' . $chat['id']) ?>" 
                   class="fiori-card flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden p-5" style="cursor:pointer; display:flex; text-decoration:none; min-height: 180px;">
                    
                    <div>
                        <div class="flex justify-between items-start mb-3 relative">
                            <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded" style="background:var(--fiori-surface); color:var(--fiori-text-muted);">
                                <?= $chat['ticket_number'] ?>
                            </span>
                            <?php
                                if ($chat['status'] === 'Resolved') echo '<span class="fiori-status fiori-status--positive">Resolved</span>';
                                elseif ($chat['status'] === 'In Progress') echo '<span class="fiori-status fiori-status--information">In Progress</span>';
                                elseif ($chat['status'] === 'Closed') echo '<span class="fiori-status" style="background:#e0e0e0; color:#606060;">Closed</span>';
                                else echo '<span class="fiori-status fiori-status--warning">' . esc($chat['status']) . '</span>';
                            ?>
                        </div>

                        <h3 class="fiori-card__title text-base mb-2 line-clamp-2" style="color:var(--fiori-text-primary);">
                            <?= esc($chat['subject']) ?>
                        </h3>
                        
                        <p class="fiori-card__content text-xs line-clamp-2 mb-4" style="color:var(--fiori-text-secondary); padding:0;">
                            <?= esc($chat['description']) ?>
                        </p>
                    </div>

                    <div class="pt-3 border-t flex items-center justify-between mt-auto" style="border-color:var(--fiori-border);">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded flex items-center justify-center shrink-0" style="background:var(--fiori-surface); border:1px solid var(--fiori-border);">
                                <?php if (empty($chat['assigned_to'])): ?>
                                    <span class="material-symbols-outlined text-[14px]" style="color:var(--fiori-text-muted);">smart_toy</span>
                                <?php else: ?>
                                    <span class="material-symbols-outlined text-[14px]" style="color:var(--fiori-blue);">support_agent</span>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-semibold uppercase tracking-wider" style="color:var(--fiori-text-muted);">Handling Agent</span>
                                <span class="text-[11px] font-medium truncate max-w-[100px]" style="color:var(--fiori-text-primary);">
                                    <?= empty($chat['assigned_to']) ? 'HRWeb Bot' : esc($chat['staff_name']) ?>
                                </span>
                            </div>
                        </div>
                        <div class="text-right flex flex-col items-end">
                            <span class="text-[9px] font-semibold uppercase tracking-wider" style="color:var(--fiori-text-muted);">Last Message</span>
                            <span class="text-[10px] font-medium mt-0.5" style="color:var(--fiori-text-secondary);"><?= date('M d, H:i', strtotime($chat['updated_at'])) ?></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
