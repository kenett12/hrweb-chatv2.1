<style>
/* ── SAP Fiori Side Navigation ── */
#sidebar {
    background: var(--fiori-sidenav-bg);
    width: 15rem;
    transition: width 200ms ease;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border-right: 1px solid rgba(0,0,0,0.15);
    height: 100%;
}
html.sidebar-is-collapsed #sidebar { width: 3rem; }
html.sidebar-is-collapsed #sidebar .nav-label { display: none; }
html.sidebar-is-collapsed #sidebar .nav-section-title { display: none; }
html.sidebar-is-collapsed #sidebar .nav-item { justify-content: center; padding-left: 0; padding-right: 0; }
html.sidebar-is-collapsed #sidebar .nav-item-icon { margin: 0; }
</style>

<aside id="sidebar">
    <nav class="flex-1 py-2 overflow-y-auto overflow-x-hidden scrollbar-hide">

        <!-- General Section -->
        <div class="nav-section-title px-3 pt-3 pb-1">
            <p class="text-[10px] font-semibold uppercase tracking-[0.12em]" style="color:rgba(209,219,230,0.4);">General</p>
        </div>

        <a href="<?= base_url($portalPrefix . '/dashboard') ?>"
           class="nav-item flex items-center gap-3 px-3 py-2.5 mx-1 rounded transition-colors <?= url_is($portalPrefix.'/dashboard') ? 'is-active' : '' ?>"
           style="<?= url_is($portalPrefix.'/dashboard') ? 'background:rgba(0,112,242,0.18); color:#ffffff; border-left:3px solid #0070f2; padding-left:9px;' : 'color:var(--fiori-sidenav-text);' ?>"
           onmouseover="this.style.background='var(--fiori-sidenav-hover)'"
           onmouseout="if(!this.classList.contains('is-active'))this.style.background=''; else this.style.background='rgba(0,112,242,0.18)'">
            <span class="material-symbols-outlined nav-item-icon text-[20px] flex-none" style="<?= url_is($portalPrefix.'/dashboard') ? 'color:#0070f2' : 'color:var(--fiori-sidenav-text)' ?>">grid_view</span>
            <span class="nav-label text-sm font-medium whitespace-nowrap truncate">Dashboard</span>
        </a>

        <?php if ($portalPrefix === 'superadmin'): ?>
        <!-- Management Section -->
        <div class="nav-section-title px-3 pt-4 pb-1">
            <p class="text-[10px] font-semibold uppercase tracking-[0.12em]" style="color:rgba(209,219,230,0.4);">Management</p>
        </div>

        <a href="<?= base_url('superadmin/tsr-management') ?>"
           class="nav-item flex items-center gap-3 px-3 py-2.5 mx-1 rounded transition-colors <?= url_is('superadmin/tsr-management*') ? 'is-active' : '' ?>"
           style="<?= url_is('superadmin/tsr-management*') ? 'background:rgba(0,112,242,0.18); color:#ffffff; border-left:3px solid #0070f2; padding-left:9px;' : 'color:var(--fiori-sidenav-text);' ?>"
           onmouseover="this.style.background='var(--fiori-sidenav-hover)'"
           onmouseout="if(!this.classList.contains('is-active'))this.style.background=''; else this.style.background='rgba(0,112,242,0.18)'">
            <span class="material-symbols-outlined nav-item-icon text-[20px] flex-none" style="<?= url_is('superadmin/tsr-management*') ? 'color:#0070f2' : 'color:var(--fiori-sidenav-text)' ?>">admin_panel_settings</span>
            <span class="nav-label text-sm font-medium whitespace-nowrap truncate">TSR Accounts</span>
        </a>

        <a href="<?= base_url('superadmin/client-management') ?>"
           class="nav-item flex items-center gap-3 px-3 py-2.5 mx-1 rounded transition-colors <?= url_is('superadmin/client-management*') ? 'is-active' : '' ?>"
           style="<?= url_is('superadmin/client-management*') ? 'background:rgba(0,112,242,0.18); color:#ffffff; border-left:3px solid #0070f2; padding-left:9px;' : 'color:var(--fiori-sidenav-text);' ?>"
           onmouseover="this.style.background='var(--fiori-sidenav-hover)'"
           onmouseout="if(!this.classList.contains('is-active'))this.style.background=''; else this.style.background='rgba(0,112,242,0.18)'">
            <span class="material-symbols-outlined nav-item-icon text-[20px] flex-none" style="<?= url_is('superadmin/client-management*') ? 'color:#0070f2' : 'color:var(--fiori-sidenav-text)' ?>">domain</span>
            <span class="nav-label text-sm font-medium whitespace-nowrap truncate">Client Accounts</span>
        </a>

        <a href="<?= base_url('superadmin/kb') ?>"
           class="nav-item flex items-center gap-3 px-3 py-2.5 mx-1 rounded transition-colors <?= url_is('superadmin/kb*') ? 'is-active' : '' ?>"
           style="<?= url_is('superadmin/kb*') ? 'background:rgba(0,112,242,0.18); color:#ffffff; border-left:3px solid #0070f2; padding-left:9px;' : 'color:var(--fiori-sidenav-text);' ?>"
           onmouseover="this.style.background='var(--fiori-sidenav-hover)'"
           onmouseout="if(!this.classList.contains('is-active'))this.style.background=''; else this.style.background='rgba(0,112,242,0.18)'">
            <span class="material-symbols-outlined nav-item-icon text-[20px] flex-none" style="<?= url_is('superadmin/kb*') ? 'color:#0070f2' : 'color:var(--fiori-sidenav-text)' ?>">auto_stories</span>
            <span class="nav-label text-sm font-medium whitespace-nowrap truncate">Bot Knowledge</span>
        </a>

        <!-- Oversight Section -->
        <div class="nav-section-title px-3 pt-4 pb-1">
            <p class="text-[10px] font-semibold uppercase tracking-[0.12em]" style="color:rgba(209,219,230,0.4);">Oversight</p>
        </div>

        <a href="<?= base_url('superadmin/tickets') ?>"
           class="nav-item flex items-center gap-3 px-3 py-2.5 mx-1 rounded transition-colors <?= url_is('superadmin/tickets*') ? 'is-active' : '' ?>"
           style="<?= url_is('superadmin/tickets*') ? 'background:rgba(0,112,242,0.18); color:#ffffff; border-left:3px solid #0070f2; padding-left:9px;' : 'color:var(--fiori-sidenav-text);' ?>"
           onmouseover="this.style.background='var(--fiori-sidenav-hover)'"
           onmouseout="if(!this.classList.contains('is-active'))this.style.background=''; else this.style.background='rgba(0,112,242,0.18)'">
            <span class="material-symbols-outlined nav-item-icon text-[20px] flex-none" style="<?= url_is('superadmin/tickets*') ? 'color:#0070f2' : 'color:var(--fiori-sidenav-text)' ?>">confirmation_number</span>
            <span class="nav-label text-sm font-medium whitespace-nowrap truncate">All Tickets</span>
        </a>

        <a href="<?= base_url('superadmin/system-management') ?>"
           class="nav-item flex items-center gap-3 px-3 py-2.5 mx-1 rounded transition-colors <?= url_is('superadmin/system-management*') ? 'is-active' : '' ?>"
           style="<?= url_is('superadmin/system-management*') ? 'background:rgba(0,112,242,0.18); color:#ffffff; border-left:3px solid #0070f2; padding-left:9px;' : 'color:var(--fiori-sidenav-text);' ?>"
           onmouseover="this.style.background='var(--fiori-sidenav-hover)'"
           onmouseout="if(!this.classList.contains('is-active'))this.style.background=''; else this.style.background='rgba(0,112,242,0.18)'">
            <span class="material-symbols-outlined nav-item-icon text-[20px] flex-none" style="<?= url_is('superadmin/system-management*') ? 'color:#0070f2' : 'color:var(--fiori-sidenav-text)' ?>">settings_alert</span>
            <span class="nav-label text-sm font-medium whitespace-nowrap truncate">System Settings</span>
        </a>

        <div class="nav-section-title px-3 pt-4 pb-1">
            <p class="text-[10px] font-semibold uppercase tracking-[0.12em]" style="color:rgba(209,219,230,0.4);">Collaboration</p>
        </div>

        <a href="<?= base_url('group-chat') ?>"
           class="nav-item flex items-center gap-3 px-3 py-2.5 mx-1 rounded transition-colors <?= url_is('group-chat*') ? 'is-active' : '' ?>"
           style="<?= url_is('group-chat*') ? 'background:rgba(0,112,242,0.18); color:#ffffff; border-left:3px solid #0070f2; padding-left:9px;' : 'color:var(--fiori-sidenav-text);' ?>"
           onmouseover="this.style.background='var(--fiori-sidenav-hover)'"
           onmouseout="if(!this.classList.contains('is-active'))this.style.background=''; else this.style.background='rgba(0,112,242,0.18)'">
            <span class="material-symbols-outlined nav-item-icon text-[20px] flex-none" style="<?= url_is('group-chat*') ? 'color:#0070f2' : 'color:var(--fiori-sidenav-text)' ?>">forum</span>
            <span class="nav-label text-sm font-medium whitespace-nowrap truncate">Group Chats</span>
        </a>
        <?php endif; ?>

        <?php if ($portalPrefix === 'tsr'): ?>
        <div class="nav-section-title px-3 pt-4 pb-1">
            <p class="text-[10px] font-semibold uppercase tracking-[0.12em]" style="color:rgba(209,219,230,0.4);">Help Desk</p>
        </div>
        <a href="<?= base_url('tsr/tickets') ?>"
           class="nav-item flex items-center gap-3 px-3 py-2.5 mx-1 rounded transition-colors <?= url_is('tsr/tickets*') ? 'is-active' : '' ?>"
           style="<?= url_is('tsr/tickets*') ? 'background:rgba(0,112,242,0.18); color:#ffffff; border-left:3px solid #0070f2; padding-left:9px;' : 'color:var(--fiori-sidenav-text);' ?>"
           onmouseover="this.style.background='var(--fiori-sidenav-hover)'"
           onmouseout="if(!this.classList.contains('is-active'))this.style.background=''; else this.style.background='rgba(0,112,242,0.18)'">
            <span class="material-symbols-outlined nav-item-icon text-[20px] flex-none" style="<?= url_is('tsr/tickets*') ? 'color:#0070f2' : 'color:var(--fiori-sidenav-text)' ?>">confirmation_number</span>
            <span class="nav-label text-sm font-medium whitespace-nowrap truncate">Manage Tickets</span>
        </a>
        <a href="<?= base_url('tsr/chat') ?>"
           class="nav-item flex items-center gap-3 px-3 py-2.5 mx-1 rounded transition-colors <?= url_is('tsr/chat*') ? 'is-active' : '' ?>"
           style="<?= url_is('tsr/chat*') ? 'background:rgba(0,112,242,0.18); color:#ffffff; border-left:3px solid #0070f2; padding-left:9px;' : 'color:var(--fiori-sidenav-text);' ?>"
           onmouseover="this.style.background='var(--fiori-sidenav-hover)'"
           onmouseout="if(!this.classList.contains('is-active'))this.style.background=''; else this.style.background='rgba(0,112,242,0.18)'">
            <span class="material-symbols-outlined nav-item-icon text-[20px] flex-none" style="<?= url_is('tsr/chat*') ? 'color:#0070f2' : 'color:var(--fiori-sidenav-text)' ?>">chat</span>
            <span class="nav-label text-sm font-medium whitespace-nowrap truncate">Chats</span>
        </a>
        <a href="<?= base_url('group-chat') ?>"
           class="nav-item flex items-center gap-3 px-3 py-2.5 mx-1 rounded transition-colors <?= url_is('group-chat*') ? 'is-active' : '' ?>"
           style="<?= url_is('group-chat*') ? 'background:rgba(0,112,242,0.18); color:#ffffff; border-left:3px solid #0070f2; padding-left:9px;' : 'color:var(--fiori-sidenav-text);' ?>"
           onmouseover="this.style.background='var(--fiori-sidenav-hover)'"
           onmouseout="if(!this.classList.contains('is-active'))this.style.background=''; else this.style.background='rgba(0,112,242,0.18)'">
            <span class="material-symbols-outlined nav-item-icon text-[20px] flex-none" style="<?= url_is('group-chat*') ? 'color:#0070f2' : 'color:var(--fiori-sidenav-text)' ?>">forum</span>
            <span class="nav-label text-sm font-medium whitespace-nowrap truncate">Group Chats</span>
        </a>
        <?php endif; ?>

        <?php if ($portalPrefix === 'client'): ?>
        <div class="nav-section-title px-3 pt-4 pb-1">
            <p class="text-[10px] font-semibold uppercase tracking-[0.12em]" style="color:rgba(209,219,230,0.4);">Support</p>
        </div>
        <a href="<?= base_url('client/tickets') ?>"
           class="nav-item flex items-center gap-3 px-3 py-2.5 mx-1 rounded transition-colors <?= url_is('client/tickets*') ? 'is-active' : '' ?>"
           style="<?= url_is('client/tickets*') ? 'background:rgba(0,112,242,0.18); color:#ffffff; border-left:3px solid #0070f2; padding-left:9px;' : 'color:var(--fiori-sidenav-text);' ?>"
           onmouseover="this.style.background='var(--fiori-sidenav-hover)'"
           onmouseout="if(!this.classList.contains('is-active'))this.style.background=''; else this.style.background='rgba(0,112,242,0.18)'">
            <span class="material-symbols-outlined nav-item-icon text-[20px] flex-none">confirmation_number</span>
            <span class="nav-label text-sm font-medium whitespace-nowrap truncate">Support Tickets</span>
        </a>
        <a href="<?= base_url('client/chat') ?>"
           class="nav-item flex items-center gap-3 px-3 py-2.5 mx-1 rounded transition-colors <?= url_is('client/chat*') ? 'is-active' : '' ?>"
           style="<?= url_is('client/chat*') ? 'background:rgba(0,112,242,0.18); color:#ffffff; border-left:3px solid #0070f2; padding-left:9px;' : 'color:var(--fiori-sidenav-text);' ?>"
           onmouseover="this.style.background='var(--fiori-sidenav-hover)'"
           onmouseout="if(!this.classList.contains('is-active'))this.style.background=''; else this.style.background='rgba(0,112,242,0.18)'">
            <span class="material-symbols-outlined nav-item-icon text-[20px] flex-none">chat</span>
            <span class="nav-label text-sm font-medium whitespace-nowrap truncate">Chat</span>
        </a>
        <a href="<?= base_url('group-chat') ?>"
           class="nav-item flex items-center gap-3 px-3 py-2.5 mx-1 rounded transition-colors <?= url_is('group-chat*') ? 'is-active' : '' ?>"
           style="<?= url_is('group-chat*') ? 'background:rgba(0,112,242,0.18); color:#ffffff; border-left:3px solid #0070f2; padding-left:9px;' : 'color:var(--fiori-sidenav-text);' ?>"
           onmouseover="this.style.background='var(--fiori-sidenav-hover)'"
           onmouseout="if(!this.classList.contains('is-active'))this.style.background=''; else this.style.background='rgba(0,112,242,0.18)'">
            <span class="material-symbols-outlined nav-item-icon text-[20px] flex-none" style="<?= url_is('group-chat*') ? 'color:#0070f2' : 'color:var(--fiori-sidenav-text)' ?>">forum</span>
            <span class="nav-label text-sm font-medium whitespace-nowrap truncate">Group Chats</span>
        </a>

        <div class="nav-section-title px-3 pt-4 pb-1">
            <p class="text-[10px] font-semibold uppercase tracking-[0.12em]" style="color:rgba(209,219,230,0.4);">Account</p>
        </div>
        <a href="<?= base_url('client/settings') ?>"
           class="nav-item flex items-center gap-3 px-3 py-2.5 mx-1 rounded transition-colors <?= url_is('client/settings*') ? 'is-active' : '' ?>"
           style="<?= url_is('client/settings*') ? 'background:rgba(0,112,242,0.18); color:#ffffff; border-left:3px solid #0070f2; padding-left:9px;' : 'color:var(--fiori-sidenav-text);' ?>"
           onmouseover="this.style.background='var(--fiori-sidenav-hover)'"
           onmouseout="if(!this.classList.contains('is-active'))this.style.background=''; else this.style.background='rgba(0,112,242,0.18)'">
            <span class="material-symbols-outlined nav-item-icon text-[20px] flex-none">settings</span>
            <span class="nav-label text-sm font-medium whitespace-nowrap truncate">Settings</span>
        </a>
        <?php endif; ?>

    </nav>

    <!-- Sign Out -->
    <div class="py-2 px-1 border-t" style="border-color:rgba(255,255,255,0.08);">
        <a href="<?= base_url('logout') ?>"
           onclick="confirmAction(event, this.href, 'Sign out?', 'You will need to log in again.', 'Sign Out', '#bb0000')"
           class="flex items-center gap-3 px-3 py-2.5 rounded transition-colors"
           style="color:#f87171;"
           onmouseover="this.style.background='rgba(187,0,0,0.1)'"
           onmouseout="this.style.background=''">
            <span class="material-symbols-outlined nav-item-icon text-[20px] flex-none">logout</span>
            <span class="nav-label text-sm font-medium whitespace-nowrap truncate">Sign Out</span>
        </a>
    </div>
</aside>