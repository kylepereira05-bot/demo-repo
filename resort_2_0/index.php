<?php
session_start();
require 'db.php';

// 1. Fetch AVAILABLE rooms/cottages
try {
    $stmt = $pdo->query("SELECT * FROM accommodations WHERE status = 'Available' ORDER BY room_id DESC");
    $rooms = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_msg = "System is updating. Please try again later.";
}

// 2. Fetch REVIEWS for the Guestbook section
try {
    $rev_stmt = $pdo->query("SELECT r.*, c.fullname FROM reviews r 
                             JOIN customers c ON r.customer_id = c.customer_id 
                             ORDER BY r.created_at DESC LIMIT 6");
    $reviews = $rev_stmt->fetchAll();
} catch (PDOException $e) {
    $reviews = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nana's Place | Resort Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            scroll-behavior: smooth;
        }

        .navbar {
            background: #03045e !important;
            padding: 15px 0;
        }

        .hero-section {
            background: linear-gradient(rgba(3, 4, 94, 0.6), rgba(3, 4, 94, 0.6)), url('backpic.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            padding: 120px 0;
            text-align: center;
        }

        .room-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: 0.4s;
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .room-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .room-img {
            height: 230px;
            object-fit: cover;
        }

        /* Star Rating CSS */
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 5px;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            font-size: 1.5rem;
            color: #ddd;
            cursor: pointer;
            transition: 0.2s;
        }

        .star-rating label:hover,
        .star-rating label:hover~label,
        .star-rating input:checked~label {
            color: #ffc107;
        }

        footer {
            background: #020122;
            color: rgba(255, 255, 255, 0.7);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
                <img src="logo n.jpg" width="40" class="rounded-circle me-2 border border-2 border-info" onerror="this.src='https://via.placeholder.com/45'">
                NANA'S PLACE
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link px-3" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#rooms">Rooms</a></li>

                    <?php if (isset($_SESSION['role'])): ?>
                        <?php if ($_SESSION['role'] == 'customer'): ?>
                            <li class="nav-item">
                                <a class="nav-link text-warning px-3" href="customer_dashboard.php">My Bookings</a>
                            </li>
                        <?php elseif ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff'): ?>
                            <li class="nav-item">
                                <a class="nav-link text-info px-3" href="dashboard.php">Staff Panel</a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item ms-lg-3">
                            <a class="btn btn-outline-light rounded-pill px-4" href="logout.php">Logout</a>
                        </li>

                    <?php else: ?>
                        <li class="nav-item ms-lg-3">
                            <a href="login_page.php" class="btn btn-light rounded-pill px-4 text-primary fw-bold">
                                <i class="fas fa-sign-in-alt me-1"></i> Sign In
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
    </nav>

    <header class="hero-section">
        <div class="container">
            <h1 class="display-3 fw-bold mb-3">Escape to Paradise</h1>
            <p class="fs-5 mb-5 opacity-75">Experience the serene beauty of Nana's Place Resort.</p>
            <a href="#rooms" class="btn btn-info btn-lg rounded-pill px-5 fw-bold text-white shadow">Explore Now</a>
        </div>
    </header>

    <main class="container py-5" id="rooms">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark">Our Accommodations</h2>
            <div class="mx-auto bg-info rounded-pill mb-3" style="width: 60px; height: 4px;"></div>
        </div>

        <div class="row g-4">
            <?php foreach ($rooms as $room): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="room-card">
                        <img src="uploads/<?= $room['image_path'] ?: 'logo n.jpg' ?>" class="room-img w-100" onerror="this.src='logo n.jpg'">
                        <div class="p-4 d-flex flex-column" style="min-height: 220px;">
                            <h5 class="fw-bold mb-1"><?= htmlspecialchars($room['room_type']) ?></h5>
                            <p class="text-muted small mb-3"><?= htmlspecialchars(substr($room['description'], 0, 70)) ?>...</p>
                            <div class="mt-auto d-flex justify-content-between align-items-center border-top pt-3">
                                <span class="fw-bold text-primary fs-5">₱<?= number_format($room['price_per_night'], 2) ?></span>
                                <a href="booking_form.php?id=<?= $room['room_id'] ?>" class="btn btn-dark btn-sm px-4 rounded-pill">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <hr class="container opacity-25">

    <section class="container py-5" id="feedback">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold text-center mb-4">Guest Reviews</h4>

                        <?php if (isset($_SESSION['customer_id'])): ?>
                            <form action="submit_review.php" method="POST" class="bg-light p-3 rounded-3 mb-4 border">
                                <label class="small fw-bold text-muted mb-1">RATE US</label>
                                <div class="star-rating mb-3">
                                    <input type="radio" id="5-stars" name="rating" value="5" required /><label for="5-stars">&#9733;</label>
                                    <input type="radio" id="4-stars" name="rating" value="4" /><label for="4-stars">&#9733;</label>
                                    <input type="radio" id="3-stars" name="rating" value="3" /><label for="3-stars">&#9733;</label>
                                    <input type="radio" id="2-stars" name="rating" value="2" /><label for="2-stars">&#9733;</label>
                                    <input type="radio" id="1-star" name="rating" value="1" /><label for="1-star">&#9733;</label>
                                </div>
                                <textarea name="comment" class="form-control form-control-sm mb-2" rows="2" placeholder="Tell us about your stay..." required></textarea>
                                <button type="submit" class="btn btn-sm btn-primary px-4 rounded-pill w-100">Post Review</button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-light text-center border mb-4">
                                <small class="text-muted">Please <a href="login_page.php" class="fw-bold text-primary text-decoration-none">Login</a> to share your experience.</small>
                            </div>
                        <?php endif; ?>

                        <div class="review-list" style="max-height: 300px; overflow-y: auto;">
                            <?php if ($reviews): foreach ($reviews as $rev): ?>
                                    <div class="mb-3 border-bottom pb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold small"><?= htmlspecialchars($rev['fullname']) ?></span>
                                            <span class="text-warning small"><?= str_repeat('&#9733;', $rev['rating']) ?></span>
                                        </div>
                                        <p class="small text-muted mb-0">"<?= htmlspecialchars($rev['comment']) ?>"</p>
                                    </div>
                                <?php endforeach;
                            else: ?>
                                <p class="text-center text-muted small">No reviews yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 mt-5">
        <div class="container text-center">
            <p class="mb-1">&copy; <?= date('Y') ?> Nana's Place Resort. All Rights Reserved.</p>
            <p class="small text-white-50">Capstone Project - BSIS Student I-TECH College</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>