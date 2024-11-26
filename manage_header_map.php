<?php
$page = 15;
include_once './layouts/header.php';
$query = "select *  from header_map order by id desc ";
$allHeaders = find_by_sql($query);
?>
<!--<center>-->

<section class="content">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-7 col-md-6 col-sm-12">
                <h2>Embark Dashboard</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="main.php"><i class="zmdi zmdi-home"></i> Embark </a></li>
                    <li class="breadcrumb-item active">Manage Header Map</li>
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
                        <h4 class="title">Manage Header Map</h4>

                    </div>
                    <div class="card">  

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable"> 
                                <thead> 
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Header </th>
                                        <th>Header Description</th>  
                                        <th>Header Type</th> 
                                        <th>Date Added</th> 
                                        <th>Action</th>
                                    </tr> 
                                </thead> 
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $arrSites = array();
                                    foreach ($allHeaders as $keyHeaders => $valHeaders ) {
                                        ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo $valHeaders['header']; ?></td>
                                            <td><?php echo $valHeaders['name']; ?></td>     
                                            <td><?php echo strtoupper($valHeaders['type']); ?></td>  
                                            <td><?php echo $valHeaders['created_date']; ?></td>  
                                            <td>     

                                                <a  href="edit_header_map.php?id=<?php echo $valHeaders['id']; ?>"  >  <button type="button" rel="tooltip" title="Edit" class="btn btn-primary btn-simple btn-xs">
                                                        <i class="zmdi zmdi-edit"></i>
                                                    </button></a>
                                                <a  href="delete_header_map.php?id=<?php echo $valHeaders['id']; ?>"  >  <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
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