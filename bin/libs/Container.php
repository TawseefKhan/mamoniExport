<?php

class Container {
    protected $outputPath;
    protected $tables = [];
    protected $data;
    protected $sqlGenerator;
    protected $locations = [];
    
    public function __construct($data, $outputPath, $sqlGenerator, $fieldsPath) {
       $this->data = $data;
       $this->outputPath = $outputPath;
       $this->sqlGenerator = $sqlGenerator;
       
       $this->decodeLocations($fieldsPath);
    }
    
    //converts the locations.csv to proper array
    private function decodeLocations($fieldsPath){
        $file = fopen($fieldsPath, "r");
        while(! feof($file)){
            $row = fgets($file);
            if($row!="")
                $this->addLocation($row);
        }
        fclose($file);
    }
    
    //adds a single location from the csv file
    private function addLocation($row){
        $row = explode(",", $row);
        
        for($i=0; $i<sizeof($row); $i++){
            $row[$i]= strtolower(trim($row[$i]));
            if($row[$i]=="")
                $row[$i]=null;
        }
        
        //district
        if(!isset($this->locations[$row[0]])){
            $this->locations[$row[0]]=[];
        }
        
        //facility type
        if(!isset($this->locations[$row[0]][$row[1]])){
            $this->locations[$row[0]][$row[1]]=[];
        }
        
        //union or village
        $this->locations[$row[0]][$row[1]][$row[2]]=[];
        
        //save the geocodes
        $this->locations[$row[0]][$row[1]][$row[2]]["div"] = $row[3];
        $this->locations[$row[0]][$row[1]][$row[2]]["dist"] =  $row[4];
        $this->locations[$row[0]][$row[1]][$row[2]]["upz"] =  $row[5];
        $this->locations[$row[0]][$row[1]][$row[2]]["union"] =  $row[6];
    }


    public function addTable(Table $table){
       $this->tables[]=$table;
    }
    
    public function generateSql(){
        foreach ($this->tables as $table) {
            $this->createSchema($table);
            $table->setLocations($this->locations);
        }
        
        //loop through the data
        foreach ($this->data as $data_row) {
            //add to proper class
            foreach ($this->tables as $table) {
                if($data_row["form_type"]==$table->getTableName()){
                    $table->addRow($data_row);
                    break;
                }
            }
        }
            
        //save the data
        $myfile = fopen($this->outputPath, "w") or die("Unable to open file!");
        foreach ($this->tables as $table) {
            $generator = new $this->sqlGenerator($table->getTableName(),$table);
            fwrite($myfile, $generator->getSql());
        }
        fclose($myfile);
        
        //report        
        foreach ($this->tables as $table) {
            echo $table->getTableName() . " : " . $table->getAmbiguousCount() . "</br>"; 
        }
        
    }
    
    //open the file and add all the rows to respective classes
    private function createSchema(Table $table){
        $file = fopen($table->getFieldConfigPath(), "r");
        while(! feof($file)){
            $row = fgets($file);
            if($row!="")
                $table->addKeys($row);
        }
        fclose($file);
        $table->createSchema();
    }
    
}
