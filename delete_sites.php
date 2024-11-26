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

  $sqlDelZone = "DELETE from sites where id = '".(int)$_GET['id']."' ";
   
  if( $db->query($sqlDelZone) ){
      $session->msg("s","Site deleted.");
      redirect('manage_sites.php');
  } else {
      $session->msg("d","Site deletion failed.");
      redirect('manage_sites.php');
  }
?>
