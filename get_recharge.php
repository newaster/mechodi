<?php

require_once('/home/embarkrms.com/public_html/atm/android/includes/load.php');

$id = ( true == isset($_REQUEST['id']) && "" != $_REQUEST['id'] ) ?  $_REQUEST['id'] : 0;
$from = ( true == isset($_REQUEST['from']) && "" != $_REQUEST['from'] ) ?  $_REQUEST['from'] : date("Y-m-d");
$to = ( true == isset($_REQUEST['to']) && "" != $_REQUEST['to'] ) ?  $_REQUEST['to'] : date("Y-m-d");


if( $id == 0 ){
    echo 0;
    exit;
}


$select  = "Select SUM(rechargeval) as recharge from wateratmdata where number IN ( $id ) and DATE(date) >= '$from' and DATE(date) <= '$to' ";
$resRecharge = find_by_sql($select);
 

if( sizeof($resRecharge) > 0 ){
    echo  ( isset($resRecharge[0]['recharge']) ) ?  $resRecharge[0]['recharge'] : 0;
    exit;
}else{
    echo 0;
    exit;
}
