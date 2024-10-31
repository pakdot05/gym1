<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

if (isset($_POST['get_users'])) {
    $res = selectAll('bookings');
    $i = 1;

    $data = "";

    while ($row = mysqli_fetch_assoc($res)) {
        if ($row['status'] == 0) {
            $del_btn = "<button onclick='remove_user($row[id],0)' class='btn btn-danger w-100 shadow-none btn-sm'><i>Cancel</i></button>";
            $edit_btn = "<button onclick='remove_user($row[id],0)' class='btn btn-primary shadow-none btn-sm'><i class='bi bi-pencil-square'></i></button>";

            $status = "<button onclick='toggle_status($row[id],0)' class='btn btn-dark w-100 btn-sm shadow-none'>Approved</button>";
            
            if (!$row['status']) {
                $status = "<button onclick='toggle_status($row[id],1)' class='btn btn-secondary w-100 btn-sm shadow-none'>Pending</button>";
            }

            $data .= "
                <tr onclick='show_user_details($row[id])' style='cursor:pointer;'>
                    <td>$i</td>
                    <td>$row[name]</td>
                    <td>$row[email]</td>
                    <td>$row[phonenum]</td>
                    <td>$row[note]</td>
                    <td>$row[trainor_name]</td>
                    <td>$row[date]</td>
                    <td>$row[timeslot]</td>
                    <td>$status</td>
                    <td>$del_btn</td>
                </tr>
            ";
            $i++;
        }
    }

    // Echo the generated HTML back to the AJAX request
    echo $data;
}

if (isset($_POST['toggle_status'])) {
    $frm_data = filteration($_POST);

    // Update the status in the bookings table
    $q = "UPDATE `bookings` SET `status`=? WHERE `id`=?";
    $v = [$frm_data['value'], $frm_data['toggle_status']];

    if (update($q, $v, 'ii')) {
        // If the status is changed to approved, update the user's appointment_status in user_cred table
        if ($frm_data['value'] == 1) {
            $booking_id = $frm_data['toggle_status'];
            // Fetch the user_id associated with this booking
            $userQuery = "SELECT user_id FROM bookings WHERE id=?";
            $userResult = select($userQuery, [$booking_id], 'i');
            $userRow = mysqli_fetch_assoc($userResult);

            if ($userRow) {
                $user_id = $userRow['user_id'];
                // Update the appointment_status in user_cred table
                $updateUserCred = "UPDATE user_cred SET appointment_status = 'approved' WHERE user_id = ?";
                update($updateUserCred, [$user_id], 'i');
            }
        }

        echo 1;
    } else {
        echo 0;
    }
}

if (isset($_POST['remove_user'])) {
    $frm_data = filteration($_POST);

    // Update the user_cred's appointment_status to 'cancelled'
    $userQuery = "SELECT user_id FROM bookings WHERE id=?";
    $userResult = select($userQuery, [$frm_data['id']], 'i');
    $userRow = mysqli_fetch_assoc($userResult);

    if ($userRow) {
        $user_id = $userRow['user_id'];
        $updateUserCred = "UPDATE user_cred SET appointment_status = 'cancelled' WHERE user_id = ?";
        update($updateUserCred, [$user_id], 'i');
    }

    $res = delete("DELETE FROM `bookings` WHERE `id`=?", [$frm_data['id']], 'i');

    if ($res) {
        echo 1;
    } else {
        echo 0;
    }
}

if (isset($_POST['search_user'])) {
    $frm_data = filteration($_POST);

    $query = "SELECT * FROM `bookings` WHERE `name` LIKE ?";

    $res = select($query, ["%$frm_data[name]%"], 's');
    $i = 1;

    $data = "";

    while ($row = mysqli_fetch_assoc($res)) {
        if ($row['status'] == 0) {
            $del_btn = "<button onclick='remove_user($row[id],0)' class='btn btn-danger shadow-none btn-sm'><i>Cancel</i></button>";
            $edit_btn = "<button onclick='remove_user($row[id],0)' class='btn btn-primary shadow-none btn-sm'><i class='bi bi-pencil-square'></i></button>";

            $status = "<button onclick='toggle_status($row[id],0)' class='btn btn-dark btn-sm shadow-none'>Approved</button>";
            
            if (!$row['status']) {
                $status = "<button onclick='toggle_status($row[id],1)' class='btn btn-danger btn-sm shadow-none'>Pending</button>";
            }

            $data .= "
                <tr onclick='show_user_details($row[id])' style='cursor:pointer;'>
                    <td>$i</td>
                    <td>$row[name]</td>
                    <td>$row[email]</td>
                    <td>$row[phonenum]</td>
                    <td>$row[note]</td>
                    <td>$row[date]</td>
                    <td>$row[timeslot]</td>
                    <td>$status</td>
                    <td>$del_btn</td>
                </tr>
            ";
            $i++;
        }
    }
}

?>