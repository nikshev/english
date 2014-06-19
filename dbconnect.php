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
     *
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
}