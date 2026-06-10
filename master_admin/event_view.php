<?php
session_start();
include("../db.php");

/* ONLY ADMIN */
if(!isset($_SESSION['admin_id']))
{
    header("Location: ../index.html");
    exit();
}

/* FETCH EVENTS */
$events = mysqli_query($conn,"
SELECT *
FROM event
ORDER BY event_id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manage Events</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<style>
body{
    background:#f4f7fb;
    font-family:Arial;
}

.panel{
    background:#fff;
    padding:25px;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    margin-top:30px;
}

h4{
    color:#0d6efd;
    font-weight:bold;
    margin-bottom:20px;
}

.table td,
.table th{
    vertical-align:middle;
}

.action-btn{
    margin:2px;
}

.badge-win{
    background:#198754;
    color:#fff;
    padding:6px 10px;
    border-radius:8px;
}
</style>
</head>

<body>

<div class="container py-4">

<a href="dashboard.php" class="btn btn-dark mb-3">
← Back to Dashboard
</a>

<div class="panel">

<h4>Latest Events</h4>

<div class="table-responsive">
<table class="table table-bordered table-hover">

<thead class="table-dark">
<tr>
<th>ID</th>
<th>Event Name</th>
<th>Type</th>
<th>Description</th>
<th>Event Date</th>
<th>Venue</th>
<th>Reg Start</th>
<th>Reg Close</th>
<th>Winner</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php
if(mysqli_num_rows($events)>0)
{
while($row=mysqli_fetch_assoc($events))
{
?>

<tr>

<td><?php echo $row['event_id']; ?></td>

<td><?php echo $row['event_name']; ?></td>

<td><?php echo $row['event_type']; ?></td>

<td><?php echo $row['description']; ?></td>

<td>
<?php
if($row['event_date']!="0000-00-00")
echo date("d M Y",strtotime($row['event_date']));
else
echo "N/A";
?>
</td>

<td>
<?php echo $row['event_location']; ?>
</td>

<td>
<?php
if($row['reg_open_date']!="0000-00-00")
echo date("d M Y",strtotime($row['reg_open_date']));
else
echo "N/A";
?>
</td>

<td>
<?php
if($row['reg_close_date']!="0000-00-00")
echo date("d M Y",strtotime($row['reg_close_date']));
else
echo "N/A";
?>
</td>

<td>
<?php
$eid = $row['event_id'];

$winner = mysqli_query($conn,"
SELECT full_name,team_name,participation_mode
FROM enrollments
WHERE event_id='$eid'
AND remarks='Winner'
LIMIT 1
");

if(mysqli_num_rows($winner)>0)
{
    $w = mysqli_fetch_assoc($winner);

    if($w['participation_mode']=="Team")
    {
        echo "<span class='badge-win'>".$w['team_name']."</span>";
    }
    else
    {
        echo "<span class='badge-win'>".$w['full_name']."</span>";
    }
}
else
{
    echo "<span class='text-muted'>Not Declared</span>";
}
?>
</td>

<td>

<a href="update_event.php?event_id=<?php echo $row['event_id']; ?>"
class="btn btn-sm btn-primary action-btn">
Edit
</a>

<a href="../main.php?flag=5&event_id=<?php echo $row['event_id']; ?>"
class="btn btn-sm btn-danger action-btn"
onclick="return confirm('Delete this event?')">
Delete
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
<td colspan="10" class="text-center text-muted">
No events found.
</td>
</tr>

<?php } ?>

</tbody>

</table>
</div>

</div>

</div>

</body>
</html>