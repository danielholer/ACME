<?php
include_once(__DIR__.'/../../includes.inc');

header('Content-Type: text/html; charset=utf-8');

$empresa = 1;
$tabela = 5;
$sql = new clsDB("SOMA");
$date = getdate();

$select = "SELECT
                      B.MARC_PRO
                    , MAX(C.CUST_TAB) AS CUST_TAB
                    , SUM(NVL((SELECT
                        SUM (SALD_CTR) SALD_CTR
                    	FROM TABLE (SALDO_INICIAL_TIPOEST (
                                    1,
                                    1,
                                    A.CODI_PSV,
                                    SYSDATE,
                                    'N',
                                    NULL,
                                    NULL
                                )
                            )
                    	WHERE CODI_CTR = 1
                    	GROUP BY CODI_CTR),0)) AS EF
                FROM PRODSERV A
                INNER JOIN PRODUTO B ON B.CODI_PSV = A.CODI_PSV
                INNER JOIN TABELA C ON C.CODI_PSV = A.CODI_PSV AND TABE_CTA = $tabela AND SITU_TAB = 'A'
                WHERE A.SITU_PSV = 'A'
                	AND A.CODI_GPR IN (1,2,5,17)
                	/* AND A.DESC_PSV LIKE 'ATMO%' */
                GROUP BY B.MARC_PRO
                ORDER BY B.MARC_PRO";

$arrProdutos = $sql->getDados($select);
//var_dump($arrProdutos);

foreach ($arrProdutos as $arrProduto)
{
	$produto = $arrProduto["MARC_PRO"];
	$qtde = $arrProduto["EF"];
	$select = "SELECT
	                      D.MARC_PRO 
	                    , TO_CHAR(A.DEMI_NFE,'DD/MM/YYYY') DEMI_NFE
	                    , B.VLIQ_INF VLIQ_INF
	                    , SUM(B.QUAN_INF) AS QUAN_INF
	                    , TO_CHAR(H.DVEN_PAG,'DD/MM/YYYY') DVEN_PAG
	                    , H.DVEN_PAG - (SELECT DTBA_CTA FROM CABTAB WHERE TABE_CTA = $tabela) AS DIF
	                FROM NFENTRA A
	                    INNER JOIN INFENTRA B ON B.CODI_EMP = A.CODI_EMP AND B.CODI_TRA = A.CODI_TRA AND B.NUME_NFE = A.NUME_NFE AND B.SERI_NFE = A.SERI_NFE
	                    INNER JOIN PRODSERV C ON C.CODI_PSV = B.CODI_PSV
	                    INNER JOIN PRODUTO D ON D.CODI_PSV = C.CODI_PSV
	                    INNER JOIN FUNCAOTOPER E ON E.CODI_TOP = A.CODI_TOP
	                    INNER JOIN TRANSAC F ON F.CODI_TRA = A.CODI_TRA
	                    INNER JOIN NOTACPG G ON G.CODI_EMP = A.CODI_EMP AND G.CODI_TRA = A.CODI_TRA AND G.NDOC_NCP = A.NUME_NFE AND G.SDOC_NCP = A.SERI_NFE
	                    INNER JOIN PAGAR H ON H.CTRL_CPG = G.CTRL_CPG
	                    INNER JOIN SOMA_FABRICANTE I ON F.RAZA_TRA LIKE '%' || I.NOME || '%' AND SITU = 'A' AND GRUPO = 1
	                WHERE A.CODI_EMP = 1
	                    AND E.CODI_PTO = 1
	                    AND C.CODI_GPR IN (1,2,5,17)
	                    AND I.SITU = 'A'
	                    AND D.MARC_PRO = '$produto'
	                    AND A.DEMI_NFE > SYSDATE - 365
	                GROUP BY D.MARC_PRO, A.DEMI_NFE, B.VLIQ_INF, H.DVEN_PAG
	                ORDER BY A.DEMI_NFE DESC";

	$arrCustos = $sql->getDados($select);

	$custo = 0;
	$div = 0;
	foreach ($arrCustos as $arrCusto)
	{
		$qtdeNF = $arrCusto["QUAN_INF"];
		$juros = $arrCusto["DIF"] > 0 ? 1.3 : 1.6;
		$mult = ($qtde - $qtdeNF > 0 or $qtde == 0) ? $qtdeNF : $qtde;
		//echo "qtde -> $qtde <br> qtdeNF -> $qtdeNF <br>";
		//echo "mult -> $mult <br>";
		$div += $mult;
		$custo += $arrCusto["VLIQ_INF"] * (1-(($arrCusto["DIF"]/30)*$juros/100)) * $mult;

		$qtde -= $mult;

		if($qtde <= 0)
		{
			break;
		}
	}

	if($custo)
	{
		$custo = $custo / $div;
	}

	echo $produto." -> ".round($arrProduto["CUST_TAB"],2)." -> ".round($custo,2)."<br>";
	
}

/*
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
*/

