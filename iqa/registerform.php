<?php
// Database connection credentials
$host = 'localhost'; // Replace with your database host
$username = 'root'; // Replace with your database username
$password = 'syakila03'; // Replace with your database password
$database = 'iqa'; // Replace with your database name

// Connect to the database
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room = isset($_POST['room']) ? $_POST['room'] : 'N/A';
    $checkIn = isset($_POST['checkIn']) ? $_POST['checkIn'] : 'N/A';
    $checkOut = isset($_POST['checkOut']) ? $_POST['checkOut'] : 'N/A';
    $name = isset($_POST['name']) ? $_POST['name'] : 'N/A';
    $number = isset($_POST['number']) ? $_POST['number'] : 'N/A';
    $email = isset($_POST['email']) ? $_POST['email'] : 'N/A';
    $specialRequests = isset($_POST['specialRequests']) ? $_POST['specialRequests'] : 'None';

    // Insert booking details into the database
    $sql = "INSERT INTO bookings (room, check_in, check_out, customer_name, phone_number, email, special_requests)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $room, $checkIn, $checkOut, $name, $number, $email, $specialRequests);

    if ($stmt->execute()) {
        $message = "Booking successfully saved!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Booking Form</title>
    <style>
        body {
            font-family: Arial,;
            background-color: #f8f8f8;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input, select, textarea {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #494c8e;
        }
        .message {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Room Booking</h1>
        <form method="POST" action="">
            <label for="room">Room</label>
            <select id="room" name="room" required>
                <option value="Deluxe">Deluxe</option>
                <option value="Suite">Suite</option>
                <option value="Standard">Standard</option>
            </select>

            <label for="checkIn">Check-In Date</label>
            <input type="date" id="checkIn" name="checkIn" required>

            <label for="checkOut">Check-Out Date</label>
            <input type="date" id="checkOut" name="checkOut" required>

            <label for="name">Customer Name</label>
            <input type="text" id="name" name="name" placeholder="Enter your full name" required>

            <label for="number">Phone Number</label>
            <input type="tel" id="number" name="number" placeholder="Enter your phone number" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email address" required>

            <label for="specialRequests">Special Requests</label>
            <textarea id="specialRequests" name="specialRequests" rows="4" placeholder="Enter any special requests"></textarea>

            <button type="submit">Submit Booking</button>
        </form>

        <?php if (isset($message)) { echo "<div class='message'>$message</div>"; } ?>
    </div>
</body>
</html>
