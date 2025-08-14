<?php
include("include_front/connect.php");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

$order_id = intval($_GET['order_id']);

// Fetch order info
$query = $connect->prepare("SELECT order_status, delivery_address FROM orders WHERE order_id = ?");
$query->bind_param("i", $order_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows == 0) {
    echo json_encode(["error" => "Order not found"]);
    exit();
}

$order = $result->fetch_assoc();

// Fixed Hotel Location (Example: Machakos)
$hotel_lat = -1.5177;
$hotel_lng = 37.2634;

// Generate customer location based on delivery address
function getRandomNearby($baseLat, $baseLng, $radius = 0.02) {
    $lat = $baseLat + ((mt_rand(-1000, 1000) / 1000) * $radius);
    $lng = $baseLng + ((mt_rand(-1000, 1000) / 1000) * $radius);
    return [$lat, $lng];
}

$address = strtolower($order['delivery_address']);

if (strpos($address, 'nairobi') !== false) {
    list($customer_lat, $customer_lng) = getRandomNearby(-1.2841, 36.8155, 0.03);
} elseif (strpos($address, 'machakos') !== false) {
    list($customer_lat, $customer_lng) = getRandomNearby(-1.5177, 37.2634, 0.03);
} else {
    // Central Kenya fallback coordinates (Nyeri, Murang'a, Kiambu, etc.)
    $central = [
        [-0.4167, 36.9500], // Nyeri
        [-1.0341, 37.0693], // Murang'a
        [-1.1714, 36.8356], // Kiambu
        [-0.2833, 36.7167], // Nanyuki
    ];
    $pick = $central[array_rand($central)];
    list($customer_lat, $customer_lng) = getRandomNearby($pick[0], $pick[1], 0.02);
}

// Simulate rider location (between hotel and customer)
$rider_lat = $hotel_lat + (($customer_lat - $hotel_lat) * 0.5) + ((mt_rand(-100, 100) / 10000));
$rider_lng = $hotel_lng + (($customer_lng - $hotel_lng) * 0.5) + ((mt_rand(-100, 100) / 10000));

// Simulate ETA
$eta = rand(5, 30) . " mins";

echo json_encode([
    "status" => ucfirst($order['order_status']),
    "hotel_lat" => $hotel_lat,
    "hotel_lng" => $hotel_lng,
    "customer_lat" => $customer_lat,
    "customer_lng" => $customer_lng,
    "rider_lat" => $rider_lat,
    "rider_lng" => $rider_lng,
    "eta" => $eta
]);
