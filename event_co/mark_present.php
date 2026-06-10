<?php
session_start();
include("../db.php");

if (!isset($_SESSION['coord_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['event_id'])) {
    die("Invalid Event");
}

$event_id = $_GET['event_id'];

/* ===============================
   GET EVENT DETAILS
================================= */
$event_sql = "SELECT * FROM event WHERE event_id='$event_id'";
$event_result = mysqli_query($conn,$event_sql);
$event = mysqli_fetch_assoc($event_result);

if(!$event){
    die("Event not found");
}

$mode = strtolower($event['participation_mode']); // team / individual

/* ===============================
   SAVE PRESENT STATUS
   stores 'present' in remarks column
================================= */
if(isset($_POST['mark_present']))
{
    mysqli_query($conn,"
    UPDATE enrollments
    SET remarks=''
    WHERE event_id='$event_id'
    AND status='joined'
    ");

    if(isset($_POST['enroll_id']))
    {
        foreach($_POST['enroll_id'] as $id)
        {
            mysqli_query($conn,"
            UPDATE enrollments
            SET remarks='present'
            WHERE enroll_id='$id'
            ");
        }
    }

    $msg = "Attendance marked successfully.";
}

/* ===============================
   ONLY ACTIVE ENROLLMENTS
   joined + not withdrawn
================================= */
$list_sql = "
SELECT *
FROM enrollments
WHERE event_id='$event_id'
AND status='joined'
AND (
    withdrawn_at IS NULL
    OR withdrawn_at=''
    OR withdrawn_at='0000-00-00 00:00:00'
)
ORDER BY joined_at ASC
";

$list_result = mysqli_query($conn,$list_sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Mark Present</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<style>
body{
background:#f5f7fa;
font-family:Arial;
padding:30px;
}

.box{
max-width:950px;
margin:auto;
background:#fff;
padding:30px;
border-radius:14px;
box-shadow:0 8px 20px rgba(0,0,0,.08);
}

.title{
font-size:28px;
font-weight:700;
margin-bottom:8px;
}

.sub{
color:#666;
margin-bottom:25px;
}

.list-box{
border:1px solid #ddd;
border-radius:10px;
max-height:520px;
overflow:auto;
padding:10px;
margin-bottom:20px;
}

.item{
padding:14px;
border-bottom:1px solid #eee;
display:flex;
gap:12px;
align-items:flex-start;
}

.item:last-child{
border-bottom:none;
}

.name{
font-weight:700;
}

.small{
font-size:14px;
color:#666;
}

.present-badge{
font-size:12px;
background:#198754;
color:#fff;
padding:3px 8px;
border-radius:20px;
margin-left:10px;
}

.save-btn{
width:100%;
padding:12px;
font-weight:700;
}

.back{
display:inline-block;
margin-top:15px;
text-decoration:none;
}
</style>
</head>

<body>

<div class="box">

<div class="title">Mark Present</div>

<div class="sub">
Event: <b><?php echo $event['event_name']; ?></b><br>
Mode: <b><?php echo ucfirst($mode); ?></b>
</div>

<?php if(isset($msg)){ ?>
<div class="alert alert-success">
<?php echo $msg; ?>
</div>
<?php } ?>

<form method="post">

<div class="list-box">

<?php
if(mysqli_num_rows($list_result) > 0)
{
    while($row = mysqli_fetch_assoc($list_result))
    {
        $checked = ($row['remarks'] == 'present') ? "checked" : "";
?>

<div class="item">

<input type="checkbox"
name="enroll_id[]"
value="<?php echo $row['enroll_id']; ?>"
<?php echo $checked; ?>>

<div style="width:100%;">

<?php if($mode == "team") { ?>

<div class="name">
<?php echo $row['team_name']; ?>

<?php if($row['remarks']=='present'){ ?>
<span class="present-badge">Present</span>
<?php } ?>

</div>

<div class="small">
Name: <?php echo $row['full_name']; ?><br>
Members: <?php echo $row['total_members']; ?>
</div>

<?php } else { ?>

<div class="name">
<?php echo $row['full_name']; ?>

<?php if($row['remarks']=='present'){ ?>
<span class="present-badge">Present</span>
<?php } ?>

</div>

<div class="small">
<?php echo $row['user_email']; ?><br>
<?php echo $row['phone']; ?>
</div>

<?php } ?>

</div>

</div>

<?php
    }
}
else
{
    echo "<div class='text-danger'>No active participants found.</div>";
}
?>

</div>

<button type="submit"
name="mark_present"
class="btn btn-success save-btn">
Save Present
</button>

</form>

<a href="dashboard.php" class="back">
← Back to Dashboard
</a>

</div>

</body>
</html>