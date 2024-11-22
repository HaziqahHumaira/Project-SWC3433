<?php
// Connect to the database
$servername = "localhost";
$username = "root"; // Change this to your DB username
$password = "syakila03"; // Change this to your DB password
$dbname = "iqa"; // Change this to your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the rooms from the database
$sql = "SELECT * FROM rooms";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Catalog</title>
    <style>
        body {
            font-family: Arial, ;
            background-color: #f4f4f9;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #494c8e;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .room-price {
            color: #494c8e;
            font-weight: bold;
        }
        .table-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .room-select {
            padding: 5px;
            width: 80px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="table-container">
        <h1>Room Catalog</h1>
        <table>
            <thead>
                <tr>
                    <th>Room Type</th>
                    <th>Max Guests</th>
                    <th>Price</th>
                    <th>Guests</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        // Set the price based on the selected number of guests
                        echo "<tr>";
                        echo "<td>" . $row["room_type"] . "</td>";
                        echo "<td>" . $row["max_guests"] . "</td>";
                        echo "<td class='room-price'>$" . $row["base_price"] . " per night</td>";
                        echo "<td>
                                <select class='room-select' name='guests' id='guests_" . $row["id"] . "' onchange='updatePrice(" . $row["id"] . ", " . $row["base_price"] . ", " . $row["max_guests"] . ")'>
                                    ";
                                    for ($i = 1; $i <= $row["max_guests"]; $i++) {
                                        echo "<option value='$i'>$i</option>";
                                    }
                        echo "</select>
                              </td>";
                        echo "<td><span id='price_" . $row["id"] . "'>$" . $row["base_price"] . "</span></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No rooms available</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        // Function to update the price based on the selected number of guests
        function updatePrice(roomId, basePrice, maxGuests) {
            const selectedGuests = document.getElementById('guests_' + roomId).value;
            const totalPrice = basePrice * selectedGuests;
            document.getElementById('price_' + roomId).innerText = "$" + totalPrice.toFixed(2);
        }
    </script>

</body>
</html>

<?php
$conn->close();
?>
