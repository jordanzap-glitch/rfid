<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'includes/dbcon.php';

// Variable to hold error messages
$error_message = "";

if (isset($_POST['login'])) {
    // Get the submitted username and password
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check in Super Admin table
    $query_superadmin = "SELECT * FROM tbl_superadmin WHERE username = '$username' AND password = '$password'";
    $rs_superadmin = $conn->query($query_superadmin);
    $num_superadmin = $rs_superadmin->num_rows;

    if ($num_superadmin > 0) {
        $rows_superadmin = $rs_superadmin->fetch_assoc();
        $_SESSION['userId'] = $rows_superadmin['id'];
        $_SESSION['fullname'] = $rows_superadmin['fullname'];
        $_SESSION['user_type'] = 'superadmin';

        header('Location:SuperAdmin/index.php');
        exit();
    } else {
        // Check in Admin table
        $query_admin = "SELECT * FROM tbl_admin WHERE (email = '$username' OR username = '$username') AND password = '$password'";
        $rs_admin = $conn->query($query_admin);
        $num_admin = $rs_admin->num_rows;

        if ($num_admin > 0) {
            $rows_admin = $rs_admin->fetch_assoc();
            $_SESSION['userId'] = $rows_admin['id'];
            $_SESSION['firstname'] = $rows_admin['firstname'];
            $_SESSION['lastname'] = $rows_admin['lastname'];
            $_SESSION['email'] = $rows_admin['email'];
            $_SESSION['user_type'] = 'admin';

            header('Location:Admin/index.php');
            exit();
        } else {
            // Check in Customer table
            $query_customer = "SELECT * FROM tbl_customer WHERE (email = '$username' OR username = '$username') AND password = '$password'";
            $rs_customer = $conn->query($query_customer);
            $num_customer = $rs_customer->num_rows;

            if ($num_customer > 0) {
                $rows_customer = $rs_customer->fetch_assoc();
                $_SESSION['userId'] = $rows_customer['id'];
                $_SESSION['firstname'] = $rows_customer['firstname'];
                $_SESSION['lastname'] = $rows_customer['lastname'];
                $_SESSION['email'] = $rows_customer['email'];
                $_SESSION['user_type'] = 'customer';

                header('Location:Customer/index.php');
                exit();
            } else {
                // Invalid login
                $error_message = "Invalid Username/Password!";
            }
        }
    }
}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Log-In</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="vendors/feather/feather.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <!-- inject:css -->
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="img/srclogo.png" />
</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo d-flex justify-content-center mb-4">
                <img src="img/srclogo.png" alt="logo">
              </div>
              <h6 class="font-weight-light text-center">Sign in to continue.</h6>

              <!-- Display error message -->
              <?php if (!empty($error_message)) { ?>
                <div class="alert alert-danger text-center">
                  <?php echo $error_message; ?>
                </div>
              <?php } ?>

              <form class="pt-3" method="POST" action="">
                <div class="form-group">
                  <input type="text" class="form-control form-control-lg" name="username" placeholder="Username or Email" required>
                </div>
                <div class="form-group">
                  <input type="password" class="form-control form-control-lg" name="password" placeholder="Password" required>
                </div>
                <div class="mt-3">
                  <button type="submit" name="login" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">SIGN IN</button>
                </div>
                <div class="my-2 d-flex justify-content-between align-items-center">
                  <div class="form-check">
                    <label class="form-check-label text-muted">
                      <input type="checkbox" class="form-check-input">
                      Keep me signed in
                    </label>
                  </div>
                  <a href="#" class="auth-link text-black">Forgot password?</a>
                </div>
                <div class="text-center mt-4 font-weight-light">
                  Don't have an account? <a href="register.html" class="text-primary">Create</a>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src=".vendors/js/vendor.bundle.base.js"></script>
  <!-- inject:js -->
  <script src="js/off-canvas.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/settings.js"></script>
  <script src="js/todolist.js"></script>
  <!-- endinject -->
</body>

</html>
