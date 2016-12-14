<?php

class Table extends Schema {
    
    //will hold all the excel rows
    protected $keys;
    protected $tableName;
    protected $fieldConfigPath;
    protected $geoFields;
    protected $ambiguousCount = 0;
    protected $counter = 0;
            
    function __construct($tableName, $fieldConfigPath) {
       $this->tableName = $tableName; 
       $this->fieldConfigPath = $fieldConfigPath;
   }
   
   public function setLocations($geoFields){
       $this->locations = $geoFields;
   }
   
   public function getTableName(){
       return $this->tableName;
   }
   
   public function getFieldConfigPath(){
       return $this->fieldConfigPath;
   }
    
    public function addKeys($row){
        try {
            $row = explode(",", $row);
            for ($i=0; $i<sizeof($row); $i++){
                $row[$i] = trim($row[$i]);
            }

            $this->keys[$row[0]]["type"] =  $row[1];
            if($row[1]=="textarray"){
                $this->keys[$row[0]]["size"] = $row[2];
                $this->keys[$row[0]]["subtype"] = $row[3];
            }
        } catch (Exception $ex) {
            echo "Please make sure that the field file is properly formatted";
        }
//        var_dump($this->keys);
    }
    
    
    //will create the schema of the table
    public function createSchema(){
        //meta Data
        $this->rowNames = array_merge($this->rowNames,array(
            array("name" => "timestamp" , "type" => "timestamp" ),
            array("name" => "user_id" , "type" => "number" ),
            array("name" => "status" , "type" => "number" ),
            array("name" => "checked_by_user" , "type" => "number" )
            
        ));
        
        
        //form Data
        foreach ($this->keys as $key => $value) {
            //if not array simple
            if($value["type"]!="textarray"){
                $row = [];
                $row["name"] = $key;
                $row["type"] = $value["type"];
                $this->rowNames[]=$row;
            }
            else{
                $types = explode("~",$value["subtype"]);
                for ($i=0; $i<$value["size"]; $i++){
                    $row=[];
                    $row["name"] = $key . "_" . $i;
                    $row["type"] = (isset($types[$i])? $types[$i] : $types[0]);
                    $this->rowNames[]=$row;
                }
            }
        }
        
        //meta Data
        $this->rowNames = array_merge($this->rowNames,array(
            array("name" => "_div" , "type" => "number" ),
            array("name" => "_dist" , "type" => "number" ),
            array("name" => "_upz" , "type" => "number" ),
            array("name" => "_union" , "type" => "number" ),
            array("name" => "_geoProxy" , "type" => "text" )
        ));
        
//        var_dump($this->rowNames);
    }
    
    //will map the meta data
    private function saveMetaData($rawData, $arr=[]){
        return array(
            "timestamp" => $rawData["timestamp"],
            "user_id" => $rawData["user_id"],
            "status" => $rawData["status"],
            "checked_by_user" => $rawData["checked_by_user"]
        );
    }
    
    //will map the geoData
    private function saveGeoData($rowData, $arr=[]){
        $rowData["data"] = (array)$rowData["data"];
        $district = $rowData["user_district"];
        $facility = (isset($rowData["data"]["facility"])?$rowData["data"]["facility"]:null);
        $unions = null;
        if(isset($rowData["data"]["union"]))
        {
            $unions = $rowData["data"]["union"];
        }
        
//        var_dump($rowData);
        
        //filter the array one by one
        $arr = $this->getLocationOptions($this->locations, $district);
        $arr = $this->getLocationOptions($arr, $facility);
        $arr = $this->getLocationOptionsMultiple($arr, array($unions));
        
        //save the data to the array
        if(sizeof($arr)==1){
            reset($arr);
            $key = key($arr);
            $arr = $arr[$key];
            
            return array(
                "_div" => $arr["div"],
                "_dist" => $arr["dist"],
                "_upz" => $arr["upz"],
                "_union" => $arr["union"],
                "_geoProxy" => "NONE"
            );
        }
        else{
	    $geoDataTemp = array(
                "_div" => $this->checkIfSame($arr, "div"),
                "_dist" => $this->checkIfSame($arr, "dist"),
                "_upz" => $this->checkIfSame($arr, "upz"),
                "_union" => $this->checkIfSame($arr, "union")
            );
            if( $geoDataTemp["_div"] ==null || $geoDataTemp["_dist"] ==null || $geoDataTemp["_upz"] ==null || $geoDataTemp["_union"] ==null){
            	$this->ambiguousCount++;
                $geoDataTemp["_geoProxy"] = json_encode($arr);
	    }
	    else{
	        $geoDataTemp["_geoProxy"] = "NONE";
	    }
            return $geoDataTemp; 
        }
    }
    
