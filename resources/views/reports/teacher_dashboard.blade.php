<!DOCTYPE html>
<html>
<head>
<style>

body{
    font-family: Arial, sans-serif;
    background:#ffffff;
    color:#000000;
    margin:0;
    padding:0;
}

/* HEADER SAFE */
.header{
    background:#1e3a8a;
    color:#ffffff;
    padding:20px;
    text-align:center;
}

/* CARDS SAFE (NO FLEX) */
.cards-table{
    width:100%;
    margin-top:15px;
    border-collapse:separate;
    border-spacing:10px;
}

.card{
    width:33%;
    background:#f3f4f6;
    text-align:center;
    padding:15px;
    border:1px solid #ddd;
    font-weight:bold;
}

.blue{background:#dbeafe;}
.green{background:#dcfce7;}
.dark{background:#e5e7eb;}

/* TABLE SAFE */
.table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}

.table th{
    background:#111827;
    color:#ffffff;
    padding:10px;
    font-size:13px;
}

.table td{
    border:1px solid #ddd;
    padding:8px;
    text-align:center;
    font-size:12px;
    color:#000;
}

.table tr:nth-child(even){
    background:#f9fafb;
}

/* BADGES SAFE */
.badge-online{
    background:#2563eb;
    color:#fff;
    padding:3px 6px;
}

.badge-center{
    background:#16a34a;
    color:#fff;
    padding:3px 6px;
}

.profit{
    color:#000;
    font-weight:bold;
}

h3{
    margin-top:20px;
    font-size:14px;
}

</style>
</head>

<body>

<div class="header">
    <h2>{{ $teacher->name }} Report Dashboard</h2>
    <p>
        @if($from && $to)
            From {{ $from->format('Y-m-d') }} To {{ $to->format('Y-m-d') }}
        @else
            Full Report
        @endif
    </p>
</div>

<table class="cards">
    <tr>
        <td class="card blue">
            <h4>Online Courses</h4>
            <h2>{{ $onlineCourses }}</h2>
        </td>

        <td class="card green">
            <h4>Center Courses</h4>
            <h2>{{ $centerCourses }}</h2>
        </td>

        <td class="card dark">
            <h4>Total Profit</h4>
            <h2>{{ $totalProfit }} EGP</h2>
        </td>
    </tr>
</table>

<h3>Courses Breakdown</h3>
<table class="table">
    <tr>
        <th>Title</th>
        <th>Type</th>
        <th>Students</th>
        <th>Profit</th>
    </tr>

    @foreach($coursesData as $c)
    <tr>
        <td>{{ $c['title'] }}</td>
        <td>
            @if($c['type'] == 'online')
                <span class="badge-online">Online</span>
            @else
                <span class="badge-center">Center</span>
            @endif
        </td>
        <td>{{ $c['students'] }}</td>
        <td class="profit">{{ $c['profit'] }}</td>
    </tr>
    @endforeach
</table>

<h3>Semesters Breakdown</h3>
<table class="table">
    <tr>
        <th>Name</th>
        <th>Students</th>
        <th>Profit</th>
    </tr>

    @foreach($semesterData as $s)
    <tr>
        <td>{{ $s['name'] }}</td>
        <td>{{ $s['students'] }}</td>
        <td class="profit">{{ $s['profit'] }}</td>
    </tr>
    @endforeach
</table>

<h3>Lessons Breakdown</h3>
<table class="table">
    <tr>
        <th>Title</th>
        <th>Students</th>
        <th>Profit</th>
    </tr>

    @foreach($lessonData as $l)
    <tr>
        <td>{{ $l['title'] }}</td>
        <td>{{ $l['students'] }}</td>
        <td class="profit">{{ $l['profit'] }}</td>
    </tr>
    @endforeach
</table>

</body>
</html>
