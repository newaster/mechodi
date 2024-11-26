<?php
$page = 18;
include './layouts/header.php';
 
$id = (true == isset($_REQUEST['id']) && "" != $_REQUEST['id'] ) ? $_REQUEST['id'] : 0;
$user_id = $_SESSION['user_id'];

if ($id != 0) {
    
    $selectClu = "Select * from cluster_columns where id = '$id' and updated_by = '$user_id' ";

    $cluster = find_by_sql($selectClu);

    if (sizeof($cluster) < 1) {
        echo "<script>alert('Column not found!!!');</script>";
        redirect("manage_columns.php");
        exit;
    } else {
        $cluster = $cluster[0];
        $select_clu_name = $cluster['name'];
        $arrSelectHeaders = explode(",", $cluster['headers']);
        $cluster_id = $cluster['cluster'];
    }
}

if (isset($_REQUEST['name'])) {
    $cluid = ( true == isset($_REQUEST["cluid"]) && "" != $_REQUEST["cluid"]) ? $_REQUEST["cluid"] : "";
    $cluster_name = ( true == isset($_REQUEST['name']) && "" != $_REQUEST['name'] ) ? $_REQUEST['name'] : "";
    $headers = ( true == isset($_REQUEST['headers']) && "" != $_REQUEST['headers'] ) ? $_REQUEST['headers'] : "";

    $selectClu = "Select * from cluster_columns where name = '$cluster_name' and updated_by = '$user_id' ";

    $resClus = find_by_sql($selectClu);
    if (sizeof($resClus) > 0) {
        $created_date = date("Y-m-d H:i:s");
        $sqlColumns = " Update `cluster_columns` 
                set  `cluster` =  '$cluid', `headers` = '$headers'   where id = '$id' ";
        $db->query($sqlColumns);

        if ($db->affected_rows() > 0) {
            $session->msg("s", "Column Updated succefully.");
            redirect("manage_columns.php");
        } else {
            $session->msg("s", "Unable to Update column!!!.");
            redirect("manage_columns.php");
        }
    } else {
        $session->msg("s", "Column doesn't exists!!!.");
        redirect("manage_columns.php");
    }
}

$sqlSelectSites = "Select * from cluster where status = 'active'  and  updated_by = '{$_SESSION['user_id']}'  ";
$arrSites = find_by_sql($sqlSelectSites);

$arrNum = array();

foreach ($arrSites as $keySites => $valSites) {
    $arrNum[$valSites['number']] = $valSites['number'];
}
?>
<link rel="stylesheet" href="assets/plugins/jquery-steps/jquery.steps.css">
<style>
    .jvectormap-legend-tick{
        width: 50px !important;

    }
    .wizard > .content{
        min-height: 24em;
    }

</style>
<section class="content">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-7 col-md-6 col-sm-12">
                <h2>Embark Dashboard</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="main.php"><i class="zmdi zmdi-home"></i> Embark </a></li>
                    <li class="breadcrumb-item active">Edit Column</li>
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
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="header">
                        <h2><strong>Create</strong> Column</h2>
                        <ul class="header-dropdown">

                        </ul>                        
                    </div>

                    <div class="body">
                        <form id="cluster_form"  name="cluster_form" method="post"   >
                            <div id="wizard_horizontal">

                                <h2>Enter Column Name</h2>
                                <section  style="width:100%;height: 100%;">

                                    <center> <h2>Enter Column Name</h2><input required="required" class="form-control col-3 form-widget" type="text" name="name" value="<?php echo $select_clu_name; ?>" id="column_name"  ></center>

                                </section>

                                <h2>Select Cluster</h2>
                                <section  style="width:100%">
                                    <input id="id" type="hidden" name="id" value="<?php echo $id; ?>"  />
                                    <center> <select  name="cluid" onchange="on_cluster_select(this);" style="width: 20%" id="select_sites"  required class="ms form-widget form-control"   >
                                            <option value="" >Select Cluster</option>
                                            <?php foreach ($arrSites as $keySite => $valSite) { ?>
                                                <option <?php echo ( $cluster_id == $valSite['id'] ) ? "selected='selected'" : ""; ?> value="<?php echo $valSite['id']; ?>"><?php echo $valSite['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </center>
                                </section>
                                <h2>Select Headers to SUM</h2>
                                <section style="width:100%;height: 100%;"  >
                                    <center>

                                        <p> <b>Select Headers</b> </p> 
                                        <input id="hid_headers" type="hidden" name="headers" value="<?php echo implode(",", $arrSelectHeaders); ?>"  />
                                        <select   id="headers"  required class="ms form-widget select2" multiple="multiple"   >



                                        </select>

                                    </center>

                                </section>


                            </div>
                        </form>
                    </div>
                </div>
            </div>



        </div>

    </div>
</section>
<?php include './layouts/scripts.php'; ?>

<script src="assets/plugins/jquery-steps/jquery.steps.js"></script> <!-- JQuery Steps Plugin Js -->
<script src="assets/plugins/multi-select/js/jquery.multi-select.js"></script> <!-- Multi Select Plugin Js --> 
<script>

                                        function on_cluster_select(event) {
                                            $.post("ajaxCall.php", {operation: "get-selected-cluster-headers", id: $(event).val()}, function (data) {

                                                var jarr = $.parseJSON(data);
                                                var options = "";
                                                var arrOption = jarr[0].split(",");
                                                $.each(arrOption, function (e, v) {
                                                    var arrSelected = $("#hid_headers").val().split(",");
                                                    var selected = "";
                                                    if ($.inArray(v, arrSelected)) {
                                                        selected = "selected='selected'";
                                                    }
                                                    options += "<option " + selected + "  value='" + v + "'  >" + v + "</option>";
                                                });

                                                console.log(options);

                                                $("#headers").html(options);
                                                $('.select2').multiSelect({selectableOptgroup: false});
                                            });
                                        }

                                        $(document).ready(function () {

                                            $("#select_sites").trigger("change");

                                            $('#wizard_horizontal').steps({
                                                headerTag: 'h2',
                                                bodyTag: 'section',
                                                transitionEffect: 'slideLeft',
                                                onInit: function (event, currentIndex) {
                                                    setButtonWavesEffect(event);
                                                },
                                                onStepChanged: function (event, currentIndex, priorIndex) {
                                                    setButtonWavesEffect(event);
                                                }
                                            });


                                        });
                                        function setButtonWavesEffect(event) {
                                            $(event.currentTarget).find('[role="menu"] li a').removeClass('waves-effect');
                                            $(event.currentTarget).find('[role="menu"] li:not(.disabled) a').addClass('waves-effect');
                                        }


                                        function validate_submit() {

                                            var column_name = $("#column_name").val();
                                            if (column_name === "") {
                                                alert("Column name cannot be blank!!");
                                                return false;
                                            }

                                            var selectedSites = $("#select_sites").val();
                                            if (selectedSites.length === 0) {
                                                alert("Sites cannot be blank!!");
                                                return false;
                                            }

                                            $("#sites").val(selectedSites);
                                            var headers = $("#headers").val();
                                            $("#hid_headers").val(headers);
                                            if (headers.length === 0) {
                                                alert("Please select headers!!!");
                                                return false;
                                            }

                                            $("#cluster_form").submit();
                                            return true;
                                        }


</script>


<?php include './layouts/footer.php'; ?>