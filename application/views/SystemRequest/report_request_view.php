<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Request Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --bg-base: #09090b;
            --bg-card: #18181b;
            --bg-card-hover: #1f1f23;
            --bg-elevated: #27272a;
            --border: #27272a;
            --border-subtle: #1e1e22;

            --text-primary: #fafafa;
            --text-secondary: #a1a1aa;
            --text-muted: #71717a;

            --accent-blue: #3b82f6;
            --accent-blue-dim: rgba(59, 130, 246, 0.12);
            --accent-green: #22c55e;
            --accent-green-dim: rgba(34, 197, 94, 0.12);
            --accent-amber: #f59e0b;
            --accent-amber-dim: rgba(245, 158, 11, 0.12);
            --accent-red: #ef4444;
            --accent-red-dim: rgba(239, 68, 68, 0.12);
            --accent-purple: #a855f7;
            --accent-purple-dim: rgba(168, 85, 247, 0.12);
            --accent-cyan: #06b6d4;
            --accent-cyan-dim: rgba(6, 182, 212, 0.12);
            --accent-orange: #f97316;

            --gradient-brand: linear-gradient(135deg, #3b82f6, #8b5cf6);
            --gradient-glow: radial-gradient(ellipse at top, rgba(59,130,246,0.08) 0%, transparent 60%);

            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;

            --shadow-card: 0 1px 3px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.03);
            --shadow-elevated: 0 8px 32px rgba(0,0,0,0.4);

            --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-base: 250ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: var(--bg-base);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 600px;
            background: var(--gradient-glow);
            pointer-events: none;
            z-index: 0;
        }

        .app-wrapper {
            position: relative;
            z-index: 1;
        }

        /* ─── Navbar ─── */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 64px;
            background: rgba(9, 9, 11, 0.8);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid var(--border);
            padding: 0 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            letter-spacing: -0.02em;
        }

        .nav-brand .brand-icon {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-sm);
            background: var(--gradient-brand);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 16px;
        }

        .nav-brand .brand-text {
            color: var(--text-primary);
        }

        .nav-brand .brand-text span {
            color: var(--text-muted);
            font-weight: 400;
        }

        .nav-links {
            display: flex;
            gap: 4px;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 13px;
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            transition: all var(--transition-fast);
            border: 1px solid transparent;
        }

        .nav-links a i {
            font-size: 16px;
        }

        .nav-links a:hover {
            color: var(--text-primary);
            background: var(--bg-elevated);
        }

        .nav-links a.active {
            color: var(--text-primary);
            background: var(--bg-elevated);
            border-color: var(--border);
        }

        /* ─── Main Layout ─── */
        .main-content {
            padding: 88px 40px 60px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 32px;
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.03em;
            background: linear-gradient(135deg, #fafafa 0%, #a1a1aa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-header p {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 4px;
        }

        .btn-export {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--text-primary);
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .btn-export:hover {
            background: var(--bg-elevated);
            border-color: #3f3f46;
        }

        /* ─── KPI Grid ─── */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .kpi-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            position: relative;
            overflow: hidden;
            transition: all var(--transition-base);
        }

        .kpi-card:hover {
            border-color: #3f3f46;
            transform: translateY(-2px);
            box-shadow: var(--shadow-elevated);
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent);
        }

        .kpi-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .kpi-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .kpi-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .kpi-value {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -0.04em;
            line-height: 1;
        }

        .kpi-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ─── Cards ─── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .card-body {
            padding: 24px;
        }

        /* ─── Chart Grid ─── */
        .chart-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 16px;
            margin-bottom: 24px;
        }

        /* ─── Programmer Performance ─── */
        .perf-section {
            margin-bottom: 24px;
        }

        .perf-section .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .perf-section .section-title {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .perf-section .section-badge {
            font-size: 12px;
            font-weight: 600;
            color: var(--accent-green);
            background: var(--accent-green-dim);
            padding: 4px 10px;
            border-radius: 100px;
        }

        .perf-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .perf-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            transition: all var(--transition-base);
            position: relative;
            overflow: hidden;
        }

        .perf-card:hover {
            border-color: #3f3f46;
            transform: translateY(-2px);
            box-shadow: var(--shadow-elevated);
        }

        .perf-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .perf-card:nth-child(1)::after { background: var(--accent-blue); }
        .perf-card:nth-child(2)::after { background: var(--accent-green); }
        .perf-card:nth-child(3)::after { background: var(--accent-purple); }
        .perf-card:nth-child(4)::after { background: var(--accent-cyan); }

        .perf-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
            color: #fff;
            margin-bottom: 16px;
        }

        .perf-card:nth-child(1) .perf-avatar { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .perf-card:nth-child(2) .perf-avatar { background: linear-gradient(135deg, #22c55e, #16a34a); }
        .perf-card:nth-child(3) .perf-avatar { background: linear-gradient(135deg, #a855f7, #9333ea); }
        .perf-card:nth-child(4) .perf-avatar { background: linear-gradient(135deg, #06b6d4, #0891b2); }

        .perf-name {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .perf-role {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .perf-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }

        .perf-stat {
            background: var(--bg-base);
            border-radius: var(--radius-sm);
            padding: 12px;
        }

        .perf-stat-label {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .perf-stat-value {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .perf-progress {
            margin-top: 4px;
        }

        .perf-progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .perf-progress-label {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .perf-progress-value {
            font-size: 12px;
            font-weight: 700;
        }

        .progress-track {
            width: 100%;
            height: 6px;
            background: var(--bg-base);
            border-radius: 100px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 100px;
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .perf-card:nth-child(1) .progress-fill { background: var(--accent-blue); }
        .perf-card:nth-child(2) .progress-fill { background: var(--accent-green); }
        .perf-card:nth-child(3) .progress-fill { background: var(--accent-purple); }
        .perf-card:nth-child(4) .progress-fill { background: var(--accent-cyan); }

        /* Stacked Progress Bar */
        .stacked-track {
            width: 100%;
            height: 10px;
            background: var(--bg-base);
            border-radius: 100px;
            overflow: hidden;
            display: flex;
            margin-bottom: 14px;
        }

        .stacked-fill {
            height: 100%;
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stacked-fill-done { background: var(--accent-green); }
        .stacked-fill-proses { background: var(--accent-blue); }
        .stacked-fill-pending { background: var(--accent-amber); }

        /* Mini stat rows */
        .mini-stat-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .mini-stat-row:last-child {
            margin-bottom: 0;
        }

        .mini-stat-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .mini-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .mini-stat-label {
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .mini-stat-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .mini-stat-num {
            font-size: 14px;
            font-weight: 700;
            min-width: 20px;
            text-align: right;
        }

        .mini-bar-track {
            width: 80px;
            height: 5px;
            background: var(--bg-base);
            border-radius: 100px;
            overflow: hidden;
        }

        .mini-bar-fill {
            height: 100%;
            border-radius: 100px;
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ─── Table ─── */
        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: var(--bg-base);
            text-align: left;
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        tbody td {
            padding: 14px 16px;
            font-size: 13px;
            border-bottom: 1px solid var(--border-subtle);
            vertical-align: middle;
        }

        tbody tr {
            transition: background var(--transition-fast);
        }

        tbody tr:hover {
            background: rgba(255,255,255,0.02);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        /* ─── Badges ─── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-pending {
            background: var(--accent-amber-dim);
            color: var(--accent-amber);
        }

        .badge-proses {
            background: var(--accent-blue-dim);
            color: var(--accent-blue);
        }

        .badge-selesai {
            background: var(--accent-green-dim);
            color: var(--accent-green);
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* ─── Action Buttons ─── */
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all var(--transition-fast);
            cursor: pointer;
            border: none;
            white-space: nowrap;
        }

        .btn-action i {
            font-size: 14px;
        }

        .btn-action-proses {
            background: var(--accent-blue-dim);
            color: var(--accent-blue);
            border: 1px solid rgba(59,130,246,0.2);
        }

        .btn-action-proses:hover {
            background: var(--accent-blue);
            color: #fff;
        }

        .btn-action-selesai {
            background: var(--accent-green-dim);
            color: var(--accent-green);
            border: 1px solid rgba(34,197,94,0.2);
        }

        .btn-action-selesai:hover {
            background: var(--accent-green);
            color: #fff;
        }

        .btn-action-cetak {
            background: var(--bg-elevated);
            color: var(--text-secondary);
            border: 1px solid var(--border);
        }

        .btn-action-cetak:hover {
            background: #3f3f46;
            color: var(--text-primary);
        }

        .btn-done {
            color: var(--text-muted);
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* ─── Empty State ─── */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 48px;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        .empty-state p {
            color: var(--text-muted);
            font-size: 14px;
        }

        /* ─── Trend Grid ─── */
        .trend-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
        }

        /* ─── Responsive ─── */
        @media (max-width: 1200px) {
            .perf-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 900px) {
            .main-content {
                padding: 88px 20px 40px;
            }

            .navbar {
                padding: 0 20px;
            }

            .kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .chart-grid,
            .trend-grid {
                grid-template-columns: 1fr;
            }

            .perf-grid {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
        }

        @media (max-width: 600px) {
            .kpi-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ─── Animations ─── */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeInUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
        }

        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.1s; }
        .delay-3 { animation-delay: 0.15s; }
        .delay-4 { animation-delay: 0.2s; }
        .delay-5 { animation-delay: 0.25s; }
        .delay-6 { animation-delay: 0.3s; }

        /* ─── Scrollbar ─── */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--bg-elevated);
            border-radius: 100px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #3f3f46;
        }
    </style>
</head>

<body>
    <div class="app-wrapper">

        <nav class="navbar">
            <a href="<?= site_url('systemrequest') ?>" class="nav-brand">
                <div class="brand-icon"><i class='bx bxs-zap'></i></div>
                <div class="brand-text">IT Request <span>Hub</span></div>
            </a>
            <div class="nav-links">
                <a href="<?= site_url('systemrequest') ?>">
                    <i class='bx bx-edit-alt'></i> Form Request
                </a>
                <a href="<?= site_url('systemrequest/report') ?>" class="active">
                    <i class='bx bx-bar-chart-square'></i> Dashboard
                </a>
            </div>
        </nav>

        <main class="main-content">

            <div class="page-header animate-in">
                <div>
                    <h1>Dashboard</h1>
                    <p>Monitor performance & status IT request secara real-time</p>
                </div>
                <button class="btn-export" onclick="window.print()">
                    <i class='bx bx-printer'></i> Export Laporan
                </button>
            </div>

            <!-- KPI Cards -->
            <div class="kpi-grid">
                <div class="kpi-card animate-in delay-1">
                    <div class="kpi-top">
                        <span class="kpi-label">Total Request</span>
                        <div class="kpi-icon" style="background: var(--bg-elevated); color: var(--text-secondary);">
                            <i class='bx bx-layer'></i>
                        </div>
                    </div>
                    <div class="kpi-value"><?= isset($total) ? $total : 0 ?></div>
                    <div class="kpi-sub"><i class='bx bx-infinite'></i> Semua time</div>
                </div>
                <div class="kpi-card animate-in delay-2">
                    <div class="kpi-top">
                        <span class="kpi-label">Pending</span>
                        <div class="kpi-icon" style="background: var(--accent-amber-dim); color: var(--accent-amber);">
                            <i class='bx bx-hourglass'></i>
                        </div>
                    </div>
                    <div class="kpi-value" style="color: var(--accent-amber);"><?= isset($pending) ? $pending : 0 ?></div>
                    <div class="kpi-sub"><i class='bx bx-time-five'></i> Menunggu penanganan</div>
                </div>
                <div class="kpi-card animate-in delay-3">
                    <div class="kpi-top">
                        <span class="kpi-label">In Progress</span>
                        <div class="kpi-icon" style="background: var(--accent-blue-dim); color: var(--accent-blue);">
                            <i class='bx bx-code-alt'></i>
                        </div>
                    </div>
                    <div class="kpi-value" style="color: var(--accent-blue);"><?= isset($proses) ? $proses : 0 ?></div>
                    <div class="kpi-sub"><i class='bx bx-loader-circle'></i> Sedang dikerjakan</div>
                </div>
                <div class="kpi-card animate-in delay-4">
                    <div class="kpi-top">
                        <span class="kpi-label">Selesai</span>
                        <div class="kpi-icon" style="background: var(--accent-green-dim); color: var(--accent-green);">
                            <i class='bx bx-check-shield'></i>
                        </div>
                    </div>
                    <div class="kpi-value" style="color: var(--accent-green);"><?= isset($selesai) ? $selesai : 0 ?></div>
                    <div class="kpi-sub"><i class='bx bx-check-double'></i> Completed</div>
                </div>
            </div>

            <!-- Charts -->
            <div class="chart-grid">
                <div class="card animate-in delay-3">
                    <div class="card-header">
                        <span class="card-title">Status Distribution</span>
                    </div>
                    <div class="card-body" style="display:flex; align-items:center; justify-content:center; height: 280px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
                <div class="card animate-in delay-4">
                    <div class="card-header">
                        <span class="card-title">Request per Departemen</span>
                    </div>
                    <div class="card-body" style="height: 280px;">
                        <canvas id="deptChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Programmer Performance Section -->
            <?php if (!empty($programmer_stats)): ?>
            <div class="perf-section animate-in delay-5">
                <div class="section-header">
                    <div class="section-title">Programmer Performance</div>
                    <div class="section-badge"><i class='bx bx-pulse'></i>&nbsp; Live</div>
                </div>
                <div class="perf-grid">
                    <?php
                    $avatar_colors = ['blue', 'green', 'purple', 'cyan'];
                    $color_idx = 0;
                    foreach ($programmer_stats as $ps):
                        $total = $ps->total_assigned > 0 ? $ps->total_assigned : 1;
                        $pct_done = round(($ps->total_selesai / $total) * 100);
                        $pct_proses = round(($ps->total_proses / $total) * 100);
                        $pct_pending = 100 - $pct_done - $pct_proses;
                        $initials = '';
                        $name_parts = explode(' ', trim($ps->nama_programmer));
                        foreach ($name_parts as $np) {
                            $initials .= strtoupper(substr($np, 0, 1));
                            if (strlen($initials) >= 2) break;
                        }
                    ?>
                    <div class="perf-card">
                        <div class="perf-avatar"><?= $initials ?></div>
                        <div class="perf-name"><?= htmlspecialchars($ps->nama_programmer) ?></div>
                        <div class="perf-role">IT Developer</div>

                        <div class="perf-stats">
                            <div class="perf-stat">
                                <div class="perf-stat-label">Total</div>
                                <div class="perf-stat-value"><?= $ps->total_assigned ?></div>
                            </div>
                            <div class="perf-stat">
                                <div class="perf-stat-label">Done</div>
                                <div class="perf-stat-value" style="color: var(--accent-green);"><?= $ps->total_selesai ?></div>
                            </div>
                            <div class="perf-stat">
                                <div class="perf-stat-label">In Progress</div>
                                <div class="perf-stat-value" style="color: var(--accent-blue);"><?= $ps->total_proses ?></div>
                            </div>
                            <div class="perf-stat">
                                <div class="perf-stat-label">Pending</div>
                                <div class="perf-stat-value" style="color: var(--accent-amber);"><?= $ps->total_pending ?></div>
                            </div>
                        </div>

                        <!-- Stacked Bar -->
                        <div style="margin-bottom: 14px;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                                <span style="font-size:11px; color:var(--text-muted); font-weight:500;">Status Breakdown</span>
                            </div>
                            <div class="stacked-track">
                                <?php if ($pct_done > 0): ?>
                                <div class="stacked-fill stacked-fill-done" style="width: <?= $pct_done ?>%" title="Selesai: <?= $ps->total_selesai ?>"></div>
                                <?php endif; ?>
                                <?php if ($pct_proses > 0): ?>
                                <div class="stacked-fill stacked-fill-proses" style="width: <?= $pct_proses ?>%" title="In Progress: <?= $ps->total_proses ?>"></div>
                                <?php endif; ?>
                                <?php if ($pct_pending > 0): ?>
                                <div class="stacked-fill stacked-fill-pending" style="width: <?= $pct_pending ?>%" title="Pending: <?= $ps->total_pending ?>"></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Mini Status Rows -->
                        <div>
                            <div class="mini-stat-row">
                                <div class="mini-stat-left">
                                    <div class="mini-dot" style="background:var(--accent-green);"></div>
                                    <span class="mini-stat-label">Selesai</span>
                                </div>
                                <div class="mini-stat-right">
                                    <span class="mini-stat-num" style="color:var(--accent-green);"><?= $ps->total_selesai ?></span>
                                    <div class="mini-bar-track">
                                        <div class="mini-bar-fill" style="width:<?= $pct_done ?>%; background:var(--accent-green);"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mini-stat-row">
                                <div class="mini-stat-left">
                                    <div class="mini-dot" style="background:var(--accent-blue);"></div>
                                    <span class="mini-stat-label">In Progress</span>
                                </div>
                                <div class="mini-stat-right">
                                    <span class="mini-stat-num" style="color:var(--accent-blue);"><?= $ps->total_proses ?></span>
                                    <div class="mini-bar-track">
                                        <div class="mini-bar-fill" style="width:<?= $pct_proses ?>%; background:var(--accent-blue);"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mini-stat-row">
                                <div class="mini-stat-left">
                                    <div class="mini-dot" style="background:var(--accent-amber);"></div>
                                    <span class="mini-stat-label">Pending</span>
                                </div>
                                <div class="mini-stat-right">
                                    <span class="mini-stat-num" style="color:var(--accent-amber);"><?= $ps->total_pending ?></span>
                                    <div class="mini-bar-track">
                                        <div class="mini-bar-fill" style="width:<?= $pct_pending ?>%; background:var(--accent-amber);"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Trend Chart -->
            <?php if (!empty($monthly_trend)): ?>
            <div class="trend-grid animate-in delay-5">
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Monthly Trend</span>
                    </div>
                    <div class="card-body" style="height: 240px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Performance Leaderboard</span>
                    </div>
                    <div class="card-body" style="height: 240px;">
                        <canvas id="perfChart"></canvas>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Data Table -->
            <div class="card animate-in delay-6">
                <div class="card-header">
                    <span class="card-title">Log Permintaan</span>
                    <span style="font-size: 12px; color: var(--text-muted);"><?= count($requests) ?> records</span>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Departemen</th>
                                <th>Masalah</th>
                                <th>Solusi</th>
                                <th>PIC / Programmer</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($requests)): foreach ($requests as $row): ?>
                                <tr>
                                    <td style="white-space:nowrap; color: var(--text-secondary);">
                                        <?= date('d M Y', strtotime($row->tanggal_permintaan)) ?>
                                    </td>
                                    <td>
                                        <strong style="color: var(--text-primary);"><?= htmlspecialchars($row->departemen_peminta) ?></strong><br>
                                        <small style="color: var(--text-muted);"><?= htmlspecialchars($row->nama_peminta) ?></small>
                                    </td>
                                    <td style="max-width:240px; color: var(--text-secondary);">
                                        <?= strlen($row->masalah) > 55 ? substr($row->masalah, 0, 55) . '...' : htmlspecialchars($row->masalah) ?>
                                    </td>
                                    <td style="max-width:240px; color: var(--text-secondary);">
                                        <?= strlen($row->solusi) > 55 ? substr($row->solusi, 0, 55) . '...' : htmlspecialchars($row->solusi) ?>
                                    </td>
                                    <td style="max-width:200px; color: var(--text-secondary);">
                                        <?= strlen(trim($row->ditangani_oleh)) > 50 ? substr(trim($row->ditangani_oleh), 0, 50) . '...' : htmlspecialchars(trim($row->ditangani_oleh)) ?>
                                    </td>
                                    <td>
                                        <?php if ($row->status == 'Pending'): ?>
                                            <span class="badge badge-pending"><span class="badge-dot"></span> Pending</span>
                                        <?php elseif ($row->status == 'Proses'): ?>
                                            <span class="badge badge-proses"><span class="badge-dot"></span> Proses</span>
                                        <?php else: ?>
                                            <span class="badge badge-selesai"><span class="badge-dot"></span> Selesai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="white-space:nowrap;">
                                        <?php if ($row->status == 'Pending'): ?>
                                            <a href="javascript:void(0)" class="btn-action btn-action-proses btn-pilih-it" data-id="<?= $row->id ?>">
                                                <i class='bx bx-play'></i> Kerjakan
                                            </a>
                                        <?php elseif ($row->status == 'Proses'): ?>
                                            <a href="<?= site_url('systemrequest/update_status/' . $row->id . '/Selesai') ?>" class="btn-action btn-action-selesai">
                                                <i class='bx bx-check'></i> Selesai
                                            </a>
                                        <?php else: ?>
                                            <span class="btn-done"><i class='bx bx-check-double'></i> Done</span>
                                        <?php endif; ?>

                                        <a href="<?= site_url('systemrequest/cetak_laporan/' . $row->id) ?>" class="btn-action btn-action-cetak" target="_blank">
                                            <i class='bx bx-printer'></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <i class='bx bx-inbox'></i>
                                            <p>Belum ada data permintaan.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <script>
        // ─── Status Doughnut Chart ───
        const statusData = [
            <?= isset($pending) ? $pending : 0 ?>,
            <?= isset($proses) ? $proses : 0 ?>,
            <?= isset($selesai) ? $selesai : 0 ?>
        ];

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'In Progress', 'Selesai'],
                datasets: [{
                    data: statusData,
                    backgroundColor: ['#f59e0b', '#3b82f6', '#22c55e'],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#a1a1aa',
                            padding: 16,
                            usePointStyle: true,
                            pointStyleWidth: 8,
                            font: { family: 'Inter', size: 12, weight: '500' }
                        }
                    }
                }
            }
        });

        // ─── Department Bar Chart ───
        <?php
        $dept_labels = array();
        $dept_counts = array();
        if (!empty($dept_stats)) {
            foreach ($dept_stats as $ds) {
                $dept_labels[] = $ds->departemen_peminta ? $ds->departemen_peminta : 'Lainnya';
                $dept_counts[] = $ds->jumlah;
            }
        }
        ?>
        const deptLabels = <?= !empty($dept_labels) ? json_encode($dept_labels) : '[]' ?>;
        const deptCounts = <?= !empty($dept_counts) ? json_encode($dept_counts) : '[]' ?>;

        new Chart(document.getElementById('deptChart'), {
            type: 'bar',
            data: {
                labels: deptLabels,
                datasets: [{
                    label: 'Total Request',
                    data: deptCounts,
                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                    hoverBackgroundColor: 'rgba(59, 130, 246, 0.9)',
                    borderRadius: 6,
                    borderSkipped: false,
                    barThickness: 28
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#71717a',
                            font: { family: 'Inter', size: 11 }
                        },
                        grid: { color: 'rgba(255,255,255,0.04)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#71717a',
                            font: { family: 'Inter', size: 11 },
                            maxRotation: 45
                        }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // ─── Monthly Trend Chart ───
        <?php if (!empty($monthly_trend)): ?>
        <?php
        $trend_labels = array();
        $trend_counts = array();
        foreach ($monthly_trend as $mt) {
            $trend_labels[] = $mt->bulan;
            $trend_counts[] = $mt->jumlah;
        }
        ?>
        const trendLabels = <?= json_encode($trend_labels) ?>;
        const trendCounts = <?= json_encode($trend_counts) ?>;

        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Request',
                    data: trendCounts,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.08)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#18181b',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#71717a',
                            font: { family: 'Inter', size: 11 }
                        },
                        grid: { color: 'rgba(255,255,255,0.04)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#71717a',
                            font: { family: 'Inter', size: 11 }
                        }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // ─── Performance Leaderboard Chart ───
        <?php
        $perf_names = array();
        $perf_completed = array();
        $perf_active = array();
        foreach ($programmer_stats as $ps) {
            $perf_names[] = $ps->nama_programmer;
            $perf_completed[] = $ps->total_selesai;
            $perf_active[] = $ps->total_proses;
        }
        ?>
        new Chart(document.getElementById('perfChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($perf_names) ?>,
                datasets: [
                    {
                        label: 'Selesai',
                        data: <?= json_encode($perf_completed) ?>,
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                        borderRadius: 4,
                        barThickness: 18
                    },
                    {
                        label: 'In Progress',
                        data: <?= json_encode($perf_active) ?>,
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderRadius: 4,
                        barThickness: 18
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#71717a',
                            font: { family: 'Inter', size: 11 }
                        },
                        grid: { color: 'rgba(255,255,255,0.04)' }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            color: '#a1a1aa',
                            font: { family: 'Inter', size: 11, weight: '600' }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            color: '#a1a1aa',
                            usePointStyle: true,
                            pointStyleWidth: 8,
                            padding: 12,
                            font: { family: 'Inter', size: 11, weight: '500' }
                        }
                    }
                }
            }
        });
        <?php endif; ?>

        // ─── SweetAlert: Pilih IT ───
        $(document).ready(function() {
            $('.btn-pilih-it').click(function(e) {
                e.preventDefault();
                var requestId = $(this).data('id');

                $.ajax({
                    url: '<?= site_url("systemrequest/get_employeesItSupport") ?>',
                    type: 'GET',
                    dataType: 'json',
                    data: { searchTerm: '' },
                    success: function(employees) {
                        var options = {};
                        $.each(employees, function(index, emp) {
                            options[emp.id] = emp.text;
                        });

                        Swal.fire({
                            title: 'Pilih IT / Petugas',
                            input: 'select',
                            inputOptions: options,
                            inputPlaceholder: '-- Pilih Personel --',
                            showCancelButton: true,
                            confirmButtonText: 'Proses Sekarang',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#3b82f6',
                            didOpen: function() {
                                const selectEl = Swal.getInput();
                                $(selectEl).change(function() {
                                    var selectedId = $(this).val();
                                    if (selectedId) {
                                        $.ajax({
                                            url: '<?= site_url("systemrequest/get_ProgrammerKerja") ?>',
                                            type: 'GET',
                                            data: { ditangani_oleh: selectedId },
                                            dataType: 'json',
                                            success: function(response) {
                                                if (response && response.sedang_kerja) {
                                                    Swal.showValidationMessage('Programmer ini sedang mengerjakan project lain!');
                                                    Swal.getConfirmButton().setAttribute('disabled', 'disabled');
                                                } else {
                                                    Swal.resetValidationMessage();
                                                    Swal.getConfirmButton().removeAttribute('disabled');
                                                }
                                            }
                                        });
                                    } else {
                                        Swal.getConfirmButton().removeAttribute('disabled');
                                    }
                                });
                            },
                            inputValidator: function(value) {
                                return new Promise(function(resolve) {
                                    if (value !== '') {
                                        resolve();
                                    } else {
                                        resolve('Pilih personel terlebih dahulu!');
                                    }
                                });
                            }
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                var selectedEmployeeId = result.value;
                                var selectedEmployeeName = options[selectedEmployeeId];
                                window.location.href = '<?= site_url("systemrequest/update_status/") ?>' + requestId + '/Proses?it_id=' + selectedEmployeeId + '&it_nama=' + encodeURIComponent(selectedEmployeeName);
                            }
                        });
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal mengambil data karyawan.', 'error');
                    }
                });
            });
        });
    </script>
</body>

</html>
