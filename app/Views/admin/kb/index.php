<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<style>
    /* Custom Scrollbar for premium feel */
    .custom-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scroll::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; transition: all 0.3s ease; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #1e72af; }

    /* Sharp Box Outlines */
    .kb-container { background: white; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.05), 0 4px 6px -4px rgb(0 0 0 / 0.05); }
    .kb-section-header { background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; }
    img { -webkit-user-drag: none; user-select: none; }
</style>

<div class="p-8 bg-[#f4f6f9] min-h-screen">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                <i class="fas fa-robot text-clr-blue bg-blue-50 p-3 rounded-2xl"></i> Bot Knowledge Manager
            </h1>
            <p class="text-slate-500 font-medium mt-2 ml-1">Train your AI with automated guides and step-by-step visuals.</p>
        </div>
        <button onclick="document.getElementById('cat-modal').classList.remove('hidden')" 
                class="bg-slate-900 text-white px-8 py-4 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] flex items-center gap-3 shadow-lg hover:shadow-xl hover:bg-clr-blue transition-all active:scale-95">
            <i class="fas fa-plus"></i> New Category
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <div class="lg:col-span-4 sticky top-8">
            <div class="kb-container rounded-[2rem] flex flex-col max-h-[calc(100vh-120px)] overflow-hidden">
                <div class="kb-section-header px-8 py-6 shrink-0 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-clr-blue/10 flex items-center justify-center text-clr-blue">
                        <i class="fas fa-pen text-xs"></i>
                    </div>
                    <h2 class="text-xs font-black text-slate-700 uppercase tracking-[0.15em]">Create New Guide</h2>
                </div>
                
                <div class="p-8 overflow-y-auto custom-scroll flex-1 bg-white">
                    <form action="<?= base_url('superadmin/kb/store') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <?= csrf_field() ?>
                        
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Category</label>
                            <div class="relative">
                                <i class="fas fa-folder absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <select name="category_id" required class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-700 focus:bg-white focus:border-clr-blue focus:ring-4 focus:ring-clr-blue/10 transition-all outline-none appearance-none cursor-pointer">
                                    <option value="" disabled selected>Select a folder...</option>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Article Question</label>
                            <div class="relative">
                                <i class="fas fa-question-circle absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="text" name="question" required placeholder="e.g., How to download records?" 
                                       class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-700 placeholder-slate-400 focus:bg-white focus:border-clr-blue focus:ring-4 focus:ring-clr-blue/10 transition-all outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Trigger Keywords</label>
                            <div class="relative">
                                <i class="fas fa-tags absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="text" name="keywords" placeholder="Setup_Cutoff, Payroll_Rules" 
                                       class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-700 placeholder-slate-400 focus:bg-white focus:border-clr-blue focus:ring-4 focus:ring-clr-blue/10 transition-all outline-none">
                            </div>
                            <p class="text-[9px] text-slate-400 font-medium mt-2 ml-2">Separate multiple tags with commas.</p>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Bot Instructions</label>
                            <textarea name="answer" rows="6" required placeholder="1. Go to Transaction Menu...&#10;[IMAGE:1]&#10;2. Click Save..."
                                      class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-700 placeholder-slate-400 focus:bg-white focus:border-clr-blue focus:ring-4 focus:ring-clr-blue/10 transition-all outline-none custom-scroll resize-y"></textarea>
                            
                            <div class="mt-3 p-3 bg-blue-50/50 border border-blue-100 rounded-xl flex items-start gap-3">
                                <i class="fas fa-info-circle text-clr-blue mt-0.5"></i>
                                <p class="text-[10px] text-slate-500 leading-relaxed font-medium">
                                    To insert photos seamlessly, type <strong class="text-clr-blue bg-white px-1.5 py-0.5 rounded shadow-sm border border-blue-100">[IMAGE:1]</strong> for the first attached file, <strong class="text-clr-blue bg-white px-1.5 py-0.5 rounded shadow-sm border border-blue-100">[IMAGE:2]</strong> for the second, and so on.
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Attach Visuals</label>
                            <label for="kb_images" class="group flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-slate-300 rounded-[1.5rem] bg-slate-50 hover:bg-blue-50/50 hover:border-clr-blue transition-all cursor-pointer overflow-hidden relative">
                                <div id="ph" class="text-center group-hover:scale-105 transition-transform duration-300">
                                    <div class="w-10 h-10 bg-white shadow-sm rounded-full flex items-center justify-center mx-auto mb-2 text-slate-400 group-hover:text-clr-blue transition-colors">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Click to Upload Files</p>
                                </div>
                                
                                <div id="pv" class="hidden flex flex-col items-center justify-center w-full h-full bg-emerald-50/50">
                                    <div class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center text-white shadow-md mb-2">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <p id="fn" class="text-[10px] font-black text-emerald-600 uppercase tracking-widest"></p>
                                </div>
                                <input id="kb_images" name="kb_images[]" type="file" accept="image/*" class="hidden" multiple onchange="previewFiles(this)">
                            </label>
                            <div id="file-list-container" class="mt-3 space-y-1.5 hidden"></div>
                        </div>

                        <button type="submit" class="w-full bg-[#1e72af] text-white py-4 rounded-2xl font-black uppercase text-xs tracking-[0.15em] shadow-lg hover:shadow-xl hover:bg-[#165a8a] transition-all active:scale-[0.98] flex items-center justify-center gap-3 mt-4">
                            <i class="fas fa-paper-plane"></i> Deploy to Bot
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8">
            <div class="kb-container rounded-[2rem] overflow-hidden">
                <div class="kb-section-header px-10 py-7 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-500">
                            <i class="fas fa-list text-xs"></i>
                        </div>
                        <h2 class="text-[11px] font-black text-slate-700 uppercase tracking-[0.15em]">Active Guides</h2>
                    </div>
                    <span class="text-[10px] font-black text-clr-blue uppercase bg-blue-50 px-4 py-2 rounded-full border border-blue-100">Total: <?= count($articles) ?></span>
                </div>
                <div class="overflow-x-auto custom-scroll">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] border-b border-slate-100 bg-slate-50/50">
                                <th class="px-10 py-5">Category</th>
                                <th class="px-6 py-5">Question</th>
                                <th class="px-10 py-5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php foreach($articles as $art): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-10 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-500 shadow-sm">
                                            <i class="fas fa-folder-open text-[10px]"></i>
                                        </div>
                                        <span class="text-[10px] font-bold text-slate-600 uppercase tracking-widest"><?= esc($art['category_name']) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 font-bold text-sm text-slate-800"><?= esc($art['question']) ?></td>
                                <td class="px-10 py-5 text-right">
                                    <button onclick="openDeleteModal('<?= base_url('superadmin/kb/delete/'.$art['id']) ?>')" 
                                            class="w-9 h-9 rounded-xl bg-red-50 text-red-500 border border-red-100 transition-all hover:bg-red-500 hover:text-white shadow-sm hover:shadow-md">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="cat-modal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[100] p-6 transition-opacity">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden border border-slate-200">
        <form action="<?= base_url('superadmin/kb/storeCategory') ?>" method="POST" class="p-8 space-y-6">
            <?= csrf_field() ?>
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600"><i class="fas fa-folder-plus"></i></div>
                    <h2 class="text-lg font-black text-slate-900 tracking-tight">New Category</h2>
                </div>
                <button type="button" onclick="document.getElementById('cat-modal').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Folder Name</label>
                <div class="relative">
                    <i class="fas fa-edit absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="name" required class="w-full pl-11 pr-4 py-4 rounded-2xl border border-slate-200 font-bold text-slate-700 bg-slate-50 focus:bg-white focus:border-clr-blue focus:ring-4 focus:ring-clr-blue/10 transition-all outline-none">
                </div>
            </div>
            
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Choose Icon</label>
                <div class="grid grid-cols-5 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-200 max-h-48 overflow-y-auto custom-scroll">
                    <?php 
                    $icons = ['fas fa-folder', 'fas fa-money-bill', 'fas fa-clock', 'fas fa-user-circle', 'fas fa-file-invoice', 'fas fa-cog', 'fas fa-briefcase', 'fas fa-id-card', 'fas fa-envelope', 'fas fa-tools', 'fas fa-shield-alt'];
                    foreach($icons as $icon): 
                    ?>
                    <button type="button" onclick="selIcon('<?= $icon ?>', this)" 
                            class="ico-opt w-12 h-12 bg-white rounded-xl border border-slate-200 flex items-center justify-center text-slate-400 hover:text-clr-blue hover:border-clr-blue transition-all active:scale-95 shadow-sm hover:shadow">
                        <i class="<?= $icon ?>"></i>
                    </button>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="icon" id="icon-input" value="fas fa-folder">
            </div>
            
            <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black uppercase text-[11px] tracking-widest shadow-xl transition-all hover:bg-clr-blue active:scale-[0.98]">
                Confirm Category
            </button>
        </form>
    </div>
