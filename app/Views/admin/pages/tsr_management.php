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

    <!-- TABS NAVIGATION -->
    <div class="flex space-x-1 bg-gray-100/50 p-1.5 rounded-2xl w-max mb-6 border border-gray-200/50">
        <button onclick="switchTab('tsr_accounts_tab')" id="nav_tsr_accounts_tab" class="px-6 py-2.5 text-sm font-bold rounded-xl transition-all tab-btn bg-white text-gray-900 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
                TSR Accounts
            </div>
        </button>
        <button onclick="switchTab('kpi_overview_tab')" id="nav_kpi_overview_tab" class="px-6 py-2.5 text-sm font-bold rounded-xl transition-all tab-btn text-gray-500 hover:text-gray-700 hover:bg-gray-100">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">monitoring</span>
                KPI Overview
            </div>
        </button>
    </div>

    <!-- TAB 1: TSR ACCOUNTS -->
    <div id="tsr_accounts_tab" class="tab-content block">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm mx-auto overflow-hidden">
            <div class="overflow-x-auto w-full">
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
            </div> <!-- End overflow-x-auto -->
        </div> <!-- End bg-white -->
    </div> <!-- End tsr_accounts_tab -->

    <!-- TAB 2: KPI OVERVIEW -->
    <div id="kpi_overview_tab" class="tab-content hidden space-y-8">
        <!-- Assignments Table -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-black text-gray-900">Detailed Client Assignments</h3>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Current TSR Map</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Company / Client</th>
                            <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Lead Support</th>
                            <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Co-TSR 1</th>
                            <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Co-TSR 2</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if (!empty($detailed_assignments)): ?>
                            <?php foreach ($detailed_assignments as $assign): ?>
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-6 py-4 font-bold text-gray-900"><?= esc($assign['company']) ?></td>
                                    <td class="px-6 py-4">
                                        <?php if ($assign['lead']): ?>
                                            <span class="inline-block px-2 py-1 bg-blue-50 text-blue-600 rounded-md text-[11px] font-bold uppercase tracking-wider"><?= esc($assign['lead']) ?></span>
                                        <?php else: ?>
                                            <span class="text-gray-300 italic text-[11px]">Unassigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if ($assign['co1']): ?>
                                            <span class="inline-block px-2 py-1 bg-gray-50 border border-gray-200 text-gray-600 rounded-md text-[11px] font-bold uppercase tracking-wider"><?= esc($assign['co1']) ?></span>
                                        <?php else: ?>
                                            <span class="text-gray-300 italic text-[11px]">Unassigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if ($assign['co2']): ?>
                                            <span class="inline-block px-2 py-1 bg-gray-50 border border-gray-200 text-gray-600 rounded-md text-[11px] font-bold uppercase tracking-wider"><?= esc($assign['co2']) ?></span>
                                        <?php else: ?>
                                            <span class="text-gray-300 italic text-[11px]">Unassigned</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400 font-bold text-sm">No assignments found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- KPI Tally Overview Table -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-12">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-emerald-50/30">
                <div>
                    <h3 class="text-lg font-black text-gray-900">KPI Summary</h3>
                    <p class="text-xs font-bold text-emerald-500 uppercase tracking-widest mt-1">Utilization Overview</p>
                </div>
                <div class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded-xl border border-emerald-200">
                    <span class="text-[10px] font-bold uppercase tracking-widest opacity-70 block text-center mb-0.5">Target Minimum</span>
                    <span class="font-black text-xl flex justify-center"><?= esc($min_target) ?> Leads</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">TSR Account</th>
                            <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] text-center">Lead Clients</th>
                            <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] text-center">Co-Leads</th>
                            <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] text-center">% Utilization</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if (!empty($kpi_data)): ?>
                            <?php foreach ($kpi_data as $email => $data): ?>
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs uppercase">
                                                <?= substr($data['name'], 0, 2) ?>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900"><?= esc($data['name']) ?></p>
                                                <p class="text-xs text-gray-400"><?= esc($email) ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-gray-700">
                                        <span class="inline-block px-3 py-1 bg-gray-100 rounded-lg"><?= $data['leads'] ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-gray-500">
                                        <span class="inline-block px-3 py-1 bg-gray-50 rounded-lg"><?= $data['coleads'] ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php
                                            $util = $data['utilization'];
                                            $utilColor = $util >= 100 ? 'text-emerald-600 bg-emerald-50 border-emerald-200' :
                                                         ($util >= 50 ? 'text-amber-600 bg-amber-50 border-amber-200' :
                                                                       'text-red-500 bg-red-50 border-red-200');
                                        ?>
                                        <div class="inline-flex items-center gap-2 px-3 py-1 font-black text-sm rounded-lg border <?= $utilColor ?>">
                                            <?= $util ?>%
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400 font-bold text-sm">No KPI data available</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="addTsrModal"
    class="fixed inset-0 z-[2000] hidden bg-gray-900/60 backdrop-blur-md items-center justify-center p-4">
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
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 text-center">Full
                        Name</label>
                    <input type="text" name="full_name"
                        class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold text-center"
                        placeholder="John Doe" minlength="3" maxlength="100" pattern="[A-Za-z\s]+" title="Full name can only contain alphabetical characters and spaces." required>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 text-center">Employee ID</label>
                    <input type="text" name="employee_id" id="tsr_employee_id"
                        class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-extrabold text-center tracking-[0.3em] uppercase placeholder-gray-300"
                        placeholder="AAA-0000" maxlength="8" pattern="[A-Za-z]{3}-\d{4}" title="Employee ID must be exactly 3 letters followed by 4 numbers (e.g., AAA-0000)" required>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 text-center">Email
                        Address</label>
                    <div class="relative flex items-center justify-center">
                        <input type="text" name="email_prefix"
                            class="w-full pl-[95px] pr-[95px] py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-extrabold text-center placeholder-gray-300"
                            placeholder="example" pattern="[a-zA-Z0-9_\.]+" title="Email prefix can only contain letters, numbers, dots, and underscores." required>
                        <span class="absolute right-5 text-[#3297ca] font-extrabold select-none pointer-events-none">@hrweb.ph</span>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 text-center">Temp
                        Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="tsr_password"
                            class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#3297ca]/10 focus:border-[#3297ca] outline-none transition-all text-gray-900 font-semibold text-center"
                            minlength="8" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+" title="Password must contain at least one uppercase letter, one lowercase letter, and one number." required>
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
     * Toggles between main tabs (TSR vs KPI)
     */
    function switchTab(tabId) {
        // Hide all contents
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.remove('block');
            el.classList.add('hidden');
        });
        
        // Unstyle all tabs
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.classList.remove('bg-white', 'text-gray-900', 'shadow-sm', 'ring-1', 'ring-gray-900/5');
            el.classList.add('text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-100');
        });
        
        // Show actual tab
        document.getElementById(tabId).classList.remove('hidden');
        document.getElementById(tabId).classList.add('block');
        
        // Style active tab
        const activeNav = document.getElementById('nav_' + tabId);
        activeNav.classList.remove('text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-100');
        activeNav.classList.add('bg-white', 'text-gray-900', 'shadow-sm', 'ring-1', 'ring-gray-900/5');
    }

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

    /**
     * Auto-formats Employee ID into AAA-0000 format
     */
    document.getElementById('tsr_employee_id').addEventListener('input', function (e) {
        let value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); // Remove anything not letter or number
        
        let formatted = '';
        
        // Extract up to 3 letters
        let letters = value.match(/^[A-Z]{0,3}/)?.[0] || '';
        formatted += letters;

        // Remove the parsed letters to process the remaining numbers
        value = value.substring(letters.length);

        // Extract numbers and append with dash
        if (letters.length === 3 || value.length > 0) {
            let numbers = value.match(/^[0-9]{0,4}/)?.[0] || '';
            if (letters.length === 3) {
                formatted += '-';
            }
            formatted += numbers;
        }

        this.value = formatted;
    });
</script>
<?= $this->endSection() ?>