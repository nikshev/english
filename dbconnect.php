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
        mysql_query($query) or die('Query fault: ' . mysql_error());
        $id=mysql_insert_id();
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
        mysql_query($query) or die('Query fault: ' . mysql_error()."<br/>Query:".$query);
        $this->disconnect();
    }

    /**
     * Function getDictionaries()
     * Return array of dictionaries stored in database
     */
    public function getDictionaries(){
        $this->connect();
        $query="SELECT * FROM dictionaries";
        $result=mysql_query($query) or die('Query fault: ' . mysql_error()."<br/>Query:".$query);
        $return_arr=array();
        while($row=mysql_fetch_assoc($result)){
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
        mysql_query($query) or die('Query to dictionary fault: ' . mysql_error());
        $query="DELETE FROM dictionaries";
        mysql_query($query) or die('Query to dictionaries fault: ' . mysql_error());
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
        $query="SELECT english,other FROM `dictionary` WHERE lcount<20 and id_dic=".$id." limit ".$limit;
        $result=mysql_query($query) or die('Query fault: ' . mysql_error()."<br/>Query:".$query);
        $this->disconnect();
        return $return_arr;
    }

    /**
     * Function connect()
     * Function for connect to db
     */
    private function connect(){
        $this->link = mysql_connect($this->host, $this->dbuser,$this->password);
        if (!$this->link) {
            die('Connection error: ' . mysql_error());
        }
        mysql_select_db($this->dbname) or die('Database error: ' . mysql_error());
    }

    /**
     * Function diconnect()
     * Function for diconnect to db
     */
    private function disconnect(){
        if (isset($this->link))
            mysql_close($this->link);
    }
}