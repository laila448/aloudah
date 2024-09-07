<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drivers report</title>
    <style>
        
        body {
            font-family: 'Amiri','DejaVu Sans', 'Arial', sans-serif;
            margin: 20px;
            direction: rtl; /* Set the direction of the entire document to RTL */
            text-align: right; /* Align the text to the right */
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
           
        }
        .header img {
            position: absolute;
            top: 0;
        }
        .header img.left {
            left: 0;
        }
        .header .title {
            font-size: 1.5em;
            margin-bottom: 10px;
            display: inline-block;
            margin-top: 20px;
            direction: rtl; /* Set the direction of the entire document to RTL */
            text-align: center;
        }
        .header .date-range {
            font-size: 1em;
            margin-bottom: 5px;
            direction: rtl; /* Set the direction of the entire document to RTL */
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            direction: rtl;
        }
        table, th, td {
            border: 1px solid black;
            direction: rtl; /* Set the direction of the entire document to RTL */
            text-align: right;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            font-family: 'Amiri','DejaVu Sans', 'Arial', sans-serif;
            direction: rtl;
            unicode-bidi: embed;
        }
        th {
            background-color: #f2f2f2;
            direction: rtl;
        }
    </style>
</head>
<body>
    <div class="header">

    <img src='/logo.jpg' alt="Logo" class="left" height="50">

        <div class="title" style="position: absolute; top: 20px; left: 50px;"> شركة العودة  </div>
        <div class="driver-name">
            <span> عدد رحلات السائق: {{$name}} </span>    <span> ({{$trip_count}}) </span>
        </div>
        <div class="date-range">
            <span>   إلى تاريخ: {{$to}} </span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;  &nbsp; <span>من تاريخ: {{$from}}</span>
        </div>
       
        <div style="border: 0.5px solid black;"></div>
        <div class="note" >         </div>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>تاريخ الرحلة</th>
                <th>رقم الرحلة</th>
                <th>الوجهة</th>
                <th>رقم السيارة</th>
                <th>الفرع</th>
            </tr>
        </thead>
        <tbody>
            @php $count = 1; @endphp
            @foreach($trips as $trip)
            <tr>
                <td>{{ $count }}</td>
                <td>{{ $trip->date }}</td>
                <td>{{ $trip->number }}</td>
                <td>{{ $trip->destination }}</td>
                <td>{{ $trip->truck_number }}</td>
                <td>{{ $trip->branch }}</td>
            </tr>
            @php $count++; @endphp
            @endforeach
        </tbody>
    </table>
</body>
</html>
