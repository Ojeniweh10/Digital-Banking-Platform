<?php
session_start();
include('conf/config.php'); //get configuration file

if (isset($_POST['verify_otp'])) {
  $input_otp = $_POST['otp'];
  $current_time = time();
  
  if ($current_time > $_SESSION['otp_expiry']) {
    // OTP expired
    session_destroy();
    header("location:pages_client_index.php");
    exit();
  } elseif ($input_otp == $_SESSION['otp']) {
    // OTP is correct, log the user in
    $_SESSION['authenticated'] = true;
    header("location:pages_dashboard.php");
    exit();
  } else {
    // Invalid OTP
    $err = "Invalid OTP. Please try again.";
    session_destroy();
    header("location:pages_client_index.php");
    exit();
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
      <p>OTP Verification</p>
    </div>
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Enter the OTP sent to your email</p>

        <form method="post">
          <div class="input-group mb-3">
            <input type="text" name="otp" class="form-control" placeholder="OTP" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-key"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
            </div>
            <div class="col-4">
              <button type="submit" name="verify_otp" class="btn btn-success btn-block">Verify OTP</button>
            </div>
          </div>
        </form>
        
        <?php if(isset($err)) { echo "<p class='text-danger'>$err</p>"; } ?>
        
        <p class="mb-0">
          <a href="index.php" class="text-center">Back to Login</a>
        </p>
      </div>
    </div>
  </div>
  <script src="plugins/jquery/jquery.min.js"></script>
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="dist/js/adminlte.min.js"></script>
</body>
</html>
