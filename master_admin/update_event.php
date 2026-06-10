<?php
session_start();
include("../db.php");

/* ONLY ADMIN */
if(!isset($_SESSION['admin_id']))
{
    header("Location: index.html");
    exit();
}
if(!isset($_GET["event_id"]))
{
    header("Location: dashboard.php");
    exit();
}

$event_id = $_GET["event_id"];

/* Event Data */
$event_q = mysqli_query($conn,"
SELECT * FROM event
WHERE event_id='$event_id'
");

$event = mysqli_fetch_assoc($event_q);

/* All Coordinators */
$co_q = mysqli_query($conn,"
SELECT co_id,co_name
FROM coordinator
");

/* Already Assigned */
$map_q = mysqli_query($conn,"
SELECT event_co_id
FROM event_coordinator_map
WHERE event_id='$event_id'
");

$selected = array();

while($row = mysqli_fetch_assoc($map_q))
{
    $selected[] = $row["event_co_id"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Event</title>

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
min-height:100vh;
display:flex;
justify-content:center;
align-items:center;
padding:30px;
}

.edit-card{
width:100%;
max-width:800px;
background:#ffffff;
border-radius:24px;
overflow:hidden;
box-shadow:0 25px 50px rgba(0,0,0,.25);
}

.header-box{
background:linear-gradient(135deg,#8b5cf6,#2563eb);
padding:28px;
color:#fff;
text-align:center;
}

.header-box h2{
margin:0;
font-weight:700;
}

.header-box p{
margin:5px 0 0;
opacity:.9;
}

.form-area{
padding:35px;
}

.form-label{
font-weight:700;
color:#111827;
margin-bottom:8px;
}

.form-control{
height:50px;
border-radius:14px;
border:1px solid #d1d5db;
}

textarea.form-control{
height:120px;
resize:none;
padding-top:12px;
}

.form-control:focus{
box-shadow:none;
border-color:#2563eb;
}

.co-box{
background:#f8fafc;
padding:18px;
border-radius:16px;
border:1px solid #e5e7eb;
max-height:240px;
overflow-y:auto;
}

.form-check{
padding:10px 12px;
border-bottom:1px solid #e5e7eb;
}

.form-check:last-child{
border-bottom:none;
}

.form-check-input{
width:18px;
height:18px;
margin-top:3px;
/* padding-left:5px; */
cursor:pointer;
}

.form-check-label{
margin-left:8px;
font-weight:600;
cursor:pointer;
}

.btn-update{
width:100%;
padding:14px;
border:none;
border-radius:14px;
font-weight:700;
color:#fff;
background:linear-gradient(135deg,#2563eb,#06b6d4);
margin-top:20px;
transition:.3s;
}

.btn-update:hover{
transform:translateY(-2px);
}

.back-link{
display:inline-block;
margin-top:15px;
text-decoration:none;
font-weight:600;
color:#2563eb;
}

.back-link:hover{
color:#111827;
}

@media(max-width:768px)
{
.form-area{
padding:25px;
}
}
</style>
</head>

<body>

<div class="edit-card">

<div class="header-box">
<h2><i class="fa fa-pen-to-square"></i> Update Event</h2>
<p>Edit event details and manage coordinators</p>
</div>

<div class="form-area">

<form method="post" action="../main.php?flag=4">

<input type="hidden"
name="event_id"
value="<?php echo $event['event_id']; ?>">

<div class="mb-3">
<label class="form-label">EVENT NAME</label>

<input type="text"
name="event_name"
class="form-control"
value="<?php echo $event['event_name']; ?>"
required>
</div>

<div class="mb-3">
<label class="form-label">EVENT TYPE</label>

<input type="text"
name="event_type"
class="form-control"
value="<?php echo $event['event_type']; ?>"
required>
</div>

<div class="mb-3">
<label class="form-label">DESCRIPTION</label>

<textarea
name="des"
class="form-control"
required><?php echo $event['description']; ?></textarea>
</div>

<div class="mb-3">
<label class="form-label">ASSIGNED COORDINATORS</label>

<div class="co-box">

<?php
while($co = mysqli_fetch_assoc($co_q))
{
$checked = in_array($co["co_id"],$selected) ? "checked" : "";
?>

<div class="form-check">

<input type="checkbox"
class="form-check-input"
name="event_co_id[]"
value="<?php echo $co['co_id']; ?>"
id="co<?php echo $co['co_id']; ?>"
<?php echo $checked; ?>>

<label class="form-check-label"
for="co<?php echo $co['co_id']; ?>">
<?php echo $co["co_name"]; ?>
</label>

</div>

<?php } ?>

</div>
</div>

<button type="submit" class="btn-update">
<i class="fa fa-floppy-disk"></i> Update Event
</button>

</form>

<a href="dashboard.php" class="back-link">
<i class="fa fa-arrow-left"></i> Back to Dashboard
</a>

</div>
</div>

</body>
</html>