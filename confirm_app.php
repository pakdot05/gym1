<?php

function build_calendar($month, $year, $trainor_id) {
    global $con;
    // Create array containing abbreviations of days of week.
    $daysOfWeek = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');

    // What is the first day of the month in question?
    $firstDayOfMonth = mktime(0,0,0,$month,1,$year);

    // How many days does this month contain?
    $numberDays = date('t', $firstDayOfMonth);

    // Retrieve some information about the first day of the month in question.
    $dateComponents = getdate($firstDayOfMonth);

    // What is the name of the month in question?
    $monthName = $dateComponents['month'];

    // What is the index value (0-6) of the first day of the month in question.
    $dayOfWeek = $dateComponents['wday'];

    // Create the table tag opener and day headers
    $datetoday = date('Y-m-d');
    $calendar = "<table class='table table-bordered'>";
    $calendar .= "<center><h3 class='mt-3'>$monthName $year</h3>";

    $calendar .= "<a class='btn btnn btn-xs custom-bg text-white' style='width: 170px;' href='?trainor_id=".$trainor_id."&month=".date('m', mktime(0, 0, 0, $month-1, 1, $year))."&year=".date('Y', mktime(0, 0, 0, $month-1, 1, $year))."'> < Previous Month</a> ";
    $calendar .= "<a class='btn btnn btn-xs custom-bg text-white' href='?trainor_id=".$trainor_id."&month=".date('m')."&year=".date('Y')."'>Current Month</a> ";
    $calendar .= "<a class='btn btnn btn-xs custom-bg text-white' style='width: 170px;' href='?trainor_id=".$trainor_id."&month=".date('m', mktime(0, 0, 0, $month+1, 1, $year))."&year=".date('Y', mktime(0, 0, 0, $month+1, 1, $year))."'>Next Month > </a></center><br>";

    $calendar .= "<tr>";
    foreach($daysOfWeek as $day) {
        $calendar .= "<th class='header' style='font-size: 20px;'>$day</th>";
    }
    $calendar .= "</tr><tr>";

    // Adjust for the first day of the month
    if ($dayOfWeek > 0) {
        for ($k=0; $k<$dayOfWeek; $k++) {
            $calendar .= "<td class='empty'></td>";
        }
    }

    // Initiate day counter
    $currentDay = 1;
    $month = str_pad($month, 2, "0", STR_PAD_LEFT);

    while ($currentDay <= $numberDays) {
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= "</tr><tr>";
        }

        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";
        $today = $date == date('Y-m-d') ? "today" : "";

        if ($date < date('Y-m-d')) {
            $calendar .= "<td><h4>$currentDay</h4> <button class='btn btn-danger btn-xs' style='font-size: 15px;'>N/A</button>";
        } else {
            $totalbookings = checkSlots($date); // Corrected function call
            if ($totalbookings == 22) {
                $calendar .= "<td class='$today'><h4>$currentDay</h4> <a href='#' class='btn btn-danger btn-xs' style='font-size: 15px;'>Appointment Full</a>";
            } else {
                $calendar .= "<td class='$today'><h4>$currentDay</h4> <a href='book.php?date=".$date."' class='btn btnn btn-success btn-xs' style='font-size: 15px;'>Appoint</a>";
            }
        }

        $calendar .= "</td>";
        $currentDay++;
        $dayOfWeek++;
    }

    // Complete the row of the last week in month, if necessary
    if ($dayOfWeek != 7) {
        $remainingDays = 7 - $dayOfWeek;
        for ($l=0; $l<$remainingDays; $l++) {
            $calendar .= "<td class='empty'></td>";
        }
    }

    $calendar .= "</tr></table>";
    echo $calendar;
}

function checkSlots($date) {
    global $con; // Use the global $con defined in config.php
    $stmt = $con->prepare("SELECT COUNT(*) AS total FROM bookings WHERE date = ?");
    $stmt->bind_param('s', $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalbookings = $result->fetch_assoc()['total'];
    $stmt->close();
    return $totalbookings;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GYM - Confirm Appointment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

    <!-- LINKS -->
    <?php require('inc/links.php') ?>

    <style>
        .btnn {
            background-color: #09858d !important;
            border: 1px solid #09858d !important;
        }
        .btnn:hover {
            background-color: #096066 !important;
        }
        .btn1 {
            border: 1px solid #09858d !important;
            color: #09858d !important;
            text-decoration: none !important;
        }
        .btn1:hover {
            background-color: #096066 !important;
            color: white !important;
        }
    </style>
</head>

<body class="bg-light">
    <?php require('inc/header.php') ?>

    <?php 
        if (!isset($_GET['trainor_id']) || $settings_r['shutdown'] == true) {
            redirect('trainors.php');
        } else if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
            redirect('trainors.php');
        }

        $data = filteration($_GET);
        $trainor_res = select("SELECT * FROM `trainor` WHERE `trainor_id`=? AND `status`=? AND `removed`=?", [$data['trainor_id'], 1, 0], 'iii');

        if (mysqli_num_rows($trainor_res) == 0) {
            redirect('trainors.php');
        }

        $trainor_data = mysqli_fetch_assoc($trainor_res);
        
        $_SESSION['trainor'] = [
            "trainor_id" => $trainor_data['trainor_id'],
            "name" => $trainor_data['name'],
            "price" => $trainor_data['price'],
            "payment" => null,
            "available" => false,
        ];

        $user_res = select("SELECT * FROM `user_cred` WHERE `user_id`=? LIMIT 1", [$_SESSION['uId']], "i");
        $user_data = mysqli_fetch_assoc($user_res);
    ?>

    <div class="my-5 px-4">
        <h2 class="fw-bold text-center">APPOINTMENT</h2>
        <div class="h-line bg-dark"></div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-12 bg-white shadow">
                <?php
                    $dateComponents = getdate();
                    if (isset($_GET['month']) && isset($_GET['year'])) {
                        $month = $_GET['month'];
                        $year = $_GET['year'];
                    } else {
                        $month = $dateComponents['mon'];
                        $year = $dateComponents['year'];
                    }
                    echo build_calendar($month, $year, $data['trainor_id']);
                ?>
            </div>
        </div>
    </div>

    <?php require('inc/footer.php') ?>
</body>
</html>
