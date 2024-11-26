<?php
$page = 9;
include_once './layouts/header.php';
if ($_SESSION['role'] == 'admin') {
    $query = "select l2.* , CONCAT( l1.fname , ' ',l1.lname ) as updated_users from login as l2 inner join login as l1 on l1.id = l2.updated_by  where l2.role = 'guest' order by l2.id desc ";
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
                <h2>Aster Dashboard</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="main.php"><i class="zmdi zmdi-home"></i> Aster </a></li>
                    <li class="breadcrumb-item active">Manage Users</li>
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
                        <h4 class="title">Manage Users</h4>

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
                                        <th>Password</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                        <th>City</th>
                                        <th>Assigned Zones</th>
                                        <th>Action</th>
                                    </tr>

                                </thead>

                                <tbody>
                                    <?php
                                    $i = 1;
                                    $arrSites = array();
                                    foreach ($allUsers as $keyUser => $valUser) {
                                        if (isset($valUser['sites']) && "" != $valUser['sites']) {
                                            $sqlSites = "Select name, unique_id from zone where unique_id in ( " . $valUser['sites'] . " )  group by unique_id ";
                                        }
                                        $arrSites = find_by_sql($sqlSites);
                                        ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <?php if ($_SESSION['role'] == 'admin') { ?>
                                                <td><?php echo $valUser['updated_users']; ?></td>
                                            <?php } ?>
                                            <td><?php echo $valUser['uname']; ?></td>
                                            <td><?php echo $valUser['cleartext_pass']; ?></td>
                                            <td><?php echo $valUser['fname']; ?></td>
                                            <td><?php echo $valUser['lname']; ?></td>
                                            <td><?php echo $valUser['email']; ?></td>
                                            <td><?php echo $valUser['number']; ?></td>
                                            <td><?php echo $valUser['city']; ?></td>
                                            <td><?php
                                                foreach (array_values($arrSites) as $keyval => $value) {
                                                    echo $value['name'] . " | ";
                                                }
                                                ?></td>
                                            <td>                                       

                                                <a  href="add_user.php?id=<?php echo $valUser['id']; ?>"   >  <button type="button" rel="tooltip" title="Edit" class="btn btn-primary btn-simple btn-sm">
                                                        <i class="zmdi zmdi-edit"></i>
                                                    </button></a>
                                                    <?php if( $valUser['status'] == "active" ) { ?>
                                                <a  href="deactivate_user.php?id=<?php echo $valUser['id']; ?>&status=inactive" >  <button type="button" rel="tooltip" title="Deactivate" class="btn btn-danger btn-simple btn-sm">
                                                        <i class="zmdi zmdi-delete"></i>
                                                    </button> </a>
                                                    <?php } else { ?>
                                                    <a  href="deactivate_user.php?id=<?php echo $valUser['id']; ?>&status=active" >  <button type="button" rel="tooltip" title="Activate" class="btn btn-danger btn-simple btn-sm">
                                                        <i class="zmdi zmdi-undo"></i>
                                                    </button> </a>
                                                    <?php } ?>
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