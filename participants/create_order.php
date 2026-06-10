<?php
require('../vendor/autoload.php');

use Razorpay\Api\Api;

$keyId = "rzp_test_yourkey";
$keySecret = "your_secret";

$api = new Api($keyId, $keySecret);

$amount = $_POST['amount'];

$order = $api->order->create([
    'receipt' => 'EVT_' . time(),
    'amount' => $amount * 100,
    'currency' => 'INR'
]);

echo json_encode([
    "id" => $order['id']
]);