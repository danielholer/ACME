<?php
include_once(__DIR__.'/../../includes.inc');

header('Content-Type: text/html; charset=utf-8');

$arrEmpresas = ["SOMA","SOMAR","CRISTALINA"];

foreach($arrEmpresas as $empresa)
{
	$sql = new clsDB($empresa);

	$update =  "UPDATE RECEBER SET DPON_REC = 0
				WHERE CTRL_CBR in
				(
					SELECT C.CTRL_CBR 
					FROM NOTA A
					INNER JOIN NOTACRC B ON B.CODI_EMP = A.CODI_EMP AND B.NDOC_NOC = A.NOTA_NOT AND B.SDOC_NOC = A.SERI_NOT
					INNER JOIN RECEBER C ON C.CTRL_CBR = B.CTRL_CBR
					WHERE  ((ACRE_NOT = 0 OR ACRE_NOT IS NULL) AND DPON_REC > 0
					 OR ACRE_NOT > 0 AND DPON_REC = 0)
					AND A.DEMI_NOT >= TO_DATE('01/10/2017','DD/MM/YYYY')
					AND A.SITU_NOT <> 9
				)";

	$res = $sql->execute($update);

	$update =  "UPDATE RECEBER SET DPON_REC = 5
				WHERE CTRL_CBR in
				(
					SELECT C.CTRL_CBR 
					FROM NOTA A
					INNER JOIN NOTACRC B ON B.CODI_EMP = A.CODI_EMP AND B.NDOC_NOC = A.NOTA_NOT AND B.SDOC_NOC = A.SERI_NOT
					INNER JOIN RECEBER C ON C.CTRL_CBR = B.CTRL_CBR
					WHERE  ((ACRE_NOT = 0 OR ACRE_NOT IS NULL) AND DPON_REC > 0
					 OR ACRE_NOT > 0 AND DPON_REC = 0)
					AND A.DEMI_NOT >= TO_DATE('01/10/2017','DD/MM/YYYY')
					AND A.SITU_NOT <> 9
				)";

	$res = $sql->execute($update);

	$sql->commit();

	$sql->close();

}