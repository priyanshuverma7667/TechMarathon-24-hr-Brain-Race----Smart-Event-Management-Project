<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Participant Registration</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
background:linear-gradient(135deg,#0d6efd,#20c997);
min-height:100vh;
display:flex;
align-items:center;
justify-content:center;
font-family:Arial;
padding:20px;
}

.card{
width:100%;
max-width:850px;
border:none;
border-radius:18px;
overflow:hidden;
}

.card-header{
background:#0d6efd;
color:#fff;
text-align:center;
padding:18px;
font-size:24px;
font-weight:700;
}

.form-control,
.form-select{
height:45px;
border-radius:10px;
}

textarea.form-control{
height:auto;
}

.btn-register{
height:48px;
font-size:18px;
font-weight:600;
border-radius:10px;
}

.small-link{
text-decoration:none;
font-weight:600;
}
</style>
</head>

<body>

<div class="card shadow-lg">

<div class="card-header">
Participant Registration
</div>

<div class="card-body p-4">

<form action="../main.php?flag=10" method="POST" enctype="multipart/form-data">

<div class="row">

<div class="col-md-6 mb-3">
<label class="form-label">First Name</label>
<input type="text" name="first_name" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Last Name</label>
<input type="text" name="last_name" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Username</label>
<input type="text" name="username" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Email Address</label>
<input type="email" name="user_email" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Phone Number</label>
<input type="text" name="phone" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Password</label>
<input type="password" name="user_pass" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Gender</label>
<select name="gender" class="form-select" required>
<option value="">Select Gender</option>
<option value="Male">Male</option>
<option value="Female">Female</option>
<option value="Other">Other</option>
</select>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Date of Birth</label>
<input type="date" name="date_of_birth" class="form-control" required>
</div>


</div>

<div class="d-grid mt-3">
<button type="submit" class="btn btn-primary btn-register">
Create Account
</button>
</div>

<div class="text-center mt-3">
Already have an account?
<a href="index.html" class="small-link">Login Here</a>
</div>

</form>

</div>
</div>

</body>
</html>