<?php
$page = 1;
$pageval = "index";
include './includes/load.php';
?>

<!doctype html>
<html class="no-js " lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="description" content="Responsive Bootstrap 4 and web Application ui kit.">

        <title>Mechmann-Odisha</title>
        <!-- Favicon-->
        <link rel="icon" href="favicon.ico" type="image/x-icon">
        <!-- Custom Css -->
        <link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/style.min.css">
        
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#000000">
    </head>

    <?php
    $message = "";

    if (isset($_POST['submit'])) {

        $username = ( isset($_POST['username']) && "" != $_POST['username'] ) ? remove_junk($db->escape($_POST['username'])) : "";
        $password = ( isset($_POST['password']) && "" != ($_POST['password'])) ? sha1($_POST['password']) : "";
        $remoteAddr = $_SERVER['REMOTE_ADDR'];

        if ($username != "" && $password != "") {

            $selectRec = "Select * from login where ( email = '" . $username . "' or uname = '" . $username . "' ) and password = '" . $password . "'  and status = 'active'  ";
            $arrUser = find_by_sql($selectRec);
            
            if (sizeof($arrUser) > 0 && $arrUser[0]['status'] === "active") {
                
                
                $sqlLastLogin = find_by_sql("Select * from login_history where username = '$username' ");
    	         
    	        if( sizeof( $sqlLastLogin ) >= 5 ){
    	            $sqlLastLogin = $sqlLastLogin[0];
    	            $delid = $sqlLastLogin['id'];
    	           // $del = "Delete from login_history where id = $delid Limit 1 ";
    	            delete_by_id('login_history' , (int) $delid );
    	        }
    	        
    	        $last_login = " INSERT INTO `login_history` (  `ip`, `login_date`, `username` ) VALUES ( '$remoteAddr' , '".date("Y-m-d H:i:s")."' , '$username' )";
    	        $db->query($last_login);
                

                $session->login($arrUser[0]);

                redirect("main.php");
                exit;
            } else if (sizeof($arrUser) > 0 && $arrUser[0]['status'] === "inactive") {
                $message = "Login Failed! Account Inactive!!!";
            } else {
                $message = "Login Failed! Please input valid username and password ";
            }
        } else {
            $message = "Please input valid username and password.";
        }
    }
    ?>

    <body class="theme-blush">

        <?php
        if ($message != "") {
            ?>
            <div id="alert_box" class="alert alert-info" >
                <span>
                    <?php
                    if ($message != "") {

                        echo $message;
                    }
                    ?>   

                </span>
            </div>
        <?php }
        ?>  


        <div class="authentication">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-sm-12">
                        <form class="card auth_form"  method="post" >
                            <div class="header">
                                <img class="logo" src="assets/images/Logo-1.png" alt="" style="width:100%;">
                                
                            </div>
                            <div class="body">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="username" placeholder="Username">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="zmdi zmdi-account-circle"></i></span>
                                    </div>
                                </div>

                                <div class="input-group mb-3">
                                    <input type="password" class="form-control" name="password" placeholder="Password">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="zmdi zmdi-lock"></i></span>
                                    </div>
                                </div>

                                <button  name="submit" type="submit" class="btn btn-primary btn-block waves-effect waves-light"  >SIGN IN</button>
                                <hr/>
                                <button id="installButton" class="btn btn-warning btn-block waves-effect waves-light" > Download APP </button>
                                <div class="signin_with mt-3">
                                    <!--<p class="mb-0">or  <a href="register.php" class="btn btn-primary btn-block waves-effect waves-light">Sign Up </a> </p>-->
        <!--                            <button class="btn btn-primary btn-icon btn-icon-mini btn-round facebook"><i class="zmdi zmdi-facebook"></i></button>
                                    <button class="btn btn-primary btn-icon btn-icon-mini btn-round twitter"><i class="zmdi zmdi-twitter"></i></button-->
                                    <a  target="_blank"  href="https://embarkrms.com/privacy_policy.php" class="btn btn-danger"><i class="zmdi zmdi-assignment-check"></i>Privacy Policy</a>
                                </div>
                            </div>
                        </form>
                        <div class="copyright text-center">
                            Aster Technologies and Controls LLP &copy; 
                            <script>document.write(new Date().getFullYear())</script>

                        </div>
                    </div>
                    <div class="col-lg-8 col-sm-12">
                        <div class="card">
                            <img src="assets/images/login.jpg"     alt="Sign In"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jquery Core Js -->
        <script src="assets/bundles/libscripts.bundle.js"></script>
        <script src="assets/bundles/vendorscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js -->

        <script>


                               $(document).ready(function () {

                                   $("#alert_box").fadeOut(5000);

                               });
                               
                               
                            let deferredPrompt; // Store the event so it can be triggered later
                            const installButton = document.getElementById('installButton');
                            
                            window.addEventListener('beforeinstallprompt', (e) => {
                              // Prevent the mini-infobar from appearing
                              e.preventDefault();
                              deferredPrompt = e; // Store the event
                              installButton.style.display = 'block'; // Show the install button
                            });
                            
                            installButton.addEventListener('click', async () => {
                              if (deferredPrompt) {
                                deferredPrompt.prompt(); // Show the prompt
                                const { outcome } = await deferredPrompt.userChoice; // Wait for the user to respond
                                if (outcome === 'accepted') {
                                  console.log('User accepted the install prompt');
                                } else {
                                  console.log('User dismissed the install prompt');
                                }
                                deferredPrompt = null; // Clear the prompt
                              }
                            });
                            
                            // Optional: Hide the button if the app is already installed
                            window.addEventListener('appinstalled', () => {
                              console.log('App successfully installed');
                              installButton.style.display = 'none';
                            });


        </script>
        
        <script>
            if ('serviceWorker' in navigator) {
              navigator.serviceWorker.register('/service-worker.js')
                .then(() => console.log('Service Worker registered!'))
                .catch((error) => console.error('Service Worker registration failed:', error));
            }
         </script>
    </body>
</html>