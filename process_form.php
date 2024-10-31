<?php
// Database connection
$servername = "localhost";
$username = "username";
$password = "";
$dbname = "gymko";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Form data
$name = $_POST['name'];
$phone = $_POST['phone'];
$date = $_POST['date'];

// SQL query to insert data
$sql = "INSERT INTO bookings (name, phone, date) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $phone, $date);

// Execute the statement
if ($stmt->execute()) {
    echo "Appointment booked successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close statement and database connection
$stmt->close();
$conn->close();
?>
