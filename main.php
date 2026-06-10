<?php
include_once("db.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$flag = 0;

if(isset($_GET["flag"]))
    $flag = $_GET["flag"];

switch($flag)
{
    case 1:
        # master admin login
        $admin_email = $_POST["admin_email"];
        $admin_pass  = md5($_POST["admin_pass"]);

        $sql = "SELECT * FROM admin WHERE admin_email='$admin_email' AND password='$admin_pass'";

        $result = mysqli_query($conn,$sql);

        if(mysqli_num_rows($result) == 1)
        {
            $row = mysqli_fetch_assoc($result);
            session_start();

            /* Start Admin Session */
            $_SESSION["admin_id"] = $row["admin_id"];
            $_SESSION["admin_name"] = $row["admin_name"];

            header("Location: master_admin/dashboard.php");
            exit();
        }
        else
        {
            echo "<script>
                alert('Invalid User');
                window.location='master_admin/index.html';
                </script>";
                exit();
        }

    break;
    
    case 2:

        $event_name = $_POST["event_name"];
        $event_type = $_POST["event_type"];
        $des        = $_POST["des"];


        $sql = "insert into event
                (event_name,event_type,description)
                values
                ('$event_name','$event_type','$des')";

        if(mysqli_query($conn,$sql))
            header("Location: master_admin/dashboard.php");
        else
            echo "Failed To Add Event";

    break;
    
    case 3:

        $event_id      = $_POST["event_id"];
        $event_co_ids  = $_POST["event_co_id"];

        $count = 0;

        /* EVENT DETAILS */
        $event = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT * FROM event
        WHERE event_id='$event_id'
        "));

        $event_name     = $event['event_name'];

        foreach($event_co_ids as $co_id)
        {
            if($co_id != "")
            {
                /* CHECK DUPLICATE */
                $check = mysqli_query($conn,"
                SELECT * FROM event_coordinator_map
                WHERE event_id='$event_id'
                AND event_co_id='$co_id'
                ");

                if(mysqli_num_rows($check) == 0)
                {
                    /* INSERT */
                    mysqli_query($conn,"
                    INSERT INTO event_coordinator_map(event_id,event_co_id)
                    VALUES('$event_id','$co_id')
                    ");

                    $count++;

                    /* FETCH COORDINATOR */
                    $co = mysqli_fetch_assoc(mysqli_query($conn,"
                    SELECT * FROM coordinator
                    WHERE co_id='$co_id'
                    "));

                    $co_name  = $co['co_name'];
                    $co_email = $co['co_email'];

                    /* SEND MAIL */
                    try
                    {
                        $mail = new PHPMailer(true);

                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;

                        $mail->Username   = 'priyanshu76670@gmail.com';
                        $mail->Password   = 'dpeixoaddetvqiem';

                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        $mail->SMTPOptions = array(
                            'ssl' => array(
                                'verify_peer'       => false,
                                'verify_peer_name'  => false,
                                'allow_self_signed' => true
                            )
                        );

                        $mail->setFrom(
                            'priyanshu76670@gmail.com',
                            'Event Management'
                        );

                        $mail->addAddress($co_email,$co_name);

                        $mail->isHTML(true);

                        $mail->Subject =
                        "Assigned as Event Coordinator";

                        $mail->Body = "
                        <h2>Hello $co_name</h2>

                        <p>You have been assigned as coordinator for:</p>

                        <h3>$event_name</h3>
                    

                        

                        <p>Please login to dashboard.</p><br>
                        <b>Your login email is $co_email</b><br>
                        <b>Your Password is by default '98765'</b>
                        ";

                        $mail->send();
                    }
                    catch(Exception $e)
                    {
                        /* Ignore mail error */
                    }
                }
            }
        }

        /* REDIRECT */
        if($count > 0)
        {
            header("Location:master_admin/dashboard.php?msg=1");
        }
        else
        {
            header("Location:master_admin/event_coordinator.php?msg=0");
        }

    break;

    case 4:

        $event_id   = $_POST["event_id"];
        $event_name = $_POST["event_name"];
        $event_type = $_POST["event_type"];
        $des        = $_POST["des"];

        /* Update Event Details */
        $sql = "update event
                set event_name = '$event_name',
                    event_type = '$event_type',
                    description = '$des'
                where event_id = '$event_id'";

        if(mysqli_query($conn,$sql))
        {
            /* Remove Old Coordinators */
            mysqli_query($conn,"
                delete from event_coordinator_map
                where event_id = '$event_id'
            ");

            /* Insert New Coordinators */
            if(isset($_POST["event_co_id"]))
            {
                foreach($_POST["event_co_id"] as $co_id)
                {
                    mysqli_query($conn,"
                        insert into event_coordinator_map(event_id,event_co_id)
                        values('$event_id','$co_id')
                    ");
                }
            }

            echo "Event Updated Successfully";
        }
        else
        {
            echo "Failed To Update Event";
        }

    break;
    
    case 5:
        # update co-ordinators

        $event_id = $_GET["event_id"];

        mysqli_query($conn,"
            delete from event_coordinator_map
            where event_id='$event_id'
        ");

        mysqli_query($conn,"
            delete from event
            where event_id='$event_id'
        ");

        header("Location:master_admin/event_view.php");

    break;

    case 6:
        // Coordinator Login

        $coord_email = $_POST["coord_email"];
        $coord_pass  = md5($_POST["coord_pass"]);

        $sql = "SELECT * FROM coordinator 
                WHERE co_email='$coord_email' 
                AND co_pass='$coord_pass'";

        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) == 1)
        {
            // FETCH ROW FIRST
            $row = mysqli_fetch_assoc($result);
            session_start();
            $_SESSION['coord_id']    = $row['co_id'];
            $_SESSION['coord_name']  = $row['co_name'];
            $_SESSION['coord_email'] = $row['co_email'];
            header("Location: event_co/dashboard.php");
            exit();
        }
        else
        {
            echo "invalid coordinator";
        }

    break;
    case 10:
        // user register
            session_start();
          

            $first_name = mysqli_real_escape_string($conn,$_POST['first_name']);
            $last_name  = mysqli_real_escape_string($conn,$_POST['last_name']);
            $full_name  = $first_name.' '.$last_name;

            $username   = mysqli_real_escape_string($conn,$_POST['username']);
            $user_email = mysqli_real_escape_string($conn,$_POST['user_email']);
            $phone      = mysqli_real_escape_string($conn,$_POST['phone']);
            $user_pass  = md5($_POST['user_pass']);

            $gender     = mysqli_real_escape_string($conn,$_POST['gender']);
            $dob        = mysqli_real_escape_string($conn,$_POST['date_of_birth']);


            /* DUPLICATE CHECK */
            $check = mysqli_query($conn,"
                SELECT user_id
                FROM users
                WHERE username='$username'
                OR user_email='$user_email'
                OR phone='$phone'
                LIMIT 1
            ");

            if(mysqli_num_rows($check) > 0)
            {
                echo "<script>
                alert('Username / Email / Phone already exists');
                window.location='participants/register.php';
                </script>";
                exit();
            }
            
            /* INSERT USER */
            $sql = "
            INSERT INTO users
            (
                first_name,
                last_name,
                full_name,
                username,
                user_email,
                phone,
                user_pass,
                role,
                gender,
                date_of_birth,
                is_verified,
                is_active,
                total_events_joined,
                total_events_won,
                total_certificates,
                created_at,
                updated_at
            )
            VALUES
            (
                '$first_name',
                '$last_name',
                '$full_name',
                '$username',
                '$user_email',
                '$phone',
                '$user_pass',
                'Participant',
                '$gender',
                '$dob',
                1,
                1,
                0,
                0,
                0,
                NOW(),
                NOW()
            )";

            if(mysqli_query($conn,$sql))
            {
                echo "<script>
                alert('Registration Successful');
                window.location='participants/index.html';
                </script>";
            }
            else
            {
                echo "<script>
                alert('Registration Failed');
                window.location='participants/register.php';
                </script>";
            }
    break;
    case 15:
        // user Login

        $user_email = $_POST["user_email"];
        $user_pass  = md5($_POST["user_pass"]);

        $sql = "SELECT * FROM users WHERE user_email='$user_email' AND user_pass='$user_pass'";

        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) == 1)
        {
            // FETCH ROW FIRST
            $row = mysqli_fetch_assoc($result);
            session_start();
            $_SESSION['user_id']    = $row['user_id'];
            $_SESSION['user_email'] = $row['user_email'];
            $_SESSION["username"] = $row["username"];
            header("Location: participants/dashboard.php");
            exit();
        }
        else
        {
            echo "<script>
                alert('Invalid User');
                window.location='participants/index.html';
                </script>";
                exit();
        }

    break;

    case 16:

        session_start();

        if (!isset($_SESSION['user_id'])) {
            header("Location: participants/index.html");
            exit();
        }

        $user_id  = $_SESSION['user_id'];
        $event_id = $_POST['event_id'];

        $phone   = mysqli_real_escape_string($conn,$_POST['phone']);
        $remarks = mysqli_real_escape_string($conn,$_POST['remarks'] ?? '');

        /* =========================================
        USER DETAILS
        ========================================= */
        $uq = mysqli_query($conn,"
        SELECT *
        FROM users
        WHERE user_id='$user_id'
        LIMIT 1
        ");

        $user = mysqli_fetch_assoc($uq);

        if(!$user)
        {
            echo "<script>
            alert('Invalid User');
            window.location='participants/dashboard.php';
            </script>";
            exit();
        }

        /* =========================================
        EVENT DETAILS
        ========================================= */
        $eq = mysqli_query($conn,"
        SELECT *
        FROM event
        WHERE event_id='$event_id'
        LIMIT 1
        ");

        $event = mysqli_fetch_assoc($eq);

        if(!$event)
        {
            echo "<script>
            alert('Invalid Event');
            window.location='participants/dashboard.php';
            </script>";
            exit();
        }

        /* =========================================
        PAYMENT CHECK
        ========================================= */
        if($event['fees'] > 0)
        {
            if(empty($_POST['razorpay_payment_id']))
            {
                echo "<script>
                alert('Payment Required');
                window.location='participants/dashboard.php';
                </script>";
                exit();
            }
        }

        /* =========================================
        DUPLICATE CHECK
        ========================================= */
        $dup = mysqli_query($conn,"
        SELECT enroll_id
        FROM enrollments
        WHERE user_id='$user_id'
        AND event_id='$event_id'
        AND status='Joined'
        LIMIT 1
        ");

        if(mysqli_num_rows($dup) > 0)
        {
            echo "<script>
            alert('You already joined this event');
            window.location='participants/dashboard.php';
            </script>";
            exit();
        }

        /* =========================================
        DATE CHECK
        ========================================= */
        $today = date("Y-m-d");

        if($today < $event['reg_open_date'])
        {
            echo "<script>
            alert('Registration has not started');
            window.location='participants/dashboard.php';
            </script>";
            exit();
        }

        if($today > $event['reg_close_date'])
        {
            echo "<script>
            alert('Registration closed');
            window.location='participants/dashboard.php';
            </script>";
            exit();
        }

        /* =========================================
        GENDER CHECK
        ========================================= */
        if($event['allowed_gender'] != 'Any')
        {
            if(strtolower($user['gender']) != strtolower($event['allowed_gender']))
            {
                echo "<script>
                alert('Only {$event['allowed_gender']} participants allowed');
                window.location='participants/dashboard.php';
                </script>";
                exit();
            }
        }

        /* =========================================
        DEFAULT VALUES
        ========================================= */
        $team_name      = '';
        $members        = 1;
        $team_leader_id = "NULL";

        /* =========================================
        TEAM MODE
        ========================================= */
        if(strtolower(trim($event['participation_mode'])) == 'team')
        {
            $team_name = mysqli_real_escape_string($conn,$_POST['team_name']);
            $members   = (int)$_POST['members'];

            $emails = [];

            if(!empty($_POST['member1_email'])) $emails[] = trim($_POST['member1_email']);
            if(!empty($_POST['member2_email'])) $emails[] = trim($_POST['member2_email']);
            if(!empty($_POST['member3_email'])) $emails[] = trim($_POST['member3_email']);
            if(!empty($_POST['member4_email'])) $emails[] = trim($_POST['member4_email']);

            /* Team size check */
            if($members < $event['min_participants'] || $members > $event['max_participants'])
            {
                echo "<script>
                alert('Invalid Team Size');
                history.back();
                </script>";
                exit();
            }

            /* Correct member count */
            $actual_total = count($emails) + 1; // + Leader

            if($actual_total != $members)
            {
                echo "<script>
                alert('Member count mismatch');
                history.back();
                </script>";
                exit();
            }

            /* Prevent duplicate emails in same team */
            if(count($emails) != count(array_unique($emails)))
            {
                echo "<script>
                alert('Duplicate member emails entered');
                history.back();
                </script>";
                exit();
            }

            /* Leader email cannot repeat */
            foreach($emails as $mail)
            {
                if(strtolower($mail) == strtolower($user['user_email']))
                {
                    echo "<script>
                    alert('Leader email cannot be entered as member');
                    history.back();
                    </script>";
                    exit();
                }
            }

            /* Validate all members */
            foreach($emails as $mail)
            {
                $mq = mysqli_query($conn,"
                SELECT *
                FROM users
                WHERE user_email='$mail'
                LIMIT 1
                ");

                if(mysqli_num_rows($mq) == 0)
                {
                    echo "<script>
                    alert('$mail is not registered');
                    history.back();
                    </script>";
                    exit();
                }

                $member_user = mysqli_fetch_assoc($mq);
                $member_id   = $member_user['user_id'];

                /* already joined same event */
                $check2 = mysqli_query($conn,"
                SELECT enroll_id
                FROM enrollments
                WHERE user_id='$member_id'
                AND event_id='$event_id'
                AND status='Joined'
                LIMIT 1
                ");

                if(mysqli_num_rows($check2) > 0)
                {
                    echo "<script>
                    alert('$mail already joined this event');
                    history.back();
                    </script>";
                    exit();
                }
            }

            $team_leader_id = $user_id;
        }

        /* =========================================
        SLOT CHECK
        ========================================= */
        $cq = mysqli_query($conn,"
        SELECT COUNT(*) total
        FROM enrollments
        WHERE event_id='$event_id'
        AND status='Joined'
        ");

        $count = mysqli_fetch_assoc($cq);

        if(($count['total'] + $members) > $event['max_participants'])
        {
            echo "<script>
            alert('Not enough slots available');
            history.back();
            </script>";
            exit();
        }

        /* =========================================
        INSERT LEADER / INDIVIDUAL
        ========================================= */
        $sql = "
        INSERT INTO enrollments
        (
        user_id,
        event_id,
        full_name,
        user_email,
        phone,
        team_name,
        total_members,
        participation_mode,
        remarks,
        status,
        joined_at,
        team_leader_id
        )
        VALUES
        (
        '$user_id',
        '$event_id',
        '{$user['full_name']}',
        '{$user['user_email']}',
        '$phone',
        '$team_name',
        '$members',
        '{$event['participation_mode']}',
        '$remarks',
        'Joined',
        NOW(),
        $team_leader_id
        )
        ";

        if(mysqli_query($conn,$sql))
        {
            /* Insert Team Members */
            if(strtolower(trim($event['participation_mode'])) == 'team')
            {
                foreach($emails as $mail)
                {
                    $mq = mysqli_query($conn,"
                    SELECT *
                    FROM users
                    WHERE user_email='$mail'
                    LIMIT 1
                    ");

                    $m = mysqli_fetch_assoc($mq);

                    mysqli_query($conn,"
                    INSERT INTO enrollments
                    (
                    user_id,
                    event_id,
                    full_name,
                    user_email,
                    phone,
                    team_name,
                    total_members,
                    participation_mode,
                    remarks,
                    status,
                    joined_at,
                    team_leader_id
                    )
                    VALUES
                    (
                    '{$m['user_id']}',
                    '$event_id',
                    '{$m['full_name']}',
                    '{$m['user_email']}',
                    '{$m['phone']}',
                    '$team_name',
                    '$members',
                    'Team',
                    'Added by Team Leader',
                    'Joined',
                    NOW(),
                    '$user_id'
                    )
                    ");
                }
            }

            /* Update leader stats */
            mysqli_query($conn,"
            UPDATE users
            SET total_events_joined = total_events_joined + 1
            WHERE user_id='$user_id'
            ");

            /* Save Payment */
            if($event['fees'] > 0)
            {
                $payment_id = $_POST['razorpay_payment_id'];

                mysqli_query($conn,"
                INSERT INTO payments
                (
                user_id,
                event_id,
                payment_id,
                amount,
                status,
                created_at
                )
                VALUES
                (
                '$user_id',
                '$event_id',
                '$payment_id',
                '{$event['fees']}',
                'Success',
                NOW()
                )
                ");
            }

            echo "<script>
            alert('Successfully Joined Event');
            window.location='participants/dashboard.php';
            </script>";
        }
        else
        {
            echo "<script>
            alert('Unable to Join Event');
            window.location='participants/dashboard.php';
            </script>";
        }

    break;
    case 17:
        session_start();

        /* ONLY ADMIN */
        if(!isset($_SESSION['admin_id']))
        {
            header("Location: index.html");
            exit();
        }

        $co_id      = $_POST['co_id'];
        $co_name    = $_POST['co_name'];
        $co_email   = $_POST['co_email'];
        $co_phone   = $_POST['co_phone'];
        $co_gender  = $_POST['co_gender'];
        $co_address = $_POST['co_address'];
        $co_pass    = md5($_POST['co_pass']);     // use md5() if needed

        mysqli_query($conn,"
        UPDATE coordinator SET
        co_name='$co_name',
        co_email='$co_email',
        co_phone='$co_phone',
        co_gender='$co_gender',
        co_address='$co_address',
        co_pass='$co_pass'
        WHERE co_id='$co_id'
        ");

        header("Location: master_admin/coordinator_list.php?msg=updated");
        exit();
    break;

    case 18:
        session_start();

        /* ONLY ADMIN */
        if(!isset($_SESSION['admin_id']))
        {
            header("Location: index.html");
            exit();
        }

        /* CHECK ID */
        if(!isset($_GET['id']))
        {
            header("Location: master_admin/coordinators.php");
            exit();
        }

        $co_id = $_GET['id'];

        /* OPTIONAL: DELETE ASSIGNED EVENT MAPPINGS FIRST */
        mysqli_query($conn,"
        DELETE FROM event_coordinator_map
        WHERE event_co_id='$co_id'
        ");

        /* DELETE COORDINATOR */
        mysqli_query($conn,"
        DELETE FROM coordinator
        WHERE co_id='$co_id'
        ");

        header("Location: master_admin/coordinator_list.php");
        exit();
    break;

    case 19:

        $co_name    = mysqli_real_escape_string($conn, $_POST['co_name']);
        $co_email   = mysqli_real_escape_string($conn, $_POST['co_email']);
        $co_phone   = mysqli_real_escape_string($conn, $_POST['co_phone']);
        $co_gender  = mysqli_real_escape_string($conn, $_POST['co_gender']);
        $co_address = mysqli_real_escape_string($conn, $_POST['co_address']);
        $co_pass    = md5($_POST['co_pass']);

        /* CHECK EMAIL ALREADY EXISTS */
        $check = mysqli_query($conn,"
        SELECT * FROM coordinator
        WHERE co_email='$co_email'
        ");

        if(mysqli_num_rows($check) > 0)
        {
            header("Location: master_admin/add_coordinator.php?msg=exists");
            exit();
        }

        /* INSERT */
        mysqli_query($conn,"
        INSERT INTO coordinator
        (
        co_name,
        co_email,
        co_phone,
        co_gender,
        co_address,
        created_at,
        co_pass
        )
        VALUES
        (
        '$co_name',
        '$co_email',
        '$co_phone',
        '$co_gender',
        '$co_address',
        NOW(),
        '$co_pass'
        )
        ");

        header("Location: master_admin/coordinator_list.php");
        exit();

    break;

}
?>