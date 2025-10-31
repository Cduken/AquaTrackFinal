<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Records Export - AQUATRACK</title>
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

        .section {
            margin-bottom: 15px;
        }

        .section-title {
            background-color: #f8f9fa;
            padding: 6px 10px;
            font-weight: bold;
            border-left: 4px solid #2c5282;
            margin-bottom: 8px;
            font-size: 11px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
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

        .revenue-highlight {
            background-color: #e6fffa;
            border: 1px solid #81e6d9;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 12px;
            text-align: center;
        }

        .text-success {
            color: #38a169;
        }

        .text-danger {
            color: #e53e3e;
        }

        .text-warning {
            color: #d69e2e;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>AQUATRACK - RECORDS REPORT</h1>
        <div class="subtitle">Period: {{ $periodTitle }}</div>
        <div class="subtitle">Generated on: {{ $exportDate }}</div>
    </div>

    <!-- Revenue Summary Section -->
    <div class="section">
        <div class="section-title">REVENUE SUMMARY</div>

        <div class="revenue-highlight">
            <strong>Total Revenue from Paid Bills: </strong>
            <span class="text-success">{{ $revenueStats['Total Revenue'] }}</span>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $revenueStats['Total Records'] }}</div>
                <div class="stat-label">Total Records</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-success">{{ $revenueStats['Paid Records'] }}</div>
                <div class="stat-label">Paid Records</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-warning">{{ $revenueStats['Pending Records'] }}</div>
                <div class="stat-label">Pending Records</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-danger">{{ $revenueStats['Overdue Records'] }}</div>
                <div class="stat-label">Overdue Records</div>
            </div>
        </div>
    </div>

    <!-- Records Data Section -->
    <div class="section">
        <div class="section-title">RECORDS DATA ({{ $totalRecords }} records)</div>

        <table>
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Account Number</th>
                    <th>Serial Number</th>
                    <th>Reading</th>
                    <th>Consumption</th>
                    <th>Amount</th>
                    <th>Surcharge</th>
                    <th>Total Amount</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Reading Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $record)
                    @php
                        // Calculate surcharge using Philippine time
                        $surcharge = null;
                        $totalAmount = $record->amount;

                        if ($record->status !== 'Paid' && $record->due_date) {
                            $dueDate = \Carbon\Carbon::parse($record->due_date);
                            $today = \Carbon\Carbon::now('Asia/Manila'); // Philippine time

                            if ($today->gt($dueDate)) {
                                $surcharge = round($record->amount * 0.1, 2);
                                $totalAmount = $record->amount + $surcharge;
                            }
                        }
                    @endphp
                    <tr>
                        <td>{{ $record->user->name }} {{ $record->user->lastname }}</td>
                        <td>{{ $record->user->account_number ?? 'N/A' }}</td>
                        <td>{{ $record->user->serial_number ?? 'N/A' }}</td>
                        <td>{{ $record->reading }} m³</td>
                        <td>{{ $record->consumption }} m³</td>
                        <td>₱{{ number_format($record->amount, 2) }}</td>
                        <td>
                            @if ($surcharge)
                                ₱{{ number_format($surcharge, 2) }}
                            @else
                                None
                            @endif
                        </td>
                        <td>₱{{ number_format($totalAmount, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($record->due_date)->format('M d, Y') }}</td>
                        <td>
                            @if ($record->status === 'Paid')
                                <span class="text-success">{{ $record->status }}</span>
                            @elseif($record->status === 'Pending')
                                <span class="text-warning">{{ $record->status }}</span>
                            @else
                                <span class="text-danger">{{ $record->status }}</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($record->reading_date)->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Generated by AQUATRACK System | {{ $exportDate }}
    </div>
</body>

</html>
