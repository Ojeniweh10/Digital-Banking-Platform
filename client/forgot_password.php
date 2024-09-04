<?php
session_start();
include('conf/config.php');
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['reset_password'])) {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $mysqli->prepare("SELECT client_id FROM ib_clients WHERE email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($client_id);
    $rs = $stmt->fetch();

    if ($rs) {
        // Fully process the result set
        $stmt->close(); // Close the previous statement

        // Generate a new password
        $new_password = bin2hex(random_bytes(4)); // 8 characters password
        $hashed_password = sha1(md5($new_password)); // Double hash for security

        // Update the password in the database
        $update_stmt = $mysqli->prepare("UPDATE ib_clients SET password=? WHERE client_id=?");
        $update_stmt->bind_param('si', $hashed_password, $client_id);
        $update_stmt->execute();
        $update_stmt->close(); // Close the update statement

        // Send the new password via email
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ojeniwehalexander@gmail.com';
            $mail->Password   = 'necc xsuy ntil kisw';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->setFrom('ojeniwehalexander@gmail.com', 'Byte Bank');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Your New Password';
            $mail->Body    = "Your new password is $new_password. Please change it after logging in.";

            $mail->send();
            echo "<script>alert('A new password has been sent to your email address.');</script>";
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
        }

    } else {
        echo "<script>alert('Email not found in our records.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<?php include("dist/_partials/head.php"); ?>
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <p>Forgot Password</p>
    </div>
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Enter your email to reset your password</p>

        <form method="post">
          <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <button type="submit" name="reset_password" class="btn btn-success btn-block">Reset Password</button>
            </div>
          </div>
        </form>

        <p class="mb-0">
          <a href="pages_client_index.php" class="text-center">Back to Login</a>
        </p>
      </div>
    </div>
  </div>
  <script src="plugins/jquery/jquery.min.js"></script>
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="dist/js/adminlte.min.js"></script>
</body>
</html>
