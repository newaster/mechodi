<?php
$page = 4;
include './layouts/header.php';

$id = (true == isset($_REQUEST['id']) && "" != $_REQUEST['id'] ) ? $_REQUEST['id'] : 0;
$userid = (true == isset($_SESSION['user_id']) && "" != $_SESSION['user_id'] ) ? $_SESSION['user_id'] : 0;
$querySites = "select * from sites where id = '$id' and created_by = '$userid' Limit 1";
$sitesdata = find_by_sql($querySites);
$allSites = array();
if (sizeof($sitesdata) > 0) {
    $allSites = $sitesdata[0];
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
                    <li class="breadcrumb-item active"><?php echo ( sizeof($allSites) > 0 ) ? "Edit Site" : "Add Site"; ?></li>
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
                        <h2><strong><li class="zmdi zmdi-square-o">&nbsp;</li><?php echo ( sizeof($allSites) > 0 ) ? "EDIT" : "CREATE"; ?></strong> Site</h2>
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
                            <div class="card-content">  
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group label-floating is-empty">
                                            <label class="control-label">Site Name</label>
                                            <input required="required" id="site_name" value="<?php echo $allSites['name']; ?>" name="site_name" type="text" class="form-control">
                                            <span class="material-input"></span></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group label-floating">
                                            <label class="control-label"> Modem Number (10 digits)</label>
                                            <input required="required" id="number" name="number" <?php echo ( true == isset($_REQUEST['id']) && "" != $_REQUEST['id'] ) ? "readonly='readonly'" : "" ?>  value="<?php echo $allSites['number']; ?>"   type="number" class="form-control">
                                            <span class="material-input"></span></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group label-floating is-empty">
                                            <label class="control-label">Latitude ( East )</label>
                                            <input required="required" id="latitude" name="latitude" value="<?php echo $allSites['latitude']; ?>"  type="text" class="form-control">
                                            <span class="material-input"></span></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group label-floating is-empty">
                                            <label class="control-label">Longitude ( North )</label>
                                            <input required="required" id="longitude" name="longitude" value="<?php echo $allSites['longitude']; ?>" type="text" class="form-control">
                                            <span class="material-input"></span></div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group label-floating is-empty">
                                            <label class="control-label">Technician Contact Number</label>
                                            <input required="required" id="contact" name="contact" type="number" value="<?php echo $allSites['number']; ?>"  class="form-control">
                                            <span class="material-input"></span></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group label-floating is-empty">
                                            <label class="control-label">City</label>
                                            <input required="required" id="city" name="city" type="text" value="<?php echo $allSites['city']; ?>" class="form-control">
                                            <span class="material-input"></span></div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group label-floating is-empty">
                                            <label class="control-label">Status</label>
                                            <select required="required" id="sitestatus" name="status" class="form-control">
                                                <option value="">Select Status</option>
                                                <option  <?php echo ( isset($allSites) && $allSites['status'] == "active" ) ? "selected='selected'" : ""; ?>  value="active">Active</option>
                                                <option <?php echo ( isset($allSites) && $allSites['status'] == "inactive" ) ? "selected='selected'" : ""; ?> value="inactive">Inactive</option>
                                            </select> 
                                            <span class="material-input"></span></div>
                                    </div>                                
                                </div>
                                <input  id="siteid" name="siteid" type="hidden"  value="<?php echo $allSites['id']; ?>"  />
                                <button type="submit" value="submit" name="submit" id="submitsite" class="btn btn-primary pull-right"><?php echo ( sizeof($allSites) > 0 ) ? "EDIT" : "CREATE"; ?> Site</button>
                                <div class="clearfix"></div>

                            </div>
                        </div>                          
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12" style="display:none;">
                <div class="card">
                    <div class="header">
                        <h2><strong><li class="zmdi zmdi-widgets">&nbsp;</li>SITE </strong>IMAGES</h2>
                        <ul class="header-dropdown">

                        </ul>
                    </div>
                    <div class="body text-center"   style="margin-right:0px;">

                        <div class="card card-profile">

                            <div class="content">
                                <h6 class="category text-gray">Site Images</h6>
                                <h4 class="card-title">Select At least 1 Site images</h4>       

                                <input type="file" required="required" id="siteimages" name="siteimages[]" style="width:80%;margin: 10%" value="Select File" class="btn btn-round" multiple="multiple">
                                <!--<button type="submit" class="btn btn-primary pull-right">Upload Images</button>-->
                                <div class="progress" id="progress" data-background-color="green">
                                    <div id="bar"></div>
                                    <div id="percent">0%</div>
                                </div>

                                <div id="status"></div>

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


        function uploadProgressHandler(event)
        {
//          alert((event.loaded / event.total) * 100);
//          $("#loaded_n_total").html("Uploaded "+event.loaded+" bytes of "+event.total);
            if (event.lengthComputable) {

                var percent = (event.loaded / event.total) * 100;
                var progress = Math.round(percent);
                $("#percent").html(progress + " percent  progress");
                $("#progress").css("width", progress + "%");
                $("#status").html(progress + "% uploaded... please wait");
            }
        }

        function loadHandler(event)
        {
            //  $("#percent").html(event.target.responseText.msg);
            $("#progress").css("width", "0%");
        }

        function errorHandler(event) {
            $("#status").html("Upload Failed");
        }

        function abortHandler(event) {
            $("#status").html("Upload Aborted");
        }


        $("#submitsite").click(function (event)
        {
            event.preventDefault();


            var sitename = $("#site_name").val();

            if (sitename === "") {
                alert("Sitename cannot be blank!!!");
                return;
            }
            var number = $("#number").val();
            if (number === "") {
                alert("Number cannot be blank!!!");
                return;
            }

            if (number.length < 10) {
                alert("Number length cannot be less than 10!!!");
                return;
            }

            var latitude = $("#latitude").val();
            if (latitude === "") {
                alert("Latitude cannot be blank!!!");
                return;
            }
            var longitude = $("#longitude").val();
            if (longitude === "") {
                alert("Longitude cannot be blank!!!");
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
            var siteid = $("#siteid").val();

            if ( siteid == "" && $("#siteimages")[0].files.length === 0 ) {
              //  alert("Please select at least one Site Image!!!");
            //    return;
            }


            var sitestatus = $("#sitestatus").val();

            var submitsite = $("#submitsite").val();



            var formData = new FormData();

            $.each($("#siteimages")[0].files, function (e, v) {

                var file = v;

                formData.append("siteimages[]", file);
            });

            formData.append("site_name", sitename);
            formData.append("number", number);
            formData.append("latitude", latitude);
            formData.append("longitude", longitude);
            formData.append("contact", contact);
            formData.append("city", city);
            formData.append("status", sitestatus);
            formData.append("submit", submitsite);
            formData.append("siteid", siteid);

            $.ajax({url: 'ajaxsite.php',
                method: 'POST',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                xhr: function ()
                {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress",
                            uploadProgressHandler,
                            false
                            );
                    xhr.addEventListener("load", loadHandler, false);
                    xhr.addEventListener("error", errorHandler, false);
                    xhr.addEventListener("abort", abortHandler, false);
                    return xhr;
                },
                success: function (result, status, xhr) {
                    var jarr = JSON.parse(result);
                    if (jarr.result === "error") {
                        alert(jarr.msg);
                    } else {
                        alert(jarr.msg);
                        $("#site_name").val("");
                        $("#number").val("");
                        $("#latitude").val("");
                        $("#longitude").val("");
                        $("#contact").val("");
                        $("#city").val("");
                        $("#sitestatus").val("");
                        $("#submitsite").val("");
                        $("#siteid").val("");
                        window.location.href = "";
                    }
                }
            }
            );
        }
        );
    });

</script>


<?php include './layouts/footer.php'; ?>