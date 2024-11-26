<?php
$page = 9;
include_once './layouts/header.php';
if ($_SESSION['role'] == 'admin') {
    $query = "select l2.* , CONCAT( l2.fname , ' ',l2.lname ) as updated_users from login as l2    order by l2.id desc ";
} else {
    $query = "select * from login  where updated_by = '{$_SESSION['user_id']}' and login.role = 'guest'   order by id desc ";
}
$allUsers = find_by_sql($query);
?>
<!--<center>-->

<section class="content">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-7 col-md-6 col-sm-12">
                <h2>Embark Dashboard</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="main.php"><i class="zmdi zmdi-home"></i> Embark </a></li>
                    <li class="breadcrumb-item active">Manage Sub Users</li>
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
                        <h4 class="title">Manage Sub Users</h4>

                    </div>
                    <div class="card">  

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">


                                <thead>

                                    <tr>
                                        <th>Sr. No.</th>
                                        <?php if ($_SESSION['role'] == 'admin') { ?>
                                            <th>Updated By</th>
                                        <?php } ?>
                                        <th>Username </th>
                                        <?php if ($_SESSION['role'] == 'admin') { ?>
                                            <th>Password</th>
                                        <?php } ?>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                        <th>City</th>
                                        <th>Assigned Zones</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Available Points</th>
                                        <th>Action</th>
                                        <th>Last Logins</th>
                                    </tr>

                                </thead>

                                <tbody>
                                    <?php
                                    $i = 1;
                                    
                                    foreach ($allUsers as $keyUser => $valUser) {
                                        $arrSites = array();
                                        if (isset($valUser['sites']) && $valUser['sites'] != "") {
                                            $sqlSites = "Select name, unique_id from zone where unique_id in ( " . $valUser['sites'] . " )  group by unique_id ";
                                            $arrSites = find_by_sql($sqlSites);
                                        }  
                                        
                                        ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <?php if ($_SESSION['role'] == 'admin') { ?>
                                                <td><?php echo $valUser['updated_users']; ?></td>
                                            <?php } ?>

                                            <td><?php echo $valUser['uname']; ?></td>
                                            <?php if ($_SESSION['role'] == 'admin') { ?>
                                                <td><?php echo $valUser['cleartext_pass']; ?></td>
                                            <?php } ?>
                                            <td><?php echo $valUser['fname']; ?></td>
                                            <td><?php echo $valUser['lname']; ?></td>
                                            <td><?php echo $valUser['email']; ?></td>
                                            <td><?php echo $valUser['number']; ?></td>
                                            <td><?php echo $valUser['city']; ?></td>                                            
                                            <td><?php
                                                foreach (array_values($arrSites) as $keyval => $value) {
                                                    echo $value['name'] . " | ";
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo  ( "guest" !== $valUser['role'] ) ?  strtoupper($valUser['role']) : "SUB USER"; ?></td>
                                            <td><?php echo $valUser['status']; ?></td>
                                            <td><?php echo $valUser['quota']; ?></td>
                                            <td>                                       

                                                <a  href="add_user.php?id=<?php echo $valUser['id']; ?>"   >  <button type="button" rel="tooltip" title="Edit" class="btn btn-primary btn-simple btn-sm">
                                                        <i class="zmdi zmdi-edit"></i>
                                                    </button>
                                                </a>

                                            </td>
                                            
                                            <td>    <button type="button" rel="tooltip" id="last_login" name="<?php echo $valUser['uname']; ?>"  data-attr="<?php echo $valUser['uname']; ?>" title="Show" class="btn btn-danger btn-simple btn-sm last_login">
                                                                <i class="zmdi zmdi-devices"></i>
                                                            </button>  
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

                                    <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" id="myModal">
                                        <div class="modal-dialog ">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Last 5 Logins</h5>
                                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                      
                                                    
                                                </div>
                                                
                                               
                                            </div>
                                        </div>
                                    </div>

<!--</center>-->
<?php include_once './layouts/scripts.php'; ?>


<script>

    $(function () {
        
        $('.table').delegate(".last_login", "click", function () {
            
            var id = $(this).attr('data-attr');
            $.ajax({
                url: "ajaxCall.php",
                type: "POST",
                data: {operation: "last_login", id: id},
                success: function (data) {
                        $('#myModal').modal('show');
                        $(".modal-body").html(data);
                }
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