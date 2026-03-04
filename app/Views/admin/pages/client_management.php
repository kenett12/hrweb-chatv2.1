<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<!-- SAP Fiori Page Header -->
<div class="fiori-page-header">
    <div>
        <h1 class="fiori-page-title">Client Registry</h1>
        <p class="fiori-page-subtitle">Manage corporate partners and their HR portal access credentials</p>
    </div>
    <button onclick="toggleModal('addClientModal')" class="btn btn-accent">
        <span class="material-symbols-outlined text-[16px]">add_business</span>
        Register Client
    </button>
</div>

<!-- Client List Report -->
<div class="fiori-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="fiori-table">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>HR Representative</th>
                    <th class="text-center">Accounts</th>
                    <th class="text-center">Inquiries</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($client_list)): ?>
                    <?php foreach ($client_list as $client): ?>
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded flex items-center justify-center text-white text-xs font-semibold flex-none" style="background:var(--fiori-blue); border-radius:4px;">
                                    <?= strtoupper(substr($client['company_name'], 0, 1)) ?>
                                </div>
                                <span class="font-medium" style="color:var(--fiori-text-base);"><?= esc($client['company_name']) ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                <?php
                                    $contacts = json_decode($client['hr_contact'], true);
                                    $hasPrinted = false;
                                    if (is_array($contacts)) {
                                        if (isset($contacts['lead']) || isset($contacts['co1']) || isset($contacts['co2'])) {
                                            if (!empty($contacts['lead'])) {
                                                echo '<span class="fiori-status fiori-status--information" title="Lead Support">' . esc($contacts['lead']) . '</span> ';
                                                $hasPrinted = true;
                                            }
                                            if (!empty($contacts['co1'])) {
                                                echo '<span class="fiori-status fiori-status--neutral" title="Co-TSR 1">' . esc($contacts['co1']) . '</span> ';
                                                $hasPrinted = true;
                                            }
                                            if (!empty($contacts['co2'])) {
                                                echo '<span class="fiori-status fiori-status--neutral" title="Co-TSR 2">' . esc($contacts['co2']) . '</span>';
                                                $hasPrinted = true;
                                            }
                                        } elseif (!empty($contacts)) {
                                            foreach ($contacts as $contact) {
                                                echo '<span class="fiori-status fiori-status--neutral">' . esc($contact) . '</span> ';
                                                $hasPrinted = true;
                                            }
                                        }
                                    } elseif (!empty($client['hr_contact']) && $client['hr_contact'] !== 'null') {
                                        echo '<span class="text-sm font-medium" style="color:var(--fiori-text-base);">' . esc($client['hr_contact']) . '</span>';
                                        $hasPrinted = true;
                                    }
                                    if (!$hasPrinted) {
                                        echo '<span class="text-xs italic" style="color:var(--fiori-text-muted);">Unassigned</span>';
                                    }
                                ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="fiori-status fiori-status--neutral font-semibold"><?= $client['account_count'] ?? 0 ?> acct</span>
                        </td>
                        <td class="text-center">
                            <a href="<?= base_url('superadmin/tickets?client_id=' . $client['client_id']) ?>" class="btn btn-outline" style="height:28px; padding:0 12px; font-size:0.75rem;">
                                View Logs
                            </a>
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="openManageAccountsModal(<?= $client['client_id'] ?>, '<?= esc(addslashes($client['company_name'])) ?>')"
                                    class="w-8 h-8 flex items-center justify-center rounded transition-colors" style="color:var(--fiori-text-muted);"
                                    onmouseover="this.style.background='var(--fiori-positive-light)'; this.style.color='var(--fiori-positive)';" onmouseout="this.style.background=''; this.style.color='var(--fiori-text-muted)';" title="Manage Accounts">
                                    <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
                                </button>
                                <button onclick='openEditClientModal(<?= $client['client_id'] ?>, "<?= esc(addslashes($client['company_name'])) ?>", <?= json_encode($client['hr_contact'] ? json_decode($client['hr_contact'], true) : []) ?>)'
                                    class="w-8 h-8 flex items-center justify-center rounded transition-colors" style="color:var(--fiori-text-muted);"
                                    onmouseover="this.style.background='var(--fiori-blue-light)'; this.style.color='var(--fiori-blue)';" onmouseout="this.style.background=''; this.style.color='var(--fiori-text-muted)';" title="Edit Client">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                                <button onclick="confirmAction(event, '<?= base_url('superadmin/client-management/delete/'.$client['client_id']) ?>', 'Delete Corporate Client?', 'This will permanently remove all sub-accounts.', 'Delete', 'var(--fiori-negative)')"
                                    class="w-8 h-8 flex items-center justify-center rounded transition-colors" style="color:var(--fiori-text-muted);"
                                    onmouseover="this.style.background='var(--fiori-negative-light)'; this.style.color='var(--fiori-negative)';" onmouseout="this.style.background=''; this.style.color='var(--fiori-text-muted)';" title="Delete">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center py-16">
                        <span class="material-symbols-outlined text-4xl block mb-3" style="color:var(--fiori-border);">domain_disabled</span>
                        <p class="text-sm font-medium" style="color:var(--fiori-text-secondary);">No corporate clients found</p>
                        <p class="text-xs mt-1" style="color:var(--fiori-text-muted);">Click "Register Client" to add your first partner.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<!-- Add Client Dialog -->
