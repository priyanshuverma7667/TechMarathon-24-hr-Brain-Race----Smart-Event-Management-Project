<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

include('../db.php');

$user_id = $_SESSION['user_id'];

/* USER DETAILS */
$uq = mysqli_query($conn,"
SELECT * FROM users
WHERE user_id='$user_id'
LIMIT 1
");

$user = mysqli_fetch_assoc($uq);

$user_name   = $user['full_name'];
$user_email  = $user['user_email'];
$user_gender = $user['gender'];

$event_id = $_GET['id'] ?? '';

if($event_id==''){
    die("Invalid Event");
}

/* EVENT DETAILS */
$q = mysqli_query($conn,"
SELECT * FROM event
WHERE event_id='$event_id'
LIMIT 1
");

$event = mysqli_fetch_assoc($q);

if(!$event){
    die("Event Not Found");
}

/* ALREADY JOINED */
$chk = mysqli_query($conn,"
SELECT enroll_id FROM enrollments
WHERE user_id='$user_id'
AND event_id='$event_id'
AND status='Joined'
LIMIT 1
");

if(mysqli_num_rows($chk)>0){
    echo "<script>
    alert('You already joined this event');
    window.location='dashboard.php';
    </script>";
    exit();
}

/* REGISTRATION CHECK */
$today = date("Y-m-d");

if($today < $event['reg_open_date']){
    echo "<script>
    alert('Registration not started');
    window.location='dashboard.php';
    </script>";
    exit();
}

if($today > $event['reg_close_date']){
    echo "<script>
    alert('Registration closed');
    window.location='dashboard.php';
    </script>";
    exit();
}

/* GENDER CHECK */
if($event['allowed_gender']!='Any'){
    if(strtolower($user_gender) != strtolower($event['allowed_gender'])){
        echo "<script>
        alert('Only {$event['allowed_gender']} participants allowed');
        window.location='dashboard.php';
        </script>";
        exit();
    }
}

$amount = (float)$event['fees'];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Enroll Event</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<style>
body{
    background:#f4f6f9;
}
.card{
    border-radius:16px;
}
</style>
</head>

<body>

<div class="container py-5">
<div class="row justify-content-center">
<div class="col-md-10">

<div class="card shadow border-0">

<div class="card-header bg-success text-white">
<h4 class="mb-0">Enroll Event</h4>
</div>

<div class="card-body">

<!-- EVENT DETAILS -->
<div class="alert alert-primary">

<h5><?php echo $event['event_name']; ?></h5>

<div class="row">

<div class="col-md-6">
<strong>Mode:</strong>
<?php echo $event['participation_mode']; ?>
</div>

<div class="col-md-6">
<strong>Fee:</strong>
₹<?php echo number_format($event['fees'],2); ?>
</div>

<div class="col-md-6 mt-2">
<strong>Date:</strong>
<?php echo date('d M Y',strtotime($event['event_date'])); ?>
</div>

<div class="col-md-6 mt-2">
<strong>Location:</strong>
<?php echo $event['event_location']; ?>
</div>

</div>
</div>

<form id="enrollForm" action="../main.php?flag=16" method="POST">

<input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
<input type="hidden" name="participation_mode" value="<?php echo $event['participation_mode']; ?>">
<input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">

<!-- USER DETAILS -->
<h5 class="text-primary mb-3">Your Details</h5>

<div class="row">

<div class="col-md-6 mb-3">
<label>Name</label>
<input type="text" class="form-control" value="<?php echo $user_name; ?>" readonly>
</div>

<div class="col-md-6 mb-3">
<label>Email</label>
<input type="text" class="form-control" value="<?php echo $user_email; ?>" readonly>
</div>

<div class="col-md-6 mb-3">
<label>Gender</label>
<input type="text" class="form-control" value="<?php echo $user_gender; ?>" readonly>
</div>

<div class="col-md-6 mb-3">
<label>Phone</label>
<input type="text" name="phone" class="form-control" required>
</div>

</div>

<!-- TEAM MODE -->
<?php if(strtolower(trim($event['participation_mode'])) == 'team'){ ?>

<hr>

<h5 class="text-success mb-3">Team Details</h5>

<div class="row">

<div class="col-md-6 mb-3">
<label>Team Name</label>
<input type="text" name="team_name" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>Total Members</label>
<input type="number"
name="members"
class="form-control"
min="<?php echo $event['min_participants']; ?>"
max="<?php echo $event['max_participants']; ?>"
required>
</div>

<div class="col-md-12">
<div class="alert alert-warning">
You are automatically Team Leader.
</div>
</div>

<div class="col-md-6 mb-3">
<label>Member 1 Email</label>
<input type="email"
name="member1_email"
class="form-control"
placeholder="Enter registered email">
</div>

<div class="col-md-6 mb-3">
<label>Member 2 Email</label>
<input type="email"
name="member2_email"
class="form-control"
placeholder="Enter registered email">
</div>

<div class="col-md-6 mb-3">
<label>Member 3 Email</label>
<input type="email"
name="member3_email"
class="form-control"
placeholder="Enter registered email">
</div>

<div class="col-md-6 mb-3">
<label>Member 4 Email</label>
<input type="email"
name="member4_email"
class="form-control"
placeholder="Enter registered email">
</div>

</div>

<?php } ?>

<!-- REMARKS -->
<div class="mb-3">
<label>Remarks</label>
<textarea name="remarks" class="form-control"></textarea>
</div>

<!-- BUTTONS -->
<?php if($amount > 0){ ?>

<button type="button" onclick="payNow()" class="btn btn-success">
Pay ₹<?php echo number_format($amount,2); ?> & Enroll
</button>

<?php } else { ?>

<button type="submit" class="btn btn-success">
Confirm Enrollment
</button>

<?php } ?>

<a href="dashboard.php" class="btn btn-secondary">
Back
</a>

</form>

</div>
</div>

</div>
</div>
</div>

<script>
function payNow()
{
    var options = {
        "key": "rzp_test_RGJ1hfRVhqu7MS",
        "amount": "<?php echo $amount*100; ?>",
        "currency": "INR",
        "name": "Event Registration",
        "description": "<?php echo $event['event_name']; ?>",

        "handler": function (response){

            document.getElementById("razorpay_payment_id").value =
            response.razorpay_payment_id;

            document.getElementById("enrollForm").submit();
        },

        "prefill": {
            "name": "<?php echo $user_name; ?>",
            "email": "<?php echo $user_email; ?>"
        },

        "theme": {
            "color": "#198754"
        }
    };

    var rzp1 = new Razorpay(options);
    rzp1.open();
}
</script>

</body>
</html>