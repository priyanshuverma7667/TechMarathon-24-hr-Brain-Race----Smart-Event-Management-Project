<?php
session_start();
include("../db.php");

/* PHPMailer */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

if (!isset($_SESSION['coord_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['event_id'])) {
    die("Invalid Event");
}

$event_id = $_GET['event_id'];

/* ===============================
GET EVENT
================================= */
$event_q = mysqli_query($conn,"
SELECT * FROM event
WHERE event_id='$event_id'
LIMIT 1
");

$event = mysqli_fetch_assoc($event_q);

if(!$event){
    die("Event not found");
}

$mode = strtolower($event['participation_mode']);

/* ===============================
MAIL FUNCTION
================================= */
function sendMailToUser($email,$name,$subject,$body)
{
    try{
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            return false;
        }

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = "smtp.gmail.com";
        $mail->SMTPAuth   = true;
        $mail->Username   = "priyanshu76670@gmail.com";
        $mail->Password   = "dpeixoaddetvqiem";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->SMTPOptions = [
            'ssl'=>[
                'verify_peer'=>false,
                'verify_peer_name'=>false,
                'allow_self_signed'=>true
            ]
        ];

        $mail->setFrom(
            "priyanshu76670@gmail.com",
            "Smart Event"
        );

        $mail->addAddress($email,$name);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();

        return true;

    }catch(Exception $e){
        return false;
    }
}

/* ===============================
SAVE WINNER
================================= */
if(isset($_POST['save_winner']))
{
    /* OLD WINNERS */
    $old_winners = [];

    $old_q = mysqli_query($conn,"
    SELECT enroll_id,team_name
    FROM enrollments
    WHERE event_id='$event_id'
    AND is_winner='1'
    ");

    while($r=mysqli_fetch_assoc($old_q))
    {
        if($mode=="team"){
            $old_winners[] = $r['team_name'];
        }else{
            $old_winners[] = $r['enroll_id'];
        }
    }

    /* RESET */
    mysqli_query($conn,"
    UPDATE enrollments
    SET is_winner='0'
    WHERE event_id='$event_id'
    ");

    $new_winners = [];

    /* MARK NEW WINNERS */
    if(isset($_POST['winner_id']))
    {
        foreach($_POST['winner_id'] as $value)
        {
            $new_winners[] = $value;

            if($mode=="team")
            {
                $team = mysqli_real_escape_string($conn,$value);

                mysqli_query($conn,"
                UPDATE enrollments
                SET is_winner='1'
                WHERE event_id='$event_id'
                AND team_name='$team'
                AND remarks='present'
                ");
            }
            else
            {
                mysqli_query($conn,"
                UPDATE enrollments
                SET is_winner='1'
                WHERE enroll_id='$value'
                ");
            }
        }
    }

    /* ONLY NEWLY ADDED WINNERS */
    $fresh_winners = array_diff($new_winners,$old_winners);

    foreach($fresh_winners as $winner)
    {
        if($mode=="team")
        {
            $winner = mysqli_real_escape_string($conn,$winner);

            $q = mysqli_query($conn,"
            SELECT u.full_name,u.user_email,e.event_name
            FROM enrollments en
            INNER JOIN users u ON en.user_id=u.user_id
            INNER JOIN event e ON en.event_id=e.event_id
            WHERE en.event_id='$event_id'
            AND en.team_name='$winner'
            AND en.remarks='present'
            ");
        }
        else
        {
            $q = mysqli_query($conn,"
            SELECT u.full_name,u.user_email,e.event_name
            FROM enrollments en
            INNER JOIN users u ON en.user_id=u.user_id
            INNER JOIN event e ON en.event_id=e.event_id
            WHERE en.enroll_id='$winner'
            LIMIT 1
            ");
        }

        while($row=mysqli_fetch_assoc($q))
        {
            $subject = "Congratulations Winner - ".$row['event_name'];

            $body = "
            <h2>Congratulations ".$row['full_name']." 🎉</h2>

            <p>You are  <b>Winner</b> in
            <b>".$row['event_name']."</b>.</p>

            <p>Please Download your cerificate.</p>

            <p>Your Winner Certificate is ready.</p>
             <p>Login and download certificate.</p>
            ";

            sendMailToUser(
                $row['user_email'],
                $row['full_name'],
                $subject,
                $body
            );
        }
    }

    $msg = "Winner updated successfully.";
}

/* ===============================
LIST DATA
================================= */
if($mode=="team")
{
    $result = mysqli_query($conn,"
    SELECT *
    FROM enrollments
    WHERE event_id='$event_id'
    AND remarks='present'
    GROUP BY team_name
    ORDER BY joined_at ASC
    ");
}
else
{
    $result = mysqli_query($conn,"
    SELECT *
    FROM enrollments
    WHERE event_id='$event_id'
    AND remarks='present'
    ORDER BY joined_at ASC
    ");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Mark Winner</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<style>
body{
background:#f4f6f9;
padding:30px;
font-family:Arial;
}
.box{
max-width:900px;
margin:auto;
background:#fff;
padding:30px;
border-radius:14px;
box-shadow:0 8px 20px rgba(0,0,0,.08);
}
.list-box{
border:1px solid #ddd;
padding:10px;
border-radius:10px;
max-height:520px;
overflow:auto;
}
.item{
padding:14px;
border-bottom:1px solid #eee;
display:flex;
gap:12px;
}
.item:last-child{
border-bottom:none;
}
.badge-win{
background:#f59e0b;
color:#fff;
padding:3px 8px;
border-radius:20px;
font-size:12px;
}
</style>
</head>

<body>

<div class="box">

<h2>Mark Winner</h2>

<p>
Event:
<b><?php echo $event['event_name']; ?></b><br>
Mode:
<b><?php echo ucfirst($mode); ?></b>
</p>

<?php if(isset($msg)){ ?>
<div class="alert alert-success">
<?php echo $msg; ?>
</div>
<?php } ?>

<form method="post">

<div class="list-box">

<?php
if(mysqli_num_rows($result)>0)
{
while($row=mysqli_fetch_assoc($result))
{
$checked = ($row['is_winner']==1) ? "checked" : "";

$value = ($mode=="team")
? $row['team_name']
: $row['enroll_id'];
?>

<div class="item">

<input type="checkbox"
name="winner_id[]"
value="<?php echo $value; ?>"
<?php echo $checked; ?>>

<div style="width:100%;">

<?php if($mode=="team"){ ?>

<b><?php echo $row['team_name']; ?></b>

<?php if($row['is_winner']==1){ ?>
<span class="badge-win">Winner</span>
<?php } ?>

<br>
Leader:
<?php echo $row['full_name']; ?>

<?php } else { ?>

<b><?php echo $row['full_name']; ?></b>

<?php if($row['is_winner']==1){ ?>
<span class="badge-win">Winner</span>
<?php } ?>

<br>
<?php echo $row['user_email']; ?>

<?php } ?>

</div>
</div>

<?php
}
}
else
{
echo "No present users found.";
}
?>

</div>

<button type="submit"
name="save_winner"
class="btn btn-warning w-100 mt-3">
Save Winner
</button>

</form>

<a href="dashboard.php" class="mt-3 d-inline-block">
← Back
</a>

</div>

</body>
</html>

