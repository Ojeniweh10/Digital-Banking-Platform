<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('conf/config.php'); // get configuration file
require '../vendor/autoload.php'; // Load Composer’s autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = sha1(md5($_POST['password'])); // double encrypt to increase security
    $stmt = $mysqli->prepare("SELECT email, password, client_id FROM iB_clients WHERE email=? AND password=?"); // SQL to log in user
    $stmt->bind_param('ss', $email, $password); // bind fetched parameters
    $stmt->execute(); // execute bind
    $stmt->bind_result($email, $password, $client_id); // bind result
    $rs = $stmt->fetch();

    if ($rs) { // if it's successful
        // Generate OTP
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiry'] = time() + 300; // 5 minutes expiry time
        $_SESSION['client_id'] = $client_id;
        $_SESSION['email'] = $email;

        // Send OTP via PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = 2;                                       // Enable verbose debug output
            $mail->Debugoutput = 'html';                                // Output format
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'ojeniwehalexander@gmail.com';          // SMTP username (your email)
            $mail->Password   = 'necc xsuy ntil kisw';                  // App-specific password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Enable SSL encryption
            $mail->Port       = 465;                                    // TCP port to connect to

            // Recipients
            $mail->setFrom('ojeniwehalexander@gmail.com', 'Byte Bank');
            $mail->addAddress($email);     // Add a recipient

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Your OTP Code to login ';
            $mail->Body    = "Your OTP code is $otp , do not share to anyone , if you didn't attempt to log in please ignore. it iwll expire in 5 minutes";

            $mail->send();
            echo "<script>alert('OTP has been sent to your email address');</script>";
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
        }

        // Redirect to OTP verification page
        echo "<script>window.location.href='otp_verification.php';</script>";
        exit();
    } else {
        $err = "Access Denied Please Check Your Credentials";
    }
}

// Persist System Settings On Brand
$ret = "SELECT * FROM `iB_SystemSettings` ";
$stmt = $mysqli->prepare($ret);
$stmt->execute(); // ok
$res = $stmt->get_result();
while ($auth = $res->fetch_object()) {
?>
<!DOCTYPE html>
<html>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<?php include("dist/_partials/head.php"); ?>
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <p><?php echo $auth->sys_name; ?></p>
    </div>
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Log In To Start Client Session</p>

        <form method="post">
          <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="remember">
                <label for="remember">
                  Remember Me
                </label>
              </div>
            </div>
            <div class="col-4">
              <button type="submit" name="login" class="btn btn-success btn-block">Log In</button>
            </div>
          </div>
        </form>

        <p class="mb-0">
          <a href="pages_client_signup.php" class="text-center">Register a new account</a>
        </p>
        <p class="mb-1">
          <a href="forgot_password.php">I forgot my password</a>
        </p>
      </div>
    </div>
  </div>
  <script src="plugins/jquery/jquery.min.js"></script>
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="dist/js/adminlte.min.js"></script>
</body>
</html>
<?php
}
?>
