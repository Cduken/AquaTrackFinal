<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reports Export - AQUATRACK</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            margin: 0;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #2c5282;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            color: #2c5282;
            font-size: 18px;
        }

        .header .subtitle {
            color: #666;
            font-size: 12px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 15px;
        }

        .stat-card {
            border: 1px solid #e2e8f0;
            padding: 8px;
            border-radius: 4px;
            text-align: center;
        }

        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #2c5282;
        }

        .stat-label {
            font-size: 9px;
            color: #718096;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8px;
        }

        th {
            background-color: #2c5282;
            color: white;
            padding: 6px 4px;
            text-align: left;
            font-weight: bold;
        }

        td {
            padding: 4px;
            border: 1px solid #e2e8f0;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #718096;
            font-size: 8px;
        }

        .status-pending { color: #d69e2e; }
        .status-in_progress { color: #3182ce; }
        .status-resolved { color: #38a169; }
        .status-deleted { color: #e53e3e; }

        .priority-high { color: #e53e3e; }
        .priority-medium { color: #d69e2e; }
        .priority-low { color: #38a169; }

        .filters-section {
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 12px;
            font-size: 9px;
        }

        .filters-section h3 {
            margin: 0 0 4px 0;
            color: #2c5282;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>AQUATRACK - REPORTS EXPORT</h1>
        <div class="subtitle">Generated on: {{ $exportDate }}</div>
    </div>

    <!-- Statistics Section -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $totalReports }}</div>
            <div class="stat-label">Total Reports</div>
        </div>
        <div class="stat-card">
            <div class="stat-value status-pending">{{ $pendingCount }}</div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-value status-in_progress">{{ $inProgressCount }}</div>
            <div class="stat-label">In Progress</div>
        </div>
        <div class="stat-card">
            <div class="stat-value status-resolved">{{ $resolvedCount }}</div>
            <div class="stat-label">Resolved</div>
        </div>
    </div>

    <!-- Filters Section -->
    @if($filters['search'] || $filters['status'] || $filters['userType'])
    <div class="filters-section">
        <h3>Applied Filters:</h3>
        @if($filters['search'])
        <div><strong>Search:</strong> {{ $filters['search'] }}</div>
        @endif
        @if($filters['status'])
        <div><strong>Status:</strong> {{ ucfirst($filters['status']) }}</div>
        @endif
        @if($filters['userType'] && $filters['userType'] !== 'all')
        <div><strong>User Type:</strong> {{ $filters['userType'] === 'authenticated' ? 'Registered' : ucfirst($filters['userType']) }}</div>
        @endif
    </div>
    @endif

    <!-- Reports Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tracking Code</th>
                <th>Reporter</th>
                <th>User Type</th>
                <th>Location</th>
                <th>Issue Type</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Date Created</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
            <tr>
                <td>{{ $report->id }}</td>
                <td>{{ $report->tracking_code }}</td>
                <td>{{ $report->reporter_name ?: ($report->user->name ?? 'N/A') }}</td>
                <td>
                    @if($report->formatted_user_types)
                        {{ $report->formatted_user_types }}
                    @else
                        {{ $report->user_id ? 'Registered' : 'Guest' }}
                    @endif
                </td>
                <td>
                    {{ $report->zone ?: 'N/A' }}<br>
                    {{ $report->barangay ?: 'N/A' }}, {{ $report->purok ?: 'N/A' }}
                </td>
                <td>
                    {{ $report->water_issue_type === 'others' ? ($report->custom_water_issue ?: 'Custom Issue') : $report->water_issue_type }}
                </td>
                <td class="priority-{{ $report->priority }}">
                    {{ ucfirst($report->priority ?: 'N/A') }}
                </td>
                <td class="status-{{ $report->status }}">
                    @if(str_starts_with($report->status, 'Deleted'))
                        Deleted
                    @else
                        {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                    @endif
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($report->created_at)->format('M d, Y') }}<br>
                    {{ \Carbon\Carbon::parse($report->created_at)->format('g:i A') }}
                </td>
                <td>{{ Str::limit($report->description, 50) ?: 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated by AQUATRACK System | {{ $exportDate }}
    </div>
</body>
</html>
