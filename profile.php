<?php
require('inc/links.php');

// Check if user is logged in and session variables are set 
if (!isset($_SESSION['uId'])) {
    die("User not logged in");
}  

// Fetch user profile data from the database
$uId = $_SESSION['uId'];
$query = "SELECT * FROM user_cred WHERE user_id = ?";
$result = select($query, [$uId], 'i');

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['uName'] = $user['name'];
    $_SESSION['uEmail'] = $user['email'];
    $_SESSION['uPhone'] = $user['phonenum'];
    $_SESSION['uDob'] = $user['dob'];
    $_SESSION['uAdd'] = $user['address'];
    $_SESSION['uProfile'] = $user['profile'];
} else {
    die("User not found");
}

// Handle form submission for profile data update
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $phonenum = $_POST['phonenum'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];

    // Update user data in the database excluding email
    $updateQuery = "UPDATE user_cred SET name=?, phonenum=?, dob=?, address=? WHERE user_id=?";
    $updateResult = update($updateQuery, [$name, $phonenum, $dob, $address, $uId], 'ssssi');

    if ($updateResult) {
        // Update session variables with new data
        $_SESSION['uName'] = $name;
        $_SESSION['uPhone'] = $phonenum;
        $_SESSION['uDob'] = $dob;
        $_SESSION['uAdd'] = $address;

        echo "<script>alert('Profile updated successfully');</script>";
        // Optionally, redirect the user to another page or refresh this page
    } else {
        echo "<script>alert('Failed to update profile');</script>";
    }
}

