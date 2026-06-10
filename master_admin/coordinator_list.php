<?php
session_start();
include("../db.php");

/* ONLY ADMIN */
if(!isset($_SESSION['admin_id']))
{
    header("Location: index.html");
    exit();
}

/* LIST ALL COORDINATORS */
$list = mysqli_query($conn,"
SELECT * FROM coordinator
ORDER BY co_id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manage Coordinators</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

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

.table td,.table th{
    vertical-align:middle;
}

.top-btn{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
}

.page-title{
    font-weight:bold;
    color:#0d6efd;
}

.action-btn{
    min-width:70px;
}
</style>
</head>

<body>

<div class="container py-4">

<div class="top-btn">
<h2 class="page-title mb-0">All Coordinators</h2>

<a href="add_coordinator.php" class="btn btn-primary">
+ Add Coordinator
</a>
</div>

<?php if(isset($_GET['msg'])) { ?>

<div class="alert alert-success alert-dismissible fade show">
<?php
if($_GET['msg']=="added") echo "Coordinator Added Successfully";
if($_GET['msg']=="updated") echo "Coordinator Updated Successfully";
if($_GET['msg']=="deleted") echo "Coordinator Deleted Successfully";
?>
<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<?php } ?>

<div class="card">
<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<tr class="table-dark">
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>Gender</th>
<th>Address</th>
<th>Created</th>
<th width="190">Actions</th>
</tr>

<?php
if(mysqli_num_rows($list) > 0)
{
while($row=mysqli_fetch_assoc($list))
{
?>

<tr>
<td><?php echo $row['co_id']; ?></td>

<td><?php echo $row['co_name']; ?></td>

<td><?php echo $row['co_email']; ?></td>

<td><?php echo $row['co_phone']; ?></td>

<td><?php echo $row['co_gender']; ?></td>

<td><?php echo $row['co_address']; ?></td>

<td><?php echo $row['created_at']; ?></td>

<td>

<a href="edit_coordinator.php?id=<?php echo $row['co_id']; ?>"
class="btn btn-sm btn-warning action-btn">
Edit
</a>

<a href="../main.php?flag=18&id=<?php echo $row['co_id']; ?>"
class="btn btn-sm btn-danger action-btn"
onclick="return confirm('Delete this coordinator?')">
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
<td colspan="8" class="text-center text-danger">
No Coordinators Found
</td>
</tr>

<?php } ?>

</table>

</div>

</div>
</div>

<div class="mt-3">
<a href="dashboard.php" class="btn btn-secondary">
Back Dashboard
</a>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>