<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            margin: 10px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #2c3e50;
            font-size: 18px;
            margin: 0 0 10px 0;
            font-weight: bold;
        }
        .header .period {
            font-size: 14px;
            color: #7f8c8d;
            margin: 5px 0;
        }
        .meta {
            text-align: center;
            margin-bottom: 15px;
            font-size: 10px;
            color: #7f8c8d;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #bdc3c7;
            padding: 4px 2px;
            text-align: center;
            word-wrap: break-word;
            vertical-align: middle;
            overflow: hidden;
            white-space: nowrap;
        }
        th {
            background-color: #34495e;
            color: white;
            font-weight: bold;
            font-size: 7px;
            text-transform: uppercase;
            padding: 3px 1px;
        }
        .emp-code {
            width: 10%;
            text-align: center;
            font-weight: bold;
            font-size: 8px;
        }
        .emp-name {
            width: 10%;
            text-align: left;
            font-weight: bold;
            padding-left: 6px;
            font-size: 8px;
            max-width: 14%;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .department {
            width: 10%;
            text-align: center;
            font-size: 8px;
        }
        .site {
            width: 10%;
            text-align: center;
            font-size: 8px;
        }
        .date-cell {
            width: 1.8%;
            text-align: center;
            font-size: 8px;
            font-weight: bold;
            padding: 4px 1px;
            min-width: 16px;
        }
        .status-p {
            background-color: #27ae60;
            color: white;
            font-weight: bold;
        }
        .status-a {
            background-color: #e74c3c;
            color: white;
            font-weight: bold;
        }
        .legend {
            margin-top: 15px;
            font-size: 9px;
            text-align: center;
        }
        .legend span {
            margin: 0 10px;
            padding: 2px 6px;
            border-radius: 3px;
            color: white;
            font-weight: bold;
        }
        .legend .present {
            background-color: #27ae60;
        }
        .legend .absent {
            background-color: #e74c3c;
        }
        tbody tr:nth-child(even) {
            background-color: #ecf0f1;
        }
        tbody tr:hover {
            background-color: #d5dbdb;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Monthly Attendance Report</h1>
        <div class="period">{{ $start }} to {{ $end }}</div>
    </div>

    <div class="meta">
        <strong>Generated on:</strong> {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}
    </div>

    <table>
        <thead>
            <tr>
                <th class="emp-code">Emp Code</th>
                <th class="emp-name">Employee Name</th>
                <th class="department">Department</th>
                <th class="site">Site</th>
                @foreach($dateRange as $date)
                    <th class="date-cell">{{ \Carbon\Carbon::parse($date)->day }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $employee)
                <tr>
                    <td class="emp-code">{{ $employee['emp_id'] }}</td>
                    <td class="emp-name">{{ $employee['name'] }}</td>
                    <td class="department">{{ $employee['department'] }}</td>
                    <td class="site">{{ $employee['site_name'] }}</td>
                    @foreach($employee['status'] as $status)
                        <td class="date-cell {{ $status == 'P' ? 'status-p' : 'status-a' }}">{{ $status }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="legend">
        <strong>Legend:</strong>
        <span class="present">P = Present</span>
        <span class="absent">A = Absent</span>
    </div>

</body>
</html>
