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
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] text-center">Total Accounts
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
                                <div class="flex flex-wrap gap-1">
                                    <?php 
                                        $contacts = json_decode($client['hr_contact'], true);
                                        $hasPrinted = false;

                                        if (is_array($contacts)) {
                                            if (isset($contacts['lead']) || isset($contacts['co1']) || isset($contacts['co2'])) {
                                                if (!empty($contacts['lead'])) {
                                                    echo '<span class="inline-block px-2 py-1 bg-blue-50 text-blue-600 rounded-md text-[11px] font-bold uppercase tracking-wider tooltip" title="Lead Support">' . esc($contacts['lead']) . '</span> ';
                                                    $hasPrinted = true;
                                                }
                                                if (!empty($contacts['co1'])) {
                                                    echo '<span class="inline-block px-2 py-1 bg-gray-50 text-gray-500 border border-gray-200 rounded-md text-[11px] font-semibold uppercase tracking-wider tooltip" title="Co-TSR 1">' . esc($contacts['co1']) . '</span> ';
                                                    $hasPrinted = true;
                                                }
                                                if (!empty($contacts['co2'])) {
                                                    echo '<span class="inline-block px-2 py-1 bg-gray-50 text-gray-500 border border-gray-200 rounded-md text-[11px] font-semibold uppercase tracking-wider tooltip" title="Co-TSR 2">' . esc($contacts['co2']) . '</span>';
                                                    $hasPrinted = true;
                                                }
                                            } else if (!empty($contacts)) {
                                                foreach ($contacts as $contact) {
                                                    echo '<span class="inline-block px-2 py-1 bg-gray-100 text-gray-600 border border-gray-200 rounded-md text-[11px] font-bold uppercase tracking-wider">' . esc($contact) . '</span> ';
                                                    $hasPrinted = true;
                                                }
                                            }
                                        } elseif (!empty($client['hr_contact']) && $client['hr_contact'] !== 'null') {
                                            echo '<span class="font-semibold text-gray-700">'.esc($client['hr_contact']).'</span>';
                                            $hasPrinted = true;
                                        }

                                        if (!$hasPrinted) {
                                            echo '<span class="text-gray-400 italic text-[11px]">Unassigned</span>';
                                        }
                                    ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-gray-900 font-bold bg-gray-100 px-3 py-1 rounded-xl"><?= $client['account_count'] ?? 0 ?> Accounts</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="<?= base_url('superadmin/tickets?client_id=' . $client['client_id']) ?>" class="btn btn-info py-1.5 px-4 text-[11px] uppercase tracking-wider shadow-none">
                                    View Logs
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                    <button onclick="openManageAccountsModal(<?= $client['client_id'] ?>, '<?= esc(addslashes($client['company_name'])) ?>')"
                                        class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all" title="Manage Accounts">
                                        <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
                                    </button>
                                    <button onclick='openEditClientModal(<?= $client['client_id'] ?>, "<?= esc(addslashes($client['company_name'])) ?>", <?= json_encode($client['hr_contact'] ? json_decode($client['hr_contact'], true) : []) ?>)'
                                        class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-[#1e72af] hover:bg-blue-50 rounded-xl transition-all" title="Edit Company">
                                        <span class="material-symbols-outlined text-[20px]">edit_square</span>
                                    </button>
                                    <button onclick="confirmAction(event, '<?= base_url('superadmin/client-management/delete/'.$client['client_id']) ?>', 'Delete Corporate Client?', 'WARNING: This action cannot be undone and will permanently revoke all sub-accounts!', 'Yes, delete it', '#eb6063')"
                                        class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-[#eb6063] hover:bg-red-50 rounded-xl transition-all" title="Delete Company">
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

                    <div class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold text-[#3297ca] uppercase tracking-widest mb-2">* Lead Support (Required)</label>
                            <select name="lead_tsr" required
                                class="w-full px-4 py-4 bg-gray-50 border border-[#3297ca]/30 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold cursor-pointer">
                                <option value="" disabled selected>Select Lead TSR</option>
                                <?php if (isset($tsr_list) && !empty($tsr_list)): ?>
                                    <?php foreach ($tsr_list as $tsr): ?>
                                        <option value="<?= esc($tsr['email']) ?>"><?= esc($tsr['email']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Co-TSR 1 (Optional)</label>
                                <select name="co_tsr_1"
                                    class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold cursor-pointer text-sm">
                                    <option value="" selected>None</option>
                                    <?php if (isset($tsr_list) && !empty($tsr_list)): ?>
                                        <?php foreach ($tsr_list as $tsr): ?>
                                            <option value="<?= esc($tsr['email']) ?>"><?= esc($tsr['email']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Co-TSR 2 (Optional)</label>
                                <select name="co_tsr_2"
                                    class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold cursor-pointer text-sm">
                                    <option value="" selected>None</option>
                                    <?php if (isset($tsr_list) && !empty($tsr_list)): ?>
                                        <?php foreach ($tsr_list as $tsr): ?>
                                            <option value="<?= esc($tsr['email']) ?>"><?= esc($tsr['email']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
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

                    <div class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold text-[#3297ca] uppercase tracking-widest mb-2">* Lead Support (Required)</label>
                            <select name="lead_tsr" id="edit_lead_tsr" required
                                class="w-full px-4 py-4 bg-gray-50 border border-[#3297ca]/30 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold cursor-pointer">
                                <option value="" disabled>Select Lead TSR</option>
                                <?php if (isset($tsr_list) && !empty($tsr_list)): ?>
                                    <?php foreach ($tsr_list as $tsr): ?>
                                        <option value="<?= esc($tsr['email']) ?>"><?= esc($tsr['email']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Co-TSR 1 (Optional)</label>
                                <select name="co_tsr_1" id="edit_co_tsr_1"
                                    class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold cursor-pointer text-sm">
                                    <option value="">None</option>
                                    <?php if (isset($tsr_list) && !empty($tsr_list)): ?>
                                        <?php foreach ($tsr_list as $tsr): ?>
                                            <option value="<?= esc($tsr['email']) ?>"><?= esc($tsr['email']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Co-TSR 2 (Optional)</label>
                                <select name="co_tsr_2" id="edit_co_tsr_2"
                                    class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold cursor-pointer text-sm">
                                    <option value="">None</option>
                                    <?php if (isset($tsr_list) && !empty($tsr_list)): ?>
                                        <?php foreach ($tsr_list as $tsr): ?>
                                            <option value="<?= esc($tsr['email']) ?>"><?= esc($tsr['email']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 mt-10">
                    <button type="button" onclick="toggleModal('editClientModal')"
                        class="flex-1 px-4 py-4 border-2 border-gray-100 text-gray-400 font-bold rounded-2xl hover:bg-gray-50 transition-colors uppercase text-xs tracking-widest">Discard</button>
                    <button type="submit" class="flex-1 btn btn-info py-4 uppercase text-xs tracking-widest text-white shadow-none">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manage Accounts Modal -->
<div id="manageAccountsModal" class="fixed inset-0 z-[2000] hidden bg-gray-900/60 backdrop-blur-md overflow-y-auto w-full h-full">
    <div class="flex items-center justify-center min-h-screen px-4 py-10 w-full h-full">
        <div class="bg-white w-full max-w-4xl rounded-3xl shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200 flex flex-col max-h-[90vh]">
            <div class="px-8 py-7 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-100"
                        style="background-color: var(--clr-green);">
                        <span class="material-symbols-outlined">manage_accounts</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-gray-900" id="manage_accounts_company_title">Company Accounts</h3>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Manage Sub-Accounts</p>
                    </div>
                </div>
                <!-- Top right actions -->
                <div class="flex items-center gap-3">
                    <button onclick="openAddAccountModal()" class="btn btn-success px-4 py-2 text-sm shadow-md shadow-emerald-100">
                        <span class="material-symbols-outlined text-[18px]">person_add</span>
                        <span>Add Account</span>
                    </button>
                    <button onclick="toggleModal('manageAccountsModal')" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>

            <div class="p-8 overflow-y-auto flex-1">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Email / Login ID</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Role</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] text-center">Manage</th>
                            </tr>
                        </thead>
                        <tbody id="accountsTableBody" class="divide-y divide-gray-50">
                            <!-- Populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Sub-Account Modal -->
<div id="accountModal" class="fixed inset-0 z-[2010] hidden bg-gray-900/60 backdrop-blur-md overflow-y-auto w-full h-full">
    <div class="flex items-center justify-center min-h-screen px-4 py-10 w-full h-full">
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
            <div class="px-8 py-7 border-b border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-cyan-100 bg-emerald-500">
                    <span class="material-symbols-outlined" id="account_modal_icon">person_add</span>
                </div>
                <div>
                    <h3 class="text-xl font-black text-gray-900" id="account_modal_title">Add Sub-Account</h3>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Role-based Access</p>
                </div>
            </div>

            <form id="accountForm" method="POST" class="p-8">
                <?= csrf_field() ?>
                <input type="hidden" name="client_id" id="account_client_id" value="">
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Account Email</label>
                        <input type="email" name="email" id="account_email"
                            class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all text-gray-900 font-semibold"
                            required>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Access Role</label>
                            <select name="client_role" id="account_role" required
                                class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all text-gray-900 font-semibold appearance-none cursor-pointer">
                                <option value="">Select Role...</option>
                                <option value="HR">HR</option>
                                <option value="TK 1">TK 1</option>
                                <option value="TK 2">TK 2</option>
                                <option value="PAYROLL 1">PR 1</option>
                                <option value="PAYROLL 2">PR 2</option>
                                <option value="EXECOM">EXECOM</option>
                                <option value="IT">IT</option>
                                <option value="AUDITOR">AUDITOR</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Password <span class="text-gray-400 normal-case font-normal text-[10px]" id="account_pwd_optional"></span></label>
                            <div class="relative">
                                <input type="password" name="password" id="account_password"
                                    class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all text-gray-900 font-semibold pr-12"
                                    required>
                                <button type="button" onclick="togglePassword('account_password', 'eyeIconAccount')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors flex items-center focus:outline-none">
                                    <span class="material-symbols-outlined notranslate text-[22px]" id="eyeIconAccount">visibility</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 mt-10">
                    <button type="button" onclick="toggleModal('accountModal')"
                        class="flex-1 px-4 py-4 border-2 border-gray-100 text-gray-400 font-bold rounded-2xl hover:bg-gray-50 transition-colors uppercase text-xs tracking-widest">Discard</button>
                    <button type="submit" class="flex-1 btn btn-success py-4 uppercase text-xs tracking-widest shadow-none">Save Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function openEditClientModal(id, companyName, hrContactsObj) {
        // Set form action dynamically based on ID
        document.getElementById('editClientForm').action = `<?= base_url('superadmin/client-management/update') ?>/${id}`;
        
        // Populate inputs
        document.getElementById('edit_company_name').value = companyName;
        
        // Reset selections
        document.getElementById('edit_lead_tsr').value = "";
        document.getElementById('edit_co_tsr_1').value = "";
        document.getElementById('edit_co_tsr_2').value = "";

        // Populate selections from object
        if (hrContactsObj && typeof hrContactsObj === 'object') {
            if (hrContactsObj.lead) document.getElementById('edit_lead_tsr').value = hrContactsObj.lead;
            if (hrContactsObj.co1) document.getElementById('edit_co_tsr_1').value = hrContactsObj.co1;
            if (hrContactsObj.co2) document.getElementById('edit_co_tsr_2').value = hrContactsObj.co2;
        }

        toggleModal('editClientModal');
    }

    function openManageAccountsModal(clientId, companyName) {
        document.getElementById('manage_accounts_company_title').innerText = companyName + ' Accounts';
        document.getElementById('account_client_id').value = clientId; // For when we click Add Account
        
        // Show loading state
        const tbody = document.getElementById('accountsTableBody');
        tbody.innerHTML = `<tr><td colspan="3" class="px-6 py-10 text-center text-gray-400"><span class="material-symbols-outlined animate-spin">refresh</span> Loading accounts...</td></tr>`;
        
        toggleModal('manageAccountsModal');

        // Fetch accounts via AJAX
        fetch(`<?= base_url('superadmin/client-management/accounts') ?>/${clientId}`)
            .then(response => response.json())
            .then(data => {
                tbody.innerHTML = '';
                if(data.length > 0) {
                    data.forEach(account => {
                        let row = `
                            <tr class="hover:bg-gray-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-[16px]">person</span>
                                        </div>
                                        <span class="font-semibold text-gray-700">${account.email}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-[11px] font-bold uppercase tracking-wider">${account.client_role || 'No Role'}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openEditAccountModal(${account.id}, '${account.email}', '${account.client_role || ''}')"
                                            class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-[#1e72af] hover:bg-blue-50 rounded-xl transition-all" title="Edit Account">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        <button onclick="confirmAction(event, '<?= base_url('superadmin/client-management/delete-account') ?>/${account.id}', 'Delete Sub-Account?', 'This will revoke their access immediately!', 'Yes, delete', '#eb6063')"
                                            class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-[#eb6063] hover:bg-red-50 rounded-xl transition-all" title="Delete Account">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="3" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 bg-gray-50 text-gray-300">
                                        <span class="material-symbols-outlined text-4xl">group_off</span>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900">No sub-accounts yet</h3>
                                    <p class="text-gray-400 text-xs mt-1">Create an account to allow access.</p>
                                </div>
                            </td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                console.error("Error fetching accounts:", error);
                tbody.innerHTML = `<tr><td colspan="3" class="px-6 py-8 text-center text-red-500">Failed to load accounts.</td></tr>`;
            });
    }

    function openAddAccountModal() {
        document.getElementById('accountForm').action = `<?= base_url('superadmin/client-management/store-account') ?>`;
        document.getElementById('account_modal_title').innerText = "Add Sub-Account";
        document.getElementById('account_modal_icon').innerText = "person_add";
        
        document.getElementById('account_email').value = "";
        document.getElementById('account_role').value = "";
        
        const pwdInput = document.getElementById('account_password');
        pwdInput.required = true;
        pwdInput.placeholder = "Enter password";
        document.getElementById('account_pwd_optional').innerText = "";
        
        toggleModal('accountModal');
    }

    function openEditAccountModal(accountId, email, role) {
        document.getElementById('accountForm').action = `<?= base_url('superadmin/client-management/update-account') ?>/${accountId}`;
        document.getElementById('account_modal_title').innerText = "Edit Sub-Account";
        document.getElementById('account_modal_icon').innerText = "manage_accounts";
        
        document.getElementById('account_email').value = email;
        document.getElementById('account_role').value = role;
        
        const pwdInput = document.getElementById('account_password');
        pwdInput.required = false;
        pwdInput.placeholder = "Leave blank to keep unchanged";
        document.getElementById('account_pwd_optional').innerText = "(Optional)";
        
        toggleModal('accountModal');
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