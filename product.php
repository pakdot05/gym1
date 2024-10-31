<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GYM - Products</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <?php require('inc/links.php'); ?>

    <style>
        /* General Styles */
        body {
            background-color: #f8f9fa;
        }

        .h-line {
            width: 100px;
            height: 5px;
            background-color: #007BFF;
            margin: 10px auto;
        }

        .title {
            font-size: 28px;
            margin-bottom: 20px;
            color: #343a40;
        }

        /* Search Form Styles */
        .search-form {
            display: flex;
            justify-content: center;
            margin-bottom: 40px;
        }

        .search-form input[type="text"] {
            width: 300px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            margin-right: 10px;
            font-size: 16px;
        }

        .search-form button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-form button:hover {
            background-color: #0056b3;
        }

        /* Product Box Styling */
        .box-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .box {
            background-color: white;
            width: calc(25% - 20px);
            margin: 10px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #343a40;
        }

        .quantity-display {
            font-size: 14px;
            margin-bottom: 10px;
            color: #6c757d;
        }

        .price {
            font-size: 16px;
            font-weight: bold;
            color: #28a745;
        }

        .flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .qty {
            width: 60px;
            padding: 5px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            text-align: center;
        }

        /* Empty Products Message */
        .empty {
            text-align: center;
            font-size: 18px;
            color: #888;
            margin-top: 20px;
        }

        /* Checkout Button Style */
        .checkout-button {
            background-color: black;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
            width: 100%;
            text-align: center;
        }

        .checkout-button:hover {
            background-color:#096066;
        }

        /* Responsive Styling */
        @media (max-width: 1200px) {
            .box {
                width: calc(33.333% - 20px);
            }
        }

        @media (max-width: 768px) {
            .box {
                width: calc(50% - 20px);
            }

            .search-form input[type="text"] {
                width: 250px;
            }
        }

        @media (max-width: 576px) {
            .box {
                width: 100%;
                margin: 10px 0;
            }

            .search-form input[type="text"] {
                width: 100%;
                margin-bottom: 10px;
            }

            .search-form {
                flex-direction: column;
            }
        }
    </style>

</head>
<body class="bg-light">

    <!-- HEADER/NAVBAR --> 
    <?php require('inc/header.php'); ?>
    <!-- HEADER/NAVBAR --> 

    <div class="my-5 px-4">
    <h2 class="fw-bold text-center">PRODUCTS</h2>
    <div class="h-line bg-dark"></div>

    <form action="" method="get" class="search-form">
        <input type="text" id="searchInput" name="search" placeholder="Search here..">
        <button type="submit">Search</button>
    </form>

    <!-- Product Display -->
    <div id="productContainer" class="box-container">
        <?php
        if ($settings_r['shutdown']) {
            echo "<p class='text-center text-danger'>The gym is currently shut down. Subscription is not available.</p>";
        } else {
            // User can view products but not checkout if not logged in
            $login = 0; // Variable to track login status
            if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
                $login = 0; // Not logged in
            } else {
                $login = 1; // Logged in
            }

            $search = isset($_GET['search']) ? $_GET['search'] : '';

            // SQL query to fetch products
            $query = "SELECT * FROM `products` WHERE `name` LIKE ? OR `quantity` LIKE ?";

            if ($stmt = $con->prepare($query)) {
                // Bind the search parameter
                $search_param = "%$search%";
                $stmt->bind_param('ss', $search_param, $search_param);

                // Execute the query
                $stmt->execute();

                // Get the result
                $result = $stmt->get_result();

                // Check if there are any matching products
                if ($result->num_rows > 0) {
                    while ($fetch_products = $result->fetch_assoc()) {
                        ?>
                        <form action="checkout.php" method="post" class="box checkout-form">
                            <input type="hidden" name="pid" value="<?=$fetch_products['id'];?>">
                            <input type="hidden" name="name" value="<?=$fetch_products['name'];?>">
                            <input type="hidden" name="price" value="<?=$fetch_products['price'];?>">
                            <input type="hidden" name="image" value="<?=$fetch_products['image'];?>">

                            <!-- Display product image -->
                            <img src="images/<?=$fetch_products['image'];?>" alt="<?=$fetch_products['name'];?>" class="product-image">
                            
                            <!-- Display product name -->
                            <div class="name text-center"><?=$fetch_products['name'];?></div>
                            
                            <!-- Display available quantity -->
                            <div class="quantity-display text-center">Available: <?=$fetch_products['quantity'];?></div>

                            <!-- Display price and quantity input -->
                            <div class="flex">
                                <div class="price"><span style="color: red;">₱</span><?=$fetch_products['price'];?></div>
                                <input type="number" name="qty" class="qty" min="1" max="<?=$fetch_products['quantity'];?>" value="1">
                            </div>
                            
                            <!-- Checkout Button -->
                            <button type="submit" class="checkout-button" data-login="<?=$login;?>">Checkout</button>
                        </form>
                        <?php
                    }
                } else {
                    echo '<p class="empty">No Products Found!</p>';
                }

                // Close the statement
                $stmt->close();
            } else {
                echo "Error: Could not prepare SQL statement.";
            }
        }
        ?>
    </div>
</div>


    <!-- FOOTER -->
    <?php require('inc/footer.php'); ?>
    <!-- FOOTER -->

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        var swiper = new Swiper(".mySwiper", {
            slidesPerView: 4,
            spaceBetween: 40,
            loop: true,
            pagination: {
                el: ".swiper-pagination",
            },
            breakpoints: {
                320: { slidesPerView: 1 },
                640: { slidesPerView: 1 },
                768: { slidesPerView: 3 },
                1024: { slidesPerView: 3 },
            }
        });

        const searchInput = document.getElementById('searchInput');
        const productContainer = document.getElementById('productContainer');
        const emptyMessage = document.querySelector('.empty');

        searchInput.addEventListener('input', searchProducts);

        function searchProducts() {
            const filter = searchInput.value.toUpperCase();
            const products = productContainer.getElementsByClassName('box');

            let productsFound = false;

            for (let i = 0; i < products.length; i++) {
                const name = products[i].getElementsByClassName('name')[0].textContent.toUpperCase();
                const price = products[i].getElementsByClassName('price')[0].textContent.toUpperCase();

                if (name.includes(filter) || price.includes(filter)) {
                    products[i].style.display = '';
                    productsFound = true;
                } else {
                    products[i].style.display = 'none';
                }
            }

            if (productsFound) {
                emptyMessage.style.display = 'none';
            } else {
                emptyMessage.style.display = 'block';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
    const checkoutButtons = document.querySelectorAll('.checkout-button');

    checkoutButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            // Get the login status from the data-login attribute
            const isLoggedIn = this.getAttribute('data-login');

            // If the login status is undefined or 0, show an alert and prevent form submission
            if (isLoggedIn === '0' || isLoggedIn === null) {
                e.preventDefault();  // Prevent the form from submitting
                alert('Please log in first to proceed with checkout.');  // Show the correct alert message
            }
            // If the user is logged in (isLoggedIn is '1'), allow checkout (form submission will continue)
        });
    });
});

    </script>

</body>
</html>
