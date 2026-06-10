<?php
session_start();
include("../db.php");

/* Only Admin */
if(!isset($_SESSION['admin_id']))
{
    header("Location: index.html");
    exit();
}

/* Get All Events With Assigned Coordinators */
$sql = "
SELECT 
    e.event_id,
    e.event_name,
    e.event_type,
    e.status,
    c.co_name,
    c.co_email,
    c.co_phone
FROM event e
LEFT JOIN event_coordinator_map m 
    ON e.event_id = m.event_id
LEFT JOIN coordinator c
    ON m.event_co_id = c.co_id
ORDER BY e.event_id DESC
";

$result = mysqli_query($conn,$sql);

/* Build grouped array */
$data = [];

while($row = mysqli_fetch_assoc($result))
{
    $id = $row["event_id"];

    if(!isset($data[$id]))
    {
        $data[$id] = [
            "event_id"   => $row["event_id"],
            "event_name" => $row["event_name"],
            "event_type" => $row["event_type"],
            
            "status"     => $row["status"],
            "coordinators" => []
        ];
    }

    if(!empty($row["co_name"]))
    {
        $data[$id]["coordinators"][] = [
            "name"  => $row["co_name"],
            "email" => $row["co_email"],
            "phone" => $row["co_phone"]
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Assigned Events</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<style>
body{
    background:#f4f6f9;
}

.topbar{
    background:#0d6efd;
    color:#fff;
    padding:18px;
}

.card-box{
    background:#fff;
    border-radius:14px;
    padding:20px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
}

.badge-status{
    font-size:13px;
}

.co-box{
    background:#eef4ff;
    padding:10px;
    border-radius:10px;
    margin-bottom:8px;
}
</style>
</head>

<body>

<div class="topbar">
<div class="container">
<h3 class="mb-0">All Events & Assigned Coordinators</h3>
</div>
</div>

<div class="container mt-4">

<div class="card-box">

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">

<tr class="table-primary">
<th>ID</th>
<th>Event Name</th>
<th>Type</th>
<th>Status</th>
<th>Assigned Coordinators</th>
</tr>

<?php
if(count($data) > 0)
{
    foreach($data as $row)
    {
?>
<tr>
<td><?php echo $row["event_id"]; ?></td>

<td><?php echo $row["event_name"]; ?></td>

<td><?php echo $row["event_type"]; ?></td>
<td>
<span class="badge bg-success badge-status">
<?php echo ucfirst($row["status"]); ?>
</span>
</td>

<td>

<?php
if(count($row["coordinators"]) > 0)
{
    foreach($row["coordinators"] as $co)
    {
?>
<div class="co-box">
<b><?php echo $co["name"]; ?></b><br>
<small><?php echo $co["email"]; ?></small><br>
<small><?php echo $co["phone"]; ?></small>
</div>
<?php
    }
}
else
{
    echo "<span class='text-danger'>No Coordinator Assigned</span>";
}
?>

</td>
</tr>

<?php
    }
}
else
{
?>
<tr>
<td colspan="6" class="text-center text-danger">
No Events Found
</td>
</tr>
<?php } ?>

</table>
</div>

<a href="dashboard.php" class="btn btn-primary mt-3">
Back Dashboard
</a>

</div>
</div>

</body>
</html>