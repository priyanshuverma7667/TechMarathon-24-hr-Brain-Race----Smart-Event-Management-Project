<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

include('../db.php');

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['username'] ?? 'User';

/* Dashboard Counts */
$total_events = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total FROM event
"))['total'];

$upcoming = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total FROM event
WHERE status='upcoming'
"))['total'];

$joined = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total FROM enrollments
WHERE user_id='$user_id'
AND status='Joined'
"))['total'];

$team_joined = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total FROM enrollments
WHERE user_id='$user_id'
AND participation_mode='Team'
AND status='Joined'
"))['total'];

$individual_joined = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total FROM enrollments
WHERE user_id='$user_id'
AND participation_mode='Individual'
AND status='Joined'
"))['total'];

$certificates = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total FROM enrollments
WHERE user_id='$user_id'
AND (
    is_winner = 1
    OR remarks='present'
)
"))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>User Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body{
background:#f4f7fb;
font-family:'Poppins',sans-serif;
}

.navbar{
background:linear-gradient(90deg,#0d6efd,#0b5ed7);
padding:14px 20px;
}

.navbar-brand{
font-weight:700;
font-size:22px;
}

.card{
border:none;
border-radius:16px;
box-shadow:0 10px 25px rgba(0,0,0,.07);
}

.table td,.table th{
vertical-align:middle;
font-size:14px;
}

.badge{
padding:8px 10px;
border-radius:8px;
}

.btn{
border-radius:10px;
}

@media(max-width:768px){
.navbar-brand{
font-size:18px;
}
}
</style>
</head>

<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-dark">
<div class="container-fluid">

<span class="navbar-brand">
Participant Dashboard
</span>

<div class="text-white">
Welcome, <?php echo $user_name; ?> |
<a href="logout.php" class="text-warning text-decoration-none">
Logout
</a>
</div>

</div>
</nav>

<div class="container py-4">

<!-- Welcome -->
<div class="card mb-4">
<div class="card-body">
<h3>Hello, <?php echo $user_name; ?></h3>
<p class="text-muted mb-0">
Manage your joined and upcoming events.
</p>
</div>
</div>

<!-- Stats -->
<div class="row g-3">

<div class="col-md-4">
<div class="card text-center">
<div class="card-body">
<h3><?php echo $total_events; ?></h3>
<p>Total Events</p>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card text-center">
<div class="card-body">
<h3><?php echo $upcoming; ?></h3>
<p>Upcoming Events</p>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card text-center">
<div class="card-body">
<h3><?php echo $joined; ?></h3>
<p>My Joined Events</p>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card text-center">
<div class="card-body">
<h3><?php echo $team_joined; ?></h3>
<p>Team Events Joined</p>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card text-center">
<div class="card-body">
<h3><?php echo $individual_joined; ?></h3>
<p>Individual Events Joined</p>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card text-center">
<div class="card-body">
<h3><?php echo $certificates; ?></h3>
<p>Certificates Earned</p>
</div>
</div>
</div>

</div>

<!-- Participation History -->
<div class="card mt-4">
<div class="card-header bg-primary text-white">
My Event Participation History
</div>

<div class="card-body table-responsive">

<table class="table table-bordered table-hover">
<tr class="table-dark">
<th>#</th>
<th>Event Name</th>
<th>Mode</th>
<th>Status</th>
<th>Joined Date</th>
</tr>

<?php
$sql = mysqli_query($conn,"
SELECT e.event_name,
en.participation_mode,
en.status,
en.joined_at
FROM enrollments en
INNER JOIN event e ON en.event_id=e.event_id
WHERE en.user_id='$user_id'
ORDER BY en.joined_at DESC
");

$i=1;

while($row=mysqli_fetch_assoc($sql))
{
?>
<tr>
<td><?php echo $i++; ?></td>
<td><?php echo $row['event_name']; ?></td>
<td><?php echo $row['participation_mode']; ?></td>
<td><?php echo $row['status']; ?></td>
<td><?php echo date('d M Y', strtotime($row['joined_at'])); ?></td>
</tr>
<?php } ?>

</table>

</div>
</div>

<div class="row mt-4">

<!-- Joined Events -->
<div class="col-md-6 mb-4">
<div class="card h-100">

<div class="card-header bg-primary text-white">
My Participated Events
</div>

<div class="card-body table-responsive">

<table class="table table-bordered table-hover">
<tr class="table-dark">
<th>#</th>
<th>Event</th>
<th>Date</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php
$sql1 = mysqli_query($conn,"
SELECT en.enroll_id,
e.event_name,
e.event_date,
en.status
FROM enrollments en
INNER JOIN event e
ON en.event_id = e.event_id
WHERE en.user_id='$user_id'
AND en.status='Joined'
ORDER BY en.joined_at DESC
");

$i=1;

if(mysqli_num_rows($sql1)>0)
{
while($row=mysqli_fetch_assoc($sql1))
{
?>
<tr>
<td><?php echo $i++; ?></td>
<td><?php echo $row['event_name']; ?></td>
<td><?php echo date('d M Y', strtotime($row['event_date'])); ?></td>
<td><span class="badge bg-success">Joined</span></td>
<td>

<a href="withdraw.php?id=<?php echo $row['enroll_id']; ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('Withdraw from this event?')">
Withdraw
</a>

</td>
</tr>
<?php
}
}
else
{
?>
<tr>
<td colspan="5" class="text-center text-muted">
No joined events.
</td>
</tr>
<?php } ?>

</table>

</div>
</div>
</div>

<!-- Upcoming -->
<div class="col-md-6 mb-4">
<div class="card h-100">

<div class="card-header bg-success text-white">
Upcoming Events
</div>

<div class="card-body table-responsive">

<table class="table table-bordered table-hover">
<tr class="table-dark">
<th>#</th>
<th>Event</th>
<th>Date</th>
<th>Action</th>
</tr>

<?php
$sql2 = mysqli_query($conn,"
SELECT * FROM event
WHERE status='upcoming'
AND event_date >= CURDATE()
ORDER BY event_date ASC
");

$j=1;

if(mysqli_num_rows($sql2)>0)
{
while($row=mysqli_fetch_assoc($sql2))
{
$event_id = $row['event_id'];

$check = mysqli_query($conn,"
SELECT * FROM enrollments
WHERE user_id='$user_id'
AND event_id='$event_id'
AND status='Joined'
");

$already = mysqli_num_rows($check);
?>

<tr>
<td><?php echo $j++; ?></td>
<td><?php echo $row['event_name']; ?></td>
<td><?php echo date('d M Y', strtotime($row['event_date'])); ?></td>
<td>

<?php if($already>0){ ?>

<button class="btn btn-sm btn-secondary" disabled>
Already Joined
</button>

<?php } else { ?>

<a href="enroll.php?id=<?php echo $event_id; ?>"
class="btn btn-sm btn-success">
Join
</a>

<?php } ?>

</td>
</tr>

<?php
}
}
else
{
?>
<tr>
<td colspan="4" class="text-center text-muted">
No upcoming events available.
</td>
</tr>
<?php } ?>

</table>

</div>
</div>
</div>

</div>

<!-- CERTIFICATE SECTION -->
<div class="card mt-4">

<div class="card-header bg-warning text-dark">
My Certificates
</div>

<div class="card-body table-responsive">

<table class="table table-bordered table-hover">

<tr class="table-dark">
<th>#</th>
<th>Event Name</th>
<th>Certificate Type</th>
<th>Action</th>
</tr>

<?php
$cert_sql = mysqli_query($conn,"
SELECT e.event_name,
e.event_date,
en.enroll_id,
en.remarks,
en.is_winner
FROM enrollments en
INNER JOIN event e
ON en.event_id=e.event_id
WHERE en.user_id='$user_id'
AND e.event_date < CURDATE()
AND (
    en.remarks='present'
)
ORDER BY e.event_date DESC
");

$k=1;

if(mysqli_num_rows($cert_sql)>0)
{
while($cert=mysqli_fetch_assoc($cert_sql))
{
?>
<tr>

<td><?php echo $k++; ?></td>

<td><?php echo $cert['event_name']; ?></td>

<td>

<?php
if($cert['is_winner']==1 && $cert["remarks"]=="present")
{
echo "<span class='badge bg-success'>Achievement</span>";
}
else
{
echo "<span class='badge bg-primary'>Participation</span>";
}
?>

</td>

<td>

<a href="download_certificate.php?id=<?php echo $cert['enroll_id']; ?>"
class="btn btn-sm btn-warning">
Download
</a>

</td>

</tr>
<?php
}
}
else
{
?>
<tr>
<td colspan="4" class="text-center text-muted">
No certificates available yet.
</td>
</tr>
<?php } ?>

</table>

</div>
</div>

</div>

</body>
</html>