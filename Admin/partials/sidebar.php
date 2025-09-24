<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  .arrow-icon {
  transition: transform 0.3s ease; /* smooth rotation */
}
.nav-link[aria-expanded="true"] .arrow-icon {
  transform: rotate(90deg); /* rotate > to v */
}
</style>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="index.php">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>


    


          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#manage-student" aria-expanded="false" aria-controls="manage-student">
              <i class="fa-solid fa-users menu-icon"></i>
              <span class="menu-title">Manage Student</span>
              &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa-solid fa-angle-right arrow-icon"></i>
            </a>
            <div class="collapse" id="manage-student">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="add_student.php">Add Borrower</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="table_student.php">User Table</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="attend_table.php">Attendance Table</a>
                </li>
              </ul>
            </div>
          </li>


          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#manage-books" aria-expanded="false" aria-controls="manage-books">
              <i class="fa-solid fa-book menu-icon"></i>
              <span class="menu-title">Manage Books</span>
              &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa-solid fa-angle-right arrow-icon"></i>
            </a>
            <div class="collapse" id="manage-books">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="add_books.php">Add Books</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="table_book.php">Table</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="loan_book.php">Borrow Book</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="return_book.php">Return Book</a>
                </li>
              </ul>
            </div>
          </li>


      
        </ul>
</nav>