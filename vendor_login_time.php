<?php
$update_query = "UPDATE vendor_sign_in SET vendor_login_time = CURRENT_TIMESTAMP WHERE vendor_id = $vendor_id";
if (mysqli_query($connect, $update_query)) {
    date_default_timezone_set('Asia/Manila');
} else {
    echo "Error: " . mysqli_error($connect);
}
