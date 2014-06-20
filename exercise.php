<?php
/**
 * User: nikshev
 * Date: 6/20/14
 * Time: 12:29 PM
 */
namespace english\english;
require_once("dbconnect.php");
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
$dbconnect = new DBconnect();

if (isset($_GET["id"]))
 $ex=intval($_GET["id"]);
else
 $ex=0;

if ($ex===1)
  if (isset($_POST["sid"])&&isset($_POST["values"]))
   $dbconnect->compareSentences($_POST["sid"],$_POST["values"],true);

if ($ex===2){
 if (isset($_POST["sid"])&&isset($_POST["values"]))
  $dbconnect->compareSentences($_POST["sid"],$_POST["values"],false);
 header("Location:http://".$_SERVER['HTTP_HOST']."/english/lesson.php");
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>English dictionary</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="English lesson" content="English lesson">
    <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" media="screen" />
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.1.min.js"></script>
</head>
<body>

<?php

 $url="exercise.php?id=2";
 if ($ex===0)
  $url="exercise.php?id=1";
 $nurl="../english/".$url;

 $str='<form class="form-horizontal" enctype="multipart/form-data" action="'.$url.'" method="POST">';
 $str.="<table id=\"myTable\" class=\"table table-bordered\" width=\"10%\">".
      "<thead>".
      "<tr>".
      "<th>English sentence</th><th>Other sentence</th>";
      "</tr>".
      "</thead>".
      "<tbody>";
 $result=$dbconnect->getSentencesFromExercise();
 $hresult=$dbconnect->getSentencesFromExercise();

 //Create hint
 $hint="";
 foreach ($hresult as $row){
    if ($ex===1){
        $hint.=$row["english"]." ";
    } else {
        $hint.=$row["other"]." ";
    }
 }

 //Create other
 $dir="../audio/";
 foreach ($result as $row){
    $str.="<tr>";
    if ($ex===1){
        $str.='<td class="first"><input type="hidden" id="sid[]" name="sid[]" value="'.$row["id"].'">'.trim($row["other"]);
        $str.='<td class="second"><input type="text" id="values[]" name="values[]"/>';
        $str.="</td>";
        $str.="</tr>";
    } else {
      $filename_en=$dir.$row["id"].".mp3";
      $dbconnect->setText(trim($row["english"]),$lng="en");
      $dbconnect->saveToFile($filename_en);
      $str.="<tr>";
      $str.='<td class="first"><input type="hidden" id="sid[]" name="sid[]" value="'.$row["id"].'">'.trim($row["english"]);
      $str.='<audio id="'.$row["id"].'" controls="controls">';
      $str.='<source src="'.$filename_en.'" type="audio/mpeg" />';
      $str.='Your browser does not support the audio element.';
      $str.='</audio>';
      $str.='<td class="second"><input type="text" id="values[]" name="values[]""/>';
     }
     $str.="</td>";
     $str.="</tr>";
 }

 $str.="</tbody></table>";
 if ($ex===0)
   $str.='<button type="submit" class="btn">Save and start next exercise</button></form>';
 else
     $str.='<button type="submit" class="btn">Save and start next lesson</button></form>';

?>

<div style="text-align:center;">
    <div style="margin:0 auto; width:60%">
        <h1>Exercise</h1>
        <a href="<?php  echo $nurl; ?>">Skip this exercise</a>
        <div style="float:left; width:100%; border: 1px solid #ddd; border-collapse: separate; border-bottom: 0;
         -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px;">
            <?php  echo $hint; ?>
        </div>

        <?php  echo $str; ?>

    </div>

</div>
</body>