<?php
require('inc/db_config.php');
require('inc/essentials.php');

// Initialize variables
$search_query = "";

// Check if there's a search query (from AJAX or form submission)
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);  // Get the search input and sanitize it
}

// Fetch orders from the database with optional search filtering
$query = "SELECT o.order_id, o.product_name, o.quantity, o.payment_status, 
                 o.address, u.name AS user_name, o.payment_method, o.price
          FROM orders o
          JOIN user_cred u ON o.user_id = u.user_id";

// Add search condition to the query
if (!empty($search_query)) {
    $query .= " WHERE o.product_name LIKE ? OR u.name LIKE ? OR o.address LIKE ? OR o.payment_status LIKE ?";
}

$orders = [];
if ($stmt = $con->prepare($query)) {
    if (!empty($search_query)) {
        $search_term = "%" . $search_query . "%";  // Prepare search term for SQL LIKE
        $stmt->bind_param("ssss", $search_term, $search_term, $search_term, $search_term);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    $stmt->close();
} else {
    die("Error fetching orders: " . $con->error);
}

// Handle AJAX request for search
if (isset($_GET['ajax_search'])) {
    if (count($orders) > 0) {
        foreach ($orders as $index => $order) {
            echo "<tr>
                    <td>" . ($index + 1) . "</td>
                    <td>{$order['product_name']}</td>
                    <td>{$order['user_name']}</td>
                    <td>{$order['quantity']}</td>
                    <td>{$order['price']}</td>
                    <td>{$order['address']}</td>
                    <td>{$order['payment_method']}</td>
                    <td>{$order['payment_status']}</td>
                    <td>";

            // Claim button will always be displayed, and change to 'Claimed' upon payment
            if ($order['payment_status'] === 'Paid') {
                echo "<button class='btn btn-success btn-sm' disabled>Claimed</button>";
            } else {
                echo "<button class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#paymentModal'
                        data-order-id='{$order['order_id']}' 
                        data-price='{$order['price']}'>
                        Claim
                      </button>";
            }

            // Always display the delete button
            echo "
                <form method='post' action='product_sales.php' onsubmit='return confirm(\"Are you sure you want to delete this order?\");'>
                    <input type='hidden' name='order_id' value='{$order['order_id']}'>
                    <button type='submit' name='delete_order' class='btn btn-danger btn-sm'>Delete</button>
                </form>
            ";

            echo "</td></tr>";
        }
    } else {
        echo "<tr><td colspan='10' class='text-center'>No orders found</td></tr>";
    }
    exit();
}

// Handle payment processing and order update
if (isset($_POST['order_id']) && isset($_POST['amount'])) {
    $order_id = (int)$_POST['order_id'];
    $amount_received = (float)$_POST['amount'];

    // Fetch the order price
    $query = "SELECT price FROM orders WHERE order_id = ?";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $stmt->close();
    }

    // Update the order status if payment is sufficient
    if ($amount_received >= $price) {
        $updateQuery = "UPDATE orders SET payment_status = 'Paid' WHERE order_id = ?";
        if ($stmt = $con->prepare($updateQuery)) {
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $stmt->close();
        }
        echo "Payment successful";
    } else {
        echo "Insufficient amount";
    }
    exit();
}

