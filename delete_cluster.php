<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  
?>
<?php
  $d_sale = find_by_id('cluster',(int)$_GET['id']); 
  if(!$d_sale){
    $session->msg("d","Missing cluster id.");
    redirect('manage_cluster.php');
  }
?>
<?php
  $delete_id = delete_by_id('cluster',(int)$d_sale['id']);
  if($delete_id){
      $session->msg("s","Cluster deleted.");
      redirect('manage_cluster.php');
  } else {
      $session->msg("d","Cluster deletion failed.");
      redirect('manage_cluster.php');
  }
?>
