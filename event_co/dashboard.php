<?php
session_start();
include("../db.php");

if (!isset($_SESSION['coord_id'])) {
    header("Location: ../login.php");
    exit();
}

$coord_id   = $_SESSION['coord_id'];
$coord_name = $_SESSION['coord_name'] ?? 'Coordinator';

/* ===============================
GET ASSIGNED EVENTS
================================= */
$sql = "
SELECT e.*
FROM event e
INNER JOIN event_coordinator_map m
ON e.event_id = m.event_id
WHERE m.event_co_id='$coord_id'
ORDER BY e.event_date ASC
";

$result = mysqli_query($conn, $sql);
$total_events = mysqli_num_rows($result);

$today = date("Y-m-d");
?>

<!DOCTYPE html>
<html>
<head>
<title>Coordinator Dashboard</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

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
color:#fff;
padding:20px;
}

.container-box{
width:92%;
margin:auto;
margin-top:30px;
}

.card-box{
background:#fff;
padding:20px;
border-radius:10px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
margin-bottom:25px;
}

.count{
font-size:35px;
color:#3498db;
font-weight:bold;
}

table{
width:100%;
border-collapse:collapse;
}

th,td{
padding:12px;
border:1px solid #ddd;
text-align:left;
vertical-align:middle;
}

th{
background:#3498db;
color:#fff;
}

.top-menu{
margin-top:10px;
}

.top-menu a{
color:#fff;
text-decoration:none;
margin-right:15px;
}

.top-menu a:hover{
text-decoration:underline;
}

.btn-action{
padding:8px 12px;
color:#fff;
text-decoration:none;
border-radius:5px;
display:inline-block;
margin:2px;
font-size:14px;
}

.manage{
background:#27ae60;
}

.manage:hover{
background:#219150;
}

.winner{
background:#e67e22;
}

.winner:hover{
background:#ca6a10;
}

.present{
background:#3498db;
}

.present:hover{
background:#217dbb;
}

.running{
color:#16a34a;
font-weight:bold;
}

.ended{
color:#dc2626;
font-weight:bold;
}

.upcoming{
color:#2563eb;
font-weight:bold;
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">

<h2>Welcome, <?php echo $coord_name; ?></h2>

<div class="top-menu">
<a href="dashboard.php">Dashboard</a>
<a href="participant_list.php">Participants</a>
<a href="stats.php">Stats</a>
<a href="logout.php">Logout</a>
</div>

</div>

<!-- MAIN -->
<div class="container-box">

<!-- TOTAL -->
<div class="card-box">
<h3>Total Assigned Events</h3>
<div class="count"><?php echo $total_events; ?></div>
</div>

<!-- EVENTS TABLE -->
<div class="card-box">

<h3>My Events</h3>
<br>

<div class="table-responsive">

<table>

<tr>
<th>ID</th>
<th>Event Name</th>
<th>Type</th>
<th>Description</th>
<th>Event Date</th>
<th>Status</th>
<th>Actions</th>
</tr>

<?php
if($total_events > 0)
{
    while($row = mysqli_fetch_assoc($result))
    {
        $status = "Upcoming";

        /* ===============================
        STATUS CHECK
        ================================= */
        if($row['status'] == 'running')
        {
            $status = "Running";
        }
        elseif(
            $row['status'] == 'complete' ||
            (
                $row['event_date'] != '0000-00-00' &&
                $row['event_date'] < $today
            )
        )
        {
            $status = "Ended";
        }

        echo "<tr>";

        echo "<td>".$row['event_id']."</td>";
        echo "<td>".$row['event_name']."</td>";
        echo "<td>".$row['event_type']."</td>";
        echo "<td>".$row['description']."</td>";
        echo "<td>".$row['event_date']."</td>";

        /* STATUS COLOR */
        if($status == "Running")
        {
            echo "<td class='running'>Running</td>";
        }
        elseif($status == "Ended")
        {
            echo "<td class='ended'>Ended</td>";
        }
        else
        {
            echo "<td class='upcoming'>Upcoming</td>";
        }

        echo "<td>";

        /* MANAGE BUTTON */
        echo "
        <a class='btn-action manage'
        href='manage_event.php?id=".$row['event_id']."'>
        Manage
        </a>";

        /* RUNNING => MARK PRESENT */
        if($row['status'] == 'running')
        {
            echo "
            <a class='btn-action present'
            href='mark_present.php?event_id=".$row['event_id']."'>
            Mark Present
            </a>";
        }

        /* COMPLETE / ENDED => MARK WINNER */
        if(
            $row['status'] == 'complete' ||
            $status == 'Ended'
        )
        {
            echo "
            <a class='btn-action winner'
            href='winner.php?event_id=".$row['event_id']."'>
            Mark Winner
            </a>";
        }

        echo "</td>";

        echo "</tr>";
    }
}
else
{
    echo "
    <tr>
    <td colspan='7' class='text-center'>
    No events assigned.
    </td>
    </tr>";
}
?>

</table>

</div>
</div>

</div>

</body>
</html>