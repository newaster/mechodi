<!-- Right Icon menu Sidebar -->
<div class="navbar-right">
    <ul class="navbar-nav">

        <li><a href="logout.php" class="mega-menu" title="Sign Out"><i class="zmdi zmdi-power"></i></a></li>
        <li><a href="javascript:void(0);" class="js-right-sidebar" title="Setting"><i class="zmdi zmdi-settings zmdi-hc-spin"></i></a></li>
        <li><a href="javascript:void(0);" class="mega-menu last_login_2" data-attr="<?php echo $_SESSION['username'];?>" data-attr-2="<?php echo $_SESSION['email'];?>"  title="Logins"><i class="zmdi zmdi-devices"></i></a></li>
        <?php if ($_SESSION['role'] != 'guest') { ?>
            <li class="dropdown">

                <?php
                if ($_SESSION['role'] == 'user') {
                    $sqlSelectMaintenance = "select * from maintenance  where updated_by = '{$_SESSION['user_id']}' and DATE(maintenance_date) = DATE( NOW() ) order by id desc ";
                } else if ($_SESSION['role'] == 'admin') {
                    $sqlSelectMaintenance = "select * from maintenance where DATE(maintenance_date) = DATE( NOW() )   order by id desc ";
                }
                $resMaintenance = find_by_sql($sqlSelectMaintenance);
                
                
                $user_id =  $_SESSION['user_id'];
                if ($_SESSION['role'] == 'user') {
                    $query = "Select name , datediff(renewal_date , NOW()) as days 
                    from  sites where created_by = '$user_id' having days < 30  order by id desc Limit 10";
                }else{
                    $query = "Select name , datediff(renewal_date , NOW()) as days 
                    from  sites  having days < 30  order by id desc Limit 10";
                }
                           
                $resSites = find_by_sql($query);
                
                 
                ?>

                <a href="javascript:void(0);" class="dropdown-toggle" title="Notifications" data-toggle="dropdown" role="button"><i class="zmdi zmdi-notifications"></i>
                  <?php if(sizeof($resSites)  > 0 ) { ?> <div class="notify"><span class="<?php echo ( sizeof($resSites) > 0  ) ? "heartbit" : "" ;?>"></span><span class="<?php echo ( sizeof($resSites) > 0  ) ? "point" : "" ;?>"></span></div> <?php } ?>
                </a>
                
                <ul class="dropdown-menu slideUp2"  >
                    <li class="header">Notifications</li>
                    <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 330px;"><li class="body" style="overflow-y: scroll; width: auto; height: 330px;">
                            <?php if(sizeof($resMaintenance) > 0 ) { ?> <label>Maintenance</label> <?php } ?>
                            <ul class="menu list-unstyled"  >

                                <?php
                                

                                foreach ($resMaintenance as $keyMaintenance => $valMaintenance) {

                                    $sqlSites = "Select * from sites where number = '" . $valMaintenance['site'] . "'   ";
                                    $arrSites = find_by_sql($sqlSites);
                                    ?>

                                    <li>
                                        <a href="javascript:void(0);">
                                            <div class="icon-circle bg-blue"><i class="zmdi zmdi-arrow-right"></i></div>
                                            <div class="menu-info">
                                                <h4><?php echo $arrSites[0]['name']; ?></h4>
                                                <p><i class="zmdi zmdi-time"></i>Scheduled Maintenance today at <?php echo $valMaintenance['maintenance_time']; ?> </p>
                                            </div>
                                        </a>
                                    </li>

                                <?php } ?>

                            </ul>
                            <?php if(sizeof($resSites) > 0 ) { ?> <label>Sites Renewal</label> <?php } ?>
                            <ul class="menu list-unstyled" >

                                <?php
                                

                                foreach ($resSites as $keyMaintenance => $valMaintenance) {
 
                                    ?>

                                    <li>
                                        <a href="manage_sites.php">
                                            <div class="icon-circle bg-blue"><i class="zmdi zmdi-arrow-right"></i></div>
                                            <div class="menu-info">
                                                <h4><?php echo $valMaintenance['name']; ?></h4>
                                                <p><i class="zmdi zmdi-time"></i> Site validity about to expire within <?php echo $valMaintenance['days']; ?> </p>
                                            </div>
                                        </a>
                                    </li>

                                <?php } ?>

                            </ul>
                            
                    </div>
            </li>
        </ul>
    </li>     <?php } ?> 
