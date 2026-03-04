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
                <select name="category" class="fiori-input">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= esc($cat['name']) ?>"><?= esc($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
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
            <label class="block text-xs font-semibold uppercase tracking-wider" style="color:var(--fiori-text-secondary);">Attach Photo (Optional)</label>
            <div id="dropzone"
                class="relative group border-2 border-dashed border-gray-200 hover:border-[#1e72af] hover:bg-blue-50/30 rounded-2xl p-8 transition-all duration-300 text-center cursor-pointer">
                <input type="file" name="attachment" id="attachment" accept="image/*"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                <div id="dropzone-prompt" class="space-y-3">
                    <div
                        class="w-12 h-12 bg-blue-50 text-[#1e72af] rounded-full flex items-center justify-center mx-auto group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[28px]">add_a_photo</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-700">Click to upload or drag and drop</p>
                        <p class="text-[11px] text-gray-400">PNG, JPG or GIF (max. 2MB)</p>
                    </div>
                </div>
                <div id="preview-container"
                    class="hidden mt-2 pt-4 border-t border-gray-100 items-center justify-center gap-4">
                    <div class="relative w-16 h-16 rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                        <img id="image-preview" src="#" class="w-full h-full object-cover">
                    </div>
                    <div class="text-left">
                        <p id="file-name" class="text-xs font-bold text-gray-700 truncate max-w-[150px]"></p>
                        <button type="button" id="remove-file"
                            class="text-[10px] text-red-500 font-bold uppercase hover:underline">Remove file</button>
                    </div>
                </div>
            </div>
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
    const attachment = document.getElementById('attachment');
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('image-preview');
    const fileNameText = document.getElementById('file-name');
    const prompt = document.getElementById('dropzone-prompt');

    attachment.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                previewImage.src = event.target.result;
                fileNameText.textContent = file.name;
                previewContainer.classList.replace('hidden', 'flex');
                prompt.classList.add('opacity-30');
            }
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('remove-file').addEventListener('click', function () {
        attachment.value = '';
        previewContainer.classList.replace('flex', 'hidden');
        prompt.classList.remove('opacity-30');
    });
</script>
<?= $this->endSection() ?>