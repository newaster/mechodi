<?php

$page = 24;
include_once './layouts/header.php';

echo $city ;

if ($_SESSION['role'] == 'admin') {
    $query = "select number, name , datediff(sites.renewal_date, NOW()) as days from sites where status = 'active' order by id desc ";
} else if ($_SESSION['role'] == 'guest') {
    $query = "select sites.name , sites.number , datediff(sites.renewal_date, NOW()) as days from sites inner join zone on zone.number = sites.number where zone.unique_id in ('{$_SESSION['zone']}') and status = 'active' having days > 0 order by sites.id desc ";
} else {
    $query = "select number, name , datediff(sites.renewal_date, NOW()) as days from sites where created_by = '{$_SESSION['user_id']}' and status = 'active' having days > 0  order by id desc ";
}
$allNumbers = find_by_sql($query);
//$allNumbers = $allNumbers[0];

$siteNumbers = array();

foreach ($allNumbers as $keyNumber => $valNumber) {
    $siteNumbers[] = $valNumber['number'];
} 

if( sizeof($siteNumbers) == 0 ){
    $siteNumbers[] = 1;
}

$siteNumbers = array_slice($siteNumbers, 0,1000);


$fromdate = ( true == isset($_POST['fromdate']) && "" != $_POST['fromdate'] ) ? $_POST['fromdate'] : date("Y-M"); 
 
?>
<!--<center>-->
<link href="assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />
<style>

 .dtp-picker,.dtp-actual-month,.p80,.dtp-actual-day,.dtp-actual-num{
        display:none;
    }

