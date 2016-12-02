<?php

class Table extends Schema {
    
    //will hold all the excel rows
    protected $keys;
    protected $tableName;
    protected $fieldConfigPath;
            
    function __construct($tableName, $fieldConfigPath ) {
       $this->tableName = $tableName; 
       $this->fieldConfigPath = $fieldConfigPath;
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
    private function saveGeoData($rawData, $arr=[]){
        
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
        $rawData=$rawData["data"];
        
        foreach ($this->keys as $key => $value) {
            //textarray
            if($value["type"]=="textarray"){
                //loop through the size
                $types = explode(",",$value["subtype"]);
                for ($i=0; $i<$value["size"]; $i++){
                    $row=[];
                    $row["name"] = $key . "_" . $i;
                    $row["type"] = (isset($types[$i])? $types[$i] : $types[0]);
                    
                    if(!is_array($rawData[$key])){
                        $rawData[$key] = explode("~",$rawData[$key] );
                    }
                    if(!isset($rawData[$key][$i]))
                        $rawData[$key][$i]=null;
            
                    $arr[$row["name"]]=  $this->getVal($rawData[$key][$i], $row["type"]);
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
        $newRow = [];
        $newRow = array_merge($newRow, $this->saveMetaData($data));
        $newRow = array_merge($newRow, $this->saveCoreData($data));
//        $this->saveGeoData($data, $newRow);
        $this->rowData[]=$newRow;
    }
    
    
    public function showData(){
        var_dump($this->rowData);
    }
    
}