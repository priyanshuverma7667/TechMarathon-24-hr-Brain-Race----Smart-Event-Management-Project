<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

include("../db.php");
require('../vendor/autoload.php');

use Razorpay\Api\Api;

$keyId = "rzp_test_RGJ1hfRVhqu7MS";
$keySecret = "ztU5ZYHSDJJYjQL0xMOKUAG";

$api = new Api($keyId,$keySecret);

$payment_id = $_POST['razorpay_payment_id'];
$order_id   = $_POST['razorpay_order_id'];
$signature  = $_POST['razorpay_signature'];

try{
    $api->utility->verifyPaymentSignature([
        'razorpay_order_id'=>$order_id,
        'razorpay_payment_id'=>$payment_id,
        'razorpay_signature'=>$signature
    ]);
}
catch(Exception $e){
    die("Payment Failed");
}

/* success => join event */
$_POST['payment_id']=$payment_id;

header("Location: ../main.php?flag=16");
exit();
?>