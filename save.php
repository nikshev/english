<?php
/**
 * User: nikshev
 * Date: 6/20/14
 * Time: 10:51 AM
 */

namespace english\english;
require_once("dbconnect.php");
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

$dbconnect = new DBconnect();

$id=intval($_POST["dic_id"]);
$sl=intval($_POST["sl"]);
$in=intval($_POST["ibe"]);
if (isset($_POST["csc"]))
    $dbconnect->cleanSuccessCount();

$settings=array("sent_limit"=>$sl,"interval"=>$in, "dictionary"=>$id);

$dbconnect->setSettings($settings);
header("Location:http://".$_SERVER['HTTP_HOST']."/english/lesson.php");
?>