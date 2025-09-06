<?php
// Include database connection
include '../includes/session.php';
include '../includes/dbcon.php';

$student = null;
$statusMsg = "";
$loans = [];

// Handle RFID scan submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['uid']) && !isset($_POST['return'])) {
    $uid = mysqli_real_escape_string($conn, $_POST['uid']);

    $sql = "SELECT id, uid, firstname, lastname, email, address, eligible_status, year, section, image_path
            FROM tbl_students 
            WHERE uid = '$uid' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $student = mysqli_fetch_assoc($result);

        // Fetch all active loans (status borrowed)
        $student_id = $student['id'];
        $loanSql = "SELECT l.id AS loan_id, l.book_id, l.borrow_date, b.title
                    FROM tbl_rfid_loan l
                    JOIN tbl_books b ON l.book_id = b.id
                    WHERE l.student_id = '$student_id' AND l.status = 'borrowed'
                    ORDER BY l.borrow_date ASC";
        $loanResult = mysqli_query($conn, $loanSql);
        if ($loanResult && mysqli_num_rows($loanResult) > 0) {
            while ($row = mysqli_fetch_assoc($loanResult)) {
                $loans[] = $row;
            }
        }
    } else {
        $statusMsg = '<div class="alert alert-danger">❌ No student found with this UID.</div>';
    }
}

