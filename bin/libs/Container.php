<?php

class Container {
    protected $outputPath;
    protected $tables = [];
    protected $data;
    protected $sqlGenerator;
    
    public function __construct($data, $outputPath, $sqlGenerator) {
       $this->data = $data;
       $this->outputPath = $outputPath;
       $this->sqlGenerator = $sqlGenerator;
    }
   
    public function addTable(Table $table){
       $this->tables[]=$table;
    }
    
    public function generateSql(){
        foreach ($this->tables as $table) {
            $this->createSchema($table);
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
