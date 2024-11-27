<?php

include_once("db/DB.php");
include_once("tables/Postulantes.php");

$db = new DB();
if ($db->connect()) {
    $postulantes = new Postulantes();
    $postulantes->prueba();
    $db->disconnect();
} else {
    echo "Malió sal la cosa";
}