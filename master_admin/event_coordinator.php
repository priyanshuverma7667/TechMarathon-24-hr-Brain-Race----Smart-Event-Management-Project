<?php
session_start();
include("../db.php");

/* ONLY ADMIN */
if(!isset($_SESSION['admin_id']))
{
    header("Location: index.html");
    exit();
}

$event_sql = "SELECT event_id,event_name FROM event";
$event_result = mysqli_query($conn,$event_sql);

$co_sql = "SELECT co_id,co_name FROM coordinator";
$co_result = mysqli_query($conn,$co_sql);

$coordinator = array();

while($row = mysqli_fetch_assoc($co_result))
{
    $coordinator[] = $row;
}

/* message */
$msg  = "";
$type = "";
if(isset($_GET["msg"])){
   $msg = $_GET["msg"];
   if ($msg == 0){
    $msg = "Coordinator is already assigned to this event.";
    $type = "danger";
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Assign Event Coordinator</title>

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
max-width:700px;
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

.form-select{
height:50px;
border-radius:12px;
border:1px solid #d1d5db;
padding:10px;
}

.form-select:focus{
box-shadow:none;
border-color:#2563eb;
}

.co-row{
margin-bottom:15px;
}

.add-btn{
border:none;
padding:10px 18px;
border-radius:12px;
font-weight:600;
background:#16a34a;
color:#fff;
transition:.3s;
}

.add-btn:hover{
transform:translateY(-2px);
}

.submit-btn{
width:100%;
padding:14px;
border:none;
border-radius:14px;
font-weight:700;
color:#fff;
background:linear-gradient(135deg,#2563eb,#06b6d4);
margin-top:15px;
transition:.3s;
}

.submit-btn:hover{
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
color:#111827;
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
<h2><i class="fa fa-link"></i> Assign Coordinator</h2>
<p>Map coordinators with events easily</p>
</div>
<?php if($msg != "") { ?>

<div class="alert alert-<?php echo $type; ?> alert-dismissible fade show mb-4" role="alert">

<i class="fa <?php echo ($type=='success') ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>

<?php echo $msg; ?>

<button type="button" class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php } ?>
<form method="post" action="../main.php?flag=3">

<div class="mb-3">
<label class="form-label">SELECT EVENT</label>

<select name="event_id" class="form-select" required>
<option value="">Select Event</option>

<?php
while($row = mysqli_fetch_assoc($event_result))
{
?>

<option value="<?php echo $row['event_id']; ?>">
<?php echo $row['event_name']; ?>
</option>

<?php } ?>

</select>
</div>

<div id="coordinator_box">

<div class="co-row">
<label class="form-label">CO-ORDINATOR 1</label>

<select name="event_co_id[]" class="form-select co_select" required>

<option value="">Select Coordinator</option>

<?php
foreach($coordinator as $co)
{
?>

<option value="<?php echo $co['co_id']; ?>">
<?php echo $co['co_name']; ?>
</option>

<?php } ?>

</select>
</div>

</div>

<button type="button"
onclick="addCoordinator()"
class="add-btn">
<i class="fa fa-plus"></i> Add More
</button>

<button type="submit" class="submit-btn">
<i class="fa fa-check-circle"></i> Assign Coordinator
</button>

</form>

<a href="dashboard.php" class="back-btn">
<i class="fa fa-arrow-left"></i> Back to Dashboard
</a>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let count = 1;

function addCoordinator()
{
count++;

let div = document.createElement("div");
div.className = "co-row";

div.innerHTML = `
<label class="form-label">CO-ORDINATOR ${count}</label>

<select name="event_co_id[]" class="form-select co_select">
<option value="">Select Coordinator</option>

<?php
foreach($coordinator as $co)
{
?>
<option value="<?php echo $co['co_id']; ?>">
<?php echo $co['co_name']; ?>
</option>
<?php
}
?>

</select>
`;

document.getElementById("coordinator_box").appendChild(div);

updateEvents();
}

function updateEvents()
{
let selects = document.querySelectorAll(".co_select");

selects.forEach(function(sel){
sel.addEventListener("change", hideSelected);
});

hideSelected();
}

function hideSelected()
{
let selects = document.querySelectorAll(".co_select");
let selected = [];

selects.forEach(function(sel){
if(sel.value != "")
selected.push(sel.value);
});

selects.forEach(function(sel){

let current = sel.value;

for(let i=0;i<sel.options.length;i++)
{
let val = sel.options[i].value;

if(val == "")
continue;

if(selected.includes(val) && val != current)
sel.options[i].style.display = "none";
else
sel.options[i].style.display = "block";
}
});
}

updateEvents();
</script>

</body>
</html>