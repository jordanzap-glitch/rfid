<?php
// Include database connection
include '../includes/session.php';
include '../includes/dbcon.php';

$student = null;
$statusMsg = "";

// Handle RFID scan submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['uid']) && !isset($_POST['borrow'])) {
    $uid = mysqli_real_escape_string($conn, $_POST['uid']);

    $sql = "SELECT id, uid, firstname, lastname, email, address, eligible_status, year, section, image_path
            FROM tbl_students 
            WHERE uid = '$uid' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $student = mysqli_fetch_assoc($result);
    } else {
        $statusMsg = '<div class="alert alert-danger">❌ No student found with this UID.</div>';
    }
}

// Handle Borrow Books submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrow'])) {
    $uid = mysqli_real_escape_string($conn, $_POST['uid']);
    $book_ids = isset($_POST['book_ids']) ? $_POST['book_ids'] : [];

    // Get student_id from UID
    $studentSql = "SELECT id, eligible_status FROM tbl_students WHERE uid = '$uid' LIMIT 1";
    $studentResult = mysqli_query($conn, $studentSql);

    if ($studentResult && mysqli_num_rows($studentResult) > 0) {
        $studentRow = mysqli_fetch_assoc($studentResult);
        $student_id = $studentRow['id'];
        $eligible_status = $studentRow['eligible_status'];

        if ($eligible_status == 1) {
            if (!empty($book_ids)) {
                $successCount = 0;
                foreach ($book_ids as $book_id) {
                    $book_id = mysqli_real_escape_string($conn, $book_id);

                    // Insert into tbl_rfid_loan
                    $insert = "INSERT INTO tbl_rfid_loan (uid, student_id, book_id, status, borrow_date) 
                               VALUES ('$uid', '$student_id', '$book_id', 'borrowed', NOW())";

                    if (mysqli_query($conn, $insert)) {
                        // Update tbl_books to unavailable
                        $updateBook = "UPDATE tbl_books SET status = 'unavailable' WHERE id = '$book_id'";
                        mysqli_query($conn, $updateBook);

                        $successCount++;
                    }
                }

                // Update student eligibility to 0 (Not Eligible)
                $updateStudent = "UPDATE tbl_students SET eligible_status = 0 WHERE id = '$student_id'";
                mysqli_query($conn, $updateStudent);

                if ($successCount > 0) {
                    $statusMsg = '<div class="alert alert-success">✅ ' . $successCount . ' book(s) borrowed successfully! Student is now Not Eligible.</div>';
                } else {
                    $statusMsg = '<div class="alert alert-danger">❌ Failed to borrow books.</div>';
                }
            } else {
                $statusMsg = '<div class="alert alert-warning">⚠️ No books selected.</div>';
            }
        } else {
            $statusMsg = '<div class="alert alert-warning">⚠️ Student is not eligible to borrow books.</div>';
        }
    } else {
        $statusMsg = '<div class="alert alert-danger">❌ Invalid UID. Student not found.</div>';
    }
}

