<?php
session_start();
include("../db.php");

/* ONLY ADMIN */
if(!isset($_SESSION['admin_id']))
{
    header("Location: index.html");
    exit();
}

$sql = "SELECT co_id,co_name FROM coordinator";
$result = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Event</title>

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

.form-box{
width:100%;
max-width:650px;
background:#ffffff;
padding:35px;
border-radius:24px;
box-shadow:0 20px 40px rgba(0,0,0,.25);
}

.topbar{
background:linear-gradient(135deg,#2563eb,#06b6d4);
padding:20px;
border-radius:18px;
color:#fff;
margin-bottom:25px;
text-align:center;
}

.topbar h2{
margin:0;
font-weight:700;
}

.topbar p{
margin:0;
opacity:.9;
}

.form-label{
font-weight:600;
margin-bottom:8px;
color:#111827;
}

.form-control{
height:50px;
border-radius:12px;
border:1px solid #d1d5db;
padding:12px;
}

textarea.form-control{
height:130px;
resize:none;
}

.form-control:focus{
box-shadow:none;
border-color:#2563eb;
}

.btn-submit{
width:100%;
padding:14px;
border:none;
border-radius:14px;
font-weight:700;
color:#fff;
background:linear-gradient(135deg,#2563eb,#06b6d4);
transition:.3s;
}

.btn-submit:hover{
transform:translateY(-2px);
}

.back-btn{
display:inline-block;
margin-top:15px;
text-decoration:none;
color:#2563eb;
font-weight:600;
}

.back-btn:hover{
color:#0f172a;
}

.icon{
margin-right:8px;
}

@media(max-width:768px)
{
.form-box{
padding:25px;
}
}
</style>
</head>

<body>

<div class="form-box">

<div class="topbar">
<h2><i class="fa fa-calendar-plus icon"></i>Add New Event</h2>
<p>Create and manage your event easily</p>
</div>

<form method="post" action="../main.php?flag=2">

<div class="mb-3">
<label class="form-label">EVENT NAME</label>
<input type="text"
name="event_name"
class="form-control"
placeholder="Enter event name"
required>
</div>

<div class="mb-3">
<label class="form-label">EVENT TYPE</label>
<input type="text"
name="event_type"
class="form-control"
placeholder="Enter event type"
required>
</div>

<div class="mb-4">
<label class="form-label">EVENT DESCRIPTION</label>
<textarea
name="des"
class="form-control"
placeholder="Enter event description"
required></textarea>
</div>

<button type="submit" class="btn-submit">
<i class="fa fa-plus-circle icon"></i>Add Event
</button>

</form>

<a href="dashboard.php" class="back-btn">
<i class="fa fa-arrow-left"></i> Back to Dashboard
</a>

</div>

</body>
</html>