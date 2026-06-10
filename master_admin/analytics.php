<?php
session_start();
include("../db.php");

/* ONLY ADMIN */
if(!isset($_SESSION['admin_id']))
{
    header("Location:index.html");
    exit();
}

/* TOTAL COUNTS */
$total_events = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total FROM event
"))['total'];

$upcoming = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total FROM event
WHERE status='upcoming'
"))['total'];

$running = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total FROM event
WHERE status='running'
"))['total'];

$ended = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total FROM event
WHERE status='ended'
"))['total'];

$total_users = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total FROM users
"))['total'];

$total_enrollments = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total FROM enrollments
"))['total'];

$total_present = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total FROM enrollments
WHERE remarks='present'
OR remarks='winner'
"))['total'];

$total_winners = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total FROM enrollments
WHERE is_winner = 1
"))['total'];

/* JOINED VS WITHDRAWN */
$total_joined = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total FROM enrollments
WHERE status='Joined'
"))['total'];

$total_withdrawn = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total FROM enrollments
WHERE status='Withdrawn'
"))['total'];

/* GENDER COUNTS */
$male = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM enrollments en
INNER JOIN users u ON en.user_id=u.user_id
WHERE u.gender='Male'
"))['total'];

$female = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM enrollments en
INNER JOIN users u ON en.user_id=u.user_id
WHERE u.gender='Female'
"))['total'];

$other = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM enrollments en
INNER JOIN users u ON en.user_id=u.user_id
WHERE u.gender='Other'
"))['total'];

/* REGISTRATIONS PER EVENT */
$event_names = [];
$event_counts = [];

