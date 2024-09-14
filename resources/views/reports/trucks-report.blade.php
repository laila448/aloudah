<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trucks report</title>
    <style>
        
        body {
            font-family: 'Amiri','DejaVu Sans', 'Arial', sans-serif;
            margin: 20px;
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
            text-align: center;
        }
        .header .date-range {
            font-size: 1em;
            margin-bottom: 5px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
            text-align: right;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            font-family: 'Amiri','DejaVu Sans', 'Arial', sans-serif;
            unicode-bidi: embed;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">

    <img src='/logo.jpg' alt="Logo" class="left" height="50">

        <div class="title" style="position: absolute; top: 20px; left: 50px;"> Aloudah company  </div>
        <div class="driver-name">
            <span>Number of truck trips : {{$number}}  ({{$trip_count}}) </span>  
        </div>
        <div class="date-range">
        <span>from : {{$from}}     ,     to : {{$to}}</span>
        </div>
       
        <div style="border: 0.5px solid black;"></div>
        <div class="note" >         </div>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Trip date</th>
                <th>Trip number</th>
                <th>Destination</th>
                <th>Driver</th>
                <th>Branch</th>
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
                <td>{{ $trip->driver_name }}</td>
                <td>{{ $trip->branch }}</td>
            </tr>
            @php $count++; @endphp
            @endforeach
        </tbody>
    </table>
</body>
</html>
