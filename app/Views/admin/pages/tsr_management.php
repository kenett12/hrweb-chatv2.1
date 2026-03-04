<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<!-- SAP Fiori Page Header -->
<div class="fiori-page-header">
    <div>
        <h1 class="fiori-page-title">Staff Management</h1>
        <p class="fiori-page-subtitle">Manage internal employees, Staff Accounts, and KPI performance</p>
    </div>
    <button onclick="toggleModal('addTsrModal')" class="btn btn-accent">
        <span class="material-symbols-outlined text-[16px]">person_add</span>
        Add Staff
    </button>
</div>

<!-- SAP Fiori IconTabBar -->
<div class="fiori-tab-bar">
    <button onclick="switchTab('tsr_accounts_tab')" id="nav_tsr_accounts_tab" class="fiori-tab-item is-active">
        <span class="material-symbols-outlined text-[16px]">manage_accounts</span>
        Staff Accounts
    </button>
    <button onclick="switchTab('kpi_overview_tab')" id="nav_kpi_overview_tab" class="fiori-tab-item">
        <span class="material-symbols-outlined text-[16px]">monitoring</span>
        KPI Overview
    </button>
</div>

<!-- TAB 1: TSR ACCOUNTS -->
<div id="tsr_accounts_tab" class="tab-content">
    <div class="fiori-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="fiori-table">
                <thead>
                    <tr>
                        <th>Emp ID</th>
                        <th>Full Name</th>
                        <th>Email Address</th>
                        <th>Account Status</th>
                        <th>Availability</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tsr_list)): ?>
                        <?php foreach ($tsr_list as $tsr): ?>
                        <tr>
                            <td>
                                <span class="fiori-status fiori-status--information font-mono">#<?= esc($tsr['employee_id']) ?></span>
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded flex items-center justify-center text-white text-xs font-semibold flex-none" style="background:var(--fiori-blue); border-radius:4px;">
                                        <?= strtoupper(substr($tsr['full_name'], 0, 1)) ?>
                                    </div>
                                    <span class="font-medium" style="color:var(--fiori-text-base);"><?= esc($tsr['full_name']) ?></span>
                                </div>
                            </td>
                            <td style="color:var(--fiori-text-secondary);"><?= esc($tsr['email']) ?></td>
                            <td>
                                <?php if ($tsr['status'] === 'active'): ?>
                                    <span class="fiori-status fiori-status--positive" title="Account is enabled">Enabled</span>
                                <?php else: ?>
                                    <span class="fiori-status fiori-status--negative" title="Account is disabled">Disabled</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($tsr['availability_status'] === 'active'): ?>
                                    <span class="fiori-status fiori-status--positive font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full" style="background:var(--fiori-positive);"></span>Online
                                    </span>
                                <?php elseif ($tsr['availability_status'] === 'busy'): ?>
                                    <span class="fiori-status fiori-status--warning font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full" style="background:var(--fiori-warning);"></span>Busy
                                    </span>
                                <?php else: ?>
                                    <span class="fiori-status fiori-status--neutral">
                                        <span class="w-1.5 h-1.5 rounded-full" style="background:var(--fiori-text-muted);"></span>Offline
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <button onclick="openEditStaffModal('<?= $tsr['id'] ?>', '<?= esc(addslashes($tsr['full_name'])) ?>', '<?= esc($tsr['employee_id']) ?>', '<?= esc(explode('@', $tsr['email'])[0]) ?>', '<?= esc($tsr['role']) ?>', '<?= esc($tsr['status']) ?>')"
                                        class="w-8 h-8 flex items-center justify-center rounded transition-colors" style="color:var(--fiori-text-muted);" onmouseover="this.style.background='var(--fiori-blue-light)'; this.style.color='var(--fiori-blue)';" onmouseout="this.style.background=''; this.style.color='var(--fiori-text-muted)';" title="Edit">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <button onclick="confirmAction(event, '<?= base_url('superadmin/tsr-management/delete/'.$tsr['id']) ?>', 'Delete TSR Account?', 'This will permanently remove their system access.', 'Delete', 'var(--fiori-negative)')"
                                        class="w-8 h-8 flex items-center justify-center rounded transition-colors" style="color:var(--fiori-text-muted);" onmouseover="this.style.background='var(--fiori-negative-light)'; this.style.color='var(--fiori-negative)';" onmouseout="this.style.background=''; this.style.color='var(--fiori-text-muted)';" title="Delete">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-16">
                            <span class="material-symbols-outlined text-4xl block mb-3" style="color:var(--fiori-border);">group_off</span>
                            <p class="text-sm font-medium" style="color:var(--fiori-text-secondary);">No staff accounts found</p>
                            <p class="text-xs mt-1" style="color:var(--fiori-text-muted);">Click "Add Staff" to register the first account.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB 2: KPI OVERVIEW -->
