<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register & Book Table</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .navbar-brand span {
      color: red;
    }
    .container {
      margin-top: 60px;
    }
    .form-section {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .nav-link.btn {
      padding: 5px 12px !important;
      margin-left: 10px;
      font-size: 14px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <a class="navbar-brand" href="#">RESTRO <span>ZONE</span></a>
  <div class="collapse navbar-collapse">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
      <li class="nav-item"><a href="#" class="nav-link">Restaurants</a></li>
      <li class="nav-item"><a href="login.php" class="nav-link">Login</a></li>
      <li class="nav-item"><a href="register.php" class="nav-link">Register</a></li>
      <li class="nav-item">
        <a href="#book" class="nav-link btn btn-success text-white">Book Table</a>
      </li>
    </ul>
  </div>
</nav>

<!-- Register & Booking Form -->
<div class="container">
  <div class="row">
    <!-- Registration Form -->
    <div class="col-md-6">
      <div class="form-section">
        <h3>Register</h3>
        <form action="register_submit.php" method="post">
          <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary">Register</button>
        </form>
      </div>
    </div>

    <!-- Table Booking Form -->
    <div class="col-md-6" id="book">
      <div class="form-section">
        <h3>Book a Table</h3>
        <form action="book_table.php" method="post">
          <div class="form-group">
            <label for="fullname">Full Name</label>
            <input type="text" name="fullname" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="contact">Contact Number</label>
            <input type="tel" name="contact" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="date">Booking Date</label>
            <input type="date" name="date" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="time">Booking Time</label>
            <input type="time" name="time" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="people">Number of People</label>
            <input type="number" name="people" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-success">Book Now</button>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>
