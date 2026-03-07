<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<!-- SAP Fiori Page Header -->
<div class="fiori-page-header">
    <div>
        <h1 class="fiori-page-title">Bot Knowledge Manager</h1>
        <p class="fiori-page-subtitle">Train your AI assistant with automated guides and step-by-step instructions</p>
    </div>
    <button onclick="toggleModal('cat-modal')" class="btn btn-outline">
        <span class="material-symbols-outlined text-[16px]">create_new_folder</span>
        New Category
    </button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

    <!-- Left: Create Guide Form -->
    <div class="lg:col-span-4 sticky top-5">
        <div class="fiori-card">
            <div class="fiori-card__header">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]" style="color:var(--fiori-blue);">edit_note</span>
                    <span class="fiori-card__title">Create New Guide</span>
                </div>
            </div>
            <div class="fiori-card__content">
                <form action="<?= base_url('superadmin/kb/store') ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <?= csrf_field() ?>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Category</label>
                        <select name="category_id" required class="fiori-input">
                            <option value="" disabled selected>Select a folder…</option>
                            <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Article Question</label>
                        <input type="text" name="question" required placeholder="e.g., How to download records?" class="fiori-input">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Trigger Keywords</label>
                        <input type="text" name="keywords" placeholder="Setup_Cutoff, Payroll_Rules" class="fiori-input">
                        <p class="text-xs mt-1" style="color:var(--fiori-text-muted);">Separate multiple tags with commas.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Bot Instructions</label>
                        <textarea name="answer" rows="6" required placeholder="1. Go to Transaction Menu...&#10;[IMAGE:1]&#10;2. Click Save..."
                            class="fiori-input" style="height:auto; padding:10px 12px; resize:vertical;"></textarea>
                        <div class="mt-2 p-3 rounded space-y-2" style="background:var(--fiori-blue-light); border:1px solid #b3d4fb; border-radius:4px;">
                            <p class="text-xs" style="color:var(--fiori-blue);">
                                <span class="font-semibold">Tip (Images):</span> Type <code class="bg-white px-1 rounded border border-blue-200">[IMAGE:1]</code> to embed the first uploaded image inline, <code class="bg-white px-1 rounded border border-blue-200">[IMAGE:2]</code> for the second, etc.
                            </p>
                            <p class="text-xs" style="color:var(--fiori-blue);">
                                <span class="font-semibold">Tip (Links):</span> Type <code class="bg-white px-1 rounded border border-blue-200">[Click to open](https://example.com)</code> to create a hidden, clickable text link.
                            </p>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold uppercase tracking-wider" style="color:var(--fiori-text-secondary);">Attach Visuals</label>
                            <span id="file-count-label" class="text-[10px] font-bold text-emerald-600 hidden bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100"></span>
                        </div>
                        <label for="kb_images" class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed rounded cursor-pointer transition-colors hover:bg-blue-50"
                            style="border-color:var(--fiori-border); border-radius:4px;">
                            <div class="text-center">
                                <span class="material-symbols-outlined text-[28px] block mb-1" style="color:var(--fiori-text-muted);">cloud_upload</span>
                                <p class="text-xs font-medium" style="color:var(--fiori-text-muted);">Click to upload more files</p>
                            </div>
                            <input id="kb_images" name="kb_images[]" type="file" accept="image/*" class="hidden" multiple onchange="previewFiles(this)">
                        </label>
                        <div id="file-list-container" class="mt-2 space-y-1.5 hidden"></div>
                    </div>

                    <button type="submit" class="btn btn-accent w-full">
                        <span class="material-symbols-outlined text-[16px]">send</span>
                        Deploy to Bot
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right: Articles Table -->
    <div class="lg:col-span-8">
        <div class="fiori-card overflow-hidden">
            <div class="fiori-card__header">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]" style="color:var(--fiori-blue);">auto_stories</span>
                    <span class="fiori-card__title">Active Guides</span>
                </div>
                <span class="fiori-status fiori-status--information font-semibold">Total: <?= count($articles) ?></span>
            </div>
            <div class="overflow-x-auto">
                <table class="fiori-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Question</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($articles)): ?>
                            <?php foreach($articles as $art): ?>
                            <tr>
                                <td>
                                    <span class="fiori-status fiori-status--neutral">
                                        <span class="material-symbols-outlined text-[13px]">folder_open</span>
                                        <?= esc($art['category_name']) ?>
                                    </span>
                                </td>
                                <td class="font-medium" style="color:var(--fiori-text-base);"><?= esc($art['question']) ?></td>
                                <td class="text-right">
                                    <button onclick="confirmAction(event, '<?= base_url('superadmin/kb/delete/'.$art['id']) ?>', 'Delete this guide?', 'This will also delete all uploaded images. This cannot be undone.', 'Delete', 'var(--fiori-negative)')"
                                        class="w-8 h-8 flex items-center justify-center rounded transition-colors ml-auto" style="color:var(--fiori-text-muted);"
                                        onmouseover="this.style.background='var(--fiori-negative-light)'; this.style.color='var(--fiori-negative)';" onmouseout="this.style.background=''; this.style.color='var(--fiori-text-muted)';">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="3" class="py-14 text-center">
                                <span class="material-symbols-outlined text-4xl block mb-2" style="color:var(--fiori-border);">auto_stories</span>
                                <p class="text-sm font-medium" style="color:var(--fiori-text-secondary);">No guides yet</p>
                                <p class="text-xs mt-1" style="color:var(--fiori-text-muted);">Use the form to add your first knowledge base article.</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<!-- New Category Dialog -->
