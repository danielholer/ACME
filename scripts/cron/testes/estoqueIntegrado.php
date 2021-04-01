<?php
include_once(__DIR__.'/../../../includes.inc');

header('Content-Type: text/html; charset=utf-8');

$arrEmpresas = ["SOMA","SOMAR","CRISTALINA"];

//estoque inegrado
$arrEstoqueSOMA = getEstoques("SOMA");
$arrEstoqueSOMAR = getEstoques("SOMAR");
$arrEstoqueCRISTALINA = getEstoques("CRISTALINA");

$arrEstoques = array_merge_recursive($arrEstoqueSOMA, $arrEstoqueSOMAR, $arrEstoqueCRISTALINA);
ksort($arrEstoques);


//estoque integrado 2
$arrEstoqueSOMA = Estoque::getEstoquesFamilias("SOMA");
$arrEstoqueSOMAR = Estoque::getEstoquesFamilias("SOMAR");
$arrEstoqueCRISTALINA = Estoque::getEstoquesFamilias("CRISTALINA");

$arrEstoques2 = array_merge_recursive($arrEstoqueSOMA, $arrEstoqueSOMAR, $arrEstoqueCRISTALINA);
ksort($arrEstoques2);

if($arrEstoques == $arrEstoques2)
{
  echo "OK";
}
else
{
  echo "ERRO";
}

function getEstoques($empresa)
{
    switch ($empresa)
    {
        case 'SOMA':
            $codi_emp = 1;
            break;
        case 'SOMAR':
            $codi_emp = 50;
            break;
        case 'CRISTALINA':
            $codi_emp = 60;
            break;
    }

    $select = "SELECT A.MARC_PRO
                      ,SUM (NVL ( (  SELECT SUM (SALD_CTR) SALD_CTR
                                     FROM TABLE (SALDO_INICIAL_TIPOEST ($codi_emp, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                    WHERE CODI_CTR = 1
                                 GROUP BY CODI_CTR),0)) AS EF
                      ,SUM (NVL ( (  SELECT SUM (SALD_CTR) SALD_CTR
                                     FROM TABLE (SALDO_INICIAL_TIPOEST ($codi_emp, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                    WHERE CODI_CTR = 2
                                 GROUP BY CODI_CTR),0)) AS PCNR
                      ,SUM (NVL ( (  SELECT SUM (SALD_CTR) SALD_CTR
                                     FROM TABLE (SALDO_INICIAL_TIPOEST ($codi_emp, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                    WHERE CODI_CTR = 3
                                 GROUP BY CODI_CTR),0)) 
                                 +
                                SUM (NVL ( (  SELECT SUM (SALD_CTR) SALD_CTR
                                     FROM TABLE (SALDO_INICIAL_TIPOEST (99, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                    WHERE CODI_CTR = 3
                                 GROUP BY CODI_CTR),0)) AS PVNE
                      ,SUM (NVL ( (  SELECT SUM (SALD_CTR) SALD_CTR
                                     FROM TABLE (SALDO_INICIAL_TIPOEST ($codi_emp, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                    WHERE CODI_CTR = 5
                                 GROUP BY CODI_CTR),0)) AS VF
                      ,SUM (NVL ( (  SELECT SUM (SALD_CTR) SALD_CTR
                                     FROM TABLE (SALDO_INICIAL_TIPOEST ($codi_emp, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                    WHERE CODI_CTR = 9
                                 GROUP BY CODI_CTR),0)) AS RDE
            FROM PRODUTO A INNER JOIN PRODSERV B ON B.CODI_PSV = A.CODI_PSV
            WHERE     B.SITU_PSV = 'A'
                AND B.CODI_GPR IN (1,2,5,6,17)
                AND ( NVL( ( SELECT SUM (SALD_CTR) SALD_CTR
                              FROM TABLE (SALDO_INICIAL_TIPOEST ($codi_emp, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                               WHERE CODI_CTR = 1
                            GROUP BY CODI_CTR),0) > 0
                      OR NVL ( ( SELECT SUM (SALD_CTR) SALD_CTR
                              FROM TABLE (SALDO_INICIAL_TIPOEST ($codi_emp, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                   WHERE CODI_CTR = 2
                                GROUP BY CODI_CTR),0) > 0
                      OR NVL ( ( SELECT SUM (SALD_CTR) SALD_CTR
                              FROM TABLE (SALDO_INICIAL_TIPOEST ($codi_emp, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                   WHERE CODI_CTR = 3
                                GROUP BY CODI_CTR),0)
                                +
                                NVL ( (  SELECT SUM (SALD_CTR) SALD_CTR
                                     FROM TABLE (SALDO_INICIAL_TIPOEST (99, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                    WHERE CODI_CTR = 3
                                 GROUP BY CODI_CTR),0) > 0
                      OR NVL ( ( SELECT SUM (SALD_CTR) SALD_CTR
                              FROM TABLE (SALDO_INICIAL_TIPOEST ($codi_emp, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                   WHERE CODI_CTR = 5
                                GROUP BY CODI_CTR),0) > 0
                      OR NVL ( ( SELECT SUM (SALD_CTR) SALD_CTR
                              FROM TABLE (SALDO_INICIAL_TIPOEST ($codi_emp, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                   WHERE CODI_CTR = 9
                                GROUP BY CODI_CTR),0) > 0)
            /* AND ROWNUM <= 5 */
            GROUP BY A.MARC_PRO
            ORDER BY A.MARC_PRO";

        //echo $select;
        $sql = new clsDB($empresa);
        $arr = [];
        foreach ($sql->getDados($select) as $row)
        {
            if ($empresa === "SOMA")
            {
                if ($row["MARC_PRO"] === "CURYOM 550 EC") { $row["VF"] -= 3500 ;}
                if ($row["MARC_PRO"] === "ELATUS") { $row["VF"] -= 4000 ;}
                if ($row["MARC_PRO"] === "ENGEO PLENO") { $row["VF"] -= 4000 ;}
                if ($row["MARC_PRO"] === "PRIORI XTRA") { $row["VF"] -= 3200 ;}
            }

            $EC = $row["EF"] + $row["RDE"] + $row["PCNR"] - $row["PVNE"] - $row["VF"];
            $arr[$row["MARC_PRO"]][$empresa] = ["EC" => $EC, 
                                                "EF" => $row["EF"],
                                                "PCNR" => $row["PCNR"],
                                                "PVNE" => $row["PVNE"],
                                                "VF" => $row["VF"],
                                                "RDE" => $row["RDE"]];
        }
        
        $sql->close();
        
        return $arr;
}

