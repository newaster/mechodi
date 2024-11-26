<?php

$page = 1;
require_once('./includes/load.php');
//require_once('/home/embarkrms/public_html/mail_function.php');


$arrNoHeader = array('Number', 'Site', 'Device', 'Date', 'Time', 'Status');


$operation = ( true == isset($_REQUEST["operation"]) && "" != $_REQUEST["operation"]) ? $_REQUEST["operation"] : "";
if (false == isset($_SESSION['user_id']) || "" == $_SESSION['user_id'] || $_SESSION['user_id'] < 1) {
    $message = "You need to be logged In!!!";
    echo json_encode(array("result" => "error", "msg" => $message));
    exit;
}
switch ($operation) {

    case "set-selected-headers":
        $numbers = ( true == isset($_REQUEST["numbers"]) && "" != $_REQUEST["numbers"]) ? $_REQUEST["numbers"] : "";
        $clustername = ( true == isset($_REQUEST["clustername"]) && "" != $_REQUEST["clustername"]) ? trim($_REQUEST["clustername"]) : "";
        $headersselected = ( true == isset($_REQUEST['cumulativeselectheaders']) && "" != $_REQUEST['cumulativeselectheaders'] ) ? $_REQUEST['cumulativeselectheaders'] : "";
        $headers = ( true == isset($_REQUEST['headers']) && "" != $_REQUEST['headers'] ) ? $_REQUEST['headers'] : "";

        $created_date = date("Y-m-d H:i:s");
        $user_id = $_SESSION['user_id'];

        $arrNumbers = explode(",", $numbers);
        $arrNumbers = array_filter($arrNumbers);
        $numbers = implode(",", $arrNumbers);

        if ($user_id != "") {

            $sql = " Insert into `cluster` 
                ( `name`, `numbers`, `headers`, `cumulativeheaders`, `sitekey`,  `updated_by` , `created_date`) 
                VALUES ( '$clustername' , '$numbers', '$headers', '$headersselected', '$sitekey', '{$user_id}', '$created_date' )";

            if ($db->query($sql)) {
                echo $msg = "Cluster Updated Successfully";
            } else {
                echo $msg = "Some Error Occurred";
            }
        } else {
            echo $msg = "Some Error Occurred";
        }

        break;

    case "set-column-headers":
        $id = ( true == isset($_REQUEST["id"]) && "" != $_REQUEST["id"]) ? $_REQUEST["id"] : "";
        $name = ( true == isset($_REQUEST["name"]) && "" != $_REQUEST["name"]) ? $_REQUEST["name"] : "";
        $headers = ( true == isset($_REQUEST["headers"]) && "" != $_REQUEST["headers"]) ? $_REQUEST["headers"] : "";
        $user_id = $_SESSION['user_id'];
        $created_date = date("Y-m-d");

        $sqlColumns = " Insert into `cluster_columns` 
                ( `name`, `cluster`, `headers` , `created_date`, `sitekey` , `updated_by` ) 
                VALUES ( '$name' , '$id', '$headers', '$created_date',  '" . $_SESSION['LOG_KEY'] . "', '" . $user_id . "' )";

        if ($db->query($sqlColumns)) {
            echo $msg = 1;
        } else {
            echo $msg = 0;
        }

        exit;

        break;


    case "edit-column-headers":
        $id = ( true == isset($_REQUEST["id"]) && "" != $_REQUEST["id"]) ? $_REQUEST["id"] : "";
        $idcolumn = ( true == isset($_REQUEST["idcolumn"]) && "" != $_REQUEST["idcolumn"]) ? $_REQUEST["idcolumn"] : "";
        $name = ( true == isset($_REQUEST["name"]) && "" != $_REQUEST["name"]) ? $_REQUEST["name"] : "";
        $headers = ( true == isset($_REQUEST["headers"]) && "" != $_REQUEST["headers"]) ? $_REQUEST["headers"] : "";
        $user_id = $_SESSION['user_id'];
        $created_date = date("Y-m-d");

        $sqlColumnsUpdate = " Update `cluster_columns` 
                set `name` = '$name' , `cluster` = '$id', `headers` = '$headers' where id = '$idcolumn' and sitekey = '" . $_SESSION['LOG_KEY'] . "' and `updated_by` = '" . $user_id . "'  ";
        $db->query($sqlColumnsUpdate);
        if ($db->affected_rows() === 1) {
            echo $msg = 1;
        } else {
            echo $msg = 0;
        }

        exit;

        break;

    case "get-selected-headers":

        $number = ( true == isset($_REQUEST["numbers"]) && "" != $_REQUEST["numbers"]) ? $_REQUEST["numbers"] : "";
        $arrHeaders = array();

        $sql = 'SELECT mh.* FROM embark_data as mh where  mh.NUMBER = "' . $number . '" and ( COLNAME LIKE "%COND%" or COLNAME LIKE "%TDS%" or COLNAME LIKE "%CCDU%" or COLNAME LIKE "%2MTDS%" or COLNAME LIKE "%RCHG%" or COLNAME LIKE  "%N%" )   ';
        $result = find_by_sql($sql);

        foreach ($result as $keyRow => $settingsrow) {
            $arrHeaders[0] = $settingsrow['COLNAME'];
        }

        echo json_encode($arrHeaders);


        break;

    case "get-selected-cluster-headers":

        $id = ( true == isset($_REQUEST["id"]) && "" != $_REQUEST["id"]) ? $_REQUEST["id"] : "";

        $sql = " SELECT headers FROM `cluster` WHERE status = 'active' and id = '" . $id . "' ";
        $result = find_by_sql($sql);
        $num = sizeof($result);

        if (intval($num) > 0) {
            $allcluster = array();
            foreach ($result as $key => $rows) {
                $allcluster[0] = $rows['headers'];
            }

            echo json_encode($allcluster);
        } else {
            echo 0;
        }


        break;

    case "fetch-user":

        $id = ( true == isset($_POST['id']) && "" != $_POST['id'] ) ? $_POST['id'] : "";

        $sql = "Select * from login where id = '" . $id . "' order by id desc Limit 1";

        $arrResponse = array();

        $result = find_by_sql($sql);

        foreach ($result as $key => $row) {
            $arrResponse['uname'] = $row['uname'];
            $arrResponse['number'] = $row['number'];
            $arrResponse['password'] = $row['password'];
            $arrResponse['key'] = $row['key'];
            $arrResponse['email'] = $row['email'];

            $arrResponse['role'] = $row['role'];

            $arrResponse['status'] = ( $row['status'] == 'active' ) ? 'active' : 'inactive';
        }

        if (sizeof($arrResponse) > 0) {
            echo json_encode($arrResponse);
        } else {
            echo 0;
        }

        break;



    case "edit-selected-headers":
        $id = ( true == isset($_REQUEST["id"]) && "" != $_REQUEST["id"]) ? $_REQUEST["id"] : "";
        $numbers = ( true == isset($_REQUEST["numbers"]) && "" != $_REQUEST["numbers"]) ? $_REQUEST["numbers"] : "";
        $clustername = ( true == isset($_REQUEST["clustername"]) && "" != $_REQUEST["clustername"]) ? $_REQUEST["clustername"] : "";
        $headersselected = ( true == isset($_REQUEST['cumulativeselectheaders']) && "" != $_REQUEST['cumulativeselectheaders'] ) ? $_REQUEST['cumulativeselectheaders'] : "";
        $headers = ( true == isset($_REQUEST['headers']) && "" != $_REQUEST['headers'] ) ? $_REQUEST['headers'] : "";

        $arrNumbers = explode(",", $numbers);
        $arrNumbers = array_filter($arrNumbers);
        $numbers = implode(",", $arrNumbers);

        $sql = "Update cluster set numbers = '$numbers', name='$clustername' , cumulativeheaders = '$headersselected' , headers = '" . $headers . "' where id = $id and sitekey = '" . $_SESSION['LOG_KEY'] . "' ";

        $msg = "";
        $db->query($sql);
        if ($db->affected_rows() === 1) {
            echo $msg = "Cluster Updated Successfully";
        } else {
            echo $msg = "Some Error Occurred";
        }
        break;

    case "edit-cluster":
        $id = ( true == isset($_REQUEST["id"]) && "" != $_REQUEST["id"]) ? $_REQUEST["id"] : "";
        $sql = "Select * from cluster where id = '$id' and updated_by = '" . $_SESSION['user_id'] . "' ";
        $result = find_by_sql($sql);
        $allcluster = array();
        foreach ($result as $key => $val) {
            $allcluster['id'] = $val['id'];
            $allcluster['clustername'] = $val['name'];
            $allcluster['headers'] = $val['headers'];
            $allcluster['numbers'] = $val['numbers'];
            $allcluster['cummulative'] = $val['cumulativeheaders'];
        }
        echo json_encode($allcluster);
        break;

    case "delete-cluster":
        $clustername = ( true == isset($_REQUEST["clustername"]) && "" != $_REQUEST["clustername"]) ? $_REQUEST["clustername"] : "";
        $id = ( true == isset($_REQUEST["id"]) && "" != $_REQUEST["id"]) ? $_REQUEST["id"] : "";
        $sql = "Update cluster set status = 'inactive' where id = '$id' and sitekey = '" . $_SESSION['LOG_KEY'] . "' ";
        $db->query($sql);
        if ($db->affected_rows() === 1) {
            echo $msg = "Cluster Updated Successfully";
        } else {
            echo $msg = "Some Error Occurred";
        }

        break;

    case "add-cronemail":
        $email = ( true == isset($_REQUEST["email"]) && "" != $_REQUEST["email"]) ? $_REQUEST["email"] : "";

        $sql = "Update login set cronemail = '$email' where login.key = '" . $_SESSION['LOG_KEY'] . "' ";
        $db->query($sql);
        if ($db->affected_rows() === 1) {
            echo 1;
        } else {
            echo 0;
        }

        break;
    case "add-customer":

        $username = ( true == isset($_REQUEST["username"]) && "" != $_REQUEST["username"]) ? $_REQUEST["username"] : "";
        $email = ( true == isset($_REQUEST["email"]) && "" != $_REQUEST["email"]) ? $_REQUEST["email"] : "";
        $first_name = ( true == isset($_REQUEST["first_name"]) && "" != $_REQUEST["first_name"]) ? $_REQUEST["first_name"] : "";
        $last_name = ( true == isset($_REQUEST["last_name"]) && "" != $_REQUEST["last_name"]) ? $_REQUEST["last_name"] : "";
        $city = ( true == isset($_REQUEST["city"]) && "" != $_REQUEST["city"]) ? $_REQUEST["city"] : "";
        $contact = ( true == isset($_REQUEST["contact"]) && "" != $_REQUEST["contact"]) ? $_REQUEST["contact"] : "";
        $customerselectall = ( true == isset($_REQUEST["customerselectall"]) && "" != $_REQUEST["customerselectall"]) ? $_REQUEST["customerselectall"] : "";
        $password = ( true == isset($_REQUEST["password"]) && "" != $_REQUEST["password"]) ? $_REQUEST["password"] : "";
        $status = ( true == isset($_REQUEST["status"]) && "" != $_REQUEST["status"]) ? $_REQUEST["status"] : "";
        $quotaAssigned = ( isset($_POST['quota']) && "" != $_POST['quota'] ) ? $_POST['quota'] : "";

        $sqlSelectLogin = "Select * from login where email = '$email' or number = '" . $contact . "'  or uname = '" . $username . "' ";

        $resultLogin = find_by_sql($sqlSelectLogin);
        $message = "";
        foreach ($resultLogin as $keyres => $resource) {

            if ($email == $resource['email']) {
                $message = "Email already exists!!!";
            } else if ($contact == $resource['number']) {
                $message = "Contact already exists!!!";
            } else if ($contact == $resource['uname']) {
                $message = "Username already exists!!!";
            }
        }


        if ($message != "") {
            echo json_encode(array("result" => "error", "msg" => $message));
            exit;
        }

        if (false == isset($_SESSION['user_id']) || "" == $_SESSION['user_id']) {
            $message = "Error creating user.";
            echo json_encode(array("result" => "error", "msg" => "Error creating user!!!"));
            exit;
        }

        $sqlInsertLogin = " Insert into login ( uname, fname, lname, email, number, role , company, updated_by  , sites, city, country , created_date, password , cleartext_pass , status , quota ) ";
        $sqlInsertLogin .= " VALUES ";
        $sqlInsertLogin .= " ( '" . $username . "','" . $first_name . "','" . $last_name . "','" . $email . "','" . $contact . "','guest','" . $_SESSION['company'] . "' ,'" . $_SESSION['user_id'] . "', '" . $customerselectall . "' , '" . $city . "', '" . $city . "', NOW() , '" . sha1($password) . "' , '$password' , '$status', '$quotaAssigned' ) ";

        if ($db->query($sqlInsertLogin)) {
            $message = "User added successfully";
            echo json_encode(array("result" => "success", "msg" => $message));
            exit;
        } else {
            $message = "Error adding User " . mysqli_error($link);
            echo json_encode(array("result" => "error", "msg" => $message));
            exit;
        }


        break;

    case "edit-customer":

        $customer_id = ( true == isset($_REQUEST["customer_id"]) && "" != $_REQUEST["customer_id"]) ? $_REQUEST["customer_id"] : "";
        $username = ( true == isset($_REQUEST["username"]) && "" != $_REQUEST["username"]) ? $_REQUEST["username"] : "";
        $email = ( true == isset($_REQUEST["email"]) && "" != $_REQUEST["email"]) ? $_REQUEST["email"] : "";
        $first_name = ( true == isset($_REQUEST["first_name"]) && "" != $_REQUEST["first_name"]) ? $_REQUEST["first_name"] : "";
        $last_name = ( true == isset($_REQUEST["last_name"]) && "" != $_REQUEST["last_name"]) ? $_REQUEST["last_name"] : "";
        $city = ( true == isset($_REQUEST["city"]) && "" != $_REQUEST["city"]) ? $_REQUEST["city"] : "";
        $contact = ( true == isset($_REQUEST["contact"]) && "" != $_REQUEST["contact"]) ? $_REQUEST["contact"] : "";
        $customerselectall = ( true == isset($_REQUEST["customerselectall"]) && "" != $_REQUEST["customerselectall"]) ? $_REQUEST["customerselectall"] : "";
        $password = ( true == isset($_REQUEST["password"]) && "" != $_REQUEST["password"]) ? $_REQUEST["password"] : "";
        $status = ( true == isset($_REQUEST["status"]) && "" != $_REQUEST["status"]) ? $_REQUEST["status"] : "";
        $quotaAssigned = ( isset($_POST['quota']) && "" != $_POST['quota'] ) ? $_POST['quota'] : "";

        $selectCustomer = "Select * from login where id = '" . $customer_id . "' ";
        $resCustomer = find_by_sql($selectCustomer);

        if (sizeof($resCustomer) < 1) {
            $message = "User not found!!!";
            echo json_encode(array("result" => "error", "msg" => $message));
            exit;
        }

        $updateCustomer = "Update login set ";
        $updateCustomer .= " fname =  '" . $first_name . "' , ";
        $updateCustomer .= " lname =  '" . $last_name . "' , ";
        $updateCustomer .= " city =  '" . $city . "' , ";
        $updateCustomer .= " sites =  '" . $customerselectall . "' ,  ";
        $updateCustomer .= " status =  '" . $status . "' ,  ";
        $updateCustomer .= " quota = '{$quotaAssigned}'   ";
       
        $shPass = "";
        if ($password != "") {
            $shPass = sha1($password);
            $updateCustomer .= ", password =  '" . $shPass . "',  ";
            $updateCustomer .= " cleartext_pass =  '" . $password . "'  ";
        }

        $updateCustomer .= " WHERE id = '" . $customer_id . "' ";
        $res = $db->query($updateCustomer);
        
        if ( $db->affected_rows() > 0 ) {
            $message = "User updated successfully!!!";
            echo json_encode(array("result" => "success", "msg" => $message));
            exit;
        } else {
            $message = "Error updating User";
            echo json_encode(array("result" => "error", "msg" => $message));
            exit;
        }

        break;

    case "edit-maintenance":
        $id = ( true == isset($_REQUEST["id"]) && "" != $_REQUEST["id"]) ? $_REQUEST["id"] : "";
        $action = ( true == isset($_REQUEST["action"]) && "" != $_REQUEST["action"]) ? $_REQUEST["action"] : "";
        $user_id = $_SESSION['user_id'];
        if ($id != "") {
            $updateMaintenance = "Update maintenance set status = 'inactive' , action = '$action' where id = '$id'  and  updated_by = '$user_id' Limit 1  ";

            $db->query($updateMaintenance);

            if ($db->affected_rows() === 1) {
                $message = "Maintenance updated successfully!!!";
                echo json_encode(array("result" => "success", "msg" => $message));
                exit;
            } else {
                $message = "Error updating Maintenance";
                echo json_encode(array("result" => "error", "msg" => $message));
                exit;
            }
        } else {
            $message = "Error updating Maintenance. ID not found";
            echo json_encode(array("result" => "error", "msg" => $message));
            exit;
        }


        break;

    case "add-maintenance":

        $username = ( true == isset($_REQUEST["username"]) && "" != $_REQUEST["username"]) ? $_REQUEST["username"] : "";
        $email = ( true == isset($_REQUEST["email"]) && "" != $_REQUEST["email"]) ? $_REQUEST["email"] : "";
        $contact = ( true == isset($_REQUEST["contact"]) && "" != $_REQUEST["contact"]) ? $_REQUEST["contact"] : "";
        $site = ( true == isset($_REQUEST["site"]) && "" != $_REQUEST["site"]) ? $_REQUEST["site"] : "";
        $maintenance_date = ( true == isset($_REQUEST["maintenance_date"]) && "" != $_REQUEST["maintenance_date"]) ? $_REQUEST["maintenance_date"] : "";
        $maintenance_time = ( true == isset($_REQUEST["maintenance_time"]) && "" != $_REQUEST["maintenance_time"]) ? $_REQUEST["maintenance_time"] : "";

        if (false == isset($_SESSION['user_id']) || "" == $_SESSION['user_id'] || $_SESSION['user_id'] < 1) {
            $message = "Error adding maintenance, You need to be logged In!!!";
            echo json_encode(array("result" => "error", "msg" => $message));
            exit;
        }

        $user_id = $_SESSION['user_id'];

        $selectSite = "Select * from sites where number = '$site' ";
        $result = find_by_sql($selectSite);
        $latitude = "0";
        $longitude = "0";
        foreach ($result as $key => $val) {
            $latitude = $val['latitude'];
            $longitude = $val['longitude'];
        }

        $siteAddress = geolocationaddress($latitude, $longitude);

        $insertMaintenance = "Insert into maintenance ( `username`, `email`, `contact`, `site`, `maintenance_date`, `maintenance_time`, `created_date`,  `updated_by` ) ";
        $insertMaintenance .= " VALUES ";
        $insertMaintenance .= "( '{$username}','{$email}', '{$contact}', '{$site}','" . date("Y-m-d", strtotime($maintenance_date)) . "', '{$maintenance_time}',NOW(), '{$user_id}' )";

        if ($db->query($insertMaintenance)) {

            $selectSite = "Select * from sites where number = '{$site}' order by id desc Limit 1";
            $res = find_by_sql($selectSite);

            if (sizeof($res) > 0) {
                $arrSites = $res[0];

//                $arrSites = $arrSites[0];

                $sitename = $arrSites['name'];
                $sitenumber = $arrSites['number'];
                $technician = $arrSites['contact'];
                $longitude = $arrSites['longitude'];
                $latitude = $arrSites['latitude'];
                $city = $arrSites['city'];

                $emailContent = "Hello " . ucwords($username) . " <br/>";
                $emailContent .= "Please find the site maintenance details as given below. <br/>";
                $emailContent .= "<b>Site Name</b> : " . $sitename . "<br/>";
                $emailContent .= "<b>Site Number</b> : " . $sitenumber . "<br/>";
                $emailContent .= "<b>Site Address</b> : " . $siteAddress . "<br/>";
                $emailContent .= "<b>Technician Contact</b> : " . $technician . "<br/>";
                $emailContent .= "<b>Site Co-Ordinates</b> : Longitude : " . $longitude . " , Latitude : " . $latitude . "<br/>";
                $emailContent .= "<b>City</b> : " . $city . "<br/>";
                $emailContent .= "<b>Maintenance Schedule</b> : " . $maintenance_date . " at " . $maintenance_time . " <br/>";

                $emailheaders = "MIME-Version: 1.0" . "\r\n";
                $emailheaders .= "Content-type:text/html;charset=ISO-8859-1" . "\r\n";
                $emailheaders .= "From: admin@embarkrms.com" . "\r\n";
                if ($email != "") { 
                    
                   // cmail($email, "Embark Site Maintenance", $emailContent, $emailheaders);

                    $emailContent = str_replace("<br/>", "\n", $emailContent);
                    $emailContent = str_replace("<b>", "", $emailContent);
                    $emailContent = str_replace("</b>", "", $emailContent);

                    $url = 'http://api.msg91.com/api/v2/sendsms?';
                    $data = array('country' => '91', 'authkey' => '291751ACvXHczjYJ5d67b7e2', 'sender' => 'Embark', 'route' => '4', 'mobiles' => $contact, 'message' => $emailContent);

                    $response = execute_url($url, $data);
                }
            }

            $message = "Maintenance added successfully!!!";
            echo json_encode(array("result" => "success", "msg" => $message));
            exit;
        } else {
            $message = "Error adding maintenance!!!";
            echo json_encode(array("result" => "error", "msg" => $message));
            exit;
        }
    

        break;
    case "get-all-sites":
        if ($_SESSION['role'] == 'admin') {
            $sql = "Select s.*  from sites as s  ";
        } else if ($_SESSION['role'] == 'guest') {
            $sql = "select sites.* from sites inner join zone on zone.number = sites.number where zone.unique_id in ('{$_SESSION['zone']}')  order by sites.id asc ";
        } else {
            $sql = "Select s.*  from sites as s where s.created_by = '" . $_SESSION['user_id'] . "' ";
        }
        $result = find_by_sql($sql);
        $allcluster = array();
        $i = 0;
        foreach ($result as $key => $val) {
            $allcluster[$i]['id'] = $val['id'];
            $allcluster[$i]['latitude'] = $val['latitude'];
            $allcluster[$i]['longitude'] = $val['longitude'];
            $allcluster[$i]['name'] = $val['name'];
            $allcluster[$i]['images'] = $val['images'];

            $sqlSite = "Select * from embark_data where number = '" . $val['number'] . "' order by id desc Limit 1";
            $res = find_by_sql($sqlSite);
            $valSite = $res[0];

            $arrHeaders = explode(",", $valSite['COLNAME']);
            $arrvalues = explode(",", $valSite['COLVALUES']);
            $j = 0;
            foreach ($arrHeaders as $keyHead => $valHead) {

                $allcluster[$i]['details'][$j]['key'] = $valHead;
                $allcluster[$i]['details'][$j]['value'] = $arrvalues[$keyHead];
                $j++;
            }

            $allcluster[$i]['date'] = $valSite['DATETIME'];
            $i++;
        }

        if (sizeof($allcluster) > 0) {
            echo json_encode(array("response" => 0, "data" => $allcluster));
        } else {
            echo json_encode(array("response" => 1));
        }
        break;

    case "add-zone":

        $userid = $_SESSION['user_id'];
        $zonename = ( true == isset($_REQUEST["zonename"]) && "" != $_REQUEST["zonename"]) ? $_REQUEST["zonename"] : "";
        $numbers = ( true == isset($_REQUEST["numbers"]) && "" != $_REQUEST["numbers"]) ? $_REQUEST["numbers"] : "";
        $id = ( true == isset($_REQUEST["id"]) && "" != $_REQUEST["id"]) ? $_REQUEST["id"] : "";
        if ($id == "") {
            $sqlZone = "Select * from zone where name = '$zonename' and updated_by = '$userid' ";
            $result = find_by_sql($sqlZone);

            if (sizeof($result) > 0) {
                echo json_encode(array("result" => "error", "msg" => "Zone name already defined "));
                exit;
            } else {

                $arrNumbers = explode(",", $numbers);
                $boolInsert = false;

                $sqlZone = "Select unique_id as unique_id from zone order by unique_id desc Limit 1 ";
                $resultZone = find_by_sql($sqlZone);
                $arrResultZone = $resultZone[0];

                $unique_id = 0;
                if (sizeof($arrResultZone) > 0) {
                    $unique_id = $arrResultZone['unique_id'];
                    //foreach ($arrResultZone as $key => $val ) {
                    //  $unique_id = $val['unique_id'];
                    //    }
                }

                $unique_id = $unique_id + 1;
                foreach ($arrNumbers as $keyNumbers => $valNumbers) {
                    $insertZone = "Insert into zone ( unique_id, name, number , updated_by , created_date  ) ";
                    $insertZone .= " VALUES ";
                    $insertZone .= " ( '$unique_id' , '$zonename' , '$valNumbers' , '$userid' , '" . date("Y-m-d H:i:s") . "' ) ";
                    $resultZone = $db->query($insertZone);
                }

                echo json_encode(array("result" => "success", "msg" => "Zone successfully added!!"));
                exit;
            }
        }
        break;
    case "edit-zone":

        $userid = $_SESSION['user_id'];
        $zonename = ( true == isset($_REQUEST["zonename"]) && "" != $_REQUEST["zonename"]) ? $_REQUEST["zonename"] : "";
        $numbers = ( true == isset($_REQUEST["numbers"]) && "" != $_REQUEST["numbers"]) ? $_REQUEST["numbers"] : "";
        $id = ( true == isset($_REQUEST["id"]) && "" != $_REQUEST["id"]) ? $_REQUEST["id"] : "";
        $previous_numbers = ( true == isset($_REQUEST["previous_numbers"]) && "" != $_REQUEST["previous_numbers"]) ? $_REQUEST["previous_numbers"] : "";
        if ($id != "") {
            $sqlZone = "Select * from zone where name = '$zonename' and updated_by = '$userid' ";
            $result = find_by_sql($sqlZone);
            $arrResult = $result[0];
            $arrNumbers = explode(",", $numbers);
            $arrPrevNumbers = explode(",", $previous_numbers);
            $arrAllNumbers = array_merge($arrNumbers, $arrPrevNumbers);
            $arrAllNumbers = array_unique($arrAllNumbers);
            if (sizeof($arrPrevNumbers) > sizeof($arrNumbers)) {
                $arrDiff = array_diff(array_values($arrPrevNumbers), array_values($arrNumbers));
            } else {
                $arrDiff = array_diff(array_values($arrNumbers), array_values($arrPrevNumbers));
            }


            $selectUniqueId = "Select * from zone where number in (" . implode(",", $arrPrevNumbers) . ")  ";
            $resultUniqueId = find_by_sql($selectUniqueId);
            $arrResultUniqueId = $resultUniqueId[0];

            $unique_id = $arrResultUniqueId['unique_id'];
            /*   if (sizeof($arrResult) > 0) {
              $unique_id = $arrResult['unique_id'];
              //foreach ($arrResultZone as $key => $val ) {
              //  $unique_id = $val['unique_id'];
              //    }
              }

              $unique_id = $unique_id+1;
             */

            $sqlDelete = "Delete from zone where number in (" . implode(",", $arrAllNumbers) . ")  and unique_id = '$unique_id' ";
            $db->query($sqlDelete);


            foreach ($arrNumbers as $keyNumbers => $valNumbers) {
                $insertZone = "Insert into zone ( unique_id, name, number , updated_by , created_date  ) ";
                $insertZone .= " VALUES ";
                $insertZone .= " ( '$unique_id' , '$zonename' , '$valNumbers' , '$userid' , '" . date("Y-m-d H:i:s") . "' ) ";
                $resultZone = $db->query($insertZone);
            }


            if ($resultZone) {
                echo json_encode(array("result" => "success", "msg" => "Zone successfully updated!!"));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "Zone update failed to edit!!"));
                exit;
            }
        } else {
            echo json_encode(array("result" => "error", "msg" => "Zone update failed!!"));
            exit;
        }

        break;
    case "set-header-map":
        $header = ( true == isset($_REQUEST['header']) && "" != $_REQUEST['header'] ) ? $_REQUEST['header'] : "";
        $header_description = ( true == isset($_REQUEST['header_description']) && "" != $_REQUEST['header_description'] ) ? $_REQUEST['header_description'] : "";
        $type = ( true == isset($_REQUEST['type']) && "" != $_REQUEST['type'] ) ? $_REQUEST['type'] : "";
        $display = ( true == isset($_REQUEST['display']) && "" != $_REQUEST['display'] ) ? $_REQUEST['display'] : "";

        $select = "Select * from header_map where header = '$header'";
        $resHeaders = find_by_sql($select);

        if (sizeof($resHeaders) > 0) {
            echo json_encode(array("result" => "error", "msg" => "Header already exists!!"));
            exit;
        } else {

            $insertHeaderMap = "INSERT INTO header_map ( `header`, `name`, `type` , `display` ,`created_date`  ) VALUES ( '$header', '$header_description', '$type', '$display' , '" . date("Y-m-d H:i:s") . "' )  ";
            if ($db->query($insertHeaderMap)) {
                echo json_encode(array("result" => "success", "msg" => "Header added successfully!!"));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "Error adding header!!"));
                exit;
            }
        }
        exit;
        break;

    case "edit-header-operation":
        $id = ( true == isset($_REQUEST['id']) && "" != $_REQUEST['id'] ) ? $_REQUEST['id'] : "";
        $header = ( true == isset($_REQUEST['header']) && "" != $_REQUEST['header'] ) ? $_REQUEST['header'] : "";
        $header_description = ( true == isset($_REQUEST['header_description']) && "" != $_REQUEST['header_description'] ) ? $_REQUEST['header_description'] : "";
        $type = ( true == isset($_REQUEST['type']) && "" != $_REQUEST['type'] ) ? $_REQUEST['type'] : "";
        $display = ( true == isset($_REQUEST['display']) && "" != $_REQUEST['display'] ) ? $_REQUEST['display'] : "";

        $select = "Select * from header_map where id = '$id'";
        $resHeaders = find_by_sql($select);

        if (sizeof($resHeaders) > 0) {

            $update_sql = "Update header_map set   `header` = '$header' , name = '$header_description' , type = '$type'  , display = '$display'  where id = '$id' ";
            $db->query($update_sql);
            if ($db->affected_rows() > 0) {
                echo json_encode(array("result" => "success", "msg" => "Header updated successfully!!"));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "Unable to update Header!!"));
                exit;
            }
        } else {
            echo json_encode(array("result" => "error", "msg" => "Unable to update Header!!"));
            exit;
        }
        exit;
        break;
        
     case "zone-kld":

        $id = ( true == isset($_REQUEST['id']) && "" != $_REQUEST['id'] ) ? $_REQUEST['id'] : "";

        $select = "Select GROUP_CONCAT(number) as assigned_numbers from zone as s where s.unique_id = '$id' group by s.unique_id  order by s.id desc Limit 1 ";

        $resKld = find_by_sql($select);

        $resKld = $resKld[0];

        $number = $resKld['assigned_numbers'];

        if (sizeof($resKld) > 0) {

            $selectEmbData = "SELECT COLNAME , MAX(LENGTH(COLNAME)) FROM `embark_data` WHERE number IN ($number) ORDER BY `ID` DESC ";
            $resEmbData = find_by_sql($selectEmbData);

            if (sizeof($resEmbData) > 0) {
                $column = $resEmbData[0]['COLNAME'];
                $diffHeaderArr = array_diff(explode(",", $column), $arrNoHeader);
            }
             
           
            $cumulativeArr = array();
            $variableArr = array();

            // $selectRec = "Select * from embark_data where number IN ($number)  and DATE(datetime) = '".date("Y-m-d")."' and LENGTH(COLNAME) > 50 order by id asc ";
            $selectRec = "Select * from embark_data where number IN ($number)   and  LENGTH(COLNAME) > 50 and DATE(datetime) = (Select DATE(datetime) from embark_data where number IN ($number)  and  LENGTH(COLNAME) > 50   order by id desc Limit 1 )  order by id asc ";
            
            $resLast = find_by_sql($selectRec);
            $arrAllHeaders = array();
            $arrHeaders = array();
            $arrValues = array();
            foreach ($resLast as $keyLast => $valLast) {
                $arrHeaders = explode(",", $valLast["COLNAME"]);
                
                $id = $valLast["ID"];
                foreach ($arrHeaders as $keyHeaders => $valHeaders) {
                    if (!in_array($valHeaders, $arrNoHeader)) {
                        $arrAllHeaders[$valHeaders][] = 0;
                    }
                }
            }
            
             $rec_date = "";
            
            if( sizeof($resLast) > 0 ){
                $rec_date = date( "Y-m-d", strtotime($resLast[0]['DATETIME']));
            }
            
            
            
             foreach( $arrAllHeaders as $keyHeaders => $valHeaders ){
                
                
                $sqlHeaderMap = "Select * from header_map where header = '$keyHeaders'   ";
                $resCumulative = find_by_sql($sqlHeaderMap);
                
                if (empty($resCumulative) == false) {
                    
                    $resCumulative = $resCumulative[0];
                    if ($resCumulative['type'] == 'cumulative'  && $resCumulative['display'] == "yes" ) { 
                            
                           // echo $selectCumulative = "Select '$keyHeaders' as headr, GROUP_CONCAT(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$keyHeaders' , COLNAME))) as first, DATE(datetime) as rec_date, number  from embark_data where  number IN ( $number  ) and DATE(datetime) = '".date("Y-m-d")."' GROUP BY NUMBER  HAVING first IS NOT NULL   order by id desc";
                            
                            $selectCumulative = "Select '$keyHeaders' as headr, MAX(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$keyHeaders' , COLNAME))) as max_val, MIN(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$keyHeaders' , COLNAME))) as min_val, ( MAX(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$keyHeaders' , COLNAME))) - MIN(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$keyHeaders' , COLNAME)))  ) as difference, DATE(datetime) as rec_date, number  from embark_data where  number IN ( $number  ) and DATE(datetime) = '".date("Y-m-d")."' GROUP BY NUMBER   order by id desc";
                            
                            $resCumulativeArr = find_by_sql($selectCumulative);
                            $arrInitial = array();
                            $arrFinal = array();
                            foreach( $resCumulativeArr as $keyCumulative => $valCumulative ){
                                  
                                    $arrInitial[] = $valCumulative['min_val'];
                                    $arrFinal[] = $valCumulative['max_val'];
                                
                            } 
                            
                            
                            $totInitial = array_sum(array_values($arrInitial)) ;
                            $totFinal = array_sum(array_values($arrFinal)) ;
                            
                            $cumulativeArr[$keyHeaders]['initial'] = $totInitial;
                            $cumulativeArr[$keyHeaders]['final'] = $totFinal;
                            $cumulativeArr[$keyHeaders]['total'] = $totFinal - $totInitial;
                            $cumulativeArr[$keyHeaders]['name'] = $resCumulative['name'] ;// . ", " . $keyFinHeader;
                            
                    
                    }else if ( $resCumulative['display'] == "yes" ) {
                         $selectRec = "Select number, '$keyHeaders' as headr, AVG(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$keyHeaders' , COLNAME))) as avg , MAX(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$keyHeaders' , COLNAME))) as max , MIN(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$keyHeaders' , COLNAME))) as min, DATE(datetime) as rec_date  from embark_data where  number IN ( $number ) and DATE(datetime) = '".date("Y-m-d")."' group by number  order by id desc    ";
                        $resLast = find_by_sql($selectRec);
                        
                            $arrInitial = array();
                            $arrFinal = array();
                            $arrAvg = array();
                            foreach( $resLast as $keyCumulative => $valCumulative ){
                                    $arrInitial[] = $valCumulative['min'];
                                    $arrFinal[] =  $valCumulative['max'];
                                    $arrAvg[] = $valCumulative['avg'];
                            } 
                            
                            $totInitial = array_sum(array_values($arrInitial)) / count($arrInitial);
                            $totFinal = array_sum(array_values($arrFinal)) / count($arrFinal);
                            
                            $variableArr[$keyHeaders]['initial'] = $totInitial;
                            $variableArr[$keyHeaders]['final'] = $totFinal;
                            $variableArr[$keyHeaders]['average'] = array_sum(array_values($arrAvg)) / count($arrAvg);
                            $variableArr[$keyHeaders]['name'] = $resCumulative['name'];
                        
                    }
                    
                }else{
                         $selectRec = "Select number, '$keyHeaders' as headr, AVG(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$keyHeaders' , COLNAME))) as avg , MAX(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$keyHeaders' , COLNAME))) as max , MIN(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$keyHeaders' , COLNAME))) as min, DATE(datetime) as rec_date  from embark_data where  number IN ( $number ) and DATE(datetime) = '".date("Y-m-d")."' group by number  order by id desc    ";
                        $resLast = find_by_sql($selectRec);
                    
                        $arrInitial = array();
                            $arrFinal = array();
                            $arrAvg = array();
                            foreach( $resLast as $keyCumulative => $valCumulative ){
                                    $arrInitial[] = $valCumulative['min'];
                                    $arrFinal[] =  $valCumulative['max'];
                                    $arrAvg[] = $valCumulative['avg'];
                            } 
                            
                            $totInitial = array_sum(array_values($arrInitial)) / count($arrInitial);
                            $totFinal = array_sum(array_values($arrFinal)) / count($arrFinal);
                            
                            $variableArr[$keyHeaders]['initial'] = $totInitial;
                            $variableArr[$keyHeaders]['final'] = $totFinal;
                            $variableArr[$keyHeaders]['average'] = array_sum(array_values($arrAvg)) / count($arrAvg);
                            $variableArr[$keyHeaders]['name'] = $keyHeaders ;
                    
                }
                
                 
            }
            
            

            $tableCumulative = "";

            foreach ($cumulativeArr as $keyCumTab => $valCumTab) {
                $tableCumulative .= "<tr>";
                $tableCumulative .= "<td>" . $valCumTab['name'] . "</td>";
                $tableCumulative .= "<td>" . $valCumTab['total'] . "</td>";
                $tableCumulative .= "<td>" . $valCumTab['initial'] . "</td>";
                $tableCumulative .= "<td>" . $valCumTab['final'] . "</td>";
                $tableCumulative .= "<td><i data-param='$keyCumTab' class='zmdi zmdi-trending-up action'></i></td>";
                $tableCumulative .= "</tr>";
            }


            $tableVariable = "";

            foreach ($variableArr as $keyVarTab => $valVarTab) {
                $tableVariable .= "<tr>";
                $tableVariable .= "<td>" . $valVarTab['name'] . "</td>";
                $tableVariable .= "<td>" . $valVarTab['average'] . "</td>";
                $tableVariable .= "<td>" . $valVarTab['initial'] . "</td>";
                $tableVariable .= "<td>" . $valVarTab['final'] . "</td>";
                $tableVariable .= "<td><i data-param='$keyVarTab' class='zmdi zmdi-trending-up action'></i></td>";
                $tableVariable .= "</tr>";
            }

            $param = $_SESSION['cumulative'];

            $selectRec = "Select   SUM(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) as val   from embark_data where  number IN (  $number  ) and DATE(datetime) =  '".date("Y-m-d")."'    ";
            $resKld = find_by_sql($selectRec);
            $value = 0;

            if (sizeof($resKld) > 0) {
                $resKld = $resKld[0];
                if ($resKld['val'] != "") {
                    $value = number_format((floatval($resKld['val']) / sizeof($resKld)), 2, ".", "");
                }
            }

            if (empty($cumulativeArr) == false || empty($variableArr) == false) {
                echo json_encode(array("result" => "success", "msg" => "Record found ", "cumulative" => $tableCumulative, "variable" => $tableVariable, "kld" => $value , "rec_date" => $rec_date ));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "Record Not found "));
                exit;
            }
        } else {

            echo json_encode(array("result" => "error", "msg" => "No record found!!"));
            exit;
        }

        break;
        
    case "site-kld":

        $id = ( true == isset($_REQUEST['id']) && "" != $_REQUEST['id'] ) ? $_REQUEST['id'] : "";

        $select = "Select s.* from sites as s where s.id = '$id'   order by s.id desc Limit 1 ";

        $resKld = find_by_sql($select);
        
        

        $resKld = $resKld[0];

        $number = $resKld['number'];

        if (sizeof($resKld) > 0) {

            $selectEmbData = "SELECT COLNAME , MAX(LENGTH(COLNAME)) FROM `embark_data` WHERE number = '$number' ORDER BY `ID` DESC ";
            $resEmbData = find_by_sql($selectEmbData);

            if (sizeof($resEmbData) > 0) {
                $column = $resEmbData[0]['COLNAME'];
                $diffHeaderArr = array_diff(explode(",", $column), $arrNoHeader);
            }

            $cumulativeArr = array();
            $variableArr = array();

           // $selectRec = "Select * from embark_data where number = '$number' and DATE(datetime) = '".date("Y-m-d")."' and LENGTH(COLNAME) > 35 order by id asc ";
            $selectRec = "Select * from embark_data where number = '$number'  and  LENGTH(COLNAME) > 20 and DATE(datetime) = (Select DATE(datetime) from embark_data where number = '$number' and  LENGTH(COLNAME) > 20   order by id desc Limit 1 )  order by id asc ";
            $resLast = find_by_sql($selectRec);
            $arrAllHeaders = array();
            $arrHeaders = array();
            $arrValues = array();
            foreach ($resLast as $keyLast => $valLast) {
                $arrHeaders = explode(",", $valLast["COLNAME"]);
                $arrValues = explode(",", $valLast["COLVALUES"]);
                $id = $valLast["ID"];
                foreach ($arrHeaders as $keyHeaders => $valHeaders) {
                    if (!in_array($valHeaders, $arrNoHeader)) {
                        $arrAllHeaders[$valHeaders][] = $arrValues[$keyHeaders];
                    }
                }
            }

            

            $rec_date = "";
            
            if( sizeof($resLast) > 0 ){
                $rec_date = date( "Y-m-d", strtotime($resLast[0]['DATETIME']));
            }

            

            foreach ($arrAllHeaders as $keyFinHeader => $valFinHeader) {
                
                $keyFinHeaderHead = str_replace("~","", $keyFinHeader);
                
                $sqlHeaderMap = "Select * from header_map where TRIM(header) = '$keyFinHeaderHead'   ";
                $resCumulative = find_by_sql($sqlHeaderMap);
                 
                

                if (empty($resCumulative) == false) {

                    $resCumulative = $resCumulative[0];
                    if ($resCumulative['type'] == 'cumulative' && $resCumulative['display'] == "yes" ) {
                        $cumulativeArr[$keyFinHeader]['initial'] = $valFinHeader[0];
                        $cumulativeArr[$keyFinHeader]['final'] = $valFinHeader[sizeof($valFinHeader) - 1];
                        $cumulativeArr[$keyFinHeader]['total'] = number_format($cumulativeArr[$keyFinHeader]['final'] - $cumulativeArr[$keyFinHeader]['initial'], 2, ".", "");
                        $cumulativeArr[$keyFinHeader]['name'] = $resCumulative['name'] ;// . ", " . $keyFinHeader;
                    } else if( $resCumulative['display'] == "yes" ) {
                        $variableArr[$keyFinHeader]['initial'] = min($valFinHeader);
                        $variableArr[$keyFinHeader]['final'] = max($valFinHeader);
                        $variableArr[$keyFinHeader]['average'] = number_format(array_sum($valFinHeader) / count($valFinHeader), 2, ".", "");
                        $variableArr[$keyFinHeader]['name'] = $resCumulative['name'] ;// . ", " . $keyFinHeader;
                    }
                } else {
                    $variableArr[$keyFinHeader]['initial'] = min($valFinHeader);
                    $variableArr[$keyFinHeader]['final'] = max($valFinHeader);
                    $variableArr[$keyFinHeader]['average'] = number_format(array_sum($valFinHeader) / count($valFinHeader), 2, ".", "");
                    $variableArr[$keyFinHeader]['name'] = $keyFinHeader;
                }
            }


            $tableCumulative = "";

            foreach ($cumulativeArr as $keyCumTab => $valCumTab) {
                $tableCumulative .= "<tr>";
                $tableCumulative .= "<td>" . $valCumTab['name'] . "</td>";
                $tableCumulative .= "<td>" . $valCumTab['total'] . "</td>";
                $tableCumulative .= "<td>" . $valCumTab['initial'] . "</td>";
                $tableCumulative .= "<td>" . $valCumTab['final'] . "</td>";
                $tableCumulative .= "<td><i data-param='$keyCumTab' class='zmdi zmdi-trending-up action'></i></td>";
                $tableCumulative .= "</tr>";
            }


            $tableVariable = "";

            foreach ($variableArr as $keyVarTab => $valVarTab) {
                $tableVariable .= "<tr>";
                $tableVariable .= "<td>" . $valVarTab['name'] . "</td>";
                $tableVariable .= "<td>" . $valVarTab['average'] . "</td>";
                $tableVariable .= "<td>" . $valVarTab['initial'] . "</td>";
                $tableVariable .= "<td>" . $valVarTab['final'] . "</td>";
                $tableVariable .= "<td><i data-param='$keyVarTab' class='zmdi zmdi-trending-up action'></i></td>";
                $tableVariable .= "</tr>";
            }

             $param = $_SESSION['cumulative'];
            
            $selectCummulative = "Select * from header_map where header = '$param' "; 
            
            $resCumulativePar = find_by_sql($selectCummulative);
            $isCumulative = "variable";
            if( sizeof($resCumulativePar) > 0 ){
                $isCumulative  = $resCumulativePar[0]['type'];
            }

            
            $value = 0;
            
            if ( $isCumulative == "variable"  ) {
                $selectRec = "Select   SUM(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) as val, SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME)) as cVal   from embark_data where  number = '$number' and DATE(datetime) =  '".date("Y-m-d")."' order by datetime desc   ";
                $resKld = find_by_sql($selectRec);
                if( sizeof($resKld) > 0 ){
                    $kldArr = $resKld;
                    $resKld = $resKld[0];
                    if ( $resKld['val'] != "" ) {
                        $value = number_format((floatval($resKld['val']) / sizeof($kldArr)), 2, ".", "");
                    } 
                }
            }else{
                $selectRec = "Select  SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME)) as cVal   from embark_data where  number = '$number' and DATE(datetime) =  '".date("Y-m-d")."' order by datetime desc   ";
                $resKld = find_by_sql($selectRec);
                
                if( sizeof($resKld) > 0 ){
                    $resKld = $resKld[0];
                    $value = number_format((floatval($resKld['cVal']) ), 2, ".", "");
                }
            }

            if ( empty($cumulativeArr) == false || empty($variableArr) == false ) {
                echo json_encode(array("result" => "success", "msg" => "Record found ", "cumulative" => $tableCumulative, "variable" => $tableVariable, "kld" => $value , "rec_date" =>  $rec_date));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "Record Not found "));
                exit;
            }
        } else {

            echo json_encode(array("result" => "error", "msg" => "No record found!!"));
            exit;
        }

        break;
        
        
    case "get-zone-parameter-trend":

        $id = ( true == isset($_REQUEST['id']) && "" != $_REQUEST['id'] ) ? $_REQUEST['id'] : "";
        $param = ( true == isset($_REQUEST['param']) && "" != $_REQUEST['param'] ) ? $_REQUEST['param'] : "";
        $duration = ( true == isset($_REQUEST['duration']) && "" != $_REQUEST['duration'] ) ? $_REQUEST['duration'] : "";
        $dateFrom = date("Y-m-d", strtotime(" -1 " . $duration));

        $sqlHeaderMap = "Select * from header_map where header = '$param' and type = 'cumulative'  ";
        $resCumulative = find_by_sql($sqlHeaderMap);

        $cumulativeFlag = false;
        if (sizeof($resCumulative) > 0) {
            $cumulativeFlag = true;
        }

        $sqlSelectSites = "Select GROUP_CONCAT(number) as assigned_numbers from zone as s where s.unique_id = '$id' group by s.unique_id  order by s.id desc Limit 1";
        $arrSites = find_by_sql($sqlSelectSites);
        $arrSites = $arrSites[0];
        $number = $arrSites['assigned_numbers'];
        $arrAllHeaders = array();
        if ($duration == "DAY") {

            $timeHr = array();

            for ($j = 0; $j <= date("H"); $j++) {
                $timeHr[] = $num = sprintf("%02d", $j);
            }

            $kldStepVals = array();
            $kldStepDate = array();
            

                if ($cumulativeFlag == true) { 
                    
                    $selectRec = "Select (  MAX(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) - MIN(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) )  as parVal , CONCAT(HOUR(TIMES),':',MINUTE(TIMES)) as TIMES from embark_data where number IN ( $number ) and DATE(datetime) =  '".date("Y-m-d")."' GROUP BY number,HOUR(TIMES)  order by id asc  ";
                        
                }else{
                    $selectRec = "Select AVG(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) as parVal , CONCAT(HOUR(TIMES),':',MINUTE(TIMES)) as TIMES   from embark_data where number IN ( $number ) and DATE(datetime) =  '".date("Y-m-d")."' GROUP BY number,HOUR(TIMES)  order by id asc  ";
                }
            
            $resLast = find_by_sql($selectRec);
            $arrOccurance = array();
            foreach ($resLast as $keyHr => $valLast) {
                $intHr = intval(date("H", strtotime($valLast["TIMES"]))); 
                $arrOccurance[$intHr]['values'][] = floatval($valLast['parVal']);
            }
            
           
            foreach ($timeHr as $keyTimeHr => $valhr) {
                if (isset($arrOccurance[$keyTimeHr]['values'])) {
                    if ($cumulativeFlag == true) { 
                        $kldStepVals[$keyTimeHr] = number_format(array_sum(array_values($arrOccurance[$keyTimeHr]['values'])) , 2, ".", ""); 
                    }else{
                        $vals = array_sum(array_values($arrOccurance[$keyTimeHr]['values'])) / count($arrOccurance[$keyTimeHr]['values']);
                        $kldStepVals[$keyTimeHr] = number_format( $vals, 2, ".", ""); 
                    }
                } else {
                    $kldStepVals[$keyTimeHr] = 0;
                }
                $kldStepDate[$keyTimeHr] = $keyTimeHr . ":00";
            }


            if (sizeof($kldStepVals) > 0) {
                echo json_encode(array("result" => "success", "msg" => "Record found!!", "values" => $kldStepVals, "period" => $kldStepDate));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "No record found!!"));
                exit;
            }
        } else {

            $days = ( true == isset($duration) && "WEEK" == $duration ) ? 7 : 30;

            if ($days == 30) {
                $days = ( true == isset($duration) && "QUARTER" == $duration ) ? 90 : 30;
            }

            $dateArr = getLastNDays($days, "Y-m-d");

            $kldStepVals = array();
            $kldStepDate = array();

            $selectDates = find_by_sql("Select DATE(datetime) as dates from embark_data where number IN ($number) and DATE(datetime) IN ('" . implode("','", $dateArr) . "')   ");
            $allDates = array();

            foreach ($selectDates as $dateKey => $dateVal) {
                $allDates[$dateVal['dates']] = $dateVal['dates'];
            }
 

            foreach ($dateArr as $keyDate => $valDate) {
                $time = time();
                
                if ($cumulativeFlag == true) { 
                    $selectRec = "Select (  MAX(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) - MIN(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) )  as parVal , DATE(datetime) as rec_date from embark_data where number IN ( $number ) and DATE(datetime) =  '".$valDate."' GROUP BY number,DATE(datetime)   order by id asc  ";
                 }else{
                    $selectRec = "Select AVG(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) as parVal , DATE(datetime) as rec_date  from embark_data where number IN ( $number ) and DATE(datetime) =  '".$valDate."' GROUP BY number,DATE(datetime)   order by id asc  ";
                }
               
                $resLast = find_by_sql($selectRec);
                 $arrOccurance = array();
                if( sizeof( $resLast ) > 0 ){
               
                foreach ($resLast as $keyHr => $valLast) {
                    $intHr = (date("Y-m-d", strtotime($valLast["rec_date"]))); 
                    $arrOccurance[$intHr]['values'][] = floatval($valLast['parVal']);
                }
                
                
        
                foreach ($arrOccurance as $keyOccDay => $valOccDay) {
                    if ($cumulativeFlag == true) { 
                        $kldStepVals[$keyDate] = number_format(array_sum(array_values($valOccDay['values'])) , 2, ".", ""); 
                    }else{
                        $vals = array_sum(array_values($valOccDay['values'])) / count($valOccDay['values']);
                        $kldStepVals[$keyDate] = number_format( $vals, 2, ".", ""); 
                    }

                    $kldStepDate[$keyDate] = date("M-d", strtotime($valDate));
                }
                
                } else {
                    $kldStepVals[$keyDate] = 0; 
                    $kldStepDate[$keyDate] = date("M-d", strtotime($valDate));
                }
                 
                
            }


            if (sizeof($kldStepVals) > 0) {
                echo json_encode(array("result" => "success", "msg" => "Record found!!", "values" => $kldStepVals, "period" => $kldStepDate));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "No record found!!"));
                exit;
            }
        }


        break;
    
    case "get-parameter-trend":

        $id = ( true == isset($_REQUEST['id']) && "" != $_REQUEST['id'] ) ? $_REQUEST['id'] : "";
        $param = ( true == isset($_REQUEST['param']) && "" != $_REQUEST['param'] ) ? $_REQUEST['param'] : "";
        $duration = ( true == isset($_REQUEST['duration']) && "" != $_REQUEST['duration'] ) ? $_REQUEST['duration'] : "";
        $dateFrom = date("Y-m-d", strtotime(" -1 " . $duration));

        $sqlHeaderMap = "Select * from header_map where header = '$param' and type = 'cumulative'  ";
        $resCumulative = find_by_sql($sqlHeaderMap);

        $cumulativeFlag = false;
        if (sizeof($resCumulative) > 0) {
            $cumulativeFlag = true;
        }

        $sqlSelectSites = "Select * from sites where status = 'active'  and  id = '{$id}'  ";
        $arrSites = find_by_sql($sqlSelectSites);
        $arrSites = $arrSites[0];
        $number = $arrSites['number'];
        $arrAllHeaders = array();
        if ($duration == "DAY") {

            $timeHr = array();

            for ($j = 0; $j <= date("H"); $j++) {
                $timeHr[] = $num = sprintf("%02d", $j);
            }

            $kldStepVals = array();
            $kldStepDate = array();

            $selectRec = "Select * from embark_data where number = '$number' and DATE(datetime) =  '".date("Y-m-d")."' order by id asc  ";
            $resLast = find_by_sql($selectRec);
            $arrOccurance = array();
            foreach ($resLast as $keyHr => $valLast) {
                $intHr = intval(date("H", strtotime($valLast["TIMES"])));

                $arrHeaders = explode(",", $valLast["COLNAME"]);
                $arrValues = explode(",", $valLast["COLVALUES"]);
                $arrCom = array_combine($arrHeaders, $arrValues);
                $arrOccurance[$intHr]['values'][] = floatval($arrCom[$param]);
            }


            foreach ($timeHr as $keyTimeHr => $valhr) {
                if (isset($arrOccurance[$keyTimeHr]['values'])) {
                    if ($cumulativeFlag == true) {
                        $arrValOcc = $arrOccurance[$keyTimeHr]['values'];
                        $arrValOcc = array_values(array_diff($arrValOcc, [0]));
                        $kldStepVals[$keyTimeHr] = number_format($arrValOcc[sizeof($arrValOcc) - 1] - $arrValOcc[0], 2, ".", "");
                    } else {
                        $arrCount = array_filter($arrOccurance[$keyTimeHr]['values']);
                        $kldStepVals[$keyTimeHr] = number_format(array_sum($arrOccurance[$keyTimeHr]['values']) / count($arrCount), 2, ".", "");
                    }
                } else {
                    $kldStepVals[$keyTimeHr] = 0;
                }
                $kldStepDate[$keyTimeHr] = $keyTimeHr . ":00";
            }


            if (sizeof($kldStepVals) > 0) {
                echo json_encode(array("result" => "success", "msg" => "Record found!!", "values" => $kldStepVals, "period" => $kldStepDate));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "No record found!!"));
                exit;
            }
        } else {

            $days = ( true == isset($duration) && "WEEK" == $duration ) ? 7 : 30;

            if ($days == 30) {
                $days = ( true == isset($duration) && "QUARTER" == $duration ) ? 90 : 30;
            }

            $dateArr = getLastNDays($days, "Y-m-d");

            $kldStepVals = array();
            $kldStepDate = array();

            $selectDates = find_by_sql("Select DATE(datetime) as dates from embark_data where number = '$number' and DATE(datetime) IN ('" . implode("','", $dateArr) . "')   ");
            $allDates = array();

            foreach ($selectDates as $dateKey => $dateVal) {
                $allDates[$dateVal['dates']] = $dateVal['dates'];
            }

//            print_r($allDates);

            foreach ($dateArr as $keyDate => $valDate) {
                $time = time();
                if (isset($allDates[$valDate])) {
                    $selectRec = "Select * from embark_data where number = '$number' and DATE(datetime) = '$valDate' and LENGTH(COLNAME) > 35  order by id asc Limit 50   ";
                    $resLast = find_by_sql($selectRec);

                    //echo  time() - $time   ." / ";

                    if (sizeof($resLast) > 0) {
                        $arrOccurance = array();
                        foreach ($resLast as $keyHr => $valLast) {
                            $arrHeaders = explode(",", $valLast["COLNAME"]);
                            $arrValues = explode(",", $valLast["COLVALUES"]);
                            $arrCom = array_combine($arrHeaders, $arrValues);
                            $arrOccurance[$valDate][] = floatval($arrCom[$param]);
                        }
                    } else {
                        $arrOccurance[$valDate][] = 0;
                    }
                } else {
                    $arrOccurance[$valDate][] = 0;
                }

                foreach ($arrOccurance as $keyOccDay => $valOccDay) {
                    if ($cumulativeFlag == true) {
                        $arrValOcc = $valOccDay;
                        $arrValOcc = array_values(array_diff($arrValOcc, [0]));
                        $kldStepVals[$keyDate] = number_format($arrValOcc[sizeof($arrValOcc) - 1] - $arrValOcc[0], 2, ".", "");
                    } else {
                        $arrCount = array_filter($valOccDay);
                        $kldStepVals[$keyDate] = number_format(array_sum($valOccDay) / count($arrCount), 2, ".", "");
                    }

                    $kldStepDate[$keyDate] = date("M-d", strtotime($keyOccDay));
                }
            }


            if (sizeof($kldStepVals) > 0) {
                echo json_encode(array("result" => "success", "msg" => "Record found!!", "values" => $kldStepVals, "period" => $kldStepDate));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "No record found!!"));
                exit;
            }
        }


        break;
    case "get-parameter-trends":
        $id = ( true == isset($_REQUEST['id']) && "" != $_REQUEST['id'] ) ? $_REQUEST['id'] : "";
        $param = ( true == isset($_REQUEST['param']) && "" != $_REQUEST['param'] ) ? $_REQUEST['param'] : "";
        $duration = ( true == isset($_REQUEST['duration']) && "" != $_REQUEST['duration'] ) ? $_REQUEST['duration'] : "";
        $dateFrom = date("Y-m-d", strtotime(" -1 " . $duration));

        $sqlSelectSites = "Select * from sites where status = 'active'  and  id = '{$id}'  ";
        $arrSites = find_by_sql($sqlSelectSites);
        $arrSites = $arrSites[0];
        $number = $arrSites['number'];

        if ($duration == "DAY") {

            $timeHr = array();

            for ($j = 0; $j <= date("H"); $j++) {
                $timeHr[] = $num = sprintf("%02d", $j);
            }

            //print_r($timeHr);

            $kldStepVals = array();
            $kldStepDate = array();
            $i = 0;
            foreach ($timeHr as $keyHr => $valHr) {

                $sqlSelect = "SELECT  AVG( " . $param . "  ) as avgd ,  created_date  FROM mbr_log where  DATE(created_date)  = '" . date("Y-m-d") . "' and HOUR(created_date) = '$valHr'    and number = '$number'    order by created_date asc";

                $resSql = find_by_sql($sqlSelect);


                if (sizeof($resSql) > 0) {
                    foreach ($resSql as $keyKld => $valKld) {

                        if (isset($valKld['avgd']) && $valKld['avgd'] != "") {
                            $kldStepVals[$i] = number_format((intval($valKld['avgd']) * 0.01), 2, '.', '');
                        } else {
                            $kldStepVals[$i] = "0";
                        }


                        $kldStepDate[$i] = $valHr . ":00";
                    }
                } else {
                    $kldStepVals[$i] = "0";
                    $kldStepDate[$i] = $valHr . ":00";
                }
                $i++;
            }

            if (sizeof($kldStepVals) > 0) {
                echo json_encode(array("result" => "success", "msg" => "Record found!!", "values" => $kldStepVals, "period" => $kldStepDate));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "No record found!!"));
                exit;
            }
        } else {

            $days = ( true == isset($duration) && "WEEK" == $duration ) ? 7 : 30;

            if ($days == 30) {
                $days = ( true == isset($duration) && "QUARTER" == $duration ) ? 90 : 30;
            }


            $dateArr = getLastNDays($days, "Y-m-d");
            $kldStepVals = array();
            $kldStepDate = array();
            $i = 0;
            foreach ($dateArr as $keyDate => $valDate) {
                $sqlSelect = "SELECT  AVG( " . $param . "  ) as avgd  FROM mbr_log where  DATE(created_date) = '$valDate' and number = '$number'    order by created_date asc";
                $resSql = find_by_sql($sqlSelect);
//                print_r($resSql);
                $kldStepVals[$i] = "0";
                foreach ($resSql as $keyKld => $valKld) {

                    if (isset($valKld['avgd']) && $valKld['avgd'] != "") {
                        $kldStepVals[$i] = number_format((intval($valKld['avgd']) * 0.01), 2, '.', '');
                    } else {
                        $kldStepVals[$i] = "0";
                    }
                }


                $kldStepDate[$i] = date("M-d", strtotime($valDate));
                $i++;
            }


            if (sizeof($kldStepVals) > 0) {
                echo json_encode(array("result" => "success", "msg" => "Record found!!", "values" => $kldStepVals, "period" => $kldStepDate));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "No record found!!"));
                exit;
            }
        }

        break;

    case "edit-columns":
        $id = ( true == isset($_REQUEST["id"]) && "" != $_REQUEST["id"]) ? $_REQUEST["id"] : "";
        $sql = "Select * from cluster_columns where id = '$id' and sitekey = '" . $_SESSION['LOG_KEY'] . "' ";
        $result = mysqli_query($link, $sql);
        $allcluster = array();
        while ($val = mysqli_fetch_assoc($result)) {
            $allcluster['id'] = $val['id'];
            $allcluster['columnname'] = $val['name'];
            $allcluster['headers'] = $val['headers'];
            $allcluster['cluster'] = $val['cluster'];
        }
        echo json_encode($allcluster);
        break;

    case "delete-columns":

        $id = ( true == isset($_REQUEST["id"]) && "" != $_REQUEST["id"]) ? $_REQUEST["id"] : "";
        $sql = "Update cluster_columns set status = 'inactive' where id = '$id' and sitekey = '" . $_SESSION['LOG_KEY'] . "' ";

        if (mysqli_query($link, $sql)) {
            echo $msg = 1;
        } else {
            echo $msg = 0;
        }

        break;


    case "get-columns":

        $sql = " SELECT `id`, `name` FROM `cluster_columns` WHERE status = 'active' and sitekey = '" . $_SESSION['LOG_KEY'] . "' ";
        $result = mysqli_query($link, $sql);
        $num = mysqli_num_rows($result);

        if (intval($num) > 0) {
            $intco = 0;
            $allcluster = array();
            while ($rows = mysqli_fetch_assoc($result)) {
                $allcluster[$intco]['name'] = $rows['name'];
                $allcluster[$intco]['id'] = $rows['id'];
                $intco++;
            }

            echo json_encode(array("key" => 0, "val" => $allcluster));
        } else {
            echo json_encode(array("key" => 1));
        }

        break;

    case "set-column-headers":
        $id = ( true == isset($_REQUEST["id"]) && "" != $_REQUEST["id"]) ? $_REQUEST["id"] : "";
        $name = ( true == isset($_REQUEST["name"]) && "" != $_REQUEST["name"]) ? $_REQUEST["name"] : "";
        $headers = ( true == isset($_REQUEST["headers"]) && "" != $_REQUEST["headers"]) ? $_REQUEST["headers"] : "";
        $created_date = date("Y-m-d");

        $sqlColumns = " Insert into `cluster_columns` 
                ( `name`, `cluster`, `headers` , `created_date`, `sitekey`) 
                VALUES ( '$name' , '$id', '$headers', '$created_date',  '" . $_SESSION['LOG_KEY'] . "' )";

        if (mysqli_query($link, $sqlColumns)) {
            echo $msg = 1;
        } else {
            echo $msg = 0;
        }

        exit;

        break;

    case "edit-column-headers":
        $id = ( true == isset($_REQUEST["id"]) && "" != $_REQUEST["id"]) ? $_REQUEST["id"] : "";
        $idcolumn = ( true == isset($_REQUEST["idcolumn"]) && "" != $_REQUEST["idcolumn"]) ? $_REQUEST["idcolumn"] : "";
        $name = ( true == isset($_REQUEST["name"]) && "" != $_REQUEST["name"]) ? $_REQUEST["name"] : "";
        $headers = ( true == isset($_REQUEST["headers"]) && "" != $_REQUEST["headers"]) ? $_REQUEST["headers"] : "";
        $created_date = date("Y-m-d");

        $sqlColumnsUpdate = " Update `cluster_columns` 
                set `name` = '$name' , `cluster` = '$id', `headers` = '$headers' where id = '$idcolumn' and sitekey = '" . $_SESSION['LOG_KEY'] . "'  ";
        mysqli_query($link, $sqlColumnsUpdate);
        if (mysqli_affected_rows($link)) {
            echo $msg = 1;
        } else {
            echo $msg = 0;
        }

        exit;
        
    case "get-trend":
        $id = ( true == isset($_REQUEST['id']) && "" != $_REQUEST['id'] ) ? $_REQUEST['id'] : "";
        $param = ( true == isset($_REQUEST['param']) && "" != $_REQUEST['param'] ) ? $_REQUEST['param'] : "";
        $duration = ( true == isset($_REQUEST['duration']) && "" != $_REQUEST['duration'] ) ? $_REQUEST['duration'] : "";
        $dateFrom = date("Y-m-d", strtotime(" -1 " . $duration));

        $sqlSelectSites = "Select * from sites where status = 'active'  and  id = '{$id}'  ";
        $arrSites = find_by_sql($sqlSelectSites);
        $arrSites = $arrSites[0];
        $number = $arrSites['number'];
        
        
        $sqlHeaderMap = "Select * from header_map where header = '$param' and type = 'cumulative'  ";
        $resCumulative = find_by_sql($sqlHeaderMap);

        $cumulativeFlag = false;
        if (sizeof($resCumulative) > 0) {
            $cumulativeFlag = true;
        }

        if ($duration == "DAY") {

            $timeHr = array();

            for ($j = 0; $j <= date("H"); $j++) {
                $timeHr[] = $num = sprintf("%02d", $j);
            }
 

            $kldStepVals = array();
            $kldStepDate = array();
            $i = 0;
            foreach ($timeHr as $keyHr => $valHr) {

                // $sqlSelect = "SELECT  AVG( " . $param . "  ) as avgd ,  created_date  FROM mbr_log where  DATE(created_date)  = '".date("Y-m-d")."' and HOUR(created_date) = '$valHr'  and " . $param . "  <> 0   and number = '$number'    order by created_date asc";

                if ($cumulativeFlag == true) { 
                    
                    $sqlSelect = "Select (  MAX(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) - MIN(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) )  as avgd , CONCAT(HOUR(TIMES),':',MINUTE(TIMES)) as TIMES from embark_data where number IN ( $number ) and DATE(datetime) =  '".date("Y-m-d")."'  order by id asc  ";
                        
                }else{
                    $sqlSelect = "Select AVG(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) as avgd , CONCAT(HOUR(TIMES),':',MINUTE(TIMES)) as TIMES   from embark_data where number IN ( $number ) and DATE(datetime) =  '".date("Y-m-d")."'  order by id asc  ";
                }


                $resSql = find_by_sql($sqlSelect);


                if (sizeof($resSql) > 0) {
                    foreach ($resSql as $keyKld => $valKld) {

                        if (isset($valKld['avgd']) && $valKld['avgd'] != "") {
                            $kldStepVals[$i] = number_format((($valKld['avgd'])), 2, '.', '');
                        } else {
                            $kldStepVals[$i] = "0";
                        }


                        $kldStepDate[$i] = $valHr . ":00";
                    }
                } else {
                    $kldStepVals[$i] = "0";
                    $kldStepDate[$i] = $valHr . ":00";
                }
                $i++;
            }

            if (sizeof($kldStepVals) > 0) {
                echo json_encode(array("result" => "success", "msg" => "Record found!!", "values" => $kldStepVals, "period" => $kldStepDate));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "No record found!!"));
                exit;
            }
        } else if ($duration == "DAILY") { 
            
            
           $period = getDatesFromRange(date('Y-m-d',strtotime('last sunday')), date('Y-m-d') );
            
            $kldStepVals = array();
            $kldStepDate = array();
            $i = 0;
            foreach ($period as $keyHr => $valHr) {

                // $sqlSelect = "SELECT  AVG( " . $param . "  ) as avgd ,  created_date  FROM mbr_log where  DATE(created_date)  = '".$valHr."'   and number = '$number' and " . $param . "  <> 0    order by created_date asc";
                
                if ($cumulativeFlag == true) { 
                    
                    $sqlSelect = "Select (  MAX(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) - MIN(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) )  as avgd , CONCAT(HOUR(TIMES),':',MINUTE(TIMES)) as TIMES from embark_data where number IN ( $number ) and DATE(datetime)  = '".$valHr."'   order by id asc  ";
                        
                }else{
                    $sqlSelect = "Select AVG(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) as avgd , CONCAT(HOUR(TIMES),':',MINUTE(TIMES)) as TIMES   from embark_data where number IN ( $number ) and DATE(datetime)  = '".$valHr."'  order by id asc  ";
                }
                
                
                $resSql = find_by_sql($sqlSelect);


                if (sizeof($resSql) > 0) {
                    foreach ($resSql as $keyKld => $valKld) {

                        if (isset($valKld['avgd']) && $valKld['avgd'] != "") {
                            $kldStepVals[$i] = number_format((($valKld['avgd'])), 2, '.', '');
                        } else {
                            $kldStepVals[$i] = "0";
                        }


                        $kldStepDate[$i] =  date("D M j" , strtotime($valHr));
                    }
                } else {
                    $kldStepVals[$i] = "0";
                    $kldStepDate[$i] = date("D M j" , strtotime($valHr));
                }
                $i++;
            }

            if (sizeof($kldStepVals) > 0) {
                echo json_encode(array("result" => "success", "msg" => "Record found!!", "values" => $kldStepVals, "period" => $kldStepDate));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "No record found!!"));
                exit;
            } 
            
            
        }else if ($duration == "WEEK") { 
         
            $arrWeeks = getWeekSunSat();
         
            $kldStepVals = array();
            $kldStepDate = array();
            $i = 0;
            foreach ($arrWeeks as $keyHr => $valHr) {

                $start = $valHr['start'];
                $end = $valHr['end'];
                
                // $sqlSelect = "SELECT  AVG( " . $param . "  ) as avgd ,  created_date  FROM mbr_log where (  DATE(created_date)  >= '".$start."' and DATE(created_date)  <= '".$end."' )   and number = '$number' and " . $param . "  <> 0    order by created_date asc";

                if ($cumulativeFlag == true) { 
                    
                    $sqlSelect = "Select (  MAX(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) - MIN(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) )  as avgd , CONCAT(HOUR(TIMES),':',MINUTE(TIMES)) as TIMES from embark_data where number IN ( $number ) and (  DATE(datetime)  >= '".$start."' and DATE(datetime)  <= '".$end."' )    order by id asc  ";
                        
                }else{
                    $sqlSelect = "Select AVG(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) as avgd , CONCAT(HOUR(TIMES),':',MINUTE(TIMES)) as TIMES   from embark_data where number IN ( $number ) and (  DATE(datetime)  >= '".$start."' and DATE(datetime)  <= '".$end."' )    order by id asc  ";
                }

                $resSql = find_by_sql($sqlSelect);


                if (sizeof($resSql) > 0) {
                    foreach ($resSql as $keyKld => $valKld) {

                        if (isset($valKld['avgd']) && $valKld['avgd'] != "") {
                            $kldStepVals[$i] = number_format((($valKld['avgd'])), 2, '.', '');
                        } else {
                            $kldStepVals[$i] = "0";
                        }

                        $kldStepDate[$i] =  date("D M j" , strtotime($start))." - ".date("D M j" , strtotime($end)); 
                    }
                } else {
                    $kldStepVals[$i] = "0";
                    $kldStepDate[$i] = date("D M j" , strtotime($start))." - ".date("D M j" , strtotime($end));
                }
                $i++;
            }

            if (sizeof($kldStepVals) > 0) {
                echo json_encode(array("result" => "success", "msg" => "Record found!!", "values" => $kldStepVals, "period" => $kldStepDate));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "No record found!!"));
                exit;
            } 
                 
                 
        }else if ($duration == "MONTHLY"){
            
            $first_day_this_month = date('Y-m-01'); 
            $days = dateDiffInDays($first_day_this_month , date("Y-m-d"));
            $days = $days + 1;
            $dateArr = array(1, 2, 3,4 , 5 ,6, 7, 8,9 , 10 ,11 ,12 );
            $kldStepVals = array();
            $kldStepDate = array();
            $i = 0;
            foreach ($dateArr as $keyDate => $valDate) {
                // $sqlSelect = "SELECT  AVG( " . $param . "  ) as avgd  FROM mbr_log where  MONTH(created_date) = '$valDate' and number = '$number'  and " . $param . "  <> 0    order by created_date asc";
                
                
                if ($cumulativeFlag == true) { 
                    
                    $sqlSelect = "Select (  MAX(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) - MIN(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) )  as avgd , CONCAT(HOUR(TIMES),':',MINUTE(TIMES)) as TIMES from embark_data where number IN ( $number ) and MONTH(datetime) = '$valDate'   order by id asc  ";
                        
                }else{
                    $sqlSelect = "Select AVG(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) as avgd , CONCAT(HOUR(TIMES),':',MINUTE(TIMES)) as TIMES   from embark_data where number IN ( $number ) and MONTH(datetime) = '$valDate'   order by id asc  ";
                }
                
                
                $resSql = find_by_sql($sqlSelect); 
                
                $kldStepVals[$i] = "0";
                foreach ($resSql as $keyKld => $valKld) {

                    if (isset($valKld['avgd']) && $valKld['avgd'] != "") {
                        $kldStepVals[$i] = number_format((($valKld['avgd'])), 2, '.', '');
                    } else {
                        $kldStepVals[$i] = "0";
                    }
                }


                $kldStepDate[$i] = date('F', mktime(0,0,0,$valDate, 1, date('Y')));// date("M", strtotime($valDate));
                $i++;
            }


            if (sizeof($kldStepVals) > 0) {
                echo json_encode(array("result" => "success", "msg" => "Record found!!", "values" => $kldStepVals, "period" => $kldStepDate));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "No record found!!"));
                exit;
            }
            
            
        }
        
         break;
         
    case "get-trend-zone":
        $id = ( true == isset($_REQUEST['id']) && "" != $_REQUEST['id'] ) ? $_REQUEST['id'] : "";
        $param = ( true == isset($_REQUEST['param']) && "" != $_REQUEST['param'] ) ? $_REQUEST['param'] : "";
        $duration = ( true == isset($_REQUEST['duration']) && "" != $_REQUEST['duration'] ) ? $_REQUEST['duration'] : "";
        $dateFrom = date("Y-m-d", strtotime(" -1 " . $duration));

        $sqlSelectSites = "Select GROUP_CONCAT(number) as assigned_numbers from zone as s where s.unique_id = '$id' group by s.unique_id  order by s.id desc Limit 1";
        $arrSites = find_by_sql($sqlSelectSites);
        $arrSites = $arrSites[0];
        $number = $arrSites['assigned_numbers'];

        if ($duration == "DAY") {

            $timeHr = array();

            for ($j = 0; $j <= date("H"); $j++) {
                $timeHr[] = $num = sprintf("%02d", $j);
            }
 

            $kldStepVals = array();
            $kldStepDate = array();
            $i = 0;
            foreach ($timeHr as $keyHr => $valHr) {

                // $sqlSelect = "SELECT  AVG( " . $param . "  ) as avgd ,  created_date  FROM mbr_log where  DATE(created_date)  = '".date("Y-m-d")."' and HOUR(created_date) = '$valHr'  and " . $param . "  <> 0   and number = '$number'    order by created_date asc";

                if ($cumulativeFlag == true) { 
                    
                    $sqlSelect = "Select (  MAX(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) - MIN(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) )  as avgd , CONCAT(HOUR(TIMES),':',MINUTE(TIMES)) as TIMES from embark_data where number IN ( $number ) and DATE(datetime) =  '".date("Y-m-d")."'   order by id asc  ";
                        
                }else{
                    $sqlSelect = "Select AVG(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) as avgd , CONCAT(HOUR(TIMES),':',MINUTE(TIMES)) as TIMES   from embark_data where number IN ( $number ) and DATE(datetime) =  '".date("Y-m-d")."'    order by id asc  ";
                }


                $resSql = find_by_sql($sqlSelect);


                if (sizeof($resSql) > 0) {
                    foreach ($resSql as $keyKld => $valKld) {

                        if (isset($valKld['avgd']) && $valKld['avgd'] != "") {
                            $kldStepVals[$i] = number_format((($valKld['avgd'])), 2, '.', '');
                        } else {
                            $kldStepVals[$i] = "0";
                        }


                        $kldStepDate[$i] = $valHr . ":00";
                    }
                } else {
                    $kldStepVals[$i] = "0";
                    $kldStepDate[$i] = $valHr . ":00";
                }
                $i++;
            }

            if (sizeof($kldStepVals) > 0) {
                echo json_encode(array("result" => "success", "msg" => "Record found!!", "values" => $kldStepVals, "period" => $kldStepDate));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "No record found!!"));
                exit;
            }
        } else if ($duration == "DAILY") { 
            
            
           $period = getDatesFromRange(date('Y-m-d',strtotime('last sunday')), date('Y-m-d') );
            
            $kldStepVals = array();
            $kldStepDate = array();
            $i = 0;
            foreach ($period as $keyHr => $valHr) {

                // $sqlSelect = "SELECT  AVG( " . $param . "  ) as avgd ,  created_date  FROM mbr_log where  DATE(created_date)  = '".$valHr."'   and number = '$number' and " . $param . "  <> 0    order by created_date asc";
                
                if ($cumulativeFlag == true) { 
                    
                    $sqlSelect = "Select (  MAX(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) - MIN(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) )  as avgd  , DATE(datetime) as rec_date from embark_data where number IN ( $number ) and DATE(datetime)  = '".$valHr."'      order by id asc  ";
                        
                }else{
                    $sqlSelect = "Select AVG(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) as avgd , DATE(datetime) as rec_date from embark_data where number IN ( $number ) and DATE(datetime)  = '".$valHr."'    order by id asc  ";
                }
                
                
                $resSql = find_by_sql($sqlSelect);


                if (sizeof($resSql) > 0) {
                    foreach ($resSql as $keyKld => $valKld) {

                        if (isset($valKld['avgd']) && $valKld['avgd'] != "") {
                            $kldStepVals[$i] = number_format((($valKld['avgd'])), 2, '.', '');
                        } else {
                            $kldStepVals[$i] = "0";
                        }


                        $kldStepDate[$i] =  date("D M j" , strtotime($valHr));
                    }
                } else {
                    $kldStepVals[$i] = "0";
                    $kldStepDate[$i] = date("D M j" , strtotime($valHr));
                }
                $i++;
            }

            if (sizeof($kldStepVals) > 0) {
                echo json_encode(array("result" => "success", "msg" => "Record found!!", "values" => $kldStepVals, "period" => $kldStepDate));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "No record found!!"));
                exit;
            } 
            
            
        }else if ($duration == "WEEK") { 
         
            $arrWeeks = getWeekSunSat();
         
            $kldStepVals = array();
            $kldStepDate = array();
            $i = 0;
            foreach ($arrWeeks as $keyHr => $valHr) {

                $start = $valHr['start'];
                $end = $valHr['end'];
                
                // $sqlSelect = "SELECT  AVG( " . $param . "  ) as avgd ,  created_date  FROM mbr_log where (  DATE(created_date)  >= '".$start."' and DATE(created_date)  <= '".$end."' )   and number = '$number' and " . $param . "  <> 0    order by created_date asc";

                if ($cumulativeFlag == true) { 
                    
                    $sqlSelect = "Select (  MAX(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) - MIN(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) )  as avgd , DATE(datetime) as rec_date from embark_data where number IN ( $number ) and (  DATE(datetime)  >= '".$start."' and DATE(datetime)  <= '".$end."' )       order by id asc  ";
                        
                }else{
                    $sqlSelect = "Select AVG(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) as avgd , DATE(datetime) as rec_date   from embark_data where number IN ( $number ) and (  DATE(datetime)  >= '".$start."' and DATE(datetime)  <= '".$end."' )  order by id asc  ";
                }

                $resSql = find_by_sql($sqlSelect);


                if (sizeof($resSql) > 0) {
                    foreach ($resSql as $keyKld => $valKld) {

                        if (isset($valKld['avgd']) && $valKld['avgd'] != "") {
                            $kldStepVals[$i] = number_format((($valKld['avgd'])), 2, '.', '');
                        } else {
                            $kldStepVals[$i] = "0";
                        }

                        $kldStepDate[$i] =  date("D M j" , strtotime($start))." - ".date("D M j" , strtotime($end)); 
                    }
                } else {
                    $kldStepVals[$i] = "0";
                    $kldStepDate[$i] = date("D M j" , strtotime($start))." - ".date("D M j" , strtotime($end));
                }
                $i++;
            }

            if (sizeof($kldStepVals) > 0) {
                echo json_encode(array("result" => "success", "msg" => "Record found!!", "values" => $kldStepVals, "period" => $kldStepDate));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "No record found!!"));
                exit;
            } 
                 
                 
        }else if ($duration == "MONTHLY"){
            
            $first_day_this_month = date('Y-m-01'); 
            $days = dateDiffInDays($first_day_this_month , date("Y-m-d"));
            $days = $days + 1;
            $dateArr = array(1, 2, 3,4 , 5 ,6, 7, 8,9 , 10 ,11 ,12 );
            $kldStepVals = array();
            $kldStepDate = array();
            $i = 0;
            foreach ($dateArr as $keyDate => $valDate) {
                // $sqlSelect = "SELECT  AVG( " . $param . "  ) as avgd  FROM mbr_log where  MONTH(created_date) = '$valDate' and number = '$number'  and " . $param . "  <> 0    order by created_date asc";
                
                
                if ($cumulativeFlag == true) { 
                    
                    $sqlSelect = "Select (  MAX(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) - MIN(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) )  as avgd , DATE(datetime) as rec_date from embark_data where number IN ( $number ) and MONTH(datetime) = '$valDate'  and YEAR(datetime) = YEAR(NOW())    order by id asc  ";
                        
                }else{
                    $sqlSelect = "Select AVG(SPLIT_STRING(COLVALUES, ',' , FIND_IN_SET('$param' , COLNAME))) as avgd , DATE(datetime) as rec_date   from embark_data where number IN ( $number ) and MONTH(datetime) = '$valDate' and YEAR(datetime) = YEAR(NOW())     order by id asc  ";
                }
                
                
                $resSql = find_by_sql($sqlSelect); 
                
                $kldStepVals[$i] = "0";
                foreach ($resSql as $keyKld => $valKld) {

                    if (isset($valKld['avgd']) && $valKld['avgd'] != "") {
                        $kldStepVals[$i] = number_format((($valKld['avgd'])), 2, '.', '');
                    } else {
                        $kldStepVals[$i] = "0";
                    }
                }


                $kldStepDate[$i] = date('F', mktime(0,0,0,$valDate, 1, date('Y')));// date("M", strtotime($valDate));
                $i++;
            }


            if (sizeof($kldStepVals) > 0) {
                echo json_encode(array("result" => "success", "msg" => "Record found!!", "values" => $kldStepVals, "period" => $kldStepDate));
                exit;
            } else {
                echo json_encode(array("result" => "error", "msg" => "No record found!!"));
                exit;
            }
            
            
        }
        
         break;
         
        case "last_login":
                $id = ( true == isset($_REQUEST['id']) ) ? $_REQUEST['id'] : "";
                $uid = ( true == isset($_REQUEST['uid']) ) ? $_REQUEST['uid'] : "";
                
                $select = "Select * from login_history where  ( username = '$id'  or username = '$uid' )  order by id desc Limit 5";
                $res = find_by_sql($select);
                
                $table = "<table style='width:100%' border='1' cellpadding='5' ><thead><tr><th>Sr. No.</th><th>IP</th><th>Datetime</th></tr></thead><tbody>";
                
                foreach($res as $keyTable => $valTable ){
                    $table .= "<tr>";
                    $table .= "<td>".($keyTable + 1)."</td>";
                    $table .= "<td>".($valTable['ip'])."</td>";
                    $table .= "<td>".($valTable['login_date'])."</td>";
                }
                
                $table .= "</tbody></table>";
                
                echo $table;
                
                break;
    
        case "site-collection":
			$id = ( true == isset($_REQUEST['id']) ) ? $_REQUEST['id'] : "";
			
			if( $id != "all" ){
    		
        		$selectNumber = find_by_sql("Select number from sites where id = '$id'");
        		$number = "";
        
        		if( sizeof($selectNumber) > 0 ){
                	$number = $selectNumber[0]['number'];
                }
        		
        		$selectUsage = "Select * from CARD_USAGE_VIEW where number = '$number' and DATE(created_date) = '".date("Y-m-d")."'";
        		$resUsage = find_by_sql($selectUsage);
        		
        		$arrRes = array();
        		if( sizeof($resUsage) > 0 ){
        		foreach( $resUsage as $keyUsage => $valUsage ){
        		    $arrRes['twenty'] = floatval($arrRes['twenty']) + floatval($valUsage['twenty']);
        		    $arrRes['ones'] = floatval($arrRes['ones']) + floatval($valUsage['ones']);
        		    $arrRes['fives'] =  floatval($arrRes['fives']) + floatval($valUsage['fives']);
        		}
        		}else{
        		    $arrRes['twenty'] = 0;
        		    $arrRes['ones'] = 0;
        		    $arrRes['fives'] = 0;
        		}
        		
        		$totalToday = floatval($arrRes['twenty'] ) + floatval($arrRes['ones'] ) + floatval($arrRes['fives'] );
                $arrRes['total_today'] = $totalToday;
        
        
        
                //$selectAll = "Select SUM(card_usage) as  todate from card_usage where SUBSTR(number,-10) = '$number'  ";
                $selectAll  = "Select  ( SUM(twenty) + SUM(fives) + SUM(ones) ) as todate from CARD_USAGE_VIEW where number IN ( ".$number.")  ";

        		
        		$resAll = find_by_sql($selectAll);
                
                $tillDate = 0;
                
                if( sizeof($resAll) > 0 ){
                    $arrRes['todate'] = $resAll[0]['todate'];
                }else{
                    $arrRes['todate'] = 0;
                }
                
              //  echo " Select recharge_numbers from login  where id = '" . $_SESSION['user_id'] . "' order by id desc LIMIT 1";
                $resLogin = find_by_sql(" Select recharge_numbers from login  where id = '" . $_SESSION['user_id'] . "' order by id desc LIMIT 1");
                
                if( sizeof($resLogin) > 0 ){
                    $resLogin = $resLogin[0];
                }
                
                $allNumbers = trim($resLogin['recharge_numbers']);
                
                if( $allNumbers != "" ){
                    $rech_numbers = explode(",",$resLogin['recharge_numbers']);
                } else {
                    $rech_numbers = array("0");
                }
                
                
                
                $arrRes['recharge_todate'] = 0;  
        //         $selectRech = "Select IF(recharge !='', SUM(recharge) ,0) as recharge from card_recharge where number = '$number'  ";
        // 		$resRech = find_by_sql($selectRech);
            
        //         if( sizeof($resRech) > 0 ){
        //             $arrRes['recharge_todate'] = $resRech[0]['recharge'];
        //         }else{
        //             $arrRes['recharge_todate'] = 0;
        //         }
                
                $table = "";
                
        		$resToday = find_by_sql("Select * from card_usage where number = '$number' and DATE(created_date) = '".date("Y-m-d")."' ");
                 
                
                $arrRes['result'] = "success";
        		$arrRes['rec_date'] = date("Y-m-d");
        
        
                $db->selectDB("dbembark");
                       
                $select  = "Select SUM(rechargeval) as recharge, number from wateratmdata where number IN (  ".implode(",", array_values($rech_numbers))." ) and DATE(rec_date_time) = DATE(NOW()) ";
                $resRecharge = find_by_sql($select);
                $arrRecharge = array();
                foreach( $resRecharge as $keyRech => $valRech  ){
                    $arrRecharge[] = $valRech['recharge'];
                }
        
                $arrRes['recharge_todate'] = array_sum(array_values($arrRecharge));  
                
                
                foreach( $resUsage as $keyUsage => $valUsage ){
                    
                    $table .= "<tr>";
                    $table .= "<td>".($valUsage['created_date'])."</td>";
                    $table .= "<td>".($arrRes['todate'])."</td>";
                    $table .= "<td>".($arrRes['recharge_todate'])."</td>";
                  	$table .= "<td>".($totalToday)."</td>";
                    $table .= "<td>".($valUsage['ones'])."</td>";
                    $table .= "<td>".($valUsage['fives'])."</td>";
                    $table .= "<td>".($valUsage['twenty'])."</td>";
                    
                    $table .= "</tr>";
                }
                $arrRes['table'] = $table;
        
                echo json_encode($arrRes);
			}else{
			    
			    
			    if( $_SESSION['role'] != "admin" ){
			        
    			     $user_id = $_SESSION['user_id'];
    			     $selectNumber = find_by_sql("Select number as num from sites where created_by = '$user_id'");
        		
			    }else{
			        
			        $selectNumber = find_by_sql("Select GROUP_CONCAT(number) as num from sites ");
        		    
			    }
			     
			   
			     
			   $number = "";
        
        		if( sizeof($selectNumber) > 0 ){
                	$number =  $selectNumber[0]['num'];
                }
        		
        		$selectUsage = "Select SUM(twenty) as twenty, SUM(fives) as fives, SUM(ones) as ones, number , 	created_date , uid  from CARD_USAGE_VIEW where number IN ($number) and DATE(created_date) = '".date("Y-m-d")."'";
        		$resUsage = find_by_sql($selectUsage);
        		
        		$arrRes = array();
        		if( sizeof($resUsage) > 0 ){
        		    
        		    $arrRes['twenty'] = $resUsage[0]['twenty'];
        		    $arrRes['ones'] = $resUsage[0]['ones'];
        		    $arrRes['fives'] =  $resUsage[0]['fives'];
        		    
        // 		foreach( $resUsage as $keyUsage => $valUsage ){
        // 		    $arrRes['twenty'] = floatval($arrRes['twenty']) + floatval($valUsage['twenty']);
        // 		    $arrRes['ones'] = floatval($arrRes['ones']) + floatval($valUsage['ones']);
        // 		    $arrRes['fives'] =  floatval($arrRes['fives']) + floatval($valUsage['fives']);
        // 		}
        		}else{
        		    $arrRes['twenty'] = 0;
        		    $arrRes['ones'] = 0;
        		    $arrRes['fives'] = 0;
        		}
        		
        		$totalToday = floatval($arrRes['twenty'] ) + floatval($arrRes['ones'] ) + floatval($arrRes['fives'] );
                $arrRes['total_today'] = $totalToday;
        
              //  $recharge =  file_get_contents("https://atm.embarkrms.com/get_recharge.php?id=$number&from=2022-10-06&to=2022-10-06");
        
                // $selectAll = "Select SUM(card_usage) as  todate from card_usage where SUBSTR(number, -10) IN ( $number )  ";
        	    
        	    $selectAll  = "Select  ( SUM(twenty) + SUM(fives) + SUM(ones) ) as todate from CARD_USAGE_VIEW where number IN ( ".$number.")  ";
 
        		$resAll = find_by_sql($selectAll);
                
                $tillDate = 0;
                
                if( sizeof($resAll) > 0 ){
                    $arrRes['todate'] = $resAll[0]['todate'];
                }else{
                    $arrRes['todate'] = 0;
                }
                
        //         $selectRech = "Select IF(recharge !='', SUM(recharge) ,0) as recharge from card_recharge where number = '$number'  ";
        // 		$resRech = find_by_sql($selectRech);
            
        //         if( sizeof($resRech) > 0 ){
        //             $arrRes['recharge_todate'] = $resRech[0]['recharge'];
        //         }else{
        //             $arrRes['recharge_todate'] = 0;
        //         }
                
            //    $arrRes['recharge_todate'] = $recharge;
            if( $_SESSION['role'] != "admin" ){
                 $resLogin = find_by_sql(" Select recharge_numbers from login  where  id = '" . $_SESSION['user_id'] . "' order by id desc LIMIT 1");
            }else{
                 $resLogin = find_by_sql(" Select recharge_numbers from login  ");
            
            }
                if( sizeof($resLogin) > 0 ){
                    $resLogin = $resLogin[0];
                }
                
                $allNumbers = trim($resLogin['recharge_numbers']);
                
                if( $allNumbers != "" ){
                    $rech_numbers = explode(",",$resLogin['recharge_numbers']);
                } else {
                    $rech_numbers = array("0");
                }
                
                
                
                $table = "";
                
        		$resToday = find_by_sql("Select * from CARD_USAGE_VIEW where number IN ($number) and DATE(created_date) = '".date("Y-m-d")."' ");
                 
                
                $arrRes['result'] = "success";
        		$arrRes['rec_date'] = date("Y-m-d");
        		
        		$db->selectDB("dbembark");
                
                if( $_SESSION['role'] != "admin" ){
                                        
                    $select  = "Select SUM(rechargeval) as recharge, number from wateratmdata where number IN (  ".implode(",", array_values($rech_numbers))." ) and DATE(rec_date_time) = DATE(NOW()) ";
                
                }else{
                  $select  = "Select SUM(rechargeval) as recharge, number from wateratmdata where DATE(rec_date_time) = DATE(NOW()) ";
                  
                }
                
                $resRecharge = find_by_sql($select);
                $arrRecharge = array();
                foreach( $resRecharge as $keyRech => $valRech  ){
                    $arrRecharge[] = $valRech['recharge'];
                }
        
                $arrRes['recharge_todate'] = array_sum(array_values($arrRecharge)); 
                
                
                foreach( $resToday as $keyUsage => $valUsage ){
                    
                    $table .= "<tr>";
                    $table .= "<td>".($valUsage['created_date'])."</td>";
                    $table .= "<td>".($arrRes['todate'])."</td>";
                    $table .= "<td>".($arrRes['recharge_todate'])."</td>";
                  	$table .= "<td>".($totalToday)."</td>";
                    $table .= "<td>".($valUsage['ones'])."</td>";
                    $table .= "<td>".($valUsage['fives'])."</td>";
                    $table .= "<td>".($valUsage['twenty'])."</td>";
                    
                    $table .= "</tr>";
                }
        
                $arrRes['table'] = $table; 
                echo json_encode($arrRes);
			    
			    
			}
    
          break;
         
        
        
}

