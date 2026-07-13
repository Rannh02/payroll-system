<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>User Activity Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 11px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            margin-bottom: 25px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 15px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #4b5563;
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .metadata-table {
            width: 100%;
            margin-top: 10px;
        }
        .metadata-table td {
            padding: 2px 0;
        }
        .metadata-label {
            font-weight: bold;
            color: #4b5563;
            width: 90px;
        }
        .metadata-value {
            color: #1f2937;
        }
        .stats-container {
            margin-bottom: 20px;
            overflow: hidden;
            width: 100%;
        }
        .stat-card {
            float: left;
            width: 30%;
            margin-right: 3%;
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
        }
        .stat-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            border-bottom: 1px solid #d1d5db;
            text-transform: uppercase;
            font-size: 9px;
        }
        .data-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) {
            background-color: #fafafa;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            text-align: center;
        }
        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-default {
            background-color: #f3f4f6;
            color: #374151;
        }
        .footer {
            position: fixed;
            bottom: -15px;
            left: 0;
            right: 0;
            height: 20px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }
        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h1 class="company-name">VIA Architects Associates</h1>
                    <div class="report-title">User Activity Report</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div style="font-size: 9px; color: #6b7280;">Confidential | IT Administration</div>
                </td>
            </tr>
        </table>

        <table class="metadata-table">
            <tr>
                <td class="metadata-label">Date Generated:</td>
                <td class="metadata-value">{{ \Carbon\Carbon::now()->format('F d, Y h:i A') }}</td>
                <td class="metadata-label">Range:</td>
                <td class="metadata-value">
                    {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                </td>
            </tr>
            @if($search)
            <tr>
                <td class="metadata-label">Search Query:</td>
                <td class="metadata-value" colspan="3">"{{ $search }}"</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-label">Total Logins</div>
            <div class="stat-value">{{ number_format($totalLogins) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Active Users</div>
            <div class="stat-value">{{ number_format($uniqueUsers) }}</div>
        </div>
        <div class="stat-card" style="margin-right: 0;">
            <div class="stat-label">Account Unlocks</div>
            <div class="stat-value">{{ number_format($totalUnlocks) }}</div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Timestamp</th>
                <th style="width: 30%;">User / Email</th>
                <th style="width: 15%;">Action</th>
                <th style="width: 13%;">IP Address</th>
                <th style="width: 12%;">Browser</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                        <strong>{{ $log->user ? $log->user->name : 'Unknown User' }}</strong><br>
                        <span style="color: #6b7280; font-size: 10px;">{{ $log->email }}</span>
                    </td>
                    <td>
                        @if($log->status === 'SUCCESS')
                            <span class="badge badge-success">Login Success</span>
                        @elseif($log->status === 'UNLOCKED')
                            <span class="badge badge-info">Unlocked</span>
                        @else
                            <span class="badge badge-default">{{ $log->status }}</span>
                        @endif
                    </td>
                    <td>{{ $log->ip_address }}</td>
                    <td>{{ $log->browser ?? 'Unknown' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #6b7280; padding: 20px;">No user activity events found for this filter criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        VIA Architects Associates - Systems & IT Audit Log &bull; Page <span class="page-number"></span>
    </div>
</body>
</html>
