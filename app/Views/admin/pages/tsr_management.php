<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">TSR Accounts</h1>
            <div class="h-1.5 w-12 rounded-full mt-2" style="background-color: var(--clr-cyan);"></div>
            <p class="text-gray-500 mt-3 font-medium">Manage technical support representative access and account status.
            </p>
        </div>

        <button onclick="toggleModal('addTsrModal')" class="btn btn-info px-6 shadow-md shadow-cyan-100">
            <span class="material-symbols-outlined text-[20px]">person_add</span>
            <span>Add New TSR</span>
        </button>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">ID / Emp ID
                    </th>
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Full Name</th>
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Email Address
                    </th>
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Status</th>
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] text-center">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if (!empty($tsr_list)): ?>
                    <?php foreach ($tsr_list as $tsr): ?>
                        <tr class="hover:bg-gray-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-lg bg-cyan-50 text-cyan-700 text-xs font-bold border border-cyan-100">
                                    #<?= $tsr['employee_id'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-cyan-600"
                                        style="background-color: rgba(50, 151, 202, 0.1);">
                                        <span class="material-symbols-outlined text-[18px]">person</span>
                                    </div>
                                    <span class="font-bold text-gray-900"><?= $tsr['full_name'] ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500 font-medium"><?= $tsr['email'] ?></td>
                            <td class="px-6 py-4">
                                <?php if ($tsr['status'] === 'active'): ?>
                                    <span class="badge badge-active">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                        Active
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                        Inactive
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-[#1e72af] hover:bg-blue-50 rounded-xl transition-all"
                                        title="Edit Account">
                                        <span class="material-symbols-outlined text-[20px]">edit_square</span>
                                    </button>
                                    <button onclick="confirmAction(event, '<?= base_url('superadmin/tsr-management/delete/'.$tsr['id']) ?>', 'Delete TSR Account?', 'WARNING: This action cannot be undone and will permanently revoke their system access!', 'Yes, delete it', '#eb6063')"
                                        class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-[#eb6063] hover:bg-red-50 rounded-xl transition-all"
                                        title="Remove Account">
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
                                    style="background-color: rgba(30, 114, 175, 0.05); color: var(--clr-blue);">
                                    <span class="material-symbols-outlined text-5xl">group_off</span>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900">No accounts found</h3>
                                <p class="text-gray-500 text-sm mt-2 max-w-xs mx-auto">There are currently no TSR accounts
                                    registered in the system.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="addTsrModal"
    class="fixed inset-0 z-[2000] hidden bg-gray-900/60 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-7 border-b border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-cyan-100"
                style="background-color: var(--clr-cyan);">
                <span class="material-symbols-outlined">person_add</span>
            </div>
            <div>
                <h3 class="text-xl font-black text-gray-900">Add TSR</h3>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Registry Onboarding</p>
            </div>
        </div>

        <form action="<?= base_url('superadmin/tsr-management/store') ?>" method="POST" class="p-8">
            <?= csrf_field() ?>
            <div class="space-y-6">
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Full
                        Name</label>
                    <input type="text" name="full_name"
                        class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold"
                        placeholder="John Doe" required>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Employee
                        ID</label>
                    <input type="text" name="employee_id"
                        class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold"
                        placeholder="EMP-001" required>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Email
                        Address</label>
                    <input type="email" name="email"
                        class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold"
                        placeholder="tsr@hrweb.com" required>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Temp
                        Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="tsr_password"
                            class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold pr-12"
                            required>
                        <button type="button" onclick="togglePassword('tsr_password', 'eyeIconTsr')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors flex items-center focus:outline-none">
                            <span class="material-symbols-outlined notranslate text-[22px]" id="eyeIconTsr">visibility</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 mt-10">
                <button type="button" onclick="toggleModal('addTsrModal')"
                    class="flex-1 px-4 py-4 border-2 border-gray-100 text-gray-400 font-bold rounded-2xl hover:bg-gray-50 transition-colors uppercase text-xs tracking-widest">Cancel</button>
                <button type="submit" class="flex-1 btn btn-primary py-4 uppercase text-xs tracking-widest">Create
                    Account</button>
            </div>
        </form>
    </div>
</div>

<script>
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