<?php
$page = 22;
include './layouts/header.php';


$allNumbers = find_by_sql("Select recharge_numbers from login where id =  '" . $_SESSION['user_id'] . "'  ");
 

$allNumbers = $allNumbers[0];

$recharge_numbers = $allNumbers['recharge_numbers'];
  

if (isset($_POST['submit'])) {

    $numbers = ( isset($_POST['numbers']) && $_POST['numbers'] != "" ) ? $_POST['numbers'] : "";

    if ($numbers == "") {
        $session->msg("d", "Unable to set recharge numbers.");
        redirect('set_recharge_numbers.php');
    }
    
    $arrNumbers = explode(",", $numbers);
    $arrNumbers = array_map('trim', $arrNumbers);
    
    $allNumbers = implode(",", $arrNumbers);

     $updateParam = "Update login set recharge_numbers = '$allNumbers' where id = '" . $_SESSION['user_id'] . "' order by id desc LIMIT 1";
   
    $db->query($updateParam);
    
    if ($db->affected_rows() > 0) {
        $session->msg("s", "Recharge user number set successfully.");
        redirect('set_recharge_numbers.php');
    } else {
        $session->msg("d", "Unable to set Recharge user number.");
        redirect('set_recharge_numbers.php');
    }
}
 

?>

<style>
    .jvectormap-legend-tick{
        width: 50px !important;
    }

    .progress{
        background:#e47297;
        width:20px;
    }

</style>
<section class="content">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-7 col-md-6 col-sm-12">
                <h2>Embark Dashboard</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="main.php"><i class="zmdi zmdi-home"></i> Embark </a></li>
                    <li class="breadcrumb-item active">SET RECHARGE NUMBERS</li>
                </ul>
                <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-12">                
                <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            </div>
        </div>
    </div>
    <div class="container-fluid">

        <div class="row clearfix">
            <div class="col-md-12 col-lg-8">
                <div class="card">
                    <div class="header">
                        <h2><strong><li class="zmdi zmdi-assignment-account">&nbsp;</li>SET</strong> RECHARGE NUMBERS</h2>
                        <ul class="header-dropdown">

                        </ul>                        
                    </div>
                    <div class="body">
                        <div class="card">
                            <div class="header" > 

                                <div class="row"> 

                                    <div class="col-md-6">
                                        <p class="category">*All fields are Mandatory</p>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-12 card-content">   
                                <form  method="post"  >
                                    <div class="col-lg-8 col-md-8">
                                        <p> <b>Add comma separated numbers from atm recharge app users</b> </p>

                                         <textarea class="form-control" rows="10" id="numbers" name="numbers" placeholder="Recharge Numbers"><?php echo $recharge_numbers; ?></textarea>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-lg-12 col-md-12 ">
                                        <button name="submit" type="submit"   class="btn btn-primary pull-right">Set Numbers</button>
                                    </div>
                                    
                                </form>

                            </div>
                        </div>                          
                    </div>
                </div>
            </div> 
        </div> 
    </div>
</section>
<?php include './layouts/scripts.php'; ?> 


<?php include './layouts/footer.php'; ?>