<?php
include_once(__DIR__.'/../../includes.inc');

header('Content-Type: text/html; charset=utf-8');

$empresa = 1;

$sql = new clsDB("SOMA");

$select = "SELECT 
			CODI_PSV
			, TO_CHAR(A.DREC_NFE,'DD/MM/YYYY') AS DREC_NFE
			, MAX(A.QUAN_INF) AS QUAN_INF
			, ROUND(A.VLOR_INF,2) AS VLOR_INF
			, TO_CHAR(NVL(D.DVEN_PAG,(SELECT DTBA_CTA FROM CABTAB WHERE TABE_CTA = 3)),'DD/MM/YYYY') AS DVEN_PAG 
			, NVL(D.DVEN_PAG - (SELECT DTBA_CTA FROM CABTAB WHERE TABE_CTA = 3),0) AS DIF
			, (SELECT FAPV_CTA FROM CABTAB WHERE TABE_CTA = 3) AS FAPV_CTA
			, (SELECT FATV_CTA FROM CABTAB WHERE TABE_CTA = 3) AS FATV_CTA
			FROM INFENTRA A
			LEFT JOIN NOTACPG C ON A.CODI_EMP = C.CODI_EMP AND A.CODI_TRA = C.CODI_TRA AND A.NUME_NFE = C.NDOC_NCP AND A.SERI_NFE = C.SDOC_NCP
			LEFT JOIN PAGAR D ON C.CTRL_CPG = D.CTRL_CPG
			WHERE A.CODI_EMP = $empresa
			AND A.DREC_NFE > TO_DATE('01/08/2019','DD/MM/YYYY')
			AND (A.CODI_PSV, A.DREC_NFE) IN 
				(
					SELECT A.CODI_PSV, MAX(A.DREC_NFE) DREC_NFE FROM INFENTRA A
					INNER JOIN PRODSERV C ON C.CODI_PSV = A.CODI_PSV AND C.CODI_GPR = 3
					GROUP BY A.CODI_PSV
				)
			AND 
			    (A.DUMANUT > (SELECT MAX(AA.DUMANUT) FROM TABELA AA INNER JOIN CABTAB BB ON AA.TABE_CTA = BB.TABE_CTA AND BB.SITU_CTA = 'A' WHERE AA.CODI_PSV = A.CODI_PSV)
			    OR A.CODI_PSV NOT IN (SELECT CODI_PSV FROM TABELA AA INNER JOIN CABTAB BB ON AA.TABE_CTA = BB.TABE_CTA AND BB.SITU_CTA = 'A')
			    OR A.CODI_PSV IN (SELECT CODI_PSV FROM TABELA AA INNER JOIN CABTAB BB ON AA.TABE_CTA = BB.TABE_CTA AND BB.SITU_CTA = 'A' WHERE AA.SITU_TAB = 'I'))
			GROUP BY A.CODI_PSV, A.DREC_NFE, ROUND(A.VLOR_INF,2), D.DVEN_PAG
			ORDER BY A.CODI_PSV, A.DREC_NFE DESC
			";

$arr = $sql->getDados($select);

$valorTotal = 0;
$qtdeTotal = 0;
$length = count($arr);
for($i = 0; $i < $length; ++$i) {

	$curarr = $arr[$i];
	$nextarr = next($arr);
	$codi_psv = $curarr["CODI_PSV"];

	$juros = $curarr["DIF"] > 0 ? $curarr["FAPV_CTA"] : $curarr["FATV_CTA"];
	$custo = $curarr["VLOR_INF"] * (1-(($curarr["DIF"]/30)*$juros/100));


	echo $codi_psv;
	echo " -> ";
	echo $nextarr["CODI_PSV"];;
	echo " -> ";
	echo $curarr["DVEN_PAG"];
	echo " -> ";
	echo $curarr["VLOR_INF"];
	echo " -> ";
	echo $custo;

	$valorTotal += $custo * $curarr["QUAN_INF"];
	$qtdeTotal += $curarr["QUAN_INF"];
	$custoMedio = $valorTotal / $qtdeTotal;

	if($curarr["CODI_PSV"] <> $nextarr["CODI_PSV"]){
		$valorTotal = 0;
		$qtdeTotal = 0;
		echo " -> ";
		echo "MERGE";

		fncMerge($codi_psv, $custoMedio);
	}

	echo " -> ";
	echo $custoMedio;


	echo "<br>";
	


}

