<header class="bg-white border-b border-gray-200/80 px-8 h-[56px] flex items-center justify-between relative z-50 flex-shrink-0">
    <!-- In-app push toasts -->
    <div id="toast-container" class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

    <div class="header-left">
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.18em]">
            <?= $page_title ?? 'Dashboard' ?>
        </p>
    </div>

    <div class="header-right flex items-center gap-4">

        <!-- Notification Bell -->
        <div class="relative" id="notification-wrapper">
            <button id="notification-btn"
                class="relative w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-gray-100 transition-all focus:outline-none">
                <span class="material-symbols-outlined text-[20px]">notifications</span>
                <span id="unread-badge"
                    class="hidden absolute -top-0.5 -right-0.5 w-4 h-4 flex items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white">0</span>
            </button>

            <!-- Notification Dropdown -->
            <div id="notification-dropdown"
                class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl border border-gray-200 shadow-lg overflow-hidden transform origin-top-right transition-all opacity-0 scale-95"
                style="z-index:200;">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Notifications</h3>
                    <div class="flex items-center gap-3">
                        <button id="enable-os-alerts"
                            class="text-[10px] font-bold text-blue-500 hover:text-blue-700 uppercase tracking-wider transition-colors">
                            <i class="fas fa-bell"></i> Alerts
                        </button>
                        <button id="mark-all-read"
                            class="hidden text-[10px] font-bold text-blue-600 hover:text-blue-800 uppercase tracking-wider transition-colors">Mark Read</button>
                        <button id="clear-all-notifs"
                            class="hidden text-[10px] font-bold text-red-500 hover:text-red-700 uppercase tracking-wider transition-colors">Clear All</button>
                    </div>
                </div>
                <div id="notification-list"
                    class="max-h-[340px] overflow-y-auto scrollbar-hide flex flex-col divide-y divide-gray-50 bg-white">
                    <div class="p-6 text-center text-slate-400 text-xs">Loading...</div>
                </div>
                <div class="px-4 py-3 border-t border-gray-100 bg-gray-50 text-center">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">All Notifications</span>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="w-px h-5 bg-gray-200"></div>

        <!-- User Profile -->
        <div class="flex items-center gap-2.5 cursor-pointer">
            <div class="text-right hidden sm:block">
                <p class="text-[13px] font-semibold text-slate-800 leading-none">
                    <?= $userEmail ?? session()->get('email') ?? 'User' ?>
                </p>
                <div class="flex items-center justify-end gap-1.5 mt-0.5">
                    <?php if (isset($userAvailability) && $portalPrefix === 'tsr'): ?>
                        <?php 
                            $statusColor = 'bg-gray-400';
                            if ($userAvailability === 'active') $statusColor = 'bg-emerald-500';
                            elseif ($userAvailability === 'busy') $statusColor = 'bg-amber-500';
                        ?>
                        <div class="flex items-center gap-1 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100" title="Status: <?= ucfirst($userAvailability) ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $statusColor ?>"></span>
                            <span class="text-[8px] font-bold uppercase tracking-wider text-slate-500"><?= $userAvailability ?></span>
                        </div>
                    <?php endif; ?>
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color:var(--color-accent);">
                        <?= $userRole ?? session()->get('role') ?? 'Role' ?>
                    </p>
                </div>
            </div>
            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm font-bold"
                style="background:var(--color-accent);">
                <?= strtoupper(substr($userEmail ?? session()->get('email') ?? 'A', 0, 1)) ?>
            </div>
        </div>

    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const notifBtn      = document.getElementById('notification-btn');
    const notifDropdown = document.getElementById('notification-dropdown');
    const badge         = document.getElementById('unread-badge');
    const list          = document.getElementById('notification-list');
    const markAllReadBtn = document.getElementById('mark-all-read');
    const clearAllBtn   = document.getElementById('clear-all-notifs');
    let isOpen = false;
    let lastNotifId = null;

    // OS Notification permission
    const enableAlertsBtn = document.getElementById('enable-os-alerts');
    if ("Notification" in window) {
        if (Notification.permission === "granted") {
            enableAlertsBtn.style.display = 'none';
        } else {
            enableAlertsBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                Notification.requestPermission().then(p => {
                    if (p === 'granted') {
                        enableAlertsBtn.style.display = 'none';
                        showToast('System', 'Desktop notifications enabled!', null);
                    }
                });
            });
        }
    } else {
        enableAlertsBtn.style.display = 'none';
    }

    // ─── In-App Toast ───
    window.showToast = function(title, message, link) {
        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto bg-white border border-gray-200 shadow-lg rounded-xl p-4 w-72 flex gap-3 cursor-pointer transition-all duration-300 opacity-0 translate-y-2';
        toast.style.fontFamily = 'Inter, sans-serif';
        toast.innerHTML = `
            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 flex-none" style="background:var(--color-accent-light);">
                <span class="material-symbols-outlined text-[16px]" style="color:var(--color-accent);">notifications_active</span>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="text-[11px] font-bold text-slate-800 uppercase tracking-wider">${title}</h4>
                <p class="text-[12px] text-slate-500 mt-0.5 line-clamp-2 leading-relaxed">${message}</p>
            </div>
            <button class="shrink-0 w-5 h-5 flex items-center justify-center text-slate-400 hover:text-slate-700" onclick="this.parentElement.remove(); event.stopPropagation();">
                <span class="material-symbols-outlined text-[16px]">close</span>
            </button>
        `;
        toast.onclick = () => { toast.remove(); if (link && link !== 'null') window.location.href = link; };

        document.getElementById('toast-container').appendChild(toast);
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                toast.classList.remove('opacity-0', 'translate-y-2');
            });
        });
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.remove(), 300);
        }, 6000);
    };

    // Toggle dropdown
    notifBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        isOpen = !isOpen;
        if (isOpen) {
            notifDropdown.classList.remove('hidden');
            setTimeout(() => {
                notifDropdown.classList.remove('opacity-0','scale-95');
                notifDropdown.classList.add('opacity-100','scale-100');
            }, 10);
            fetchNotifications();
        } else {
            closeDropdown();
        }
    });

    document.addEventListener('click', (e) => {
        if (isOpen && !document.getElementById('notification-wrapper').contains(e.target)) closeDropdown();
    });

    function closeDropdown() {
        isOpen = false;
        notifDropdown.classList.remove('opacity-100','scale-100');
        notifDropdown.classList.add('opacity-0','scale-95');
        setTimeout(() => notifDropdown.classList.add('hidden'), 150);
    }

    async function fetchNotifications() {
        try {
            const response = await fetch('<?= base_url('api/notifications/fetch') ?>');
            if (!response.ok) return;
            const data = await response.json();
            if (data.status === 'success') {
                if (data.notifications.length > 0) {
                    const topNotif = data.notifications[0];
                    if (lastNotifId !== null && topNotif.id > lastNotifId && topNotif.is_read == 0) {
                        showToast(topNotif.title, topNotif.message, topNotif.link);
                        if ("Notification" in window && Notification.permission === "granted") {
                            new Notification(topNotif.title, { body: topNotif.message, icon: '<?= base_url("assets/img/logo-icon.png") ?>' });
                        }
                    }
                    lastNotifId = topNotif.id;
                }
                updateUI(data.notifications, data.unread_count);
            }
        } catch(e) { /* silent fail */ }
    }

    function updateUI(notifications, unreadCount) {
        if (unreadCount > 0) {
            badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
            badge.classList.remove('hidden');
            markAllReadBtn.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
            markAllReadBtn.classList.add('hidden');
        }
        clearAllBtn.classList.toggle('hidden', notifications.length === 0);

        list.innerHTML = '';
        if (notifications.length === 0) {
            list.innerHTML = `<div class="p-8 flex flex-col items-center justify-center text-center">
                <span class="material-symbols-outlined text-slate-200 text-4xl mb-3">notifications_off</span>
                <p class="text-slate-400 text-xs font-medium">You're all caught up.</p>
            </div>`;
            return;
        }
        notifications.forEach(n => {
            const isRead = parseInt(n.is_read) === 1;
            const timeStr = new Date(n.created_at).toLocaleTimeString([], { hour:'2-digit', minute:'2-digit' });
            const dateStr = new Date(n.created_at).toLocaleDateString();
            const dot = !isRead ? `<span class="w-1.5 h-1.5 rounded-full mt-2 shrink-0 flex-none" style="background:var(--color-accent)"></span>` : `<span class="w-1.5 shrink-0"></span>`;
            list.insertAdjacentHTML('beforeend', `
                <div class="px-4 py-3 flex items-start gap-3 hover:bg-gray-50 transition-colors cursor-pointer ${isRead ? '' : 'bg-blue-50/40'}"
                    onclick="handleNotificationClick(${n.id}, '${n.link}')">
                    ${dot}
                    <div class="flex-1 min-w-0">
                        <p class="text-[12px] font-semibold ${isRead ? 'text-slate-500' : 'text-slate-800'}">${n.title}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-2">${n.message}</p>
                        <p class="text-[9px] font-bold text-slate-300 uppercase tracking-widest mt-1.5">${dateStr} · ${timeStr}</p>
                    </div>
                </div>
            `);
        });
    }

    window.handleNotificationClick = async (id, link) => {
        try { await fetch(`<?= base_url('api/notifications/read/') ?>${id}`, { method: 'POST', headers: {'X-Requested-With': 'XMLHttpRequest'} }); } catch(e) {}
        if (link && link !== 'null' && link.trim() !== '') window.location.href = link;
        else fetchNotifications();
    };

    markAllReadBtn.addEventListener('click', async (e) => {
        e.stopPropagation();
        try { const res = await fetch('<?= base_url('api/notifications/readAll') ?>', { method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'} }); if(res.ok) fetchNotifications(); } catch(e) {}
    });

    clearAllBtn.addEventListener('click', async (e) => {
        e.stopPropagation();
        try { const res = await fetch('<?= base_url('api/notifications/clearAll') ?>', { method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'} }); if(res.ok) fetchNotifications(); } catch(e) {}
    });

    fetchNotifications();

    if (typeof io !== 'undefined') {
        const globalSocket = io('http://localhost:3001');
        const currentUserId = <?= session()->get('id') ?? session()->get('user_id') ?? 'null' ?>;
        globalSocket.on('new_notification', (data) => {
            if (data.user_id == currentUserId || data.user_id == null) fetchNotifications();
        });
    } else {
        setInterval(fetchNotifications, 10000);
    }
});
</script>