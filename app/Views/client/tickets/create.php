<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="px-8 py-6 max-w-4xl mx-auto">
    <div class="fiori-page-header mb-6">
        <div>
            <h2 class="fiori-page-title text-xl">Submit New Ticket</h2>
            <p class="fiori-page-subtitle">Fill in the details below to reach our team.</p>
        </div>
    </div>

    <form action="<?= base_url('client/tickets/store') ?>" method="post" enctype="multipart/form-data"
        class="fiori-card p-6 space-y-4">
        <?= csrf_field() ?>

        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Subject</label>
            <input type="text" name="subject" class="fiori-input" placeholder="What is the issue?" required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Category</label>
                <select name="category" id="category" class="fiori-input" required>
                    <option value="" disabled selected>Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= esc($cat['name']) ?>" data-id="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Subcategory (Optional)</label>
                <select name="subcategory" id="subcategory" class="fiori-input">
                    <option value="" selected>Select Subcategory</option>
                    <!-- Options populated by JS -->
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Priority</label>
                <select name="priority" class="fiori-input">
                    <option value="Low">Low</option>
                    <option value="Medium" selected>Medium</option>
                    <option value="High">High</option>
                    <option value="Urgent">Urgent</option>
                </select>
            </div>
        </div>

        <div class="space-y-2 mt-2">
            <label class="block text-xs font-semibold uppercase tracking-wider" style="color:var(--fiori-text-secondary);">Attach Photos (Optional)</label>
            <div id="dropzone"
                class="relative group border-2 border-dashed border-gray-200 hover:border-[#1e72af] hover:bg-blue-50/30 rounded-2xl p-8 transition-all duration-300 text-center cursor-pointer">
                <input type="file" name="attachments[]" id="attachments" accept="image/*" multiple
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                <div id="dropzone-prompt" class="space-y-3">
                    <div
                        class="w-12 h-12 bg-blue-50 text-[#1e72af] rounded-full flex items-center justify-center mx-auto group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[28px]">add_a_photo</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-700">Click to upload or drag and drop multiple photos</p>
                        <p class="text-[11px] text-gray-400">PNG, JPG or GIF (max. 2MB per file)</p>
                    </div>
                </div>
                <div id="preview-list" class="hidden mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <!-- Previews will be injected here -->
                </div>
            </div>
        </div>

        <div class="mt-2">
            <label class="block text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--fiori-text-secondary);">External Links (Optional)</label>
            <div id="links-container" class="space-y-2">
                <div class="flex gap-2">
                    <input type="url" name="external_links[]" class="fiori-input" placeholder="https://example.com/shared-folder">
                    <button type="button" id="add-link" class="btn btn-outline" style="width:40px; padding:0; flex-shrink:0;">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                    </button>
                </div>
            </div>
            <p class="text-[10px] text-gray-400 mt-1">Add links to Google Drive, Dropbox, or other shared resources.</p>
        </div>

        <div class="mt-2">
            <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Description</label>
            <textarea name="description" rows="5" class="fiori-input" placeholder="Describe your issue in detail..." required></textarea>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t mt-4" style="border-color:var(--fiori-border);">
            <button type="submit" class="btn btn-accent">Submit Ticket</button>
            <a href="<?= base_url('client/tickets') ?>" class="fiori-btn--regular" style="padding:0 16px; display:inline-flex; align-items:center; height:36px; border:1px solid var(--fiori-border); border-radius:4px; font-size:0.8125rem; font-weight:500; text-decoration:none;">Cancel</a>
        </div>
    </form>
</div>

<script>
    // --- DYNAMIC SUBCATEGORY FILTERING ---
    const categorySelect = document.getElementById('category');
    const subcategorySelect = document.getElementById('subcategory');
    const subcategories = <?= json_encode($subcategories) ?>;

    categorySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const parentId = selectedOption.getAttribute('data-id');
        
        // Clear current subcategories
        subcategorySelect.innerHTML = '<option value="" selected>Select Subcategory</option>';
        
        if (parentId) {
            const filtered = subcategories.filter(sub => sub.parent_id == parentId);
            
            if (filtered.length > 0) {
                filtered.forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.name;
                    option.textContent = sub.name;
                    subcategorySelect.appendChild(option);
                });
                subcategorySelect.disabled = false;
            } else {
                subcategorySelect.disabled = true;
            }
        } else {
            subcategorySelect.disabled = true;
        }
    });

    // --- MULTIPLE FILE PREVIEWS ---
    const attachmentsInput = document.getElementById('attachments');
    const previewList = document.getElementById('preview-list');
    const prompt = document.getElementById('dropzone-prompt');

    let ticketFilesDT = new DataTransfer();

    attachmentsInput.addEventListener('change', function (e) {
        if (e.target.files && e.target.files.length > 0) {
            Array.from(e.target.files).forEach(file => {
                ticketFilesDT.items.add(file);
            });
            attachmentsInput.files = ticketFilesDT.files;
        }
        renderTicketPreviews();
    });

    window.removeTicketFile = (indexToDel) => {
        const newDT = new DataTransfer();
        Array.from(ticketFilesDT.files).forEach((file, index) => {
            if (index !== indexToDel) newDT.items.add(file);
        });
        ticketFilesDT = newDT;
        attachmentsInput.files = ticketFilesDT.files;
        renderTicketPreviews();
    };

    window.renderTicketPreviews = () => {
        previewList.innerHTML = '';
        
        if (ticketFilesDT.files.length > 0) {
            previewList.classList.replace('hidden', 'grid');
            prompt.classList.add('opacity-30');

            Array.from(ticketFilesDT.files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        const div = document.createElement('div');
                        div.className = 'relative group/item';
                        div.innerHTML = `
                            <div class="aspect-square rounded-lg overflow-hidden border border-gray-200 shadow-sm bg-white">
                                <img src="${event.target.result}" class="w-full h-full object-cover">
                            </div>
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/item:opacity-100 transition-opacity flex flex-col items-center justify-center rounded-lg p-2">
                                <p class="text-[9px] text-white font-bold truncate w-full text-center">${file.name}</p>
                            </div>
                            <button type="button" onclick="removeTicketFile(${index})" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-red-600 transition-colors opacity-0 group-hover/item:opacity-100 cursor-pointer z-10" title="Remove image">
                                <span class="material-symbols-outlined text-[12px]">close</span>
                            </button>
                        `;
                        previewList.appendChild(div);
                    }
                    reader.readAsDataURL(file);
                }
            });
        } else {
            previewList.classList.replace('grid', 'hidden');
            prompt.classList.remove('opacity-30');
        }
    };

    // Dynamic Link Addition
    document.getElementById('add-link').addEventListener('click', function() {
        const container = document.getElementById('links-container');
        const div = document.createElement('div');
        div.className = 'flex gap-2 mt-2';
        div.innerHTML = `
            <input type="url" name="external_links[]" class="fiori-input" placeholder="https://example.com/another-link">
            <button type="button" class="btn btn-outline text-red-500 hover:bg-red-50" style="width:40px; padding:0; flex-shrink:0;" onclick="this.parentElement.remove()">
                <span class="material-symbols-outlined text-[20px]">delete</span>
            </button>
        `;
        container.appendChild(div);
    });
</script>
<?= $this->endSection() ?>