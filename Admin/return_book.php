<?php
// Include database connection
include '../includes/session.php';
include '../includes/dbcon.php';

$student = null; // will hold borrower info whether from students or regulars
$statusMsg = "";
$loans = [];

/**
 * Helper: find borrower by uid in tbl_students or tbl_regulars
 * Returns associative array with keys: source_table ('tbl_students'|'tbl_regulars'), id, uid, firstname, lastname, email, address, eligible_status, image_path, user_type (for regulars), year, section
 * or null if not found.
 */
function findBorrowerByUid($conn, $uid) {
    // search tbl_students
    $u = mysqli_real_escape_string($conn, $uid);
    $sqlS = "SELECT id, uid, firstname, lastname, email, address, eligible_status, year, section, image_path
             FROM tbl_students
             WHERE uid = '$u' LIMIT 1";
    $resS = mysqli_query($conn, $sqlS);
    if ($resS && mysqli_num_rows($resS) > 0) {
        $r = mysqli_fetch_assoc($resS);
        return [
            'source_table' => 'tbl_students',
            'id' => $r['id'],
            'uid' => $r['uid'],
            'firstname' => $r['firstname'],
            'lastname' => $r['lastname'],
            'email' => $r['email'],
            'address' => $r['address'],
            'eligible_status' => $r['eligible_status'],
            'image_path' => $r['image_path'],
            'user_type' => 'Student',
            'year' => $r['year'],
            'section' => $r['section'],
        ];
    }

    // search tbl_regulars
    $sqlR = "SELECT id, uid, firstname, lastname, email, address, eligible_status, image_path, user_type
             FROM tbl_regulars
             WHERE uid = '$u' LIMIT 1";
    $resR = mysqli_query($conn, $sqlR);
    if ($resR && mysqli_num_rows($resR) > 0) {
        $r = mysqli_fetch_assoc($resR);
        return [
            'source_table' => 'tbl_regulars',
            'id' => $r['id'],
            'uid' => $r['uid'],
            'firstname' => $r['firstname'],
            'lastname' => $r['lastname'],
            'email' => $r['email'],
            'address' => $r['address'],
            'eligible_status' => $r['eligible_status'],
            'image_path' => $r['image_path'],
            'user_type' => $r['user_type'], // expected: 'Teacher' or 'Non Teaching'
            // For regulars, year/section are N/A
            'year' => 'N/A',
            'section' => 'N/A',
        ];
    }

    return null;
}

// Handle RFID scan submission (fetch borrower info from either table)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['uid']) && !isset($_POST['return'])) {
    $uid = mysqli_real_escape_string($conn, $_POST['uid']);

    $borrower = findBorrowerByUid($conn, $uid);

    if ($borrower) {
        $student = $borrower;

        // Fetch all active loans (status borrowed) for that borrower id (works for students or regulars)
        $borrower_id = $student['id'];
        if ($student['source_table'] === 'tbl_students') {
            $loanSql = "SELECT l.id AS loan_id, l.book_id, l.borrow_date, b.title
                        FROM tbl_rfid_loan l
                        JOIN tbl_books b ON l.book_id = b.id
                        WHERE l.student_id = '$borrower_id' AND l.status = 'borrowed'
                        ORDER BY l.borrow_date ASC";
        } else {
            $loanSql = "SELECT l.id AS loan_id, l.book_id, l.borrow_date, b.title
                        FROM tbl_rfid_loan l
                        JOIN tbl_books b ON l.book_id = b.id
                        WHERE l.regulars_id = '$borrower_id' AND l.status = 'borrowed'
                        ORDER BY l.borrow_date ASC";
        }

        $loanResult = mysqli_query($conn, $loanSql);
        if ($loanResult && mysqli_num_rows($loanResult) > 0) {
            while ($row = mysqli_fetch_assoc($loanResult)) {
                $loans[] = $row;
            }
        }
    } else {
        $student = null;
        $statusMsg = '<div class="alert alert-danger">❌ No borrower found with this UID.</div>';
    }
}