// Handle Return Book submission (multiple books)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['return'])) {
    $loan_ids = $_POST['loan_ids'] ?? [];
    $uid = mysqli_real_escape_string($conn, $_POST['uid']);

    if (!empty($loan_ids)) {
        foreach ($loan_ids as $loan_id) {
            $loan_id = mysqli_real_escape_string($conn, $loan_id);

            // Fetch loan
            $loanSql = "SELECT * FROM tbl_rfid_loan WHERE id = '$loan_id' AND uid = '$uid' AND status = 'borrowed' LIMIT 1";
            $loanResult = mysqli_query($conn, $loanSql);

            if ($loanResult && mysqli_num_rows($loanResult) > 0) {
                $loanRow = mysqli_fetch_assoc($loanResult);
                $book_id = $loanRow['book_id'];
                $student_id = $loanRow['student_id'];

                // Update loan status
                $updateLoan = "UPDATE tbl_rfid_loan SET status = 'returned', return_date = NOW() WHERE id = '$loan_id'";
                if (mysqli_query($conn, $updateLoan)) {
                    // Make book available again
                    $updateBook = "UPDATE tbl_books SET status = 'available' WHERE id = '$book_id'";
                    mysqli_query($conn, $updateBook);
                }
            }
        }

        // Check if student still has active loans
        $checkSql = "SELECT COUNT(*) AS cnt FROM tbl_rfid_loan WHERE student_id = '$student_id' AND status = 'borrowed'";
        $checkResult = mysqli_query($conn, $checkSql);
        $countRow = mysqli_fetch_assoc($checkResult);

        if ($countRow['cnt'] == 0) {
            mysqli_query($conn, "UPDATE tbl_students SET eligible_status = 1 WHERE id = '$student_id'");
        }

        $statusMsg = '<div class="alert alert-success">✅ Selected books returned successfully!</div>';
    } else {
        $statusMsg = '<div class="alert alert-warning">⚠️ Please select at least one book to return.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "partials/head.php";?>
  <style>
    .profile-img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
    }
    .borrowed-book {
      border: 1px solid #ddd;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 12px;
      background: #f9f9f9;
    }
    /* Spinner Loader */
    .spinner-border {
      width: 3rem;
      height: 3rem;
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
              <div class="card">
                <div class="card-body text-center">
                  <h4 class="card-title">Scan RFID Card</h4>
                  <p class="card-description">Tap an RFID card to fetch student info</p>

                  <form method="POST" action="">
                    <div class="form-group">
                      <label for="uid">UID</label>
                      <p id="uid-display" class="form-control text-center font-weight-bold" style="background:#f8f9fa;">
                        <?php echo isset($student['uid']) ? htmlspecialchars($student['uid']) : 'Waiting for scan...'; ?>
                      </p>
                      <input type="hidden" id="uid" name="uid" value="<?php echo isset($student['uid']) ? htmlspecialchars($student['uid']) : ''; ?>" required>
                    </div>
                    <button type="submit" id="scan-submit" style="display:none;">Submit</button>
                  </form>

                </div>
              </div>
            </div>

            <!-- Student Profile Card -->
            <div class="col-md-8 grid-margin stretch-card">
              <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body">
                  <h4 class="card-title mb-4">🎓 Student Profile</h4>

                  <!-- Loader -->
                  <div id="profile-loader" class="text-center py-5" style="display:none;">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 fw-bold text-muted">Wait for the validation...</p>
                  </div>

                  <div id="profile-content" style="display:<?php echo $student ? 'block' : 'none'; ?>;">
                  <?php if ($student): ?>
                    <div class="d-flex align-items-center mb-4">
                      <?php
                        $photoPath = !empty($student['image_path']) ? "../uploads/" . htmlspecialchars($student['image_path']) : "static/images/default-avatar.png";
                      ?>
                      <img src="<?php echo $photoPath; ?>" 
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

                    <div class="row text-start">
                      <div class="col-md-6 mb-3">
                        <p class="mb-1 fw-semibold text-muted">Email</p>
                        <p class="fs-6 text-dark"><?php echo htmlspecialchars($student['email']); ?></p>
                      </div>
                      <div class="col-md-6 mb-3">
                        <p class="mb-1 fw-semibold text-muted">Address</p>
                        <p class="fs-6 text-dark"><?php echo htmlspecialchars($student['address']); ?></p>
                      </div>
                      <div class="col-md-6 mb-3">
                        <p class="mb-1 fw-semibold text-muted">UID</p>
                        <p class="fs-6 text-dark"><?php echo htmlspecialchars($student['uid']); ?></p>
                      </div>
                      <div class="col-md-6 mb-3">
                        <p class="mb-1 fw-semibold text-muted">Status</p>
                        <?php if ($student['eligible_status'] == 1): ?>
                          <p class="fs-6 text-success fw-bold">Eligible to Borrow</p>
                        <?php else: ?>
                          <p class="fs-6 text-danger fw-bold">Not Eligible to Borrow</p>
                        <?php endif; ?>
                      </div>
                      <div class="col-md-6 mb-3">
                        <p class="mb-1 fw-semibold text-muted">Grade/Year</p>
                        <p class="fs-6 text-dark"><?php echo htmlspecialchars($student['year']); ?></p>
                      </div>
                      <div class="col-md-6 mb-3">
                        <p class="mb-1 fw-semibold text-muted">Course/Section</p>
                        <p class="fs-6 text-dark"><?php echo htmlspecialchars($student['section']); ?></p>
                      </div>
                    </div>

                    <!-- Borrowed Books Info -->
                      <hr>
                      <h5 class="mb-3">📚 Borrowed Books</h5>
                      <?php if (!empty($loans)): ?>
                        <form method="POST" action="">
                          <input type="hidden" name="return" value="1">
                          <input type="hidden" name="uid" value="<?php echo $student['uid']; ?>">

                          <div style="max-height: 250px; overflow-y: auto; padding-right: 10px;">
                            <?php foreach ($loans as $loan): ?>
                              <div class="borrowed-book d-flex align-items-center">
                                <input type="checkbox" name="loan_ids[]" value="<?php echo $loan['loan_id']; ?>" class="form-check-input me-2">
                                <div>
                                  <p class="mb-1"><strong>Title:</strong> <?php echo htmlspecialchars($loan['title']); ?></p>
                                  <p class="mb-0"><strong>Borrowed On:</strong> <?php echo htmlspecialchars($loan['borrow_date']); ?></p>
                                </div>
                              </div>
                            <?php endforeach; ?>
                          </div>

                          <button type="submit" class="btn btn-danger mt-3">Return Selected Books</button>
                        </form>
                      <?php else: ?>
                        <p class="text-muted mt-3">No borrowed books.</p>
                      <?php endif; ?>


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

  <!-- JS -->
  <script src="static/vendors/js/vendor.bundle.base.js"></script>
  <script>
  const uidInput = document.getElementById("uid");
  const uidDisplay = document.getElementById("uid-display");
  const scanSubmit = document.getElementById("scan-submit");
  const profileLoader = document.getElementById("profile-loader");
  const profileContent = document.getElementById("profile-content");

  let scanning = false;

  // Simulate RFID scan
  document.addEventListener("keydown", function(e) {
    if (!scanning) {
      // New scan → reset old UID
      uidInput.value = "";
      uidDisplay.textContent = "Scanning...";
      scanning = true;
    }

    if (e.key === "Enter") {
      e.preventDefault();
      if (uidInput.value.trim() !== "") {
        uidDisplay.textContent = uidInput.value;

        // Show loader in profile card
        profileLoader.style.display = "block";
        profileContent.style.display = "none";

        // After 2 seconds, submit form
        setTimeout(() => {
          scanSubmit.click();
          scanning = false; // ready for next scan
        }, 2000);
      }
    } else {
      if (e.key.length === 1) {
        uidInput.value += e.key;
        uidDisplay.textContent = uidInput.value; // live update display
      }
    }
  });
  </script>
</body>
</html>
