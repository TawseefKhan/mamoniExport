<?php
//auto loader for all classes
$directory="/libs/";
function __autoload($class_name) {
    global $directory;
    if (file_exists(__DIR__  . $directory . $class_name . '.php')) {
        require_once ($directory . $class_name . '.php');
        return;
    }
}

//field sets 
define("ANTINANTALS", __DIR__ . "/resources/dh_antenantals.csv");
define("FAMILYPLAN", __DIR__ . "/resources/dh_familyplan.csv");
define("INVENTORY", __DIR__ . "/resources/dh_inventory.csv");
define("SATELLITE", __DIR__ . "/resources/dh_satelliteclinic.csv");
define("SICKCHILD", __DIR__ . "/resources/dh_sickchild.csv");
define("FIELDS", __DIR__ . "/resources/locations.csv");

//API Link
define("API_URL", "http://119.148.43.34/mamoni/survey/api/sync");
define("USERNAME", $argv[1]);
define("PASSWORD", $argv[2]);