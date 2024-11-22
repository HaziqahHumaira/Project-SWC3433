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

// Fetch room data from the database
$query = "SELECT * FROM rooms";
$stmt = $pdo->query($query);
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Booking handler
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selectedRoom = $_POST['room_type'];
    $guests = $_POST['guests'];
    $roomPrice = 0;

    // Find the selected room's price
    foreach ($rooms as $room) {
        if ($room['room_type'] == $selectedRoom) {
            $roomPrice = $room['price'];
            break;
        }
    }

    // Calculate total price based on guests
    $totalPrice = $roomPrice * $guests;
    echo "<script>alert('You have booked a $selectedRoom for $guests guests. Total price: RM $totalPrice');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Rooms</title>
    <style>
        /* General Styles for Page */
        body {
            font-family: Arial,;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            color: #333;
        }

        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h2 {
            font-size: 2.5rem;
            color: #2f40be;
            text-align: center;
            margin-bottom: 40px;
            font-family: 'Caveat Brush', cursive;
        }

        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #494c8e;
            color: white;
        }

        .logo img {
            width: 70px;
        }

        .navbar {
            flex-grow: 1;
            display: flex;
            justify-content: center;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            gap: 40px;
        }

        .navbar ul li a {
            text-decoration: none;
            color: #ffffff;
            font-size: 18px;
        }

        .navbar ul li a:hover {
            color: #17b4db;
        }

        /* Room Section Styles */
        .room {
            display: flex;
            flex-direction: row;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .room img {
            width: 50%;
            height: auto;
            object-fit: cover;
        }

        .room-info {
            padding: 20px;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .room-info h3 {
            font-size: 1.8rem;
            color: #152f51;
            margin-bottom: 15px;
        }

        .room-info p {
            font-size: 1rem;
            margin: 5px 0;
        }

        .room-info ul {
            list-style: none;
            padding: 0;
            margin: 10px 0;
        }

        .room-info ul li {
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #17b4db;
            margin-top: 10px;
        }

        /* Booking Form Styles */
        form {
            margin-top: 15px;
        }

        form label {
            font-weight: bold;
            margin-right: 10px;
        }

        form select {
            padding: 5px 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form button {
            padding: 10px 20px;
            background-color: #152f51;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-left: 10px;
        }

        form button:hover {
            background-color: #e67342;
            transform: translateY(-3px);
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .room {
                flex-direction: column;
            }

            .room img {
                width: 100%;
                height: 200px;
            }

            .room-info {
                width: 100%;
            }

            h2 {
                font-size: 2rem;
            }

            .navbar ul {
                gap: 20px;
            }

            .navbar ul li a {
                font-size: 16px;
            }
        }
    </style>
</head>
<header class="header">
    <div class="logo">
        <img src="logo2.jpg" alt="Logo">
    </div>
    <nav class="navbar">
        <ul>
            <li><a href="Project.html">Home</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="http://localhost/iqa/rooms.php">Room</a></li>
            <li><a href="gallery.html">Gallery</a></li>
            <li><a href="faq.html">Faq</a></li>
        </ul>
    </nav>
</header>
<body>
<main>
    <h2>Our Rooms</h2>

    <?php foreach ($rooms as $room): ?>
    <section class="room">
        <img src="<?php echo $room['image']; ?>" alt="<?php echo $room['room_type']; ?>">
        <div class="room-info">
            <h3><?php echo $room['room_type']; ?></h3>
            <p><?php echo $room['description']; ?></p>
            <ul>
                <li>Bed: <?php echo $room['bed_type']; ?></li>
                <li>Capacity: <?php echo $room['capacity']; ?> Guests</li>
                <li>View: <?php echo $room['view']; ?></li>
                <li><?php echo implode(', ', explode(',', $room['amenities'])); ?></li>
            </ul>
            <p class="price">Price: RM<?php echo $room['price']; ?>/night</p>

            <!-- Booking Form -->
            <form method="GET" action="book_now.php">
    <label for="guests_<?php echo $room['id']; ?>">Number of Guests:</label>
    <select name="guests" id="guests_<?php echo $room['id']; ?>" required>
        <?php for ($i = 1; $i <= $room['capacity']; $i++): ?>
            <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo ($i > 1) ? 'Guests' : 'Guest'; ?></option>
        <?php endfor; ?>
    </select>
    <input type="hidden" name="room_type" value="<?php echo $room['room_type']; ?>">
    <input type="hidden" name="total_price" value="<?php echo $room['price']; ?>"> <!-- You can adjust this to reflect the number of guests -->
    <button type="submit">Book Now</button>
</form>
</form>

        </div>
    </section>
    <?php endforeach; ?>
</main>
</body>
</html>
