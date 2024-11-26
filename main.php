<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

include './layouts/header.php';

// Determine the query based on the role
switch ($_SESSION['role']) {
    case 'user':
        $query = "SELECT * FROM sites WHERE created_by = '{$_SESSION['user_id']}' ORDER BY id DESC";
        break;
    case 'admin':
        $query = "SELECT * FROM sites ORDER BY id DESC";
        break;
    case 'guest':
        $query = "SELECT sites.* 
                  FROM sites 
                  INNER JOIN zone ON zone.number = sites.number 
                  WHERE zone.unique_id IN ('{$_SESSION['zone']}') 
                  ORDER BY sites.id DESC";
        break;
}

$allSites = find_by_sql($query);

$arrStatus = array("ON" => "active", "OFF" => "closed");
$online = 0;
$offline = 0;
$arrSiteStatus = [];
$arrNumbers = array_column($allSites, 'number');

// Batch fetch all embark_data rows for today's date in one query
if (!empty($arrNumbers)) {
    $numberList = implode("','", $arrNumbers);
    $embarkDataQuery = "
        SELECT number 
        FROM embark_data 
        WHERE number IN ('$numberList') 
          AND DATE(datetime) = '" . date("Y-m-d") . "'";
    $embarkData = find_by_sql($embarkDataQuery);
    $onlineNumbers = array_column($embarkData, 'number');

    foreach ($arrNumbers as $number) {
        $arrSiteStatus[$number] = in_array($number, $onlineNumbers) ? "ON" : "OFF";
        if ($arrSiteStatus[$number] === "ON") {
            $online++;
        } else {
            $offline++;
        }
    }
}

// Calculate percentages
$total = max($online + $offline, 1); // Prevent division by zero
$perOnline = ($online / $total) * 100;
$perOffline = 100 - $perOnline;

// Prepare site map data
$arrSitesMap = array_map(function ($site) use ($arrStatus, $arrSiteStatus) {
    return [
        "name" => $site['name'],
        "coords" => [$site['longitude'], $site['latitude']],
        "status" => $arrStatus[$arrSiteStatus[$site['number']] ?? "OFF"]
    ];
}, $allSites);

// Get the parameter for cumulative data
$param = $_SESSION['cumulative'];
$param_map = $param;
$resHeaderMap = find_by_sql("SELECT * FROM header_map WHERE header = '$param' AND status = 'active'");
if (!empty($resHeaderMap)) {
    $param_map = $resHeaderMap[0]['name'];
}

// Prepare date range
$days = 5;
$dateArr = getLastNDays($days, "Y-m-d");

