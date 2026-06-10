<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}


include("../db.php");

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$enroll_id = intval($_GET['id']);

/* Get certificate data + FULL NAME from users table */
$sql = mysqli_query($conn,"
SELECT en.*,
       u.full_name,
       e.event_name,
       e.event_date,
       en.is_winner
FROM enrollments en
INNER JOIN event e
ON en.event_id = e.event_id
INNER JOIN users u
ON en.user_id = u.user_id
WHERE en.enroll_id='$enroll_id'
AND en.user_id='$user_id'
LIMIT 1
");

if(mysqli_num_rows($sql)==0)
{
    die("Certificate not found.");
}

$row = mysqli_fetch_assoc($sql);

/* Only after event ended */
if($row['event_date'] >= date("Y-m-d"))
{
    die("Certificate available after event ends.");
}

/* Only Present / Winner */
if(
    $row['remarks'] != 'winner' &&
    $row['remarks'] != 'present'
)
{
    die("Certificate not available.");
}

/* Certificate Type */
if($row['remarks'] == 'present' && $row['is_winner'] == 1)
{
    $title = "ACHIEVEMENT CERTIFICATE";
    $subtitle = "Winner Certificate";
}
elseif($row['remarks'] == 'present' && $row['is_winner'] == 0)
{
    $title = "PARTICIPATION CERTIFICATE";
    $subtitle = "Participation Certificate";
}

/* FULL NAME from users table */
$participant = $row['full_name'];

$event_name = $row['event_name'];
$event_date = date("d M Y", strtotime($row['event_date']));
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Certificate</title>

<style>
body{
margin:0;
padding:0;
font-family:Georgia, serif;
background:#f2f2f2;
}

.wrapper{
width:1100px;
height:750px;
margin:30px auto;
background:#fff;
border:18px solid #d4af37;
padding:40px;
box-sizing:border-box;
position:relative;
}

.title{
text-align:center;
font-size:42px;
font-weight:bold;
margin-top:40px;
color:#222;
}

.sub{
text-align:center;
font-size:22px;
margin-top:10px;
color:#555;
}

.text{
text-align:center;
font-size:24px;
margin-top:50px;
}

.name{
text-align:center;
font-size:42px;
font-weight:bold;
color:#198754;
margin-top:20px;
text-transform:uppercase;
}

.event{
text-align:center;
font-size:24px;
margin-top:35px;
line-height:1.8;
}

.footer{
position:absolute;
bottom:70px;
left:60px;
right:60px;
display:flex;
justify-content:space-between;
}

.sign{
width:250px;
text-align:center;
}

.line{
border-top:2px solid #000;
margin-bottom:8px;
}

.print-btn{
text-align:center;
margin:20px;
}

button{
padding:12px 25px;
font-size:18px;
border:none;
background:#0d6efd;
color:#fff;
border-radius:8px;
cursor:pointer;
}

@media print{
.print-btn{display:none;}
body{background:#fff;}
.wrapper{margin:0;}
}
</style>
</head>

<body>

<div class="print-btn">
<button onclick="window.print()">Download / Print Certificate</button>
</div>

<div class="wrapper">

<div class="title"><?php echo $title; ?></div>

<div class="sub"><?php echo $subtitle; ?></div>

<div class="text">
This certificate is proudly presented to
</div>

<div class="name">
<?php echo $participant; ?>
</div>

<div class="event">
for participation in<br>

<b><?php echo $event_name; ?></b><br>

held on <?php echo $event_date; ?>

<?php
if($row['remarks']=='winner')
{
echo "<br><span style='font-size:28px;color:#dc3545;font-weight:bold;'>🏆 WINNER 🏆</span>";
}
?>
</div>

<div class="footer">

<div class="sign">
<div class="line"></div>
Coordinator Signature
</div>

<div class="sign">
<div class="line"></div>
Authorized Signature
</div>

</div>

</div>

</body>
</html>