function dateDiffInDays($date1, $date2)  
{ 
    // Calculating the difference in timestamps 
    $diff = strtotime($date2) - strtotime($date1); 
      
    // 1 day = 24 hours 
    // 24 * 60 * 60 = 86400 seconds 
    return abs(round($diff / 86400)); 
} 


function getMin( $a ) {
   $arr_filtered = array_values(array_filter( $a ));
   return min($arr_filtered);

}

function getWeekSunSat() {
    $arr = array();
    for( $i = 4 ; $i >= 0 ; $i-- ){
        $previous_week = strtotime("-$i week +1 day");
    
        $start_week = strtotime("last sunday midnight",$previous_week);
        $end_week = strtotime("next saturday",$start_week);
        
        $start_week = date("Y-m-d",$start_week);
        $end_week = date("Y-m-d",$end_week);
        $arr[$i]['start'] = $start_week;
        $arr[$i]['end'] = $end_week;
    }
    
    return $arr ;
}

 

function getDatesFromRange($start, $end, $format='Y-m-d') {
    return array_map(function($timestamp) use($format) {
        return date($format, $timestamp);
    },
    range(strtotime($start) + ($start < $end ? 4000 : 8000), strtotime($end) + ($start < $end ? 8000 : 4000), 86400));
}


function geolocationaddress($lat, $long) {
    $geocode = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$long&sensor=false&key=AIzaSyBobuM3r1H0ypFdb4vlj3Ix_l9WvyMLEaU";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $geocode);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    curl_close($ch);

    $output = json_decode($response);
    $dataarray = get_object_vars($output);
    if ($dataarray['status'] != 'ZERO_RESULTS' && $dataarray['status'] != 'INVALID_REQUEST') {
        if (isset($dataarray['results'][0]->formatted_address)) {

            $address = $dataarray['results'][0]->formatted_address;
        } else {

            $address = 'Not Found';
        }
    } else {
        $address = 'Not Found';
    }

    return $address;
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

function execute_url($url, $httppost) {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $httppost);

// In real life you should use something like:
// curl_setopt($ch, CURLOPT_POSTFIELDS, 
//          http_build_query(array('postvar1' => 'value1')));
// Receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);

    curl_close($ch);
    return $server_output;
}

?>
