<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #444;
            padding: 4px;
            text-align: center;
            word-wrap: break-word;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .emp-cell {
            text-align: left;
            font-weight: bold;
        }
        .meta {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <h2>Monthly Attendance Summary - {{ $start }} to {{ $end }}</h2>

    <div class="meta">
        <strong>Generated on:</strong> {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Emp Code</th>
                <th>Name</th>
                @foreach($dateRange as $date)
                    <th>{{ \Carbon\Carbon::parse($date)->day }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $employee)
                <tr>
                    <td>{{ $employee['emp_id'] }}</td>
                    <td class="emp-cell">{{ $employee['name'] }}</td>
                    @foreach($employee['status'] as $status)
                        <td>{{ $status }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
