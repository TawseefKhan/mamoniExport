<?php

class SqlGenerator {
    
    public $schema;
    protected $sql="";
    protected $tableName;
    
    function __construct($tableName, Schema $schema ) {
       $this->schema = $schema;
       $this->tableName = $tableName;       
   }
    
    private function createTable(){
        $query = "DROP TABLE IF EXISTS ".$this->tableName.";";
        $query .= "CREATE TABLE ".$this->tableName."(";
        $query .= "_id SERIAL,";
        $i=1;
        $size = sizeof($this->schema->getRowNames());
        foreach ($this->schema->getRowNames() as $row){
            $type="";
            if($row["type"]=="number")
                $type = "integer";
            else
                $type = $row["type"];
            
            if($size==$i)
                $query .= '"' .  $row["name"] . '" ' . $type;
            else           
                $query .= '"' .  $row["name"] . '" ' . $type . ", ";   
            
            $i++;
        }
        $query .= ", CONSTRAINT ".$this->tableName."_pk PRIMARY KEY (_id));";
        
        $this->sql .= $query;
    }
    
    private function createData(){
        foreach ($this->schema->getRowData() as $row) {
            $query = "INSERT INTO ". $this->tableName ." VALUES(";
            $query .= "DEFAULT,";
            $i=1;
            $size = sizeof($row);
            foreach ($row as $key => $col){
                if($col=="")
                {
                    if($size==$i)
                        $query .= "null" ;
                    else           
                        $query .= 'null, ';  

                    $i++;
                    continue;
                }
                
                if($size==$i)
                    $query .= "'" . $col . "' " ;
                else           
                    $query .= "'" . $col . "', ";   

                $i++;
            }
            $query .= ");";

            $this->sql .= $query;
        }
    }
    
    public function getSql(){
        $this->createTable();
        $this->createData();
        return $this->sql;
    }
    
}
