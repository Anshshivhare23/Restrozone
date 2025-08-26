<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php");
session_start();
error_reporting(0);

// Fetch fresh user data for display
$user_id = intval($_SESSION['user_id']);
$query = mysqli_query($db, "SELECT * FROM users WHERE u_id = '$user_id'");
$user_data = mysqli_fetch_assoc($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    if (
        empty($_POST['f_name']) ||
        empty($_POST['l_name']) ||
        empty($_POST['email']) ||
        empty($_POST['phone']) ||
        empty($_POST['address'])
    ) {
        $message = "All fields must be filled!";
    } else {
        $f_name = mysqli_real_escape_string($db, $_POST['f_name']);
        $l_name = mysqli_real_escape_string($db, $_POST['l_name']);
        $email = mysqli_real_escape_string($db, $_POST['email']);
        $phone = mysqli_real_escape_string($db, $_POST['phone']);
        $address = mysqli_real_escape_string($db, $_POST['address']);

        $update = "UPDATE users SET f_name='$f_name', l_name='$l_name', email='$email', phone='$phone', address='$address' WHERE u_id='$user_id'";
        if (mysqli_query($db, $update)) {
            // Refresh data after update
            $_SESSION['f_name'] = $f_name;
            $_SESSION['l_name'] = $l_name;
            $_SESSION['email'] = $email;
            $_SESSION['phone'] = $phone;
            $_SESSION['address'] = $address;

            header("Location: profile.php");
            exit();
        } else {
            $message = "Failed to update profile.";
        }
    }
}
?>


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profile</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .main-body {
            padding: 15px;
        }

        .card {
            box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
            border: none;
            border-radius: .25rem;
        }

        .gutters-sm>.col, .gutters-sm>[class*=col-] {
            padding-right: 8px;
            padding-left: 8px;
        }

        .mb-3 {
            margin-bottom: 1rem !important;
        }

        .text-secondary input {
            width: 100%;
            border: none;
            background: #f8f9fa;
            padding: 5px 10px;
            border-radius: 5px;
        }

        input:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, .5);
        }
    </style>
</head>

<body class="home">
    <div class="site-wrapper animsition" data-animsition-in="fade-in" data-animsition-out="fade-out">
        <header id="header" class="header-scroll top-header headrom">
            <nav class="navbar navbar-dark">
                <div class="container">
                    <a class="navbar-brand" href="index.html">
                        <img class="img-rounded" src="images/221b-2.png" alt="">
                    </a>
                    <div class="collapse navbar-toggleable-md float-lg-right" id="mainNavbarCollapse">
                        <ul class="nav navbar-nav">
                            <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                            <li class="nav-item"><a class="nav-link active" href="your_orders.php">My Orders</a></li>
                            <li class="nav-item"><a class="nav-link active" href="user_profile.php">Profile</a></li>
                            <li class="nav-item"><a class="nav-link active" href="restaurant.php">Restaurant</a></li>
                            <li class="nav-item"><a class="nav-link active" href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <br><br><br><br><br>

        <!-- User profile start -->
        <form method="POST" action="">
            <div class="container">
                <div class="main-body">
                    <div class="row gutters-sm">
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex flex-column align-items-center text-center">
                                        <img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="Admin" class="rounded-circle" width="150">
                                        <div class="mt-3">
<h4><?php echo $user_data['f_name'] . ' ' . $user_data['l_name']; ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <?php if (!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>

                                    <!-- First Name -->
                                    <div class="row">
                                        <div class="col-sm-3"><h6 class="mb-0">First Name</h6></div>
                                        <div class="col-sm-9 text-secondary">
                                            <input name="f_name" value="<?php echo $user_data['f_name']; ?>">
                                        </div>
                                    </div>
                                    <hr>

                                    <!-- Last Name -->
                                    <div class="row">
                                        <div class="col-sm-3"><h6 class="mb-0">Last Name</h6></div>
                                        <div class="col-sm-9 text-secondary">
                                            <input name="l_name" value="<?php echo $user_data['l_name']; ?>">
                                        </div>
                                    </div>
                                    <hr>

                                    <!-- Email -->
                                    <div class="row">
                                        <div class="col-sm-3"><h6 class="mb-0">Email</h6></div>
                                        <div class="col-sm-9 text-secondary">
                                            <input name="email" value="<?php echo $user_data['email']; ?>">
                                        </div>
                                    </div>
                                    <hr>

                                    <!-- Phone -->
                                    <div class="row">
                                        <div class="col-sm-3"><h6 class="mb-0">Phone</h6></div>
                                        <div class="col-sm-9 text-secondary">
                                            <input name="phone" value="<?php echo $user_data['phone']; ?>">
                                        </div>
                                    </div>
                                    <hr>

                                    <!-- Address -->
                                    <div class="row">
                                        <div class="col-sm-3"><h6 class="mb-0">Address</h6></div>
                                        <div class="col-sm-9 text-secondary">
                                            <input name="address" value="<?php echo $user_data['address']; ?>">
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <input type="submit" name="submit" class="btn btn-success btn-block" onclick="return confirm('Confirm Changes?');" value="Save Changes">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <?php include "include/footer.php" ?>
    </div>

    <!-- Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/tether.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/animsition.min.js"></script>
    <script src="js/bootstrap-slider.min.js"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script src="js/headroom.js"></script>
    <script src="js/foodpicky.min.js"></script>
</body>
</html>
