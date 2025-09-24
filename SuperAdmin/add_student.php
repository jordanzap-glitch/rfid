<?php
// Include database connection
include '../includes/session.php';
include '../includes/dbcon.php';

$statusMsg = ""; // For status messages

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid       = mysqli_real_escape_string($conn, $_POST['uid']);
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname  = mysqli_real_escape_string($conn, $_POST['lastname']);
    $year      = isset($_POST['year']) ? mysqli_real_escape_string($conn, $_POST['year']) : "";
    $section   = isset($_POST['section']) ? mysqli_real_escape_string($conn, $_POST['section']) : "";
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $address   = mysqli_real_escape_string($conn, $_POST['address']);
    $regType   = mysqli_real_escape_string($conn, $_POST['registration_type']);
    $date_created = date("Y-m-d H:i:s");

    $imagePath = "";
    if (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] == 0) {
        $targetDir = "../uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = array("jpg","jpeg","png","gif");
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFilePath)) {
                $imagePath = $targetFilePath;
            }
        }
    }

    if ($regType === "Student") {
        $sql = "INSERT INTO tbl_students 
                (uid, firstname, lastname, year, section, email, address, image_path, eligible_status, date_created, user_type) 
                VALUES 
                ('$uid', '$firstname', '$lastname', '$year', '$section', '$email', '$address', '$imagePath', 1, '$date_created', 'Student')";
    } elseif ($regType === "Teaching") {
        $sql = "INSERT INTO tbl_regulars 
                (uid, firstname, lastname, email, address, image_path, eligible_status, user_type, date_created) 
                VALUES 
                ('$uid', '$firstname', '$lastname', '$email', '$address', '$imagePath', 1, 'Teacher', '$date_created')";
    } elseif ($regType === "Nonteaching") {
        $sql = "INSERT INTO tbl_regulars 
                (uid, firstname, lastname, email, address, image_path, eligible_status, user_type, date_created) 
                VALUES 
                ('$uid', '$firstname', '$lastname', '$email', '$address', '$imagePath', 1, 'Non Teaching', '$date_created')";
    }

    if (mysqli_query($conn, $sql)) {
        $updateRFID = "UPDATE tbl_rfid_auth SET inuse = 1 WHERE uid = '$uid'";
        mysqli_query($conn, $updateRFID);
        header("Location: ".$_SERVER['PHP_SELF']."?status=success");
        exit();
    } else {
        $error = urlencode(mysqli_error($conn));
        header("Location: ".$_SERVER['PHP_SELF']."?status=error&msg=$error");
        exit();
    }
}

if (isset($_GET['status'])) {
    if ($_GET['status'] == "success") {
        $statusMsg = '<div class="alert alert-success">✅ Registered successfully!</div>';
    } elseif ($_GET['status'] == "error" && isset($_GET['msg'])) {
        $statusMsg = '<div class="alert alert-danger">❌ Error: '.htmlspecialchars($_GET['msg']).'</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "partials/head.php";?>
  <style>
  #loading-spinner {
    display: none;
    text-align: center;
    margin-top: 15px;
  }
  .spinner-border {
    width: 2rem;
    height: 2rem;
    border: 0.25em solid #ccc;
    border-top: 0.25em solid #007bff;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin-bottom: 10px;
  }
  @keyframes spin {
    100% { transform: rotate(360deg); }
  }
  .card-header-custom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
  }
  .dropdown-select {
    width: 200px;
  }
  </style>
