<?php
session_start();
include("../db.php");

if (!isset($_SESSION['coord_id'])) {
    header("Location: ../login.php");
    exit();
}

$coord_id = $_SESSION['coord_id'];

if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$event_id = $_GET['id'];

/* Only assigned coordinator can manage */
$check = "SELECT e.*
          FROM event e
          INNER JOIN event_coordinator_map m
          ON e.event_id = m.event_id
          WHERE e.event_id='$event_id'
          AND m.event_co_id='$coord_id'";

$result = mysqli_query($conn,$check);

if(mysqli_num_rows($result)==0)
{
    die("Access Denied");
}

$row = mysqli_fetch_assoc($result);

/* UPDATE EVENT */
if(isset($_POST['update']))
{
    $reg_open_date    = $_POST['reg_open_date'];
    $reg_close_date   = $_POST['reg_close_date'];
    $event_date       = $_POST['event_date'];
    $event_time       = $_POST['event_time'];
    $event_location   = $_POST['event_location'];
    $max_participants = $_POST['max_participants'];
    $min_participants = $_POST['min_participants'];
    $fees             = $_POST['fees'];

    $participation_mode    = $_POST['participation_mode'];
    $allowed_gender        = $_POST['allowed_gender'];
    $required_gender_member= $_POST['required_gender_member'];

    if($fees < 0)
    {
        $fees = 0;
    }

    $sql = "UPDATE event SET

            reg_open_date='$reg_open_date',
            reg_close_date='$reg_close_date',
            event_date='$event_date',
            event_time='$event_time',
            event_location='$event_location',
            max_participants='$max_participants',
            min_participants='$min_participants',
            fees='$fees',

            participation_mode='$participation_mode',
            allowed_gender='$allowed_gender',
            required_gender_member='$required_gender_member'

            WHERE event_id='$event_id'";

    if(mysqli_query($conn,$sql))
    {
        echo "<script>
        alert('Event Updated Successfully');
        window.location='dashboard.php';
        </script>";
        exit();
    }
    else
    {
        echo "Update Failed";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Event</title>

<style>
body{
font-family:Arial;
background:#f4f6f9;
margin:0;
padding:0;
}

.container{
width:700px;
margin:40px auto;
background:white;
padding:25px;
border-radius:10px;
box-shadow:0 2px 10px rgba(0,0,0,.1);
}

h2{
margin-bottom:20px;
color:#2c3e50;
}

label{
font-weight:bold;
display:block;
margin-bottom:5px;
}

input,select{
width:100%;
padding:10px;
margin-bottom:15px;
border:1px solid #ccc;
border-radius:5px;
}

button{
background:#27ae60;
color:white;
padding:12px 18px;
border:none;
cursor:pointer;
border-radius:5px;
}

button:hover{
background:#219150;
}

.readonly{
background:#eee;
}
</style>
</head>

<body>

<div class="container">

<h2>Manage Event</h2>

<form method="POST">

<label>Event Name</label>
<input type="text"
value="<?php echo $row['event_name']; ?>"
readonly class="readonly">

<label>Event Type</label>
<input type="text"
value="<?php echo $row['event_type']; ?>"
readonly class="readonly">

<label>Description</label>
<input type="text"
value="<?php echo $row['description']; ?>"
readonly class="readonly">

<label>Registration Open Date</label>
<input type="date"
name="reg_open_date"
value="<?php echo $row['reg_open_date']; ?>">

<label>Registration Close Date</label>
<input type="date"
name="reg_close_date"
value="<?php echo $row['reg_close_date']; ?>">

<label>Event Date</label>
<input type="date"
name="event_date"
value="<?php echo $row['event_date']; ?>">

<label>Event Time</label>
<input type="time"
name="event_time"
value="<?php echo $row['event_time']; ?>">

<label>Event Location</label>
<input type="text"
name="event_location"
value="<?php echo $row['event_location']; ?>">

<label>Mode of Participation</label>
<select name="participation_mode">

<option value="Individual"
<?php if($row['participation_mode']=="Individual") echo "selected"; ?>>
Individual
</option>

<option value="Team"
<?php if($row['participation_mode']=="Team") echo "selected"; ?>>
Team
</option>

</select>

<label>Allowed Gender Participation</label>
<select name="allowed_gender">

<option value="Any"
<?php if($row['allowed_gender']=="Any") echo "selected"; ?>>
Any
</option>

<option value="Male"
<?php if($row['allowed_gender']=="Male") echo "selected"; ?>>
Male
</option>

<option value="Female"
<?php if($row['allowed_gender']=="Female") echo "selected"; ?>>
Female
</option>

<option value="Other"
<?php if($row['allowed_gender']=="Other") echo "selected"; ?>>
Other
</option>

</select>

<label>Team Must Contain At Least One</label>
<select name="required_gender_member">

<option value="None"
<?php if($row['required_gender_member']=="None") echo "selected"; ?>>
None
</option>

<option value="Male"
<?php if($row['required_gender_member']=="Male") echo "selected"; ?>>
Male
</option>

<option value="Female"
<?php if($row['required_gender_member']=="Female") echo "selected"; ?>>
Female
</option>

<option value="Other"
<?php if($row['required_gender_member']=="Other") echo "selected"; ?>>
Other
</option>

</select>

<label>Maximum Participants</label>
<input type="number"
min="1"
name="max_participants"
value="<?php echo $row['max_participants']; ?>">

<label>Minimum Participants</label>
<input type="number"
min="1"
name="min_participants"
value="<?php echo $row['min_participants']; ?>">

<label>Participation Fee</label>
<input type="number"
step="0.01"
min="0"
name="fees"
value="<?php echo $row['fees']; ?>">

<button type="submit" name="update">
Update Event
</button>

</form>

</div>

</body>
</html>