</div>

<div id="delete-modal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[110] p-6 transition-opacity">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-sm overflow-hidden border border-slate-200 animate-in zoom-in-95 duration-200">
        <div class="p-10 text-center">
            <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-trash-alt text-3xl text-red-500"></i>
            </div>
            <h3 class="font-black text-slate-900 text-xl mb-2">Delete this guide?</h3>
            <p class="text-sm text-slate-500 font-medium">This will also delete the uploaded images. This action cannot be undone.</p>
        </div>
        <div class="flex p-5 gap-4 bg-slate-50 border-t border-slate-100">
            <button onclick="document.getElementById('delete-modal').classList.add('hidden')" class="flex-1 py-3.5 rounded-xl font-bold text-slate-600 bg-white border border-slate-200 transition-colors hover:bg-slate-100">Cancel</button>
            <a id="confirm-delete-link" href="#" class="flex-1 py-3.5 rounded-xl font-bold text-white bg-red-500 text-center shadow-md transition-all hover:bg-red-600 hover:shadow-lg">Delete</a>
        </div>
    </div>
</div>

<script>
    /**
     * Enhanced Preview function: Updates the main dropzone text 
     * AND generates a neat visual list of files below it.
     */
    function previewFiles(input) { 
        const container = document.getElementById('file-list-container');
        container.innerHTML = ''; // Clear old lists
        
        if (input.files && input.files.length > 0) { 
            // Update dropzone UI
            document.getElementById('fn').textContent = input.files.length + (input.files.length === 1 ? " File Selected" : " Files Selected"); 
            document.getElementById('pv').classList.remove('hidden'); 
            document.getElementById('ph').classList.add('hidden'); 
            
            // Build visual file list
            container.classList.remove('hidden');
            Array.from(input.files).forEach((file, index) => {
                const item = document.createElement('div');
                item.className = 'flex items-center gap-3 bg-white border border-slate-200 p-2.5 rounded-xl shadow-sm';
                item.innerHTML = `
                    <div class="w-7 h-7 bg-blue-50 text-clr-blue rounded-lg flex items-center justify-center shrink-0 text-[10px]">
                        <i class="fas fa-image"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-700 truncate">${file.name}</p>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">[IMAGE:${index + 1}]</p>
                    </div>
                `;
                container.appendChild(item);
            });
        } else {
            // Revert to empty state
            document.getElementById('pv').classList.add('hidden'); 
            document.getElementById('ph').classList.remove('hidden');
            container.classList.add('hidden');
        }
    }
    
    function selIcon(ico, el) { 
        document.getElementById('icon-input').value = ico; 
        document.querySelectorAll('.ico-opt').forEach(b => { 
            b.classList.remove('border-clr-blue', 'text-clr-blue', 'ring-2', 'ring-clr-blue/20'); 
            b.classList.add('border-slate-200', 'text-slate-400'); 
        }); 
        el.classList.replace('border-slate-200', 'border-clr-blue'); 
        el.classList.replace('text-slate-400', 'text-clr-blue'); 
        el.classList.add('ring-2', 'ring-clr-blue/20');
    }
    
    function openDeleteModal(url) { 
        document.getElementById('confirm-delete-link').href = url; 
        document.getElementById('delete-modal').classList.remove('hidden'); 
    }
</script>
<?= $this->endSection() ?>