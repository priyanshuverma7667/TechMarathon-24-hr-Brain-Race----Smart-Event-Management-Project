<?php
session_start();
include("../db.php");

/* ONLY ADMIN */
if(!isset($_SESSION['admin_id']))
{
    header("Location: index.html");
    exit();
}

/* CHECK ID */
if(!isset($_GET['id']))
{
    header("Location: coordinators.php");
    exit();
}

$id = $_GET['id'];

/* FETCH RECORD */
$get = mysqli_query($conn,"
SELECT * FROM coordinator
WHERE co_id='$id'
");

if(mysqli_num_rows($get) == 0)
{
    header("Location: coordinators.php");
    exit();
}

$row = mysqli_fetch_assoc($get);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Coordinator</title>

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

.title{
    color:#0d6efd;
    font-weight:bold;
}
</style>
</head>

<body>

<div class="container py-5">

<div class="card">
<div class="card-body">

<h2 class="title mb-4">Edit Coordinator</h2>

<form action="../main.php?flag=17" method="post">

<!-- IMPORTANT -->
<input type="hidden" name="co_id"
value="<?php echo $row['co_id']; ?>">

<div class="row g-3">

<div class="col-md-6">
<label>Name</label>
<input type="text"
name="co_name"
class="form-control"
required
value="<?php echo $row['co_name']; ?>">
</div>

<div class="col-md-6">
<label>Email</label>
<input type="email"
name="co_email"
class="form-control"
required
value="<?php echo $row['co_email']; ?>">
</div>

<div class="col-md-6">
<label>Phone</label>
<input type="text"
name="co_phone"
class="form-control"
required
value="<?php echo $row['co_phone']; ?>">
</div>

<div class="col-md-6">
<label>Gender</label>
<select name="co_gender" class="form-control" required>

<option value="Male"
<?php if($row['co_gender']=="Male") echo "selected"; ?>>
Male
</option>

<option value="Female"
<?php if($row['co_gender']=="Female") echo "selected"; ?>>
Female
</option>

<option value="Other"
<?php if($row['co_gender']=="Other") echo "selected"; ?>>
Other
</option>

</select>
</div>

<div class="col-md-12">
<label>Address</label>
<textarea
name="co_address"
class="form-control"
rows="3"><?php echo $row['co_address']; ?></textarea>
</div>

<div class="col-md-12">
<label>Password</label>
<input type="text"
name="co_pass"
class="form-control"
required
value="<?php echo $row['co_pass']; ?>">
</div>

<div class="col-md-6">
<button type="submit"
name="update_coordinator"
class="btn btn-warning w-100">
Update Coordinator
</button>
</div>

<div class="col-md-6">
<a href="coordinators.php"
class="btn btn-secondary w-100">
Back
</a>
</div>

</div>

</form>

</div>
</div>

</div>

</body>
</html>