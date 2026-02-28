<header class="bg-white border-b border-gray-100 px-8 py-4 flex items-center justify-between relative z-50">
    <!-- Overlay Container for In-App Push Toasts -->
    <div id="toast-container" class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"></div>

    <div class="header-left">
        <h1 class="text-sm font-bold text-gray-400 uppercase tracking-[0.2em]">Dashboard Overview</h1>
    </div>

    <div class="header-right flex items-center gap-6">

        <!-- Notification Bell -->
        <div class="relative" id="notification-wrapper">
            <button id="notification-btn" class="relative text-gray-400 hover:text-indigo-600 transition-colors focus:outline-none">
                <span class="material-symbols-outlined text-[24px]">notifications</span>
                <span id="unread-badge" class="hidden absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white shadow-sm ring-2 ring-white">0</span>
            </button>

            <!-- Notification Dropdown -->
            <div id="notification-dropdown" class="hidden absolute right-0 mt-3 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden transform origin-top-right transition-all opacity-0 scale-95" style="z-index: 100;">
                <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest">Notifications</h3>
                    <div class="flex items-center gap-3">
                        <button id="enable-os-alerts" class="text-[10px] font-bold text-blue-500 hover:text-blue-700 uppercase tracking-wider transition-colors" title="Enable Desktop OS Notifications">
                            <i class="fas fa-bell"></i> Alerts
                        </button>
                        <button id="mark-all-read" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-wider transition-colors hidden">Mark Read</button>
                        <button id="clear-all-notifs" class="text-[10px] font-bold text-red-500 hover:text-red-700 uppercase tracking-wider transition-colors hidden">Clear All</button>
                    </div>
                </div>
                <div id="notification-list" class="max-h-[350px] overflow-y-auto scrollbar-hide flex flex-col divide-y divide-gray-50 bg-white">
                    <div class="p-6 text-center text-gray-400 text-xs font-medium">Loading...</div>
                </div>
                <div class="p-3 border-t border-gray-50 bg-gray-50 text-center">
                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">All Notifications</span>
                </div>
            </div>
        </div>

        <!-- User Profile -->
        <div class="flex items-center gap-3 bg-gray-50 px-4 py-2 rounded-xl border border-gray-100 cursor-pointer hover:bg-white hover:border-indigo-100 transition-all shadow-sm">
            <div class="text-right">
                <p class="text-sm font-semibold text-gray-900 leading-none"><?= $userEmail ?? session()->get('email') ?? 'User' ?></p>
                <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider mt-1"><?= $userRole ?? session()->get('role') ?? 'Role' ?></p>
            </div>
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white text-md font-bold shadow-sm shadow-indigo-200">
                <?= strtoupper(substr($userEmail ?? session()->get('email') ?? 'A', 0, 1)) ?>
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const notifBtn = document.getElementById('notification-btn');
    const notifDropdown = document.getElementById('notification-dropdown');
    const badge = document.getElementById('unread-badge');
    const list = document.getElementById('notification-list');
    const markAllReadBtn = document.getElementById('mark-all-read');
    const clearAllBtn = document.getElementById('clear-all-notifs');
    let isOpen = false;
    let lastNotifId = null;

    // Manual OS Notification Permission Request
    const enableAlertsBtn = document.getElementById('enable-os-alerts');
    if ("Notification" in window) {
        if (Notification.permission === "granted") {
            enableAlertsBtn.style.display = 'none'; // Already granted
        } else {
            enableAlertsBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        enableAlertsBtn.style.display = 'none';
                        showToast('System', 'Desktop notifications enabled successfully!', null);
                    }
                });
            });
        }
    } else {
        enableAlertsBtn.style.display = 'none';
    }

    // Custom In-App Toast System (Guaranteed Push Delivery)
    window.showToast = function(title, message, link) {
        const toast = document.createElement('div');
        toast.className = 'bg-white border-l-4 border-indigo-500 shadow-2xl rounded-xl p-5 w-80 transform transition-all duration-500 translate-x-full opacity-0 pointer-events-auto cursor-pointer flex gap-3 relative overflow-hidden';
        toast.innerHTML = `
            <div class="absolute top-0 left-0 w-full h-1 bg-indigo-500/10"></div>
            <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center shrink-0 text-indigo-500">
                <span class="material-symbols-outlined text-sm">notifications_active</span>
            </div>
            <div class="flex-1">
                <h4 class="text-xs font-black text-gray-900 uppercase tracking-wider">${title}</h4>
                <p class="text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed">${message}</p>
            </div>
        `;
        
        toast.onclick = () => { 
            toast.remove();
            if(link && link !== 'null') window.location.href = link; 
        };
        
        document.getElementById('toast-container').appendChild(toast);
        
        // Slide From Right
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            });
        });

        // Auto-remove after 6 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 500);
        }, 6000);
    };

    // Toggle dropdown
    notifBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        isOpen = !isOpen;
        if (isOpen) {
            notifDropdown.classList.remove('hidden');
            setTimeout(() => {
                notifDropdown.classList.remove('opacity-0', 'scale-95');
                notifDropdown.classList.add('opacity-100', 'scale-100');
            }, 10);
            fetchNotifications(); // Refresh on open
        } else {
            closeDropdown();
        }
    });

    // Close on click outside
    document.addEventListener('click', (e) => {
        if (isOpen && !document.getElementById('notification-wrapper').contains(e.target)) {
            closeDropdown();
        }
    });

    function closeDropdown() {
        isOpen = false;
        notifDropdown.classList.remove('opacity-100', 'scale-100');
        notifDropdown.classList.add('opacity-0', 'scale-95');
        setTimeout(() => notifDropdown.classList.add('hidden'), 150);
    }

    async function fetchNotifications() {
        try {
            const response = await fetch('<?= base_url('api/notifications/fetch') ?>');
            if(!response.ok) return;
            const data = await response.json();
            
            if (data.status === 'success') {
                // Desktop Push Logic
                if (data.notifications.length > 0) {
                    const topNotif = data.notifications[0];
                    if (lastNotifId !== null && topNotif.id > lastNotifId && topNotif.is_read == 0) {
                        
                        // 1. GUARANTEED IN-APP PUSH NOTIFICATION
                        showToast(topNotif.title, topNotif.message, topNotif.link);

                        // 2. OS-LEVEL DESKTOP NOTIFICATION (If permitted)
                        if ("Notification" in window) {
                            if (Notification.permission === "granted") {
                                new Notification(topNotif.title, { body: topNotif.message, icon: '<?= base_url("assets/img/logo-icon.png") ?>' });
                            } else if (Notification.permission !== "denied") {
                                Notification.requestPermission();
                            }
                        }
                    }
                    lastNotifId = topNotif.id;
                }

                updateUI(data.notifications, data.unread_count);
            }
        } catch (error) {
            console.error('Notification error:', error);
        }
    }

    function updateUI(notifications, unreadCount) {
        // Update Badge
        if (unreadCount > 0) {
            badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
            badge.classList.remove('hidden');
            markAllReadBtn.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
            markAllReadBtn.classList.add('hidden');
        }

        if (notifications.length > 0) {
            clearAllBtn.classList.remove('hidden');
        } else {
            clearAllBtn.classList.add('hidden');
        }

        // Render List
        list.innerHTML = '';
        if (notifications.length === 0) {
            list.innerHTML = `<div class="p-8 flex flex-col items-center justify-center text-center">
                <span class="material-symbols-outlined text-gray-200 text-4xl mb-3">notifications_off</span>
                <p class="text-gray-400 text-xs font-semibold">You're all caught up!</p>
            </div>`;
            return;
        }

        notifications.forEach(n => {
            const isRead = parseInt(n.is_read) === 1;
            const bgClass = isRead ? 'bg-white' : 'bg-indigo-50/30';
            const dotIndicator = !isRead ? `<span class="w-2 h-2 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.5)] shrink-0 mt-1.5"></span>` : '<span class="w-2 shrink-0"></span>';
            const timeStr = new Date(n.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            const dateStr = new Date(n.created_at).toLocaleDateString();

            const itemHTML = `
                <div class="p-4 hover:bg-gray-50 transition-colors flex items-start gap-3 cursor-pointer ${bgClass}" onclick="handleNotificationClick(${n.id}, '${n.link}')">
                    ${dotIndicator}
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold ${isRead ? 'text-gray-600' : 'text-gray-900'}">${n.title}</p>
                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">${n.message}</p>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-2">${dateStr} • ${timeStr}</p>
                    </div>
                </div>
            `;
            list.insertAdjacentHTML('beforeend', itemHTML);
        });
    }

    window.handleNotificationClick = async (id, link) => {
        try {
            await fetch(`<?= base_url('api/notifications/read/') ?>${id}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
        } catch(e) {}
        
        if (link && link !== 'null' && link.trim() !== '') {
            window.location.href = link;
        } else {
            fetchNotifications(); // Refresh UI if no hard reload
        }
    };

    markAllReadBtn.addEventListener('click', async (e) => {
        e.stopPropagation();
        try {
            const res = await fetch('<?= base_url('api/notifications/readAll') ?>', {
                method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (res.ok) fetchNotifications();
        } catch(e) {}
    });

    clearAllBtn.addEventListener('click', async (e) => {
        e.stopPropagation();
        try {
            const res = await fetch('<?= base_url('api/notifications/clearAll') ?>', {
                method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (res.ok) fetchNotifications();
        } catch(e) {}
    });

    // Initial load of notifications
    fetchNotifications();

    // ── REAL TIME SOCKET PUSH LISTENER (Triggers instant fetch when DB gets new row) ──
    if (typeof io !== 'undefined') {
        const globalSocket = io('http://localhost:3001');
        const currentUserId = <?= session()->get('id') ?? session()->get('user_id') ?? 'null' ?>;
        
        globalSocket.on('new_notification', (data) => {
            // Only update if the notification belongs to the active user or is a broadcast
            if (data.user_id == currentUserId || data.user_id == null) {
                fetchNotifications();
            }
        });
    } else {
        // Fallback to polling if socket.io is not included on the page
        setInterval(fetchNotifications, 10000);
    }
});
</script>