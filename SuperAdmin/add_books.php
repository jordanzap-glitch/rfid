<?php
include '../includes/session.php';
include '../includes/dbcon.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form values and sanitize
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $genre = mysqli_real_escape_string($conn, $_POST['genre']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $date_created = date("Y-m-d H:i:s");

    // Insert into database
    $query = "INSERT INTO tbl_books (title, description, genre, status, date_created) 
              VALUES ('$title', '$description', '$genre', '$status', '$date_created')";
    if (mysqli_query($conn, $query)) {
        $message = "<div class='alert alert-success'>✅ Book Added successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <?php include "partials/head.php";?>
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
            <div class="col-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Add Book</h4>
                  <p class="card-description">Fill out the form to add a new book</p>

                  <!-- Show success/error message -->
                  <?php if (!empty($message)) { echo $message; } ?>

                  <form class="forms-sample" method="POST" action="">
                    
                    <!-- Title -->
                    <div class="form-group">
                      <label for="title">Book Title</label>
                      <input type="text" class="form-control" id="title" name="title" placeholder="Enter book title" required>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                      <label for="description">Description</label>
                      <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter description" required></textarea>
                    </div>

                    <!-- Genre -->
                    <div class="form-group">
                      <label for="genre">Genre</label>
                      <select class="form-control" id="genre" name="genre" required>
                        <option value="">-- Select Genre --</option>
                        <option value="BSIS COLLEGE">BSIS COLLEGE</option>
                        <option value="COLLEGE ENTREP">COLLEGE ENTREP</option>
                        <option value="COLLEGE FOREIGN">COLLEGE FOREIGN</option>
                        <option value="TAGALOG COLLEGE">TAGALOG COLLEGE</option>
                        <option value="FILIPINIANA COLLEGE">FILIPINIANA COLLEGE</option>
                        <option value="FILIPINO-TAGALOG HIGH SCHOOL">FILIPINO-TAGALOG HIGH SCHOOL</option>
                        <option value="FILIPINIANA HIGH SCHOOL">FILIPINIANA HIGH SCHOOL</option>
                        <option value="FORIEGN HIGH SCHOOL">FORIEGN HIGH SCHOOL</option>
                        <option value="KAPAMPANGAN SHS">KAPAMPANGAN SHS</option>
                        <option value="SHS FOREIGN">SHS FOREIGN</option>
                      </select>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                      <label for="status">Status</label>
                      <select class="form-control" id="status" name="status" required>
                        <option value="">-- Select Status --</option>
                        <option value="available">Available</option>
                        <option value="not available">Not Available</option>
                      </select>
                    </div>

                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                    <button type="reset" class="btn btn-light">Cancel</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
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
  <script src="static/vendors/datatables.net/jquery.dataTables.js"></script>
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
  <!-- End custom js for this page-->
</body>

</html>
