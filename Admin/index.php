<?php 
error_reporting(0);
include '../includes/session.php';
include '../includes/dbcon.php';

/* ===========================
   FUNCTIONS TO GET COUNTS
   =========================== */

function getAdminCount($conn) {
    $sql = "SELECT COUNT(*) AS total FROM tbl_admin";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function getStudentCount($conn) {
    $sql = "SELECT COUNT(*) AS total FROM tbl_students";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function getBookCount($conn) {
    $sql = "SELECT COUNT(*) AS total FROM tbl_books";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function getBorrowedBookCount($conn) {
    $sql = "SELECT COUNT(*) AS total FROM tbl_books WHERE status='unavailable'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <?php include "partials/head.php";?>
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
</head>
<body>
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <?php include "partials/navbar.php";?>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_settings-panel.html -->
      <?php include "partials/settings-panel.php";?>
     
      <!-- partial -->
      <!-- partial:partials/_sidebar.html -->
      <?php include "partials/sidebar.php";?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">Welcome Admin!!!</h3>
                </div>
              </div>
            </div>
          </div>

          <!-- START INLINE CARDS -->
          <div class="row">
            <!-- Admin Count -->
            
            
            <!-- Books Available -->
            <div class="col-md-3 mb-4 stretch-card transparent">
              <div class="card card-light-blue">
                <div class="card-body">
                  <p class="mb-4">Books Available</p>
                  <p class="fs-30 mb-2">
                    <?php echo getBookCount($conn); ?>
                  </p>
                </div>
              </div>
            </div>
            
            <!-- Number of Books Borrowed -->
            <div class="col-md-3 mb-4 stretch-card transparent">
              <div class="card card-light-danger">
                <div class="card-body">
                  <p class="mb-4">Number of Books Borrowed</p>
                  <p class="fs-30 mb-2">
                    <?php echo getBorrowedBookCount($conn); ?>
                  </p>
                </div>
              </div>
            </div>
          </div>
          <!-- END INLINE CARDS -->

          <!-- Borrowed Books Table -->
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title mb-0">Books Borrowed Table</p>
                  <div class="table-responsive">
                    <table id="borrowedBooksTable" class="table table-striped table-borderless">
                      <thead>
                        <tr>
                          <th>Student Name</th>
                          <th>Book Title</th>
                          <th>Borrowed Date</th>
                          <th>Status</th>
                        </tr>  
                      </thead>
                      <tbody>
                        <?php
                        $sql = "SELECT 
                                  s.firstname, 
                                  s.lastname, 
                                  b.title, 
                                  l.borrow_date, 
                                  l.status 
                                FROM tbl_rfid_loan l
                                INNER JOIN tbl_students s ON l.student_id = s.id
                                INNER JOIN tbl_books b ON l.book_id = b.id
                                ORDER BY l.borrow_date DESC";
                        $result = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $studentName = $row['firstname'] . " " . $row['lastname'];
                                $bookTitle = $row['title'];
                                $borrowDate = date("d M Y", strtotime($row['borrow_date']));
                                $status = $row['status'];

                                // choose badge color based on status
                                $badgeClass = "badge-warning";
                                if (strtolower($status) == "returned") {
                                    $badgeClass = "badge-success";
                                } elseif (strtolower($status) == "unavailable") {
                                    $badgeClass = "badge-danger";
                                }

                                echo "<tr>
                                        <td>{$studentName}</td>
                                        <td class='font-weight-bold'>{$bookTitle}</td>
                                        <td>{$borrowDate}</td>
                                        <td class='font-weight-medium'><div class='badge {$badgeClass}'>{$status}</div></td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>No borrowed books found</td></tr>";
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Advanced Table (placeholder removed to clean layout) -->
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        <?php include 'partials/footer.php'; ?>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>   
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- plugins:js -->
  <script src="static/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
  <script src="static/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
  <script src="static/js/dataTables.select.min.js"></script>

  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="static/js/off-canvas.js"></script>
  <script src="static/js/hoverable-collapse.js"></script>
  <script src="static/js/template.js"></script>
  <script src="static/js/settings.js"></script>
  <script src="static/js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="static/js/dashboard.js"></script>
  <script src="static/js/Chart.roundedBarCharts.js"></script>

  <!-- Initialize DataTable -->
  <script>
    $(document).ready(function () {
        $('#borrowedBooksTable').DataTable({
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50, 100],
            "ordering": false,
            "searching": false,
            "scrollY": "400px",
            "scrollCollapse": true,
            "paging": true
        });
    });
  </script>
  <!-- End custom js for this page-->
</body>

</html>
