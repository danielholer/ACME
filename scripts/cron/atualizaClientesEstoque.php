<?php
include_once(__DIR__.'/../../includes.inc');

header('Content-Type: text/html; charset=utf-8');

$insert = " INSERT INTO SOMA_CLIENTE
			SELECT CODI_TRA, RAZA_TRA FROM TRANSAC
			WHERE SITU_TRA = 'A'
				AND CODI_TRA NOT IN	(SELECT ID FROM SOMA_CLIENTE)
				AND CODI_TRA > 3000";

$sql = new clsDB();

$res = $sql->execute($insert);

if ($res) {
    $sql->commit();
    echo "OK";
} 
else {
    echo "ERRO: ".$sql->getLastError();
}