<div id="addClientModal" class="fiori-overlay hidden">
    <div class="fiori-dialog">
        <div class="fiori-dialog__header">
            <h3 class="fiori-dialog__title">Register Client</h3>
            <button onclick="toggleModal('addClientModal')" class="w-7 h-7 flex items-center justify-center rounded transition-colors" style="color:var(--fiori-text-muted);" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background=''">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <form action="<?= base_url('superadmin/client-management/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="fiori-dialog__body space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Company Name</label>
                    <input type="text" name="company_name" class="fiori-input" placeholder="Acme Corporation" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-blue);">Lead Support <span style="color:var(--fiori-negative);">*</span></label>
                    <select name="lead_tsr" class="fiori-input" required>
                        <option value="" disabled selected>Select Lead TSR</option>
                        <?php if (isset($tsr_list) && !empty($tsr_list)): ?>
                            <?php foreach ($tsr_list as $tsr): ?>
                            <option value="<?= esc($tsr['email']) ?>"><?= esc($tsr['email']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Co-TSR 1</label>
                        <select name="co_tsr_1" class="fiori-input">
                            <option value="">None</option>
                            <?php if (isset($tsr_list) && !empty($tsr_list)): ?>
                                <?php foreach ($tsr_list as $tsr): ?>
                                <option value="<?= esc($tsr['email']) ?>"><?= esc($tsr['email']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Co-TSR 2</label>
                        <select name="co_tsr_2" class="fiori-input">
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
            <div class="fiori-dialog__footer">
                <button type="button" onclick="toggleModal('addClientModal')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-accent">Register Client</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Client Dialog -->
<div id="editClientModal" class="fiori-overlay hidden">
    <div class="fiori-dialog">
        <div class="fiori-dialog__header">
            <h3 class="fiori-dialog__title">Edit Client</h3>
            <button onclick="toggleModal('editClientModal')" class="w-7 h-7 flex items-center justify-center rounded transition-colors" style="color:var(--fiori-text-muted);" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background=''">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <form id="editClientForm" method="POST">
            <?= csrf_field() ?>
            <div class="fiori-dialog__body space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Company Name</label>
                    <input type="text" name="company_name" id="edit_company_name" class="fiori-input" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-blue);">Lead Support <span style="color:var(--fiori-negative);">*</span></label>
                    <select name="lead_tsr" id="edit_lead_tsr" class="fiori-input" required>
                        <option value="" disabled>Select Lead TSR</option>
                        <?php if (isset($tsr_list) && !empty($tsr_list)): ?>
                            <?php foreach ($tsr_list as $tsr): ?>
                            <option value="<?= esc($tsr['email']) ?>"><?= esc($tsr['email']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Co-TSR 1</label>
                        <select name="co_tsr_1" id="edit_co_tsr_1" class="fiori-input">
                            <option value="">None</option>
                            <?php if (isset($tsr_list) && !empty($tsr_list)): ?>
                                <?php foreach ($tsr_list as $tsr): ?>
                                <option value="<?= esc($tsr['email']) ?>"><?= esc($tsr['email']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Co-TSR 2</label>
                        <select name="co_tsr_2" id="edit_co_tsr_2" class="fiori-input">
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
            <div class="fiori-dialog__footer">
                <button type="button" onclick="toggleModal('editClientModal')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-accent">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Manage Accounts Dialog -->
<div id="manageAccountsModal" class="fiori-overlay hidden">
    <div class="fiori-dialog fiori-dialog--lg" style="max-height:85vh; display:flex; flex-direction:column;">
        <div class="fiori-dialog__header flex-shrink-0">
            <h3 class="fiori-dialog__title" id="manage_accounts_company_title">Company Accounts</h3>
            <div class="flex items-center gap-2">
                <button onclick="openAddAccountModal()" class="btn btn-accent">
                    <span class="material-symbols-outlined text-[16px]">person_add</span>
                    Add Account
                </button>
                <button onclick="toggleModal('manageAccountsModal')" class="w-7 h-7 flex items-center justify-center rounded transition-colors" style="color:var(--fiori-text-muted);" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background=''">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        </div>
        <div class="overflow-y-auto flex-1">
            <table class="fiori-table">
                <thead>
                    <tr>
                        <th>Email / Login ID</th>
                        <th>Role</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="accountsTableBody">
                    <tr><td colspan="3" class="py-10 text-center text-sm" style="color:var(--fiori-text-muted);">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Sub-Account Dialog -->
<div id="accountModal" class="fiori-overlay hidden" style="z-index:2010;">
    <div class="fiori-dialog">
        <div class="fiori-dialog__header">
            <h3 class="fiori-dialog__title" id="account_modal_title">Add Sub-Account</h3>
            <button onclick="toggleModal('accountModal')" class="w-7 h-7 flex items-center justify-center rounded transition-colors" style="color:var(--fiori-text-muted);" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background=''">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <form id="accountForm" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="client_id" id="account_client_id" value="">
            <div class="fiori-dialog__body space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Full Name</label>
                        <input type="text" name="full_name" id="account_full_name" class="fiori-input" placeholder="e.g. John Doe" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Account Email</label>
                        <input type="email" name="email" id="account_email" class="fiori-input" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Access Role</label>
                        <select name="client_role" id="account_role" class="fiori-input" required>
                            <option value="">Select Role…</option>
                            <option value="HR">HR</option>
                            <option value="TK 1">TK 1</option>
                            <option value="TK 2">TK 2</option>
                            <option value="PAYROLL 1">Payroll 1</option>
                            <option value="PAYROLL 2">Payroll 2</option>
                            <option value="EXECOM">EXECOM</option>
                            <option value="IT">IT</option>
                            <option value="AUDITOR">Auditor</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:var(--fiori-text-secondary);">Password <span id="account_pwd_optional" class="font-normal" style="color:var(--fiori-text-muted);"></span></label>
                        <div class="relative">
                            <input type="password" name="password" id="account_password" class="fiori-input pr-9" required>
                            <button type="button" onclick="togglePassword('account_password','eyeIconAccount')" class="absolute right-2.5 top-1/2 -translate-y-1/2 focus:outline-none" style="color:var(--fiori-text-muted);">
                                <span class="material-symbols-outlined text-[18px]" id="eyeIconAccount">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="fiori-dialog__footer">
                <button type="button" onclick="toggleModal('accountModal')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-accent">Save Account</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function openEditClientModal(id, companyName, hrContactsObj) {
    document.getElementById('editClientForm').action = `<?= base_url('superadmin/client-management/update') ?>/${id}`;
    document.getElementById('edit_company_name').value = companyName;
    document.getElementById('edit_lead_tsr').value = '';
    document.getElementById('edit_co_tsr_1').value = '';
    document.getElementById('edit_co_tsr_2').value = '';
    if (hrContactsObj && typeof hrContactsObj === 'object') {
        if (hrContactsObj.lead) document.getElementById('edit_lead_tsr').value = hrContactsObj.lead;
        if (hrContactsObj.co1)  document.getElementById('edit_co_tsr_1').value = hrContactsObj.co1;
        if (hrContactsObj.co2)  document.getElementById('edit_co_tsr_2').value = hrContactsObj.co2;
    }
    toggleModal('editClientModal');
}

function openManageAccountsModal(clientId, companyName) {
    document.getElementById('manage_accounts_company_title').innerText = companyName + ' — Accounts';
    document.getElementById('account_client_id').value = clientId;
    const tbody = document.getElementById('accountsTableBody');
    tbody.innerHTML = `<tr><td colspan="3" class="py-10 text-center text-sm" style="color:var(--fiori-text-muted);">Loading accounts...</td></tr>`;
    toggleModal('manageAccountsModal');

    fetch(`<?= base_url('superadmin/client-management/accounts') ?>/${clientId}`)
        .then(r => r.json())
        .then(data => {
            tbody.innerHTML = '';
            if (data.length > 0) {
                data.forEach(account => {
                    tbody.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded flex items-center justify-center text-white text-xs font-semibold flex-none" style="background:var(--fiori-positive); border-radius:4px;">${account.email.charAt(0).toUpperCase()}</div>
                                    <div class="flex flex-col">
                                        <span class="font-bold" style="color:var(--fiori-text-base);">${account.full_name || 'N/A'}</span>
                                        <span class="text-xs" style="color:var(--fiori-text-muted);">${account.email}</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="fiori-status fiori-status--neutral">${account.client_role || 'No Role'}</span></td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <button onclick="openEditAccountModal(${account.id}, '${account.email}', '${account.full_name || ''}', '${account.client_role || ''}')" class="w-8 h-8 flex items-center justify-center rounded transition-colors" style="color:var(--fiori-text-muted);" onmouseover="this.style.background='var(--fiori-blue-light)'; this.style.color='var(--fiori-blue)'" onmouseout="this.style.background=''; this.style.color='var(--fiori-text-muted)'" title="Edit">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <button onclick="confirmAction(event, '<?= base_url('superadmin/client-management/delete-account') ?>/${account.id}', 'Delete Sub-Account?', 'This will revoke their access immediately.', 'Delete', 'var(--fiori-negative)')" class="w-8 h-8 flex items-center justify-center rounded transition-colors" style="color:var(--fiori-text-muted);" onmouseover="this.style.background='var(--fiori-negative-light)'; this.style.color='var(--fiori-negative)'" onmouseout="this.style.background=''; this.style.color='var(--fiori-text-muted)'" title="Delete">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>`);
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="3" class="py-12 text-center text-sm" style="color:var(--fiori-text-muted);">No sub-accounts found. Click "Add Account" to create one.</td></tr>`;
            }
        })
        .catch(() => { tbody.innerHTML = `<tr><td colspan="3" class="py-8 text-center text-sm" style="color:var(--fiori-negative);">Failed to load accounts.</td></tr>`; });
}

function openAddAccountModal() {
    document.getElementById('accountForm').action = `<?= base_url('superadmin/client-management/store-account') ?>`;
    document.getElementById('account_modal_title').innerText = 'Add Sub-Account';
    document.getElementById('account_email').value = '';
    document.getElementById('account_full_name').value = '';
    document.getElementById('account_role').value = '';
    const pwd = document.getElementById('account_password');
    pwd.required = true; pwd.placeholder = ''; pwd.value = '';
    document.getElementById('account_pwd_optional').innerText = '';
    toggleModal('accountModal');
}

function openEditAccountModal(accountId, email, fullName, role) {
    document.getElementById('accountForm').action = `<?= base_url('superadmin/client-management/update-account') ?>/${accountId}`;
    document.getElementById('account_modal_title').innerText = 'Edit Sub-Account';
    document.getElementById('account_email').value = email;
    document.getElementById('account_full_name').value = fullName;
    document.getElementById('account_role').value = role;
    const pwd = document.getElementById('account_password');
    pwd.required = false; pwd.placeholder = 'Leave blank to keep unchanged'; pwd.value = '';
    document.getElementById('account_pwd_optional').innerText = '(optional)';
    toggleModal('accountModal');
}

function togglePassword(inputId, iconId) {
    const i = document.getElementById(inputId), e = document.getElementById(iconId);
    i.type = i.type === 'password' ? 'text' : 'password';
    e.textContent = i.type === 'password' ? 'visibility' : 'visibility_off';
}
</script>
<?= $this->endSection() ?>