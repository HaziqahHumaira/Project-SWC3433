<?php
// Database connection details
$host = 'localhost';
$dbname = 'iqa';
$username = 'root';
$password = 'syakila03';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Check if the form data has been passed
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture the booking details passed from the form
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $room_type = $_POST['room_type'] ?? '';
    $guests = $_POST['guests'] ?? '';
    $total_price = $_POST['total_price'] ?? '';
    $check_in = $_POST['check_in'] ?? '';
    $check_out = $_POST['check_out'] ?? '';

    // Insert booking data into the 'bookings' table
    $query = "INSERT INTO bookings (name, email, phone, room_type, guests, total_price, check_in, check_out) 
              VALUES (:name, :email, :phone, :room_type, :guests, :total_price, :check_in, :check_out)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':room_type', $room_type);
    $stmt->bindParam(':guests', $guests);
    $stmt->bindParam(':total_price', $total_price);
    $stmt->bindParam(':check_in', $check_in);
    $stmt->bindParam(':check_out', $check_out);
    $stmt->execute();

    // Success message without redirecting
    $message = "Your booking has been successfully made!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Summary</title>
    <style>
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

        .summary-item {
            margin-bottom: 15px;
        }

        .summary-item strong {
            font-weight: bold;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        button {
            padding: 10px 20px;
            background-color: #152f51;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            display: block;
            margin: 20px auto;
        }

        button:hover {
            background-color: #e67342;
        }

        .message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <main>
        <h2>Booking Summary</h2>

        <?php if (isset($message)) { ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <div class="summary-item">
            <strong>Full Name:</strong> <?php echo htmlspecialchars($name); ?>
        </div>
        <div class="summary-item">
            <strong>Email:</strong> <?php echo htmlspecialchars($email); ?>
        </div>
        <div class="summary-item">
            <strong>Phone Number:</strong> <?php echo htmlspecialchars($phone); ?>
        </div>
        <div class="summary-item">
            <strong>Check-In Date:</strong> <?php echo htmlspecialchars($check_in); ?>
        </div>
        <div class="summary-item">
            <strong>Check-Out Date:</strong> <?php echo htmlspecialchars($check_out); ?>
        </div>

        <h3>Room Booking Details</h3>
        <table>
            <thead>
                <tr>
                    <th>Room Type</th>
                    <th>Number of Guests</th>
                    <th>Total Price (RM)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars($room_type); ?></td>
                    <td><?php echo htmlspecialchars($guests); ?></td>
                    <td><?php echo htmlspecialchars($total_price); ?></td>
                </tr>
            </tbody>
        </table>

        <!-- The link will redirect to homepage or booking page -->
        <div class="message">
            <a href="Project.html" style="text-decoration: none; color: #fff; background-color: #152f51; padding: 10px 20px; border-radius: 5px; display: inline-block; font-size: 1rem;">Confirm and Proceed</a>
        </div>
    </main>
</body>
</html>
