<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Security Incident Report</title>
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
            border-bottom: 2px solid #dc2626;
            padding-bottom: 15px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #7f1d1d;
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
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
        }
        .stat-label {
            font-size: 9px;
            color: #991b1b;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #7f1d1d;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #fee2e2;
            color: #7f1d1d;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            border-bottom: 1px solid #fca5a5;
            text-transform: uppercase;
            font-size: 9px;
        }
        .data-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #fca5a5;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) {
            background-color: #fdf2f2;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            text-align: center;
        }
        .badge-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fee2e2;
        }
        .badge-warning {
            background-color: #fffbeb;
            color: #92400e;
            border: 1px solid #fef3c7;
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
                    <div class="report-title">Security Incident Report</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div style="font-size: 9px; color: #b91c1c; font-weight: bold;">CRITICAL | Security Administration</div>
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
            <div class="stat-label">Total Incidents</div>
            <div class="stat-value">{{ number_format($totalIncidents) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Failed Logins</div>
            <div class="stat-value">{{ number_format($bruteForceAttempts) }}</div>
        </div>
        <div class="stat-card" style="margin-right: 0;">
            <div class="stat-label">Account Lockouts</div>
            <div class="stat-value">{{ number_format($accountLockouts) }}</div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Timestamp</th>
                <th style="width: 30%;">Email Attempted</th>
                <th style="width: 15%;">Incident Type</th>
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
                        <strong>{{ $log->email }}</strong><br>
                        @if($log->user)
                            <span style="color: #4b5563; font-size: 10px;">User: {{ $log->user->name }}</span>
                        @else
                            <span style="color: #dc2626; font-size: 10px;">Non-existent Account</span>
                        @endif
                    </td>
                    <td>
                        @if($log->status === 'FAILED')
                            <span class="badge badge-danger">Login Failed</span>
                        @elseif($log->status === 'LOCKED')
                            <span class="badge badge-warning">Account Locked</span>
                        @else
                            <span class="badge badge-default">{{ $log->status }}</span>
                        @endif
                    </td>
                    <td>{{ $log->ip_address }}</td>
                    <td>{{ $log->browser ?? 'Unknown' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #6b7280; padding: 20px;">No security incidents detected for this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        VIA Architects Associates - Systems Security Audit &bull; Page <span class="page-number"></span>
    </div>
</body>
</html>
