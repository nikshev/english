<?php
/**
 *
 * User: nikshev
 * Date: 6/18/14
 * Time: 10:49 AM
 */

namespace english\english;
require_once("dbconnect.php");
/**
 *
 * Class Import
 * @package english\english
 * Import dictionary from txt file with format "English sentence" - "Russian sentence"
 *
 */

class Import
{

    var $filename;
    var $dicname;

    /**
     * Constructor for class Import
     * Init variables
     */
    public function __construct($filename, $dicname)
    {
        $this->dicname = $dicname;
        $this->filename = $filename;
    }

    /**
     * Function for import
     * Function read data from uploaded file and store it to database
     */
    public function import()
    {
        $file = fopen($this->filename, 'r');
        $dbconnect = new DBconnect();
        $dbconnect->cleanDB(); //This line you must delete it is only for debug
        $id = $dbconnect->addDictionary($this->dicname);
        if ($id > 0) {
            while (!feof($file)) {
                $tmp_str = fgets($file);
                $tmparray = explode('-', $tmp_str);
                if (isset($tmparray[0]) && isset($tmparray[1])) {
                    //var_dump($tmparray);
                    //echo "<br/>";
                    $dbconnect->addLine($id,trim($tmparray[1]),trim($tmparray[0]));
                }
            }
        }
    }

}

//If we have post values than we do import, else show import form
if (isset($_POST["dic_name"])) {
    $uploaddir = getcwd()."/";
    $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
        $import = new Import($uploadfile, addslashes($_POST["dic_name"]));
        $import->import();
        unset($_POST["dic_name"]);
        unset($_FILES);
        echo 'Upload succesfull! <a href="javascript:history.back()">You can download another dictionary!</a>';
    } else {
        unset($_POST);
        unset($_FILES);
        echo 'Error upload file! <a href="javascript:history.back()">Please try again!</a>';
    }
} else {
    echo ' <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
              <head>
                <title>Import english dictionary</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <meta name="Import english dictionary" content="Import english dictionary">
                <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" media="screen" />
              </head>
            <body>
            <form class="form-horizontal" enctype="multipart/form-data" action="import.php" method="POST">
              <h1 style="margin-left:55px;">Import english dictionary</h1>
                <div class="control-group">
                 <label class="control-label" for="dic_name">Dictionary name</label>
                 <div class="controls">
                 <input type="text" id="dic_name" name="dic_name" placeholder="Dictionary name" value=""/><br/>
                 </div>
                </div>
               <div class="control-group">
                <label class="control-label" for="userfile">File name</label>
                 <div class="controls">
                  <input id="userfile" name="userfile" type="file" placeholder="File name" value=""/><br/>
                 </div>
               </div>
               <div class="control-group">
                <div class="controls">
                 <button type="submit" class="btn">Import</button>
                </div>
               </div>
             </form>';

}