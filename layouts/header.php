<?php 
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
<link rel="icon" href="./assets/images/embfavicon.png" type="image/x-icon"> <!-- Favicon-->
<link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.min.css">
<!-- Custom Css -->
<link rel="stylesheet" href="assets/css/style.min.css">
<link rel="stylesheet" href="assets/plugins/jvectormap/jquery-jvectormap-2.0.5.css"/>
<link rel="stylesheet" href="assets/plugins/charts-c3/plugin.css"/>

<link rel="stylesheet" href="assets/plugins/morrisjs/morris.min.css" />
<link rel="stylesheet" href="assets/plugins/footable-bootstrap/css/footable.bootstrap.min.css">
<link rel="stylesheet" href="assets/plugins/footable-bootstrap/css/footable.standalone.min.css">

<!-- Bootstrap Select Css -->
<link rel="stylesheet" href="assets/plugins/bootstrap-select/css/bootstrap-select.css" />

<!-- Select2 -->
<link rel="stylesheet" href="assets/plugins/select2/select2.css" />
<!-- Multi Select Css -->
<link rel="stylesheet" href="assets/plugins/multi-select/css/multi-select.css">

<!-- JQuery DataTable Css -->
<link rel="stylesheet" href="assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css">

<style>
    
    .footable-sortable{
        text-align: center;
    }
    
    
    #DataTables_Table_0_wrapper .row{
        margin-right: 0px;
    }
    
    th,td{
        color:#777;
    }
    
    
</style>


</head>

<body class="theme-green">


<!-- Page Loader -->
<div class="page-loader-wrapper">
    <div class="loader">
        <div class="m-t-30"><img class="zmdi-hc-spin" src="assets/images/loader.svg" width="48" height="48" alt="Aero"></div>
        <p>Please wait...</p>
    </div>
</div>

<!-- Overlay For Sidebars -->
<div class="overlay"></div>

<!-- Main Search -->
<div id="search">
    <button id="close" type="button" class="close btn btn-primary btn-icon btn-icon-mini btn-round">x</button>
    <form>
      <input type="search" value="" placeholder="Search..." />
      <button type="submit" class="btn btn-primary">Search</button>
    </form>
</div>



<?php


include 'admin_menu.php';
 

?>

<!-- Main Content -->
<!-   -->
