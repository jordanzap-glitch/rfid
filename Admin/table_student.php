<?php
// Include database connection
include '../includes/session.php';
include '../includes/dbcon.php';

$statusMsg = "";

// ✅ Handle Delete Request for both tables
if (isset($_GET['delete_id']) && isset($_GET['type'])) {
    $deleteId = intval($_GET['delete_id']);
    $type = $_GET['type'] === 'regular' ? 'tbl_regulars' : 'tbl_students';
    $deleteQuery = "DELETE FROM $type WHERE id = $deleteId";
    if (mysqli_query($conn, $deleteQuery)) {
        $statusMsg = '<div class="alert alert-success">✅ User deleted successfully!</div>';
    } else {
        $statusMsg = '<div class="alert alert-danger">❌ Error deleting user: ' . htmlspecialchars(mysqli_error($conn)) . '</div>';
    }
}

// ✅ Handle Edit/Update Request for tbl_students
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id']) && isset($_POST['edit_type']) && $_POST['edit_type'] === 'student') {
    $editId    = intval($_POST['edit_id']);
    $uid       = mysqli_real_escape_string($conn, $_POST['uid']);
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname  = mysqli_real_escape_string($conn, $_POST['lastname']);
    $year      = mysqli_real_escape_string($conn, $_POST['year']);
    $section   = mysqli_real_escape_string($conn, $_POST['section']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $address   = mysqli_real_escape_string($conn, $_POST['address']);

    $updateQuery = "UPDATE tbl_students 
                    SET uid='$uid', firstname='$firstname', lastname='$lastname', year='$year', section='$section', 
                        email='$email', address='$address' 
                    WHERE id=$editId";

    if (mysqli_query($conn, $updateQuery)) {
        $statusMsg = '<div class="alert alert-success">✅ Student updated successfully!</div>';
    } else {
        $statusMsg = '<div class="alert alert-danger">❌ Error updating student: ' . htmlspecialchars(mysqli_error($conn)) . '</div>';
    }
}

// ✅ Handle Edit/Update Request for tbl_regulars
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id']) && isset($_POST['edit_type']) && $_POST['edit_type'] === 'regular') {
    $editId    = intval($_POST['edit_id']);
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname  = mysqli_real_escape_string($conn, $_POST['lastname']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $address   = mysqli_real_escape_string($conn, $_POST['address']);

    $updateQuery = "UPDATE tbl_regulars 
                    SET firstname='$firstname', lastname='$lastname', email='$email', address='$address'
                    WHERE id=$editId";

    if (mysqli_query($conn, $updateQuery)) {
        $statusMsg = '<div class="alert alert-success">✅ Regular user updated successfully!</div>';
    } else {
        $statusMsg = '<div class="alert alert-danger">❌ Error updating regular user: ' . htmlspecialchars(mysqli_error($conn)) . '</div>';
    }
}

// ✅ Fetch combined records from tbl_students and tbl_regulars
$studentQuery = "
    SELECT id, uid, firstname, lastname, year, section, email, address, date_created, user_type, 'student' AS source
    FROM tbl_students
    UNION ALL
    SELECT id, uid, firstname, lastname, NULL AS year, NULL AS section, email, address, date_created, user_type, 'regular' AS source
    FROM tbl_regulars
    ORDER BY date_created DESC
