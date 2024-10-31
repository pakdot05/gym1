<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

if (isset($_POST['action']) && $_POST['action'] === 'get_users') {
    global $con;

    $status = $_POST['status']; // Get the status from the AJAX request
    $query = "SELECT * FROM `bookings` WHERE `status` = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $status);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $i = 1;
    $data = "";

    while ($row = mysqli_fetch_assoc($res)) {
        $del_btn = "<button onclick='remove_user($row[id])' class='btn btn-danger shadow-none btn-sm'><i class='bi bi-trash'></i></button>";
        $edit_btn = "<button onclick='edit_user($row[id])' class='btn btn-primary shadow-none btn-sm'><i class='bi bi-pencil-square'></i></button>";

        // Determine button color and text based on status
        if ($row['status'] == 0) {
            $status_btn = "<button data-id='$row[id]' data-status='1' class='btn btn-danger w-100 btn-sm shadow-none toggle-status'>Pending</button>";
        } elseif ($row['status'] == 1) {
            $status_btn = "<button data-id='$row[id]' data-status='3' class='btn btn-primary w-100 btn-sm shadow-none toggle-status'>Approved</button>";
        } elseif ($row['status'] == 3) {
            $status_btn = "<button class='btn btn-success w-100 btn-sm shadow-none' disabled>Completed</button>";
        }

        // Append rows with booking details
        $data .= "
            <tr>
                <td>$i</td>
                <td>$row[name]</td>
                <td>$row[email]</td>
                <td>$row[phonenum]</td>
                <td>$row[note]</td>
                <td>$row[trainor_name]</td>
                <td>$row[date]</td>
                <td>$row[timeslot]</td>
                <td>$status_btn</td>
                <td>$del_btn</td>
            </tr>
        ";
        $i++;
    }

    echo $data; // Return the table rows to the frontend
}
if (isset($_POST['toggle_status'])) {
    $frm_data = filteration($_POST);
    $nextStatus = $frm_data['status'] == 1 ? 3 : 1;
    $q = "UPDATE `bookings` SET `status`=? WHERE `id`=?";
    $v = [$nextStatus, $frm_data['id']];

    if (update($q, $v, 'ii')) {
        if ($nextStatus == 3) {
            $booking_id = $frm_data['id'];
            $userQuery = "SELECT user_id FROM bookings WHERE id=?";
            $userResult = select($userQuery, [$booking_id], 'i');
            $userRow = mysqli_fetch_assoc($userResult);

            if ($userRow) {
                $user_id = $userRow['user_id'];
                $updateUserCred = "UPDATE user_cred SET appointment_status = 'completed' WHERE user_id = ?";
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
    $status = $_POST['status']; // Get the status from the AJAX request

    $query = "SELECT * FROM `bookings` WHERE `status` = ? AND `name` LIKE ?";
    $res = select($query, [$status, "%$frm_data[name]%"], 'is');
    $i = 1;

    $data = "";

    while ($row = mysqli_fetch_assoc($res)) {
        $del_btn = "<button onclick='remove_user($row[id])' class='btn btn-danger shadow-none btn-sm'><i class='bi bi-trash'></i></button>";
        $edit_btn = "<button onclick='edit_user($row[id])' class='btn btn-primary shadow-none btn-sm'><i class='bi bi-pencil-square'></i></button>";

        // Determine button color and text based on status
        if ($row['status'] == 0) {
            $status_btn = "<button data-id='$row[id]' data-status='1' class='btn btn-danger w-100 btn-sm shadow-none toggle-status'>Pending</button>";
        } elseif ($row['status'] == 1) {
            $status_btn = "<button data-id='$row[id]' data-status='3' class='btn btn-primary w-100 btn-sm shadow-none toggle-status'>Approved</button>";
        } elseif ($row['status'] == 3) {
            $status_btn = "<button class='btn btn-success w-100 btn-sm shadow-none' disabled>Completed</button>";
        }

        $data .= "
            <tr>
                <td>$i</td>
                <td>$row[name]</td>
                <td>$row[email]</td>
                <td>$row[phonenum]</td>
                <td>$row[note]</td>
                <td>$row[trainor_name]</td>
                <td>$row[date]</td>
                <td>$row[timeslot]</td>
                <td>$status_btn</td>
                <td>$del_btn</td>
            </tr>
        ";
        $i++;
    }

    echo $data;
}

?>