function fncMerge($codi_psv, $custo) {

	$margem = 30;
	$margem_min = 15;
	$sql = new clsDB("SOMA");

	$merge = "MERGE INTO TABELA A
				      USING (SELECT '$codi_psv' as CODI_PSV, $custo as CUSTO, $margem as MARGEM, $margem_min as MARGEM_MIN FROM DUAL) B
				      ON (TABE_CTA = 3 AND A.CODI_PSV = B.CODI_PSV)
				      WHEN MATCHED THEN UPDATE SET  SITU_TAB = 'A'
				                                   ,TCUS_TAB = 1
				                                   ,CUST_TAB = B.CUSTO
				                                   ,BASI_TAB = B.CUSTO/(1-B.MARGEM/100)
				                                   ,PDES_TAB = ROUND(( (1/(100-B.MARGEM)) - (1/(100-B.MARGEM_MIN)) ) / (1/(100-B.MARGEM)) * 100,2)
				      WHEN NOT MATCHED THEN INSERT (TABE_CTA,CODI_PSV,BASI_TAB,DESC_TAB,ACRE_TAB,PDES_TAB,TCUS_TAB,CUST_TAB,MARG_TAB,QTDC_TAB
				                                   ,COMI_TAB,DUMANUT,SITU_TAB,PACR_TAB,DSCT_TAB,RIVC_TAB,MKP_TAB,ABAS_TAB,ADES_TAB,AACR_TAB
				                                   ,CICL_TAB,CHEK_TAB,QMIN_TAB,VLOH_TAB,DSOF_TAB,DMOF_TAB,VCDO_TAB,DINSERT,DTSYNCAPI) VALUES 
				                                   (3,B.CODI_PSV
				                                     ,B.CUSTO/(1-B.MARGEM/100),0,0,ROUND(( (1/(100-B.MARGEM)) - (1/(100-B.MARGEM_MIN)) ) / (1/(100-B.MARGEM)) * 100,2),1
				                                     ,B.CUSTO,B.MARGEM,0
				                                   ,0,SYSDATE,'A',30,NULL,NULL,NULL,0,0,0
				                                   ,NULL,NULL,NULL,NULL,NULL,NULL,NULL,SYSDATE,NULL)";

	//echo $merge."<br>";
	$res = $sql->execute($merge);

	if ($res) {
	    $sql->commit();
	    echo "OK";
	} 
	else {
	    echo "ERRO";
	}

	$merge = "MERGE INTO TABELA A
				      USING (SELECT '$codi_psv' as CODI_PSV, $custo as CUSTO, $margem as MARGEM, $margem_min as MARGEM_MIN FROM DUAL) B
				      ON (TABE_CTA = 4 AND A.CODI_PSV = B.CODI_PSV)
				      WHEN MATCHED THEN UPDATE SET  SITU_TAB = 'A'
				                                   ,TCUS_TAB = 1
				                                   ,CUST_TAB = B.CUSTO/0.95
				                                   ,BASI_TAB = (B.CUSTO/0.95)/(1-B.MARGEM/100)
				                                   ,PDES_TAB = ROUND(( (1/(100-B.MARGEM)) - (1/(100-B.MARGEM_MIN)) ) / (1/(100-B.MARGEM)) * 100,2)
				      WHEN NOT MATCHED THEN INSERT (TABE_CTA,CODI_PSV,BASI_TAB,DESC_TAB,ACRE_TAB,PDES_TAB,TCUS_TAB,CUST_TAB,MARG_TAB,QTDC_TAB
				                                   ,COMI_TAB,DUMANUT,SITU_TAB,PACR_TAB,DSCT_TAB,RIVC_TAB,MKP_TAB,ABAS_TAB,ADES_TAB,AACR_TAB
				                                   ,CICL_TAB,CHEK_TAB,QMIN_TAB,VLOH_TAB,DSOF_TAB,DMOF_TAB,VCDO_TAB,DINSERT,DTSYNCAPI) VALUES 
				                                   (4,B.CODI_PSV
				                                     ,(B.CUSTO/0.95)/(1-B.MARGEM/100),0,0,ROUND(( (1/(100-B.MARGEM)) - (1/(100-B.MARGEM_MIN)) ) / (1/(100-B.MARGEM)) * 100,2),1
				                                     ,B.CUSTO/0.95,B.MARGEM,0
				                                   ,0,SYSDATE,'A',30,NULL,NULL,NULL,0,0,0
				                                   ,NULL,NULL,NULL,NULL,NULL,NULL,NULL,SYSDATE,NULL)";

	//echo $merge."<br>";
	$res = $sql->execute($merge);

	if ($res) {
	    $sql->commit();
	    echo "OK";
	} 
	else {
	    echo "ERRO";
	}

	$merge = "MERGE INTO TABELA A
				      USING (SELECT '$codi_psv' as CODI_PSV, $custo as CUSTO, $margem as MARGEM, $margem_min as MARGEM_MIN FROM DUAL) B
				      ON (TABE_CTA = 5 AND A.CODI_PSV = B.CODI_PSV)
				      WHEN MATCHED THEN UPDATE SET  SITU_TAB = 'A'
				                                   ,TCUS_TAB = 1
				                                   ,CUST_TAB = B.CUSTO/0.95
				                                   ,BASI_TAB = (B.CUSTO/0.95)/(1-B.MARGEM/100)
				                                   ,PDES_TAB = ROUND(( (1/(100-B.MARGEM)) - (1/(100-B.MARGEM_MIN)) ) / (1/(100-B.MARGEM)) * 100,2)
				      WHEN NOT MATCHED THEN INSERT (TABE_CTA,CODI_PSV,BASI_TAB,DESC_TAB,ACRE_TAB,PDES_TAB,TCUS_TAB,CUST_TAB,MARG_TAB,QTDC_TAB
				                                   ,COMI_TAB,DUMANUT,SITU_TAB,PACR_TAB,DSCT_TAB,RIVC_TAB,MKP_TAB,ABAS_TAB,ADES_TAB,AACR_TAB
				                                   ,CICL_TAB,CHEK_TAB,QMIN_TAB,VLOH_TAB,DSOF_TAB,DMOF_TAB,VCDO_TAB,DINSERT,DTSYNCAPI) VALUES 
				                                   (5,B.CODI_PSV
				                                     ,(B.CUSTO/0.95)/(1-B.MARGEM/100),0,0,ROUND(( (1/(100-B.MARGEM)) - (1/(100-B.MARGEM_MIN)) ) / (1/(100-B.MARGEM)) * 100,2),1
				                                     ,B.CUSTO/0.95,B.MARGEM,0
				                                   ,0,SYSDATE,'A',30,NULL,NULL,NULL,0,0,0
				                                   ,NULL,NULL,NULL,NULL,NULL,NULL,NULL,SYSDATE,NULL)";

	$res = $sql->execute($merge);

	if ($res) {
	    $sql->commit();
	    echo "OK";
	} 
	else {
	    echo "ERRO";
	}

	$merge = "MERGE INTO TABELA A
				      USING (SELECT '$codi_psv' as CODI_PSV, $custo as CUSTO, $margem as MARGEM, $margem_min as MARGEM_MIN FROM DUAL) B
				      ON (TABE_CTA = 6 AND A.CODI_PSV = B.CODI_PSV)
				      WHEN MATCHED THEN UPDATE SET  SITU_TAB = 'A'
				                                   ,TCUS_TAB = 1
				                                   ,CUST_TAB = B.CUSTO/0.9
				                                   ,BASI_TAB = (B.CUSTO/0.9)/(1-B.MARGEM/100)
				                                   ,PDES_TAB = ROUND(( (1/(100-B.MARGEM)) - (1/(100-B.MARGEM_MIN)) ) / (1/(100-B.MARGEM)) * 100,2)
				      WHEN NOT MATCHED THEN INSERT (TABE_CTA,CODI_PSV,BASI_TAB,DESC_TAB,ACRE_TAB,PDES_TAB,TCUS_TAB,CUST_TAB,MARG_TAB,QTDC_TAB
				                                   ,COMI_TAB,DUMANUT,SITU_TAB,PACR_TAB,DSCT_TAB,RIVC_TAB,MKP_TAB,ABAS_TAB,ADES_TAB,AACR_TAB
				                                   ,CICL_TAB,CHEK_TAB,QMIN_TAB,VLOH_TAB,DSOF_TAB,DMOF_TAB,VCDO_TAB,DINSERT,DTSYNCAPI) VALUES 
				                                   (6,B.CODI_PSV
				                                     ,(B.CUSTO/0.9)/(1-B.MARGEM/100),0,0,ROUND(( (1/(100-B.MARGEM)) - (1/(100-B.MARGEM_MIN)) ) / (1/(100-B.MARGEM)) * 100,2),1
				                                     ,B.CUSTO/0.9,B.MARGEM,0
				                                   ,0,SYSDATE,'A',30,NULL,NULL,NULL,0,0,0
				                                   ,NULL,NULL,NULL,NULL,NULL,NULL,NULL,SYSDATE,NULL)";

	$res = $sql->execute($merge);

	if ($res) {
	    $sql->commit();
	    echo "OK";
	} 
	else {
	    echo "ERRO";
	}

}