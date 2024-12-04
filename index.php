<?php

include_once("db/DB.php");
include_once("services/Orchestrator.php");

$db = new DB();
if ($db->connect()) {
    $conect = $db->getConection();
    $orchestrator = new Orchestrator($conect);
    $orchestrator->manageTables();
    $db->disconnect();
} else {
    echo "Malió sal la cosa (｡•́︿•̀｡)"."\n";
}
