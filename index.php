<?php
include("config.php");

//load the field names and add it to the classes
$inventory = new Table();
$file = fopen(INVENTORY, "r");
while(! feof($file)){
    $row = fgets($file);
    if($row!="")
        $inventory->addKeys($row);
}
fclose($file);
$inventory->createSchema();

//load the data and convert to array
$string = file_get_contents("resources/data.json");
$json = json_decode($string, true);
$json = $json["forms"];

//loop through the data
foreach ($json as $data_row) {
    if($data_row["form_type"]=="dh_inventory"){
        $inventory->addRow($data_row);
    }
}
$inventory->showData();

//add to proper class
