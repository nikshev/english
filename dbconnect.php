<?php
/**
 * User: nikshev
 * Date: 6/18/14
 * Time: 10:43 AM
 */

namespace english\english;

/**
 * Class DBconnect
 * @package english\english
 */

class DBconnect {
    var $host;
    var $dbname;
    var $dbuser;
    var $password;
    var $link;
    var $audio;

    /**
     * Constructor for DBconnect
     * @package english\english
     */
    public function __construct($params=array("host"=>"localhost","dbname"=>"english","dbuser"=>"english","password"=>"english")){
      $this->host=$params["host"];
      $this->dbname=$params["dbname"];
      $this->dbuser=$params["dbuser"];
      $this->password=$params["password"];
    }

    /**
     * Function addDictionary
     * Function insert dictionary name into a table "dictionaries"
     * input $dictionary - dictionary name
     * output id of dictionary
     */
    public function addDictionary($dictionary){
        $id=-1;
        $this->connect();
        $query="INSERT INTO dictionaries (name)
                VALUES ('".$dictionary."');";
        mysqli_query($this->link,$query) or die('Query fault: ' . mysqli_error($this->link));
        $id=mysqli_insert_id($this->link);
        $this->disconnect();
        return $id;
    }

    /**
     * Function addLine
     * Function insert line name into a table "dictionary"
     * input $id      - dictionary id
     *       $english - english sentence
     *       $other   - other sentence
     */
    public function addLine($id,$english,$other){
        $this->connect();
        $query="INSERT INTO `dictionary`( `id_dic`, `english`, `other`)
                VALUES (".$id.",'".addslashes($english)."','".addslashes($other)."')";
        mysqli_query($this->link,$query) or die('Query fault: ' . mysqli_error($this->link)."<br/>Query:".$query);
        $this->disconnect();
    }

    /**
     * Function getDictionaries()
     * Return array of dictionaries stored in database
     */
    public function getDictionaries(){
        $this->connect();
        $query="SELECT * FROM dictionaries";
        $result=mysqli_query($this->link,$query) or die('Query fault: ' . mysqli_error($this->link)."<br/>Query:".$query);
        $return_arr=array();
        while($row=mysqli_fetch_assoc($result)){
            $return_arr[]=array('id'=>$row["id"], "name"=>$row["name"]);
        }
        $this->disconnect();
        return $return_arr;
    }

    /**
     * Function cleanDB()
     * Function for cleaning db
     */
    public function cleanDB(){
        $this->connect();
        $query="DELETE FROM dictionary";
        mysqli_query($this->link,$query) or die('Query to dictionary fault: ' . mysqli_error($this->link));
        $query="DELETE FROM dictionaries";
        mysql_query($this->link,$query) or die('Query to dictionaries fault: ' . mysql_error($this->link));
        $this->disconnect();
    }

    /**
     * Function getSentence
     * Function get sentence and return array
     * output $return_arr - sentences from exercise
     */
    public function getSentence($id,$limit){
        $return_arr=array();
        $this->connect();
        $query="SELECT id,english,other FROM `dictionary` WHERE lcount<20 and id_dic=".$id." ORDER BY RAND() LIMIT ".$limit;
        $result=mysqli_query($this->link,$query) or die('Query fault: ' . mysqli_error($this->link)."<br/>Query:".$query);
        while($row=mysqli_fetch_assoc($result)){
         $return_arr[]=array('id'=>$row["id"], "english"=>$row["english"],"other"=>$row["other"]);
        }
        $this->disconnect();
        return $return_arr;
    }

    /**
     * Function connect()
     * Function for connect to db
     */
    private function connect(){
        $this->link = mysqli_connect($this->host, $this->dbuser,$this->password,$this->dbname);
        if (!$this->link) {
            die('Connection error: ' . mysqli_qerror());
        }
        //mysql_select_db($this->dbname) or die('Database error: ' . mysql_error());
    }

    /**
     * Function diconnect()
     * Function for diconnect to db
     */
    private function disconnect(){
        if (isset($this->link))
            mysqli_close($this->link);
    }

