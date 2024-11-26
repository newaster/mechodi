<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  
?>
<?php
  $d_sale = find_by_id('header_map',(int)$_GET['id']); 
  if(!$d_sale){
    $session->msg("d","Missing Record id.");
    redirect('manage_header_map.php');
  }
?>
<?php
  $delete_id = delete_by_id('header_map',(int)$d_sale['id']);
  if($delete_id){
      $session->msg("s","Record deleted.");
      redirect('manage_header_map.php');
  } else {
      $session->msg("d","Record deletion failed.");
      redirect('manage_header_map.php');
  }
?>
