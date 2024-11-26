<?php


error_reporting(E_ALL);
ini_set('display_errors', '1');

include './layouts/header.php';


 
if ($_SESSION['role'] == 'user') {
    $query = "select * from sites  where created_by = '{$_SESSION['user_id']}'  order by id desc ";
} else if ($_SESSION['role'] == 'admin') {
    $query = "select * from sites    order by id desc ";
} else if ($_SESSION['role'] == 'guest') {
    $query = "select sites.* from sites inner join zone on zone.number = sites.number where zone.unique_id in ('{$_SESSION['zone']}') order by sites.id desc ";
}

$allSites = find_by_sql($query);

 

$arrStatus = array("ON" => "active", "OFF" => "closed");

$online = 0;
$offline = 0;

$arrSiteStatus = array();
$arrNumbers = array();

$time = microtime(true);

foreach ($allSites as $keySites => $valSites) {
    $arrNumbers[] = $number = $valSites['number'];
    $selectMbr = "Select * from embark_data where number = '$number' and DATE(datetime) = '" . date("Y-m-d") . "' Limit 1 ";
    $arrSites = find_by_sql($selectMbr);

    if (sizeof($arrSites) > 0) {
        $arrSiteStatus[$number] = "ON";
        $online++;
    } else {
        $arrSiteStatus[$number] = "OFF";
        $offline++;
    }
}


if (sizeof($arrNumbers) < 1) {
    $arrNumbers[] = "1";
}

$total = $online + $offline;

if($total == 0)
{
    $total = 1;
}

$perOnline = ( $online / $total ) * 100;
$perOffline = 100 - $perOnline;

$arrSitesMap = array();

$i = 0;
foreach ($allSites as $keySites => $valSites) {
    $arrSitesMap[$i]["name"] = $valSites['name'];
    $arrSitesMap[$i]["coords"] = array($valSites['longitude'], $valSites['latitude']);
    $arrStatus[$arrSiteStatus[$valSites['number']]];
    $arrSitesMap[$i]["status"] = $arrStatus[$arrSiteStatus[$valSites['number']]];
    $i++;
}

//print_r($arrSitesMap);

$datePrev = date("Y-m-d", strtotime(" -1 day"));

$days = 5;

$param = $_SESSION['cumulative'];
$param_map = "";
$resHeaderMap = find_by_sql("Select * from header_map where header = '$param' and status = 'active' ");
if (sizeof($resHeaderMap) > 0) {
    $resHeaderMap = $resHeaderMap[0];
    $param_map = $resHeaderMap['name'];
} else {
    $param_map = $param;
}

$dateArr = getLastNDays($days, "Y-m-d");

$kldStepVals = array();
$kldStepDate = array();
$valData = array();
 $selectRec = "Select   MAX(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) as max , MIN(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) as min, DATE(datetime) as rec_date  from embark_data where  number IN (  " . implode(",", $arrNumbers) . " ) and DATE(datetime) IN (  '" . implode("','", $dateArr) . "' ) group by DATE(DATETIME)  order by id desc    ";
 $resLast = find_by_sql($selectRec);

if (sizeof($resLast) > 0) {

    foreach ($resLast as $keyHr => $valLast) {
        $valData[$valLast["rec_date"]] = $valLast["max"] - $valLast["min"];
    }
}


foreach ($dateArr as $kDate => $vDate) {
    if (isset($valData[$vDate])) {
        $kldStepVals[$kDate] = number_format($valData[$vDate], 2, ".", "");
        $kldStepDate[$kDate] = date("M-d", strtotime($vDate));
    } else {
        $kldStepVals[$kDate] = 0;
        $kldStepDate[$kDate] = date("M-d", strtotime($vDate));
    }
}
?>

<style>
    .jvectormap-legend-tick{
        width: 50px !important;
    }

