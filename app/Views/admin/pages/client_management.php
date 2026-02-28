<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Client Registry</h1>
            <div class="h-1.5 w-12 rounded-full mt-2" style="background-color: var(--clr-green);"></div>
            <p class="text-gray-500 mt-3 font-medium">Manage corporate partners and dedicated HR portal access
                credentials.</p>
        </div>

        <button onclick="toggleModal('addClientModal')" class="btn btn-success px-6 shadow-md shadow-emerald-100">
            <span class="material-symbols-outlined text-[20px]">add_business</span>
            <span>Register New Client</span>
        </button>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Company
                        Identity</th>
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">HR
                        Representative</th>
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">System Email
                    </th>
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] text-center">
                        Inquiry History</th>
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] text-center">
                        Manage</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if (!empty($client_list)): ?>
                    <?php foreach ($client_list as $client): ?>
                        <tr class="hover:bg-gray-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                                        style="background-color: rgba(30, 114, 175, 0.08); color: var(--clr-blue);">
                                        <span class="material-symbols-outlined text-[22px]">domain</span>
                                    </div>
                                    <span class="font-bold text-gray-900 text-base"><?= $client['company_name'] ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-700"><?= $client['hr_contact'] ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-500 font-medium"><?= $client['email'] ?></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="<?= base_url('superadmin/tickets?client_id=' . $client['id']) ?>" class="btn btn-info py-1.5 px-4 text-[11px] uppercase tracking-wider shadow-none">
                                    View Logs
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-3">
                                    <button onclick="openEditClientModal(<?= $client['id'] ?>, '<?= esc(addslashes($client['company_name'])) ?>', '<?= esc(addslashes($client['hr_contact'])) ?>', '<?= esc(addslashes($client['email'])) ?>')"
                                        class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-[#1e72af] hover:bg-blue-50 rounded-xl transition-all">
                                        <span class="material-symbols-outlined text-[20px]">edit_square</span>
                                    </button>
                                    <button onclick="confirmAction(event, '<?= base_url('superadmin/client-management/delete/'.$client['id']) ?>', 'Delete Corporate Client?', 'WARNING: This action cannot be undone and will permanently revoke their system access!', 'Yes, delete it', '#eb6063')"
                                        class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-[#eb6063] hover:bg-red-50 rounded-xl transition-all">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-24 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full flex items-center justify-center mb-6"
                                    style="background-color: rgba(50, 151, 202, 0.05); color: var(--clr-cyan);">
                                    <span class="material-symbols-outlined text-5xl">corporate_fare</span>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900">No corporate clients found</h3>
                                <p class="text-gray-500 text-sm mt-2 max-w-xs mx-auto leading-relaxed">Your business partner
                                    registry is empty. Start by registering a new corporate client.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="addClientModal"
    class="fixed inset-0 z-[2000] hidden bg-gray-900/60 backdrop-blur-md overflow-y-auto w-full h-full">
    <div class="flex items-center justify-center min-h-screen px-4 py-10 w-full h-full">
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
            <div class="px-8 py-7 border-b border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-cyan-100"
                    style="background-color: var(--clr-cyan);">
                    <span class="material-symbols-outlined">add_business</span>
                </div>
                <div>
                    <h3 class="text-xl font-black text-gray-900">Register Client</h3>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">HRWeb Partner Onboarding</p>
                </div>
            </div>

            <form action="<?= base_url('superadmin/client-management/store') ?>" method="POST" class="p-8">
                <?= csrf_field() ?>
                <div class="space-y-6">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Company
                            Name</label>
                        <input type="text" name="company_name"
                            class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold"
                            placeholder="Acme Corporation" required>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">HR Representative (TSR)</label>
                            <select name="hr_contact" required
                                class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold appearance-none cursor-pointer">
                                <option value="">Select a TSR...</option>
                                <?php if (isset($tsr_list) && !empty($tsr_list)): ?>
                                    <?php foreach ($tsr_list as $tsr): ?>
                                        <option value="<?= esc($tsr['email']) ?>"><?= esc($tsr['email']) ?></option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No Active TSRs Available</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Temp
                                Password</label>
                            <div class="relative">
                                <input type="password" name="password" id="add_client_password"
                                    class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold pr-12"
                                    required>
                                <button type="button" onclick="togglePassword('add_client_password', 'eyeIconAddClient')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors flex items-center focus:outline-none">
                                    <span class="material-symbols-outlined notranslate text-[22px]" id="eyeIconAddClient">visibility</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Corporate
                            Email</label>
                        <input type="email" name="email"
                            class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold"
                            placeholder="hr@company.com" required>
                    </div>
                </div>

                <div class="flex gap-4 mt-10">
                    <button type="button" onclick="toggleModal('addClientModal')"
                        class="flex-1 px-4 py-4 border-2 border-gray-100 text-gray-400 font-bold rounded-2xl hover:bg-gray-50 transition-colors uppercase text-xs tracking-widest">Discard</button>
                    <button type="submit" class="flex-1 btn btn-primary py-4 uppercase text-xs tracking-widest">Confirm
                        Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Client Modal -->
<div id="editClientModal"
    class="fixed inset-0 z-[2000] hidden bg-gray-900/60 backdrop-blur-md overflow-y-auto w-full h-full">
    <div class="flex items-center justify-center min-h-screen px-4 py-10 w-full h-full">
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
            <div class="px-8 py-7 border-b border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-100"
                    style="background-color: var(--clr-blue);">
                    <span class="material-symbols-outlined">edit_note</span>
                </div>
                <div>
                    <h3 class="text-xl font-black text-gray-900">Edit Client</h3>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Update Partner Details</p>
                </div>
            </div>

            <form id="editClientForm" method="POST" class="p-8">
                <?= csrf_field() ?>
                <div class="space-y-6">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Company
                            Name</label>
                        <input type="text" name="company_name" id="edit_company_name"
                            class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold"
                            required>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">HR Representative (TSR)</label>
                            <select name="hr_contact" id="edit_hr_contact" required
                                class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold appearance-none cursor-pointer">
                                <option value="">Select a TSR...</option>
                                <?php if (isset($tsr_list) && !empty($tsr_list)): ?>
                                    <?php foreach ($tsr_list as $tsr): ?>
                                        <option value="<?= esc($tsr['email']) ?>"><?= esc($tsr['email']) ?></option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No Active TSRs Available</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">New
                                Password <span class="text-gray-400 normal-case font-normal">(Optional)</span></label>
                            <div class="relative">
                                <input type="password" name="password" id="edit_client_password"
                                    class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold pr-12"
                                    placeholder="Leave blank to keep current">
                                <button type="button" onclick="togglePassword('edit_client_password', 'eyeIconEditClient')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors flex items-center focus:outline-none">
                                    <span class="material-symbols-outlined notranslate text-[22px]" id="eyeIconEditClient">visibility</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Corporate
                            Email</label>
                        <input type="email" name="email" id="edit_email"
                            class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold"
                            required>
                    </div>
                </div>

                <div class="flex gap-4 mt-10">
                    <button type="button" onclick="toggleModal('editClientModal')"
                        class="flex-1 px-4 py-4 border-2 border-gray-100 text-gray-400 font-bold rounded-2xl hover:bg-gray-50 transition-colors uppercase text-xs tracking-widest">Discard</button>
                    <button type="submit" class="flex-1 btn btn-info py-4 uppercase text-xs tracking-widest text-white shadow-none">Save
                        Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function openEditClientModal(id, companyName, hrContact, email) {
        // Set form action dynamically based on ID
        document.getElementById('editClientForm').action = `<?= base_url('superadmin/client-management/update') ?>/${id}`;
        
        // Populate inputs
        document.getElementById('edit_company_name').value = companyName;
        document.getElementById('edit_email').value = email;
        
        // Select matching TSR
        const hrContactSelect = document.getElementById('edit_hr_contact');
        if(hrContactSelect) {
            for(let i=0; i < hrContactSelect.options.length; i++) {
                if(hrContactSelect.options[i].value === hrContact) {
                    hrContactSelect.selectedIndex = i;
                    break;
                }
            }
        }

        toggleModal('editClientModal');
    }

    // confirmClientDelete is removed in favor of global confirmAction

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