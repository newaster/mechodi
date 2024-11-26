<?php
$page = 20;
include './layouts/header.php';

if (isset($_POST['submit'])) {

    $cumulative = ( isset($_POST['cummulative']) && $_POST['cummulative'] != "" ) ? $_POST['cummulative'] : "";

    if ($cumulative == "") {
        $session->msg("d", "Unable to set parameter.");
        redirect('set_dashboard_param.php');
    }

    $updateParam = "Update login set param = '$cumulative' where id = '" . $_SESSION['user_id'] . "' order by id desc LIMIT 1";

    $db->query($updateParam);
    $_SESSION['cumulative'] = $cumulative;
    if ($db->affected_rows() > 0) {
        $session->msg("s", "Parameter set successfully.");
        redirect('set_dashboard_param.php');
    } else {
        $session->msg("d", "Unable to set parameter.");
        redirect('set_dashboard_param.php');
    }
}

if( $_SESSION['role'] != "guest" ){

$selectRec = "Select embark_data.COLNAME from embark_data INNER JOIN sites on sites.number = embark_data.number where   LENGTH(COLNAME) > 35 and sites.status = 'active'  and  sites.created_by = '{$_SESSION['user_id']}' order by embark_data.id desc Limit 1";
}else{
    $updated_by = $_SESSION['user_admin'];
    $selectRec = "Select embark_data.COLNAME from embark_data INNER JOIN sites on sites.number = embark_data.number where   LENGTH(COLNAME) > 35 and sites.status = 'active'  and  sites.created_by = '{$updated_by}' order by embark_data.id desc Limit 1";

}

$resLast = find_by_sql($selectRec);
 
$resLast = $resLast[0];
$arrAllHeaders = array();
$arrHeaders = array();
$arrHeaders = explode(",", $resLast["COLNAME"]);

$arrMBR = $arrHeaders;

$arrNoHeader = array('Number', 'Site', 'Device', 'Date', 'Time', 'Status');


$arrMBR = array_diff($arrMBR, $arrNoHeader);

$selectHeaders = "Select * from header_map where header IN ( '".implode("','", $arrMBR)."' )  and type='cumulative' ";

$resHeaderMap = find_by_sql($selectHeaders);
$arrHeaders = array();
foreach ( $resHeaderMap as $keyHeader => $valHeaders ){
    $arrHeaders[] = $valHeaders['header'];
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
                    <li class="breadcrumb-item active">SET DASHBOARD PARAMETER</li>
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
                        <h2><strong><li class="zmdi zmdi-assignment-account">&nbsp;</li>SET</strong> DASHBOARD PARAMETER</h2>
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
                                    <div class="col-lg-6 col-md-6">
                                        <p> <b>Select Cumulative Header</b> </p>

                                        <select  id="cummulative"   required class="form-control show-tick ms select3 form-widget"  name="cummulative"  data-placeholder="Select" >
                                            <option value="">Select Cumulative </option>
                                            <?php foreach ($arrHeaders as $keyMBR => $valMBR) { ?>
                                                <option <?php echo ( isset($_SESSION['cumulative']) && $valMBR == $_SESSION['cumulative'] ) ? "selected='selected'" : ""; ?>  value="<?php echo $valMBR; ?>"><?php echo strtoupper($valMBR); ?></option>
                                            <?php } ?> 
                                        </select>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-lg-6 col-md-6">
                                        <button name="submit" type="submit" id="header_set" class="btn btn-primary pull-right">Set Header</button>
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