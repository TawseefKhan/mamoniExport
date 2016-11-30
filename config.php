<?php
//auto loader for all classes
$directory="libs/";
function __autoload($class_name) {
    if (file_exists($directory . $class_name . '.php')) {
        require_once ($directory . $class_name . '.php');
        return;
    }
}

//field sets 
define("ANTINANTALS", "resources/dh_antenantals.csv");
define("FAMILYPLAN", "resources/dh_familyplan.csv");
define("INVENTORY", "resources/dh_inventory.csv");
define("SATELLITE", "resources/dh_satelliteclinic.csv");
define("SICKCHILD", "resources/dh_sickchild.csv");

//API Link
define("API_URL", "");
define("USERNAME", "");
define("PASSWORD", "");