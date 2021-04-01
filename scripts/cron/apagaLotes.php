<?php
include_once(__DIR__.'/../../includes.inc');

header('Content-Type: text/html; charset=utf-8');

$delete =  "DELETE FROM MOVLOTPV
            WHERE CODI_PSV IN
            (
                SELECT CODI_PSV FROM PRODSERV WHERE CODI_GPR <> 3
            )";

$sql = new clsDB("SOMA");
$res = $sql->execute($delete);

if ($res) {
    $sql->commit();
    echo "OK";
} 
else {
    echo "ERRO";
}

