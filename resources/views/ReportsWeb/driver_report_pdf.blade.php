<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تقرير السائق</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>تقرير السائق</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>رقم الرحلة</th>
                <th>رقم الشاحنة</th>
                <th>فرع</th>
                <th>وجهة</th>
                <th>تاريخ الرحلة</th>
            </tr>
        </thead>
        <tbody>
            @php $count = 1; @endphp
            @foreach($reportData as $data)
                <tr>
                    <td>{{ $count++ }}</td>
                    <td>{{ $data['trip_number'] }}</td>
                    <td>{{ $data['truck_number'] }}</td>
                    <td>{{ $data['branch_desk'] }}</td>
                    <td>{{ $data['destination'] }}</td>
                    <td>{{ $data['trip_date'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>