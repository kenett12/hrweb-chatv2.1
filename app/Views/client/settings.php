<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-8">
    
    <!-- Sidebar / Navigation -->
    <div class="w-full md:w-1/4">
        <h1 class="text-2xl font-black text-gray-900 tracking-tight italic mb-6">Settings</h1>
        
        <nav class="flex flex-col gap-2">
            <button onclick="switchTab('general')" id="tab-btn-general" class="text-left px-5 py-3 rounded-xl transition-all font-bold bg-[#1e72af]/10 text-[#1e72af]">
                <i class="fas fa-building w-6 text-center mr-2"></i> General Info
            </button>
            <button onclick="switchTab('security')" id="tab-btn-security" class="text-left px-5 py-3 rounded-xl transition-all font-semibold text-gray-600 hover:bg-gray-50">
                <i class="fas fa-shield-alt w-6 text-center mr-2"></i> Security
            </button>
        </nav>
    </div>

    <!-- Main Content Area -->
    <div class="w-full md:w-3/4">
        <?php if (session()->getFlashdata('msg')): ?>
            <div class="bg-emerald-50 text-[#20ae5c] border border-emerald-100 px-6 py-4 rounded-xl mb-6 font-semibold flex items-center gap-3 shadow-sm">
                <i class="fas fa-check-circle"></i>
                <?= session()->getFlashdata('msg') ?>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-50 text-red-600 border border-red-100 px-6 py-4 rounded-xl mb-6 font-semibold flex items-center gap-3 shadow-sm">
                <i class="fas fa-exclamation-circle"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <!-- General Info Tab -->
        <div id="tab-general" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                    <i class="fas fa-building text-lg"></i>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900 text-lg">General Information</h2>
                    <p class="text-xs text-gray-400 mt-1">These details are assigned by your system administrator.</p>
                </div>
            </div>
            
            <div class="p-8 space-y-8">
                <div class="flex flex-col md:flex-row gap-6 md:items-center border-b border-gray-50 pb-6">
                    <div class="w-full md:w-1/3">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest">Company Name</label>
                    </div>
                    <div class="w-full md:w-2/3">
                        <input type="text" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-gray-600 font-semibold cursor-not-allowed focus:outline-none focus:ring-0" 
                               value="<?= $clientProfile['company_name'] ?? 'N/A' ?>" readonly>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-6 md:items-center border-b border-gray-50 pb-6">
                    <div class="w-full md:w-1/3">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest">Primary HR Contact</label>
                    </div>
                    <div class="w-full md:w-2/3 flex items-center gap-3">
                        <input type="text" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-gray-600 font-semibold cursor-not-allowed focus:outline-none focus:ring-0" 
                               value="<?= $clientProfile['hr_contact'] ?? 'N/A' ?>" readonly>
                        <span class="text-gray-400" title="Contact administrator to change"><i class="fas fa-lock"></i></span>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-6 md:items-center">
                    <div class="w-full md:w-1/3">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest">Email Address</label>
                        <p class="text-[10px] text-gray-400 mt-1">Used for login</p>
                    </div>
                    <div class="w-full md:w-2/3">
                        <input type="text" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-gray-600 font-semibold cursor-not-allowed focus:outline-none focus:ring-0" 
                               value="<?= $user['email'] ?? 'N/A' ?>" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Tab -->
        <div id="tab-security" class="hidden bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">
                    <i class="fas fa-shield-alt text-lg"></i>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900 text-lg">Security & Password</h2>
                    <p class="text-xs text-gray-400 mt-1">Update your authentication details.</p>
                </div>
            </div>
            
            <form action="<?= base_url('client/settings/update') ?>" method="POST" class="p-8">
                <div class="space-y-6 max-w-xl">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Current Password</label>
                        <div class="relative">
                            <input type="password" name="current_password" id="current_password" required
                                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-[#3297ca] focus:ring-1 focus:ring-[#3297ca] focus:outline-none transition-colors text-gray-800 font-medium pr-12" 
                                   placeholder="Enter existing password">
                            <button type="button" onclick="togglePassword('current_password', 'eyeIconCurrent')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors flex items-center focus:outline-none">
                                <span class="material-symbols-outlined notranslate text-[22px]" id="eyeIconCurrent">visibility</span>
                            </button>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 my-6"></div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">New Password</label>
                        <div class="relative">
                            <input type="password" name="new_password" id="new_password" required minlength="6"
                                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-[#3297ca] focus:ring-1 focus:ring-[#3297ca] focus:outline-none transition-colors text-gray-800 font-medium pr-12" 
                                   placeholder="Enter new password">
                            <button type="button" onclick="togglePassword('new_password', 'eyeIconNew')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors flex items-center focus:outline-none">
                                <span class="material-symbols-outlined notranslate text-[22px]" id="eyeIconNew">visibility</span>
                            </button>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-2">Must be at least 6 characters long.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Confirm New Password</label>
                        <div class="relative">
                            <input type="password" name="confirm_password" id="confirm_password" required minlength="6"
                                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-[#3297ca] focus:ring-1 focus:ring-[#3297ca] focus:outline-none transition-colors text-gray-800 font-medium pr-12" 
                                   placeholder="Confirm new password">
                            <button type="button" onclick="togglePassword('confirm_password', 'eyeIconConfirm')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors flex items-center focus:outline-none">
                                <span class="material-symbols-outlined notranslate text-[22px]" id="eyeIconConfirm">visibility</span>
                            </button>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="px-8 py-3 text-white font-bold rounded-xl transition-all shadow-md hover:-translate-y-0.5" style="background-color: var(--clr-blue);">
                            Update Password
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </div>
</div>

<script>
    function switchTab(tabName) {
        // Hide all tabs
        document.getElementById('tab-general').classList.add('hidden');
        document.getElementById('tab-security').classList.add('hidden');
        
        // Reset buttons styling
        const btnGen = document.getElementById('tab-btn-general');
        const btnSec = document.getElementById('tab-btn-security');
        
        btnGen.className = 'text-left px-5 py-3 rounded-xl transition-all font-semibold text-gray-600 hover:bg-gray-50';
        btnSec.className = 'text-left px-5 py-3 rounded-xl transition-all font-semibold text-gray-600 hover:bg-gray-50';
        
        // Show active tab & button
        document.getElementById('tab-' + tabName).classList.remove('hidden');
        
        const activeBtn = document.getElementById('tab-btn-' + tabName);
        activeBtn.className = 'text-left px-5 py-3 rounded-xl transition-all font-bold bg-[#1e72af]/10 text-[#1e72af]';
    }

    // Auto-open security tab if there's an error flashdata (usually related to password update)
    <?php if (session()->getFlashdata('error')): ?>
        switchTab('security');
    <?php endif; ?>

    /**
     * Toggles password visibility between text and dots
     */
    function togglePassword(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const eyeIcon = document.getElementById(iconId);
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.textContent = 'visibility_off';
        } else {
            passwordInput.type = 'password';
            eyeIcon.textContent = 'visibility';
        }
    }
</script>
<?= $this->endSection() ?>
