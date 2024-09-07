<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>destination report</title>
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
            <span>  الرحلات المتجهة إلى : {{$branch_desk}}  ({{$trip_count}}) </span>  
        </div>
        <div class="date-range">
            <span>  حالة الرحلة : {{$status}}       من تاريخ : {{$from}}      إلى تاريخ : {{$to}}</span>
        </div>
       
        <div style="border: 0.5px solid black;"></div>
        <div class="note" >         </div>
    
    <table>
        <thead>
            <tr>
                <th>تاريخ الرحلة</th>
                <th>رقم الرحلة</th>
                <th>الوجهة</th>
                <th>السائق</th>
                <th>المجموع العام</th>
                <th>مسبق</th>
                <th>محول</th>
                <th>الخصم</th>
                <th>المجموع العام - الخصم</th>
                <th>متفرقات مدفوعة</th>
                <th>ضد الشحن</th>
                <th>الفرع</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trips as $trip)
            <tr>
                <td>{{ $trip->date }}</td>
                <td>{{ $trip->number }}</td>
                <td>{{ $trip->destination }}</td>
                <td>{{ $trip->driver_name }}</td>
                <td>{{ $trip->general_total }}</td>
                <td>{{ $trip->advance }}</td>
                <td>{{ $trip->adapter }}</td>
                <td>{{ $trip->discount }}</td>
                <td>{{ $trip->total_disc }}</td>
                <td>{{ $trip->misc_paid }}</td>
                <td>{{ $trip->against_shipping }}</td>
                <td>{{ $trip->branch }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
