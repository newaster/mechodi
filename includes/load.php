<?php
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
    $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $location);
    exit;
}

$timezone_identifier = "Asia/Kolkata";
date_default_timezone_set($timezone_identifier);
// -----------------------------------------------------------------------
// DEFINE SEPERATOR ALIASES
// -----------------------------------------------------------------------
define("URL_SEPARATOR", '/');

define("DS", DIRECTORY_SEPARATOR);

// -----------------------------------------------------------------------
// DEFINE ROOT PATHS
// -----------------------------------------------------------------------
defined('SITE_ROOT') ? null : define('SITE_ROOT', realpath(dirname(__FILE__)));
define("LIB_PATH_INC", SITE_ROOT . DS);


require_once(LIB_PATH_INC . 'config.php');
require_once(LIB_PATH_INC . 'functions.php');
require_once(LIB_PATH_INC . 'session.php');
require_once(LIB_PATH_INC . 'database.php');
require_once(LIB_PATH_INC . 'sql.php');

require_once(LIB_PATH_INC . 'upload.php');

$session = new Session();
$msg = $session->msg();
 
error_reporting(E_ERROR);

$arr_pages = array(1,2);

 

if( $pageval != "index" && ( isset($_SESSION['user_id']) === false || $_SESSION['user_id'] == "" ) ){
  redirect("index.php");
}

?>
