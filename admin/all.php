<?php
    require('inc/essentials.php');
    require('inc/db_config.php');
    adminLogin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment</title>

    <?php require('inc/links.php');?>

</head>
<body class="bg-light">
    
    <?php require('inc/header.php');?>

    <div class="container-fluid" id="main-content">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
              <h3 class="mb-4">All Transactions</h3>


                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">

                        <div class="text-end mb-4">
                            <input type="text" oninput="search_user(this.value)" class="form-control shadow-none w-25 ms-auto" placeholder="Search...">
                        </div>

                        <div class="table-responsive-md" style="height: 450px; overflow-y: scroll;">
                            <table class="table table-hover border">
                                <thead class="sticky-top">
                                    <tr class="bg-dark text-light ambot">
                                    <th scope="col">#</th>
                                        <th scope="col">User Name</th>
                                        <th scope="col">User Email</th>
                                        <th scope="col">Product/Plan</th>
                                        <th scope="col" >Price</th>
                                        <th scope="col">Payment Method</th>
                                        <th scope="col">Payment Status</th>
                                        <th scope="col" class="text-center">Created At</th>  
                                    </tr>
                                </thead>
                                <tbody id="users-data">
                                    <?php
                                        $search = isset($_GET['search']) ? $_GET['search'] : '';

                                        $sql = "SELECT 
                                                    o.order_id,
                                                    o.user_name,
                                                    o.user_email,
                                                    o.product_name,
                                                    o.price,
                                                    o.payment_method,
                                                    o.payment_status,
                                                    o.created_at
                                                FROM 
                                                    orders o
                                                WHERE 
                                                    o.payment_status = 'paid'
                                                UNION ALL
                                                SELECT 
                                                    s.id AS order_id,
                                                    s.name AS user_name,
                                                    s.email AS user_email,
                                                    s.plan AS product_name,
                                                    s.price,
                                                    CASE
                                                        WHEN s.payment_id LIKE '%Walk-in%' THEN 'Walk-in'
                                                        ELSE 'E-Wallet'
                                                    END AS payment_method,
                                                    'Paid' AS payment_status,
                                                    s.created_at
                                                FROM 
                                                    subscriptions s
                                                ORDER BY user_name ASC";

                                        if (!empty($search)) {
                                            $sql .= " AND (
                                                        o.user_name LIKE '%$search%' OR
                                                        o.user_email LIKE '%$search%' OR
                                                        o.product_name LIKE '%$search%' OR
                                                        s.name LIKE '%$search%' OR
                                                        s.email LIKE '%$search%' OR
                                                        s.plan LIKE '%$search%'
                                                    )";
                                        }

                                        $result = mysqli_query($con, $sql);

                                        if (mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $row['order_id']; ?></td>
                                                    <td><?php echo $row['user_name']; ?></td>
                                                    <td><?php echo $row['user_email']; ?></td>
                                                    <td><?php echo $row['product_name']; ?></td>
                                                    <td><?php echo $row['price']; ?></td>
                                                    <td><?php echo $row['payment_method']; ?></td>
                                                    <td>
                                                        <span class="badge bg-success">
                                                            <?php echo $row['payment_status']; ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-center"><?php echo $row['created_at']; ?></td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="8" class="text-center">No transactions found.</td>
                                            </tr>
                                            <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <?php require('inc/scripts.php');?>

    <script>
        // This JavaScript code is for the search functionality, 
        // but it's not necessary to keep it in the HTML file.
        // You can move it to a separate JavaScript file (e.g., scripts/appointment.js)
        // and include it in your HTML using the <script> tag.
        document.addEventListener('DOMContentLoaded', function() {
            //load_paid_transactions(); // This function is not needed in this combined code
        });

        function search_user(value) {
            // This function is for the search functionality and is not needed in this combined code
        }
    </script>

</body>
</html>