<?php
// Database connection details
$host = 'localhost';
$dbname = 'iqa';
$username = 'root';
$password = 'syakila03';

// Create a PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Retrieve booking information from URL (GET)
if (isset($_GET['room_type'], $_GET['guests'], $_GET['total_price'])) {
    $room_type = $_GET['room_type'];
    $guests = $_GET['guests'];
    $total_price = $_GET['total_price'];
} else {
    die("Required parameters are missing.");
}

// Handle form submission for booking
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $room_type = $_POST['room_type'];
    $guests = $_POST['guests'];
    $total_price = $_POST['total_price'];

    // Insert booking data into the 'bookings' table
    $query = "INSERT INTO bookings (name, email, phone, room_type, guests, total_price) 
              VALUES (:name, :email, :phone, :room_type, :guests, :total_price)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':room_type', $room_type);
    $stmt->bindParam(':guests', $guests);
    $stmt->bindParam(':total_price', $total_price);
    $stmt->execute();

    echo "<script>alert('Your booking has been successfully made!'); window.location.href = 'thank_you.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Form</title>
    <style>
        /* Styles for the registration form */
        body {
            font-family: Arial,;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        main {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            font-size: 2rem;
            color: #2f40be;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="email"], input[type="number"], select {
            width: 100%;
            padding: 10px;
            margin: 5px 0 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            background-color: #152f51;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }

        button:hover {
            background-color: #e67342;
        }
    </style>
</head>
<body>
    <main>
        <h2>Booking Registration</h2>
        <form method="POST" action="booking_summary.php">
    <label for="name">Full Name</label>
    <input type="text" id="name" name="name" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>

    <label for="phone">Phone Number</label>
    <input type="text" id="phone" name="phone" required>

    <label for="room_type">Room Type</label>
    <input type="text" id="room_type" name="room_type" value="<?php echo htmlspecialchars($room_type); ?>" readonly>

    <label for="guests">Number of Guests</label>
    <input type="number" id="guests" name="guests" value="<?php echo htmlspecialchars($guests); ?>" readonly>

    <label for="total_price">Total Price (RM)</label>
    <input type="number" id="total_price" name="total_price" value="<?php echo htmlspecialchars($total_price); ?>" readonly>

    <!-- Add Check-In and Check-Out fields -->
    <label for="check_in">Check-In Date</label>
    <input type="date" id="check_in" name="check_in" required>

    <label for="check_out">Check-Out Date</label>
    <input type="date" id="check_out" name="check_out" required>

    <button type="submit">Confirm Booking</button>
</form>



    </main>
</body>
</html>