// Handle profile picture update
if (isset($_POST['submitPhoto'])) {
    $profileImage = $_FILES['profileImage'];

    // Validate and handle profile image upload
    if ($profileImage['error'] === UPLOAD_ERR_OK) {
        // Validate file type, size, etc. as per your requirements

        // Move uploaded file to a permanent location
        $uploadDir = 'images/users/';
        $uploadFile = $uploadDir . basename($profileImage['name']);

        if (move_uploaded_file($profileImage['tmp_name'], $uploadFile)) {
            // Update profile image path in the database
            $updateQuery = "UPDATE user_cred SET profile=? WHERE user_id=?";
            $newProfileFileName = basename($profileImage['name']);
            $updateResult = update($updateQuery, [$newProfileFileName, $uId], 'si');

            if ($updateResult) {
                $_SESSION['uProfile'] = $newProfileFileName;
                echo "<script>alert('Profile picture updated successfully');</script>";
                // Optionally, redirect or refresh the page
            } else {
                echo "<script>alert('Failed to update profile picture');</script>";
            }
        } else {
            echo "<script>alert('Failed to move uploaded file');</script>";
        }
    } else {
        echo "<script>alert('Error uploading profile picture');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GYM - Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

    <!-- LINKS -->
    <style>
        .box {
            border-top-color: var(--blue) !important;
        }

        .btn{
            background-color:  #323232 !important;
            border: 1px solid  #323232 !important;
        }

        .btn:hover{
            background-color: #096066 !important;
        }
    </style>
</head>
<body class="bg-light">

<!-- HEADER/NAVBAR -->
<?php require('inc/header.php') ?>
<!-- HEADER/NAVBAR -->

<div class="my-5 px-4">
    <h2 class="fw-bold text-center">PROFILE</h2>
    <div class="h-line bg-dark"></div>
</div>

<div class="container" style="height: 100vh;">
<form method="post" action="" enctype="multipart/form-data">
    <div class="photo bg-dark" style="height: 300px; width: 300px; border-radius: 50%; margin: 0 auto;">
        <?php if (!empty($_SESSION['uProfile'])): ?>
            <?php
            $profilePath = SITE_URL . 'images/users/' . $_SESSION['uProfile'];
            echo "<img src='$profilePath' alt='Profile Image' style='height: 100%; width: 100%; object-fit: cover; border-radius: 50%;'>";
            ?>
        <?php else: ?>
            <p style='color: white; text-align: center; line-height: 300px;'>No profile image set</p>
        <?php endif; ?>
    </div>
    <div class="form-group mt-3">
        <label for="profileImage">Change Profile Picture</label>
        <input type="file" class="form-control" id="profileImage" name="profileImage">
    </div>
    <div class="form-group text-center">
        <button type="submit" class="btn custom-bg text-white" name="submitPhoto">Change Photo</button>
    </div>
</form>

<form method="post" action="">
    <div class="form-group mb-3 mt-5">
        <label for="">Name</label>
        <input required type="text" name="name" id="username" class="form-control" value="<?php echo isset($_SESSION['uName']) ? $_SESSION['uName'] : ''; ?>">
    </div>
    <div class="form-group mb-3">
        <label for="">Email</label>
        <input required type="email" name="email" class="form-control" disabled value="<?php echo isset($_SESSION['uEmail']) ? $_SESSION['uEmail'] : ''; ?>">
    </div>
    <div class="form-group mb-3">
        <label for="">Phone no.</label>
        <input required type="tel" name="phonenum" class="form-control" value="<?php echo isset($_SESSION['uPhone']) ? $_SESSION['uPhone'] : ''; ?>">
    </div>
    <div class="form-group mb-3">
        <label for="dob">Date of Birth</label>
        <input required type="date" name="dob" id="dob" class="form-control" value="<?php echo isset($_SESSION['uDob']) ? $_SESSION['uDob'] : ''; ?>">
    </div>
    <div class="form-group mb-3">
        <label for="address">Address</label>
        <input required type="text" name="address" id="address" class="form-control" value="<?php echo isset($_SESSION['uAdd']) ? $_SESSION['uAdd'] : ''; ?>">
    </div>
    <div class="form-group text-end">
        <button type="submit" class="btn custom-bg text-white" name="submit">Save</button>
    </div>
</form>
<div class="qr-con text-center" style="display: none;">
                            <input type="hidden" class="form-control" id="generatedCode" name="generated_code">
                            <p>Take a pic with your qr code.</p>
                            <img class="mb-4" src="" id="qrImg" alt="">
                        </div>


    
</div>

<br><br><br>
<br><br><br>
<br><br><br>
<br><br><br>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

    <!-- Data Table -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
<script>

    document.addEventListener('DOMContentLoaded', function () {
        const navLinks = document.querySelectorAll('.nav-link');
        $(document).ready( function () {
            $('#studentTable').DataTable();
        });

 
        function generateRandomCode(length) {
            const characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            let randomString = '';

            for (let i = 0; i < length; i++) {
                const randomIndex = Math.floor(Math.random() * characters.length);
                randomString += characters.charAt(randomIndex);
            }

            return randomString;
        }

        function generateQrCode() {
            const qrImg = document.getElementById('qrImg');

            let text = generateRandomCode(10);
            $("#generatedCode").val(text);

            if (text === "") {
                alert("Please enter text to generate a QR code.");
                return;
            } else {
                const apiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(text)}`;

                qrImg.src = apiUrl;
                document.getElementById('name').style.pointerEvents = 'none';
                document.getElementById('address').style.pointerEvents = 'none';
                document.querySelector('.modal-close').style.display = '';
                document.querySelector('.qr-con').style.display = '';
                document.querySelector('.qr-generator').style.display = 'none';
            }
        }

        function setActiveLink() {
            const currentPath = window.location.pathname.split('/').pop();

            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active-nav-link');
                } else {
                    link.classList.remove('active-nav-link');
                }
            });
        }


        navLinks.forEach(link => {
            link.addEventListener('click', function () {
                navLinks.forEach(link => link.classList.remove('active-nav-link'));
                this.classList.add('active-nav-link');
            });
        });

        setActiveLink();
    });

<!-- FOOTER -->
<?php require('inc/footer.php') ?>
<!-- FOOTER -->

</body>
</html>