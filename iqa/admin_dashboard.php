<?php
// Start session and check if the admin is logged in
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
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

// Fetch all bookings from the database, including check-in and check-out dates
$query = "SELECT * FROM bookings";
$stmt = $pdo->prepare($query);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Logout functionality
if (isset($_GET['logout'])) {
    session_start();
    session_destroy();
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial,;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
        }

        main {
            max-width: 1000px;
            margin: 30px auto;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        button {
            background-color: #e74c3c;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #c0392b;
        }

        .logout-button {
            padding: 10px 20px;
            background-color: #152f51;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 20px auto;
        }

        .logout-button:hover {
            background-color: #e67342;
        }
    </style>
</head>
<body>

<main>
    <h2>Admin Dashboard</h2>

    <!-- Logout button -->
    <a href="admin_dashboard.php?logout=true">
        <button class="logout-button">Logout</button>
    </a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Room Type</th>
                <th>Guests</th>
                <th>Total Price (RM)</th>
                <th>Check-In Date</th>
                <th>Check-Out Date</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($bookings): ?>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo $booking['id']; ?></td>
                        <td><?php echo $booking['name']; ?></td>
                        <td><?php echo $booking['email']; ?></td>
                        <td><?php echo $booking['phone']; ?></td>
                        <td><?php echo $booking['room_type']; ?></td>
                        <td><?php echo $booking['guests']; ?></td>
                        <td><?php echo $booking['total_price']; ?></td>
                        <td><?php echo $booking['check_in']; ?></td>
                        <td><?php echo $booking['check_out']; ?></td>
                        <td><?php echo $booking['created_at']; ?></td>
                        <td>
                            <button onclick="window.location.href='delete_booking.php?id=<?php echo $booking['id']; ?>'">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" style="text-align: center;">No bookings found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

</body>
</html>