    /**
     * Function setText()
     * Function set text and get it mp3 representation from Google
     * input $text - text for audio convert
     */
    function setText($text,$lng="en")
    {
        $text = trim($text);
        if (!empty($text)) {
            $uagent = "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.872.0 Safari/535.2";
            $text =  urlencode(iconv("UTF-8", "UTF-8", $text));
            $url = "http://translate.google.com/translate_tts?tl=".$lng."&q={$text}";
            $ch = curl_init( $url );
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_ENCODING, "");
            curl_setopt($ch, CURLOPT_USERAGENT, $uagent);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            $content = curl_exec( $ch );
            $this->audio = $content;
            return $this->audio;
        } else {
            return false;
        }
    }


    /**
     * Function saveToFile()
     * Function save to file audio content which set up function setText
     * input $filename - file name for save audio content
     */
    function saveToFile($filename)
    {
        $filename = trim($filename);
        if (!file_exists($filename)) {
            if (!empty($filename)) {
                return file_put_contents($filename, $this->audio);
            } else {
                return false;
            }
        }
    }

    /**
     * Function getSettings
     * Function get settings from database and return settings array
     * outpu $retarr - settings array
     */
    function getSettings(){
      $retarr=array();
      $this->connect();
      $query="SELECT * FROM settings";
      $result=mysqli_query($this->link,$query) or die('Query fault: ' . mysqli_error($this->link)."<br/>Query:".$query);
      while($row=mysqli_fetch_assoc($result)){
          $retarr[$row["name"]]=$row["parameters"];
      }
      $this->disconnect();
      return $retarr;
    }

    /**
     * Function setSettings
     * Function set up settings from database and return settings array
     * input $settings - settings array
     */
    function setSettings($settings){
      if(!isset($settings["sent_limit"]))
          $settings["sent_limit"]=15;

      if(!isset($settings["interval"]))
          $settings["interval"]=20;

      if(!isset($settings["dictionary"]))
         $settings["dictionary"]=8;

      $this->connect();
      $query="UPDATE settings SET parameters=".$settings["sent_limit"]." WHERE name='sent_limit'";
      mysqli_query($this->link,$query) or die('Query fault: ' . mysqli_error($this->link)."<br/> Query:".$query);
      $query="UPDATE settings SET parameters=".$settings["interval"]." WHERE name='interval'";
      mysqli_query($this->link,$query) or die('Query fault: ' . mysqli_error($this->link)."<br/> Query:".$query);
      $query="UPDATE settings SET parameters=".$settings["dictionary"]." WHERE name='dictionary'";
      mysqli_query($this->link,$query) or die('Query fault: ' . mysqli_error($this->link)."<br/> Query:".$query);
      $this->disconnect();
    }

    /**
     * Function newExercise()
     * Function clean exercise table in database
     */
    function newExercise(){
        $this->connect();
        $query="DELETE FROM exercise";
        mysqli_query($this->link,$query) or die('Query to exercise fault: ' . mysqli_error($this->link));
        $this->disconnect();
    }

    /**
     * Function addSentenceToExercise()
     * Function add sentence id to exercise
     * input $sentence_id - id of sentence
     */
    function addSentenceToExercise($sentence_id){
        $this->connect();
        $query="INSERT INTO `exercise`( `sentence_id`)
                VALUES (".$sentence_id.")";
        mysqli_query($this->link,$query) or die('Query fault: ' . mysqli_error($this->link)."<br/>Query:".$query);
        $this->disconnect();
    }

    /**
     * Function getSentencesFromExercise()
     * Function get sentences from exercise
     * input $sentence_id - id of sentence
     */
    function getSentencesFromExercise(){
        $return_arr=array();
        $this->connect();
        $query="SELECT A.* FROM (SELECT dictionary.id,dictionary.english,dictionary.other FROM dictionary
                JOIN exercise WHERE dictionary.id=exercise.sentence_id ORDER BY dictionary.id DESC, RAND()) AS A
                ORDER BY RAND()";
        $result=mysqli_query($this->link,$query) or die('Query fault: ' . mysqli_error($this->link)."<br/>Query:".$query);
        while($row=mysqli_fetch_assoc($result)){
            $return_arr[]=array('id'=>$row["id"], "english"=>$row["english"],"other"=>$row["other"]);
        }
        $this->disconnect();
        return $return_arr;
    }

    /**
     * Function getSentenceByID
     * Function get sentences from dictionary table by sentence id
     * input  $id - id of sentence
     * output $retarr - array of sentence
     */
    function getSentenceByID($id){
        $return_arr=array();
        $this->connect();
        $query="SELECT * FROM dictionary WHERE id=".$id;
        $result=mysqli_query($this->link,$query) or die('Query fault: ' . mysqli_error($this->link)."<br/>Query:".$query);
        while ($row=mysqli_fetch_assoc($result)){
         $return_arr["english"]=$row["english"];
         $return_arr["other"]=$row["other"];
        }
        $this->disconnect();
        return $return_arr;
    }

    /**
     * Function compareSentences()
     * Function compare translated sentence with original
     * input $s_arr - id of sentence
     *       $v_arr - translated sentences
     *       $direction - direction of compare (true - compare $v_arr
     *                                          with "other" sentnece,
     *                                          false - compare $v_arr
     *                                          with "english" sentnece,
     */
    function compareSentences($s_arr,$v_arr,$direction){

      if (!isset($direction))
         $direction=true;

      if (isset($s_arr)&&isset($v_arr)){
          for ($i=0;$i<count($s_arr);$i++){
            $result=$this->getSentenceByID($s_arr[$i]);
            if ($direction){
              $var_1=$result["other"];
            } else {
              $var_1=$result["english"];
            }
            similar_text($this->removeSymbols($var_1),$this->removeSymbols($v_arr[$i]), $percent);
            if ($percent>70){
             $this->updateSuccesCount($s_arr[$i]);
            }
          }
      }
    }

    /**
     * Function removeSymbols
     * Function remove specefic symbols and spaces from string
     * input  $str - string for process
     * output $string - clean string
     */
    function removeSymbols($str){
        $string=$str;
        $string = str_replace(' ', '', $string);
        $string = str_replace('.', '', $string);
        $string = str_replace('-', '', $string);
        $string = str_replace('|', '', $string);
        $string = str_replace('?', '', $string);
        $string = str_replace('!', '', $string);
        $string = str_replace('(', '', $string);
        $string = str_replace(')', '', $string);
        $string = str_replace('"', '', $string);
        $string = str_replace('\'', '', $string);
        $string = str_replace('\\', '', $string);
        $string = str_replace('/', '', $string);
        return $string;
    }

    /**
     * Function updateSuccesCount
     * Increase lcount in dictionary table
     * input  $id - id of sentence
     */
    function updateSuccesCount($id){
        $this->connect();
        $query="UPDATE `dictionary` SET `lcount`=`lcount`+1 WHERE `id`=".$id;
        mysqli_query($this->link,$query) or die('Query to update count fault: ' . mysqli_error($this->link)."<br/>Query:".$query);
        $this->disconnect();
    }

    /**
     * Function cleanSuccessCount()
     * Set lcount=0 in dictionary table for all records
     */
    function cleanSuccessCount(){
        $this->connect();
        $query="UPDATE dictionary SET lcount=0";
        mysqli_query($this->link,$query) or die('Query to update count fault: ' . mysqli_error($this->link)."<br/>Query:".$query);
        $this->disconnect();
    }
}