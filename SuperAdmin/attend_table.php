<?php
// Include database connection
include '../includes/session.php';
include '../includes/dbcon.php';

$statusMsg = "";

// ✅ Handle Delete Attendance Request
if (isset($_GET['delete_id']) && $_GET['type'] === 'attendance') {
    $deleteId = intval($_GET['delete_id']);

    $deleteQuery = "DELETE FROM tbl_attendance WHERE id = $deleteId";
    if (mysqli_query($conn, $deleteQuery)) {
        $statusMsg = '<div class="alert alert-success">✅ Attendance record deleted successfully!</div>';
    } else {
        $statusMsg = '<div class="alert alert-danger">❌ Error deleting attendance: ' . htmlspecialchars(mysqli_error($conn)) . '</div>';
    }
}

// ✅ Fetch combined attendance records with time_in, time_out, and status
$attendanceQuery = "
    SELECT a.id AS attendance_id, a.user_id, a.status, a.time_in, a.time_out,
           s.firstname AS student_firstname, s.lastname AS student_lastname, s.year, s.section, s.address AS student_address, s.user_type AS student_type,
           r.firstname AS regular_firstname, r.lastname AS regular_lastname, r.address AS regular_address, r.user_type AS regular_type
    FROM tbl_attendance a
    LEFT JOIN tbl_students s ON a.user_id = s.id
    LEFT JOIN tbl_regulars r ON a.user_id = r.id
    ORDER BY a.time_in DESC
";
$attendanceResult = mysqli_query($conn, $attendanceQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "partials/head.php";?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .action-icons i {
      cursor: pointer;
      font-size: 1.2rem;
      margin: 0 5px;
    }
    .action-icons i.delete { color: #dc3545; }

    /* Optional: improve badge styles */
    .badge-status {
      font-size: 0.85rem;
      padding: 0.4em 0.7em;
      border-radius: 0.35rem;
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
          <div class="row">
            <div class="col-12 grid-margin stretch-card">
              <div class="card shadow-sm rounded-3">
                <div class="card-body">
                  <h4 class="card-title">Attendance Records</h4>
                  <div class="table-responsive">
                    <table id="attendanceTable" class="table table-hover">
                      <thead>
                        <tr>
                          <th>Full Name</th>
                          <th>Grade/Year</th>
                          <th>Section/Course</th>
                          <th>Address</th>
                          <th>User Type</th>
                          <th>Status</th>
                          <th>Time In</th>
                          <th>Time Out</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if ($attendanceResult && mysqli_num_rows($attendanceResult) > 0): ?>
                          <?php while ($row = mysqli_fetch_assoc($attendanceResult)): ?>
                            <?php
                                // Determine if user is student or regular
                                if ($row['student_firstname']) {
                                    $fullname = $row['student_firstname'] . " " . $row['student_lastname'];
                                    $year = $row['year'] ?? '-';
                                    $section = $row['section'] ?? '-';
                                    $address = $row['student_address'];
                                    $user_type = $row['student_type'];
                                } else {
                                    $fullname = $row['regular_firstname'] . " " . $row['regular_lastname'];
                                    $year = '-';
                                    $section = '-';
                                    $address = $row['regular_address'];
                                    $user_type = $row['regular_type'];
                                }

                                // Determine badge color for status
                                if (strtolower($row['status']) === 'in') {
                                    $statusBadge = '<span class="badge bg-success badge-status">In</span>';
                                } elseif (strtolower($row['status']) === 'out') {
                                    $statusBadge = '<span class="badge bg-danger badge-status">Out</span>';
                                } else {
                                    $statusBadge = '<span class="badge bg-secondary badge-status">' . htmlspecialchars($row['status']) . '</span>';
                                }
                            ?>
                            <tr>
                              <td><?= htmlspecialchars($fullname); ?></td>
                              <td><?= htmlspecialchars($year); ?></td>
                              <td><?= htmlspecialchars($section); ?></td>
                              <td><?= htmlspecialchars($address); ?></td>
                              <td><?= htmlspecialchars($user_type); ?></td>
                              <td><?= $statusBadge; ?></td>
                              <td><?= htmlspecialchars($row['time_in']); ?></td>
                              <td><?= htmlspecialchars($row['time_out']); ?></td>
                              <td class="action-icons">
                                <a href="?delete_id=<?= $row['attendance_id']; ?>&type=attendance" 
                                   onclick="return confirm('Are you sure you want to delete this attendance record?');" 
                                   title="Delete">
                                  <i class="fa-solid fa-trash delete"></i>
                                </a>
                              </td>
                            </tr>
                          <?php endwhile; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="9" class="text-center">No attendance records found.</td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
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

  <!-- JS Scripts -->
  <script src="static/vendors/js/vendor.bundle.base.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="static/vendors/datatables.net/jquery.dataTables.js"></script>
  <script src="static/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
  <script>
    $(document).ready(function () {
        $('#attendanceTable').DataTable({
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