$q1 = mysqli_query($conn,"
SELECT e.event_name,
COUNT(en.enroll_id) total
FROM event e
LEFT JOIN enrollments en
ON e.event_id=en.event_id
GROUP BY e.event_id
ORDER BY total DESC
");

while($r=mysqli_fetch_assoc($q1))
{
    $event_names[] = $r['event_name'];
    $event_counts[] = $r['total'];
}

/* WINNERS PER EVENT */
$winner_event = [];
$winner_count = [];

$q2 = mysqli_query($conn,"
SELECT e.event_name,
COUNT(en.enroll_id) total
FROM event e
LEFT JOIN enrollments en
ON e.event_id=en.event_id
AND en.is_winner= 1
GROUP BY e.event_id
ORDER BY total DESC
");

while($r=mysqli_fetch_assoc($q2))
{
    $winner_event[] = $r['event_name'];
    $winner_count[] = $r['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Analytics Dashboard</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
background:#f4f7fb;
font-family:Arial;
}

.card{
border:none;
border-radius:16px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.stat{
font-size:34px;
font-weight:bold;
color:#0d6efd;
}

.heading{
font-weight:bold;
color:#0d6efd;
}

canvas{
max-height:350px;
}
</style>
</head>

<body>

<div class="container py-4">

<a href="dashboard.php" class="btn btn-dark mb-3">
← Back to Dashboard
</a>

<h2 class="heading mb-4">
All Events Analytics Dashboard
</h2>

<!-- TOP BOXES -->
<div class="row g-3">

<div class="col-md-3">
<div class="card p-3 text-center">
<div class="stat"><?php echo $total_events; ?></div>
Total Events
</div>
</div>

<div class="col-md-3">
<div class="card p-3 text-center">
<div class="stat text-success"><?php echo $total_users; ?></div>
Users
</div>
</div>

<div class="col-md-3">
<div class="card p-3 text-center">
<div class="stat text-warning"><?php echo $total_enrollments; ?></div>
Enrollments
</div>
</div>

<div class="col-md-3">
<div class="card p-3 text-center">
<div class="stat text-danger"><?php echo $total_winners; ?></div>
Winners
</div>
</div>

</div>

<!-- CHARTS -->
<div class="row mt-4 g-4">

<div class="col-md-6">
<div class="card p-3">
<h5>Event Status</h5>
<canvas id="statusChart"></canvas>
</div>
</div>

<div class="col-md-6">
<div class="card p-3">
<h5>Attendance Summary</h5>
<canvas id="attendanceChart"></canvas>
</div>
</div>

<div class="col-md-6">
<div class="card p-3">
<h5>Gender Enrollment</h5>
<canvas id="genderChart"></canvas>
</div>
</div>

<div class="col-md-6">
<div class="card p-3">
<h5>Winner Distribution</h5>
<canvas id="winnerChart"></canvas>
</div>
</div>

<div class="col-md-12">
<div class="card p-3">
<h5>Registrations Per Event</h5>
<canvas id="eventChart"></canvas>
</div>
</div>

<div class="col-md-6">
<div class="card p-3">
<h5>Joined vs Withdrawn Users</h5>
<canvas id="withdrawChart"></canvas>
</div>
</div>

</div>

<!-- TABLE -->
<div class="card mt-4">
<div class="card-body">

<h5>Detailed Event Report</h5>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<tr class="table-dark">
<th>ID</th>
<th>Event</th>
<th>Status</th>
<th>Date</th>
<th>Joined</th>
<th>Present</th>
<th>Winner</th>
</tr>

<?php
$list = mysqli_query($conn,"
SELECT e.*,

(SELECT COUNT(*) FROM enrollments en
WHERE en.event_id=e.event_id) total_join,

(SELECT COUNT(*) FROM enrollments en
WHERE en.event_id=e.event_id
AND (en.remarks='present' OR en.remarks='winner')) total_present,

(SELECT COUNT(*) FROM enrollments en
WHERE en.event_id=e.event_id
AND en.is_winner = 1) total_winner

FROM event e
ORDER BY e.event_id DESC
");

while($row=mysqli_fetch_assoc($list))
{
echo "
<tr>
<td>{$row['event_id']}</td>
<td>{$row['event_name']}</td>
<td>{$row['status']}</td>
<td>{$row['event_date']}</td>
<td>{$row['total_join']}</td>
<td>{$row['total_present']}</td>
<td>{$row['total_winner']}</td>
</tr>";
}
?>

</table>

</div>
</div>
</div>

<a href="dashboard.php" class="btn btn-dark mb-3 mt-4">
← Back to Dashboard
</a>

</div>

<script>

/* EVENT STATUS */
new Chart(document.getElementById("statusChart"),{
type:"pie",
data:{
labels:["Upcoming","Running","Ended"],
datasets:[{
data:[
<?php echo $upcoming; ?>,
<?php echo $running; ?>,
<?php echo $ended; ?>
]
}]
}
});

/* ATTENDANCE */
new Chart(document.getElementById("attendanceChart"),{
type:"doughnut",
data:{
labels:["Enrollments","Present","Winners"],
datasets:[{
data:[
<?php echo $total_enrollments; ?>,
<?php echo $total_present; ?>,
<?php echo $total_winners; ?>
]
}]
}
});

/* GENDER */
new Chart(document.getElementById("genderChart"),{
type:"polarArea",
data:{
labels:["Male","Female","Other"],
datasets:[{
data:[
<?php echo $male; ?>,
<?php echo $female; ?>,
<?php echo $other; ?>
]
}]
}
});

/* WINNERS */
new Chart(document.getElementById("winnerChart"),{
type:"line",
data:{
labels:<?php echo json_encode($winner_event); ?>,
datasets:[{
label:"Winners",
data:<?php echo json_encode($winner_count); ?>,
fill:false,
tension:0.4
}]
}
});

/* REGISTRATIONS */
new Chart(document.getElementById("eventChart"),{
type:"bar",
data:{
labels:<?php echo json_encode($event_names); ?>,
datasets:[{
label:"Registrations",
data:<?php echo json_encode($event_counts); ?>
}]
}
});

/* JOINED VS WITHDRAWN */
new Chart(document.getElementById("withdrawChart"),{
type:"bar",
data:{
labels:["Joined","Withdrawn"],
datasets:[{
label:"Users",
data:[
<?php echo $total_joined; ?>,
<?php echo $total_withdrawn; ?>
]
}]
},
options:{
responsive:true,
plugins:{
legend:{
display:false
}
},
scales:{
y:{
beginAtZero:true
}
}
}
});

</script>

</body>
</html>