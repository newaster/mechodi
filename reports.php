<?php

$page = 3;
include_once './layouts/header.php';

echo $city ;

if ($_SESSION['role'] == 'admin') {
    $query = "select number, name , datediff(sites.renewal_date, NOW()) as days from sites  order by id desc ";
} else if ($_SESSION['role'] == 'guest') {
    $query = "select sites.name , sites.number , datediff(sites.renewal_date, NOW()) as days from sites inner join zone on zone.number = sites.number where zone.unique_id in ('{$_SESSION['zone']}') having days > 0 order by sites.id desc ";
} else {
    $query = "select number, name , datediff(sites.renewal_date, NOW()) as days from sites where created_by = '{$_SESSION['user_id']}' having days > 0  order by id desc ";
}
$allNumbers = find_by_sql($query);
//$allNumbers = $allNumbers[0];

$siteNumbers = array();

foreach ($allNumbers as $keyNumber => $valNumber) {
    $siteNumbers[] = $valNumber['number'];
}
if (sizeof($siteNumbers) > 0) {
    // $selectSites = "Select * from mbr_log where number in (" . implode(',', $siteNumbers) . ")  ";
}


$fromdate = ( true == isset($_POST['fromdate']) && "" != $_POST['fromdate'] ) ? $_POST['fromdate'] : "";
$todate = ( true == isset($_POST['todate']) && "" != $_POST['todate'] ) ? $_POST['todate'] : "";

$arrParamHide = array("1TEMP C" ,  "~2TEMP C" , "~3TEMP C" , "~4TEMP C", "1TEMP C" ,  "2TEMP C" , "3TEMP C" , "4TEMP C");
 
