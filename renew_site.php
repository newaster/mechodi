<?php
  require_once('includes/load.php');
?>
<?php

$sqlSelZone = "Select * from sites where id = '".(int)$_GET['id']."' ";

  $d_sale = find_by_sql($sqlSelZone); 
  if(!$d_sale){
    $session->msg("d","Missing site id.");
    redirect('manage_sites.php');
  }
?>
<?php
  $uid = $_SESSION['user_id'];
  $selectUser = "Select * from login where id = '$uid'";
  $resUser = find_by_sql($selectUser);
  $resUser = $resUser[0];
  $quota = $resUser['quota'];
  if( $quota < 1 ){
      $session->msg("d","Subscription Quota Unavailable!!!. Please Contact Support");
      redirect('manage_sites.php');
  }
  
  $quota = $quota - 1;
  $sqlDelZone = "Update  sites set renewal_date = '".date("Y-m-d H:i:s", strtotime('+1 year'))."' where id = '".(int)$_GET['id']."' ";
  $db->query($sqlDelZone);
  if( $db->affected_rows() ){
      $updateQuota = "Update login set quota = '$quota' where id = '$uid' ";
      $db->query($updateQuota);
      
      $insertDebit = "INSERT INTO `debit` ( `point`, `rec_id`, `purpose`, `user_id` , `created_date`) VALUES (1, '".(int)$_GET['id']."' , 'RENEWED SITE' , '$uid' ,'".date("Y-m-d H:i:s")."')";
      $db->query($insertDebit);
      
      $session->msg("s","Site Renewed.");
      redirect('manage_sites.php');
  } else {
      $session->msg("d","Site Renewal failed.");
      redirect('manage_sites.php');
  }
?>
