<?php
$update_query = "UPDATE admin_sign_in SET admin_login_time = CURRENT_TIMESTAMP WHERE admin_id = $id";
if (mysqli_query($connect, $update_query)) {
    date_default_timezone_set('Asia/Manila');
    echo "Current Time: " . date("Y-m-d H:i:s");
} else {
    echo "Error: " . mysqli_error($connect);
}
