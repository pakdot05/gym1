<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        :root {
            --blue: #40534C;
            --blue-hover: #D6BD98;
        }

        .nav a {
            display: block;
            padding: 10px;
            margin: 3px;
            text-decoration: none;
            transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out;
            border-radius: 5px;
        }

        .nav a:hover {
            background-color: var(--blue-hover);
            color: white !important;
        }

        .nav a.active {
            background-color: var(--blue-hover);
            color: white !important;
        }
        .logo {
        height: 50px;
        width: 50px;
        border-radius: 50%;
        object-fit: cover;
    }
    </style>
</head>
<body>
<div class="container-fluid ambot text-light p-3 d-flex align-items-center justify-content-between sticky-top">
    <div class="d-flex align-items-center">
        <a href="https://r.search.yahoo.com/_ylt=AwrKFyeq.mxmw14SbaazRwx.;_ylu=Y29sbwNzZzMEcG9zAzEEdnRpZAMEc2VjA3Ny/RV=2/RE=1718446891/RO=10/RU=https%3a%2f%2fweb.facebook.com%2fMinglanillaDentalClinic%2f/RK=2/RS=7yvecSnF0Y43YkmiECpXqcOj7po-">
            <img src="../images/logo.jpg" alt="Logo" class="logo">
        </a>
        <a class="navbar-brand" style=" font-size: 1.2rem; font-weight: bold; text-align: center; color: white;"> Gym Samurai</a>
    </div>
    <a href="logout.php" class="btn btn-light btn-sm">LOGOUT</a>
</div>


<div class="col-lg-2 ambot border-top border-3" id="dashboard-menu">
    <nav class="navbar navbar-expand-lg ambot">
        <div class="container-fluid flex-lg-column align-items-stretch">
            <h4 class="mt-2 text-white">ADMIN PANEL</h4>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminDropdown" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse flex-column align-items-stretch mt-2" id="adminDropdown">
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a id="dashboard-link" class="link" href="dashboard.php">Dashboard</a>
                    </li>
                
                    <li class="nav-item">
                        <a id="patient-link" class="link" href="users.php">Members</a>
                    </li>
                    <li class="nav-item">
                        <a id="appointment-link" class="link" href="appointment.php">Booking</a>
                    </li>
                    <li class="nav-item">
                        <a id="product-link" class="link" href="products.php">Product</a>
                    </li>
                    <li class="nav-item">
                        <a id="patient-link" class="link" href="Product_Sales.php">Product_Sale</a>
                    </li>
                    <li class="nav-item">
                        <a id="trainors-link" class="link" href="trainors.php">Trainor</a>
                    </li>
                    <li class="nav-item">
                        <a id="invoices-link" class="link" href="invoices.php">Invoices</a>
                    </li>
                    <li class="nav-item">
                        <a id="specialty-link" class="link" href="specialty.php">Services</a>
                    </li>
                    <li class="nav-item">
                        <a id="carousel-link" class="link" href="carousel.php">Announcement</a>
                    </li>
                    <li class="nav-item">
                        <a id="settings-link" class="link" href="settings.php">Settings</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get all navigation links
        const navLinks = document.querySelectorAll('.link');

        // Function to set the active link based on the current URL
        function setActiveLink() {
            // Get the current path
            const currentPath = window.location.pathname.split('/').pop();

            // Loop through the links and add active class to the matching link
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }

        // Add click event listener to each link
        navLinks.forEach(link => {
            link.addEventListener('click', function () {
                // Remove active class from all links
                navLinks.forEach(link => link.classList.remove('active'));

                // Add active class to the clicked link
                this.classList.add('active');
            });
        });

        // Set the active link based on the current URL when the page loads
        setActiveLink();
    });
</script>

</body>
</html>
