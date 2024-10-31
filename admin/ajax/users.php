<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

if (isset($_POST['get_users'])) {
    $res = selectAll('user_cred');
    $i = 1;
    $data = "";

    while ($row = mysqli_fetch_assoc($res)) {
        $del_btn = "<button onclick='remove_user($row[user_id],0)' class='btn btn-danger shadow-none btn-sm'><i class='bi bi-trash'></i> Delete</button>";
        $status = "<button onclick='toggle_status($row[user_id],0)' class='btn btn-success btn-sm shadow-none'>active</button>";
        if (!$row['status']) {
            $status = "<button onclick='toggle_status($row[user_id],1)' class='btn btn-danger btn-sm shadow-none'>inactive</button>";
        }
        $date = date("d-m-Y", strtotime($row['datentime']));
        $data .= "
            <tr data-user-id='$row[user_id]'>
                <td>$i</td>
                <td>$row[name]</td>
                <td>$row[email]</td>
                <td>$row[phonenum]</td>
                <td>$row[address]</td>
                <td>$row[dob]</td>
                <td>$status</td>
                <td>$date</td>
            </tr>
        ";
        $i++;
    }

    echo $data;
}

if (isset($_POST['fetch_user_details'])) {
    $user_id = filter_var($_POST['fetch_user_details'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch user details
    $query_user = "SELECT * FROM `user_cred` WHERE `user_id` = ?";
    $res_user = select($query_user, [$user_id], 'i');
    $user_details = mysqli_fetch_assoc($res_user);

    // Fetch user bookings
    $query_bookings = "SELECT * FROM `bookings` WHERE `user_id` = ?";
    $res_bookings = select($query_bookings, [$user_id], 'i');

    $bookings_data = "";
    $i = 1;
    while ($booking = mysqli_fetch_assoc($res_bookings)) {
        $date = date("d-m-Y", strtotime($booking['date']));
        $time = date("H:i:s", strtotime($booking['timeslot']));
        $names = $booking['name'];
        $bookings_data .= "
            <tr>
                <td>{$i}</td>
                <td>{$names}</td>
                <td>{$user_details['email']}</td>
                <td>{$user_details['phonenum']}</td>
                <td>{$booking['note']}</td>
                <td>{$date}</td>
                <td>{$time}</td>
            </tr>
        ";
        $i++;
    }

    // Ensure that the image path is correct
    $user_profile_image = "../images/users/{$user_details['profile']}"; // Adjust path as per your directory structure

    $del_btn = "<button onclick='remove_user($user_details[user_id],0)' class='btn btn-danger shadow-none btn-sm'><i class='bi bi-trash'></i> Delete</button>";
    $status = "<button onclick='toggle_status($user_details[user_id],0)' class='btn btn-success btn-sm shadow-none'>" . ($user_details['status'] ? 'active' : 'inactive') . "</button>";
    if (!$user_details['status']) {
        $status = "<button onclick='toggle_status($user_details[user_id],1)' class='btn btn-danger btn-sm shadow-none'>inactive</button>";
    }

    echo "
        <div class='photo bg-dark' style='height: 200px; width: 200px; border-radius: 50%; margin: 0 auto;'>
            <img src='$user_profile_image' alt='Profile Image' style='height: 100%; width: 100%; object-fit: cover; border-radius: 50%;'>
        </div>
        <br><br>
        <div>
            <h5>Name: {$user_details['name']}</h5>
            <h5>Email: {$user_details['email']}</h5>
            <h5>Phone#: {$user_details['phonenum']}</h5>
            <h5>Address: {$user_details['address']}</h5>
            <h5>Bdate: {$user_details['dob']}</h5>
            <h5>Registration Date: " . date("d-m-Y", strtotime($user_details['datentime'])) . "</h5>
            
        </div>
        <hr>
        
        <h4>BOOKINGS</h4>
        <div class='table-responsive-md' style='height: 300px; overflow-y: scroll;'>
            <table class='table table-hover border'>
                <thead class='sticky-top'>
                    <tr class='text-light ambot'>
                        <th scope='col'>#</th>
                        <th scope='col'>Name</th>
                        <th scope='col'>Email</th>
                        <th scope='col'>Phone no.</th>
                        <th scope='col' width='20%'>Note</th>
                        <th scope='col'>Date</th>
                        <th scope='col'>Time</th>
                    </tr>
                </thead>
                <tbody id='users-data'>
                    $bookings_data
                </tbody>
            </table>
        </div>
        <br>
        <div>$del_btn</div>
    ";
}

if (isset($_POST['toggle_status'])) {
    $frm_data = filteration($_POST);
    $q = "UPDATE `user_cred` SET `status`=? WHERE `user_id`=?";
    $v = [$frm_data['value'], $frm_data['toggle_status']];
    if (update($q, $v, 'ii')) {
        echo 1;
    } else {
        echo 0;
    }
}

if (isset($_POST['remove_user'])) {
    $frm_data = filteration($_POST);
    $res = delete("DELETE FROM `user_cred` WHERE `user_id`=?", [$frm_data['user_id']], 'i');
    if ($res) {
        echo 1;
    } else {
        echo 0;
    }
}

if (isset($_POST['search_user'])) {
    $frm_data = filteration($_POST);
    $query = "SELECT * FROM `user_cred` WHERE `name` LIKE ?";
    $res = select($query, ["%$frm_data[name]%"], 's');
    $i = 1;
    $data = "";

    while ($row = mysqli_fetch_assoc($res)) {
        $del_btn = "<button onclick='remove_user($row[user_id],0)' class='btn btn-danger shadow-none btn-sm'><i class='bi bi-trash'></i> Delete</button>";
        $status = "<button onclick='toggle_status($row[user_id],0)' class='btn btn-dark btn-sm shadow-none'>active</button>";
        if (!$row['status']) {
            $status = "<button onclick='toggle_status($row[user_id],1)' class='btn btn-danger btn-sm shadow-none'>inactive</button>";
        }
        $date = date("d-m-Y", strtotime($row['datentime']));
        $data .= "
            <tr data-user-id='$row[user_id]'>
                <td>$i</td>
                <td>$row[name]</td>
                <td>$row[email]</td>
                <td>$row[phonenum]</td>
                <td>$row[address]</td>
                <td>$row[dob]</td>
                <td>$status</td>
                <td>$date</td>
            </tr>
        ";
        $i++;
    }

    echo $data;
}
?>