    public function getAmbiguousCount(){
        return $this->ambiguousCount;
    }
    
    public function getTableCount(){
        return $this->counter;
    }
    
    private function checkIfSame($arr, $key_str){
        $id=null;
        foreach ($arr as $key => $value) {
            if($id==null)
            {
                $id=$value[$key_str];
                continue;
            }
            else{
                if($value[$key_str]!=$id)
                    return null;
            }
        }
        return $id;
    }
    
    private function getLocationOptionsMultiple($arr, $query){
        //only one possibility left
        if(sizeof($arr)==1){
            return $arr;
        }
        else{
            $newArr = [];
            $all = [];
            foreach ($arr as $key => $value) {
//                var_dump($query);
//                echo strtolower($query[0]) . " : " . strtolower($query[1]) . "</br>" ;
                if (strpos(strtolower($query[0]), $key) !== false) {
                    $newArr[$key] = $value;
                }
                else{
                    $all[$key] = $value;
                }
            }
            
            if(sizeof($newArr)==0)
                return $all;
            else
                return $newArr;
        }            
            
    }
    
    
    private function getLocationOptions($arr, $query){
        //only one possibility left
        if(sizeof($arr)==1){
            reset($arr);
            $key = key($arr);
            return $arr[$key];
        }
        else{
            $newArr = [];
            $all = [];
            foreach ($arr as $key => $value) {
                if (strpos(strtolower($query), $key) !== false) {
                    $newArr = array_merge($newArr, $value);
                }
                else{
                    $all = array_merge($all, $value);
                }
            }
            
            if(sizeof($newArr)==0)
                return $all;
            else
                return $newArr;
        }            
            
    }
    
    private function getVal($data, $type){
        //booleans
        if($type=="bool"){
            $data = trim($data);
            if($data=="1" || $data=="true" || $data===true)
                return "true";
            else
                return "false";
        }
        
        //number
        if($type=="number"){
            if(is_int($data))
                return $data;
            else{
                 try{
                     return intval(trim($data));
                } catch (Exception $ex) {
                    return -9;
                }
            }
           
        }
        
        //text
        if($type=="text"){
            return trim($data);
        }
        
        //date
        if($type=="date"){
            $data = trim($data);
        if($data == null || $data == "")
            return null;
        
        $date = date_create_from_format("d-m-Y", $data);
        return $date->format("m-d-Y");
        }
        
        //time
        if($type=="time"){
            return trim($data);
        }
        
    }
    
    //will map form core data
    private function saveCoreData($rawData){ 
        $arr=[];
        $rawData=(array)$rawData["data"];
        
        foreach ($this->keys as $key => $value) {
            $value = (array)$value;
            //textarray
            if($value["type"]=="textarray"){
                //loop through the size
                $types = explode("~",$value["subtype"]);
                for ($i=0; $i<$value["size"]; $i++){
                    $row=[];
                    $row["name"] = $key . "_" . $i;
                    $row["type"] = (isset($types[$i])? $types[$i] : $types[0]);
                    
                    if(!is_array($rawData[$key])){
                        $rawData[$key] = explode(",",$rawData[$key] );
                    }
                    if(!isset($rawData[$key][$i]))
                        $rawData[$key][$i]=null;
                    
                    $type="";
                    if(isset($types[$i]))
                        $type=$types[$i];
                    else
                        $type=$types[0];
                    
//                    var_dump($rawData[$key][$i]);
//                    echo $type . "</br>";
            
                    $arr[$row["name"]]=  $this->getVal($rawData[$key][$i], $type);
                }
            }
            else{
                if(!isset($rawData[$key]))
                        $rawData[$key]=null;
                
                
                $arr[$key]=  $this->getVal($rawData[$key], $this->keys[$key]["type"]);
            }
        }
        
        return $arr;
        
    }
    
    //will add a single row
    public function addRow($data){
        $data = (array)$data;
        $this->counter++;
        $newRow = [];
        $newRow = array_merge($newRow, $this->saveMetaData($data));
        $newRow = array_merge($newRow, $this->saveGeoData($data));
        $newRow = array_merge($newRow, $this->saveCoreData($data));
        $this->rowData[]=$newRow;
    }
    
    
    public function showData(){
        var_dump($this->rowData);
    }
    
}