</head>
<body>
  <div class="container-scroller">
    <?php include "partials/navbar.php";?>
    <div class="container-fluid page-body-wrapper">
      <?php include "partials/settings-panel.php";?>
      <?php include "partials/sidebar.php";?>

      <div class="main-panel">
        <div class="content-wrapper">

          <?php if ($statusMsg) echo $statusMsg; ?>

          <form class="forms-sample w-100" method="POST" action="" enctype="multipart/form-data">
            <div class="row">

              <div class="col-md-4 grid-margin stretch-card">
                <div class="card" id="rfid-card">
                  <div class="card-body text-center">
                    <h4 class="card-title">Scan RFID Card</h4>
                    <p class="card-description">Tap an RFID card to generate UID</p>
                    <div id="rfid-animation" class="mt-3">
                      <div id="rfid-circle" class="rfid-circle green"></div>
                      <div class="rfid-check">✔</div>
                      <p id="rfid-status" class="text-success font-weight-bold mt-2">Card Scanned!</p>
                    </div>
                    <?php include 'partials/spinner.php'; ?>
                    <div class="form-group">
                      <label for="uid">UID</label>
                      <p id="uid-display" class="form-control text-center font-weight-bold" 
                         style="background:#f8f9fa;">Waiting for scan...</p>
                      <input type="hidden" id="uid" name="uid" required>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="card-header-custom">
                      <h4 class="card-title">Registration Form</h4>
                      <select class="form-control dropdown-select" id="registration-type" name="registration_type" required>
                          <option value="" disabled selected>Choose first</option>
                          <option value="Student">Student</option>
                          <option value="Teaching">Teaching</option>
                          <option value="Nonteaching">Nonteaching</option>
                        </select>

                    </div>
                    <p class="card-description">Fill out the form to register</p>

                    <div class="form-group">
                        <label for="firstname">First Name</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First Name" required disabled>
                    </div>
                    <div class="form-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last Name" required disabled>
                    </div>

                    <div class="row" id="year-section-group">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="year">Grade/Year</label>
                          <input type="text" class="form-control" id="year" name="year" placeholder="Grade/Year" required disabled>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="section">Section/Course</label>
                          <input type="text" class="form-control" id="section" name="section" placeholder="Section/Course" required disabled>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required disabled>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="Address" required disabled>
                    </div>

                    <div class="form-group">
                        <label for="profile_image">Profile Picture</label>
                        <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*" disabled>
                    </div>

                    <button type="submit" class="btn btn-primary mr-2" disabled id="submit-btn">Submit</button>
                    <button type="reset" class="btn btn-light">Cancel</button>
                  </div>
                </div>
              </div>

            </div>
          </form>

        </div>
        <?php include 'partials/footer.php'; ?>
      </div>
    </div>   
  </div>

  <script src="static/vendors/js/vendor.bundle.base.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script src="static/vendors/datatables.net/jquery.dataTables.js"></script>
  <script src="static/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
  <script src="static/js/dataTables.select.min.js"></script>
  <script src="static/js/off-canvas.js"></script>
  <script src="static/js/hoverable-collapse.js"></script>
  <script src="static/js/template.js"></script>
  <script src="static/js/settings.js"></script>
  <script src="static/js/todolist.js"></script>
  <script src="static/js/dashboard.js"></script>
  <script src="static/js/Chart.roundedBarCharts.js"></script>

  <script>
  const uidInput = document.getElementById("uid");
  const uidDisplay = document.getElementById("uid-display");
  const rfidAnimation = document.getElementById("rfid-animation");
  const rfidCircle = document.getElementById("rfid-circle");
  const rfidStatus = document.getElementById("rfid-status");
  const loadingSpinner = document.getElementById("loading-spinner");

  const firstname = document.getElementById("firstname");
  const lastname = document.getElementById("lastname");
  const year = document.getElementById("year");
  const section = document.getElementById("section");
  const email = document.getElementById("email");
  const address = document.getElementById("address");
  const profileImage = document.getElementById("profile_image");
  const submitBtn = document.getElementById("submit-btn");
  const regType = document.getElementById("registration-type");
  const yearSectionGroup = document.getElementById("year-section-group");

  let scanning = false;
  let buffer = "";

  // Toggle Grade/Year & Section fields based on dropdown
  regType.addEventListener("change", () => {
    if (regType.value === "Student") {
      yearSectionGroup.style.display = "flex";
      year.required = true;
      section.required = true;
    } else {
      yearSectionGroup.style.display = "none";
      year.required = false;
      section.required = false;
    }
  });

  document.addEventListener("keydown", function(e) {
    if (!scanning) {
      buffer = "";
      scanning = true;
    }

    if (e.key === "Enter") {
      e.preventDefault();
      if (buffer.trim() !== "") {
        const uid = buffer.trim();
        uidInput.value = uid;
        uidDisplay.textContent = uid;

        loadingSpinner.style.display = "block";
        rfidAnimation.style.display = "none";

        setTimeout(() => {
          fetch("validate_uid.php?uid=" + uid)
            .then(res => res.json())
            .then(data => {
              loadingSpinner.style.display = "none";
              rfidAnimation.style.display = "block";

              if (data.status === "valid") {
                rfidCircle.classList.remove("red");
                rfidCircle.classList.add("green");
                rfidStatus.textContent = "✅ Valid Card!";
                rfidStatus.classList.remove("text-danger");
                rfidStatus.classList.add("text-success");

                firstname.disabled = false;
                lastname.disabled = false;
                year.disabled = false;
                section.disabled = false;
                email.disabled = false;
                address.disabled = false;
                profileImage.disabled = false;
                submitBtn.disabled = false;
              } else {
                rfidCircle.classList.remove("green");
                rfidCircle.classList.add("red");
                rfidStatus.textContent = "❌ Invalid Card!";
                rfidStatus.classList.remove("text-success");
                rfidStatus.classList.add("text-danger");

                firstname.disabled = true;
                lastname.disabled = true;
                year.disabled = true;
                section.disabled = true;
                email.disabled = true;
                address.disabled = true;
                profileImage.disabled = true;
                submitBtn.disabled = true;
              }
            });
        }, 2000);
      }
      scanning = false;
      buffer = "";
    } else {
      if (e.key.length === 1) {
        buffer += e.key;
      }
    }
  });
  </script>

</body>
</html>
