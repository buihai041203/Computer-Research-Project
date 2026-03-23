@extends('layouts.panel')

@section('content')
<style>
/* ===== GIỮ NGUYÊN HỆ THỐNG BIẾN CỦA BẠN ===== */
:root {
    --cyan: #22d3ee;
    --green: #4ade80;
    --red: #f87171;
    --text-primary: #e2e8f0;
    --text-secondary: #64748b;
    --text-muted: #334155;
    --border-faint: rgba(148,163,184,.08);
    --font-ui: 'DM Sans', sans-serif;
    --font-mono: 'Space Mono', monospace;
}

/* ===== GIỮ NGUYÊN PHÔNG CHỮ & TIÊU ĐỀ ===== */
h1, table, td, th {
    font-family: var(--font-ui) !important;
}

.page-title {
    font-size: 1.4rem;
    font-weight: 600;
    letter-spacing: -0.025em;
    font-family: var(--font-ui);
    margin-bottom: 20px;
    color: var(--text-primary);
}

.page-title em {
    font-style: normal;
    color: var(--cyan);
    font-weight: 600;
}

/* ===== THIẾT KẾ BẢNG KIỂU EXCEL CHUYÊN NGHIỆP ===== */
.bg-white {
    background: #0a1220 !important;
    border: 1px solid var(--border-faint) !important;
    border-radius: 8px !important; /* Bo nhẹ kiểu phần mềm */
    overflow: hidden;
    padding: 0 !important; /* Để bảng tràn viền card */
}

table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
}

/* Header phẳng kiểu Excel */
thead th {
    font-family: var(--font-mono) !important;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #CCFFFF;
    padding: 12px 15px;
    background: rgba(255, 255, 255, 0.02);
    border-bottom: 2px solid var(--border-faint);
    border-right: 1px solid var(--border-faint); /* Đường kẻ dọc */
    text-align: left;
}

/* Rows & Zebra Striping (Dòng kẻ sọc) */
tbody tr {
    border-bottom: 1px solid var(--border-faint);
    transition: .15s;
}

tbody tr:nth-child(even) {
    background: rgba(255, 255, 255, 0.01); /* Dòng chẵn hơi tối hơn */
}

tbody tr:hover {
    background: rgba(34,211,238,.04) !important;
}

/* Cells */
td {
    padding: 12px 15px !important;
    font-size: 13px;
    color: var(--text-primary);
    border-right: 1px solid var(--border-faint); /* Đường kẻ dọc mờ */
}

/* Cột IP + Time giữ đúng phông Mono của bạn */
td:first-child,
td:last-child {
    font-family: var(--font-mono) !important;
    font-size: 11px;
    color: var(--cyan);
}

/* Type color */
.status-tag {
    font-weight: 700;
    font-size: 10px;
    text-transform: uppercase;
}

.text-red-500 { color: var(--red) !important; }
.text-green-500 { color: var(--green) !important; }

/* Description (User Agent) */
td:nth-child(5) {
    color: var(--text-secondary);
    font-size: 12px;
}

/* Bỏ đường kẻ dọc ở cột cuối cùng */
td:last-child, th:last-child {
    border-right: none;
}
/* html, body {
    margin: 0 !important;
    padding: 0 !important;
    background: var(--surface-0) !important;
    background-image: radial-gradient(ellipse 70% 40% at 50% 0%, rgba(34,211,238,.05) 0%, transparent 55%) !important;
    color: var(--text-primary);
    font-family: var(--font-ui);
} */

.scc-wrap {
    max-width: 1440px;
    margin: 0 auto;
    padding: 24px;
    padding-top: 0 !important; /* Xóa khoảng trắng trên cùng */
}
</style>
<div class="scc-wrap">
<h1 class="page-title">
    System <em>Logs</em>
</h1>

<div class="bg-white rounded shadow">
    <table>
        <thead>
            <tr>
                <th>IP Address</th>
                <th>Country</th>
                <th>Type</th>
                <th>Threat</th>
                <th>User Agent</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->ip }}</td>
                <td>{{ $log->country }}</td>
                <td>
                    @if($log->type == 'bot')
                        <span class="status-tag text-red-500">Bot</span>
                    @else
                        <span class="status-tag text-green-500">Human</span>
                    @endif
                </td>
                <td>
                    <span style="font-weight: 500;">{{ $log->threat ?? 'LOW' }}</span>
                </td>
                <td>{{ $log->user_agent }}</td>
                <td>{{ $log->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>

@endsection