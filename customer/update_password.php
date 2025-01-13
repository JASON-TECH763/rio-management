<?php
include('config/connect.php');

if (isset($_POST['email']) && isset($_POST['new_password'])) {
    $email = $_POST['email'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Update the password in the database and clear the reset token
    $query = "UPDATE customer SET password=?, reset_token=NULL, token_expiry=NULL WHERE email=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $new_password, $email);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo "Password has been reset successfully. You may now <a href='login.php'>log in</a> with your new password.";
    } else {
        echo "Failed to update password. Please try again.";
    }
}
?>
