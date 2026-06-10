<?php
session_start();
include("../db.php");

/* ONLY ADMIN */
if(!isset($_SESSION['admin_id']))
{
    header("Location: index.html");
    exit();
}

/* ADMIN NAME */
$admin_name = $_SESSION['admin_name'] ?? 'Administrator';

/* COUNTS */
$totalEvents = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM event"));
$totalCoords = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM coordinator"));
$totalAssign = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM event_coordinator_map"));

/* EVENTS */
$events = mysqli_query($conn,"
SELECT * FROM event
ORDER BY event_id DESC
");

/* COORDINATORS */
$coords = mysqli_query($conn,"
SELECT * FROM coordinator
ORDER BY co_id DESC
");

/* MESSAGE */
$msg  = "";
$type = "";

if(isset($_GET["msg"]))
{
    $msg = $_GET["msg"];

    if($msg == 1)
    {
        $msg = "Event coordinator assigned successfully.";
        $type = "success";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*{
margin:0;
padding:0;
box-sizing:border-box;
}

body{
background:#0f172a;
font-family:Arial,sans-serif;
overflow-x:hidden;
}

/* SIDEBAR */
.sidebar{
width:260px;
height:100vh;
position:fixed;
left:0;
top:0;
background:linear-gradient(180deg,#111827,#1e293b);
padding:22px;
display:flex;
flex-direction:column;
justify-content:space-between;
}

.logo{
color:#fff;
font-size:28px;
font-weight:700;
margin-bottom:25px;
}

.logo span{
color:#38bdf8;
}

.menu a{
display:block;
padding:14px;
margin-bottom:10px;
text-decoration:none;
color:#cbd5e1;
border-radius:12px;
transition:.3s;
}

.menu a:hover{
background:#2563eb;
color:#fff;
}

.admin-box{
background:rgba(255,255,255,.07);
padding:14px;
border-radius:14px;
color:#fff;
margin-top:20px;
}

.admin-name{
font-weight:700;
font-size:16px;
}

.admin-role{
font-size:13px;
color:#cbd5e1;
}

.logout-btn{
display:block;
margin-top:14px;
padding:12px;
text-align:center;
text-decoration:none;
border-radius:12px;
background:#ef4444;
color:#fff;
font-weight:600;
transition:.3s;
}

.logout-btn:hover{
background:#dc2626;
color:#fff;
}

/* CONTENT */
.content{
margin-left:260px;
padding:30px;
}

.topbar{
background:linear-gradient(135deg,#2563eb,#06b6d4);
padding:25px;
border-radius:22px;
display:flex;
justify-content:space-between;
align-items:center;
flex-wrap:wrap;
gap:15px;
color:#fff;
margin-bottom:25px;
}

.card-box{
padding:22px;
border-radius:22px;
color:#fff;
box-shadow:0 15px 30px rgba(0,0,0,.18);
}

.bg1{
background:linear-gradient(135deg,#8b5cf6,#6366f1);
}

.bg2{
background:linear-gradient(135deg,#10b981,#059669);
}

.bg3{
background:linear-gradient(135deg,#f59e0b,#ef4444);
}

.panel{
background:#fff;
padding:22px;
border-radius:22px;
margin-top:25px;
}

.table thead{
background:#eff6ff;
}

.btn-round{
border-radius:12px;
padding:10px 16px;
font-weight:600;
}

@media(max-width:991px)
{
.sidebar{
width:100%;
height:auto;
position:relative;
}

.content{
margin-left:0;
}
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

<div>

<div class="logo">
SMART <span>EVENT</span>
</div>

<div class="menu">

<a href="dashboard.php">
<i class="fa fa-chart-line"></i> Dashboard
</a>

<a href="event_view.php">
<i class="fa fa-calendar"></i> Events
</a>

<a href="add_event.php">
<i class="fa fa-plus-circle"></i> Add Event
</a>

<a href="assigned_events.php">
<i class="fa fa-link"></i> Assigned Events
</a>

<a href="coordinator_list.php">
<i class="fa fa-users"></i> Coordinators
</a>

<a href="analytics.php">
<i class="fa fa-chart-pie"></i> Analytics
</a>

</div>

</div>

<!-- BOTTOM AREA -->
<div>

<div class="admin-box">
<div class="admin-name">
<i class="fa fa-user-shield"></i>
<?php echo $admin_name; ?>
</div>

<div class="admin-role">
Master Administrator
</div>
</div>

<a href="logout.php" class="logout-btn">
<i class="fa fa-sign-out-alt"></i>
Logout
</a>

</div>

</div>

<!-- CONTENT -->
<div class="content">

<!-- TOPBAR -->
<div class="topbar">

<div>
<h2>Admin Dashboard</h2>
<p class="mb-0">Manage Events Easily</p>
</div>

<div class="d-flex gap-2">

<a href="add_event.php" class="btn btn-light btn-round">
+ Add Event
</a>

<a href="event_coordinator.php" class="btn btn-dark btn-round">
Assign Coordinator
</a>

</div>

</div>

<?php if($msg!=""){ ?>

<div class="alert alert-<?php echo $type; ?> alert-dismissible fade show">
<?php echo $msg; ?>
<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<?php } ?>

<!-- STATS -->
<div class="row g-4">

<div class="col-md-4">
<div class="card-box bg1">
<h6>Total Events</h6>
<h2><?php echo $totalEvents; ?></h2>
</div>
</div>

<div class="col-md-4">
<div class="card-box bg2">
<h6>Total Coordinators</h6>
<h2><?php echo $totalCoords; ?></h2>
</div>
</div>

<div class="col-md-4">
<div class="card-box bg3">
<h6>Total Assignments</h6>
<h2><?php echo $totalAssign; ?></h2>
</div>
</div>

</div>

<!-- EVENTS -->
<div class="panel">

<h4 class="mb-3">Latest Events</h4>

<div class="table-responsive">
<table class="table table-bordered">

<thead>
<tr>
<th>ID</th>
<th>Event Name</th>
<th>Type</th>
<th>Description</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($events)){ ?>

<tr>

<td><?php echo $row['event_id']; ?></td>
<td><?php echo $row['event_name']; ?></td>
<td><?php echo $row['event_type']; ?></td>
<td><?php echo $row['description']; ?></td>

<td>

<a href="update_event.php?event_id=<?php echo $row['event_id']; ?>"
class="btn btn-sm btn-primary">
Edit
</a>

<a href="../main.php?flag=5&event_id=<?php echo $row['event_id']; ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('Delete this event?')">
Delete
</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>
</div>

</div>

<!-- COORDINATORS -->
<div class="panel">

<h4 class="mb-3">Coordinator Directory</h4>

<div class="table-responsive">
<table class="table table-bordered">

<thead>
<tr>
<th>ID</th>
<th>Name</th>
</tr>
</thead>

<tbody>

<?php while($c=mysqli_fetch_assoc($coords)){ ?>

<tr>
<td><?php echo $c['co_id']; ?></td>
<td><?php echo $c['co_name']; ?></td>
</tr>

<?php } ?>

</tbody>

</table>
</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>