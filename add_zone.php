<?php
$page = 10;
include './layouts/header.php';

$query = "select number, name from sites where created_by = '{$_SESSION['user_id']}'  order by id desc ";

$allNumbers = find_by_sql($query);
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
                    <li class="breadcrumb-item active">Add Zone</li>
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
                        <h2><strong><li class="zmdi zmdi-assignment-account">&nbsp;</li>Add</strong> Zone</h2>
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
                                <form onsubmit="return false;" action="#" method="post"  >
                                    <div class="row">                                
                                        <div class="col-md-6"> 
                                            <div class="form-group label-floating">
                                                <label class="control-label">Zone Name</label>
                                                <input id="textcluster" name="zonename" type="text" class="form-control">
                                            </div>
                                        </div>                                
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Select Multiple Sites (Click on a Site)</label>
                                                <select multiple="multiple" class="ms form-widget"  name="zoneselectall" id="zoneselectall">
                                                    <?php
                                                    foreach ($allNumbers as $key => $val) {
                                                        ?>
                                                        <option value="<?= $val['number']; ?>"><?= ( $val['number'] . " ( " . $val['name'] . " ) "); ?></option>                            
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>


                                    </div>

                                    <button type="submit" id="clusterset" class="btn btn-primary pull-right">Create Zone</button>
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

<script src="assets/plugins/multi-select/js/jquery.multi-select.js"></script> <!-- Multi Select Plugin Js --> 

<script>

    $(document).ready(function () {

//        $('#zoneselectall').change(function (e) {
//            var select = $(this);
//            var list = select.data('prevstate');
//            var val = select.val();
//            if (list == null) {
//                list = val;
//            } else if (val.length == 1) {
//                val = val.pop();
//                var pos = list.indexOf(val);
//                if (pos == -1)
//                    list.push(val);
//                else
//                    list.splice(pos, 1);
//            } else {
//                list = val;
//            }
//            select.val(list);
//            select.data('prevstate', list);
//            changed = true;
//        }).find('option').click(function () {
//            if (!changed) {
//                $(this).parent().change();
//            }
//            changed = false;
//        });
//        
        $('#zoneselectall').multiSelect({selectableOptgroup: false});

        $("#clusterset").unbind("click").click(function (event) {
            event.preventDefault();
            
            var cumulativeselected = $("#zoneselectall").val();

            var id = $("#idcluster").val();
            if ("" === $("#textcluster").val()) {
                alert("Cluster Name cannot be blank!");
                return;
            }

            var zoneperation = "";

            zoneperation = "add-zone";

            $.post("ajaxCall.php", {operation: zoneperation, zonename: $("#textcluster").val(), numbers: cumulativeselected.join(","), id: id}, function (data) {

                var response = data.trim();

                var jarr = $.parseJSON(response);
                if (jarr.result === "error") {
                    alert(jarr.msg);
                } else {
                    alert(jarr.msg);
                    window.location = "manage_zone.php";
                }
            });
        });




    });


</script>
<?php include './layouts/footer.php'; ?>