// Handle Return Book submission (multiple books)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['return'])) {
    $loan_ids = $_POST['loan_ids'] ?? [];
    $uid = mysqli_real_escape_string($conn, $_POST['uid']);

    if (!empty($loan_ids)) {
        $processedBorrowerId = null;
        foreach ($loan_ids as $loan_id) {
            $loan_id = mysqli_real_escape_string($conn, $loan_id);

            // Fetch loan (ensure it belongs to the provided UID and is borrowed)
            $loanSql = "SELECT * FROM tbl_rfid_loan WHERE id = '$loan_id' AND uid = '$uid' AND status = 'borrowed' LIMIT 1";
            $loanResult = mysqli_query($conn, $loanSql);

            if ($loanResult && mysqli_num_rows($loanResult) > 0) {
                $loanRow = mysqli_fetch_assoc($loanResult);
                $book_id = $loanRow['book_id'];

                // Save borrower id (student_id or regulars_id)
                $processedBorrowerId = !empty($loanRow['student_id']) ? $loanRow['student_id'] : $loanRow['regulars_id'];

                // Update loan status to returned
                $updateLoan = "UPDATE tbl_rfid_loan SET status = 'returned', return_date = NOW() WHERE id = '$loan_id'";
                if (mysqli_query($conn, $updateLoan)) {
                    // Make book available again
                    $updateBook = "UPDATE tbl_books SET status = 'available' WHERE id = '$book_id'";
                    mysqli_query($conn, $updateBook);
                }
            }
        }

        if ($processedBorrowerId !== null) {
            // Determine whether this borrower id belongs to tbl_students or tbl_regulars by checking uid
            $borrowerAfter = findBorrowerByUid($conn, $uid);

            if ($borrowerAfter) {
                $source = $borrowerAfter['source_table'];
                $idToUpdate = $borrowerAfter['id'];

                // Check if borrower still has active loans
                if ($source === 'tbl_students') {
                    $checkSql = "SELECT COUNT(*) AS cnt FROM tbl_rfid_loan WHERE student_id = '$idToUpdate' AND status = 'borrowed'";
                } else {
                    $checkSql = "SELECT COUNT(*) AS cnt FROM tbl_rfid_loan WHERE regulars_id = '$idToUpdate' AND status = 'borrowed'";
                }
                $checkResult = mysqli_query($conn, $checkSql);
                $countRow = mysqli_fetch_assoc($checkResult);

                if ($countRow['cnt'] == 0) {
                    // Update eligible_status to 1 in correct table
                    if ($source === 'tbl_students') {
                        mysqli_query($conn, "UPDATE tbl_students SET eligible_status = 1 WHERE id = '$idToUpdate'");
                    } else {
                        mysqli_query($conn, "UPDATE tbl_regulars SET eligible_status = 1 WHERE id = '$idToUpdate'");
                    }
                }
            }
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
                  <p class="card-description">Tap an RFID card to fetch borrower info</p>

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

            <!-- Borrower Profile Card -->
            <div class="col-md-8 grid-margin stretch-card">
              <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body">
                  <h4 class="card-title mb-4">🎓 Borrower's Profile</h4>

                  <!-- Loader -->
                  <div id="profile-loader" class="text-center py-5" style="display:none;">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 fw-bold text-muted">Wait for the validation...</p>
                  </div>

                  <div id="profile-content" style="display:<?php echo $student ? 'block' : 'none'; ?>;">
                  <?php if ($student): ?>
                    <div class="d-flex align-items-center mb-4">
                      <?php
                      // ✅ Image handling (tbl_students or tbl_regulars)
                      $imgSrc = '../img/defaulticon.png';
                      if (!empty($student['image_path'])) {
                          $candidatePath = '../uploads/' . $student['image_path'];
                          if (file_exists($candidatePath)) {
                              $imgSrc = $candidatePath;
                          }
                      }
                      ?>
                      <img src="<?php echo htmlspecialchars($imgSrc); ?>" 
                        alt="Profile Photo" class="profile-img me-4 border border-3 border-primary shadow-sm">

                      <div class="text-start">
                        <h3 class="mb-1 fw-bold text-dark">
                          &nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($student['firstname']) . " " . htmlspecialchars($student['lastname']); ?>
                        </h3>

                        <div class="mt-2">
                          <!-- Show user type badge -->
                          &nbsp;&nbsp;&nbsp;<?php if (isset($student['user_type'])): ?>
                            <span class="badge bg-info px-3 py-2">
                              <?php echo htmlspecialchars($student['user_type']); ?>
                            </span>
                          <?php else: ?>
                            <span class="badge bg-secondary px-3 py-2">Student</span>
                          <?php endif; ?>

                          &nbsp;&nbsp;
                          <?php if ($student['eligible_status'] == 1): ?>
                            <span class="badge bg-success px-3 py-2">Eligible to Borrow</span>
                          <?php else: ?>
                            <span class="badge bg-danger px-3 py-2">Not Eligible to Borrow</span>
                          <?php endif; ?>
                        </div>

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
                        <p class="fs-6 text-dark"><?php echo htmlspecialchars($student['year'] ?? 'N/A'); ?></p>
                      </div>
                      <div class="col-md-6 mb-3">
                        <p class="mb-1 fw-semibold text-muted">Course/Section</p>
                        <p class="fs-6 text-dark"><?php echo htmlspecialchars($student['section'] ?? 'N/A'); ?></p>
                      </div>
                    </div>

                    <!-- Borrowed Books Info -->
                    <hr>
                    <h5 class="mb-3">📚 Borrowed Books</h5>
                    <?php if (!empty($loans)): ?>
                      <form method="POST" action="">
                        <input type="hidden" name="return" value="1">
                        <input type="hidden" name="uid" value="<?php echo htmlspecialchars($student['uid']); ?>">

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
                      <p class="fs-5">No borrower data yet. Please scan a card.</p>
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
