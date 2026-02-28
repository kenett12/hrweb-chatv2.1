<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Support Queue | HRWeb</title>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: #111;
            overflow: hidden;
            height: 100vh;
            width: 100vw;
            display: flex;
            flex-direction: column;
        }

        /* ── Header ─────────────────────────────────── */
        .site-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2.5rem;
            height: 90px;
            background: #fff;
            border-bottom: 4px solid #1f2937;
            flex-shrink: 0;
        }

        .site-header img { height: 52px; width: auto; }

        .live-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #1f2937;
            color: #fff;
            padding: 8px 20px;
            border-radius: 100px;
            font-weight: 800;
            font-size: 13px;
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }

        .live-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 6px rgba(34,197,94,0.7);
            animation: ping 1.5s ease-in-out infinite;
        }

        @keyframes ping {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .clock-area { text-align: right; }
        .clock-area .date { font-size: 12px; font-weight: 700; color: #6b7280; letter-spacing: 0.1em; text-transform: uppercase; }
        .clock-area .time { font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 900; color: #111; letter-spacing: 0.05em; }

        /* ── Main Grid ───────────────────────────────── */
        .main-grid {
            flex: 1;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 0;
            overflow: hidden;
        }

        /* ── Now Serving Panel ───────────────────────── */
        .panel-serving {
            background: #fff;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #e5e7eb;
        }

        .panel-title {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px 32px;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            flex-shrink: 0;
        }

        .panel-title h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.3rem;
            font-weight: 900;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: #111827;
        }

        .serving-grid {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            padding: 20px 24px;
            overflow-y: auto;
            align-content: start;
        }

        .serving-grid::-webkit-scrollbar { display: none; }

        .serving-card {
            background: #f8fafc;
            border: 1.5px solid #e5e7eb;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .serving-card.is-new {
            animation: cardIn 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            border-color: #22c55e;
            box-shadow: 0 0 16px rgba(34,197,94,0.2);
        }

        @keyframes cardIn {
            0%   { opacity: 0; transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }

        .serving-num {
            font-family: 'Outfit', sans-serif;
            font-size: 3rem;
            font-weight: 900;
            line-height: 1;
            color: #111827;
            letter-spacing: -0.02em;
        }

        .serving-agent {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #9ca3af;
        }

        .serving-agent span {
            display: block;
            color: #374151;
            font-size: 14px;
            letter-spacing: 0.05em;
            margin-top: 2px;
        }

        .priority-tag {
            display: inline-flex;
            align-items: center;
            padding: 2px 10px;
            border-radius: 100px;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            border: 1px solid #e5e7eb;
            background: #f3f4f6;
            color: #6b7280;
            width: fit-content;
        }

        .priority-tag.urgent{ background: #fee2e2; color: #b91c1c; border-color: #fca5a5; font-weight: 900; }
        .priority-tag.high  { background: #ffedd5; color: #c2410c; border-color: #fdba74; }
        .priority-tag.medium{ background: #fef3c7; color: #d97706; border-color: #fde68a; }
        .priority-tag.low   { background: #dcfce7; color: #16a34a; border-color: #bbf7d0; }

        /* ── Waiting Panel ───────────────────────────── */
        .panel-waiting {
            background: #f0f4f8;
            display: flex;
            flex-direction: column;
            color: #111;
        }

        .panel-waiting .panel-title {
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
        }

        .panel-waiting .panel-title h2 { color: #1f2937; }

        .queue-badge {
            margin-left: auto;
            background: #1f2937;
            color: #fff;
            font-size: 13px;
            font-weight: 900;
            padding: 4px 14px;
            border-radius: 100px;
            letter-spacing: 0.1em;
        }

        .waiting-list {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .waiting-list::-webkit-scrollbar { display: none; }

        .waiting-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #374151;
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            transition: box-shadow 0.15s;
        }

        .waiting-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }

        .waiting-num {
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 900;
            color: #1f2937;
            min-width: 6rem;
            letter-spacing: -0.01em;
        }

        .waiting-info {
            flex: 1;
            min-width: 0;
        }

        .waiting-client {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            truncate: ellipsis;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .waiting-subject {
            font-size: 11px;
            color: #6b7280;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 2px;
        }

        .w-priority-tag {
            flex-shrink: 0;
            padding: 2px 10px;
            border-radius: 100px;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .w-priority-tag.urgent { background: #fee2e2; color: #b91c1c; }
        .w-priority-tag.high   { background: #ffedd5; color: #c2410c; }
        .w-priority-tag.medium { background: #fef3c7; color: #d97706; }
        .w-priority-tag.low    { background: #dcfce7; color: #16a34a; }

        /* ── Empty States ───────────────────────────── */
        .empty-state {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0.4;
            gap: 12px;
        }

        .empty-state span { font-size: 52px; }
        .empty-state p { font-size: 1rem; font-weight: 600; }

        /* ── Footer ─────────────────────────────────── */
        .site-footer {
            height: 36px;
            background: rgba(0,0,0,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-top: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
            color: rgba(255,255,255,0.5);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
    </style>
</head>

<body>

    <!-- ── Header ─────────────────────────────────────────── -->
    <header class="site-header">
        <div style="display:flex; align-items:center; gap:20px;">
            <img src="<?= base_url('assets/img/logo.png') ?>" alt="HRWeb Logo">
            <div style="width:1px; height:40px; background:#e5e7eb;"></div>
            <div class="live-badge">
                <div class="live-dot"></div>
                Live Queue
            </div>
        </div>
        <div class="clock-area">
            <div id="date" class="date">Loading...</div>
            <div id="clock" class="time">00:00:00</div>
        </div>
    </header>

    <!-- ── Main ──────────────────────────────────────────── -->
    <div class="main-grid">

        <!-- Left: Now Serving -->
        <div class="panel-serving">
            <div class="panel-title">
                <span class="material-symbols-outlined" style="font-size:22px; color:#4ade80;">support_agent</span>
                <h2>In Progress</h2>
            </div>
            <div class="serving-grid" id="serving-container">
                <div class="empty-state" style="grid-column:1/-1;">
                    <span class="material-symbols-outlined">hourglass_top</span>
                    <p>Loading sessions...</p>
                </div>
            </div>
        </div>

        <!-- Right: Waiting -->
        <div class="panel-waiting">
            <div class="panel-title">
                <span class="material-symbols-outlined" style="font-size:22px; color:#1561a0;">queue</span>
                <h2>Waiting</h2>
                <div class="queue-badge"><span id="queue-count">0</span> IN QUEUE</div>
            </div>
            <div class="waiting-list" id="waiting-container">
                <!-- Injection target -->
            </div>
        </div>

    </div>

    <!-- ── Footer ─────────────────────────────────────────── -->
    <footer class="site-footer">
        <span class="material-symbols-outlined" style="font-size:14px;">info</span>
        Please wait for your ticket number to appear in the "Now Serving" column.
    </footer>

    <script>
        // ── Clock ──────────────────────────────────────────
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString('en-US', { hour12: true, hour: 'numeric', minute: '2-digit', second: '2-digit' });
            document.getElementById('date').innerText  = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
        }
        setInterval(updateClock, 1000);
        updateClock();

        // ── Queue State ─────────────────────────────────────
        const servingContainer = document.getElementById('serving-container');
        const waitingContainer = document.getElementById('waiting-container');
        const queueCount       = document.getElementById('queue-count');
        const bellSound        = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');

        let previousServingIds  = [];
        let previousWaitingIds  = [];
        let isInitialLoad       = true;
        let newlyServedThisTick = [];

        // ── Fetch ───────────────────────────────────────────
        function fetchQueueData() {
            fetch("<?= site_url('queue-display/data') ?>")
                .then(res => { if (!res.ok) throw new Error(res.status); return res.json(); })
                .then(data => {
                    if (!data || !Array.isArray(data.now_serving) || !Array.isArray(data.waiting)) return;

                    newlyServedThisTick = [];

                    if (!isInitialLoad) {
                        const newServing = data.now_serving.filter(t => !previousServingIds.includes(t.ticket_number));
                        if (newServing.length > 0) {
                            bellSound.play().catch(() => {});
                            newServing.forEach(t => newlyServedThisTick.push(t.ticket_number));
                        }
                    }

                    renderServing(data.now_serving);
                    renderWaiting(data.waiting);

                    previousServingIds = data.now_serving.map(t => t.ticket_number);
                    previousWaitingIds = data.waiting.map(t => t.ticket_number);
                    isInitialLoad = false;
                })
                .catch(err => console.error('Live Queue Error:', err));
        }

        // ── Helpers ─────────────────────────────────────────
        function priorityClass(priority) {
            const p = priority.toLowerCase();
            if (p === 'urgent') return 'urgent';
            if (p === 'high') return 'high';
            if (p === 'medium') return 'medium';
            return 'low';
        }

        function getPriorityWeight(priority) {
            const p = priority.toLowerCase();
            if (p === 'urgent') return 4;
            if (p === 'high') return 3;
            if (p === 'medium') return 2;
            return 1;
        }

        // ── Render Serving ──────────────────────────────────
        function renderServing(tickets) {
            if (tickets.length === 0) {
                servingContainer.innerHTML = `
                    <div class="empty-state" style="grid-column:1/-1;">
                        <span class="material-symbols-outlined">celebration</span>
                        <p>No active sessions</p>
                    </div>`;
                return;
            }

            // Sort by priority (highest first)
            tickets.sort((a, b) => getPriorityWeight(b.priority) - getPriorityWeight(a.priority));

            let html = '';
            tickets.forEach(t => {
                const isNew = newlyServedThisTick.includes(t.ticket_number);
                html += `
                <div class="serving-card ${isNew ? 'is-new' : ''}">
                    <span class="priority-tag ${priorityClass(t.priority)}">${t.priority}</span>
                    <div class="serving-num">${t.ticket_number}</div>
                    <div class="serving-agent">
                        Assigned TSR
                        <span>${t.counter}</span>
                    </div>
                </div>`;
            });
            servingContainer.innerHTML = html;
        }

        // ── Render Waiting ──────────────────────────────────
        function renderWaiting(tickets) {
            queueCount.innerText = tickets.length;

            if (tickets.length === 0) {
                waitingContainer.innerHTML = `
                    <div class="empty-state" style="color:#94a3b8;">
                        <span class="material-symbols-outlined" style="font-size:42px;">inbox</span>
                        <p style="font-size:13px;">No tickets waiting</p>
                    </div>`;
                return;
            }

            // Sort by priority (highest first)
            tickets.sort((a, b) => getPriorityWeight(b.priority) - getPriorityWeight(a.priority));

            let html = '';
            tickets.forEach(t => {
                html += `
                <div class="waiting-card">
                    <div class="waiting-num">${t.ticket_number}</div>
                    <div class="waiting-info">
                        <div class="waiting-client">${t.client}</div>
                        <div class="waiting-subject">${t.subject}</div>
                    </div>
                    <span class="w-priority-tag ${priorityClass(t.priority)}">${t.priority}</span>
                </div>`;
            });
            waitingContainer.innerHTML = html;
        }

        setInterval(fetchQueueData, 3000);
        fetchQueueData();
    </script>

</body>
</html>
