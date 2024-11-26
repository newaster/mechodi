<?php
$page = 14;
include './layouts/header.php';
$id = (int) $_GET['id'];

$selectHeader = "Select * from header_map where id = '$id'  ";
$arrHeader = find_by_sql($selectHeader);
$arrHeader = $arrHeader[0];
if (sizeof($arrHeader) < 1) {
    echo "<script>alert('Header not found!!!');</script>";
    exit;
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
                <h2>Embark Dashboard</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="main.php"><i class="zmdi zmdi-home"></i> Embark </a></li>
                    <li class="breadcrumb-item active">Edit Header</li>
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
                        <h2><strong>Edit</strong> Header</h2>
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
                                                <label class="control-label">Header</label>
                                                <input  id="header" name="header" type="text" value="<?php echo $arrHeader['header']; ?>" class="form-control">
                                            </div>
                                        </div>                                
                                    </div>
                                    <input type="hidden" value="<?php echo $id; ?>"  name="id" id="id"  /> 

                                    <div class="row">                                
                                        <div class="col-md-6"> 
                                            <div class="form-group label-floating">
                                                <label class="control-label">Header Description</label>
                                                <input id="header_description" name="header_description" type="text"  value="<?php echo $arrHeader['name']; ?>" class="form-control">
                                            </div>
                                        </div>                                
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="control-label">Select Multiple Sites</label>
                                            <div class="form-group label-floating">                                                
                                                <select class="select2-chosen"  name="type" id="type" >
                                                    <option <?php echo ( "variable" == $arrHeader['type'] ) ? "selected='selected' " : ""; ?>  value="variable">VARIABLE</option>  
                                                    <option <?php echo ( "cumulative" == $arrHeader['type'] ) ? "selected='selected' " : ""; ?> value="cumulative">CUMULATIVE</option> 
                                                </select>
                                            </div>
                                        </div> 
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="control-label">Show Column</label>
                                            <div class="form-group label-floating">                                                
                                                <select class="select2-chosen"  name="display" id="display" >
                                                    <option <?php echo ( "yes" == $arrHeader['display'] ) ? "selected='selected' " : ""; ?>  value="yes">Yes</option>  
                                                    <option <?php echo ( "no" == $arrHeader['display'] ) ? "selected='selected' " : ""; ?> value="no">No</option> 
                                                </select>
                                            </div>
                                        </div> 
                                    </div>

                                    <button type="submit" id="edit_header" class="btn btn-primary pull-right">Edit Header</button>
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
<script>

    $(document).ready(function () {


        $("#edit_header").unbind("click").click(function (event) {
            event.preventDefault();
            var id = $("#id").val();
            var header = $("#header").val();
            if ("" === header) {
                alert("Header cannot be blank!");
                return;
            }

            var header_description = $("#header_description").val();
            if ("" === header_description) {
                alert("Header Description cannot be blank!");
                return;
            }

            var type = $("#type").val();
            var display = $("#display").val();

            var header_operation = "";

            header_operation = "edit-header-operation";

            $.post("ajaxCall.php", {operation: header_operation, header: header, header_description: header_description, id: id, type: type , display: display}, function (data) {

                var response = data.trim();

                var jarr = $.parseJSON(response);
                if (jarr.result === "error") {
                    alert(jarr.msg);
                } else {
                    alert(jarr.msg);
                    window.location = "manage_header_map.php";
                }
            });
        });
    });


</script>
<?php include './layouts/footer.php'; ?>