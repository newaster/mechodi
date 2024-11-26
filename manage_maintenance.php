<?php
$page = 13;
include_once './layouts/header.php';
$query = "select * from maintenance  where updated_by = '{$_SESSION['user_id']}'  order by id desc ";
$allClusters = find_by_sql($query);
?>
<!--<center>-->

<section class="content">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-7 col-md-6 col-sm-12">
                <h2>Aster Dashboard</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="main.php"><i class="zmdi zmdi-home"></i> Embark </a></li>
                    <li class="breadcrumb-item active">Aster Maintenance</li>
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
                        <h4 class="title">Manage Maintenance</h4>

                    </div>
                    <div class="card">  

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>User Name </th>
                                        <th>Site Number</th>
                                        <th>User Email</th>
                                        <th>Contact</th>   
                                        <th>Maintenance Date</th> 
                                        <th>Maintenance Time</th> 
                                        <th>Date Added</th>
                                        <th>Action Taken</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>

                                </thead>

                                <tbody>
                                    <?php
                                    $i = 1;
                                    $arrSites = array();
                                    foreach ($allClusters as $keyCluster => $valCluster) {

                                        $sqlSites = "Select * from sites where number = '" . $valCluster['site'] . "'   ";
                                        $arrSites = find_by_sql($sqlSites);
                                        ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo ucwords($valCluster['username']); ?></td>
                                            <td><?php echo $valCluster['site'] . " [ " . $arrSites[0]['name'] . " ] "; ?></td>
                                            <td><?php echo $valCluster['email']; ?></td>
                                            <td><?php echo $valCluster['contact']; ?></td>
                                            <td><?php echo date("Y-m-d", strtotime($valCluster['maintenance_date'])); ?></td> 
                                            <td><?php echo $valCluster['maintenance_time']; ?></td> 
                                            <td><?php echo date("Y-m-d", strtotime($valCluster['created_date'])); ?></td> 
                                             <td><?php echo $valCluster['action']; ?></td> 
                                            <td><?php echo ( "active" == $valCluster['status'] ) ? "Pending" : "Done"; ?></td>

                                            <td>     

                                                <a  href="#"  class="change_status"   data-attr="<?php echo $valCluster['id']; ?>"     >  
                                                    <button type="button" rel="tooltip" title="DONE"   data-attr="<?php echo $valCluster['id']; ?>" class="btn btn-danger btn-simple btn-xs">
                                                        <i class="zmdi zmdi-assignment-check"></i>
                                                    </button> 
                                                </a>
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


        <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="title" id="defaultModalLabel">Report Action Taken</h4>
                        <input type="hidden" name="" id="hid_maintenance" />
                    </div>
                    <div class="modal-body">  <textarea class="form-control" id="reason" ></textarea></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-round waves-effect" id="save_reason"  >SAVE</button>
                        <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">CLOSE</button>
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
        $(".change_status").on("click", function () {

            var id = $(this).attr("data-attr");
            $("#hid_maintenance").val(id);
            $('#defaultModal').modal('show');

            $("#save_reason").on("click", function () {
                var hid = $("#hid_maintenance").val();
                var action = $("#reason").val();
                $.post("ajaxCall.php", {operation: "edit-maintenance", id: hid, action: action}, function (data) {
                    var dataArr = $.parseJSON(data);

                    if (dataArr.result === "success") {
                        alert(dataArr.msg);
                        window.location = "";
                    } else {
                        alert(dataArr.msg);
                        $('#defaultModal').modal('hide');
                        $("#reason").val("");
                    }

                });

            });

        });


        $('.js-exportable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });

</script>

<?php include_once './layouts/footer.php'; ?>