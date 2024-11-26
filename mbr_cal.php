<?php

include './includes/load.php';

$str_response = '{"Data":"7218043981*SRP 238,243,231,255,007,000,192,192,024,000,255,255,255,255,153,001,002,010,000,000,255,255,247,009,000,000,000,007,000,004,001,000,000,000"}';

$json_obj = json_decode($str_response);

$data = $json_obj->Data;

$arrData = explode("*", $data);

$siteNumber = $arrData[0];

$arrSiteData = explode(" ", $arrData[1]);

$siteName = $arrSiteData[0];

$siteData = $arrSiteData[1];

$arrSiteResponse = explode(",", $siteData);

$pres_hi = intval($arrSiteResponse[13]);
$pres_lo = intval($arrSiteResponse[12]);

$pres1 = ($pres_hi * 256) + $pres_lo;


$do_hi = intval($arrSiteResponse[11]);
$do_lo = intval($arrSiteResponse[10]);

$do_mts = ($do_hi * 256) + $do_lo;

$emf1_hi = intval($arrSiteResponse[15]);
$emf1_lo = intval($arrSiteResponse[14]);

$emf1_mts = ($emf1_hi * 256) + $emf1_lo;

$emf1_total_lo = intval($arrSiteResponse[16]);
$emf1_total_Mid = intval($arrSiteResponse[17]);
$emf1_total_hi = intval($arrSiteResponse[18]);

$emf1_total = ($emf1_total_hi * 65536) + ( $emf1_total_Mid * 256 ) + $emf1_total_lo;


$emf2_hi = intval($arrSiteResponse[21]);
$emf2_lo = intval($arrSiteResponse[20]);

$emf2_mts = ($emf2_hi * 256) + $emf2_lo;

$emf2_total_lo = intval($arrSiteResponse[22]);
$emf2_total_Mid = intval($arrSiteResponse[23]);
$emf2_total_hi = intval($arrSiteResponse[24]);

$emf2_total = ($emf2_total_hi * 65536) + ( $emf2_total_Mid * 256 ) + $emf2_total_lo;

$pres1 = ( $pres1 == 65535 ) ? 0 : $pres1;

$emf1_mts = ( $emf1_mts == 65535 ) ? 0 : $emf1_mts;
$emf2_mts = ( $emf2_mts == 65535 ) ? 0 : $emf2_mts;
$do_mts = ( $do_mts == 65535 ) ? 0 : $do_mts;
$emf1_total = ( $emf1_total == 65535 ) ? 0 : $emf1_total;
$emf2_total = ( $emf2_total == 65535 ) ? 0 : $emf2_total;

$sql_insert = "INSERT INTO mbr_log (  `sitename`, `number`, `data`, `pressure`, `emf1`, `emf2`, `do`, `emf1_total`, `emf2_total`, `created_date` )  ";
$sql_insert .= " VALUES ";
$sql_insert .= " ( '$siteName', '$siteNumber' , '$siteData', '$pres1' ,  '$emf1_mts' , '$emf2_mts' , '$do_mts', '$emf1_total', '$emf2_total' , NOW() ) ";

$db->query($sql_insert);

echo $str_out = $siteNumber . " -- SITENAME -- " . $siteName . " SITE DATA -- " . $siteData . " -- PRESSURE : " . $pres1 . " -- DO : " . $do_mts . " -- EMF1 : " . $emf1_mts . " -- EMF2 : " . $emf2_mts . " -- EMF1_TOT : " . $emf1_total . " -- EMF2_TOT " . $emf2_total;
?>
