<?php
session_start();
include("../db.php");

/* ONLY ADMIN */
if(!isset($_SESSION['admin_id']))
{
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Add Coordinator</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body{
    background:#f4f7fb;
    font-family:Arial,sans-serif;
}

.card{
    border:none;
    border-radius:18px;
    box-shadow:0 12px 28px rgba(0,0,0,.08);
}

.title{
    color:#0d6efd;
    font-weight:700;
}

.form-label{
    font-weight:600;
}

.btn{
    border-radius:10px;
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

@media(max-width:768px){
    .topbar{
        flex-direction:column;
        gap:10px;
        align-items:flex-start;
    }
}
</style>
</head>

<body>

<div class="container py-5">

<div class="topbar">
    <h2 class="title mb-0">
        <i class="fa fa-user-plus"></i> Add Coordinator
    </h2>

    <a href="coordinators.php" class="btn btn-secondary">
        <i class="fa fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
<div class="card-body p-4">

<form action="../main.php?flag=19" method="post">

<div class="row g-3">

<div class="col-md-6">
<label class="form-label">Full Name</label>
<input type="text"
name="co_name"
class="form-control"
required>
</div>

<div class="col-md-6">
<label class="form-label">Email</label>
<input type="email"
name="co_email"
class="form-control"
required>
</div>

<div class="col-md-6">
<label class="form-label">Phone</label>
<input type="text"
name="co_phone"
class="form-control"
required>
</div>

<div class="col-md-6">
<label class="form-label">Gender</label>
<select name="co_gender"
class="form-select"
required>
<option value="">Select Gender</option>
<option value="Male">Male</option>
<option value="Female">Female</option>
<option value="Other">Other</option>
</select>
</div>

<div class="col-md-12">
<label class="form-label">Address</label>
<textarea
name="co_address"
rows="3"
class="form-control"
required></textarea>
</div>

<div class="col-md-12">
<label class="form-label">Password</label>
<input type="text"
name="co_pass"
class="form-control"
required>
</div>

<div class="col-md-6">
<button type="submit"
class="btn btn-primary w-100">
<i class="fa fa-save"></i> Add Coordinator
</button>
</div>

<div class="col-md-6">
<a href="coordinators.php"
class="btn btn-dark w-100">
<i class="fa fa-list"></i> Coordinator List
</a>
</div>

</div>

</form>

</div>
</div>

</div>

</body>
</html>