<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php");  //include connection file
error_reporting(0);  // using to hide undefine undex errors
session_start(); //start temp session until logout/browser closed

?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>Feedback</title>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <!-- <link href="css/animsition.min.css" rel="stylesheet"> -->
    <link href="css/animate.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet"> 
</head>

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
									
                                     echo  '<li class="nav-item"><a href="your_orders.php" class="nav-link active">My Orders</a> </li>
                                    <li class="nav-item"> <a class="nav-link active" href="user_profile.php">Profile<span class="sr-only"></span></a> </li>'; 
                                    echo  '<li class="nav-item"> <a class="nav-link active" href="restaurant.php">Restaurant<span class="sr-only"></span></a> </li>';
									echo  '<li class="nav-item"><a href="logout.php" class="nav-link active">Logout</a> </li>';
                            }
                            
                            // Sending message to db

                            if(isset($_POST['submit']))           //if upload btn is pressed
{
		if(empty($_POST['name'])||empty($_POST['email'])||$_POST['message']=='')
		{	
											$error = 	'<div class="alert alert-danger alert-dismissible fade show">
																<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
																<strong>All fields Must be Fillup!</strong>
															</div>';
									
		
								
		}
									else
										{
												$sql = "INSERT INTO contact(name, email, message) VALUE('".$_POST['name']."','".$_POST['email']."','".$_POST['message']."')";  // store the submited data ino the database :images
												mysqli_query($db, $sql); 
												move_uploaded_file($temp, $store);
			  
													$success = 	'<div class="alert alert-success alert-dismissible fade show">
																<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
																<strong>Congrass!</strong> Message sent successfully.
															</div>';
										}
					}
                        ?>
                        
							 
                        </ul>
						 
                    </div>
                </div>
            </nav>
            <!-- /.navbar -->
        </header>
        <!-- banner part starts -->
        <section class="hero bg-image" data-image-src="images/img/bg.jpg">
            <div class="hero-inner">
                <div class="container text-center hero-text font-white">
                    <h1>Feedback</h1>
                    <!-- end:Steps -->
                </div>
            </div>
            <!--end:Hero inner -->
        </section>
        <!-- banner part ends -->
    <!-- Contact us form -->
      <!-- Popular block starts -->
      <br><br><br>
                    <!-- <p class="lead">The easiest way to your favourite food</p> -->
                <hr>
               <div class="container">
                  <div class="row">
                     <!-- REGISTER -->
                     <div class="col-md-8">
                        <div class="widget">
                           <div class="widget-body">
							  <form action="" method="post">
                                 
                                 <div class="row">
								  <div class="form-group col-sm-6">
                                  <label>Full Name</label>
                                       <input class="form-control" type="text" name="name" id="example-text-input" placeholder="Raj Sharma"> 
                                    </div>
                        </div>
                        <div class="row">
                                    <div class="form-group col-sm-6">
                                    <label>Email</label>
                                       <input type="text" class="form-control" name="email" id="exampleInputPassword1" placeholder="raj@gmail.com"> 
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label>Your Feedback</label>
                                       <input type="text" class="form-control" name="message" id="exampleInputPassword1" placeholder="I want to know..."> 
                                    </div>
                                 </div>
                                
                                 <div class="row">
                                    <div class="col-sm-4">
                                       <p> <input type="submit" value="Send" name="submit" class="btn theme-btn"> </p>
                                    </div>
                              </form>
                        </div>
				    </div>
                           <!-- end: Widget -->
                </div>
                        <!-- /REGISTER -->
                     </div>
                     <!-- WHY? -->
                    
                     <!-- /WHY? -->
                  </div>
               </div>
            </section>
                                <hr>
                                <br><br><br>
      <?php include "include/footer.php" ?>
    
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
