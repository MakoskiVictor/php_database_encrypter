<?php

include_once("db/DB.php");
include_once("tables/TableProcessor.php");

$db = new DB();
if ($db->connect()) {
    $tableProcessor = new TableProcessor();
    $conect = $db->getConection();
    $tableProcessor->useConection($conect);
    $tableProcessor->startEncryption();
    $db->disconnect();
} else {
    echo "Malió sal la cosa (｡•́︿•̀｡)"."\n";
}
