<?php
$page = 55;

include './layouts/header.php';
$dealer_id = $_SESSION['user_id']; 
 
if ($_SESSION['role'] == 'user') {
    
    $selectDebits = "Select de.*, d.fname, c.name from debit as de inner join login as d  on d.id = de.user_id inner join sites as c on c.id = de.rec_id   where de.user_id = '$dealer_id' order by id desc";
    $resDebits = find_by_sql($selectDebits);
} else if ($_SESSION['role'] == 'admin') {
    
   
    $selectDebits = "Select de.*, d.fname, c.name  from debit as de inner join login as d  on d.id = de.user_id inner join customer as c on c.id = de.rec_id   order by id desc";
    $resDebits = find_by_sql($selectDebits);
}

$selectDealer = "Select * from login where id = '$dealer_id'";
$resDealer = find_by_sql($selectDealer);

$resDealer = $resDealer[0];
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
                <h2>Aster Dashboard</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="main.php"><i class="zmdi zmdi-home"></i> Aster </a></li>
                    <li class="breadcrumb-item active">Profile</li>
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
                <div class="card widget_2 big_icon zmdi-">
                    <div class="body ">
                        <h6 style="z-index:9999;" ><?php echo "Name" ?></h6>
                        <h2   ><?php echo $resDealer['fname']." ".$resDealer['lname']; ?><small class="info"></small></h2>
                        <!--<h2 >.<small class="info"></small></h2>-->
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card widget_2 big_icon zmdi-">
                    <div class="body">
                        <h6 style="z-index:9999;" ><?php echo "Points Available" ?></h6>
                        <h2 ><?php echo $resDealer['quota']; ?><small class="info"></small></h2>
                        <!--<h2 >.<small class="info"></small></h2>-->
                    </div>
                </div>
                
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="card widget_2 big_icon zmdi-plus-">
                    <div class="body">
                        <h6 style="z-index:9999;" ><?php echo "Details" ?></h6>
                        <!--<h2 ><?php echo $resDealer['number']; ?><small class="info"></small></h2>-->
                        <h2 ><?php echo $resDealer['email']; ?><small class="info"></small></h2>
                    </div>
                </div>
            </div>

        </div>

         

        <div class="row clearfix"  >
            <div class="col-lg-12">
                <div class="card">
                    <div class="header">
                        <h2><strong><li class="zmdi zmdi-trending-up">&nbsp;</li>Transactions</strong> </h2>

                    </div>

                    <div class="body">

                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs p-0 mb-3 nav-tabs-success" role="tablist">
                            <!--<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#transactions"> <i class="zmdi zmdi-money-box"></i> TRANSACTIONS</a></li>-->
                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#debits"> <i class="zmdi zmdi-money-off"></i> DEBITS</a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            
                            
                            <div role="tabpanel" class="tab-pane in active" id="debits"> 
                                <!--<b>Quality Parameters</b>-->
                                <div class="row">
                                    <div class="table-responsive">
                                        <table  class="table table-bordered table-striped table-hover dataTable js-exportable" data-page-length="5"  >
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="10%" scope="col">Sr. No.</th>
                                                    <?php if($_SESSION['ROLE'] == 'admin'){?>
                                                    <th width="20%" scope="col">DEALER</th>
                                                    <?php } ?>
                                                    <th width="20%" scope="col">USER ACCOUNT</th>
                                                    <th width="30%" scope="col">DEBIT PURPOSE</th> 
                                                    <th width="30%" scope="col">DATE</th>
                                                </tr>
                                            </thead>
                                            <tbody  >
                                                <?php  
                                                    foreach( $resDebits as $keyTx => $valTx ){
                                                ?>
                                                       <tr> 
                                                        <td> <?php echo $keyTx+1;?> </td>
                                                        <?php if($_SESSION['ROLE'] == 'admin'){?>
                                                        <td> <?php echo $valTx['fname'];?> </td>
                                                        <?php } ?>
                                                        <td> <?php echo $valTx['name'];?> </td>
                                                        <td> <?php echo $valTx['purpose'];?> </td> 
                                                        <td> <?php echo $valTx['created_date'];?> </td>
                                                        </tr>
                                                <?php        
                                                    }
                                                ?>
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
        
  

        $("#site_select").trigger("change");
    
        $('.table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    

    });

 




</script>

<?php include './layouts/footer.php'; ?>