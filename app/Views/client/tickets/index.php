<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="fiori-page-header mb-4">
    <div class="flex items-center justify-between w-full">
        <div>
            <h1 class="fiori-page-title text-xl">My Support Tickets</h1>
            <p class="fiori-page-subtitle">View and track the status of your reported issues</p>
        </div>
        <a href="<?= base_url('client/tickets/create') ?>" class="btn btn-accent">
            <span class="material-symbols-outlined text-[18px]">add</span>
            New Ticket
        </a>
    </div>
</div>

<div class="fiori-card p-0 overflow-hidden">
    <table class="fiori-table">
        <thead>
            <tr>
                <th>Ticket #</th>
                <th>Subject</th>
                <th>Category</th>
                <th>Status</th>
                <th class="text-right">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($tickets)):
                foreach ($tickets as $t): ?>
                    <tr class="cursor-pointer" onclick="window.location.href='<?= base_url('client/tickets/view/' . $t['id']) ?>'">
                        <td>
                            <span class="font-medium" style="color:var(--fiori-text-base);"><?= esc($t['ticket_number']) ?></span>
                        </td>
                        <td>
                            <span class="font-medium" style="color:var(--fiori-text-base);"><?= esc(strlen($t['subject']) > 60 ? substr($t['subject'], 0, 60) . '…' : $t['subject']) ?></span>
                        </td>
                        <td>
                            <span class="text-sm" style="color:var(--fiori-text-muted);"><?= esc($t['category']) ?></span>
                        </td>
                        <td>
                            <?php
                                if ($t['status'] === 'Resolved') echo '<span class="fiori-status fiori-status--positive">Resolved</span>';
                                elseif ($t['status'] === 'In Progress') echo '<span class="fiori-status fiori-status--information">In Progress</span>';
                                else echo '<span class="fiori-status fiori-status--warning">' . esc($t['status']) . '</span>';
                            ?>
                        </td>
                        <td class="text-right">
                            <span class="text-xs font-semibold" style="color:var(--fiori-blue);">View →</span>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="5" class="py-12 text-center">
                        <span class="material-symbols-outlined text-4xl block mb-3" style="color:var(--fiori-border);">history_toggle_off</span>
                        <p class="text-sm font-medium" style="color:var(--fiori-text-secondary);">No tickets found</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>