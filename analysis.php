<?php
$page = 2;

include './layouts/header.php';

//$sqlSelectSites = "Select * from sites where status = 'active'  and  created_by = '{$_SESSION['user_id']}'  ";

if ($_SESSION['role'] == 'user') {
    $sqlSelectSites = "select * from sites  where created_by = '{$_SESSION['user_id']}'  order by id desc ";
} else if ($_SESSION['role'] == 'admin') {
    $sqlSelectSites = "select * from sites    order by id desc ";
} else if ($_SESSION['role'] == 'guest') {
    $sqlSelectSites = "select sites.* from sites inner join zone on zone.number = sites.number where zone.unique_id in ('{$_SESSION['zone']}') order by sites.id desc ";
}

$arrSites = find_by_sql($sqlSelectSites);

$param = $_SESSION['cumulative'];

$param_map = "";
$resHeaderMap = find_by_sql("Select * from header_map where header = '$param' and status = 'active' ");
if (sizeof($resHeaderMap) > 0) {
    $resHeaderMap = $resHeaderMap[0];
    $param_map = $resHeaderMap['name'];
} else {
    $param_map = $param;
}

 
?>


<style>
    .iframe{
        overflow: scroll;
    }

    .action{
        cursor: pointer;
    }

    th, td{
        text-align: center;
    }


    .select2{
        margin-bottom: 8.5px;
    }

</style>

<section class="content">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-7 col-md-6 col-sm-12">
                <h2>Embark Dashboard</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="main.php"><i class="zmdi zmdi-home"></i> Embark </a></li>
                    <li class="breadcrumb-item active">Plant Status</li>
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

            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card widget_2 big_icon zmdi-select-all">
                    <div class="body ">
                        <h6>SELECT SITE</h6>
                        <select class="form-control show-tick ms select2" id="site_select" data-placeholder="Select">
                            <?php
                            foreach ($arrSites as $keySites => $valSites) {
                                ?>
                                <option  value="<?php echo $valSites['id']; ?>" ><?php echo $valSites['name']; ?></option>

                            <?php } ?>

                        </select>

                        <small class="info"> Sites added </small>

                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card widget_2 big_icon zmdi-chart">
                    <div class="body">
                        <h6><?php echo $param ?></h6>
                        <h2  id="site_kld" ><?php echo $value; ?><small class="info"></small></h2>
                        <small><?php echo $param_map; ?></small>

                    </div>
                </div>
            </div>

        </div>


        <div class="row clearfix"  >
            <div class="col-lg-12">
                <div class="card">
                    <div class="header">
                        <h2><strong><li class="zmdi zmdi-trending-up">&nbsp;</li>SITE</strong> TRENDS : <div style="display:inline" id="site_time" ></div></h2>

                    </div>

                    <div class="body">

                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs p-0 mb-3 nav-tabs-success" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#quality"> <i class="zmdi zmdi-arrow-right-top"></i> Quality Parameters</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#quantity"><i class="zmdi zmdi-brightness-high"></i> Quantity Parameters</a></li> 
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane in active" id="quality"> <b>Quality Parameters</b>
                                <div class="row">
                                    <div class="table-responsive">
                                        <table  class="table table-bordered table-striped table-hover dataTable js-exportable" data-page-length="10"  >
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="30%" scope="col">PARAMETER</th>
                                                    <th width="10%" scope="col">DAY AVERAGE VALUE</th>
                                                    <th width="10%" scope="col">MIN VALUE</th>
                                                    <th width="10%" scope="col">MAX VALUE</th>
                                                    <th width="10%" scope="col">VIEW TREND</th>
                                                </tr>
                                            </thead>
                                            <tbody  id="table_params_quantity" >

                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="quantity"> <b>Quantity Parameters</b>
                                <div class="row">
                                    <div class="table-responsive">
                                        <table  class="table table-bordered table-striped table-hover dataTable js-exportable" data-page-length="10"  >
                                            <thead class="thead-light">
                                                <tr>

                                                    <th width="30%" scope="col">PARAMETER</th>
                                                    <th width="10%" scope="col">DAY VALUE</th>
                                                    <th width="10%" scope="col">INITIAL VALUE</th>
                                                    <th width="10%" scope="col">FINAL VALUE</th>
                                                    <th width="10%" scope="col">VIEW TREND</th>
                                                </tr>
                                            </thead>
                                            <tbody  id="table_params_quality" >

                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>


                </div>
            </div>
        </div>

        <div class="row clearfix trends"  style="display:none" >
            <div class="col-lg-12">
                <div class="card">
                    <div class="header">
                        <h2><strong><li class="zmdi zmdi-trending-up">&nbsp;</li>PARAMETER</strong> TRENDS  &nbsp; <img width="40px" id="loading" style="display:none" src="./assets/images/loading.gif" > </h2>
                        <ul class="header-dropdown">
                            <li class="dropdown"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="zmdi zmdi-more"></i> </a>
                                <ul class="dropdown-menu dropdown-menu-right slideUp">
                                    <!--<li><a class="duration" href="javascript:void(0);" data-attr="DAY">Today ( HOURLY )</a></li>-->
                                    <!--<li><a class="duration" href="javascript:void(0);" data-attr="WEEK"  >7 DAYS</a></li>-->
                                    <!--<li><a class="duration" href="javascript:void(0);" data-attr="MONTH"  >30 DAYS</a></li>-->
                                    <!--<li><a class="duration" href="javascript:void(0);" data-attr="QUARTER"  >90 DAYS</a></li>-->
                                    
                                     <li><a class="duration" href="javascript:void(0);" data-attr="DAY">In Day</a></li>
                                    <li><a class="duration" href="javascript:void(0);" data-attr="DAILY"  >DAILY</a></li>
                                    <li><a class="duration" href="javascript:void(0);" data-attr="WEEK"  >WEEKLY</a></li>
                                    <li><a class="duration" href="javascript:void(0);" data-attr="MONTHLY"  >MONTHLY</a></li> 
                                </ul>
                            </li>

                        </ul>
                    </div>


                    <div class="body">
                        <div class="chart" id="echart-rainfall" style="height: 400px;"></div>
                        <input type="hidden" name="parameter" id="parameter"  />
                    </div>

                </div>
            </div>
        </div>


    </div>
