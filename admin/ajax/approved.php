<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

if(isset($_POST['get_users'])) 
{
    // Ensure the connection variable is available
    global $con;
    
    // Select only the bookings with status 1 (Approved)
    $query = "SELECT * FROM `bookings` WHERE `status` = 1";
    $res = mysqli_query($con, $query);
    $i = 1;

    $data = "";

    while($row = mysqli_fetch_assoc($res))
    {
        $del_btn = "<button onclick='remove_user($row[id],0)' class='btn btn-danger shadow-none btn-sm'><i class='bi bi-trash'></i></button>";
        $edit_btn = "<button onclick='edit_user($row[id])' class='btn btn-primary shadow-none btn-sm'><i class='bi bi-pencil-square'></i></button>";

        $status = "<button onclick='toggle_status($row[id],1)' class='btn btn-dark btn-sm shadow-none'>Approved</button>";

        if($row['status'] == 3) {
            $status = "<button class='btn btn-success btn-sm shadow-none' disabled>Completed</button>";
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
                <td>$status</td>
            </tr>
        ";
        $i++;
    }

    // Echo the generated HTML back to the AJAX request
    echo $data;
}

if(isset($_POST['toggle_status']))
{
    $frm_data = filteration($_POST);

    // Update the status in the bookings table
    $q = "UPDATE `bookings` SET `status`=? WHERE `id`=?";
    $v = [3, $frm_data['toggle_status']];  // Change status to 3

    if(update($q, $v, 'ii')){
        // Update the user's appointment_status to 'completed' in user_cred table
        $booking_id = $frm_data['toggle_status'];
        // Fetch the user_id associated with this booking
        $userQuery = "SELECT user_id FROM bookings WHERE id=?";
        $userResult = select($userQuery, [$booking_id], 'i');
        $userRow = mysqli_fetch_assoc($userResult);

        if ($userRow) {
            $user_id = $userRow['user_id'];
            // Update the appointment_status in user_cred table
            $updateUserCred = "UPDATE user_cred SET appointment_status = 'completed' WHERE user_id = ?";
            update($updateUserCred, [$user_id], 'i');
        }

        echo 1;
    }
    else{
        echo 0;
    }
}

if(isset($_POST['remove_user'])) {

    $frm_data = filteration($_POST);

    $res = delete("DELETE FROM `bookings` WHERE `id`=?",[$frm_data['id']],'i');

    if($res){
        echo 1;
    }
    else{
        echo 0;
    }
}

if(isset($_POST['search_user']))
{
    $frm_data = filteration($_POST);

    // Select only the bookings with status 1 (Approved) and name matching the search term
    $query = "SELECT * FROM `bookings` WHERE `status` = 1 AND `name` LIKE ?";
    $res = select($query,["%$frm_data[name]%"],'s');
    $i = 1;

    $data = "";

    while($row = mysqli_fetch_assoc($res))
    {
        $del_btn = "<button onclick='remove_user($row[id],0)' class='btn btn-danger shadow-none btn-sm'><i class='bi bi-trash'></i> Delete</button>";
        $edit_btn = "<button onclick='edit_user($row[id])' class='btn btn-primary shadow-none btn-sm'><i class='bi bi-pencil-square'></i></button>";

        $status = "<button onclick='toggle_status($row[id],1)' class='btn btn-dark btn-sm shadow-none'>Approved</button>";

        if($row['status'] == 3) {
            $status = "<button class='btn btn-success btn-sm shadow-none' disabled>Completed</button>";
        }

        $data .= "
            <tr>
                <td>$i</td>
                <td>$row[name]</td>
                <td>$row[email]</td>
                <td>$row[phonenum]</td>
                <td>$row[note]</td>
                <td>$row[date]</td>
                <td>$row[timeslot]</td>
                <td>$status</td>
            </tr>
        ";
        $i++;
    }

    // Echo the generated HTML back to the AJAX request
    echo $data;
}

?>
