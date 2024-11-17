<?php

    $hostname = "localhost";
    $bancodedados = "banco_cartorio";
    $usuario = "root";
    $senha = "admin123";

    $mysqli = new mysqli($hostname, $usuario, $senha, $bancodedados);
    if($mysqli->connect_errno){
        echo "Falha ao conectar: (" . $mysqli->connect_errno . " ) " . $mysqli->connect_error;
    }
?>