<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="px-8 py-6 max-w-7xl mx-auto">
    <div class="mb-10">
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Support Chats</h1>
        <p class="text-gray-500 mt-2 font-medium">Select an active context or past ticket to resume your conversation.</p>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8 flex justify-end">
        <a href="<?= base_url('client/tickets/create') ?>" class="bg-[#1e72af] text-white px-6 py-3 rounded-xl shadow-md hover:bg-[#165a8a] transition-colors flex items-center gap-2 font-bold text-sm">
            <span class="material-symbols-outlined text-[20px]">add_circle</span> New Application / Support Request
        </a>
    </div>

    <?php if (empty($chats)): ?>
        <div class="bg-white rounded-[2rem] border border-gray-100 p-24 text-center shadow-sm">
            <div class="w-24 h-24 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-gray-300 text-[48px]">chat_bubble_outline</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No Active Conversations</h3>
            <p class="text-gray-500 mb-6">You don't have any support chat history yet. If you need assistance, start a new request.</p>
            <a href="<?= base_url('client/tickets/create') ?>" class="text-[#1e72af] font-bold hover:underline">Start a Request</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($chats as $chat): ?>
                <a href="<?= base_url('client/chat/' . $chat['id']) ?>" 
                   class="group bg-white rounded-3xl border border-gray-100 p-6 flex flex-col justify-between hover:shadow-xl hover:border-indigo-100 transition-all hover:-translate-y-1 relative overflow-hidden">
                    
                    <!-- Decorative background element -->
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-gradient-to-br from-blue-50 to-indigo-50/20 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>

                    <div>
                        <div class="flex justify-between items-start mb-4 relative">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50 px-3 py-1 rounded-lg">
                                <?= $chat['ticket_number'] ?>
                            </span>
                            <?php 
                                $statusColors = [
                                    'Open' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    'In Progress' => 'bg-amber-50 text-amber-600 border-amber-100',
                                    'Resolved' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'Closed' => 'bg-gray-50 text-gray-500 border-gray-200'
                                ];
                                $colorClass = $statusColors[$chat['status']] ?? 'bg-gray-100 text-gray-600';
                            ?>
                            <span class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border <?= $colorClass ?>">
                                <?= esc($chat['status']) ?>
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-[#1e72af] transition-colors line-clamp-2 leading-tight">
                            <?= esc($chat['subject']) ?>
                        </h3>
                        
                        <p class="text-sm text-gray-500 line-clamp-2 mb-6 leading-relaxed">
                            <?= esc($chat['description']) ?>
                        </p>
                    </div>

                    <div class="pt-4 border-t border-gray-50 flex items-center justify-between mt-auto">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center shadow-sm">
                                <?php if (empty($chat['assigned_to'])): ?>
                                    <span class="material-symbols-outlined text-[16px] text-gray-400">smart_toy</span>
                                <?php else: ?>
                                    <span class="material-symbols-outlined text-[16px] text-indigo-500">support_agent</span>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold uppercase tracking-widest text-gray-400">Handling Agent</span>
                                <span class="text-xs font-semibold text-gray-900 truncate max-w-[100px]">
                                    <?= empty($chat['assigned_to']) ? 'HRWeb UI Bot' : esc($chat['staff_name']) ?>
                                </span>
                            </div>
                        </div>
                        <div class="text-right flex flex-col items-end">
                            <span class="text-[9px] font-bold uppercase tracking-widest text-gray-400">Last Message</span>
                            <span class="text-[10px] font-bold text-gray-500 mt-0.5"><?= date('M d, H:i', strtotime($chat['updated_at'])) ?></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
