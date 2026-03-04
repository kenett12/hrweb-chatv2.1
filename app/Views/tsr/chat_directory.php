<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="px-8 py-6 max-w-7xl mx-auto">
    <div class="fiori-page-header mb-6">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="fiori-page-title text-xl">Active Chats</h1>
                <p class="fiori-page-subtitle">Select a conversation to resume support.</p>
            </div>
        </div>
    </div>

    <?php if (empty($chats)): ?>
        <div class="fiori-card text-center py-16">
            <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4" style="background:var(--fiori-surface);">
                <span class="material-symbols-outlined text-[32px]" style="color:var(--fiori-border);">chat_bubble_outline</span>
            </div>
            <h3 class="fiori-card__title text-lg mb-2">No Active Conversations</h3>
            <p class="text-sm mb-4" style="color:var(--fiori-text-secondary);">There are no chats currently in the queue.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($chats as $chat): ?>
                <a href="<?= base_url('tsr/tickets/view/' . $chat['id']) ?>" 
                   class="fiori-card flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden p-5" style="cursor:pointer; display:flex; text-decoration:none; min-height: 180px;">
                    
                    <div>
                        <div class="flex justify-between items-start mb-3 relative">
                            <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded" style="background:var(--fiori-surface); color:var(--fiori-text-muted);">
                                #<?= $chat['id'] ?>
                            </span>
                            <?php
                                if ($chat['status'] === 'Resolved') echo '<span class="fiori-status fiori-status--positive">Resolved</span>';
                                elseif ($chat['status'] === 'In Progress') echo '<span class="fiori-status fiori-status--information">In Progress</span>';
                                elseif ($chat['status'] === 'Closed') echo '<span class="fiori-status" style="background:#e0e0e0; color:#606060;">Closed</span>';
                                else echo '<span class="fiori-status fiori-status--warning">' . esc($chat['status']) . '</span>';
                            ?>
                        </div>

                        <h3 class="fiori-card__title text-base mb-2 line-clamp-2" style="color:var(--fiori-text-primary);">
                            <?= esc($chat['subject'] ?? 'No Subject') ?>
                        </h3>
                        
                        <p class="fiori-card__content text-xs line-clamp-2 mb-4" style="color:var(--fiori-text-secondary); padding:0;">
                            Client: <?= esc($chat['client_name'] ?? 'Guest') ?>
                        </p>
                    </div>

                    <div class="pt-3 border-t flex items-center justify-between mt-auto" style="border-color:var(--fiori-border);">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded flex items-center justify-center shrink-0" style="background:var(--fiori-surface); border:1px solid var(--fiori-border);">
                                <span class="material-symbols-outlined text-[14px]" style="color:var(--fiori-blue);">support_agent</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-semibold uppercase tracking-wider" style="color:var(--fiori-text-muted);">Assigned</span>
                                <span class="text-[11px] font-medium truncate max-w-[100px]" style="color:var(--fiori-text-primary);">
                                    <?= empty($chat['staff_name']) ? 'Unassigned' : esc($chat['staff_name']) ?>
                                </span>
                            </div>
                        </div>
                        <div class="text-right flex flex-col items-end">
                            <span class="text-[9px] font-semibold uppercase tracking-wider" style="color:var(--fiori-text-muted);">Last Update</span>
                            <span class="text-[10px] font-medium mt-0.5" style="color:var(--fiori-text-secondary);"><?= empty($chat['updated_at']) ? '' : date('M d, H:i', strtotime($chat['updated_at'])) ?></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
