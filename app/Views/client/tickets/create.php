<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="p-8 max-w-4xl mx-auto">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Submit New Ticket</h2>
        <p class="text-gray-500 text-sm">Fill in the details below to reach our team.</p>
    </div>

    <form action="<?= base_url('client/tickets/store') ?>" method="post" enctype="multipart/form-data"
        class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        <?= csrf_field() ?>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Subject</label>
            <input type="text" name="subject"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#1e72af] focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                placeholder="What is the issue?" required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Category</label>
                <select name="category"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none focus:border-[#1e72af]">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= esc($cat['name']) ?>"><?= esc($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Priority</label>
                <select name="priority"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none focus:border-[#1e72af]">
                    <option value="Low">Low</option>
                    <option value="Medium" selected>Medium</option>
                    <option value="High">High</option>
                    <option value="Urgent">Urgent</option>
                </select>
            </div>
        </div>

        <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700">Attach Photo (Optional)</label>
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

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
            <textarea name="description" rows="5"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none focus:border-[#1e72af]"
                placeholder="Describe your issue in detail..." required></textarea>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-gray-50">
            <button type="submit"
                class="px-8 py-3 bg-[#1e72af] text-white rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">Submit
                Ticket</button>
            <a href="<?= base_url('client/tickets') ?>"
                class="px-8 py-3 bg-gray-50 text-gray-500 rounded-xl font-bold hover:bg-gray-100 transition-all">Cancel</a>
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