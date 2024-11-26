<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  
?>
<?php
  $d_sale = find_by_id('maintenance',(int)$_GET['id']); 
  if(!$d_sale){
    $session->msg("d","Missing maintenance id.");
    redirect('manage_maintenance.php');
  }
?>
<?php
  $delete_id = delete_by_id('maintenance',(int)$d_sale['id']);
  if($delete_id){
      $session->msg("s","Maintenance deleted.");
      redirect('manage_maintenance.php');
  } else {
      $session->msg("d","Maintenance deletion failed.");
      redirect('manage_maintenance.php');
  }
?>
