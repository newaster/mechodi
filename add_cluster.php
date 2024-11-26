<?php
$page = 16;
include './layouts/header.php';

if (isset($_REQUEST['name'])) {

    $cluster_name = ( true == isset($_REQUEST['name']) && "" != $_REQUEST['name'] ) ? $_REQUEST['name'] : "";
    $sites = ( true == isset($_REQUEST['sites']) && "" != $_REQUEST['sites'] ) ? $_REQUEST['sites'] : "";
    $headers = ( true == isset($_REQUEST['headers']) && "" != $_REQUEST['headers'] ) ? $_REQUEST['headers'] : "";
    $cummulative = ( true == isset($_REQUEST['cummulative']) && "" != $_REQUEST['cummulative'] ) ? $_REQUEST['cummulative'] : "";
    $user_id = $_SESSION['user_id'];


    $selectClu = "Select * from cluster where name = '$cluster_name' and updated_by = '$user_id' ";

    $resClus = find_by_sql($selectClu);
    if (sizeof($resClus) == 0) {
        $insertRec = "Insert into cluster (  `name`, `numbers`, `headers`, `cumulativeheaders`,  `updated_by`, `created_date` ) ";
        $insertRec .= " VALUES ";
        $insertRec .= " ( '$cluster_name' , '$sites' , '$headers' , '$cummulative' , '$user_id' , NOW() ) ";

        if ($db->query($insertRec)) {
            
            redirect("manage_cluster.php");
        } else {
            redirect("manage_cluster.php");
        }
    } else {
        redirect("manage_cluster.php");
    }
}

$sqlSelectSites = "Select * from sites where status = 'active'  and  created_by = '{$_SESSION['user_id']}'  ";
$arrSites = find_by_sql($sqlSelectSites);




$arrNum = array();

foreach ($arrSites as $keySites => $valSites) {
    $arrNum[$valSites['number']] = $valSites['number'];
}
 
$selectRec = "Select DISTINCT embark_data.COLNAME from embark_data INNER JOIN sites on sites.number = embark_data.number where   LENGTH(embark_data.COLNAME) > 35 and sites.status = 'active'  and  sites.created_by = '{$_SESSION['user_id']}' order by embark_data.id asc   ";
$resLast = find_by_sql($selectRec);
 
$resLast = $resLast[0];

$arrAllHeaders = array();
$arrHeaders = array();
$arrHeaders = explode(",", $resLast["COLNAME"]);

$arrMBR = $arrHeaders;

$arrNoHeader = array('Number', 'Site', 'Device', 'Date', 'Time', 'Status');
$arrMBR = array_diff($arrMBR , $arrNoHeader);
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
                    <li class="breadcrumb-item active">Add Cluster</li>
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
                        <h2><strong>Create</strong> Cluster</h2>
                        <ul class="header-dropdown">

                        </ul>                        
                    </div>

                    <div class="body">
                        <form id="cluster_form"  name="cluster_form" method="post"   >
                            <div id="wizard_horizontal">

                                <h2>Enter Cluster Name</h2>
                                <section  style="width:100%;height: 100%;">

                                    <center> <h2>Enter Cluster Name</h2><input required="required" class="form-control col-3 form-widget" type="text" name="name" value="" id="cluster_name"  ></center>

                                </section>

                                <h2>Select Number</h2>
                                <section  style="width:100%">
                                    <input type="hidden" name="sites" value=""  id="sites" />
                                    <select id="select_sites"  required class="ms form-widget" multiple="multiple"   >
                                        <?php foreach ($arrSites as $keySite => $valSite) { ?>
                                            <option value="<?php echo $valSite['number']; ?>"><?php echo $valSite['name']; ?></option>
                                        <?php } ?> 

                                    </select>

                                </section>
                                <h2>Select Headers</h2>
                                <section style="width:100%;height: 100%;"  >
                                    <center>
                                        <div class="col-lg-6 col-md-6">
                                            <p> <b>Select Headers</b> </p>
                                            <input type="hidden" name="headers" value=""  id="hid_headers" />
                                            <select id="headers"  required class="form-control show-tick ms select2 form-widget" multiple data-placeholder="Select" >
                                                <?php foreach ($arrMBR as $keyMBR => $valMBR) { ?>
                                                    <option  value="<?php echo $valMBR; ?>"><?php echo strtoupper($valMBR); ?></option>
                                                <?php } ?> 
                                            </select>
                                        </div>
                                    </center>

                                </section>
                                <h2>Select Cumulative Header</h2>
                                <section  style="width:100%;height: 100%;" >

                                    <center>
                                        <div class="col-lg-6 col-md-6">
                                            <p> <b>Select Cumulative Header</b> </p>
                                            <input type="hidden" name="cummulative" value=""  id="hid_cummulative" />
                                            <select  id="cummulative"   required class="form-control show-tick ms select3 form-widget" multiple data-placeholder="Select" >
                                                <?php foreach ($arrMBR as $keyMBR => $valMBR) { ?>
                                                    <option value="<?php echo $valMBR; ?>"><?php echo strtoupper($valMBR); ?></option>
                                                <?php } ?> 
                                            </select>
                                        </div>
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

    $(document).ready(function () {



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


        $('#select_sites').multiSelect({selectableOptgroup: false});
        $('.select2').select2();
        $('.select3').select2();
    });

    function setButtonWavesEffect(event) {
        $(event.currentTarget).find('[role="menu"] li a').removeClass('waves-effect');
        $(event.currentTarget).find('[role="menu"] li:not(.disabled) a').addClass('waves-effect');
    }


    function validate_submit() {

        var cluster_name = $("#cluster_name").val();

        if (cluster_name === "") {
            alert("Cluster name cannot be blank!!");
            return false;
        }

        var selectedSites = $("#select_sites").val();

        if (selectedSites.length === 0) {
            alert("Sites cannot be blank!!");
            return false;
        }

        $("#sites").val(selectedSites);


        var headers = $("#headers").select2("val");
        if (headers.length === 0) {
            alert("Please select headers!!!");
            return false;
        }
        $("#hid_headers").val(headers);
        var cummulative = $("#cummulative").select2("val");
        if (cummulative.length === 0) {
            alert("Please Cummulative Headers!!!");
            return false;
        }
        $("#hid_cummulative").val(cummulative);


        $("#cluster_form").submit();
        return true;
    }


</script>


<?php include './layouts/footer.php'; ?>