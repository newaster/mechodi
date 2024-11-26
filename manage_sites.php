<?php
$page = 5;
include_once './layouts/header.php';
$query = "select * ,datediff(renewal_date , NOW()) as days  from sites  where created_by = '{$_SESSION['user_id']}'  order by id desc ";
$allSites = find_by_sql($query);

$sqlUserDetails = "Select * from login where id = '{$_SESSION['user_id']}'";
$resUserDetails = find_by_sql($sqlUserDetails);
$quota = $resUserDetails[0]['quota'];


?>
<!--<center>-->
 
<section class="content">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-7 col-md-6 col-sm-12">
                <h2>Aster Dashboard</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="main.php"><i class="zmdi zmdi-home"></i> Aster </a></li>
                    <li class="breadcrumb-item active">Manage Sites</li>
                </ul>
                <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-12">                
                <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <?php echo display_msg($msg); ?>
        <div class="row">
            <div class="col-md-12"> 
                <div class="body">
                    <div class="card" > 
                        <h4 class="title">Manage Sites</h4>
                         
                    </div>
                    <div class="card">  
                        
                        <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                       

                            <thead>

                                <tr>
                                    <th>Sr. No.</th>
                                    <th>Site Name </th>
                                    <th>Site Sim Number</th>
                                    <!--<th>Longitude</th>
                                    <th>Latitude</th>-->
                                    <th>Technician Contact</th>
                                    <th>City</th>
                                    <th>Created Date</th>
                                    <th>Renewal Date</th>
                                    <th>Days Left</th>
                                    <th>Subscription Status</th>
                                    <th>Renew</th>
                                    <!--<th>Status</th>-->
                                    <th>Action</th>
                                </tr>

                            </thead>

                            <tbody>
                                <?php
                                $i = 1;
                                $arrSites = array();
                                foreach ($allSites as $keySites => $valSites) {
                                    ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $valSites['name']; ?></td>
                                        <td><?php echo $valSites['number']; ?></td>
                                        <!--<td><?php echo $valSites['longitude']; ?></td>
                                        <td><?php echo $valSites['latitude']; ?></td>-->
                                        <td><?php echo $valSites['contact']; ?></td>
                                        <td><?php echo $valSites['city']; ?></td>
                                        <td><?php echo date("Y-m-d",strtotime($valSites['created_date'])); ?></td>
                                        <td><?php echo date("Y-m-d",strtotime($valSites['renewal_date'])); ?></td>
                                        <td><?php echo ( $valSites['days'] < 0 ) ? 0 : $valSites['days']; ?></td>
                                        <td><?php echo ( $valSites['days'] < 0 ) ? "Inactive" : "Active"; ?></td>
                                        
                                        <td>   
                                        <?php
                                        if( $valSites['days'] < 0 ) {
                                        if( $quota > 0 ){  ?>
                                            <div class="d-flex">
                                            
                                                <a  href="renew_site.php?id=<?php echo $valSites['id']; ?>"  >  <button type="button" rel="tooltip" title="Renew" class="btn btn-danger btn-simple btn-sm">
                                                        <i class="zmdi zmdi-refresh"></i>Renew Now
                                                    </button> </a>
                                            </div>
                                            <?php }else{ ?>
                                                <a  href="#" onclick="confirmSubscription();" >  <button type="button" rel="tooltip" title="Renew" class="btn btn-danger btn-simple btn-sm">
                                                        <i class="zmdi zmdi-refresh"></i>Renew Now
                                                    </button> </a>
                                            <?php }  ?>
                                            
                                            <?php }  ?>
                                        </td>
                                        
                                        <!--<td><?php echo $valSites['status']; ?></td>-->
                                        
                                        <td>                                       
                                            <a  href="add_site.php?id=<?php echo $valSites['id']; ?>"    >  <button type="button" rel="tooltip" title="Edit" class="btn btn-primary btn-simple btn-sm">
                                                    <i class="zmdi zmdi-edit"></i>
                                                </button></a>
                                            <a  href="delete_sites.php?id=<?php echo $valSites['id']; ?>"  >  <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-sm">
                                                    <i class="zmdi zmdi-delete"></i>
                                                </button> </a>

                                        </td>
                                    </tr>

                                    <?php
                                    $i++;
                                }
                                ?>
                            </tbody>


                        </table>
                            </div>
                    </div>
                </div>
            </div>

        </div>
   

    </div>
</section>

<!--</center>-->
<?php include_once './layouts/scripts.php'; ?>
 

<script>

function confirmSubscription(){
        
        if(confirm('Subscription Quota Unavailable!!! Please Contact Support?')){
                                
        } 
        
        // $.ajax({
        //         url: "ajaxCall.php",
        //         type: "POST",
        //         data: {operation: "check-quota"},
        //         success: function (data) {
                    
        //             if( parseInt(data) < 1 ){
        //                     if(confirm('Subscription Quota Unavailable!!! Please Contact Support?')){
                                
        //                     } 
        //             }

        //         }
        // });

       
    }

$(function () {
    
    $('.js-exportable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});

</script>

<?php include_once './layouts/footer.php'; ?>