</style>
<section class="content">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-7 col-md-6 col-sm-12">
                <h2>Embark Dashboard</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="main.php"><i class="zmdi zmdi-home"></i> Embark </a></li>
                    <li class="breadcrumb-item active">Manage Report</li>
                </ul>
                <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-12">                
                <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <?php echo display_msg($msg); ?>
        <div class="row">
            <div class="col-md-12"> 
                <div class="body">
                    <div class="card" > 
                        <h4 class="title">Report</h4>

                    </div>
                    <div class="card">

                        <form name="formplot" method="post" action="reports_card_month.php"   >
                            <div class="row">
                                 

                                <div class="col-md-5">
                                    <div class="form-group label">
                                       
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="control-label">Select Date</label>
                                                <input class="form-control datepicker"  readonly="readonly"  id="fromdate" type="text" name="fromdate"  value="<?php echo ( $fromdate == "" ) ?  date("Y-M") : $fromdate; ?>" id="fromdate">
                                            </div> 
                                           
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-2" >

                                    <div class="form-group label-floating">
                                        <label class="control-label">Click to Filter</label>
                                        <input type="submit" class="btn btn-primary pull-right" name="filterrecords" id="filterrecords" value="Filter Records" >
                                    </div>

                                </div>
                            </div>

                        </form>

                    </div>

                    <div class="body">  

                        <div class="table-responsive">
                            
                            <div class="col-md-12">
                           

                            <table  class="table table-bordered table-striped table-hover dataTable js-exportable"  >
                                <thead>
                                    <tr>
                                        <td>#</td>
                                        <td>Date</td>
                                        <td>Time From</td>
                                        <td>Time To</td>
                                        <td>Site</td>
                                        <td>Volume Dispensed Till Date</td>
                                        <td>Recharge in Month</td>
                                        <td>Volume in Month</td>
                                        <td>Usage 1's</td>
                                        <td>Usage 5's</td>
                                        <td>Usage 20's</td>
                                        <td>Total Collection</td>
                                        
                                    </tr>    
                                </thead>    
                                <tbody>
                                    <?php
                                    
                                        $month = date("m", strtotime($fromdate));
                                        $year = date("Y", strtotime($fromdate));
                                    
                                        $selectAll = "Select number , DATE(created_date) as todate, min(created_date) as min_date, max(created_date) as max_date, SUM(twenty) as twentys, SUM(ones) as ones , SUM(fives) as fives from CARD_USAGE_VIEW where number IN ( ".implode(",", array_values($siteNumbers)).") and  ( MONTH(created_date) = '$month' and YEAR(created_date) = '$year' )  GROUP BY NUMBER ";
                                        $resSites = find_by_sql($selectAll);
                                        
                                        $resNumbers = find_by_sql("Select recharge_numbers from login where  id = '" . $_SESSION['user_id'] . "' order by id desc LIMIT 1");
                                        
                                        $resNumbers = $resNumbers[0];
                                        $arrNumbers = "";
                                        if( $resNumbers['recharge_numbers'] != "" ){
                                            $arrNumbers = $resNumbers['recharge_numbers'];
                                        }else{
                                            $arrNumbers = "1";
                                        }
                                        
                                        $arrRecharge = array();
                                        
                                        $db->selectDB("dbembark");
                                        
                                        $select  = "Select SUM(rechargeval) as recharge, number from wateratmdata where number IN (  ".$arrNumbers." ) and ( MONTH(date) = '$month' and YEAR(date) = '$year' ) ";
                                        $resRecharge = find_by_sql($select);
                                        
                                        $arrRecharge = array();
                                        foreach( $resRecharge as $keyRech => $valRech  ){
                                            $arrRecharge[] = $valRech['recharge'];
                                        }
                                         
                                        $db->selectDB("u354621102_embarkgprs");
                                         
                                         
                                    foreach($resSites as $keySites => $valSites ){
                                        
                                        $number = $valSites['number'];
                                        
                                        $selectVolTillDate  = "Select  ( SUM(cv.twenty) + SUM(cv.fives) + SUM(cv.ones) ) as total , s.name as site_name from CARD_USAGE_VIEW as cv INNER JOIN sites as s on ( s.number  COLLATE latin1_general_ci = cv.number )  where cv.number IN ( ".$number.")  ";
                                        $resSitesTilDate = find_by_sql($selectVolTillDate); 
                                        $usageTillDate = $resSitesTilDate[0]['total'];
                                        $sitename = $resSitesTilDate[0]['site_name'];
                                        $volDay = floatval($valSites['twentys'] ) + floatval($valSites['ones'] ) + floatval($valSites['fives'] );
                                        
                                    ?>
                                    <tr>
                                    <td><?php echo count_id();?></td>
                                    <td><?php echo  date("F",mktime(0, 0, 0, $month,10));
                                    $todate = $valSites['todate'];?></td>
                                    <td><?php echo date("Y-m-d H:i",strtotime($valSites['min_date']));?></td>
                                    <td><?php echo date("Y-m-d H:i",strtotime($valSites['max_date']));?></td>
                                    <td><?php echo $sitename;?></td>
                                    <td><?php echo $usageTillDate;?></td>
                                    <td><?php  
                                        
                                        //echo ( isset( $arrRecharge[$number] ) ) ?  $arrRecharge[$number] : 0;
                                        echo array_sum(array_values($arrRecharge));
                                       
                                    
                                    //echo file_get_contents("https://analytics.embarkrms.com/get_recharge.php?id=$number&from=$todate&to=$todate");;?></td>
                                    <td><?php echo $volDay;?></td>
                                    <td><?php echo $valSites['ones'];?></td>
                                    <td><?php echo $valSites['fives'];?></td>
                                    <td><?php echo $valSites['twentys'];?></td>
                                    <td><?php 
                                    
                                    
                                    $twentys = $valSites['twentys'];
                                    $ones = $valSites['ones'];
                                    $fives = $valSites['fives'];
                                    
                                    $twentys_rec = $twentys / 20;
                                    $twentys_val = $twentys_rec * 15;
                                    
                                    $ones_rec = $ones / 1;
                                    $ones_val = $ones_rec * 1;
                                    
                                    $fives_rec = $fives / 5;
                                    $fives_val = $fives_rec * 5;
                                    
                                    echo ( floatval($twentys_val) + floatval($ones_val) + floatval($fives_val)  );
                                    
                                    ?></td>
                                    
                                    
                                    </tr>
                                    
                                    <?php } ?>
                                    
                                </tbody>    
                            </table>
                        </div>
                            
                             



                        </div>
                    </div>
                </div>
            </div>

        </div>


    </div>
</section>

<!--</center>-->
<?php include_once './layouts/scripts.php'; ?>

<script src="assets/plugins/momentjs/moment.js"></script> <!-- Moment Plugin Js --> 
<!-- Bootstrap Material Datetime Picker Plugin Js -->
<script src="assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script> 
<script>

    $(function () {

        $('.datepicker').bootstrapMaterialDatePicker({
            format: 'YYYY-MMM',
            clearButton: true, 
            time: false
        });

        $('.js-exportable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });

</script>

<?php include_once './layouts/footer.php'; ?>