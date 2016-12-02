<?php
include("config.php");

//load the data and convert to array
$string = file_get_contents(__DIR__ ."/resources/data.json");
$json = json_decode($string, true);
$json = $json["forms"];


//container that will call all the function within the injected tables
$container = new Container($json, "data.txt", SqlGenerator::class);

$container->addTable(new Table("dh_inventory",INVENTORY ));
$container->addTable(new Table("dh_antenantals", ANTINANTALS ));
$container->addTable(new Table("dh_familyplan",FAMILYPLAN ));
$container->addTable(new Table("dh_sickchild",SICKCHILD ));
$container->addTable(new Table("dh_satelliteclinic", SATELLITE));

$container->generateSql();