</style>
<section class="content">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-7 col-md-6 col-sm-12">
                <h2>Aster Dashboard</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="main.php"><i class="zmdi zmdi-home"></i> Aster </a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
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
                <div class="col-lg-3 col-md-4 col-sm-6 col-6 text-center">
                    <div class="card">
                        <div class="header">
                            <h2><strong><li class="zmdi zmdi-stack-overflow">&nbsp;</li>Site</strong> TDS</h2>
                        </div>
                        <div class="body">                            
                            <input type="text" class="knob" value="42" data-linecap="round" data-width="100" data-height="100" data-thickness="0.08" data-fgColor="#00adef" readonly>
                            <p>TDS</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 col-6 text-center">
                    <div class="card">
                        <div class="header">
                            <h2><strong><li class="zmdi zmdi-stack-overflow">&nbsp;</li>Site</strong> PH</h2>
                        </div>
                        <div class="body">                            
                            <input type="text" class="knob" value="81" data-linecap="round" data-width="100" data-height="100" data-thickness="0.08" data-fgColor="#ee2558" readonly>
                            <p>PH</p>
                            
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 col-6 text-center">
                    <div class="card">
                        <div class="header">
                            <h2><strong><li class="zmdi zmdi-stack-overflow">&nbsp;</li>Site</strong> Temp</h2>
                        </div>
                        <div class="body">                            
                            <input type="text" class="knob" value="62" data-linecap="round" data-width="100" data-height="100" data-thickness="0.08" data-fgColor="#8f78db" readonly>
                            <p>Temperature</p>
                            
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 col-6 text-center">
                    <div class="card">
                        <div class="header">
                            <h2><strong><li class="zmdi zmdi-stack-overflow">&nbsp;</li>Site</strong> Level</h2>
                        </div>
                        <div class="body">                            
                            <input type="text" class="knob" value="38" data-linecap="round" data-width="100" data-height="100" data-thickness="0.08" data-fgColor="#f67a82" readonly>
                            <p>Level</p>
                            
                        </div>
                    </div>
                </div>
            </div>
        

        <div class="row clearfix">
            <div class="col-md-12 col-lg-8">
                <div class="card">
                    <div class="header">
                        <h2><strong><li class="zmdi zmdi-stack-overflow">&nbsp;</li>Site</strong> Location</h2>
                        <ul class="header-dropdown">

                        </ul>                        
                    </div>
                    <div class="body">
                        <div id="world-map-markers" class="jvector-map"></div>                            
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="header">
                        <h2><strong><li class="zmdi zmdi-notifications">&nbsp;</li>SITE </strong>STATUS</h2>
                        <ul class="header-dropdown">

                        </ul>
                    </div>
                    <div class="body text-center"   style="margin-right:0px;">

                        <div class="table-responsive"  id="table_status"  style="display:none;margin-right:0px;"    >
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable" data-page-length="4" >
                                <thead>
                                    <tr>
                                        <th>SITE</th>
                                        <th data-breakpoints="xs">STATUS</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($allSites as $keySites => $valSites) { ?>

                                        <tr>
                                            <td><?php echo strtoupper($valSites['name']); ?></td>
                                            <td> <?php
                                                $number = $valSites['number'];
                                                echo $arrSiteStatus[$number];
                                                ?> </td>

                                        </tr>
                                    <?php } ?>


                                </tbody>
                            </table>
                        </div>
                        <div style="display: block" id="chart-pie" class="c3_chart d_distribution"></div>
                        <button class="btn btn-primary mt-4 mb-4" id="toggle_widget"  >View More</button>                            
                    </div>
                </div>
            </div>


        </div>
         
    </div>
