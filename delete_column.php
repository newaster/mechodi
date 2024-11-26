<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  
?>
<?php
  $d_sale = find_by_id('cluster_columns',(int)$_GET['id']); 
  if(!$d_sale){
    $session->msg("d","Missing cluster id.");
    redirect('manage_columns.php');
  }
?>
<?php
  $delete_id = delete_by_id('cluster_columns',(int)$d_sale['id']);
  if($delete_id){
      $session->msg("s","Cluster deleted.");
      redirect('manage_columns.php');
  } else {
      $session->msg("d","Cluster deletion failed.");
      redirect('manage_columns.php');
  }
?>