// Batch fetch min and max data for the date range in one query
if (!empty($arrNumbers)) {
    $selectRec = "
        SELECT 
            MAX(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param_map', COLNAME))) AS max, 
            MIN(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param_map', COLNAME))) AS min, 
            DATE(datetime) AS rec_date 
        FROM embark_data 
        WHERE number IN ('$numberList') 
          AND DATE(datetime) IN ('" . implode("','", $dateArr) . "') 
        GROUP BY DATE(datetime) 
        ORDER BY rec_date DESC";
    $resLast = find_by_sql($selectRec);

    $valData = [];
    foreach ($resLast as $row) {
        $valData[$row['rec_date']] = $row['max'] - $row['min'];
    }
}

// Prepare data for KLD steps
$kldStepVals = [];
$kldStepDate = [];
foreach ($dateArr as $vDate) {
    $kldStepVals[] = isset($valData[$vDate]) ? number_format($valData[$vDate], 2, ".", "") : 0;
    $kldStepDate[] = date("M-d", strtotime($vDate));
}




$latestData = null;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['site'])) {
    $siteNumber = $_POST['site']; // Get the selected site number

    // Fetch the latest data from the embark_data table for the selected site
    $query = "
        SELECT * 
        FROM embark_data 
        WHERE number = '$siteNumber' 
        ORDER BY datetime DESC 
        LIMIT 1";
    $latestData = find_by_sql($query);

    if (!empty($latestData)) {
        $latestData = $latestData[0]; // Get the first row
    } else {
        $error = "No data found for the selected site.";
    }
    
    $colValues = explode(",", $latestData['COLVALUES']);
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
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="header">
                            <h2> <strong>Select Site</strong></h2>
                            <!--<?php print_r($colValues);?>-->
                        </div>
                        <form method="POST">
                            <div class="body">
                                <div class="row clearfix">
                                    <div class="col-sm-6">
                                        <select class="form-control show-tick" name="site">
                                            <option value="">Select Site</option>
                                            <?php foreach ($allSites as $site) { ?>
                                                <option value="<?php echo $site['number']; ?>"><?php echo $site['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        
                                    </div>
                                    
                                    <?php if($colValues[3]!=""){?>
                                    <div class="col-sm-3">
                                        
                                        <span class="btn btn-danger"><b>Last Data: <?php echo $colValues[3]." ".$colValues[4]; ?> </b></span>
                                        
                                    </div>
                                    
                                    <?php } 
                                    else
                                    {
                                        $colValues[6] = 100;
                                        $colValues[7] = 100;
                                        $colValues[8] = 100;
                                        $colValues[9] = 100;
                                        $colValues[10] =100;
                                        $colValues[11] = 100;
                                        $colValues[12] = 100;
                                        $colValues[13] = 1;
                                        $colValues[14] = 1;
                                        $colValues[15] = 1;
                                    }
                                    ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        
        <div class="row clearfix">
                <div class="col-lg-3 col-md-4 col-sm-6 col-6 text-center">
                    <div class="card">
                        <div class="header">
                            <h2><strong><li class="zmdi zmdi-stack-overflow">&nbsp;</li>Site</strong> TDS</h2>
                        </div>
                        <div class="body">                            
                            <input type="text" class="knob" value="<?php echo is_nan($colValues[6]) ? 100 : $colValues[6]; ?>" data-linecap="round" data-width="100" data-height="100" data-thickness="0.08" data-fgColor="#00adef" readonly>
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
                            <input type="text" class="knob" value="<?php echo is_nan($colValues[7]) ? 100 : $colValues[7]; ?>" data-linecap="round" data-width="100" data-height="100" data-thickness="0.08" data-fgColor="#ee2558" readonly>
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
                            <input type="text" class="knob" value="<?php echo is_nan($colValues[8]) ? 100 : $colValues[8]; ?>" data-linecap="round" data-width="100" data-height="100" data-thickness="0.08" data-fgColor="#8f78db" readonly>
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
                            <input type="text" class="knob" value="<?php echo is_nan($colValues[9]) ? 100 : $colValues[9]; ?>" data-linecap="round" data-width="100" data-height="100" data-thickness="0.08" data-fgColor="#f67a82" readonly>
                            <p>Level</p>
                            
                        </div>
                    </div>
                </div>
            </div>
        
        <div class="row clearfix">
                <div class="col-md-12 col-lg-4">
                    <div class="card">
                        <div class="header">
                            <h2><strong><li class="zmdi zmdi-stack-overflow">&nbsp;</li>Tap</strong> 1</h2>
                        </div>
                        <div class="body">
                            <h3 class="mt-0 mb-0"><?php echo (float)$colValues[10]*(float)$colValues[13]; ?> Liters</h3>
                            <p class="text-muted">Water Dispense </p>
                            <div class="progress">
                                <div class="progress-bar l-blue" role="progressbar" aria-valuenow="<?php echo $colValues[13] ?>" aria-valuemin="0" aria-valuemax="100" style="width: 45%;"></div>
                            </div>
                            <small>Quantity : <?php echo $colValues[13] ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-4">
                    <div class="card">
                        <div class="header">
                            <h2><strong><li class="zmdi zmdi-stack-overflow">&nbsp;</li>Tap</strong> 2</h2>
                        </div>
                        <div class="body">
                            <h3 class="mt-0 mb-0"><?php echo (float)$colValues[11]*(float)$colValues[14]; ?> Liters</h3>
                            <p class="text-muted">Water Dispense </p>
                            <div class="progress">
                                <div class="progress-bar l-blue" role="progressbar" aria-valuenow="<?php echo $colValues[14] ?>" aria-valuemin="0" aria-valuemax="100" style="width: 45%;"></div>
                            </div>
                            <small>Quantity : <?php echo $colValues[14] ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-4">
                    <div class="card">
                        <div class="header">
                            <h2><strong><li class="zmdi zmdi-stack-overflow">&nbsp;</li>Tap</strong> 3</h2>
                        </div>
                        <div class="body">
                            <h3 class="mt-0 mb-0"><?php echo (float)$colValues[12]*(float)$colValues[15]; ?> Liters</h3>
                            <p class="text-muted">Water Dispense </p>
                            <div class="progress">
                                <div class="progress-bar l-blue" role="progressbar" aria-valuenow="<?php echo $colValues[15] ?>" aria-valuemin="0" aria-valuemax="100" style="width: 45%;"></div>
                            </div>
                            <small>Quantity : <?php echo $colValues[15] ?></small>
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