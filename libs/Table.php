<?php

class Table extends Schema {
    
    //will hold all the excel rows
    protected $keys;
    
    
    //will create the schema of the table
    public function createSchema(){
        foreach ($this->keys as $key => $value) {
            //if not array simple
            if($value=["type"]!="textarray"){
                $row = [];
                $row["name"] = $key;
                $row["type"] = $value=["type"];
                $this->rowNames[]=$row;
            }
            else{
                $types = explode(",",$value["subtype"]);
                for ($i=0; $i<$value["size"]; $i++){
                    $row=[];
                    $row["name"] = $key . "_" . $i;
                    $row["type"] = (isset($types[$i])? $types[$i] : $types[0]);
                    $this->rowNames[]=$row;
                    
                }
            }
        }
    }
    
    //will map the meta data
    private function saveMetaData($rawData, $arr=[]){
        
    }
    
    //will map the geoData
    private function saveGeoData($rawData, $arr=[]){
        
    }
    
    private function getVal($key, $data){
        //booleans
        if($this->key[$key]["value"]=="bool"){
            if(is_bool($data))
                return $data;
            
            $data = trim($data);
            if($data=="0" || $data=="false" || $data=="")
                return false;
            else if($data=="1" || $data=="false")
                return true;
        }
        
        //number
        if($this->key[$key]["value"]=="number"){
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
        if($this->key[$key]["value"]=="text"){
            return trim($data);
        }
        
        //date
        if($this->key[$key]["value"]=="date"){
            
        }
        
        //time
        if($this->key[$key]["value"]=="time"){
            
        }
        
    }
    
    //will map form core data
    private function saveCoreData($rawData, $arr=[]){        
        //textarray
        if($this->key[$key]["value"]=="textarray"){
            
        }
    }
    
    //will add a single row
    public function addRow($data){
        $newRow = [];
        $this->saveMetaData($data, $newRow);
        $this->saveMetaData($data, $newRow);
        $this->saveMetaData($data, $newRow);
        $this->rowData[]=$newRow;
    }
    
    
}
