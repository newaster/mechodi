<?php

require_once('./includes/load.php');

require_once './includes/upload.php';


$userid = (true == isset($_SESSION['user_id']) && "" != $_SESSION['user_id'] ) ? $_SESSION['user_id'] : 0;
$selectQuota = "Select quota from login where id = '$userid'";
$resQuota = find_by_sql($selectQuota);
$quota = $resQuota[0]['quota'];

if( $quota < 1 ){
    $message = "Subscription points unavailable. Please Contact Support!!!";
    echo json_encode(array("result" => "error", "msg" => $message , "isupdate" => false));
    exit;
} 


if (isset($_POST['submit'])) {

    $site_name = ( isset($_POST['site_name']) && "" != $_POST['site_name'] ) ? $_POST['site_name'] : "";
    $number = ( isset($_POST['number']) && "" != $_POST['number'] ) ? $_POST['number'] : "";
    $latitude = ( isset($_POST['latitude']) && "" != $_POST['latitude'] ) ? $_POST['latitude'] : "";
    $longitude = ( isset($_POST['longitude']) && "" != $_POST['longitude'] ) ? $_POST['longitude'] : "";
    $contact = ( isset($_POST['contact']) && "" != $_POST['contact'] ) ? $_POST['contact'] : "";
    $status = ( isset($_POST['status']) && "" != $_POST['status'] ) ? $_POST['status'] : "";
    $city = ( isset($_POST['city']) && "" != $_POST['city'] ) ? $_POST['city'] : "";
    $siteid = ( isset($_POST['siteid']) && "" != $_POST['siteid'] ) ? $_POST['siteid'] : "";
    

    $selectRec = "Select * from sites where number = '" . $number . "' ";
    $arrUser = find_by_sql($selectRec);
    if (sizeof($arrUser) > 0) {

        if ($siteid == "") {
            $message = "Site already exists!";
            echo json_encode(array("result" => "error", "msg" => $message , "isupdate" => true));
            exit;
        } else {

            $sliderinsertid = "";

            if (isset($_FILES['siteimages'])) {

                $arrfiles = reArrayFiles($_FILES['siteimages']);

                foreach ($arrfiles as $key => $slider) {
                    $photo = new Media();
                    $photo->upload($slider);
                    $insertid = $photo->process_media();
                    if (sizeof((array) $photo->getErrors()) > 0) {
                        $message = "Unable to upload Image " . $slider['name']." - ".json_encode((array)$photo->getErrors());
                        echo json_encode(array("result" => "error", "msg" => $message , "isupdate" => true));
                        exit;
                    }
                    $sliderinsertid .= $insertid . ",";

                    $imagesupdated = " , images = '{$sliderinsertid}'";
                }
            }

            $updateRec = "Update sites set  name = '{$site_name}' ,  latitude = '{$latitude}', longitude = '{$longitude}', contact = '{$contact}', status = '{$status}', city = '{$city}'   $imagesupdated   ";
            $updateRec .= " where  ";
            $updateRec .= "  id='" . $siteid . "' ";
            

            if ($db->query($updateRec)) {
                $message = "Record Updated Successfully!";
                echo json_encode(array("result" => "success", "msg" => $message , "isupdate" => true) );
                exit;
            } else {
                $message = "Unable to Update Record!";
                echo json_encode(array("result" => "error", "msg" => $message , "isupdate" => true));
                exit;
            }
        }
    } else {

        $sliderinsertid = "";

        if (isset($_FILES['siteimages'])) {

            $arrfiles = reArrayFiles($_FILES['siteimages']);

            foreach ($arrfiles as $key => $slider) {
                $photo = new Media();
                $photo->upload($slider);
                $insertid = $photo->process_media();
                if (sizeof((array) $photo->getErrors()) > 0) {
                    $message = "Unable to upload Image " . $slider['name']." - ".json_encode((array)$photo->getErrors());
                    echo json_encode(array("result" => "error", "msg" => $message , "isupdate" => false));
                    exit;
                }
                $sliderinsertid .= $insertid . ",";
            }
        } else {
            $message = "Please select site images.";
           // echo json_encode(array("result" => "error", "msg" => $message , "isupdate" => false));
            //exit;
        }

        

        $insertRec = "Insert into sites ( name, number, latitude, longitude, contact, status,city, images, created_by , created_date , renewal_date  ) values  ";
        $insertRec .= " ( '{$site_name}','{$number}' , '{$latitude}' ,"
                . " '{$longitude}' , '{$contact}', '{$status}', '{$city}' , '{$sliderinsertid}' , '{$_SESSION['user_id']}' , NOW()  ,'".date("Y-m-d H:i:s", strtotime('+1 year'))."' ) ";

        $db->query($insertRec);
        $last_insert_id = $db->insert_id() ;
        if ( $last_insert_id > 0) {
            
            $updateQuota = "Update login set quota = quota - 1   where id = '$userid' order by id desc ";
            $db->query($updateQuota);
            
            $insertDebit = "INSERT INTO `debit` ( `point`, `rec_id`, `purpose`, `user_id` ) VALUES (1, '$last_insert_id' , 'ADDED SITE' , '{$_SESSION['user_id']}')";
            $db->query($insertDebit);
            
            $message = "Record Inserted successfully!";
            echo json_encode(array("result" => "success", "msg" => $message , "isupdate" => false));
            exit;
        } else {
            $message = "Unable to create site!";
            echo json_encode(array("result" => "error", "msg" => $message , "isupdate" => false));
            exit;
        }
    }
}

function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}

?>