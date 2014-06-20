<?php
/**
 * User: nikshev
 * Date: 6/18/14
 * Time: 3:22 PM
 */
 namespace english\english;
 require_once("dbconnect.php");

 $dbconnect = new DBconnect();
 $settings=$dbconnect->getSettings();

 if(!isset($settings["sent_limit"]))
    $settings["sent_limit"]=15;

 if(!isset($settings["interval"]))
    $settings["interval"]=20;

 if(!isset($settings["dictionary"]))
    $settings["dictionary"]=8;

 $result=$dbconnect->getDictionaries();
 $dictionary="<option default>Choose dictionary name</option>";
 foreach ($result as $row){
     if ($row["id"]===$settings["dictionary"])
      $dictionary.='<option selected value="'.$row["id"].'">'.$row["name"].'</option>';
     else
      $dictionary.='<option value="'.$row["id"].'">'.$row["name"].'</option>';

 }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>English dictionary</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="Import english dictionary" content="Import english dictionary">
    <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" media="screen" />
</head>
<body>
<form class="form-horizontal" enctype="multipart/form-data" action="save.php" method="POST">
    <h1 style="margin-left:55px;">Settings</h1>
    <div class="control-group">
        <div class="controls">
         <a href="../english/lesson.php">Skip settings and start learning</a>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="dic_id">Dictionary name</label>
        <div class="controls">
            <select name="dic_id" id="dic_id">
              <?php echo  $dictionary;?>
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="sl">Sentences limit</label>
        <div class="controls">
            <input type="text" id="sl" name="sl" placeholder="Sentences limit" value="<?php echo  $settings["sent_limit"]?>"/><br/>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="sl">Interval between exercise</label>
        <div class="controls">
            <input type="text" id="ibe" name="ibe" placeholder="Interval between exercise" value="<?php echo $settings["interval"]?>"/><br/>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="csc">Clean success count (start learning from begin) </label>
        <div class="controls">
            <input type="checkbox" id="csc" name="csc"/><br/>
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <button type="submit" class="btn">Save and start learning</button>
        </div>
    </div>
</form>
