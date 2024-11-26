<?php
$page = 10;
include './layouts/header.php';
$id = (int) $_GET['id'];

$selectZone = "Select * from zone where unique_id = '$id'  ";
$arrZone = find_by_sql($selectZone);
if (sizeof($arrZone) < 1) {
    echo "<script>alert('No Zone found!!!');</script>";
    exit;
}

$arrZoneNumber = array();
$zonename = "";
foreach ($arrZone as $keyZone => $valZone) {
    $arrZoneNumber[] = $valZone['number'];
    $zonename = $valZone['name'];
}

$query = "select number, name from sites where created_by = '{$_SESSION['user_id']}'  order by id desc ";

$allNumbers = find_by_sql($query);
//$allNumbers = $allNumbers[0];
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
                <h2>Embark Dashboard</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="main.php"><i class="zmdi zmdi-home"></i> Embark </a></li>
                    <li class="breadcrumb-item active">Edit Zone</li>
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
                        <h2><strong>Edit</strong> Zone</h2>
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
                                <form>
                                    <div class="row">                                
                                        <div class="col-md-6"> 
                                            <div class="form-group label-floating">
                                                <label class="control-label">Zone Name</label>
                                                <input  id="textcluster" name="zonename" type="text" value="<?php echo $zonename; ?>" class="form-control">
                                            </div>
                                        </div>                                
                                    </div>
                                    <input type="hidden" value="<?php echo $id; ?>"  name="idcluster" id="idcluster"  />
                                    <input type="hidden" value="<?php echo implode(",", $arrZoneNumber); ?>"  name="previous_numbers" id="previous_numbers" />
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Select Multiple Sites (Click on a Site)</label>
                                                <select multiple="multiple" class="ms form-widget"   name="zoneselectall" id="zoneselectall">
                                                    <?php
                                                    foreach ($allNumbers as $key => $val) {
                                                        $selected = "";
                                                        if (in_array($val['number'], $arrZoneNumber)) {
                                                            $selected = " selected='selected' ";
                                                        }
                                                        ?>
                                                        <option value="<?= $val['number']; ?>" <?php echo $selected; ?>  ><?= ( $val['number'] . " ( " . $val['name'] . " ) "); ?></option>                            
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>


                                    </div>

                                    <button type="submit" id="clusterset" class="btn btn-primary pull-right">Edit Zone</button>
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
//            console.log($(this));
//            var select = $(this);
//            list = select.data('prevstate');
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


        var list = null;
        $.each($('#zoneselectall option:selected'), function (e, v) {
            $(v).parent().change();
        });
        
        $('#zoneselectall').multiSelect({selectableOptgroup: false});

        $("#clusterset").unbind("click").click(function (event) {
            event.preventDefault();

            var zoneselected = $("#zoneselectall").val();

            var id = $("#idcluster").val();
            if ("" === $("#textcluster").val()) {
                alert("Cluster Name cannot be blank!");
                return;
            }

            var zoneperation = "";

            zoneperation = "edit-zone";

            $.post("ajaxCall.php", {operation: zoneperation, zonename: $("#textcluster").val(), numbers: zoneselected.join(","), id: id, previous_numbers: $("#previous_numbers").val()}, function (data) {

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