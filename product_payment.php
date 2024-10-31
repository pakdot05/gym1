<?php
require('inc/links.php');

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

// Validate input data
if (!isset($data['productId'], $data['quantity'], $data['totalPrice'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$productId = $data['productId'];
$quantity = $data['quantity'];
$totalPrice = $data['totalPrice'];

// Prepare your Maya payment integration
$mayaPublicKey = "pk-NCLk7JeDbX1m22ZRMDYO9bEPowNWT5J4aNIKIbcTy2a";
$mayaSecretKey = "sk-8MqXdZYWV9UJB92Mc0i149CtzTWT7BYBQeiarM27iAi";

// Set up the headers
$headers = [
    "Content-Type: application/json",
    "Authorization: Basic " . base64_encode("$mayaPublicKey:$mayaSecretKey")
];

// Prepare the payment data
$paymentData = [
    "amount" => $totalPrice,
    "currency" => "PHP",
    "payment_method" => "maya",
    "description" => "Payment for product ID: $productId",
    // Additional parameters as needed by Maya API
];

// Initialize cURL session
$ch = curl_init('https://api.maya.com/v1/payment/checkout'); // Replace with the actual Maya API endpoint
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentData));

// Execute the request
$response = curl_exec($ch);
curl_close($ch);

// Decode the response
$responseData = json_decode($response, true);

// Check if the payment was successful
if (isset($responseData['success']) && $responseData['success']) {
    // Redirect the user to the payment URL provided by Maya
    echo json_encode(['success' => true, 'paymentUrl' => $responseData['paymentUrl']]);
} else {
    // Handle error
    echo json_encode(['success' => false, 'message' => 'Payment failed.']);
}
?>