?>
<!--<center>-->
<link href="assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />

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

                        <form name="formplot" method="post" action="reports.php"   >
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group label-floating">
                                        <label class="control-label">Select Site</label>

                                        <div class="row">
                                            <select class="form-control" name="number"  id="select"> 
                                                <option value="">Select</option>
                                                <?php
                                                $arrnumbers = array();

                                                foreach ($allNumbers as $keyNum => $valNum) {
                                                    $arrnumbers[] = $valNum['number'];
                
                                                    $selected = "";
                                                    if ($_REQUEST['number'] == $valNum['number']) {
                                                        $selected = " selected = 'selected'";
                                                    }
                                                    ?>      
                                                    <option value="<?php echo $valNum['number']; ?>" <?php echo $selected; ?>  >
                                                        <?php echo $valNum['number'] . " ( " . $valNum['name'] . " )"; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>   
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="form-group label">
                                       
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="control-label">Select Date From</label>
                                                <input class="form-control datepicker"  readonly="readonly"  id="fromdate" type="text" name="fromdate"  value="<?php echo ( $fromdate == "" ) ?  date("d-m-Y") : $fromdate; ?>" id="fromdate">
                                            </div> 
                                            <div class="col-md-6">
                                                 <label class="control-label">Select Date To</label>
                                                <input class="form-control datepicker"  readonly="readonly"  id="todate" type="text" name="todate"  value="<?php echo ( $todate == "" ) ? date("d-m-Y") : $todate; ?>" id="todate">
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
                            <?php
                           


                            $size = 0;
                            $table = "";
                            $reqFormNumber = ( true == isset($_POST['number']) && $_POST['number'] != "" ) ? $_POST['number'] : "";
                            $whereCond = " ";
                            $whereCond2 = "";
                            $whereCond3 = "";
                            $limit = " Limit 50";

                            if ($reqFormNumber != "") {
                                $whereCond = " where number = '" . $reqFormNumber . "'";
                                $whereCond2 = " where number = '" . $reqFormNumber . "'";
                            } else {
                                if (sizeof($siteNumbers) > 0) {
                                    $whereCond = " where number in ( " . implode(',', array_filter($siteNumbers)) . " ) ";
                                    $whereCond2 = " where number in ( " . implode(',', array_filter($siteNumbers)) . " ) ";
                                } else {
                                    $whereCond = " where number = '' ";
                                    $whereCond2 = " where  number = '' ";
                                }
                            }

                            if ($fromdate != "" && $todate != "") {

                                $datestr = date_create($todate);
                                date_add($datestr, date_interval_create_from_date_string("1 days"));


                                $whereCond3 = ' and  DATE(DATETIME) between "' . date('Y-m-d', strtotime($fromdate)) . '" and "' . date('Y-m-d', strtotime($todate)) . '"';
                                $limit = "";
                            }
                            ?>

                            <table  class="table table-bordered table-striped table-hover dataTable js-exportable"  >
                                <?php
                                foreach ($db->query('SELECT * FROM embark_data   ' . $whereCond . " " . $whereCond3 . "   order by datetime desc " . $limit) as $row) {

                                    $headers = $row['COLNAME'];

                                    $arrHeaders = explode(",", $headers);
                                     
                                    if ($size < sizeof($arrHeaders)) {
                                        $size = sizeof($arrHeaders);
                                        
                                        $table = "<thead><tr>";
                                        
                                        foreach( $arrHeaders as $keyThHeader => $valThHeader ){
                                             if( $reqFormNumber == "9582024335" && !in_array( $valThHeader , $arrParamHide )  ){
                                                $table .= "<th>".$valThHeader."</th>";
                                             }else if( $reqFormNumber != "9582024335" ){
                                                  $table .= "<th>".$valThHeader."</th>";
                                             }
                                        }
                                        
                                         $table  .= "</tr></thead>";

                                        $arrtableheader = $arrHeaders;
                                    }
                                    
                                }

                                echo $table;
                                ?>
                                <tbody>
                                    
                                <?php
                                $ii = 0;
                                $tabledata = "";
                                
                         
                                $resRec = find_by_sql('SELECT * FROM embark_data' . $whereCond . " " . $whereCond3 . "  and status='active'  order by datetime desc " . $limit);
                                
                                foreach(  $resRec as $keyRec => $row ){
                                     
                                    $tabledata .= "<tr>";
                                    $headers = $row['COLNAME'];
                                    $values = $row['COLVALUES'];
                                    $arrHeaders = explode(",", $headers);
                                    $arrvalues = explode(",", $values);
                                     
                                    
                                    $i = 0;
                                    $arr = array();
                                    $i = 0;
                                    foreach( $arrtableheader as $keyHeads => $valHeads ){
                                        if( isset($arrHeaders[$i]) && $arrHeaders[$i]  == $valHeads ){
                                            $valStr = $arrvalues[$i];
                                            if( $valStr == "NODEVICE" ){
                                                $valStr = "ASTR";
                                            }
                                            
                                            $arr[$keyHeads.$valHeads] = $valStr;
                                            $i++;
                                        }else{
                                            $arr[$keyHeads.$valHeads] = "-";
                                        }
                                    }
                                    
                                    
                                    
                                    for ($inC = 0; $inC < sizeof($arrtableheader); $inC++) {
                                             if( $reqFormNumber == "9582024335" && !in_array( $arrtableheader[$inC] , $arrParamHide )  ){
                                                        
                                                        if (  isset($arr[$inC.$arrtableheader[$inC]]) && array_key_exists($inC.$arrtableheader[$inC], $arr)) {
                                                           
                                                                $tabledata .= "<td>" . $arr[$inC.$arrtableheader[$inC]] . "</td>";
                                                           
                                                        } else {
                                                                $tabledata .= "<td>-</td>";
                                                        }
                                                        
                                             }else if( $reqFormNumber != "9582024335" ) {
                                               //  echo $inC.$arrtableheader[$inC]." -- ".$arr;
                                                   //echo $arr[$inC.$arrtableheader[$inC]];
                                                 //echo $inC.$arrtableheader[$inC]." - ".json_encode($arr);
                                                if (array_key_exists($inC.$arrtableheader[$inC], $arr)) {
                                                           
                                                        $tabledata .= "<td>" . $arr[$inC.$arrtableheader[$inC]] . "</td>";
                                                } else {
                                                        $tabledata .= "<td>-</td>";
                                                }
                                             }
                                    }
                    
                                    $tabledata .= "</tr>";
                                    $intI++;
                                    $ii++;
                                    
                                    
                                }
                                
                             
                              
                                echo $tabledata;
                                
                                file_put_contents("table.txt", $tabledata);
                                
                                ?>
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
            format: 'DD-MM-YYYY',
            clearButton: true,
            weekStart: 1,
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