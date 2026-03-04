<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Support Queue | HRWeb</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            /* ── Sync with HRWeb main.css ── */
            --fiori-shell-bg: #0f1e2e;
            --fiori-shell-text: #ffffff;
            --fiori-page-bg: #f7f7f7;
            --fiori-surface: #ffffff;
            --fiori-border: #d9d9d9;
            --fiori-blue: #0A6ED1;
            --fiori-blue-light: #e8f3ff;
            --fiori-positive: #107e3e;
            --fiori-positive-light: #f1fdf6;
            --fiori-negative: #bb0000;
            --fiori-negative-light: #fff0f0;
            --fiori-warning: #e9730c;
            --fiori-warning-light: #fff3e0;
            --fiori-text-base: #32363a;
            --fiori-text-secondary: #6a6d70;
            --fiori-text-muted: #89919a;
            --fiori-card-shadow: 0 0.125rem 0.5rem rgba(0,0,0,0.08);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--fiori-page-bg);
            color: var(--fiori-text-base);
            overflow: hidden;
            height: 100vh;
            width: 100vw;
            display: flex;
            flex-direction: column;
            font-size: 16px; /* High base size for TV */
        }

        /* ── Fiori Shell Header ── */
        .fiori-shell-header {
            height: 48px;
            background: var(--fiori-shell-bg);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            flex-shrink: 0;
            z-index: 100;
            box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.15);
        }

        .shell-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #fff;
        }

        .shell-logo img {
            height: 24px;
            filter: brightness(0) invert(1);
        }

        .shell-app-name {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .shell-clock {
            display: flex;
            align-items: center;
            gap: 16px;
            color: rgba(255,255,255,0.85);
            font-size: 13px;
            font-weight: 500;
        }

        #clock { font-weight: 700; color: #fff; font-size: 15px; }

        /* ── Main Layout (50/50 Split) ── */
        .fiori-page-content {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            padding: 1.5rem;
            overflow: hidden; /* We scroll the cards, not the page */
        }

        /* ── Fiori Card Components ── */
        .fiori-card {
            background: var(--fiori-surface);
            border: 1px solid var(--fiori-border);
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 0.125rem 0.5rem rgba(0,0,0,0.08); /* Matches main.css */
            overflow: hidden;
            height: 100%;
        }

        .fiori-card__header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--fiori-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fafafa;
            flex-shrink: 0;
            z-index: 5;
        }

        .fiori-card__title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--fiori-text-base);
            display: flex;
            align-items: center;
            gap: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .fiori-card__content {
            padding: 0;
            flex: 1;
            overflow-y: auto;
            background: #ffffff;
        }

        /* ── Standard Fiori Table Overrides for TV ── */
        .fiori-table {
            width: 100%;
            border-collapse: collapse;
        }
        .fiori-table th {
            background: #f8f9fa;
            padding: 1.25rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--fiori-text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-align: left;
            border-bottom: 2px solid var(--fiori-border);
        }
        .fiori-table td {
            padding: 1.5rem;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
            font-size: 1.125rem;
        }
        .fiori-table tr:hover { background: var(--fiori-blue-light); }

        .ticket-number-cell {
            font-weight: 800;
            color: var(--fiori-blue);
            font-family: 'Inter', sans-serif;
            font-size: 1.5rem;
        }

        .is-new {
            background: var(--fiori-blue-light) !important;
            animation: fioriPulse 2s infinite;
        }

        @keyframes fioriPulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        .status-badge {
            display: inline-flex;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            border: 1px solid transparent;
        }

        .status--positive { color: var(--fiori-positive); background: var(--fiori-positive-light); border-color: var(--fiori-positive-border); }
        .status--negative { color: var(--fiori-negative); background: var(--fiori-negative-light); border-color: var(--fiori-negative-border); }
        .status--warning  { color: var(--fiori-warning); background: var(--fiori-warning-light); border-color: var(--fiori-warning-border); }
        .status--info     { color: var(--fiori-blue); background: var(--fiori-blue-light); border-color: #b3d4fb; }

        .fiori-empty {
            padding: 4rem;
            text-align: center;
            color: var(--fiori-text-muted);
        }

        .fiori-empty i { font-size: 2rem; opacity: 0.3; }

        /* ── Footer ── */
        .fiori-page-footer {
            height: 32px;
            background: var(--fiori-surface);
            border-top: 1px solid var(--fiori-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: var(--fiori-text-muted);
            font-weight: 500;
            flex-shrink: 0;
        }

        @media (max-width: 1100px) {
            .fiori-page-content { grid-template-columns: 1fr; overflow-y: auto; }
            .fiori-card { height: auto; min-height: 400px; }
        }
    </style>
</head>

<body>

    <!-- Fiori Shell Header -->
    <header class="fiori-shell-header">
        <div class="shell-logo">
            <img src="<?= base_url('assets/img/logo.png') ?>" alt="HRWeb">
            <span class="shell-app-name">Live Support Queue</span>
        </div>
        <div class="shell-clock">
            <span id="date"></span>
            <div style="width:1px; height:14px; background:rgba(255,255,255,0.2);"></div>
            <span id="clock">00:00:00</span>
        </div>
    </header>

    <!-- Fiori Page Content -->
    <main class="fiori-page-content">
        
        <!-- Now Serving (Left Panel) -->
        <section class="fiori-card">
            <div class="fiori-card__header">
                <div class="fiori-card__title">
                    <i class="fas fa-headset"></i>
                    In Progress
                </div>
                <div class="fiori-status status--positive" id="serving-count">0 ACTIVE</div>
            </div>
            <div class="fiori-card__content">
                <table class="fiori-table">
                    <thead>
                        <tr>
                            <th>Ticket #</th>
                            <th>Client / Employee</th>
                            <th>Staff</th>
                        </tr>
                    </thead>
                    <tbody id="serving-container">
                        <!-- Rows injected here -->
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Waiting Queue (Right Panel) -->
        <section class="fiori-card">
            <div class="fiori-card__header">
                <div class="fiori-card__title">
                    <i class="fas fa-users"></i>
                    Pending Tickets
                </div>
                <div class="fiori-status status--info" id="queue-count-label">0 WAITING</div>
            </div>
            <div class="fiori-card__content">
                <table class="fiori-table">
                    <thead>
                        <tr>
                            <th>Ticket #</th>
                            <th>Client Name</th>
                            <th>Details</th>
                            <th class="text-center">Priority</th>
                        </tr>
                    </thead>
                    <tbody id="waiting-container">
                        <!-- Rows injected here -->
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <footer class="fiori-page-footer">
        HRWeb Inc. &copy; <?= date('Y') ?> &bull; System Live Queue Display
    </footer>

    <!-- Scripts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString('en-US', { hour12: true, hour: 'numeric', minute: '2-digit' });
            document.getElementById('date').innerText  = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
        }
        setInterval(updateClock, 1000);
        updateClock();

        const servingContainer = document.getElementById('serving-container');
        const waitingContainer = document.getElementById('waiting-container');
        const servingCount     = document.getElementById('serving-count');
        const queueCountLabel  = document.getElementById('queue-count-label');
        const bellSound        = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');

        let previousServingIds = [];
        let isInitialLoad = true;

        function fetchQueueData() {
            fetch("<?= site_url('queue-display/data') ?>")
                .then(res => res.json())
                .then(data => {
                    const newlyServed = isInitialLoad ? [] : data.now_serving.filter(t => !previousServingIds.includes(t.ticket_number));
                    
                    if (newlyServed.length > 0) {
                        bellSound.play().catch(() => {});
                    }

                    renderServing(data.now_serving, newlyServed.map(t => t.ticket_number));
                    renderWaiting(data.waiting);

                    previousServingIds = data.now_serving.map(t => t.ticket_number);
                    isInitialLoad = false;
                })
                .catch(err => console.error('Queue Error:', err));
        }

        function getPriorityClass(p) {
            p = p.toLowerCase();
            if (p === 'urgent') return 'status--negative';
            if (p === 'high') return 'status--warning';
            if (p === 'medium') return 'status--info';
            return 'status--positive';
        }

        function renderServing(tickets, newlyServedIds) {
            servingCount.innerText = `${tickets.length} ACTIVE`;
            if (tickets.length === 0) {
                servingContainer.innerHTML = `<tr><td colspan="4" class="fiori-empty">No active sessions at the moment.</td></tr>`;
                return;
            }

            servingContainer.innerHTML = tickets.map(t => `
                <tr class="${newlyServedIds.includes(t.ticket_number) ? 'is-new' : ''}">
                    <td class="ticket-number-cell">${t.ticket_number}</td>
                    <td>
                        <span class="font-semibold block">${t.client}</span>
                        <span class="text-xs text-slate-400">Requestor: ${t.creator || 'Self'}</span>
                    </td>
                    <td>
                        <span class="font-medium">${t.counter}</span>
                    </td>
                </tr>
            `).join('');
        }

        function renderWaiting(tickets) {
            queueCountLabel.innerText = `${tickets.length} WAITING`;
            if (tickets.length === 0) {
                waitingContainer.innerHTML = `<tr><td colspan="4" class="fiori-empty">Queue is empty.</td></tr>`;
                return;
            }

            waitingContainer.innerHTML = tickets.map(t => `
                <tr>
                    <td class="font-mono font-bold text-slate-500">${t.ticket_number}</td>
                    <td class="font-bold">${t.client}</td>
                    <td>
                        <span class="block font-medium">${t.subject}</span>
                        <span class="text-xs text-slate-400 capitalize">${t.category}</span>
                    </td>
                    <td class="text-center">
                        <span class="status-badge ${getPriorityClass(t.priority)}">${t.priority}</span>
                    </td>
                </tr>
            `).join('');
        }

        setInterval(fetchQueueData, 3000);
        fetchQueueData();
    </script>

</body>
</html>