<div id="cat-modal" class="fiori-overlay hidden">
    <div class="fiori-dialog">
        <div class="fiori-dialog__header">
            <h3 class="fiori-dialog__title">New Category</h3>
            <button type="button" onclick="toggleModal('cat-modal')"
                class="w-7 h-7 flex items-center justify-center rounded transition-colors" style="color:var(--fiori-text-muted);" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background=''">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <form action="<?= base_url('superadmin/kb/storeCategory') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="fiori-dialog__body space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Folder Name</label>
                    <input type="text" name="name" required class="fiori-input" placeholder="e.g., Payroll Setup">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--fiori-text-secondary);">Choose Icon</label>
                    <div class="grid grid-cols-6 gap-2 p-3 rounded border" style="border-color:var(--fiori-border); background:#f7f7f7; border-radius:4px; max-height:180px; overflow-y:auto;">
                        <?php
                        $icons = ['fas fa-folder','fas fa-money-bill','fas fa-clock','fas fa-user-circle','fas fa-file-invoice','fas fa-cog','fas fa-briefcase','fas fa-id-card','fas fa-envelope','fas fa-tools','fas fa-shield-alt'];
                        foreach($icons as $icon): ?>
                        <button type="button" onclick="selIcon('<?= $icon ?>', this)"
                            class="ico-opt w-10 h-10 bg-white rounded flex items-center justify-center text-sm transition-colors border"
                            style="border-color:var(--fiori-border); color:var(--fiori-text-muted);"
                            onmouseover="this.style.borderColor='var(--fiori-blue)'; this.style.color='var(--fiori-blue)';"
                            onmouseout="if(!this.classList.contains('selected')){this.style.borderColor='var(--fiori-border)'; this.style.color='var(--fiori-text-muted)';}">
                            <i class="<?= $icon ?>"></i>
                        </button>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="icon" id="icon-input" value="fas fa-folder">
                </div>
            </div>
            <div class="fiori-dialog__footer">
                <button type="button" onclick="toggleModal('cat-modal')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-accent">Create Category</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let kbFilesDT = new DataTransfer();

function previewFiles(input) {
    if (input.files && input.files.length > 0) {
        Array.from(input.files).forEach(file => {
            kbFilesDT.items.add(file);
        });
        // Important: Update the input element's files property to our cumulative list
        input.files = kbFilesDT.files;
    }
    renderKbFiles();
}

function removeKbFile(indexToDel) {
    const newDT = new DataTransfer();
    Array.from(kbFilesDT.files).forEach((file, index) => {
        if (index !== indexToDel) newDT.items.add(file);
    });
    kbFilesDT = newDT;
    document.getElementById('kb_images').files = kbFilesDT.files;
    renderKbFiles();
}

function renderKbFiles() {
    const container = document.getElementById('file-list-container');
    const countLabel = document.getElementById('file-count-label');
    container.innerHTML = '';
    
    if (kbFilesDT.files.length > 0) {
        countLabel.textContent = kbFilesDT.files.length + (kbFilesDT.files.length === 1 ? ' File Selected' : ' Files Selected');
        countLabel.classList.remove('hidden');
        container.classList.remove('hidden');
        
        Array.from(kbFilesDT.files).forEach((file, index) => {
            const item = document.createElement('div');
            item.className = 'flex items-center gap-3 p-2 rounded border relative pr-8';
            item.style = 'border-color:var(--fiori-border); background:#fff; border-radius:4px;';
            item.innerHTML = `
                <span class="material-symbols-outlined text-[16px]" style="color:var(--fiori-blue); flex-shrink:0;">image</span>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium truncate" style="color:var(--fiori-text-base);">${file.name}</p>
                    <p class="text-[10px] font-semibold uppercase tracking-wider" style="color:var(--fiori-text-muted);">[IMAGE:${index + 1}]</p>
                </div>
                <button type="button" onclick="removeKbFile(${index})" class="absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center text-red-400 hover:text-red-600 rounded hover:bg-red-50 transition-colors">
                    <span class="material-symbols-outlined text-[14px]">close</span>
                </button>`;
            container.appendChild(item);
        });
    } else {
        countLabel.classList.add('hidden');
        container.classList.add('hidden');
    }
}

function selIcon(ico, el) {
    document.getElementById('icon-input').value = ico;
    document.querySelectorAll('.ico-opt').forEach(b => {
        b.classList.remove('selected');
        b.style.borderColor = 'var(--fiori-border)';
        b.style.color = 'var(--fiori-text-muted)';
        b.style.background = '#fff';
    });
    el.classList.add('selected');
    el.style.borderColor = 'var(--fiori-blue)';
    el.style.color = 'var(--fiori-blue)';
    el.style.background = 'var(--fiori-blue-light)';
}
</script>
<?= $this->endSection() ?>