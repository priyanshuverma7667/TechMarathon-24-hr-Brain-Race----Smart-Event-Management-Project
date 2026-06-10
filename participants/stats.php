<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}


include('../db.php');

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User';

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
AND certificate_status='Issued'
"))['total'];

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Participation Stats</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
<div class="container-fluid">
<span class="navbar-brand">Participation Stats</span>
<div class="text-white">
Welcome, <?php echo $user_name; ?> |
<a href="dashboard.php" class="text-warning text-decoration-none">Dashboard</a>
</div>
</div>
</nav>

<div class="container py-4">

<div class="row g-3">

<div class="col-md-4">
<div class="card shadow border-0 text-center">
<div class="card-body">
<h3><?php echo $total_events; ?></h3>
<p class="mb-0">Total Events</p>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card shadow border-0 text-center">
<div class="card-body">
<h3><?php echo $upcoming; ?></h3>
<p class="mb-0">Upcoming Events</p>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card shadow border-0 text-center">
<div class="card-body">
<h3><?php echo $joined; ?></h3>
<p class="mb-0">My Joined Events</p>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card shadow border-0 text-center">
<div class="card-body">
<h3><?php echo $team_joined; ?></h3>
<p class="mb-0">Team Events Joined</p>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card shadow border-0 text-center">
<div class="card-body">
<h3><?php echo $individual_joined; ?></h3>
<p class="mb-0">Individual Events Joined</p>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card shadow border-0 text-center">
<div class="card-body">
<h3><?php echo $certificates; ?></h3>
<p class="mb-0">Certificates Earned</p>
</div>
</div>
</div>

</div>

<!-- Event Wise Stats -->
<div class="card shadow border-0 mt-4">
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

</div>

</body>
</html>