<div id="kpi_overview_tab" class="tab-content hidden space-y-4">

    <!-- Client Assignments -->
    <div class="fiori-card overflow-hidden">
        <div class="fiori-card__header">
            <div>
                <h3 class="fiori-card__title">Client Assignments</h3>
                <p class="fiori-card__subtitle">Current TSR-to-client mapping</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="fiori-table">
                <thead>
                    <tr>
                        <th>Company / Client</th>
                        <th>Lead Support</th>
                        <th>Co-TSR 1</th>
                        <th>Co-TSR 2</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($detailed_assignments)): ?>
                        <?php foreach ($detailed_assignments as $assign): ?>
                        <tr>
                            <td class="font-medium" style="color:var(--fiori-text-base);"><?= esc($assign['company']) ?></td>
                            <td>
                                <?php if ($assign['lead']): ?>
                                    <span class="fiori-status fiori-status--information"><?= esc($assign['lead']) ?></span>
                                <?php else: ?>
                                    <span class="text-xs italic" style="color:var(--fiori-text-muted);">Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($assign['co1']): ?>
                                    <span class="fiori-status fiori-status--neutral"><?= esc($assign['co1']) ?></span>
                                <?php else: ?>
                                    <span class="text-xs italic" style="color:var(--fiori-text-muted);">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($assign['co2']): ?>
                                    <span class="fiori-status fiori-status--neutral"><?= esc($assign['co2']) ?></span>
                                <?php else: ?>
                                    <span class="text-xs italic" style="color:var(--fiori-text-muted);">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr><td colspan="4" class="py-10 text-center text-sm" style="color:var(--fiori-text-muted);">No assignment data found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- KPI Summary -->
    <div class="fiori-card overflow-hidden">
        <div class="fiori-card__header">
            <div>
                <h3 class="fiori-card__title">KPI Summary</h3>
                <p class="fiori-card__subtitle">Utilization overview against target minimum</p>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 text-sm font-semibold rounded" style="background:var(--fiori-positive-light); color:var(--fiori-positive); border:1px solid var(--fiori-positive-border);">
                <span class="material-symbols-outlined text-[16px]">target</span>
                <?= esc($min_target) ?> Leads target
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="fiori-table">
                <thead>
                    <tr>
                        <th>TSR Account</th>
                        <th class="text-center">Lead Clients</th>
                        <th class="text-center">Co-Leads</th>
                        <th class="text-center">Utilization %</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($kpi_data)): ?>
                        <?php foreach ($kpi_data as $email => $data): ?>
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded flex items-center justify-center text-white text-xs font-semibold flex-none" style="background:var(--fiori-blue); border-radius:4px;">
                                        <?= strtoupper(substr($data['name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <p class="font-medium text-sm" style="color:var(--fiori-text-base);"><?= esc($data['name']) ?></p>
                                        <p class="text-xs" style="color:var(--fiori-text-muted);"><?= esc($email) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center font-semibold"><?= $data['leads'] ?></td>
                            <td class="text-center" style="color:var(--fiori-text-secondary);"><?= $data['coleads'] ?></td>
                            <td class="text-center">
                                <?php
                                    $util = $data['utilization'];
                                    if ($util >= 100) { $statusClass = 'fiori-status--positive'; }
                                    elseif ($util >= 50) { $statusClass = 'fiori-status--warning'; }
                                    else { $statusClass = 'fiori-status--negative'; }
                                ?>
                                <span class="fiori-status <?= $statusClass ?> font-semibold"><?= $util ?>%</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr><td colspan="4" class="py-10 text-center text-sm" style="color:var(--fiori-text-muted);">No KPI data available</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<!-- Add TSR Dialog -->
<div id="addTsrModal" class="fiori-overlay hidden">
    <div class="fiori-dialog">
        <div class="fiori-dialog__header">
            <h3 class="fiori-dialog__title">Add TSR Account</h3>
            <button onclick="toggleModal('addTsrModal')" class="w-7 h-7 flex items-center justify-center rounded transition-colors" style="color:var(--fiori-text-muted);" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background=''">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <form action="<?= base_url('superadmin/tsr-management/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="fiori-dialog__body space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Full Name</label>
                    <input type="text" name="full_name" class="fiori-input" placeholder="John Doe" minlength="3" maxlength="100" pattern="[A-Za-z\s]+" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Employee ID</label>
                    <input type="text" name="employee_id" id="tsr_employee_id" class="fiori-input font-mono uppercase tracking-widest" placeholder="AAA-0000" maxlength="8" pattern="[A-Za-z]{3}-\d{4}" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Internal Roles</label>
                    <select name="role" class="fiori-input w-full bg-white" required>
                        <option value="tsr_level_1">TSR Level 1</option>
                        <option value="tl">Team Leader (TL)</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="manager">Manager</option>
                        <option value="dev">Developer (Dev)</option>
                        <option value="tsr_level_2">TSR Level 2 / Technical</option>
                        <option value="it">IT / Technical</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Email Address</label>
                    <div class="flex">
                        <input type="text" name="email_prefix" class="fiori-input" style="border-radius:4px 0 0 4px; border-right:none;" placeholder="username" pattern="[a-zA-Z0-9_\.]+" required>
                        <span class="px-3 flex items-center text-sm font-semibold border" style="background:#f0f0f0; border-color:var(--fiori-border); border-radius:0 4px 4px 0; color:var(--fiori-blue); white-space:nowrap;">@hrweb.ph</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Temporary Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="tsr_password" class="fiori-input pr-10" minlength="8" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+" required>
                        <button type="button" onclick="togglePassword('tsr_password','eyeIconTsr')" class="absolute right-3 top-1/2 -translate-y-1/2 focus:outline-none" style="color:var(--fiori-text-muted);">
                            <span class="material-symbols-outlined text-[18px]" id="eyeIconTsr">visibility</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="fiori-dialog__footer">
                <button type="button" onclick="toggleModal('addTsrModal')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-accent">Create Account</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Staff Dialog -->
<div id="editTsrModal" class="fiori-overlay hidden">
    <div class="fiori-dialog">
        <div class="fiori-dialog__header">
            <h3 class="fiori-dialog__title">Edit Staff Account</h3>
            <button onclick="toggleModal('editTsrModal')" class="w-7 h-7 flex items-center justify-center rounded transition-colors" style="color:var(--fiori-text-muted);" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background=''">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <form id="editTsrForm" method="POST">
            <?= csrf_field() ?>
            <div class="fiori-dialog__body space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Full Name</label>
                    <input type="text" name="full_name" id="edit_full_name" class="fiori-input" placeholder="John Doe" minlength="3" maxlength="100" pattern="[A-Za-z\s]+" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Employee ID</label>
                    <input type="text" name="employee_id" id="edit_employee_id" class="fiori-input font-mono uppercase tracking-widest" placeholder="AAA-0000" maxlength="8" pattern="[A-Za-z]{3}-\d{4}" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Internal Role</label>
                    <select name="role" id="edit_role" class="fiori-input w-full bg-white" required>
                        <option value="tsr_level_1">TSR Level 1</option>
                        <option value="tl">Team Leader (TL)</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="manager">Manager</option>
                        <option value="dev">Developer (Dev)</option>
                        <option value="tsr_level_2">TSR Level 2 / Technical</option>
                        <option value="it">IT / Technical</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Email Address</label>
                    <div class="flex">
                        <input type="text" name="email_prefix" id="edit_email_prefix" class="fiori-input" style="border-radius:4px 0 0 4px; border-right:none;" placeholder="username" pattern="[a-zA-Z0-9_\.]+" required>
                        <span class="px-3 flex items-center text-sm font-semibold border" style="background:#f0f0f0; border-color:var(--fiori-border); border-radius:0 4px 4px 0; color:var(--fiori-blue); white-space:nowrap;">@hrweb.ph</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">New Password (Leave blank to keep current)</label>
                    <div class="relative">
                        <input type="password" name="password" id="edit_tsr_password" class="fiori-input pr-10" minlength="8" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+" placeholder="••••••••">
                        <button type="button" onclick="togglePassword('edit_tsr_password','edit_eyeIconTsr')" class="absolute right-3 top-1/2 -translate-y-1/2 focus:outline-none" style="color:var(--fiori-text-muted);">
                            <span class="material-symbols-outlined text-[18px]" id="edit_eyeIconTsr">visibility</span>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Account Status</label>
                    <select name="status" id="edit_status" class="fiori-input w-full bg-white" required>
                        <option value="active">Enabled</option>
                        <option value="inactive">Disabled</option>
                    </select>
                </div>
            </div>
            <div class="fiori-dialog__footer">
                <button type="button" onclick="toggleModal('editTsrModal')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-accent">Save Changes</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(el => { el.classList.add('hidden'); el.classList.remove('block'); });
    document.querySelectorAll('.fiori-tab-item').forEach(el => el.classList.remove('is-active'));
    document.getElementById(tabId).classList.remove('hidden');
    document.getElementById(tabId).classList.add('block');
    document.getElementById('nav_' + tabId).classList.add('is-active');
}
function togglePassword(inputId, iconId) {
    const i = document.getElementById(inputId), e = document.getElementById(iconId);
    i.type = i.type === 'password' ? 'text' : 'password';
    e.textContent = i.type === 'password' ? 'visibility' : 'visibility_off';
}
document.getElementById('tsr_employee_id').addEventListener('input', formatEmpId);
document.getElementById('edit_employee_id').addEventListener('input', formatEmpId);

function formatEmpId(e) {
    let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    let letters = value.match(/^[A-Z]{0,3}/)?.[0] || '';
    let rest = value.substring(letters.length);
    let formatted = letters;
    if (letters.length === 3 || rest.length > 0) { if (letters.length === 3) formatted += '-'; formatted += (rest.match(/^[0-9]{0,4}/)?.[0] || ''); }
    e.target.value = formatted;
}

function openEditStaffModal(id, fullName, employeeId, emailPrefix, role, status) {
    document.getElementById('editTsrForm').action = '<?= base_url('superadmin/tsr-management/update/') ?>' + id;
    document.getElementById('edit_full_name').value = fullName;
    document.getElementById('edit_employee_id').value = employeeId;
    document.getElementById('edit_email_prefix').value = emailPrefix;
    document.getElementById('edit_role').value = role;
    document.getElementById('edit_status').value = status;
    document.getElementById('edit_tsr_password').value = '';
    toggleModal('editTsrModal');
}
</script>
<?= $this->endSection() ?>