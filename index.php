<?php
include 'includes/dbcon.php';

// Fetch only 4 books
$books = [];
$sql = "SELECT b.id, b.title, b.description, b.status, b.genre_id, g.genre_name 
        FROM tbl_books b
        LEFT JOIN tbl_genre g ON b.genre_id = g.id
        ORDER BY b.id DESC
        LIMIT 4";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $desc = strip_tags($row['description']); 
        $words = explode(" ", $desc);
        if (count($words) > 20) {
            $desc = implode(" ", array_slice($words, 0, 20)) . "...";
        }
        $row['short_description'] = $desc;
        $books[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>SRC Library</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@700;800&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Spinner Start -->
    <?php include 'includes/spinner.php'; ?>
    <!-- Spinner End -->


    <!-- Topbar Start -->
    <?php include 'includes/topbar.php'; ?>
    <!-- Topbar End -->


    <!-- Navbar Start -->
    <?php include 'includes/navbar.php'; ?>
    <!-- Navbar End -->


    <!-- Carousel Start -->
    <div class="container-fluid p-0 mb-6 wow fadeIn" data-wow-delay="0.1s">
        <div id="header-carousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#header-carousel" data-bs-slide-to="0" class="active"
                    aria-current="true" aria-label="Slide 1">
                    <img class="img-fluid" src="img/movepic1.jpg" alt="Image">
                </button>
                <button type="button" data-bs-target="#header-carousel" data-bs-slide-to="1" aria-label="Slide 2">
                    <img class="img-fluid" src="img/movepic2.jpg" alt="Image">
                </button>
                <button type="button" data-bs-target="#header-carousel" data-bs-slide-to="2" aria-label="Slide 3">
                    <img class="img-fluid" src="img/movepic3.jpg" alt="Image">
                </button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="w-100" src="img/movepic1.jpg" alt="Image">
                    <div class="carousel-caption">
                        <h1 class="display-1 text-uppercase text-white mb-4 animated zoomIn">Welcome to Santa Rita College Library
                        </h1>
                        <a href="#" class="btn btn-primary py-3 px-4">Explore More</a>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="w-100" src="img/movepic2.jpg" alt="Image">
                    <div class="carousel-caption">
                        <h1 class="display-1 text-uppercase text-white mb-4 animated zoomIn">Welcome to Santa Rita College Library
                        </h1>
                        <a href="#" class="btn btn-primary py-3 px-4">Explore More</a>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="w-100" src="img/movepic3.jpg" alt="Image">
                    <div class="carousel-caption">
                        <h1 class="display-1 text-uppercase text-white mb-4 animated zoomIn">Welcome to Santa Rita College Library
                        </h1>
                        <a href="#" class="btn btn-primary py-3 px-4">Explore More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Carousel End -->


    <!-- About Start -->
    
    <!-- About End -->


    <!-- Features Start -->
     <div class="container-fluid pt-6 pb-6">
        <div class="container pt-4">
            <div class="row g-0 feature-row wow fadeIn" data-wow-delay="0.1s">
                <div class="col-md-6 col-lg-3 wow fadeIn" data-wow-delay="0.3s">
                    <div class="feature-item border h-100">
                        <div class="feature-icon btn-xxl-square bg-primary mb-4 mt-n4">
                            <i class="fa fa-hammer fa-2x text-white"></i>
                        </div>
                        <div class="p-5 pt-0">
                            <h5 class="text-uppercase mb-3">change</h5>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur tellus augue.</p>
                            <a class="position-relative text-body text-uppercase small d-flex justify-content-between"
                                href="#"><b class="bg-white pe-3">Read More</b> <i
                                    class="bi bi-arrow-right bg-white ps-3"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 wow fadeIn" data-wow-delay="0.4s">
                    <div class="feature-item border h-100">
                        <div class="feature-icon btn-xxl-square bg-primary mb-4 mt-n4">
                            <i class="fa fa-dollar-sign fa-2x text-white"></i>
                        </div>
                        <div class="p-5 pt-0">
                            <h5 class="text-uppercase">change</h5>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur tellus augue.</p>
                            <a class="position-relative text-body text-uppercase small d-flex justify-content-between"
                                href="#"><b class="bg-white pe-3">Read More</b> <i
                                    class="bi bi-arrow-right bg-white ps-3"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 wow fadeIn" data-wow-delay="0.5s">
                    <div class="feature-item border h-100">
                        <div class="feature-icon btn-xxl-square bg-primary mb-4 mt-n4">
                            <i class="fa fa-check-double fa-2x text-white"></i>
                        </div>
                        <div class="p-5 pt-0">
                            <h5 class="text-uppercase">change</h5>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur tellus augue.</p>
                            <a class="position-relative text-body text-uppercase small d-flex justify-content-between"
                                href="#"><b class="bg-white pe-3">Read More</b> <i
                                    class="bi bi-arrow-right bg-white ps-3"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 wow fadeIn" data-wow-delay="0.6s">
                    <div class="feature-item border h-100">
                        <div class="feature-icon btn-xxl-square bg-primary mb-4 mt-n4">
                            <i class="fa fa-tools fa-2x text-white"></i>
                        </div>
                        <div class="p-5 pt-0">
                            <h5 class="text-uppercase">change</h5>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur tellus augue.</p>
                            <a class="position-relative text-body text-uppercase small d-flex justify-content-between"
                                href="#"><b class="bg-white pe-3">Read More</b> <i
                                    class="bi bi-arrow-right bg-white ps-3"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Features End -->
    <!-- restore soon -->

    <!-- Features Start -->
    
    <!-- Features End -->


    <!-- Service Start -->
    <div class="container-fluid service pt-6 pb-6">
        <div class="container">
            <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h1 class="display-6 text-uppercase mb-5">Library Books Collection</h1>
            </div>

            <!-- Books Grid (Only 4 Books) -->
            <div class="row g-4" id="booksContainer">
                <?php if (!empty($books)): ?>
                    <?php foreach ($books as $index => $book): ?>
                        <div class="col-lg-3 col-md-6 wow fadeInUp"
                             data-wow-delay="<?php echo (0.1 * ($index % 4 + 1)); ?>s">
                            <div class="service-item h-100">
                                <div class="service-inner pb-5">
                                    <div class="service-text px-5 pt-4">
                                        <h5 class="text-uppercase"><?php echo htmlspecialchars($book['title']); ?></h5>
                                        <p><?php echo htmlspecialchars($book['short_description']); ?></p>
                                        <p>
                                            <strong>Status: </strong>
                                            <?php if ($book['status'] === 'available'): ?>
                                                <span class="badge bg-success">Available</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Borrowed</span>
                                            <?php endif; ?>
                                        </p>
                                        <?php if (!empty($book['genre_name'])): ?>
                                            <p><strong>Genre:</strong> <?php echo htmlspecialchars($book['genre_name']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <button 
                                        class="btn btn-light px-3 read-more-btn align-self-start" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#bookModal"
                                        data-title="<?php echo htmlspecialchars($book['title']); ?>"
                                        data-description="<?php echo htmlspecialchars($book['description']); ?>"
                                        data-status="<?php echo htmlspecialchars($book['status']); ?>"
                                        data-genre="<?php echo htmlspecialchars($book['genre_name']); ?>">
                                        Read More<i class="bi bi-chevron-double-right ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">No books available at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Book Modal -->
    <div class="modal fade" id="bookModal" tabindex="-1" aria-labelledby="bookModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="bookModalLabel">Book Details</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <h3 id="modalTitle"></h3>
            <p id="modalDescription"></p>
            <p><strong>Status: </strong><span id="modalStatus"></span></p>
            <p><strong>Genre: </strong><span id="modalGenre"></span></p>
            <div class="library-note">
                📚 Note: To read the full content of this book, please visit the <strong>Santa Rita College of Pampanga Library</strong>.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Service End -->


    <!-- Appoinment Start -->

    <!-- Appoinment End -->


    <!-- Team Start -->
    <div class="container-fluid team pt-6 pb-6">
        <div class="container">
            <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h1 class="display-6 text-uppercase mb-5">Library Staff</h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="team-item">
                        <div class="position-relative overflow-hidden">
                            <img class="img-fluid w-100" src="change" alt="">
                            <div class="team-social">
                                <a class="btn btn-square btn-dark mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square btn-dark mx-1" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-square btn-dark mx-1" href=""><i class="fab fa-linkedin-in"></i></a>
                                <a class="btn btn-square btn-dark mx-1" href=""><i class="fab fa-youtube"></i></a>
                            </div>
                        </div>
                        <div class="text-center p-4">
                            <h5 class="mb-1">Marita G. Valerio, RL, MALIS</h5>
                            <span>Cosultant, Library Programs and Services</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="team-item">
                        <div class="position-relative overflow-hidden">
                            <img class="img-fluid w-100" src="Change" alt="">
                            <div class="team-social">
                                <a class="btn btn-square btn-dark mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square btn-dark mx-1" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-square btn-dark mx-1" href=""><i class="fab fa-linkedin-in"></i></a>
                                <a class="btn btn-square btn-dark mx-1" href=""><i class="fab fa-youtube"></i></a>
                            </div>
                        </div>
                        <div class="text-center p-4">
                            <h5 class="mb-1">Kurl Bryan L. Duque, LPT</h5>
                            <span>Assistant Librarian</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="team-item">
                        <div class="position-relative overflow-hidden">
                            <img class="img-fluid w-100" src="change" alt="">
                            <div class="team-social">
                                <a class="btn btn-square btn-dark mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square btn-dark mx-1" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-square btn-dark mx-1" href=""><i class="fab fa-linkedin-in"></i></a>
                                <a class="btn btn-square btn-dark mx-1" href=""><i class="fab fa-youtube"></i></a>
                            </div>
                        </div>
                        <div class="text-center p-4">
                            <h5 class="mb-1">Mr. John Dexter Garcia</h5>
                            <span>Library Technician</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.6s">
                    <div class="team-item">
                        <div class="position-relative overflow-hidden">
                            <img class="img-fluid w-100" src="change" alt="">
                            <div class="team-social">
                                <a class="btn btn-square btn-dark mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square btn-dark mx-1" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-square btn-dark mx-1" href=""><i class="fab fa-linkedin-in"></i></a>
                                <a class="btn btn-square btn-dark mx-1" href=""><i class="fab fa-youtube"></i></a>
                            </div>
                        </div>
                        <div class="text-center p-4">
                            <h5 class="mb-1">Ms. Diana Rose B. Ronquillo</h5>
                            <span>Library Staff</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Team End -->


    <!-- Testimonial Start -->
    
    <!-- Testimonial End -->


    <!-- Newsletter Start -->
    
    <!-- Newsletter Start -->


    <!-- Footer Start -->
   <?php include 'includes/footer.php'; ?>   
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i
            class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>

    <script>
document.addEventListener("keydown", function(event) {
    // Example: Ctrl + L will open login.php
    if (event.ctrlKey && event.key.toLowerCase() === "l") {
        window.location.href = "login.php";
    }
});
</script>

</body>

</html>