</ul>      
</div>

<!-- Left Sidebar -->
<aside id="leftsidebar" class="sidebar">
    <div class="navbar-brand">
        <button class="btn-menu ls-toggle-btn" type="button"><i class="zmdi zmdi-menu"></i></button>
        <a href="main.php"><img src="assets/images/Logo-1.png" width="100" style="width:100%; " alt="Embark RMS"><span class="m-l-10"></span></a>
    </div>
    <div class="menu">
        <ul class="list">
            <li>
                <div class="user-info">
                    <a class="image" href="profile.html">
                        <!--<img src="assets/images/profile_av.jpg" alt="User">-->
                    </a>
                    <div class="detail">
                        <h4><?php echo $_SESSION['name']; ?></h4>
                        <small><?php echo strtoupper($_SESSION['role']); ?></small><a style="display:inline;" href="profile.php"><i class="zmdi zmdi-eye"></i></a>                        
                    </div>
                </div>
            </li>
            <li class="<?php echo ( 1 == $page ) ? "active open" : ""; ?>"><a href="main.php"><i class="zmdi zmdi-home"></i><span>Dashboard</span></a></li>
            <!-- li class="<?php //echo ( 2 == $page ) ? "active open" : ""; ?>"  ><a href="analysis.php"><i class="zmdi zmdi-chart"></i><span>Analysis</span></a></li -->
            <li class="<?php echo ( 3 == $page ) ? "active open" : ""; ?>"   ><a href="reports.php"  ><i class="zmdi zmdi-grid"></i><span>Reports</span></a>         
            </li>
            
            <!--
            <?php if ($_SESSION['role'] == 'user'    ||  $_SESSION['role'] == 'guest'  ) { ?>
                <li  class="<?php echo ( 2 == $page || 21 == $page ) ? "active open" : ""; ?>"   >
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-view-module"></i><span> Analysis</span></a>
                    <ul class="ml-menu">

                        <li class="<?php echo ( 2 == $page ) ? "active open" : ""; ?>"   ><a href="analysis.php" >Analysis</a></li>
                      <?php if ($_SESSION['role'] == 'user' ) { ?>   <li class="<?php echo ( 21 == $page ) ? "active open" : ""; ?>" ><a href="zone_analysis.php">Zone Analysis</a></li> <?php } ?>

                    </ul>

                </li>
            <?php } ?>
            
            <?php// if ($_SESSION['role'] == 'user') { ?>
                <li  class="<?php echo ( 23 == $page || 24 == $page  || 25 == $page  ) ? "active open" : ""; ?>"  >
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-card-membership"></i><span> Card Analysis</span></a>
                    <ul class="ml-menu">
                        <li  class="<?php echo ( 25 == $page ) ? "active open" : ""; ?>" ><a href="card_analysis.php">Analysis</a></li>
                        <li  class="<?php echo ( 23 == $page ) ? "active open" : ""; ?>" ><a href="reports_card_day.php">Report Day</a></li>
                        <li class="<?php echo ( 24 == $page ) ? "active open" : ""; ?>"  ><a href="reports_card_month.php">Report Month</a></li>

                    </ul>

                </li>
            <?php// } ?> -->
            
            <?php if ($_SESSION['role'] == 'user') { ?>
                <li  class="<?php echo ( 4 == $page || 5 == $page ) ? "active open" : ""; ?>"   >
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-view-module"></i><span> Sites </span></a>
                    <ul class="ml-menu">

                        <li class="<?php echo ( 4 == $page ) ? "active open" : ""; ?>"   ><a href="add_site.php" >Create site </a></li>
                        <li class="<?php echo ( 5 == $page ) ? "active open" : ""; ?>" ><a href="manage_sites.php">Manage site</a></li>

                    </ul>

                </li>
            <?php } ?>
            <?php if ($_SESSION['role'] == 'user' || $_SESSION['role'] == 'admin') { ?>
                <li  class="<?php echo ( 8 == $page || 9 == $page ) ? "active open" : ""; ?>"  >
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-account"></i><span> Users</span></a>
                    <ul class="ml-menu">
                        <?php if ($_SESSION['role'] == 'user') { ?>
                            <li  class="<?php echo ( 8 == $page ) ? "active open" : ""; ?>" ><a href="add_user.php">Create User</a></li>
                            <li class="<?php echo ( 9 == $page ) ? "active open" : ""; ?>"  ><a href="manage_user.php">Manage User</a></li>
                        <?php } ?>
                        <?php if ($_SESSION['role'] == 'admin') { ?>
                            <li class="<?php echo ( 9 == $page ) ? "active open" : ""; ?>"  ><a href="manage_login_users.php">Manage Users</a></li>
                        <?php } ?>
                    </ul>

                </li>
            <?php } ?>
            <?php if ($_SESSION['role'] == 'user') { ?>
                <li  class="<?php echo ( 10 == $page || 11 == $page ) ? "active open" : ""; ?>"  >
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-toll"></i><span> Zones</span></a>
                    <ul class="ml-menu">

                        <li  class="<?php echo ( 10 == $page ) ? "active open" : ""; ?>" ><a href="add_zone.php">Create Zone</a></li>
                        <li class="<?php echo ( 11 == $page ) ? "active open" : ""; ?>"  ><a href="manage_zone.php">Manage Zone</a></li>

                    </ul>

                </li>
            <?php } ?>
            
            <!--
            <?php if ($_SESSION['role'] == 'user') { ?>
                <li  class="<?php echo ( 16 == $page || 17 == $page ) ? "active open" : ""; ?>"  >
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-device-hub"></i><span> Cluster Management</span></a>
                    <ul class="ml-menu">

                        <li  class="<?php echo ( 16 == $page ) ? "active open" : ""; ?>" ><a href="add_cluster.php">Create Cluster</a></li>
                        <li class="<?php echo ( 17 == $page ) ? "active open" : ""; ?>"  ><a href="manage_cluster.php">Manage Cluster</a></li>

                    </ul>

                </li>
            <?php } ?> -->

            <?php if ($_SESSION['role'] == 'user') { ?>
                <li  class="<?php echo ( 12 == $page || 13 == $page ) ? "active open" : ""; ?>"  >
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-smartphone-setup"></i><span> Tickets</span></a>
                    <ul class="ml-menu">

                        <li  class="<?php echo ( 12 == $page ) ? "active open" : ""; ?>" ><a href="add_maintenance.php">Create Tickets</a></li>
                        <li class="<?php echo ( 13 == $page ) ? "active open" : ""; ?>"  ><a href="manage_maintenance.php">Manage Tickets</a></li>

                    </ul>

                </li>
            <?php } ?>
            
                <li><a href="logout.php" class="mega-menu" title="Sign Out"><i class="zmdi zmdi-power"></i><span> Logout</span></a></li>

            <!--<?php if ($_SESSION['role'] == 'user' || $_SESSION['role'] == 'admin'  ||  $_SESSION['role'] == 'guest'  ) { ?>
                <li  class="<?php echo ( 14 == $page || 15 == $page || 18 == $page || 19 == $page || 20 == $page  || 22 == $page ) ? "active open" : ""; ?>"  >
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-settings"></i><span> Settings</span></a>
                    <ul class="ml-menu">
                        <?php if ($_SESSION['role'] == 'admin') { ?>
                            <li  class="<?php echo ( 14 == $page ) ? "active open" : ""; ?>" ><a href="add_header_map.php">Add Header Map</a></li>
                            <li class="<?php echo ( 15 == $page ) ? "active open" : ""; ?>"  ><a href="manage_header_map.php">Manage Header Map</a></li>
                        <?php } ?>

                        <?php if ( $_SESSION['role'] == 'user' ||  $_SESSION['role'] == 'guest'  ) { ?>
                          <li class="<?php echo ( 20 == $page ) ? "active open" : ""; ?>"  >
                               <a href="set_dashboard_param.php">Set Dashboard Parameter</a></li>
                               <?php   if(  $_SESSION['role'] == 'user' ) {   ?>
                                <li  class="<?php echo ( 18 == $page ) ? "active open" : ""; ?>" ><a href="add_column.php">Add Columns</a></li>
                                <li class="<?php echo ( 19 == $page ) ? "active open" : ""; ?>"  ><a href="manage_columns.php">Manage Columns</a></li>
                            <?php  }
                            }?>
                            
                        <?php if ( $_SESSION['role'] == 'user' ) { ?>
                          <li class="<?php echo ( 22 == $page ) ? "active open" : ""; ?>"  >
                               <a href="set_recharge_numbers.php">Set Recharge Numbers</a></li>
                              <?php   
                            }?>    
                            
                    </ul>

                </li>
            <?php } ?> -->

            <!--<li class="<?php echo ( 6 == $page ) ? "active open" : ""; ?>"  ><a href="javascript:void(0);"><i class="zmdi zmdi-help-outline"></i><span> Help</span></a></li>-->

            <li>
               
                <div class="progress-container progress-info">
                    <span class="progress-badge">  
                        <a target="blank" href="http://astertechnologies.in" style="color:red;">
                            Aster Technologies
                        </a> 
                    </span>
                </div>
                
            </li>
        </ul>
    </div>
