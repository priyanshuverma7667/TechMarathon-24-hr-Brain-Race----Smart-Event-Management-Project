<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}


include('../db.php');

$user_id   = $_SESSION['user_id'];
$enroll_id = $_GET['id'] ?? '';

if (empty($enroll_id)) {
    echo "<script>
    alert('Invalid Request');
    window.location='dashboard.php';
    </script>";
    exit();
}

/* Fetch User Enrollment Record */
$sql = mysqli_query($conn,"
SELECT *
FROM enrollments
WHERE enroll_id='$enroll_id'
AND user_id='$user_id'
LIMIT 1
");

$row = mysqli_fetch_assoc($sql);

if (!$row) {
    echo "<script>
    alert('Enrollment record not found.');
    window.location='dashboard.php';
    </script>";
    exit();
}

$event_id   = $row['event_id'];
$mode       = strtolower($row['participation_mode']);
$team_name  = $row['team_name'];
$leader_id  = $row['team_leader_id'];

/* ======================================
   INDIVIDUAL EVENT
====================================== */
if ($mode == 'individual')
{
    mysqli_query($conn,"
    UPDATE enrollments
    SET status='Withdrawn',
        withdrawn_at=NOW()
    WHERE enroll_id='$enroll_id'
    ");

    mysqli_query($conn,"
    UPDATE users
    SET total_events_joined =
        IF(total_events_joined > 0,
           total_events_joined - 1,
           0)
    WHERE user_id='$user_id'
    ");

    echo "<script>
    alert('You have successfully withdrawn.');
    window.location='dashboard.php';
    </script>";
    exit();
}

/* ======================================
   TEAM EVENT
====================================== */
if ($mode == 'team')
{
    /* Only Team Leader Can Withdraw */
    if ($user_id == $leader_id)
    {
        /* Get all active team members */
        $members = mysqli_query($conn,"
        SELECT user_id
        FROM enrollments
        WHERE event_id='$event_id'
        AND team_name='$team_name'
        AND team_leader_id='$leader_id'
        AND status='Joined'
        ");

        /* Withdraw Whole Team */
        mysqli_query($conn,"
        UPDATE enrollments
        SET status='Withdrawn',
            withdrawn_at=NOW()
        WHERE event_id='$event_id'
        AND team_name='$team_name'
        AND team_leader_id='$leader_id'
        AND status='Joined'
        ");

        /* Update stats for all members */
        while($m = mysqli_fetch_assoc($members))
        {
            mysqli_query($conn,"
            UPDATE users
            SET total_events_joined =
                IF(total_events_joined > 0,
                   total_events_joined - 1,
                   0)
            WHERE user_id='".$m['user_id']."'
            ");
        }

        echo "<script>
        alert('Whole team withdrawn successfully.');
        window.location='dashboard.php';
        </script>";
        exit();
    }
    else
    {
        echo "<script>
        alert('You cannot withdraw. Ask your team leader to do it.');
        window.location='dashboard.php';
        </script>";
        exit();
    }
}
?>