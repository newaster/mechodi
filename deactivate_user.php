<?php
  require_once('includes/load.php');
?>
<?php

$sqlSelZone = "Select * from login where id = '".(int)$_GET['id']."' ";

  $d_sale = find_by_sql($sqlSelZone); 
  if(!$d_sale){
    $session->msg("d","Missing user id.");
    redirect('manage_user.php');
  }
?>
<?php
    
  $id = (int) $_GET['id'];
  $status =  $_GET['status'];
  
  $sqlUpdateLogin = "Update login set status = '$status'  where id = '".$id."' ";
   
  if( $db->query($sqlUpdateLogin) ){
      
      $session->msg("s","User's status changed.");
      redirect('manage_user.php');
  } else {
      $session->msg("d","User status change failed.");
      redirect('manage_user.php');
  }
?>
