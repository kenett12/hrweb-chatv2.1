<style>
    /* Collapsed Sidebar Styles */
    html.sidebar-is-collapsed #sidebar {
        width: 5rem !important; /* w-20 */
    }
    
    html.sidebar-is-collapsed #sidebar .sidebar-text {
        display: none !important;
    }
    
    html.sidebar-is-collapsed #sidebar .sidebar-logo {
        display: none !important;
    }
    
    html.sidebar-is-collapsed #sidebar .sidebar-logo-icon {
        display: block !important;
    }

    html.sidebar-is-collapsed #sidebar .sidebar-section-title {
        opacity: 0;
        visibility: hidden;
        height: 0;
        margin: 0;
        padding: 0;
    }

    html.sidebar-is-collapsed #sidebar .nav-item {
        justify-content: center;
        padding-left: 0;
        padding-right: 0;
    }

    html.sidebar-is-collapsed #sidebar .nav-icon {
        margin: 0;
    }

    html.sidebar-is-collapsed #sidebar .sidebar-header {
        justify-content: center;
        padding-left: 0;
        padding-right: 0;
    }
</style>

<aside id="sidebar"
    class="w-72 bg-white border-r border-gray-100 flex flex-col h-screen transition-all duration-300 relative z-20 shadow-sm flex-shrink-0">

    <div class="sidebar-header px-8 py-8 flex items-center justify-between transition-all duration-300 h-[88px]">
        <a href="<?= base_url($userRole . "/dashboard") ?>" class="block flex-1">
            <img src="<?= base_url("assets/img/logo.png") ?>" alt="HRWeb Logo"
                class="sidebar-logo h-10 w-auto object-contain transition-all duration-300 hover:scale-105">
            <!-- Icon only version for collapsed state -->
            <img src="<?= base_url("assets/img/logo-icon.png") ?>" alt="HW" 
                class="sidebar-logo-icon hidden h-8 w-8 object-contain transition-all duration-300 hover:scale-105 mx-auto"
                onerror="this.src='<?= base_url('assets/img/logo.png') ?>'; this.classList.replace('w-8', 'w-auto');">
        </a>
        <button id="sidebarToggle" class="text-gray-400 hover:text-indigo-600 transition-colors focus:outline-none hidden md:block">
            <span class="material-symbols-outlined text-[24px]">menu_open</span>
        </button>
    </div>

    <nav class="flex-1 px-4 space-y-1 overflow-y-auto overflow-x-hidden">

        <div class="sidebar-section-title mb-2 px-4 pt-4 transition-all duration-300">
            <p class="sidebar-text text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] whitespace-nowrap overflow-hidden">General</p>
        </div>

        <a href="<?= base_url($userRole . "/dashboard") ?>"
            class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all group <?= url_is($userRole . "/dashboard") ? "bg-blue-50/50 text-[#1e72af] font-bold" : "text-gray-500 hover:bg-gray-50 hover:text-gray-900" ?>" title="Dashboard">
            <span
                class="material-symbols-outlined nav-icon text-[22px] transition-colors shrink-0 <?= url_is($userRole . "/dashboard") ? "text-[#1e72af]" : "group-hover:text-[#1e72af]" ?>">grid_view</span>
            <span class="sidebar-text text-[15px] whitespace-nowrap overflow-hidden">Dashboard</span>
        </a>

        <?php if ($userRole === "superadmin"): ?>
            <div class="sidebar-section-title mb-2 px-4 pt-6 transition-all duration-300">
                <p class="sidebar-text text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] whitespace-nowrap overflow-hidden">Management</p>
            </div>

            <a href="<?= base_url("superadmin/tsr-management") ?>"
                class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all group <?= url_is("superadmin/tsr-management*") ? "bg-cyan-50/50 text-[#3297ca] font-bold" : "text-gray-500 hover:bg-gray-50 hover:text-gray-900" ?>" title="TSR Accounts">
                <span
                    class="material-symbols-outlined nav-icon text-[22px] transition-colors shrink-0 <?= url_is("superadmin/tsr-management*") ? "text-[#3297ca]" : "group-hover:text-[#3297ca]" ?>">admin_panel_settings</span>
                <span class="sidebar-text text-[15px] whitespace-nowrap overflow-hidden">TSR Accounts</span>
            </a>

            <a href="<?= base_url("superadmin/client-management") ?>"
                class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all group <?= url_is("superadmin/client-management*") ? "bg-emerald-50/50 text-[#20ae5c] font-bold" : "text-gray-500 hover:bg-gray-50 hover:text-gray-900" ?>" title="Client Accounts">
                <span
                    class="material-symbols-outlined nav-icon text-[22px] transition-colors shrink-0 <?= url_is("superadmin/client-management*") ? "text-[#20ae5c]" : "group-hover:text-[#20ae5c]" ?>">domain</span>
                <span class="sidebar-text text-[15px] whitespace-nowrap overflow-hidden">Client Accounts</span>
            </a>

            <a href="<?= base_url("superadmin/kb") ?>"
                class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all group <?= url_is("superadmin/kb*") ? "bg-violet-50/50 text-[#7c3aed] font-bold" : "text-gray-500 hover:bg-gray-50 hover:text-gray-900" ?>" title="Bot Knowledge">
                <span
                    class="material-symbols-outlined nav-icon text-[22px] transition-colors shrink-0 <?= url_is("superadmin/kb*") ? "text-[#7c3aed]" : "group-hover:text-[#7c3aed]" ?>">auto_stories</span>
                <span class="sidebar-text text-[15px] whitespace-nowrap overflow-hidden">Bot Knowledge</span>
            </a>

            <div class="sidebar-section-title mb-2 px-4 pt-6 transition-all duration-300">
                <p class="sidebar-text text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] whitespace-nowrap overflow-hidden">Global Oversight</p>
            </div>

            <a href="<?= base_url("superadmin/tickets") ?>"
                class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all group <?= url_is("superadmin/tickets*") ? "bg-blue-50/50 text-[#1e72af] font-bold" : "text-gray-500 hover:bg-gray-50 hover:text-gray-900" ?>" title="All Support Tickets">
                <span
                    class="material-symbols-outlined nav-icon text-[22px] transition-colors shrink-0 <?= url_is("superadmin/tickets*") ? "text-[#1e72af]" : "group-hover:text-[#1e72af]" ?>">confirmation_number</span>
                <span class="sidebar-text text-[15px] whitespace-nowrap overflow-hidden">All Support Tickets</span>
            </a>

            <a href="<?= base_url("superadmin/system-management") ?>"
                class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all group <?= url_is("superadmin/system-management*") ? "bg-purple-50/50 text-[#8b5cf6] font-bold" : "text-gray-500 hover:bg-gray-50 hover:text-gray-900" ?>" title="System Settings">
                <span
                    class="material-symbols-outlined nav-icon text-[22px] transition-colors shrink-0 <?= url_is("superadmin/system-management*") ? "text-[#8b5cf6]" : "group-hover:text-[#8b5cf6]" ?>">settings_alert</span>
                <span class="sidebar-text text-[15px] whitespace-nowrap overflow-hidden">System Settings</span>
            </a>
        <?php endif; ?>

        <?php if ($userRole === "client"): ?>
            <div class="sidebar-section-title mb-2 px-4 pt-6 transition-all duration-300">
                <p class="sidebar-text text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] whitespace-nowrap overflow-hidden">Support & Communication</p>
            </div>

            <a href="<?= base_url("client/tickets") ?>"
                class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all group <?= url_is("client/tickets*") ? "bg-cyan-50/50 text-[#3297ca] font-bold" : "text-gray-500 hover:bg-gray-50 hover:text-gray-900" ?>" title="Support Tickets">
                <span
                    class="material-symbols-outlined nav-icon text-[22px] transition-colors shrink-0 <?= url_is("client/tickets*") ? "text-[#3297ca]" : "group-hover:text-[#3297ca]" ?>">confirmation_number</span>
                <span class="sidebar-text text-[15px] whitespace-nowrap overflow-hidden">Support Tickets</span>
            </a>

            <a href="<?= base_url("client/chat") ?>"
                class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all group <?= url_is("client/chat*") ? "bg-indigo-50/50 text-[#4f46e5] font-bold" : "text-gray-500 hover:bg-gray-50 hover:text-gray-900" ?>" title="Chats">
                <span
                    class="material-symbols-outlined nav-icon text-[22px] transition-colors shrink-0 <?= url_is("client/chat*") ? "text-[#4f46e5]" : "group-hover:text-[#4f46e5]" ?>">chat</span>
                <span class="sidebar-text text-[15px] whitespace-nowrap overflow-hidden">Chats</span>
            </a>

            <div class="sidebar-section-title mb-2 px-4 pt-6 transition-all duration-300">
                <p class="sidebar-text text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] whitespace-nowrap overflow-hidden">Configuration</p>
            </div>

            <a href="<?= base_url("client/settings") ?>"
                class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all group <?= url_is("client/settings*") ? "bg-amber-50/50 text-[#f59e0b] font-bold" : "text-gray-500 hover:bg-gray-50 hover:text-gray-900" ?>" title="Account Settings">
                <span
                    class="material-symbols-outlined nav-icon text-[22px] transition-colors shrink-0 <?= url_is("client/settings*") ? "text-[#f59e0b]" : "group-hover:text-[#f59e0b]" ?>">settings</span>
                <span class="sidebar-text text-[15px] whitespace-nowrap overflow-hidden">Account Settings</span>
            </a>

        <?php endif; ?>

        <?php if ($userRole === "tsr"): ?>
            <div class="sidebar-section-title mb-2 px-4 pt-6 transition-all duration-300">
                <p class="sidebar-text text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] whitespace-nowrap overflow-hidden">Help Desk</p>
            </div>

            <a href="<?= base_url("tsr/tickets") ?>"
                class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all group <?= url_is("tsr/tickets*") ? "bg-amber-50/50 text-[#d97706] font-bold" : "text-gray-500 hover:bg-gray-50 hover:text-gray-900" ?>" title="Manage Tickets">
                <span
                    class="material-symbols-outlined nav-icon text-[22px] transition-colors shrink-0 <?= url_is("tsr/tickets*") ? "text-[#d97706]" : "group-hover:text-[#d97706]" ?>">confirmation_number</span>
                <span class="sidebar-text text-[15px] whitespace-nowrap overflow-hidden">Manage Tickets</span>
            </a>
        <?php endif; ?>

    </nav>

    <div class="p-6 border-t border-gray-50">
        <a href="<?= base_url("logout") ?>" onclick="confirmAction(event, this.href, 'Ready to leave?', 'You will need to log in again to access the portal.', 'Sign Out', '#eb6063')"
            class="nav-item flex items-center gap-3 px-4 py-3 text-[#eb6063] hover:bg-red-50 rounded-xl transition-all font-bold group" title="Sign Out">
            <span
                class="material-symbols-outlined text-[22px] transition-transform group-hover:-translate-x-1 shrink-0">logout</span>
            <span class="sidebar-text text-[14px] uppercase tracking-wider whitespace-nowrap overflow-hidden">Sign Out</span>
        </a>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('sidebarToggle');
        const toggleIcon = toggleBtn.querySelector('span');
        const html = document.documentElement;

        // Set initial icon based on state (since the html class is added in the head)
        if (html.classList.contains('sidebar-is-collapsed')) {
            toggleIcon.textContent = 'menu';
        }

        toggleBtn.addEventListener('click', () => {
            html.classList.toggle('sidebar-is-collapsed');
            
            const isCollapsed = html.classList.contains('sidebar-is-collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
            
            if (isCollapsed) {
                toggleIcon.textContent = 'menu';
            } else {
                toggleIcon.textContent = 'menu_open';
            }
        });
    });
</script>