// If delete action is triggered
if (isset($_POST['delete_order'])) {
    $order_id = (int)$_POST['order_id'];

    $deleteQuery = "DELETE FROM orders WHERE order_id = ?";
    if ($stmt = $con->prepare($deleteQuery)) {
        $stmt->bind_param("i", $order_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Order successfully deleted.";
        } else {
            $_SESSION['error'] = "Error deleting order: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Error preparing delete statement: " . $con->error;
    }
    header('Location: product_sales.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoices</title>
    <?php require('inc/links.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- AJAX for live search -->
    <script>
        function search_user(query) {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "product_sales.php?ajax_search=1&search=" + query, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById("ordersTableBody").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        document.addEventListener('DOMContentLoaded', function () {
    var orderIdToPay;
    var totalPrice;

    // Payment Modal - Set order data
    var paymentModal = document.getElementById('paymentModal');
    paymentModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        orderIdToPay = button.getAttribute('data-order-id');
        totalPrice = parseFloat(button.getAttribute('data-price'));
        document.getElementById('paymentPrice').innerText = totalPrice.toFixed(2);
        document.getElementById('changeDisplay').style.display = 'none'; // Hide change display initially
        document.getElementById('receivedAmount').value = ''; // Clear previous input
    });

    // Change display based on received amount
    document.getElementById('receivedAmount').addEventListener('input', function () {
        var receivedAmount = parseFloat(this.value);
        if (!isNaN(receivedAmount) && receivedAmount >= totalPrice) {
            var change = receivedAmount - totalPrice;
            document.getElementById('changeDisplay').style.display = 'block';
            document.getElementById('changeAmount').innerText = change.toFixed(2);
        } else {
            document.getElementById('changeDisplay').style.display = 'none'; // Hide change display if input is invalid
        }
    });

    // Confirm Payment Button Action
    document.getElementById('confirmPaymentButton').addEventListener('click', function () {
        var receivedAmount = parseFloat(document.getElementById('receivedAmount').value);
        if (isNaN(receivedAmount) || receivedAmount < totalPrice) {
            alert('Please enter a valid amount equal to or greater than the price.');
            return;
        }

        // Send AJAX request to update payment status
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "product_sales.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                location.reload(); // Reload page after payment
            }
        };
        xhr.send("order_id=" + orderIdToPay + "&amount=" + receivedAmount);
    });
});

    </script>
</head>
<body class="bg-light">

    <?php require('inc/header.php'); ?>

    <div class="container-fluid" id="main-content">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
                <h3 class="mb-4">Invoices</h3>

                <?php
                // Display success or error messages
                if (isset($_SESSION['message'])) {
                    echo "<div class='alert alert-success'>{$_SESSION['message']}</div>";
                    unset($_SESSION['message']); // Clear the message after displaying it
                }

                if (isset($_SESSION['error'])) {
                    echo "<div class='alert alert-danger'>{$_SESSION['error']}</div>";
                    unset($_SESSION['error']); // Clear the error after displaying it
                }
                ?>

                <div class="text-end mb-4">
                    <!-- Search input -->
                    <input type="text" name="search" class="form-control shadow-none w-25 ms-auto" 
                           placeholder="Search..." oninput="search_user(this.value)">
                </div>

                <div class="table-responsive-md">
                    <table class="table table-hover border" id="ordersTable">
                        <thead class="sticky-top">
                            <tr class="bg-dark text-light">
                                <th scope="col">#</th>
                                <th scope="col">Product</th>
                                <th scope="col">Name</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Price</th>
                                <th scope="col">Address</th>
                                <th scope="col">Method</th>
                                <th scope="col">Payment</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody">
                        <?php if (count($orders) > 0): ?>
                            <?php foreach ($orders as $index => $order): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo $order['product_name']; ?></td>
                                    <td><?php echo $order['user_name']; ?></td>
                                    <td><?php echo $order['quantity']; ?></td>
                                    <td><?php echo $order['price']; ?></td>
                                    <td><?php echo $order['address']; ?></td>
                                    <td><?php echo $order['payment_method']; ?></td>
                                    <td><?php echo $order['payment_status']; ?></td>
                                    <td>
                                        <?php if ($order['payment_status'] === 'Paid'): ?>
                                            <button class="btn btn-success btn-sm" disabled>Claimed</button>
                                        <?php else: ?>
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#paymentModal" 
                                                    data-order-id="<?php echo $order['order_id']; ?>" 
                                                    data-price="<?php echo $order['price']; ?>">
                                                Claim
                                            </button>
                                        <?php endif; ?>

                                        <!-- Delete button -->
                                        <form method="post" action="product_sales.php" onsubmit="return confirm('Are you sure you want to delete this order?');" style="display:inline;">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <button type="submit" name="delete_order" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="9" class="text-center">No orders found</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Payment Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Amount to be paid: <span id="paymentPrice"></span></p>
                    <p>Enter received amount:</p>
                    <input type="number" id="receivedAmount" class="form-control" min="0" step="0.01">
                    <p id="changeDisplay" style="display:none;">Change: <span id="changeAmount"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmPaymentButton">Confirm Payment</button>
                </div>
            </div>
        </div>
    </div>

    <?php require('inc/scripts.php'); ?>

</body>
</html>
