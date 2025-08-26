<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php");  //include connection file
error_reporting(0);  // using to hide undefine undex errors
session_start(); //start temp session until logout/browser closed ?
$user_id = $_SESSION['user_id'];
$query = mysqli_query($db, "SELECT * FROM users WHERE u_id = '$user_id'");
$user_data = mysqli_fetch_assoc($query);

?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>User Profile</title>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <!-- <link href="css/animsition.min.css" rel="stylesheet"> -->
    <link href="css/animate.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet"> </head>

    <style>
        .main-body {
            padding: 15px;
            background-color: #f8f9fa;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0,0,0,.1);
            border-radius: 10px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,.2);
        }
        .card-body {
            padding: 2rem;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border: 3px solid #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,.15);
            margin-bottom: 20px;
        }
        .profile-info h6 {
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .profile-info .text-secondary {
            color: #343a40 !important;
            font-size: 1.1rem;
        }
        hr {
            border-top: 1px solid rgba(0,0,0,.1);
            margin: 1.5rem 0;
        }
        .container {
            max-width: 1140px;
            margin-top: 2rem;
        }
        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem;
            }
        }
    </style>

<body class="home">
    <div class="site-wrapper animsition" data-animsition-in="fade-in" data-animsition-out="fade-out">
        <!--header starts-->
        <header id="header" class="header-scroll top-header headrom">
            <!-- .navbar -->
            <nav class="navbar navbar-dark">
                <div class="container">
                    <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#mainNavbarCollapse">&#9776;</button>
                    <a class="navbar-brand" href="index.html"> <img class="img-rounded" src="images/221b-2.png" alt=""> </a>
                    <div class="collapse navbar-toggleable-md  float-lg-right" id="mainNavbarCollapse">
                        <ul class="nav navbar-nav">
                            <li class="nav-item"> <a class="nav-link active" href="index.php">Home <span class="sr-only">(current)</span></a> </li>
                           
        


        
							<?php
						if(empty($_SESSION["user_id"])) // if user is not login
							{
								echo '<li class="nav-item"><a href="login.php" class="nav-link active">Login</a> </li>
                              <li class="nav-item"><a href="registration.php" class="nav-link active">Signup</a> </li>';
                             
							}
						else
							{
									//if user is login
									
                                    echo  '<li class="nav-item"><a href="your_orders.php" class="nav-link active">My Orders</a> </li>';
                                    echo  '<li class="nav-item"><a href="restaurants.php" class="nav-link active">Restaurants</a> </li>';
									echo  '<li class="nav-item"><a href="logout.php" class="nav-link active">Logout</a> </li>';
							}
                        ?>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- /.navbar -->
        </header>
        <!-- Popular block starts -->
        <!-- <section class="popular">
            <div class="container">
                <div class="title text-xs-center m-b-30">
                    <h2>User Profile</h2>
                </div>
                
            </div>
        </section> -->
        <br><br><br><br><br>
<!-- User profile start  -->

<div class="container">
    <div class="main-body">
            <div class="row gutters-sm">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center text-center">
                                <img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="User" class="rounded-circle profile-img">
                                <div class="mt-3">
                                    <h4><?php echo $user_data['f_name'] . ' ' . $user_data['l_name'];?></h4>
                                    <p class="text-secondary mb-1">Customer</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-body profile-info">
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6>Full Name</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?php echo $user_data['f_name'] . ' ' . $user_data['l_name'];?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6>Email</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?php echo $user_data['email'];?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6>Phone</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?php echo $user_data['phone'];?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6>Address</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?php echo $user_data['address'];?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include "include/footer.php" ?>
    
    <!--/end:Site wrapper -->
    <!-- Bootstrap core JavaScript
    ================================================== -->
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