<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  
?>
<?php

$sqlSelZone = "Select * from zone where unique_id = '".(int)$_GET['id']."' ";

  $d_sale = find_by_sql($sqlSelZone); 
  if(!$d_sale){
    $session->msg("d","Missing zone id.");
    redirect('manage_zone.php');
  }
?>
<?php

  $sqlDelZone = "DELETE from zone where unique_id = '".(int)$_GET['id']."' ";
   
  if( $db->query($sqlDelZone) ){
      $session->msg("s","Zone deleted.");
      redirect('manage_zone.php');
  } else {
      $session->msg("d","Zone deletion failed.");
      redirect('manage_zone.php');
  }
?>
