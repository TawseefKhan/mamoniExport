<?php

class Table extends Schema {
    
    //will hold all the excel rows
    protected $keys;
    
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
        
    }
    
    //will map the geoData
    private function saveGeoData($rawData, $arr=[]){
        
    }
    
    private function getVal($data, $type){
        //booleans
        if($type=="bool"){
            $data = trim($data);
            if($data=="0" || $data=="false" || $data=="" || $data===false)
                return "false";
            else if($data=="1" || $data=="true" || $data===true)
                return "true";
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
            return trim($data);
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
        $newRow = array_merge($newRow, $this->saveCoreData($data));
//        $this->saveMetaData($data, $newRow);
//        $this->saveGeoData($data, $newRow);
        $this->rowData[]=$newRow;
    }
    
    
    public function showData(){
        var_dump($this->rowData);
    }
    
}
