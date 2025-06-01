<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        .header { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 10px; }
        .date-range { text-align: center; margin-bottom: 10px; }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-size: 9px;
            white-space: nowrap;
        }

        th {
            background-color: #f0f0f0;
        }

        .table-container {
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="header">Employee Attendance Report</div>
    <div class="date-range">From {{ $start }} to {{ $end }}</div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Emp Code</th>
                    <th>Name</th>
                    @foreach($dateRange as $date)
                        <th>{{ \Carbon\Carbon::parse($date)->format('d') }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $employee)
                    <tr>
                        <td>{{ $employee['emp_id'] }}</td>
                        <td>{{ $employee['name'] }}</td>
                        @foreach($employee['status'] as $status)
                            <td>{{ $status }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
