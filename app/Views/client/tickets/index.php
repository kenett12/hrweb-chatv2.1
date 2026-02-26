<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">My Support Tickets</h2>
        <a href="<?= base_url('client/tickets/create') ?>"
            class="bg-[#1e72af] text-white px-6 py-2 rounded-xl font-bold text-sm">New Ticket</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Ticket #</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Subject</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Category</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if (!empty($tickets)):
                    foreach ($tickets as $t): ?>
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4 font-bold text-gray-700"><?= $t['ticket_number'] ?></td>
                            <td class="px-6 py-4 text-gray-600"><?= esc($t['subject']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?= esc($t['category']) ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-blue-50 text-[#1e72af]">
                                    <?= $t['status'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= base_url('client/tickets/view/' . $t['id']) ?>"
                                    class="text-[#1e72af] font-bold hover:underline">View</a>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="5" class="p-10 text-center text-gray-400">No tickets found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>