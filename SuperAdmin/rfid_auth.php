<?php
// Include database connection
include '../includes/session.php';
include '../includes/dbcon.php';

$statusMsg = "";

// Handle form submission (UID insert)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['uid'])) {
    $uid = mysqli_real_escape_string($conn, $_POST['uid']);
    $date_created = date("Y-m-d H:i:s");
    $status = "valid";

    // ✅ Check if UID already exists
    $checkQuery = "SELECT inuse FROM tbl_rfid_auth WHERE uid = '$uid' LIMIT 1";
    $checkResult = mysqli_query($conn, $checkQuery);

    if ($checkResult && mysqli_num_rows($checkResult) > 0) {
        $row = mysqli_fetch_assoc($checkResult);

        if ($row['inuse'] == 1) {
            // UID already in use
            $statusMsg = '<div class="alert alert-danger">❌ This RFID card is already in use!</div>';
        } elseif ($row['inuse'] == 0) {
            // UID already exists but not in use
            $statusMsg = '<div class="alert alert-warning">⚠️ This RFID card is already registered but not yet in use!</div>';
        }
    } else {
        // Insert UID into tbl_rfid_auth
        $sql = "INSERT INTO tbl_rfid_auth (uid, status, inuse, date_created) 
                VALUES ('$uid', '$status', 0, '$date_created')";

        if (mysqli_query($conn, $sql)) {
            $statusMsg = '<div class="alert alert-success">✅ RFID UID inserted successfully!</div>';
        } else {
            $error = htmlspecialchars(mysqli_error($conn));
            $statusMsg = '<div class="alert alert-danger">❌ Error: '.$error.'</div>';
        }
    }
}

// ✅ Fetch RFID records for the table
$rfidQuery = "SELECT uid, status, inuse, date_created FROM tbl_rfid_auth ORDER BY date_created DESC";
$rfidResult = mysqli_query($conn, $rfidQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <?php include "partials/head.php";?>
  <style>
    .badge-used {
      background-color: #28a745;
      color: #fff;
      padding: 5px 10px;
      border-radius: 12px;
      font-size: 0.8rem;
    }
    .badge-notused {
      background-color: #dc3545;
      color: #fff;
      padding: 5px 10px;
      border-radius: 12px;
      font-size: 0.8rem;
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
          <div class="row">
            <!-- Left: RFID Registration -->
            <div class="col-md-4 grid-margin stretch-card">
              <div class="card shadow-sm rounded-3">
                <div class="card-body">
                  <h4 class="card-title text-center">RFID Registration</h4>
                  <p class="card-description text-center">Scan an RFID card to register its UID</p>

                  <!-- Status message -->
                  <?php if ($statusMsg) echo $statusMsg; ?>

                  <!-- RFID Form -->
                  <form class="forms-sample" method="POST" action="">
                    <div class="row">
                      <div class="col-12">
                        <div class="card">
                          <div class="card-body text-center">
                            <h5 class="card-title">Scan RFID Card</h5>
                            <p class="card-description">Tap an RFID card to generate UID</p>

                            <!-- Spinner -->
                            <?php include 'partials/spinner.php'; ?>

                            <!-- RFID Animation -->
                            <div id="rfid-animation" class="mt-3" style="display:none;">
                              <p class="text-success font-weight-bold mt-2">Card Scanned!</p>
                            </div>

                            <!-- UID Display -->
                            <div class="form-group mt-3">
                              <label for="uid">UID</label>
                              <p id="uid-display" class="form-control text-center font-weight-bold" 
                                style="background:#f8f9fa;">Waiting for scan...</p>
                              <input type="hidden" id="uid" name="uid" required>
                            </div>

                            <button type="submit" class="btn btn-primary mt-3" id="submit-btn" disabled>Save UID</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
                  <!-- End RFID Form -->

                </div>
              </div>
            </div>

            <!-- Right: RFID Table -->
            <div class="col-md-8 grid-margin stretch-card">
              <div class="card shadow-sm rounded-3">
                <div class="card-body">
                  <h4 class="card-title">Registered RFID Cards</h4>
                  <div class="table-responsive">
                    <table id="rfidTable" class="table table-hover">
                      <thead>
                        <tr>
                          <th>UID</th>
                          <th>Status</th>
                          <th>Usage</th>
                          <th>Date Registered</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if ($rfidResult && mysqli_num_rows($rfidResult) > 0): ?>
                          <?php while ($row = mysqli_fetch_assoc($rfidResult)): ?>
                            <tr>
                              <td><?php echo htmlspecialchars($row['uid']); ?></td>
                              <td>
                                <span class="badge bg-info text-dark">
                                  <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                                </span>
                              </td>
                              <td>
                                <?php if ($row['inuse'] == 1): ?>
                                  <span class="badge-used">Used</span>
                                <?php else: ?>
                                  <span class="badge-notused">Not Used</span>
                                <?php endif; ?>
                              </td>
                              <td><?php echo htmlspecialchars($row['date_created']); ?></td>
                            </tr>
                          <?php endwhile; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="4" class="text-center">No RFID cards registered yet.</td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div> <!-- row end -->
        </div>
        <?php include 'partials/footer.php'; ?>
      </div>
    </div>   
  </div>

  <!-- plugins:js -->
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

  <!-- RFID scanner script -->
  <script>
  const uidInput = document.getElementById("uid");
  const uidDisplay = document.getElementById("uid-display");
  const rfidAnimation = document.getElementById("rfid-animation");
  const submitBtn = document.getElementById("submit-btn");
  const loadingSpinner = document.getElementById("loading-spinner");

  let scanning = false;
  let scannedUID = "";

  // RFID scan (keyboard emulation)
  document.addEventListener("keydown", function(e) {
    if (!scanning) {
      scanning = true;
      scannedUID = "";
    }

    if (e.key === "Enter") {
      e.preventDefault();
      if (scannedUID.trim() !== "") {
        uidInput.value = scannedUID;
        uidDisplay.textContent = scannedUID;

        // Show spinner first
        loadingSpinner.style.display = "block";
        rfidAnimation.style.display = "none";
        submitBtn.disabled = true;

        // Simulate validation delay
        setTimeout(() => {
          loadingSpinner.style.display = "none";
          rfidAnimation.style.display = "block";
          submitBtn.disabled = false;
        }, 2000);
      }
      scanning = false;
    } else {
      if (e.key.length === 1) {
        scannedUID += e.key;
      }
    }
  });
  </script>

  <!-- DataTable script -->
  <script>
    $(document).ready(function () {
        $('#rfidTable').DataTable({
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50, 100],
            "ordering": false,
            "searching": true,
            "scrollY": "400px",
            "scrollCollapse": true,
            "paging": true
        });
    });
  </script>
</body>
</html>