</section>
<?php include './layouts/scripts.php'; ?> 
<script>

    $(document).ready(function () {


        $('.js-exportable').DataTable({
            "bFilter": false,
            "bInfo": false,
            "lengthChange": false,
            "pagingType": "simple"
//        dom: 'Bfrtip',

//        buttons: [
//            'copy', 'csv', 'excel', 'pdf', 'print'
//        ]
        });

        setTimeout(function () {
            "use strict";
            var plants = <?php echo json_encode($arrSitesMap); ?>;


            var mapData = {

                "IN": 2000000,
            };
            if ($('#world-map-markers').length > 0) {

                var option_map = {

                    map: 'in_mill',
                    backgroundColor: 'transparent',
                    borderColor: '#fff',
                    borderOpacity: 0.25,
                    color: '#e6e6e6',
                    regionStyle: {
                        initial: {
                            fill: '#60bafd'
                        },
                        selected: {
                            fill: '#f4b964'
                        }
                    },

                    markerStyle: {
                        initial: {
                            r: 5,
                            'fill': '#fff',
                            'fill-opacity': 1,
                            'stroke': '#000',
                            'stroke-width': 1,
                            'stroke-opacity': 0.4
                        },
                    },

                    markers: plants.map(function (h) {
                        return {name: h.name, latLng: h.coords}
                    }),

                    series: {
                        markers: [{
                                attribute: 'image',
                                scale: {
                                    closed: 'assets/images/map_pins/water_inactive.png',
                                    active: 'assets/images/map_pins/water_active.png'
                                },
                                values: plants.reduce(function (p, c, i) {
                                    p[i] = c.status;
                                    return p
                                }, {}),
                                legend: {
                                    horizontal: true,
                                    title: 'Plant Status',
                                    labelRender: function (v) {
                                        return {
                                            closed: 'OFF',
                                            active: 'ON'
                                        }[v];
                                    }
                                }
                            }]
                    },
                    hoverOpacity: null,
                    normalizeFunction: 'linear',
                    zoomOnScroll: true,
                    scaleColors: ['#000000', '#000000'],
                    selectedColor: '#000000',
                   // selectedRegions: ["IN-PB"],
                    enableZoom: true,
                    hoverColor: '#fff',
                    onLoad: function (event, map)
                    {

                        $('#world-map-markers').vectorMap('zoomIn');
                    },
                    onRegionClick: function (e, code, isSelected, selectedRegions) {

                        $('#world-map-markers').vectorMap('get', 'mapObject').setFocus({region: code});
                    }
                };

                $('#world-map-markers').vectorMap(option_map);
            }
        }, 800);


        setTimeout(function () {
            $('#world-map-markers').vectorMap('get', 'mapObject').setFocus({region: 'IN', scale: 5});

        }, 1000);


        var chart = c3.generate({
            bindto: '#chart-pie', // id of chart wrapper
            data: {
                columns: [
                    // each columns data
                    ['ONLINE', '<?php echo $perOnline; ?>'],
                    ['OFFLINE', '<?php echo $perOffline; ?>'],
                ],
                type: 'pie', // default type of chart
                colors: {
                    'ONLINE': Aero.colors["lime"],
                    'OFFLINE': Aero.colors["teal"],

                },
                names: {
                    // name of each serie
                    'ONLINE': 'ONLINE',
                    'OFFLINE': 'OFFLINE'
                }
            },
            axis: {
            },
            legend: {
                show: true, //hide legend
            },
            padding: {
                bottom: 0,
                top: 0
            },
        });


        var chart = c3.generate({
            bindto: '#chart-area-step', // id of chart wrapper
            data: {
                columns: [
                    ['<?php echo $param_map; ?>', <?php echo implode(",", $kldStepVals); ?>]
                ],
                type: 'area-step', // default type of chart
                colors: {
                    'data1': Aero.colors["blue"],

                },
                names: {
                    // name of each serie
                    'KLD': 'KLD'

                }
            },
            axis: {
                x: {
                    type: 'category',
                    // name of each category
                    categories: ['<?php echo implode("','", $kldStepDate); ?>']
                },
            },
            legend: {
                show: true, //hide legend
            },
            padding: {
                bottom: 0,
                top: 0
            },
        });

        $("#toggle_widget").on("click", function () {
            $("#chart-pie").slideToggle("show");

            $("#table_status").slideToggle("show");
        });


    });

</script>

<script src="assets/bundles/knob.bundle.js"></script>
<script src="assets/js/pages/charts/jquery-knob.js"></script>
<?php

function getLastNDays_temp($days, $format = 'd/m') {
    $m = date("m", strtotime("2020-04-10"));
    $de = date("d", strtotime("2020-04-10"));
    $y = date("Y", strtotime("2020-04-10"));
    $dateArray = array();
    for ($i = 0; $i <= $days - 1; $i++) {
        $dateArray[] = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));
    }
    return array_reverse($dateArray);
}

function getLastNDays($days, $format = 'd/m') {
    $m = date("m");
    $de = date("d");
    $y = date("Y");
    $dateArray = array();
    for ($i = 0; $i <= $days - 1; $i++) {
        $dateArray[] = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));
    }
    return array_reverse($dateArray);
}
?>

<?php include './layouts/footer.php'; ?>