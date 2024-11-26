<?php
$page = 8;
include './layouts/header.php';

if ($_SESSION['role'] == 'user') {
    $query = "select unique_id, name from zone where updated_by = '{$_SESSION['user_id']}' group by unique_id order by id desc ";
} else {
    $query = "select unique_id, name from zone group by unique_id order by id desc ";
}

$allZones = find_by_sql($query);

$id = (true == isset($_REQUEST['id']) && "" != $_REQUEST['id'] ) ? $_REQUEST['id'] : 0;
$userid = (true == isset($_SESSION['user_id']) && "" != $_SESSION['user_id'] ) ? $_SESSION['user_id'] : 0;
$user = array();
if ($id != 0) {
    if ($_SESSION['role'] == 'user') {
        $queryUser = "select * from login where id = '$id' and updated_by = '$userid' Limit 1";
    } else {
        $queryUser = "select * from login where id = '$id'  Limit 1";
    }
    $user = find_by_sql($queryUser);

    if (sizeof($user) < 1) {
        echo "<script>alert('User not found!!!');</script>";
        exit;
    }
}
$arrUser = array();
if (sizeof($user) > 0) {
    $arrUser = $user[0];
}
$arrSites = array();
if (sizeof($arrUser) > 0) {
    $arrSites = explode(",", $arrUser['sites']);
}

 
?>


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
                    <li class="breadcrumb-item active"><?php echo ( sizeof($arrSites) > 0 ) ? "Edit Users" : "Add Users"; ?></li>
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
                        <h2><strong><li class="zmdi zmdi-account-add">&nbsp;</li><?php echo ( sizeof($arrSites) > 0 ) ? "Edit" : "Add"; ?></strong> User</h2>
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
                                    <input type="hidden" name="customer_id" value="<?php echo $id; ?>" id="customer_id" />
                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Username</label>
                                                <input required="required" name="username" <?php echo ( true == isset($id) && $id != 0 ) ? "readonly='readonly'" : ""; ?> id="username"  type="text" class="form-control" value="<?php echo ( true == isset($arrUser['uname']) && "" != $arrUser['uname'] ) ? $arrUser['uname'] : ""; ?>"  >
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Email address</label>
                                                <input required="required" name="email" id="email" type="email" <?php echo ( true == isset($id) && $id != 0 ) ? "readonly='readonly'" : ""; ?> class="form-control"  value="<?php echo ( true == isset($arrUser['email']) && "" != $arrUser['email'] ) ? $arrUser['email'] : ""; ?>" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Fist Name</label>
                                                <input required="required" name="first_name" id="first_name" type="text" class="form-control" value="<?php echo ( true == isset($arrUser['fname']) && "" != $arrUser['fname'] ) ? $arrUser['fname'] : ""; ?>" >
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Last Name</label>
                                                <input required="required" name="last_name" id="last_name" type="text" class="form-control" value="<?php echo ( true == isset($arrUser['lname']) && "" != $arrUser['lname'] ) ? $arrUser['lname'] : ""; ?>" >
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Contact</label>
                                                <input required="required" name="contact" id="contact" <?php echo ( true == isset($id) && $id != 0 ) ? "readonly='readonly'" : ""; ?> type="text" class="form-control" value="<?php echo ( true == isset($arrUser['number']) && "" != $arrUser['number'] ) ? $arrUser['number'] : ""; ?>" >
                                            </div>
                                        </div>

                                        <div class="col-md-6"> 
                                            <div class="form-group label-floating">
                                                <label class="control-label">City</label>
                                                <input required="required" name="city" id="city" type="text" class="form-control" value="<?php echo ( true == isset($arrUser['city']) && "" != $arrUser['city'] ) ? $arrUser['city'] : ""; ?>" >
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-md-6" >
                                            <?php
                                            if ($_SESSION['role'] == 'user') {
                                                echo "<a href='add_zone.php' >Click here to Add Zones</a>";
                                            }
                                            ?>
                                            <div class="form-group label-floating">
                                                <label class="control-label">Assign Multiple Zones to Customer </label>

                                                <select required="required" multiple="multiple"  class="form-control"  name="customerselectzone" id="customerselectzone">

                                                    <?php
                                                    $customerselectall = ( true == isset($arrUser['sites']) && "" != $arrUser['sites'] ) ? $arrUser['sites'] : "";

                                                    $arrCluster = explode(",", $customerselectall);

                                                    $selected = "";

                                                    foreach ($allZones as $key => $val) {
                                                        $select = "";
                                                        if (in_array($val['unique_id'], $arrSites)) {
                                                            $select = "selected='selected'";
                                                        }
                                                        ?>
                                                        <option <?php echo $select; ?>  value="<?= $val['unique_id']; ?>"><?= $val['name']; ?></option>                            
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Password</label>
                                                <input required="required" name="password" id="password" type="text" class="form-control" >
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6" >
                                            <label class="control-label">Select Status</label>
                                            <select required="required" id="status" name="status" class="form-control">
                                                <option value="">Select Status</option>
                                                <option  <?php echo ( isset($arrUser) && $arrUser['status'] == "active" ) ? "selected='selected'" : ""; ?>  value="active">Active</option>
                                                <option <?php echo ( isset($arrUser) && $arrUser['status'] == "inactive" ) ? "selected='selected'" : ""; ?> value="inactive">Inactive</option>
                                            </select> 

                                        </div>
                                        <div class="col-md-6"  style="<?php echo ( ( "user" == $_SESSION['role'] ) || ( "guest" == $_SESSION['role'] ) ) ? "display:none" : "";  ?>" >
                                            <div class="form-group label-floating">
                                                <label class="control-label">Assigned Quota</label>
                                                <input required="required" name="quota" id="quota" type="number" value="<?php echo $arrUser['quota'];?>" class="form-control" >
                                            </div>
                                        </div>
                                        
                                        
                                    </div>

                                    <div class="row ">
                                        <label class="control-label">&nbsp;</label>
                                        <div class="col-md-6"  >

                                            <button type="button" id="add_customer" class="btn btn-primary pull-right"><?php echo ( true == isset($id) && $id != 0 ) ? "Update User" : "Create User"; ?></button>
                                        </div>
                                    </div>
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

<script>

    $(document).ready(function () {

        $("#add_customer").unbind("click").on("click", function () {

            var idcustomer = $("#customer_id").val();

            var username = $("#username").val();
            if (username === "") {
                alert("Username cannot be blank!!!");
                return;
            }
            var first_name = $("#first_name").val();
            if (first_name === "") {
                alert("First Name cannot be blank!!!");
                return;
            }
            var last_name = $("#last_name").val();
            if (last_name === "") {
                alert("Last Name cannot be blank!!!");
                return;
            }
            var email = $("#email").val();
            if (email === "") {
                alert("Email cannot be blank!!!");
                return;
            }
            var password = $("#password").val();
            if (password === "" && parseInt(idcustomer) === 0) {
                alert("Password cannot be blank!!!");
                return;
            }
            var contact = $("#contact").val();
            if (contact === "") {
                alert("Contact cannot be blank!!!");
                return;
            }
            var city = $("#city").val();
            if (city === "") {
                alert("City cannot be blank!!!");
                return;
            }

            var status = $("#status").val();
            if (status === "") {
                alert("Please select status!!!");
                return;
            }

            var customerselectzone = $("#customerselectzone").val();

            if (customerselectzone === "") {
                alert("Please Select Zone!!!");
                return;
            }


            var quota = 0;
            
            quota = $("#quota").val();

            var operation = "";

            if (parseInt(idcustomer) === 0) {
                operation = "add-customer";
            } else {
                operation = "edit-customer";
            }



            $.post("ajaxCall.php", {operation: operation, username: username, first_name: first_name, last_name: last_name, email: email, password: password, contact: contact, city: city, customerselectall: customerselectzone.join(","), customer_id: idcustomer, status: status, quota:quota}, function (data) {
                var jarr = JSON.parse(data);
                if (jarr.result === "error") {
                    alert(jarr.msg);
                } else {
                    alert(jarr.msg);
                    <?php if ($_SESSION['role'] == 'user') { ?>
                                            window.location.href = "manage_user.php";
                    <?php } else { ?>
                                            window.location.href = "manage_login_users.php";
                    <?php } ?>
                }
            });

        });

        $('#customerselectzone').change(function (e) {

            var select = $(this);
            list = select.data('prevstate');
            var val = select.val();
            if (list == null) {
                list = val;
            } else if (val.length == 1) {
                val = val.pop();
                var pos = list.indexOf(val);
                if (pos == -1)
                    list.push(val);
                else
                    list.splice(pos, 1);
            } else {
                list = val;
            }
            select.val(list);
            select.data('prevstate', list);
            changed = true;
        }).find('option').click(function () {
            if (!changed) {
                $(this).parent().change();
            }
            changed = false;
        });


        var list = null;
        $.each($('#customerselectzone option:selected'), function (e, v) {
            $(v).parent().change();
        });

    });

</script>

<?php include './layouts/footer.php'; ?>