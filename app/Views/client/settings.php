<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-8">
    
    <!-- Sidebar / Navigation -->
    <div class="w-full md:w-1/4">
        <h1 class="text-2xl font-black text-gray-900 tracking-tight italic mb-6">Settings</h1>
        
        <nav class="flex flex-col gap-2">
            <button onclick="switchTab('general')" id="tab-btn-general" class="text-left px-4 py-2 rounded transition-all font-bold" style="background:var(--fiori-blue-light); color:var(--fiori-blue);">
                <i class="fas fa-building w-6 text-center mr-2"></i> General Info
            </button>
            <button onclick="switchTab('security')" id="tab-btn-security" class="text-left px-4 py-2 rounded transition-all font-semibold" style="color:var(--fiori-text-secondary); hover:background:#f0f0f0;">
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
        <div id="tab-general" class="fiori-card p-0 overflow-hidden">
            <div class="fiori-card__header flex items-center gap-3">
                <div class="w-8 h-8 rounded shrink-0 flex items-center justify-center" style="background:var(--fiori-blue-light); color:var(--fiori-blue);">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <h2 class="fiori-card__title">General Information</h2>
                    <p class="text-[11px]" style="color:var(--fiori-text-muted);">These details are assigned by your system administrator.</p>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="flex flex-col md:flex-row gap-4 md:items-center border-b pb-4" style="border-color:var(--fiori-border);">
                    <div class="w-full md:w-1/3">
                        <label class="block text-xs font-semibold uppercase tracking-wider" style="color:var(--fiori-text-secondary);">Company Name</label>
                    </div>
                    <div class="w-full md:w-2/3 flex items-center gap-3">
                        <input type="text" class="fiori-input bg-gray-50 cursor-not-allowed" 
                               value="<?= $clientProfile['company_name'] ?? 'N/A' ?>" readonly>
                        <span class="text-gray-400" title="Contact administrator to change"><i class="fas fa-lock"></i></span>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4 md:items-center border-b pb-4" style="border-color:var(--fiori-border);">
                    <div class="w-full md:w-1/3">
                        <label class="block text-xs font-semibold uppercase tracking-wider" style="color:var(--fiori-text-secondary);">Primary HR Contact</label>
                    </div>
                    <div class="w-full md:w-2/3 flex items-center gap-3">
                        <input type="text" class="fiori-input bg-gray-50 cursor-not-allowed" 
                               value="<?= esc($hr_contact_name ?? 'Unassigned') ?>" readonly>
                        <span class="text-gray-400" title="Contact administrator to change"><i class="fas fa-lock"></i></span>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4 md:items-center border-b pb-4" style="border-color:var(--fiori-border);">
                    <div class="w-full md:w-1/3">
                        <label class="block text-xs font-semibold uppercase tracking-wider" style="color:var(--fiori-text-secondary);">Full Name</label>
                        <p class="text-[10px] mt-1" style="color:var(--fiori-text-muted);">Displayed on support tickets</p>
                    </div>
                    <div class="w-full md:w-2/3 flex items-center gap-3">
                        <form action="<?= base_url('client/settings/update') ?>" method="POST" class="flex-1 flex gap-2">
                            <?= csrf_field() ?>
                            <input type="text" name="full_name" class="fiori-input" 
                                   value="<?= esc($user['full_name'] ?? '') ?>" placeholder="Enter your full name">
                            <button type="submit" class="btn btn-outline" style="height:36px; padding:0 12px; font-size:0.75rem;">Save</button>
                        </form>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4 md:items-center">
                    <div class="w-full md:w-1/3">
                        <label class="block text-xs font-semibold uppercase tracking-wider" style="color:var(--fiori-text-secondary);">Email Address</label>
                        <p class="text-[10px] mt-1" style="color:var(--fiori-text-muted);">Used for login</p>
                    </div>
                    <div class="w-full md:w-2/3">
                        <input type="text" class="fiori-input bg-gray-50 cursor-not-allowed" 
                               value="<?= $user['email'] ?? 'N/A' ?>" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Tab -->
        <div id="tab-security" class="hidden fiori-card p-0 overflow-hidden">
            <div class="fiori-card__header flex items-center gap-3">
                <div class="w-8 h-8 rounded shrink-0 flex items-center justify-center" style="background:#e8f5e9; color:var(--fiori-positive);">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                    <h2 class="fiori-card__title">Security & Password</h2>
                    <p class="text-[11px]" style="color:var(--fiori-text-muted);">Update your authentication details.</p>
                </div>
            </div>
            
            <form action="<?= base_url('client/settings/update') ?>" method="POST" class="p-6">
                <div class="space-y-4 max-w-xl">
                    
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Current Password</label>
                        <div class="relative">
                            <input type="password" name="current_password" id="current_password" required
                                   class="fiori-input" 
                                   placeholder="Enter existing password">
                            <button type="button" onclick="togglePassword('current_password', 'eyeIconCurrent')" class="absolute right-3 top-1/2 -translate-y-1/2 focus:outline-none" style="color:var(--fiori-text-muted);">
                                <span class="material-symbols-outlined notranslate text-[18px]" id="eyeIconCurrent">visibility</span>
                            </button>
                        </div>
                    </div>

                    <div class="border-t my-6" style="border-color:var(--fiori-border);"></div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">New Password</label>
                        <div class="relative">
                            <input type="password" name="new_password" id="new_password" required minlength="6"
                                   class="fiori-input" 
                                   placeholder="Enter new password">
                            <button type="button" onclick="togglePassword('new_password', 'eyeIconNew')" class="absolute right-3 top-1/2 -translate-y-1/2 focus:outline-none" style="color:var(--fiori-text-muted);">
                                <span class="material-symbols-outlined notranslate text-[18px]" id="eyeIconNew">visibility</span>
                            </button>
                        </div>
                        <p class="text-[10px] mt-1.5" style="color:var(--fiori-text-muted);">Must be at least 6 characters long.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Confirm New Password</label>
                        <div class="relative">
                            <input type="password" name="confirm_password" id="confirm_password" required minlength="6"
                                   class="fiori-input" 
                                   placeholder="Confirm new password">
                            <button type="button" onclick="togglePassword('confirm_password', 'eyeIconConfirm')" class="absolute right-3 top-1/2 -translate-y-1/2 focus:outline-none" style="color:var(--fiori-text-muted);">
                                <span class="material-symbols-outlined notranslate text-[18px]" id="eyeIconConfirm">visibility</span>
                            </button>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="btn btn-accent">
                            <span class="material-symbols-outlined text-[16px]">save</span> Update Password
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
        
        btnGen.className = 'text-left px-4 py-2 rounded transition-all font-semibold hover:bg-gray-50';
        btnGen.style.color = 'var(--fiori-text-secondary)';
        btnSec.className = 'text-left px-4 py-2 rounded transition-all font-semibold hover:bg-gray-50';
        btnSec.style.color = 'var(--fiori-text-secondary)';
        
        // Show active tab & button
        document.getElementById('tab-' + tabName).classList.remove('hidden');
        
        const activeBtn = document.getElementById('tab-btn-' + tabName);
        activeBtn.className = 'text-left px-4 py-2 rounded transition-all font-bold';
        activeBtn.style.color = 'var(--fiori-blue)';
        activeBtn.style.background = 'var(--fiori-blue-light)';
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
