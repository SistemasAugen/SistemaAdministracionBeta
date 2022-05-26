<?php

function initConnection() {
    $host = 'localhost';
    $username = 'system_user';
    $password = '%^Ue_vtx&*,G';
    $database = 'augen_sistema';

    $mysqli = new mysqli($host, $username, $password, $database);

    if (mysqli_connect_error()) {
        die('Error de Conexión (' . mysqli_connect_errno() . ') '
        . mysqli_connect_error());
    }

    return $mysqli;
}

?>
