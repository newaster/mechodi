<?php
$page = 12;
include './layouts/header.php';

$query = "select number, name from sites where created_by = '{$_SESSION['user_id']}'  order by id desc ";
$allNumbers = find_by_sql($query);

$id = (true == isset($_REQUEST['id']) && "" != $_REQUEST['id'] ) ? $_REQUEST['id'] : 0;
$userid = (true == isset($_SESSION['user_id']) && "" != $_SESSION['user_id'] ) ? $_SESSION['user_id'] : 0;

$queryUser = "select * from users where id = '$id' and updated_by = '$userid' Limit 1";
$user = find_by_sql($queryUser);
$arrUser = array();
if (sizeof($user) > 0) {
    $arrUser = $user[0];
}
?>

<!--<center>-->
<link href="assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />


<style>
    .jvectormap-legend-tick{
        width: 50px !important;
    }

    .progress{
        background:#e47297;
        width:20px;
    }

</style>
<section class="content">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-7 col-md-6 col-sm-12">
                <h2>Aster Dashboard</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="main.php"><i class="zmdi zmdi-home"></i> Aster </a></li>
                    <li class="breadcrumb-item active">Add Maintenance</li>
                </ul>
                <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-12">                
                <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            </div>
        </div>
    </div>
    <div class="container-fluid">

        <div class="row clearfix">
            <div class="col-md-12 col-lg-8">
                <div class="card">
                    <div class="header">
                        <h2><strong><li class="zmdi zmdi-assignment-account">&nbsp;</li>Add</strong> Maintenance</h2>
                        <ul class="header-dropdown">

                        </ul>                        
                    </div>
                    <div class="body">
                        <div class="card">
                            <div class="header" > 

                                <div class="row"> 


                                    <div class="col-md-6">
                                        <p class="category">*All fields are Mandatory</p>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-12 card-content">   
                                <form action="#" method="post"   >
                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="form-group label-floating">
                                                <label class="control-label">User Name</label>
                                                <input required="required" name="username" id="username"  type="text" class="form-control" value="<?php echo ( true == isset($arrUser['username']) && "" != $arrUser['username'] ) ? $arrUser['username'] : ""; ?>"  >
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Email address</label>
                                                <input required="required" name="email" id="email" type="email" class="form-control"  value="<?php echo ( true == isset($arrUser['email']) && "" != $arrUser['email'] ) ? $arrUser['email'] : ""; ?>" >
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Contact (Without country code) </label>
                                                <input required="required" name="contact" id="contact" type="number" class="form-control" value="<?php echo ( true == isset($arrUser['contact']) && "" != $arrUser['contact'] ) ? $arrUser['contact'] : ""; ?>" >
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Assign Sites to User</label>
                                                <select required="required"   class="form-control"  name="sites" id="sites" >

                                                    <?php
                                                    $customerselectall = ( true == isset($arrUser['sites']) && "" != $arrUser['sites'] ) ? $arrUser['sites'] : "";

                                                    $arrCluster = explode(",", $customerselectall);

                                                    $selected = "";
                                                    foreach ($allNumbers as $key => $val) {
                                                        if ("" !== $val['number']) {

                                                            $selected = ( in_array($val['number'], $arrCluster) == true ) ? " selected='selected' " : "";
                                                            ?>

                                                            <option  <?php echo $selected; ?> value="<?= $val['number']; ?>"><?= ( $val['number'] . " ( " . $val['name'] . " ) "); ?></option>                            
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">

                                        <div class="col-md-6">
                                            <label class="control-label">Maintenance Date </label>
                                            <div class="form-group label-floating">

                                                <input required="required" name="maintenance_date" readonly="readonly" id="maintenance_date" type="text" class="form-control" >
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="control-label">Maintenance Time (24 Hrs)</label>
                                            <div class="form-group label-floating">

                                                <input required="required" name="maintenance_time" readonly="readonly" id="maintenance_time" type="text" class="form-control" >
                                            </div>
                                        </div>

                                    </div>

                                    <button type="button" id="add_maintenance" class="btn btn-primary pull-right">Add Maintenance</button>
                                    <div class="clearfix"></div>
                                </form>

                            </div>
                        </div>                          
                    </div>
                </div>
            </div>



        </div>

    </div>
</section>
<?php include './layouts/scripts.php'; ?>

<script src="assets/plugins/momentjs/moment.js"></script> <!-- Moment Plugin Js --> 
<!-- Bootstrap Material Datetime Picker Plugin Js -->
<script src="assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script> 

<script>

    $(document).ready(function () {

        $('#maintenance_date').bootstrapMaterialDatePicker({
            format: 'DD-MM-YYYY',
            clearButton: true,
            weekStart: 1,
            time: false
        });

        $('#maintenance_time').bootstrapMaterialDatePicker({
            date: false,
            format: 'HH:mm',
            clearButton: true,
            weekStart: 1,
            time: true
        });

        $("#add_maintenance").on("click", function () {

            var username = $("#username").val();
            if (username === "") {
                alert("Username cannot be blank!!!");
                return;
            }

            var email = $("#email").val();
            if (email === "") {
                alert("Email cannot be blank!!!");
                return;
            }

            var contact = $("#contact").val();
            if (contact === "") {
                alert("Contact cannot be blank!!!");
                return;
            }

            var site = $("#sites").val();
            if (site === "") {
                alert("Site cannot be blank!!!");
                return;
            }

            var email = $("#email").val();
            if (email === "") {
                alert("Email cannot be blank!!!");
                return;
            }

            var maintenance_date = $("#maintenance_date").val();

            if (maintenance_date === "") {
                alert("Maintenance Date cannot be blank!!!");
                return;
            }

            var maintenance_time = $("#maintenance_time").val();
            if (maintenance_time === "") {
                alert("Maintenance Time cannot be blank!!!");
                return;
            }

            $.post("ajaxCall.php", {operation: "add-maintenance", username: username, email: email, contact: contact, site: site, maintenance_date: maintenance_date, maintenance_time: maintenance_time}, function (data) {

                var response = data.trim();

                var jarr = $.parseJSON(response);
                if (jarr.result === "error") {
                    alert(jarr.msg);
                } else {
                    alert(jarr.msg);
                    window.location = "manage_maintenance.php";
                }
            });

        });


    });


</script>
<?php include './layouts/footer.php'; ?>