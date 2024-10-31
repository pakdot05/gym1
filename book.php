<?php

require('inc/links.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
if (!isset($_SESSION['uId'])) {
    die("User not logged in");
}

$user_id = $_SESSION['uId'];
$statusSql = "SELECT appointment_status FROM user_cred WHERE user_id = ?";
$statusResult = select($statusSql, [$user_id], 'i');
$userStatus = $statusResult->fetch_assoc();

$trainer_contact = '';
$trainer_id = isset($_SESSION['trainor']['trainor_id']) ? $_SESSION['trainor']['trainor_id'] : null;
$trainer_email = '';

if ($trainer_id) {
    // Retrieve trainer's contact number and email
    $trainerSql = "SELECT contact_no, email FROM trainor WHERE trainor_id = ?";
    $trainerResult = select($trainerSql, [$trainer_id], 'i');
    if ($trainerResult->num_rows > 0) {
        $trainerData = $trainerResult->fetch_assoc();
        $trainer_contact = $trainerData['contact_no'];
        $trainer_email = $trainerData['email'];
    }
}

// Get current time in Philippine Timezone
date_default_timezone_set('Asia/Manila');
$currentTime = new DateTime();

// Define the timeslot variables OUTSIDE the function
$duration = 30;
$cleanup = 0;
$start = "07:00";
$end = "20:00";

// Function declaration
function timeslots($duration, $cleanup, $start, $end) {
    if (empty($start) || empty($end)) {
        return [];
    }

    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = new DateInterval("PT" . $duration . "M");
    $cleanupInterval = new DateInterval("PT" . $cleanup . "M");
    $slots = array();

    global $currentTime;
    if ($start < $currentTime) {
        $start = $currentTime;
    }

    $start->setTime($start->format('H'), round($start->format('i') / 30) * 30);

    $minAcceptableTime = clone $currentTime;
    $minAcceptableTime->add(new DateInterval('PT30M'));

    $endPeriod = clone $start;
    $endPeriod->add($interval);

    for ($intStart = $start; $endPeriod < $end; $intStart->add($interval)->add($cleanupInterval)) {
        $endPeriod = clone $intStart;
        $endPeriod->add($interval);

        $isPast = ($intStart < $currentTime);
        $isAcceptable = ($intStart >= $minAcceptableTime);

        if ($isAcceptable) {
            $slots[] = array(
                'slot' => $intStart->format("H:iA") . "-" . $endPeriod->format("H:iA"),
                'isPast' => $isPast
            );
        }
    }

    return $slots;
}

// Call the function with arguments and store the result
$timeslots = timeslots($duration, $cleanup, $start, $end);

// Retrieve bookings
if (isset($_GET['date'])) {
    $date = $_GET['date'];
    $date = date('Y-m-d', strtotime($date));

    $bookingsSql = "SELECT timeslot FROM bookings WHERE date = ?";
    $bookingsResult = select($bookingsSql, [$date], 's');
    $bookings = array();
    if ($bookingsResult->num_rows > 0) {
        while ($row = $bookingsResult->fetch_assoc()) {
            $bookings[] = $row['timeslot'];
        }
    }
}

if (isset($_POST['submit']) && $userStatus['appointment_status'] != 'pending' && $userStatus['appointment_status'] != 'approved') {
    $user_id = $_SESSION['uId'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phonenum = $_POST['phonenum'];
    $note = $_POST['note'];
    $trainor_name = $_POST['trainor_name'];
    $timeslot = $_POST['timeslot'];

    // Check if the timeslot is already booked
    $checkBookingSql = "SELECT * FROM bookings WHERE date = ? AND timeslot = ?";
    $checkBookingResult = select($checkBookingSql, [$date, $timeslot], 'ss');
    if ($checkBookingResult->num_rows > 0) {
        // Slot is already booked
    } else {
        // Insert the new booking
        $insertBookingSql = "INSERT INTO bookings (user_id, name, timeslot, email, phonenum, note, date, trainor_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $insertBookingResult = insert($insertBookingSql, [$user_id, $name, $timeslot, $email, $phonenum, $note, $date, $trainor_name], 'isssssss');

        // Update appointment status in user_cred table to pending
        $updateStatusSql = "UPDATE user_cred SET appointment_status = 'pending' WHERE user_id = ?";
        $updateStatusResult = update($updateStatusSql, [$user_id], 'i');

        // Display success message
        $msg = "<div class='alert alert-success'>Appointment Submitted!</div>";
        $bookings[] = $timeslot; 

        // Send email to both user and trainer
        require_once './phpmailer/src/Exception.php';
        require_once './phpmailer/src/PHPMailer.php';
        require_once './phpmailer/src/SMTP.php';

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'lorem.ipsum.sample.email@gmail.com';
            $mail->Password   = 'tetmxtzkfgkwgpsc';
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            // Send email to user
            $mail->setFrom('lorem.ipsum.sample.email@gmail.com', 'Geafitnessgym');
            $mail->addAddress($_POST["email"]);
            $mail->addReplyTo('lorem.ipsum.sample.email@gmail.com', 'Geafitnessgym');

            // Content for user email
            $mail->isHTML(true);
            $mail->Subject = 'Appointment Request Confirmation - Geafitnessgym';
            $mail->Body = "
                <p>Dear {$name},</p>

                <p>Your appointment request has been submitted successfully.</p>

                <p><strong>Details:</strong></p>

                <ul>
                    <li><strong>Date:</strong> {$date}</li>
                    <li><strong>Timeslot:</strong> {$timeslot}</li>
                    <li><strong>Trainor:</strong> {$trainor_name}</li>
                    <li><strong>Trainor's Contact:</strong> {$trainer_contact}</li>
                    <li><strong>Email:</strong> {$email}</li>
                    <li><strong>Phone:</strong> {$phonenum}</li>
                    <li><strong>Note:</strong> {$note}</li>
                </ul>

                <p>You will receive another email once your appointment is confirmed or declined.</p>

                <p>Thank you,</p>

                <p>Gymko Team</p>
            ";

            $mail->send();

            // Send email to trainer
            $mail->clearAddresses();
            $mail->addAddress($trainer_email);

            // Content for trainer email
            $mail->Subject = 'New Appointment Request at Geafitnessgym';
            $mail->Body = "
                <p>Dear {$trainor_name},</p>

                <p>You have a new appointment request from {$name}.</p>

                <p><strong>Details:</strong></p>

                <ul>
                    <li><strong>Date:</strong> {$date}</li>
                    <li><strong>Timeslot:</strong> {$timeslot}</li>
                    <li><strong>Email:</strong> {$email}</li>
                    <li><strong>Phone:</strong> {$phonenum}</li>
                    <li><strong>Note:</strong> {$note}</li>
                </ul>

                <p>Please review the request and confirm or decline the appointment.</p>

                <p>Thank you,</p>

                <p>Gymko Team</p>
            ";

            $mail->send();

            echo "
                <script>
                alert('Message was sent successfully. Thank you for reaching us!');
                document.location.href = 'book.php';
                </script>
            ";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        $stmt->close();
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book Appointment</title>
    <style>
        .btn{
            background-color:  #323232 !important;
            color: white !important;
        }
        .btn:hover{
            background-color: #096066 !important;
        }
        .btn1{
            border: 1px solid #09858d !important;
            color: #09858d !important;
            text-decoration: none !important;
        }
        .btn1:hover{
            background-color: #096066 !important;
            color: white !important;
        }
    </style>
</head>
<body class="bg-light">
    <?php require('inc/header.php') ?>

    <div class="container">
        <div class="row d-flex justify-content-center bg-light shadow mt-5 rounded mb-3 p-2">
            <div class="mt-4">
                <h2 class="text-center">Book for Date: 
                    <?php
                        if (isset($date)) {
                            echo date('m/d/Y', strtotime($date)); 
                        } else {
                            echo "Please select a date"; 
                        }
                    ?>
                </h2>
            </div>
            <div class="col-md-12">
                <?php echo isset($msg) ? $msg : ""; ?>
            </div>
            <div class="mb-2" style="font-size: 14px; margin-left: 15px;">
                <a href="#" onclick="goBack()" class="text-secondary text-decoration-none"> < BACK</a>
            </div>
            <hr style="width: 98%;">
            <?php if ($userStatus['appointment_status'] == 'pending') { ?>
                <div class="alert alert-warning" style="height: 150px;">You already have a pending appointment. Please complete or cancel your current appointment before booking a new one.</div>
                <br><br><br>
            <?php } elseif ($userStatus['appointment_status'] == 'approved') { ?>
                <div class="alert alert-warning" style="height: 150px;">You already have an appointment. Please complete or cancel your current appointment before booking a new one.</div>
                <br><br><br>
            <?php } else { ?>
                <div class="col-md-12">
                    <?php
                        if (isset($date) && isset($timeslots)) {
                            // Generate the timeslots
                            foreach ($timeslots as $ts) {
                                // Debugging: Check the timeslot and whether it's past
                                echo "<!-- Debug: Timeslot generated: " . $ts['slot'] . " | Is Past: " . ($ts['isPast'] ? 'Yes' : 'No') . " -->";

                                // Access the 'slot' key from the $ts array
                                $timeslotString = $ts['slot']; 
                                $parts = explode(' (', $timeslotString); 
                                $timeslot = $parts[0]; 

                                // Debugging: Check if the timeslot is booked
                                echo "<!-- Debug: Checking if booked: " . $timeslot . " | Bookings: " . implode(', ', $bookings) . " -->";

                                if (isset($bookings) && in_array($timeslot, $bookings)) { // Check if $bookings is set
                                    echo '<button class="btn m-1 p-2 appoint unavailable haber" style="width: 170px; background-color: #dc3545 !important;" disabled>' . $timeslot . '</button>';
                                } else if ($ts['isPast']) {
                                    // Skip past timeslots
                                    echo "<!-- Debug: Skipping past timeslot: " . $timeslot . " -->";
                                    continue;
                                } else {
                                    echo '<button class="btn m-1 p-2 appoint" style="width: 170px;" data-bs-toggle="modal" data-bs-target="#myModal" data-timeslot="' . $timeslot . '">' . $timeslot . '</button>';
                                }
                            }
                        } else {
                            echo "Please select a date.";
                        }
                    ?>
                </div>
            <?php } ?>
            <br><br><br><br>
        </div>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Appointment: <span id="slot"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <div class="form-group mb-3">
                            <label for="timeslot">Timeslot</label>
                            <input required type="text" readonly name="timeslot" id="timeslot" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="trainor_name">Trainor</label>
                            <input required type="text" readonly name="trainor_name" id="trainor_name" class="form-control" value="<?php echo isset($_SESSION['trainor']['name']) ? $_SESSION['trainor']['name'] : ''; ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="trainer_contact">Trainer's Contact</label>
                            <input required type="text" readonly name="trainer_contact" id="trainer_contact" class="form-control" value="<?php echo $trainer_contact; ?>">
                        </div>
                       <div class="form-group mb-3">
                            <label for="username">Name</label>
                            <input required type="text" name="name" id="username" class="form-control" value="<?php echo isset($_SESSION['uName']) ? $_SESSION['uName'] : ''; ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input required type="email" name="email" id="email" class="form-control" value="<?php echo isset($_SESSION['uEmail']) ? $_SESSION['uEmail'] : ''; ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="phonenum">Phone no.</label>
                            <input required type="number" name="phonenum" id="phonenum" class="form-control" value="<?php echo isset($_SESSION['uPhone']) ? $_SESSION['uPhone'] : ''; ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="note">Note (optional)</label>
                            <textarea name="note" id="note" class="form-control shadow-none" rows="3"></textarea>
                        </div>
                        <div class="form-group text-end">
                            <button type="submit" class="btn custom-bg text-white" name="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        var modal = document.getElementById('myModal');
        var timeslotInput = document.getElementById('timeslot');
        var slotDisplay = document.getElementById('slot');

        modal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget; 
            var timeslot = button.getAttribute('data-timeslot'); 
            timeslotInput.value = timeslot;
            slotDisplay.textContent = timeslot;
        });

        function goBack() {
            window.history.back();
        }
    </script>

    <?php require('inc/footer.php') ?>
</body>;
</html> 