";
$studentResult = mysqli_query($conn, $studentQuery);
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
    .action-icons i.edit { color: #007bff; }
    .action-icons i.delete { color: #dc3545; }
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
                  <h4 class="card-title">User Records</h4>
                  <div class="table-responsive">
                    <table id="studentTable" class="table table-hover">
                      <thead>
                        <tr>
                          <th>UID</th>
                          <th>Full Name</th>
                          <th>Grade/Year</th>
                          <th>Section/Course</th>
                          <th>Email</th>
                          <th>Address</th>
                          <th>Date Created</th>
                          <th>User Type</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if ($studentResult && mysqli_num_rows($studentResult) > 0): ?>
                          <?php while ($row = mysqli_fetch_assoc($studentResult)): ?>
                            <tr>
                              <td><?= htmlspecialchars($row['uid']); ?></td>
                              <td><?= htmlspecialchars($row['firstname'] . " " . $row['lastname']); ?></td>
                              <td><?= htmlspecialchars($row['year'] ?? '-'); ?></td>
                              <td><?= htmlspecialchars($row['section'] ?? '-'); ?></td>
                              <td><?= htmlspecialchars($row['email']); ?></td>
                              <td><?= htmlspecialchars($row['address']); ?></td>
                              <td><?= htmlspecialchars($row['date_created']); ?></td>
                              <td><?= htmlspecialchars($row['user_type']); ?></td>
                              <td class="action-icons">
                                <?php if ($row['source'] === 'student'): ?>
                                  <i class="fa-solid fa-pencil edit-student" 
                                    data-id="<?= $row['id']; ?>"
                                    data-uid="<?= htmlspecialchars($row['uid']); ?>"
                                    data-firstname="<?= htmlspecialchars($row['firstname']); ?>"
                                    data-lastname="<?= htmlspecialchars($row['lastname']); ?>"
                                    data-year="<?= htmlspecialchars($row['year']); ?>"
                                    data-section="<?= htmlspecialchars($row['section']); ?>"
                                    data-email="<?= htmlspecialchars($row['email']); ?>"
                                    data-address="<?= htmlspecialchars($row['address']); ?>"
                                    title="Edit Student"></i>
                                <?php else: ?>
                                  <i class="fa-solid fa-pencil edit-regular" 
                                    data-id="<?= $row['id']; ?>"
                                    data-firstname="<?= htmlspecialchars($row['firstname']); ?>"
                                    data-lastname="<?= htmlspecialchars($row['lastname']); ?>"
                                    data-email="<?= htmlspecialchars($row['email']); ?>"
                                    data-address="<?= htmlspecialchars($row['address']); ?>"
                                    title="Edit Regular"></i>
                                <?php endif; ?>
                               
                              </td>
                            </tr>
                          <?php endwhile; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="9" class="text-center">No User records found.</td>
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

  <!-- Edit Student Modal -->
  <div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="">
          <input type="hidden" name="edit_type" value="student">
          <div class="modal-header">
            <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="edit_id" id="edit_id">
            <div class="mb-3">
              <label for="uid" class="form-label">UID</label>
              <input type="text" class="form-control" name="uid" id="uid" readonly>
            </div>
            <div class="mb-3">
              <label for="firstname" class="form-label">First Name</label>
              <input type="text" class="form-control" name="firstname" id="firstname" required>
            </div>
            <div class="mb-3">
              <label for="lastname" class="form-label">Last Name</label>
              <input type="text" class="form-control" name="lastname" id="lastname" required>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="year" class="form-label">Grade/Year</label>
                <input type="text" class="form-control" name="year" id="year" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="section" class="form-label">Section/Course</label>
                <input type="text" class="form-control" name="section" id="section" required>
              </div>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" name="email" id="email" required>
            </div>
            <div class="mb-3">
              <label for="address" class="form-label">Address</label>
              <input type="text" class="form-control" name="address" id="address" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Student</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Regular User Modal -->
  <div class="modal fade" id="editRegularModal" tabindex="-1" aria-labelledby="editRegularModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="">
          <input type="hidden" name="edit_type" value="regular">
          <div class="modal-header">
            <h5 class="modal-title" id="editRegularModalLabel">Edit Regular User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="edit_id" id="reg_edit_id">
            <div class="mb-3">
              <label for="reg_firstname" class="form-label">First Name</label>
              <input type="text" class="form-control" name="firstname" id="reg_firstname" required>
            </div>
            <div class="mb-3">
              <label for="reg_lastname" class="form-label">Last Name</label>
              <input type="text" class="form-control" name="lastname" id="reg_lastname" required>
            </div>
            <div class="mb-3">
              <label for="reg_email" class="form-label">Email</label>
              <input type="email" class="form-control" name="email" id="reg_email" required>
            </div>
            <div class="mb-3">
              <label for="reg_address" class="form-label">Address</label>
              <input type="text" class="form-control" name="address" id="reg_address" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update User</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- JS Scripts -->
  <script src="static/vendors/js/vendor.bundle.base.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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

  <!-- DataTable + Modal Script -->
  <script>
    $(document).ready(function () {
        $('#studentTable').DataTable({
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50, 100],
            "ordering": false,
            "searching": true,
            "scrollY": "400px",
            "scrollCollapse": true,
            "paging": true
        });

        // Fill modal for student
        $(".edit-student").click(function() {
            $("#edit_id").val($(this).data("id"));
            $("#uid").val($(this).data("uid"));
            $("#firstname").val($(this).data("firstname"));
            $("#lastname").val($(this).data("lastname"));
            $("#year").val($(this).data("year"));
            $("#section").val($(this).data("section"));
            $("#email").val($(this).data("email"));
            $("#address").val($(this).data("address"));
            var modal = new bootstrap.Modal(document.getElementById('editStudentModal'));
            modal.show();
        });

        // Fill modal for regular
        $(".edit-regular").click(function() {
            $("#reg_edit_id").val($(this).data("id"));
            $("#reg_firstname").val($(this).data("firstname"));
            $("#reg_lastname").val($(this).data("lastname"));
            $("#reg_email").val($(this).data("email"));
            $("#reg_address").val($(this).data("address"));
            var modal = new bootstrap.Modal(document.getElementById('editRegularModal'));
            modal.show();
        });
    });
  </script>
</body>
</html>
