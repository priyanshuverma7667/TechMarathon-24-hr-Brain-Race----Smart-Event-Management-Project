<?php
session_start();
include("../db.php");

if (!isset($_SESSION['coord_id'])) {
    header("Location: ../login.php");
    exit();
}

$coord_id = $_SESSION['coord_id'];

/*
participants.php
Added:
1. Gender Filter
2. Team Leader Column
*/

/* Assigned Events */
$event_sql = "
SELECT e.event_id, e.event_name
FROM event e
INNER JOIN event_coordinator_map m
ON e.event_id = m.event_id
WHERE m.event_co_id='$coord_id'
ORDER BY e.event_name ASC
";

$event_result = mysqli_query($conn,$event_sql);

/* Filters */
$event_id = $_GET['event_id'] ?? '';
$search   = $_GET['search'] ?? '';
$mode     = $_GET['mode'] ?? '';
$status   = $_GET['status'] ?? '';
$gender   = $_GET['gender'] ?? '';

$where = " WHERE 1=1 ";

$where .= " AND e.event_id IN (
SELECT event_id
FROM event_coordinator_map
WHERE event_co_id='$coord_id'
)";

if($event_id != '')
{
    $where .= " AND e.event_id='$event_id'";
}

if($search != '')
{
    $where .= " AND (
        en.full_name LIKE '%$search%' OR
        en.user_email LIKE '%$search%' OR
        en.phone LIKE '%$search%'
    )";
}

if($mode != '')
{
    $where .= " AND en.participation_mode='$mode'";
}

if($status != '')
{
    $where .= " AND en.status='$status'";
}

if($gender != '')
{
    $where .= " AND u.gender='$gender'";
}

/* Main Query */
$sql = "
SELECT
en.*,
e.event_name,
u.gender,
u.organization_name,
u.department,
u.city,

leader.full_name AS leader_name

FROM enrollments en

INNER JOIN event e
ON en.event_id = e.event_id

LEFT JOIN users u
ON en.user_id = u.user_id

LEFT JOIN users leader
ON en.team_leader_id = leader.user_id

$where

ORDER BY en.joined_at DESC
";

$result = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Participants List</title>

<style>
body{
font-family:Arial;
background:#f4f6f9;
margin:0;
padding:0;
}

.header{
background:#2c3e50;
color:white;
padding:20px;
}

.container{
width:98%;
margin:auto;
margin-top:20px;
}

.card{
background:white;
padding:20px;
border-radius:10px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
margin-bottom:20px;
overflow:auto;
}

input,select{
padding:10px;
margin-right:10px;
margin-bottom:10px;
}

button{
padding:10px 15px;
background:#27ae60;
color:white;
border:none;
cursor:pointer;
}

table{
width:100%;
border-collapse:collapse;
font-size:14px;
}

th,td{
padding:10px;
border:1px solid #ddd;
text-align:left;
}

th{
background:#3498db;
color:white;
}
.header a{
color:white;
text-decoration:none;
margin-right:15px;
}
</style>
</head>

<body>

<div class="header">
<h2>Participants List</h2>
<br>
<a href="dashboard.php">Dashboard</a>
<a href="participant_list.php">Participants</a>
<a href="stats.php">Stats</a>
<a href="logout.php">Logout</a>
</div>

<div class="container">

<div class="card">

<form method="GET">

<select name="event_id">
<option value="">All Events</option>

<?php
while($ev=mysqli_fetch_assoc($event_result))
{
$sel = ($event_id == $ev['event_id']) ? "selected" : "";

echo "<option value='{$ev['event_id']}' $sel>
{$ev['event_name']}
</option>";
}
?>

</select>

<input type="text"
name="search"
placeholder="Name / Email / Phone"
value="<?php echo $search; ?>">

<select name="mode">
<option value="">All Modes</option>
<option value="Individual" <?php if($mode=="Individual") echo "selected"; ?>>
Individual
</option>
<option value="Team" <?php if($mode=="Team") echo "selected"; ?>>
Team
</option>
</select>

<select name="gender">
<option value="">All Gender</option>
<option value="Male" <?php if($gender=="Male") echo "selected"; ?>>
Male
</option>
<option value="Female" <?php if($gender=="Female") echo "selected"; ?>>
Female
</option>
<option value="Other" <?php if($gender=="Other") echo "selected"; ?>>
Other
</option>
</select>

<select name="status">
<option value="">All Status</option>
<option value="Joined" <?php if($status=="Joined") echo "selected"; ?>>
Joined
</option>
<option value="Withdrawn" <?php if($status=="Withdrawn") echo "selected"; ?>>
Withdrawn
</option>
</select>

<button type="submit">Filter</button>

</form>

</div>

<div class="card">

<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>Gender</th>
<th>Organization</th>
<th>Department</th>
<th>City</th>
<th>Event</th>
<th>Mode</th>
<th>Team Name</th>
<th>Members</th>
<th>Team Leader</th>
<th>Status</th>
<th>Joined At</th>
</tr>

<?php
if(mysqli_num_rows($result) > 0)
{
while($row=mysqli_fetch_assoc($result))
{
$leader = $row['leader_name'];

if($leader == '' || $leader == NULL)
{
    $leader = '-';
}

echo "
<tr>
<td>{$row['enroll_id']}</td>
<td>{$row['full_name']}</td>
<td>{$row['user_email']}</td>
<td>{$row['phone']}</td>
<td>{$row['gender']}</td>
<td>{$row['organization_name']}</td>
<td>{$row['department']}</td>
<td>{$row['city']}</td>
<td>{$row['event_name']}</td>
<td>{$row['participation_mode']}</td>
<td>{$row['team_name']}</td>
<td>{$row['total_members']}</td>
<td>$leader</td>
<td>{$row['status']}</td>
<td>{$row['joined_at']}</td>
</tr>";
}
}
else
{
echo "<tr><td colspan='15'>No participants found.</td></tr>";
}
?>

</table>

</div>

</div>

</body>
</html>