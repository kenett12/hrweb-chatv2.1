<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'HRWeb' ?> | HRWeb Inc.</title>

    <!-- SAP 72 Font via Google Fonts (Inter is the closest open-source match) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="<?= base_url('assets/css/global/main.css') ?>?v=<?= time() ?>">

    <script>
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            document.documentElement.classList.add('sidebar-is-collapsed');
        }
    </script>

    <?= $this->renderSection('styles') ?>
</head>

<body style="background:var(--fiori-page-bg); color:var(--fiori-text-base); font-family:'Inter',sans-serif;" class="h-screen flex flex-col overflow-hidden">

    <!-- Global Preloader -->
    <div id="globalPreloader"><div class="preloader-spinner"></div></div>

    <?php if (!url_is('login*') && !url_is('auth*')): ?>
    <!-- SAP Shell Header (top bar) -->
    <div class="fiori-shell-header flex-shrink-0">
        <!-- Hamburger -->
        <button id="sidebarToggle" class="w-8 h-8 flex items-center justify-center rounded text-white/70 hover:text-white hover:bg-white/10 transition-colors focus:outline-none mr-1">
            <span class="material-symbols-outlined text-[20px]" id="toggleIcon">menu</span>
        </button>

        <!-- Logo + App Name -->
        <a href="<?= base_url($portalPrefix . '/dashboard') ?>" class="flex items-center gap-2 text-white hover:no-underline flex-shrink-0">
            <img src="<?= base_url('assets/img/logo.png') ?>" alt="HRWeb Inc." class="h-6 object-contain" style="filter:brightness(0) invert(1)" onerror="this.style.display='none'">
            <span class="text-sm font-semibold tracking-wide">HRWeb Inc.</span>
        </a>

        <div class="flex-1"></div>

        <!-- Notification Bell -->
        <div class="relative" id="notification-wrapper">
            <button id="notification-btn" class="relative w-8 h-8 flex items-center justify-center rounded text-white/70 hover:text-white hover:bg-white/10 transition-colors focus:outline-none">
                <span class="material-symbols-outlined text-[20px]">notifications</span>
                <span id="unread-badge" class="hidden absolute -top-0.5 -right-0.5 w-4 h-4 flex items-center justify-center rounded-full text-[9px] font-bold text-white" style="background:var(--fiori-negative);">0</span>
            </button>
            <!-- Notification Dropdown -->
            <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white border rounded shadow-lg overflow-hidden origin-top-right transition-all opacity-0 scale-95" style="border-color:var(--fiori-border); box-shadow:0 8px 24px rgba(0,0,0,0.12); z-index:200; border-radius:4px;">
                <div class="px-4 py-3 border-b flex items-center justify-between" style="border-color:var(--fiori-border); background:#f7f7f7;">
                    <h3 class="text-xs font-semibold uppercase tracking-wider" style="color:var(--fiori-text-secondary);">Notifications</h3>
                    <div class="flex items-center gap-3">
                        <button id="enable-os-alerts" class="text-[10px] font-semibold" style="color:var(--fiori-blue);">Enable Alerts</button>
                        <button id="mark-all-read" class="hidden text-[10px] font-semibold" style="color:var(--fiori-blue);">Mark Read</button>
                        <button id="clear-all-notifs" class="hidden text-[10px] font-semibold" style="color:var(--fiori-negative);">Clear All</button>
                    </div>
                </div>
                <div id="notification-list" class="max-h-[320px] overflow-y-auto scrollbar-hide bg-white">
                    <div class="p-6 text-center text-xs" style="color:var(--fiori-text-muted);">Loading...</div>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="w-px h-5 bg-white/20 mx-1"></div>

        <!-- User -->
        <div class="flex items-center gap-2 cursor-pointer px-2 py-1 rounded hover:bg-white/10 transition-colors">
            <div class="w-7 h-7 rounded flex items-center justify-center text-white text-xs font-semibold flex-shrink-0" style="background:var(--fiori-blue);">
                <?= strtoupper(substr($userEmail ?? session()->get('email') ?? 'A', 0, 1)) ?>
            </div>
            <div class="hidden sm:block text-right">
                <p class="text-xs font-medium text-white leading-none"><?= $userEmail ?? session()->get('email') ?? 'User' ?></p>
                <div class="flex items-center justify-end gap-1.5 mt-1">
                    <?php if (isset($userAvailability)): ?>
                        <?php 
                            $statusColor = 'bg-gray-400';
                            $displayStatus = 'Offline';
                            if ($userAvailability === 'active') { $statusColor = 'bg-emerald-500'; $displayStatus = 'Online'; }
                            elseif ($userAvailability === 'busy') { $statusColor = 'bg-amber-500'; $displayStatus = 'Busy'; }
                        ?>
                        <div class="flex items-center gap-1 bg-white/10 px-1 py-0.5 rounded border border-white/20" title="Availability: <?= $displayStatus ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $statusColor ?>"></span>
                            <span class="text-[8px] font-bold uppercase tracking-wider text-white/90 leading-none"><?= $displayStatus ?></span>
                        </div>
                    <?php endif; ?>
                    <p class="text-[10px] text-white/60 leading-none uppercase tracking-wider"><?= $userRole ?? session()->get('role') ?></p>
                </div>
            </div>
        </div>

        <!-- Toast container (in-app) -->
        <div id="toast-container" class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-2 pointer-events-none"></div>
    </div>
    <?php endif; ?>

    <!-- Main body below header -->
    <div class="flex flex-1 overflow-hidden">

        <?php if (!url_is('login*') && !url_is('auth*')): ?>
        <?= $this->include('shared/sidebar') ?>
        <?php endif; ?>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <main class="flex-1 overflow-y-auto <?= (url_is('login*') || url_is('auth*')) ? 'p-0' : 'p-6' ?>">
                <div class="fade-in">
                    <?= $this->renderSection('content') ?>
                </div>
            </main>

            <?php if (!url_is('login*') && !url_is('auth*')): ?>
            <footer class="flex-shrink-0 px-6 py-2 border-t text-xs" style="border-color:var(--fiori-border); background:var(--fiori-surface); color:var(--fiori-text-muted);">
                &copy; <?= date('Y') ?> HRWeb Inc. All rights reserved.
            </footer>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modals (Rendered outside the flex wrappers to ensure fixed positioning works) -->
    <?= $this->renderSection('modals') ?>

    <script src="<?= base_url('assets/js/global/utils.js') ?>?v=<?= time() ?>"></script>
    <script>
        // SAP shell sidebar toggle
        const toggleBtn = document.getElementById('sidebarToggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                document.documentElement.classList.toggle('sidebar-is-collapsed');
                const isCollapsed = document.documentElement.classList.contains('sidebar-is-collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
                document.getElementById('toggleIcon').textContent = isCollapsed ? 'menu' : 'menu_open';
            });
            // Set initial icon
            if (document.documentElement.classList.contains('sidebar-is-collapsed')) {
                const icon = document.getElementById('toggleIcon');
                if(icon) icon.textContent = 'menu';
            }
        }

        // Notification system
        (() => {
            const notifBtn = document.getElementById('notification-btn');
            const notifDropdown = document.getElementById('notification-dropdown');
            const badge = document.getElementById('unread-badge');
            const list = document.getElementById('notification-list');
            const markAllReadBtn = document.getElementById('mark-all-read');
            const clearAllBtn = document.getElementById('clear-all-notifs');
            const enableAlertsBtn = document.getElementById('enable-os-alerts');
            if (!notifBtn) return;
            let isOpen = false, lastNotifId = null;

            window.showToast = function(title, message, link) {
                const toast = document.createElement('div');
                toast.className = 'pointer-events-auto bg-white border rounded flex gap-3 p-4 w-72 cursor-pointer transition-all duration-300 opacity-0 translate-y-2';
                toast.style.cssText = `border-color:var(--fiori-border); box-shadow:0 4px 12px rgba(0,0,0,0.1); font-family:'Inter',sans-serif; border-radius:4px;`;
                toast.innerHTML = `
                    <div class="w-1 rounded-full flex-none" style="background:var(--fiori-blue);"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold" style="color:var(--fiori-text-base);">${title}</p>
                        <p class="text-xs mt-0.5 line-clamp-2" style="color:var(--fiori-text-secondary);">${message}</p>
                    </div>
                    <button class="text-gray-300 hover:text-gray-500 flex-none" onclick="this.parentElement.remove();event.stopPropagation();">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </button>`;
                toast.onclick = () => { toast.remove(); if (link && link !== 'null') window.location.href = link; };
                document.getElementById('toast-container').appendChild(toast);
                requestAnimationFrame(() => requestAnimationFrame(() => { toast.classList.remove('opacity-0','translate-y-2'); }));
                setTimeout(() => { toast.classList.add('opacity-0','translate-y-2'); setTimeout(() => toast.remove(), 300); }, 6000);
            };

            if ("Notification" in window && Notification.permission === "granted") enableAlertsBtn.style.display = 'none';
            else enableAlertsBtn?.addEventListener('click', e => { e.stopPropagation(); Notification.requestPermission().then(p => { if(p==='granted') enableAlertsBtn.style.display='none'; }); });

            notifBtn.addEventListener('click', e => {
                e.stopPropagation(); isOpen = !isOpen;
                if (isOpen) { notifDropdown.classList.remove('hidden'); requestAnimationFrame(() => { notifDropdown.classList.remove('opacity-0','scale-95'); notifDropdown.classList.add('opacity-100','scale-100'); }); fetchNotifications(); }
                else closeDropdown();
            });
            document.addEventListener('click', e => { if (isOpen && !document.getElementById('notification-wrapper').contains(e.target)) closeDropdown(); });
            function closeDropdown() { isOpen=false; notifDropdown.classList.remove('opacity-100','scale-100'); notifDropdown.classList.add('opacity-0','scale-95'); setTimeout(()=>notifDropdown.classList.add('hidden'),150); }

            async function fetchNotifications() {
                try {
                    const r = await fetch('<?= base_url('api/notifications/fetch') ?>');
                    if(!r.ok) return;
                    const data = await r.json();
                    if (data.status==='success') {
                        if (data.notifications.length>0) {
                            const top = data.notifications[0];
                            if (lastNotifId!==null && top.id>lastNotifId && top.is_read==0) {
                                showToast(top.title, top.message, top.link);
                                if ("Notification" in window && Notification.permission==="granted") new Notification(top.title,{body:top.message});
                            }
                            lastNotifId=top.id;
                        }
                        updateUI(data.notifications, data.unread_count);
                    }
                } catch(e) {}
            }
            function updateUI(notifs, count) {
                if(count>0){badge.textContent=count>99?'99+':count;badge.classList.remove('hidden');markAllReadBtn.classList.remove('hidden');}
                else{badge.classList.add('hidden');markAllReadBtn.classList.add('hidden');}
                clearAllBtn.classList.toggle('hidden', notifs.length===0);
                list.innerHTML='';
                if(!notifs.length){list.innerHTML=`<div class="p-8 text-center text-xs" style="color:var(--fiori-text-muted);">No notifications.</div>`;return;}
                notifs.forEach(n=>{
                    const read=parseInt(n.is_read)===1;
                    const time=new Date(n.created_at).toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});
                    list.insertAdjacentHTML('beforeend',`
                        <div class="px-4 py-3 flex gap-3 cursor-pointer hover:bg-blue-50 border-b transition-colors ${read?'':'bg-blue-50/40'}" style="border-color:var(--fiori-border);" onclick="handleNotificationClick(${n.id},'${n.link}')">
                            ${!read?`<span class="w-1.5 h-1.5 rounded-full mt-2 flex-none" style="background:var(--fiori-blue);"></span>`:'<span class="w-1.5 flex-none"></span>'}
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold" style="color:${read?'var(--fiori-text-secondary)':'var(--fiori-text-base)'};">${n.title}</p>
                                <p class="text-[11px] mt-0.5 line-clamp-2" style="color:var(--fiori-text-secondary);">${n.message}</p>
                                <p class="text-[10px] mt-1" style="color:var(--fiori-text-muted);">${time}</p>
                            </div>
                        </div>`);
                });
            }
            window.handleNotificationClick=async(id,link)=>{try{await fetch(`<?= base_url('api/notifications/read/') ?>${id}`,{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest'}});}catch(e){}; if(link&&link!=='null'&&link.trim()!=='')window.location.href=link; else fetchNotifications();};
            markAllReadBtn.addEventListener('click',async e=>{e.stopPropagation();try{const r=await fetch('<?= base_url('api/notifications/readAll') ?>',{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest'}});if(r.ok)fetchNotifications();}catch(e){}});
            clearAllBtn.addEventListener('click',async e=>{e.stopPropagation();try{const r=await fetch('<?= base_url('api/notifications/clearAll') ?>',{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest'}});if(r.ok)fetchNotifications();}catch(e){}});
            fetchNotifications();
            if(typeof io!=='undefined'){const s=io('http://localhost:3001');const uid=<?= session()->get('id')??session()->get('user_id')??'null' ?>;s.on('new_notification',d=>{if(d.user_id==uid||d.user_id==null)fetchNotifications();});}
            else setInterval(fetchNotifications,10000);
        })();

        // SweetAlert Toast
        const Toast = Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3500, timerProgressBar:true, customClass:{popup:'swal2-toast'}, didOpen:(t)=>{t.addEventListener('mouseenter',Swal.stopTimer);t.addEventListener('mouseleave',Swal.resumeTimer);} });
        <?php if(session()->getFlashdata('success')): ?>Toast.fire({icon:'success',title:'<?= esc(session()->getFlashdata('success')) ?>'});<?php endif; ?>
        <?php if(session()->getFlashdata('error')): ?>Toast.fire({icon:'error',title:'<?= esc(session()->getFlashdata('error')) ?>'});<?php endif; ?>
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>