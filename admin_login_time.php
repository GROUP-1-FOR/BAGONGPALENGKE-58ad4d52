<?php
$update_query = "UPDATE admin_sign_in SET admin_login_time = CURRENT_TIMESTAMP WHERE admin_id = $admin_id";
if (mysqli_query($connect, $update_query)) {
    date_default_timezone_set('Asia/Manila');
} else {
    echo "Error: " . mysqli_error($connect);
}