</section>
<?php include './layouts/scripts.php'; ?>

<script>
    var chart_trends = null;
    $(document).ready(function () {


        // Disable inspect element
        $(document).bind("contextmenu", function (e) {
            e.preventDefault();
        });
        $(document).keydown(function (e) {
            if (e.which === 123) {
                return false;
            }
        });

        $('.select2').select2();
        setTimeout(function ()
        {
            var $contents = $('#frame').contents();
            $contents.scrollTop($contents.height());
        }, 3000);


        chart_trends = getChart("echart-rainfall");

//        chart_trends.setOption(getOptions("DO", [2.6, 5.9, 9.0, 26.4, 28.7, 70.7, 175.6, 182.2, 48.7, 18.8, 6.0, 2.3], ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec']), true);
//        }
        $(window).on('resize', function () {
            chart_trends.resize();
        });


        $("#site_select").on("change", function () {
            
            $(".trends").hide();
            var sid = $(this).val();
            

            $.post("ajaxCall.php", {operation: "site-kld", id: sid}, function (data) {
                console.log(data);
                var jarr = $.parseJSON(data);
                if (jarr.result === "success") {
                    var cumulative = jarr.cumulative;
                    var variable = jarr.variable;
                    $("#table_params_quality").html(cumulative);
                    $("#table_params_quantity").html(variable);
                    $("#site_kld").text(jarr.kld);
                    $("#site_time").text(jarr.rec_date);
                    setAction();
                } else {
                    $("#site_kld").text(0);
                    var strTbody = "";

                    strTbody += "<tr><td colspan='5'> No Data </td></tr>";
                    $("#table_params_quality").html(strTbody);
                    $("#table_params_quantity").html(strTbody);
                    setAction();
                }
            });

        });

        $("#site_select").trigger("change");


        $(".duration").on("click", function () {
            $("#loading").css("display", "inline");
            //    alert("");
            var duration = $(this).attr("data-attr");
            var param = $("#parameter").val();
            var id = $("#site_select").val();
          //  var opr =  "get-parameter-trend";// "get-parameter-trend";
            if( duration == "DAY" ){
                var opr =  "get-parameter-trend";// "get-parameter-trend";
            }else{
                var opr = "get-trend";
            }

            $.post("ajaxCall.php", {operation: opr, param: param, duration: duration, id: id}, function (data) {

                var jarr = $.parseJSON(data);
                $("#loading").css("display", "none");
                if (jarr.result === "success") {
                    
                    var rotate = 90;
                    if( duration == "WEEK" ){
                        rotate = 0;
                    }
                    

                    chart_trends.clear();
                    chart_trends.setOption(getOptions(param, jarr.values, jarr.period, rotate), true);
                    chart_trends.resize();
                } else {

                }


            });


        });


    });


    function setAction() {
        $(".action").on("click", function () {

            $(".trends").css("display", "block");

            $('html, body').animate({
                scrollTop: $("#echart-rainfall").offset().top
            }, 2000);

            var param = $(this).attr("data-param");

            $("#parameter").val(param);

            var id = $("#site_select").val();
            
            $.post("ajaxCall.php", {operation: "get-parameter-trend", param: param, duration: "DAY", id: id}, function (data) {

                var jarr = $.parseJSON(data);

                if (jarr.result === "success") {
                    chart_trends.clear();
                    chart_trends.setOption(getOptions(param, jarr.values, jarr.period), true);
                    chart_trends.resize();
                    
                } else {
                     
                }


            });


        });
    }


    function getOptions(param, data, period , rotate = 90) {

        var option_trends = {};
        option_trends = {
            legend: {
                data: [param],
                bottom: '0',
            },
            grid: {
                left: '7%',
                right: '0%',
                top: '2%',
                bottom: '20%',
            },
            tooltip: {
                trigger: 'item'
            },
            calculable: true,

            xAxis: {
                type: 'category',
                data: period,

                axisLine: {
                    lineStyle: {
                        color: Aero.colors["gray-lightest"],
                    }
                },
                axisLabel: {
                    color: Aero.colors["gray"],
                    rotate: rotate,
                    top: 20
                }
            },
            yAxis: {
                type: 'value',
                splitLine: {
                    lineStyle: {
                        color: Aero.colors["gray-lightest"],
                    }
                },
                axisLine: {
                    lineStyle: {
                        color: Aero.colors["gray-lightest"],
                    }
                },
                axisLabel: {
                    color: Aero.colors["gray"],
                    left:120
                }
            },
            series: [

                {
                    name: param,
                    type: 'bar',
                    color: '#288cff',
                    data: data,
//                    markPoint: {
//                        data: [
//                            {name: 'Highest', value: 182.2, xAxis: 7, yAxis: 183},
//                            {name: 'Minimum', value: 2.3, xAxis: 11, yAxis: 3}
//                        ]
//                    },
                    markLine: {

                        data: [
                            {type: 'average', name: 'Average', label: "Average"}
                        ]
                    }
                }
            ]
        };

        return option_trends;
    }


    function getChart(id) {
        var dom = document.getElementById(id);
        return echarts.init(dom);
    }





</script>

<?php include './layouts/footer.php'; ?>