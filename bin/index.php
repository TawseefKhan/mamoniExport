<?php
include("config.php");

echo "FETCHING DATA.... This may take upto 5 minutes.\n";

//load the data and convert to array
$data = array('data' => '{"get_all":true, "username":"'.USERNAME.'", "password":"'.PASSWORD.'"}');

$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents(API_URL, false, $context);

//failed request
if ($result === FALSE) { echo "data fetching failed"; exit(); }

//parse the json responnse
try{
    $json = json_decode($result);
    $json = (array)$json;
} catch (Exception $ex) {
    //wrong json
    echo "data fetching failed";
    exit();
}
echo $json["message"] . "\n";
if($json["status"]==false){
    exit();
}


//loading from local
//$string = file_get_contents(__DIR__ ."/resources/data.json");
//$json = json_decode($string, true);
//$json = $json["forms"];


//container that will call all the function within the injected tables
$container = new Container($json["forms"], DATAPATH, SqlGenerator::class, FIELDS);

$container->addTable(new Table("dh_inventory",INVENTORY ));
$container->addTable(new Table("dh_antenantals", ANTINANTALS ));
$container->addTable(new Table("dh_familyplan",FAMILYPLAN ));
$container->addTable(new Table("dh_sickchild",SICKCHILD ));
$container->addTable(new Table("dh_satelliteclinic", SATELLITE));

$container->generateSql();


echo "\n\n=============COMPLETED";