// Fetch books for dropdown (only available books)
$books = [];
$bookSql = "SELECT id, title FROM tbl_books WHERE status = 'available' ORDER BY title ASC";
$bookResult = mysqli_query($conn, $bookSql);
if ($bookResult && mysqli_num_rows($bookResult) > 0) {
    while ($row = mysqli_fetch_assoc($bookResult)) {
        $books[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "partials/head.php";?>
 <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
 <style>
   .profile-img {
     width: 120px;
     height: 120px;
     object-fit: cover;
     border-radius: 50%;
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

          <!-- Status Message -->
          <?php if ($statusMsg) echo $statusMsg; ?>

          <div class="row">
            <div class="col-md-4 grid-margin stretch-card">
              <div class="card" id="rfid-card">
                <div class="card-body text-center">
                  <h4 class="card-title">Scan RFID Card</h4>
                  <p class="card-description">Tap an RFID card to fetch student info</p>

                  <!-- UID Display -->
                  <form method="POST" action="">
                    <div class="form-group">
                      <label for="uid">UID</label>
                      <p id="uid-display" class="form-control text-center font-weight-bold" 
                        style="background:#f8f9fa;">
                        <?php echo isset($student['uid']) ? htmlspecialchars($student['uid']) : 'Waiting for scan...'; ?>
                      </p>
                      <input type="hidden" id="uid" name="uid" 
                        value="<?php echo isset($student['uid']) ? htmlspecialchars($student['uid']) : ''; ?>" required>
                    </div>
                    <button type="submit" id="scan-submit" style="display:none;">Submit</button>
                  </form>

                  <!-- Borrow Books Section -->
                  <hr>
                  <h4 class="card-title">Borrow Books</h4>
                  <form method="POST" action="">
                    <input type="hidden" name="borrow" value="1">
                    <div class="form-group" style="display:none;">
                        <input type="hidden" name="uid" id="borrow-uid"
                            value="<?php echo isset($student['uid']) ? htmlspecialchars($student['uid']) : ''; ?>" 
                            required>
                        </div>
                        <div class="book-counter">Books Selected: <span id="book-count">0</span></div>
                        <div class="selected-books-list" id="selected-books-list">
                            
                        </div>

                        <div class="form-group">
                        <label for="book_ids">Select Books</label>
                        <select name="book_ids[]" id="book_ids" class="form-control" multiple="multiple" required>
                            <?php foreach ($books as $book): ?>
                            <option value="<?php echo $book['id']; ?>">
                                <?php echo htmlspecialchars($book['title']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        </div>

                    <button type="submit" class="btn btn-primary btn-block">Borrow</button>
                  </form>

                </div>
              </div>
            </div>

            <!-- Student Profile Card -->
            <div class="col-md-8 grid-margin stretch-card">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body profile-card" id="student-profile">
                <h4 class="card-title text-left mb-4">🎓 Student Profile</h4>

                <!-- Loader -->
                <div id="profile-loader" class="text-center py-5" style="display:none;">
                  <div class="spinner-border text-primary" role="status"></div>
                  <p class="mt-3 fw-bold text-muted">Wait for the validation...</p>
                </div>

                <div id="profile-content" style="display:<?php echo $student ? 'block' : 'none'; ?>;">
                <?php if ($student): ?>
                    <!-- Profile Header -->
                    <div class="d-flex align-items-center mb-4">
                    <img src="<?php 
                        if (!empty($student['image_path'])) {
                            echo '../uploads/' . htmlspecialchars($student['image_path']);
                        } else {
                            echo 'static/images/default-avatar.png';
                        }
                        ?>" 
                        alt="Profile Photo" class="profile-img me-4 border border-3 border-primary shadow-sm">

                    <div class="text-start">
                        <h3 class="mb-1 fw-bold text-dark">
                        &nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($student['firstname']) . " " . htmlspecialchars($student['lastname']); ?>
                        </h3>
                        <?php if ($student['eligible_status'] == 1): ?>
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge bg-success px-3 py-2">Eligible to Borrow</span>
                        <?php else: ?>
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge bg-danger px-3 py-2">Not Eligible to Borrow</span>
                        <?php endif; ?>
                    </div>
                    </div>

                    <!-- Profile Details -->
                    <div class="row text-start">
                    <div class="col-md-6 mb-3">
                        <p class="mb-1 fw-semibold text-muted"><i class="mdi mdi-email-outline me-2"></i>Email</p>
                        <p class="fs-6 text-dark"><?php echo htmlspecialchars($student['email']); ?></p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <p class="mb-1 fw-semibold text-muted"><i class="mdi mdi-home-outline me-2"></i>Address</p>
                        <p class="fs-6 text-dark"><?php echo htmlspecialchars($student['address']); ?></p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <p class="mb-1 fw-semibold text-muted"><i class="mdi mdi-card-account-details-outline me-2"></i>UID</p>
                        <p class="fs-6 text-dark"><?php echo htmlspecialchars($student['uid']); ?></p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <p class="mb-1 fw-semibold text-muted"><i class="mdi mdi-book-open-variant me-2"></i>Status</p>
                        <?php if ($student['eligible_status'] == 1): ?>
                          <p class="fs-6 text-success fw-bold">Eligible to Borrow</p>
                        <?php else: ?>
                          <p class="fs-6 text-danger fw-bold">Not Eligible to Borrow</p>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <p class="mb-1 fw-semibold text-muted"><i class="mdi mdi-school me-2"></i>Grade/Year</p>
                        <p class="fs-6 text-dark"><?php echo htmlspecialchars($student['year']); ?></p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <p class="mb-1 fw-semibold text-muted"><i class="mdi mdi-account-group-outline me-2"></i>Course/Section</p>
                        <p class="fs-6 text-dark"><?php echo htmlspecialchars($student['section']); ?></p>
                    </div>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-5">
                    <i class="mdi mdi-account-circle-outline display-1 d-block mb-3"></i>
                    <p class="fs-5">No student data yet. Please scan a card.</p>
                    </div>
                <?php endif; ?>
                </div>

                </div>
            </div>
            </div>


          </div>
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

  <!-- Select2 -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
  const uidInput = document.getElementById("uid");
  const uidDisplay = document.getElementById("uid-display");
  const scanSubmit = document.getElementById("scan-submit");
  const borrowUid = document.getElementById("borrow-uid");
  const profileLoader = document.getElementById("profile-loader");
  const profileContent = document.getElementById("profile-content");

  let scanning = false;

  // Simulate RFID scan
  document.addEventListener("keydown", function(e) {
    if (!scanning) {
      // Start a new scan → clear previous UID
      uidInput.value = "";
      scanning = true;
    }

    if (e.key === "Enter") {
      e.preventDefault();
      if (uidInput.value.trim() !== "") {
        uidDisplay.textContent = uidInput.value;
        borrowUid.value = uidInput.value;

        // Show loader in profile card
        profileLoader.style.display = "block";
        profileContent.style.display = "none";

        // After 2 seconds, submit form
        setTimeout(() => {
          scanSubmit.click();
          scanning = false; // reset for next scan
        }, 2000);
      }
    } else {
      if (e.key.length === 1) {
        uidInput.value += e.key;
      }
    }
  });

  // Initialize Select2
  $(document).ready(function() {
    $('#book_ids').select2({
      placeholder: "Search and select books",
      allowClear: true,
      width: '100%'
    });

    // Update selected books and counter
    $('#book_ids').on('change', function() {
      let selected = $(this).find("option:selected");
      let count = selected.length;
      $("#book-count").text(count);

      let list = $("#selected-books-list ul");
      list.empty();
      selected.each(function() {
        list.append("<li><i class='mdi mdi-book-open-variant'></i> " + $(this).text() + "</li>");
      });
    });
  });
  </script>
</body>
</html>
