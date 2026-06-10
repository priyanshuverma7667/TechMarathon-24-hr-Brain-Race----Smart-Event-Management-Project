<?php
session_start();
include("../db.php");

if (!isset($_SESSION['coord_id'])) {
    header("Location: ../login.php");
    exit();
}

$coord_id   = $_SESSION['coord_id'];
$coord_name = $_SESSION['coord_name'];

/* =========================
   TOP SUMMARY
========================= */

/* Assigned Events */
$q1 = mysqli_query($conn,"
SELECT COUNT(*) total
FROM event_coordinator_map
WHERE event_co_id='$coord_id'
");
$total_events = mysqli_fetch_assoc($q1)['total'];

/* Total Registrations */
$q2 = mysqli_query($conn,"
SELECT COUNT(*) total
FROM enrollments en
INNER JOIN event_coordinator_map m
ON en.event_id = m.event_id
WHERE m.event_co_id='$coord_id'
AND en.status='Joined'
");
$total_reg = mysqli_fetch_assoc($q2)['total'];

/* Winners */
$q3 = mysqli_query($conn,"
SELECT COUNT(*) total
FROM enrollments en
INNER JOIN event_coordinator_map m
ON en.event_id = m.event_id
WHERE m.event_co_id='$coord_id'
AND en.is_winner='1'
");
$total_winners = mysqli_fetch_assoc($q3)['total'];

/* Team Entries */
$q4 = mysqli_query($conn,"
SELECT COUNT(*) total
FROM enrollments en
INNER JOIN event_coordinator_map m
ON en.event_id = m.event_id
WHERE m.event_co_id='$coord_id'
AND en.participation_mode='Team'
AND en.status='Joined'
");
$total_team = mysqli_fetch_assoc($q4)['total'];

/* Individual Entries */
$q5 = mysqli_query($conn,"
SELECT COUNT(*) total
FROM enrollments en
INNER JOIN event_coordinator_map m
ON en.event_id = m.event_id
WHERE m.event_co_id='$coord_id'
AND en.participation_mode='Individual'
AND en.status='Joined'
");
$total_individual = mysqli_fetch_assoc($q5)['total'];

/* Male */
$q6 = mysqli_query($conn,"
SELECT COUNT(*) total
FROM enrollments en
INNER JOIN users u ON en.user_id=u.user_id
INNER JOIN event_coordinator_map m ON en.event_id=m.event_id
WHERE m.event_co_id='$coord_id'
AND en.status='Joined'
AND u.gender='Male'
");
$total_male = mysqli_fetch_assoc($q6)['total'];

/* Female */
$q7 = mysqli_query($conn,"
SELECT COUNT(*) total
FROM enrollments en
INNER JOIN users u ON en.user_id=u.user_id
INNER JOIN event_coordinator_map m ON en.event_id=m.event_id
WHERE m.event_co_id='$coord_id'
AND en.status='Joined'
AND u.gender='Female'
");
$total_female = mysqli_fetch_assoc($q7)['total'];

/* Other */
$q8 = mysqli_query($conn,"
SELECT COUNT(*) total
FROM enrollments en
INNER JOIN users u ON en.user_id=u.user_id
INNER JOIN event_coordinator_map m ON en.event_id=m.event_id
WHERE m.event_co_id='$coord_id'
AND en.status='Joined'
AND u.gender='Other'
");
$total_other = mysqli_fetch_assoc($q8)['total'];

/* EVENT LIST */
$events = mysqli_query($conn,"
SELECT e.*
FROM event e
INNER JOIN event_coordinator_map m
ON e.event_id = m.event_id
WHERE m.event_co_id='$coord_id'
ORDER BY e.event_date ASC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Coordinator Statistics</title>

<style>
*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial;
}

body{
background:#f4f6f9;
}

.header{
background:#2c3e50;
color:white;
padding:20px;
}

.header a{
color:white;
text-decoration:none;
margin-right:15px;
}

.container{
width:95%;
margin:auto;
margin-top:25px;
}

.grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px;
margin-bottom:25px;
}

.card{
background:white;
padding:20px;
border-radius:10px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.card h3{
font-size:15px;
margin-bottom:10px;
color:#555;
}

.count{
font-size:30px;
font-weight:bold;
color:#3498db;
}

table{
width:100%;
border-collapse:collapse;
background:white;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

th,td{
padding:12px;
border:1px solid #ddd;
text-align:left;
}

th{
background:#3498db;
color:white;
}

.progress{
width:100%;
background:#ddd;
height:18px;
border-radius:20px;
overflow:hidden;
}

.bar{
height:18px;
background:#27ae60;
}

.badge{
padding:5px 10px;
border-radius:5px;
color:white;
font-size:12px;
}

.win{
background:green;
}

.pending{
background:red;
}
</style>
</head>

<body>

<div class="header">
<h2>Statistics Dashboard - <?php echo $coord_name; ?></h2>
<br>
<a href="dashboard.php">Dashboard</a>
<a href="participant_list.php">Participants</a>
<a href="stats.php">Stats</a>
<a href="logout.php">Logout</a>
</div>

<div class="container">

<!-- TOP CARDS -->
<div class="grid">

<div class="card">
<h3>Assigned Events</h3>
<div class="count"><?php echo $total_events; ?></div>
</div>

<div class="card">
<h3>Total Registrations</h3>
<div class="count"><?php echo $total_reg; ?></div>
</div>

<div class="card">
<h3>Team Entries</h3>
<div class="count"><?php echo $total_team; ?></div>
</div>

<div class="card">
<h3>Individual Entries</h3>
<div class="count"><?php echo $total_individual; ?></div>
</div>

<div class="card">
<h3>Total Winners</h3>
<div class="count"><?php echo $total_winners; ?></div>
</div>

<div class="card">
<h3>Male Participants</h3>
<div class="count"><?php echo $total_male; ?></div>
</div>

<div class="card">
<h3>Female Participants</h3>
<div class="count"><?php echo $total_female; ?></div>
</div>

<div class="card">
<h3>Other Participants</h3>
<div class="count"><?php echo $total_other; ?></div>
</div>

</div>

<!-- EVENT WISE -->
<table>

<tr>
<th>ID</th>
<th>Event Name</th>
<th>Total</th>
<th>Male</th>
<th>Female</th>
<th>Other</th>
<th>Filled</th>
<th>Winner</th>
</tr>

<?php
while($row=mysqli_fetch_assoc($events))
{
    $event_id = $row['event_id'];

    /* total joined */
    $q = mysqli_query($conn,"
    SELECT COUNT(*) total
    FROM enrollments
    WHERE event_id='$event_id'
    AND status='Joined'
    ");
    $reg = mysqli_fetch_assoc($q)['total'];

    /* male */
    $q = mysqli_query($conn,"
    SELECT COUNT(*) total
    FROM enrollments en
    INNER JOIN users u ON en.user_id=u.user_id
    WHERE en.event_id='$event_id'
    AND en.status='Joined'
    AND u.gender='Male'
    ");
    $male = mysqli_fetch_assoc($q)['total'];

    /* female */
    $q = mysqli_query($conn,"
    SELECT COUNT(*) total
    FROM enrollments en
    INNER JOIN users u ON en.user_id=u.user_id
    WHERE en.event_id='$event_id'
    AND en.status='Joined'
    AND u.gender='Female'
    ");
    $female = mysqli_fetch_assoc($q)['total'];

    /* other */
    $q = mysqli_query($conn,"
    SELECT COUNT(*) total
    FROM enrollments en
    INNER JOIN users u ON en.user_id=u.user_id
    WHERE en.event_id='$event_id'
    AND en.status='Joined'
    AND u.gender='Other'
    ");
    $other = mysqli_fetch_assoc($q)['total'];

    /* fill % */
    $max = $row['max_participants'];

    if($max > 0)
        $percent = round(($reg / $max) * 100);
    else
        $percent = 0;

    if($percent > 100)
        $percent = 100;

    /* winner */
    $q = mysqli_query($conn,"
    SELECT COUNT(*) total
    FROM enrollments
    WHERE event_id='$event_id'
    AND is_winner='1'
    ");
    $winner = mysqli_fetch_assoc($q)['total'];

    if($winner > 0)
        $badge = "<span class='badge win'>Declared</span>";
    else
        $badge = "<span class='badge pending'>Pending</span>";

    echo "
    <tr>
        <td>{$row['event_id']}</td>
        <td>{$row['event_name']}</td>
        <td>$reg</td>
        <td>$male</td>
        <td>$female</td>
        <td>$other</td>

        <td>
            <div class='progress'>
                <div class='bar' style='width:{$percent}%'></div>
            </div>
            {$percent}%
        </td>

        <td>$badge</td>
    </tr>
    ";
}
?>

</table>

</div>

</body>
</html>