<?php
// Include database connection
include '../includes/session.php';
include '../includes/dbcon.php';

$statusMsg = "";

// ✅ Handle Delete Request
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $deleteQuery = "DELETE FROM tbl_books WHERE id = $deleteId";
    if (mysqli_query($conn, $deleteQuery)) {
        $statusMsg = '<div class="alert alert-success">✅ Book deleted successfully!</div>';
    } else {
        $statusMsg = '<div class="alert alert-danger">❌ Error deleting book: ' . htmlspecialchars(mysqli_error($conn)) . '</div>';
    }
}

// ✅ Handle Edit/Update Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id'])) {
    $editId = intval($_POST['edit_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $genre_id = intval($_POST['genre']); // store genre_id instead of genre name

    $updateQuery = "UPDATE tbl_books 
                    SET title='$title', description='$description', status='$status', genre_id=$genre_id 
                    WHERE id=$editId";

    if (mysqli_query($conn, $updateQuery)) {
        $statusMsg = '<div class="alert alert-success">✅ Book updated successfully!</div>';
    } else {
        $statusMsg = '<div class="alert alert-danger">❌ Error updating book: ' . htmlspecialchars(mysqli_error($conn)) . '</div>';
    }
}

// ✅ Fetch book records with JOIN to get genre name
$bookQuery = "SELECT b.id, b.title, b.description, b.status, b.date_created, g.genre_name 
              FROM tbl_books b 
              LEFT JOIN tbl_genre g ON b.genre_id = g.id 
              ORDER BY b.date_created DESC";
$bookResult = mysqli_query($conn, $bookQuery);

// ✅ Fetch all genres for dropdown
$genreQuery = "SELECT id, genre_name FROM tbl_genre ORDER BY genre_name ASC";
$genreResult = mysqli_query($conn, $genreQuery);

// ✅ Helper function to shorten description
function shortenText($text, $limit = 10) {
    $words = explode(" ", $text);
    if (count($words) > $limit) {
        return implode(" ", array_slice($words, 0, $limit)) . " ...";
    }
    return $text;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <?php include "partials/head.php";?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .action-icons i {
      cursor: pointer;
      font-size: 1.2rem;
      margin: 0 5px;
    }
    .action-icons i.edit {
      color: #007bff;
    }
    .action-icons i.delete {
      color: #dc3545;
    }
    .badge-available {
      background-color: #28a745;
      color: white;
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.8rem;
    }
    .badge-unavailable {
      background-color: #dc3545;
      color: white;
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.8rem;
    }
  </style>
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
          <?php if ($statusMsg) echo $statusMsg; ?>
          <div class="row">
            <div class="col-12 grid-margin stretch-card">
              <div class="card shadow-sm rounded-3">
                <div class="card-body">
                  <h4 class="card-title">Book Records</h4>
                  <div class="table-responsive">
                    <table id="bookTable" class="table table-hover">
                      <thead>
                        <tr>
                          <th>Title</th>
                          <th>Description</th>
                          <th>Status</th>
                          <th>Genre</th>
                          <th>Date Created</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if ($bookResult && mysqli_num_rows($bookResult) > 0): ?>
                          <?php while ($row = mysqli_fetch_assoc($bookResult)): ?>
                            <tr>
                              <td><?php echo htmlspecialchars($row['title']); ?></td>
                              <td><?php echo htmlspecialchars(shortenText($row['description'], 10)); ?></td>
                              <td>
                                <?php if ($row['status'] == 'available'): ?>
                                  <span class="badge-available"><i class="fa-solid fa-check"></i> Available</span>
                                <?php else: ?>
                                  <span class="badge-unavailable"><i class="fa-solid fa-times"></i> Unavailable</span>
                                <?php endif; ?>
                              </td>
                              <td><?php echo htmlspecialchars($row['genre_name']); ?></td>
                              <td><?php echo htmlspecialchars($row['date_created']); ?></td>
                              <td class="action-icons">
                                <!-- Edit button -->
                                <i class="fa-solid fa-pencil edit" 
                                   data-id="<?php echo $row['id']; ?>"
                                   data-title="<?php echo htmlspecialchars($row['title']); ?>"
                                   data-description="<?php echo htmlspecialchars($row['description']); ?>"
                                   data-status="<?php echo htmlspecialchars($row['status']); ?>"
                                   data-genre="<?php echo htmlspecialchars($row['genre_name']); ?>"
                                   title="Edit"></i>

                                <!-- Delete button -->
                                <a href="?delete_id=<?php echo $row['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this book?');">
                                   <i class="fa-solid fa-trash delete" title="Delete"></i>
                                </a>
                              </td>
                            </tr>
                          <?php endwhile; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="6" class="text-center">No book records found.</td>
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

  <!-- Edit Book Modal -->
  <div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="">
          <div class="modal-header">
            <h5 class="modal-title" id="editBookModalLabel">Edit Book</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="edit_id" id="edit_id">
            <div class="mb-3">
              <label for="title" class="form-label">Title</label>
              <input type="text" class="form-control" name="title" id="title" required>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" name="description" id="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
              <label for="status" class="form-label">Status</label>
              <select class="form-control" name="status" id="status" required>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="genre" class="form-label">Genre</label>
              <select class="form-control" name="genre" id="genre" required>
                <?php if ($genreResult && mysqli_num_rows($genreResult) > 0): ?>
                  <?php while ($g = mysqli_fetch_assoc($genreResult)): ?>
                    <option value="<?php echo $g['id']; ?>">
                      <?php echo htmlspecialchars($g['genre_name']); ?>
                    </option>
                  <?php endwhile; ?>
                <?php else: ?>
                  <option value="">No genres available</option>
                <?php endif; ?>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Book</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- plugins:js -->
  <script src="static/vendors/js/vendor.bundle.base.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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

  <!-- DataTable + Modal Script -->
  <script>
    $(document).ready(function () {
        $('#bookTable').DataTable({
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50, 100],
            "ordering": false,
            "searching": true,
            "scrollY": "400px",
            "scrollCollapse": true,
            "paging": true
        });

        // Fill modal with book data
        $(".edit").click(function() {
            var id = $(this).data("id");
            var title = $(this).data("title");
            var description = $(this).data("description");
            var status = $(this).data("status");
            var genreName = $(this).data("genre");

            $("#edit_id").val(id);
            $("#title").val(title);
            $("#description").val(description);
            $("#status").val(status);

            // Set genre dropdown by matching text
            $("#genre option").each(function() {
                if ($(this).text() === genreName) {
                    $(this).prop("selected", true);
                }
            });

            var modal = new bootstrap.Modal(document.getElementById('editBookModal'));
            modal.show();
        });
    });
  </script>
</body>
</html>
