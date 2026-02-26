<aside id="sidebar"
    class="w-72 bg-white border-r border-gray-100 flex flex-col h-screen transition-all duration-300 relative z-20 shadow-sm">

    <div class="px-8 py-10">
        <a href="<?= base_url($userRole . "/dashboard") ?>" class="block">
            <img src="<?= base_url("assets/img/logo.png") ?>" alt="HRWeb Logo"
                class="h-10 w-auto object-contain transition-all duration-300 hover:scale-105">
        </a>
    </div>

    <nav class="flex-1 px-4 space-y-1 overflow-y-auto">

        <div class="mb-2 px-4 pt-4">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">General</p>
        </div>

        <a href="<?= base_url($userRole . "/dashboard") ?>"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group <?= url_is($userRole . "/dashboard") ? "bg-blue-50/50 text-[#1e72af] font-bold" : "text-gray-500 hover:bg-gray-50 hover:text-gray-900" ?>">
            <span
                class="material-symbols-outlined nav-icon text-[22px] transition-colors <?= url_is($userRole . "/dashboard") ? "text-[#1e72af]" : "group-hover:text-[#1e72af]" ?>">grid_view</span>
            <span class="text-[15px]">Dashboard</span>
        </a>

        <?php if ($userRole === "superadmin"): ?>
            <div class="mb-2 px-4 pt-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Management</p>
            </div>

            <a href="<?= base_url("superadmin/tsr-management") ?>"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group <?= url_is("superadmin/tsr-management*") ? "bg-cyan-50/50 text-[#3297ca] font-bold" : "text-gray-500 hover:bg-gray-50 hover:text-gray-900" ?>">
                <span
                    class="material-symbols-outlined nav-icon text-[22px] transition-colors <?= url_is("superadmin/tsr-management*") ? "text-[#3297ca]" : "group-hover:text-[#3297ca]" ?>">admin_panel_settings</span>
                <span class="text-[15px]">TSR Accounts</span>
            </a>

            <a href="<?= base_url("superadmin/client-management") ?>"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group <?= url_is("superadmin/client-management*") ? "bg-emerald-50/50 text-[#20ae5c] font-bold" : "text-gray-500 hover:bg-gray-50 hover:text-gray-900" ?>">
                <span
                    class="material-symbols-outlined nav-icon text-[22px] transition-colors <?= url_is("superadmin/client-management*") ? "text-[#20ae5c]" : "group-hover:text-[#20ae5c]" ?>">domain</span>
                <span class="text-[15px]">Client Accounts</span>
            </a>

            <a href="<?= base_url("superadmin/kb") ?>"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group <?= url_is("superadmin/kb*") ? "bg-violet-50/50 text-[#7c3aed] font-bold" : "text-gray-500 hover:bg-gray-50 hover:text-gray-900" ?>">
                <span
                    class="material-symbols-outlined nav-icon text-[22px] transition-colors <?= url_is("superadmin/kb*") ? "text-[#7c3aed]" : "group-hover:text-[#7c3aed]" ?>">auto_stories</span>
                <span class="text-[15px]">Bot Knowledge</span>
            </a>

            <div class="mb-2 px-4 pt-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Global Oversight</p>
            </div>

            <a href="<?= base_url("superadmin/tickets") ?>"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group <?= url_is("superadmin/tickets*") ? "bg-blue-50/50 text-[#1e72af] font-bold" : "text-gray-500 hover:bg-gray-50 hover:text-gray-900" ?>">
                <span
                    class="material-symbols-outlined nav-icon text-[22px] transition-colors <?= url_is("superadmin/tickets*") ? "text-[#1e72af]" : "group-hover:text-[#1e72af]" ?>">confirmation_number</span>
                <span class="text-[15px]">All Support Tickets</span>
            </a>
        <?php endif; ?>

        <?php if ($userRole === "client"): ?>
            <div class="mb-2 px-4 pt-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Support & Communication</p>
            </div>

            <a href="<?= base_url("client/tickets") ?>"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group <?= url_is("client/tickets*") ? "bg-cyan-50/50 text-[#3297ca] font-bold" : "text-gray-500 hover:bg-gray-50 hover:text-gray-900" ?>">
                <span
                    class="material-symbols-outlined nav-icon text-[22px] transition-colors <?= url_is("client/tickets*") ? "text-[#3297ca]" : "group-hover:text-[#3297ca]" ?>">confirmation_number</span>
                <span class="text-[15px]">Support Tickets</span>
            </a>

            <a href="<?= base_url("client/chat") ?>"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group <?= url_is("client/chat*") ? "bg-indigo-50/50 text-[#4f46e5] font-bold" : "text-gray-500 hover:bg-gray-50 hover:text-gray-900" ?>">
                <span
                    class="material-symbols-outlined nav-icon text-[22px] transition-colors <?= url_is("client/chat*") ? "text-[#4f46e5]" : "group-hover:text-[#4f46e5]" ?>">chat</span>
                <span class="text-[15px]">Chats</span>
            </a>

        <?php endif; ?>

        <?php if ($userRole === "tsr"): ?>
            <div class="mb-2 px-4 pt-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Help Desk</p>
            </div>

            <a href="<?= base_url("tsr/tickets") ?>"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group <?= url_is("tsr/tickets*") ? "bg-amber-50/50 text-[#d97706] font-bold" : "text-gray-500 hover:bg-gray-50 hover:text-gray-900" ?>">
                <span
                    class="material-symbols-outlined nav-icon text-[22px] transition-colors <?= url_is("tsr/tickets*") ? "text-[#d97706]" : "group-hover:text-[#d97706]" ?>">confirmation_number</span>
                <span class="text-[15px]">Manage Tickets</span>
            </a>
        <?php endif; ?>

    </nav>

    <div class="p-6 border-t border-gray-50">
        <a href="<?= base_url("logout") ?>"
            class="flex items-center gap-3 px-4 py-3 text-[#eb6063] hover:bg-red-50 rounded-xl transition-all font-bold group">
            <span
                class="material-symbols-outlined text-[22px] transition-transform group-hover:-translate-x-1">logout</span>
            <span class="text-[14px] uppercase tracking-wider">Sign Out</span>
        </a>
    </div>
</aside>