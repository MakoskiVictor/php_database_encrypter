<?php

include_once("db/DB.php");
include_once("tables/Orchestrator.php");

$db = new DB();
if ($db->connect()) {
    $orchestrator = new Orchestrator();
    $conect = $db->getConection();
    $orchestrator->useConection($conect);
    $orchestrator->startEncryption();
    $db->disconnect();
} else {
    echo "Malió sal la cosa (｡•́︿•̀｡)"."\n";
}