</aside>

<!-- Right Sidebar -->
<aside id="rightsidebar" class="right-sidebar">
    <ul class="nav nav-tabs sm">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#setting"><i class="zmdi zmdi-settings zmdi-hc-spin"></i></a></li>
        <!--<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#chat"><i class="zmdi zmdi-comments"></i></a></li>-->
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="setting">
            <div class="slim_scroll">
                <div class="card">
                    <h6>Theme Option</h6>
                    <div class="light_dark">
                        <div class="radio">
                            <input type="radio" name="radio1" id="lighttheme" value="light" checked="">
                            <label for="lighttheme">Light Mode</label>
                        </div>
                        <div class="radio mb-0">
                            <input type="radio" name="radio1" id="darktheme" value="dark">
                            <label for="darktheme">Dark Mode</label>
                        </div>
                    </div>
                </div>
                <!--                <div class="card">
                                    <h6>Color Skins</h6>
                                    <ul class="choose-skin list-unstyled">
                                        <li data-theme="purple"><div class="purple"></div></li>
                                        <li data-theme="blue"><div class="blue"></div></li>
                                        <li data-theme="cyan"><div class="cyan"></div></li>
                                        <li data-theme="green"><div class="green"></div></li>
                                        <li data-theme="orange"><div class="orange"></div></li>
                                        <li data-theme="blush" class="active"><div class="blush"></div></li>
                                    </ul>                                        
                                </div>-->
                <!--                <div class="card">
                                    <h6>General Settings</h6>
                                    <ul class="setting-list list-unstyled">
                                        <li>
                                            <div class="checkbox rtl_support">
                                                <input id="checkbox1" type="checkbox" value="rtl_view">
                                                <label for="checkbox1">RTL Version</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox ms_bar">
                                                <input id="checkbox2" type="checkbox" value="mini_active">
                                                <label for="checkbox2">Mini Sidebar</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                                <input id="checkbox3" type="checkbox" checked="">
                                                <label for="checkbox3">Notifications</label>
                                            </div>                        
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                                <input id="checkbox4" type="checkbox">
                                                <label for="checkbox4">Auto Updates</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                                <input id="checkbox5" type="checkbox" checked="">
                                                <label for="checkbox5">Offline</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                                <input id="checkbox6" type="checkbox" checked="">
                                                <label for="checkbox6">Location Permission</label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>                -->
            </div>                
        </div>       
        <div class="tab-pane right_chat" id="chat">
            <div class="slim_scroll">
                <div class="card">
                    <ul class="list-unstyled">
                                              
                    </ul>
                </div>
            </div>
        </div>
    </div>
</aside> 


<div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" id="myModal-2">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Last Login from</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                  
                
            </div>
            
           
        </div>
    </div>
</div>
