<?php
/**
 * User: nikshev
 * Date: 6/18/14
 * Time: 3:44 PM
 */
 namespace english\english;
 require_once("dbconnect.php");
 ini_set('error_reporting', E_ALL);
 ini_set('display_errors', true);

 $dbconnect = new DBconnect();
 $settings=$dbconnect->getSettings();

 if(!isset($settings["sent_limit"]))
     $settings["sent_limit"]=15;

 if(!isset($settings["interval"]))
     $settings["interval"]=20;

 if(!isset($settings["dictionary"]))
     $settings["dictionary"]=8;

 $id=$settings["dictionary"];
 $sl=$settings["sent_limit"];
 $in=$settings["interval"];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>English dictionary</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="English lesson" content="English lesson">
    <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" media="screen" />
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.1.min.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
</head>
<body>

<?php
if (isset($id)){
    if (!isset($sl))
        $sl=20;
    $str=" <input type=\"hidden\" id=\"interval\" name=\"interval\" value=\"".$in."\" />
           <table id=\"myTable\" class=\"table table-bordered\" width=\"10%\">".
           "<thead>".
            "<tr>".
              "<th>English sentence</th><th>Other sentence</th>".
            "</tr>".
           "</thead>".
           "<tbody>";

    $dbconnect->newExercise();
    $result=$dbconnect->getSentence($id,$sl);
    $dir="../audio/";
    foreach ($result as $row){
        $filename_en=$dir.$row["id"].".mp3";
        $filename_ot=$dir.$row["id"]."ot.mp3";
        $dbconnect->setText(trim($row["english"]),$lng="en");
        $dbconnect->saveToFile($filename_en);
        $dbconnect->setText(trim($row["other"]),$lng="ru");
        $dbconnect->saveToFile($filename_ot);
        $dbconnect->addSentenceToExercise($row["id"]);
        $str.="<tr>";
        $str.='<td class="first"><input type="hidden" id="sid[]" name="sid[]" value="'.$row["id"].'">'.trim($row["english"]);
        $str.='<audio id="'.$row["id"].'" controls="controls">';
        $str.='<source src="'.$filename_en.'" type="audio/mpeg" />';
        $str.='Your browser does not support the audio element.';
        $str.='</audio>';
        $str.="</td>";
        $str.='<td class="second">'.trim($row["other"]);
        $str.='<audio id="'.$row["id"].'ot" controls="controls">';
        $str.='<source src="'.$filename_ot.'" type="audio/mpeg" />';
        $str.='Your browser does not support the audio element.';
        $str.='</audio>';
        $str.="</td>";
        $str.="</tr>";
   }
    $str.="</tbody></table>";

}
?>
<div style="text-align:center;">
 <div style="margin: 0 auto; width:60%">
    <h1>Lesson</h1>
     <a id="skip" href="../english/exercise.php">Skip lesson and start exercise</a>
    <?php  echo $str; ?>
 </div>
</div>
</body>