<?php
    require('inc/db_config.php');
    require('inc/essentials.php');
    adminLogin();

    if(isset($_POST['get_approved_count'])) {
        $query = "SELECT COUNT(*) AS total FROM `bookings` WHERE `status` = 1"; // Count users with approved status
        $result = select($query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            echo json_encode(['count' => $row['total']]); // Echo the total count as JSON
        } else {
            echo json_encode(['count' => 0]); // Echo 0 if query fails
        }
        exit; // Exit to prevent further execution
    }




    // Execute SQL query to count patients
    $patient_result = mysqli_query($con, "SELECT COUNT(*) AS patient_count FROM user_cred");
    $patient_row = mysqli_fetch_assoc($patient_result);
    $patient_count = $patient_row['patient_count'];



    $subscribe_result = mysqli_query($con, "SELECT COUNT(*) AS subscription_count FROM subscriptions");
    $subscribe_row = mysqli_fetch_assoc($subscribe_result);
    $subscribe_count = $subscribe_row['subscription_count'];

    // Execute SQL query to count unapproved bookings
    $product_result = mysqli_query($con, "SELECT COUNT(*) AS product_count FROM products");
    $product_row = mysqli_fetch_assoc($product_result);
    $product_count = $product_row['product_count'];

    // Execute SQL query to count bookings dated today with status = 1
    $today_date = date('Y-m-d');
    $today_approved_booking_result = mysqli_query($con, "SELECT COUNT(*) AS booking_count FROM bookings WHERE `date` = '$today_date' AND `status` = 1");
    $today_approved_booking_row = mysqli_fetch_assoc($today_approved_booking_result);
    $today_approved_booking_count = $today_approved_booking_row['booking_count'];

    // Execute SQL query to calculate total earnings from invoices
    $earnings_result = mysqli_query($con, "SELECT SUM(price) AS total_earnings FROM subscriptions");
    $earnings_row = mysqli_fetch_assoc($earnings_result);
    $total_earnings = $earnings_row['total_earnings'] ?? 0; // If no earnings, set it to 0

    mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
    <link rel="stylesheet" href="../css/common.css">

    <?php require('inc/links.php');?>

    <style>

        :root {
            --blue: #40534C;
            --blue-hover: #096066;
            --red: #ff0000;
        }

        .dashboard-box {
            padding: 20px;
            background-color: var(--blue);
            border-radius: 8px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .dashboard-box:hover {
            background-color: var(--blue-hover);
        }

        .dashboard-link {
            text-decoration: none;
            color: inherit;
        }

        th{
            background-color: #40534C !important;
        }

    </style>

</head>
<body class="bg-light">

<?php require('inc/header.php');?>
<div class="container-fluid" id="main-content">
    <div class="row">
        <div class="col-lg-10 ms-auto p-4 overflow-hidden">
            <h3 class="mb-4">DASHBOARD</h3>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <a href="book.php" class="dashboard-link">
                        <div class="dashboard-box">
                            <h5>Today's Appointment</h5>
                            <i class="fa fa-calendar m-2" style="font-size:30px;"></i>
                            <p>Total Patients: <?php echo $today_approved_booking_count; ?></p>
                        </div>
                    </a>
                </div>

         
                                <div class="col-lg-3 col-md-6 mb-4">
                    <a href="invoices.php" class="dashboard-link">
                        <div class="dashboard-box">
                            <h5>Subscriber</h5>
                            <i class="fa fa-book m-2" style="font-size:30px;"></i>
                            <p>Total Subscriber: <?php echo $subscribe_count; ?></p>  <!-- Corrected -->
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <a href="completed.php" class="dashboard-link">
                        <div class="dashboard-box">
                            <h5>Product</h5>
                            <i class="fa fa-book m-2" style="font-size:30px;"></i>
                            <p>Total Product: <?php echo $product_count; ?></p>
                        </div>
                    </a>
                </div>

                <!-- Patients Box -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <a href="users.php" class="dashboard-link">
                        <div class="dashboard-box">
                            <h5>Member</h5>
                            <i class="fa fa-user m-2" style="font-size:30px;"></i>
                            <p>Total Member: <?php echo $patient_count; ?></p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <a href="invoices.php" class="dashboard-link">
                        <div class="dashboard-box">
                            <h5>Earn</h5>
                            <i class="fa fa-money m-2" style="font-size:30px;"></i>
                            <p>Total Earnings: ₱<?php echo number_format($total_earnings, 2); ?></p>
                        </div>
                    </a>
                </div>

          

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="text-end mb-4">
                        <input type="text" oninput="search_user(this.value)" class="form-control shadow-none w-25 ms-auto" placeholder="Search...">
                    </div>

                    <div class="table-responsive-md" style="height: 450px; overflow-y: scroll;">
                        <table class="table table-hover border">
                            <thead>
                                <tr class="bg-dark text-light">
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone no.</th>
                                    <th scope="col" width="20%">Note</th>
                                    <th scope="col">Trainor</th>
                                    <th scope="col">Date</th>
                                    <th scope="col" width="8%">Time</th>
                                    <th scope="col" class="text-center">Status</th>   
                                    <th scope="col" class="text-center" width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="users-data">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require('inc/scripts.php'); ?>
<script src="scripts/dashboard.js"></script>
</body>
</html>
