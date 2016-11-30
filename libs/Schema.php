<?php
/*
 * This object will be used to ultimately create the sql dump file
 */
class Schema {
   protected $rowNames = [];
   protected $rowData = [];
   protected $rowPrimary = [];
   
   public function getRowNames(){
       return $this->rowNames;
   }
   
   public function getRowData(){
       return $this->getRowData();
   }
   
   public function getPrimary(){
       return $this->getRowData();
   }
}
