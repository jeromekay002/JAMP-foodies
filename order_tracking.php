<?php
session_start();
include("include_front/connect.php");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['customer_id'])) {
    header("Location: index.php#loginSection");
    exit();
}

$customer_id = $_SESSION['customer_id'];

if (!isset($_GET['order_id'])) {
    echo "Order ID missing!";
    exit();
}

$order_id = $_GET['order_id'];

// Fetch order details
$order_query = $connect->prepare("SELECT * FROM orders WHERE order_id = ? AND customer_id = ?");
$order_query->bind_param("ii", $order_id, $customer_id);
$order_query->execute();
$order_result = $order_query->get_result();

if ($order_result->num_rows == 0) {
    echo "No such order found!";
    exit();
}

$order = $order_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Track Order</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        body {
            background-image: url('images/bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            padding: 30px;
            font-family: 'Segoe UI', sans-serif;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Container styles */
        .progressbar {
            counter-reset: step;
            display: flex;
            justify-content: space-between;
            list-style-type: none;
            padding: 0;
            margin: 0;
            position: relative;
        }

        /* The joined line in the background */
        .progressbar::before {
            content: "";
            position: absolute;
            top: 14px;
            /* aligns with circles */
            left: 0;
            width: 100%;
            height: 4px;
            background-color: #ddd;
            /* background line color */
            z-index: 0;
        }

        /* Progress items */
        .progressbar li {
            position: relative;
            text-align: center;
            flex: 1;
            z-index: 1;
        }

        /* Step circles */
        .progressbar li::before {
            content: counter(step);
            counter-increment: step;
            width: 28px;
            height: 28px;
            line-height: 28px;
            border: 2px solid #ddd;
            display: block;
            text-align: center;
            margin: 0 auto 10px auto;
            border-radius: 50%;
            background-color: white;
            position: relative;
            z-index: 2;
        }

        /* Active step styling */
        .progressbar li.active::before,
        .progressbar li.active~li::before {
            border-color: #ff4757;
            background-color: #ff4757;
            color: white;
        }

        /* The filled joined line for completed steps */
        .progressbar li.active::after {
            content: "";
            position: absolute;
            top: 14px;
            left: 50%;
            width: 100%;
            height: 4px;
            background-color: #ff4757;
            z-index: -1;
        }

        /* Adjust active line span */
        .progressbar li:last-child.active::after {
            width: 50%;
            /* remove line after last item */
        }


        /* Map */
        #map {
            height: 400px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .info {
            margin-bottom: 20px;
        }

        .info p {
            margin: 5px 0;
        }

        .btn-back {
            display: inline-block;
            margin-top: 15px;
            background-color: #007bff;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
        }

        .btn-back:hover {
            background-color: #0056b3;
        }

        .marker-label {
            background: #333;
            color: white;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Track Your Order</h2>

        <div class="info">
            <p><strong>Order ID:</strong> <?= $order['order_id'] ?></p>
            <p><strong>Order Date:</strong> <?= date('F j, Y, g:i a', strtotime($order['created_at'])) ?></p>
            <p><strong>Status:</strong> <span id="order-status"><?= ucfirst($order['order_status']) ?></span></p>
            <p><strong>ETA:</strong> <span id="order-eta">Calculating...</span></p>
        </div>

        <!-- Progress Bar -->
        <ul class="progressbar" id="progressbar">
            <li class="active">Order Placed</li>
            <li>Preparing</li>
            <li>Ready for Pickup</li>
            <li>Out for Delivery</li>
            <li>Delivered</li>
        </ul>


        <!-- Map -->
        <div id="map"></div>

        <a href="my_orders.php" class="btn-back">← Back to My Orders</a>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const orderId = <?= $order_id ?>;
        const graphhopperKey = "YOUR_GRAPHHOPPER_API_KEY"; // <-- Replace with your real key

        let map = L.map('map').setView([-1.286389, 36.817223], 13); // Default Nairobi
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
        }).addTo(map);

        let customerMarker, riderMarker, hotelMarker;
        let riderHotelRoute, hotelCustomerRoute;

        async function getRoute(from, to, color) {
            const url = `https://graphhopper.com/api/1/route?point=${from[0]},${from[1]}&point=${to[0]},${to[1]}&vehicle=car&points_encoded=false&key=${graphhopperKey}`;
            const res = await fetch(url);
            const data = await res.json();

            if (!data.paths || !data.paths.length) {
                console.error("No route found", data);
                return null;
            }

            const coords = data.paths[0].points.coordinates.map(c => [c[1], c[0]]);
            return L.polyline(coords, {
                color,
                weight: 4,
                opacity: 0.8
            });
        }

        function updateTracking() {
            fetch(`fetch_tracking_data.php?order_id=${orderId}`)
                .then(res => res.json())
                .then(async data => {
                    document.getElementById('order-status').textContent = data.status;
                    document.getElementById('order-eta').textContent = data.eta;

                    const steps = ["Order Placed", "Preparing", "Ready for Pickup", "Out for Delivery", "Delivered"];
                    let currentStatus = (data.status || "").toLowerCase();
                    let stepIndex = steps.findIndex(step => step.toLowerCase() === currentStatus);

                    if (stepIndex === -1) {
                        if (currentStatus.includes("received")) stepIndex = 0;
                        if (currentStatus.includes("prepar")) stepIndex = 1;
                        if (currentStatus.includes("pickup")) stepIndex = 2;
                        if (currentStatus.includes("delivery")) stepIndex = 3;
                        if (currentStatus.includes("deliver")) stepIndex = 4;
                    }

                    document.querySelectorAll("#progressbar li").forEach((li, index) => {
                        li.classList.toggle("active", index <= stepIndex);
                    });

                    // Hotel Marker
                    if (!hotelMarker) {
                        hotelMarker = L.marker([data.hotel_lat, data.hotel_lng]).addTo(map)
                            .bindTooltip("Hotel", {
                                permanent: true,
                                direction: "top",
                                className: "marker-label"
                            });
                    }

                    // Customer Marker
                    if (!customerMarker) {
                        customerMarker = L.marker([data.customer_lat, data.customer_lng]).addTo(map)
                            .bindTooltip("Customer", {
                                permanent: true,
                                direction: "top",
                                className: "marker-label"
                            });
                    }

                    // Rider Marker (always update position)
                    if (riderMarker) map.removeLayer(riderMarker);
                    riderMarker = L.marker([data.rider_lat, data.rider_lng]).addTo(map)
                        .bindTooltip("Rider", {
                            permanent: true,
                            direction: "top",
                            className: "marker-label"
                        });

                    // Remove old routes before drawing new ones
                    if (riderHotelRoute) map.removeLayer(riderHotelRoute);
                    if (hotelCustomerRoute) map.removeLayer(hotelCustomerRoute);

                    // Draw Rider→Hotel (blue) & Hotel→Customer (red)
                    riderHotelRoute = await getRoute([data.rider_lat, data.rider_lng], [data.hotel_lat, data.hotel_lng], "blue");
                    hotelCustomerRoute = await getRoute([data.hotel_lat, data.hotel_lng], [data.customer_lat, data.customer_lng], "red");

                    riderHotelRoute.addTo(map);
                    hotelCustomerRoute.addTo(map);

                    map.fitBounds([
                        [data.hotel_lat, data.hotel_lng],
                        [data.customer_lat, data.customer_lng],
                        [data.rider_lat, data.rider_lng]
                    ], {
                        padding: [50, 50]
                    });
                })
                .catch(err => console.error(err));
        }

        updateTracking();
        setInterval(updateTracking, 10000); // Refresh every 10 seconds
    </script>



</body>

</html>