<?php
include_once(__DIR__.'/../../includes.inc');

header('Content-Type: text/html; charset=utf-8');

$arrEmpresas = ["SOMA","SOMAR","CRISTALINA"];
//$arrEmpresas = ["SOMA"];

$erro = false;
foreach($arrEmpresas as $empresa)
{

    $sql = new clsDB($empresa);

    $delete =  "TRUNCATE TABLE SOMA_ESTOQUE";
    $res = $sql->execute($delete);
    $sql->commit();

    $delete =  "TRUNCATE TABLE SOMA_ESTOQUE_PV";
    $res = $sql->execute($delete);
    $sql->commit();

    $delete =  "TRUNCATE TABLE SOMA_ESTOQUE_PC";
    $res = $sql->execute($delete);
    $sql->commit();

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

    $insert = "INSERT INTO SOMA_ESTOQUE
              SELECT A.CODI_PSV
                      , (NVL ( (  SELECT SUM (SALD_CTR) SALD_CTR
                                     FROM TABLE (SALDO_INICIAL_TIPOEST ($codi_emp, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                    WHERE CODI_CTR = 1
                                 GROUP BY CODI_CTR),0)) AS EF
                      , (NVL ( (  SELECT SUM (SALD_CTR) SALD_CTR
                                     FROM TABLE (SALDO_INICIAL_TIPOEST ($codi_emp, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                    WHERE CODI_CTR = 2
                                 GROUP BY CODI_CTR),0)) AS PCNR
                      , (NVL ( (  SELECT SUM (SALD_CTR) SALD_CTR
                                     FROM TABLE (SALDO_INICIAL_TIPOEST ($codi_emp, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                    WHERE CODI_CTR = 3
                                 GROUP BY CODI_CTR),0)) 
                                 +
                                 (NVL ( (  SELECT SUM (SALD_CTR) SALD_CTR
                                     FROM TABLE (SALDO_INICIAL_TIPOEST (99, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                    WHERE CODI_CTR = 3
                                 GROUP BY CODI_CTR),0))
                                 AS PVNE
                      , (NVL ( (  SELECT SUM (SALD_CTR) SALD_CTR
                                     FROM TABLE (SALDO_INICIAL_TIPOEST ($codi_emp, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                    WHERE CODI_CTR = 5
                                 GROUP BY CODI_CTR),0))
                                 +
                                 (NVL ( (  SELECT SUM (SALD_CTR) SALD_CTR
                                     FROM TABLE (SALDO_INICIAL_TIPOEST (10, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                    WHERE CODI_CTR = 5
                                 GROUP BY CODI_CTR),0))
                                 +
                                 (NVL ( (  SELECT SUM (SALD_CTR) SALD_CTR
                                     FROM TABLE (SALDO_INICIAL_TIPOEST ($codi_emp, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                    WHERE CODI_CTR = 13
                                 GROUP BY CODI_CTR),0))
                                 +
                                 (NVL ( (  SELECT SUM (SALD_CTR) SALD_CTR
                                     FROM TABLE (SALDO_INICIAL_TIPOEST (10, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                    WHERE CODI_CTR = 13
                                 GROUP BY CODI_CTR),0))
                                 AS VF
                      , (NVL ( (  SELECT SUM (SALD_CTR) SALD_CTR
                                     FROM TABLE (SALDO_INICIAL_TIPOEST ($codi_emp, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                    WHERE CODI_CTR = 9
                                 GROUP BY CODI_CTR),0)) AS RDE
                      ,'A'
                      ,SYSDATE
            FROM PRODSERV A
            WHERE   A.SITU_PSV = 'A'
                AND A.CODI_GPR IN (1,2,5,6,17)
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
                                GROUP BY CODI_CTR),0)
                                + 
                                NVL ( ( SELECT SUM (SALD_CTR) SALD_CTR
                              FROM TABLE (SALDO_INICIAL_TIPOEST ($codi_emp, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                   WHERE CODI_CTR = 13
                                GROUP BY CODI_CTR),0) > 0
                      OR NVL ( ( SELECT SUM (SALD_CTR) SALD_CTR
                              FROM TABLE (SALDO_INICIAL_TIPOEST ($codi_emp, 1, A.CODI_PSV, SYSDATE, 'N', NULL, NULL))
                                   WHERE CODI_CTR = 9
                                GROUP BY CODI_CTR),0) > 0)
            ORDER BY A.CODI_PSV";

    $res = $sql->execute($insert);

    if ($res) {
        $sql->commit();
        echo "OK";
    } 
    else {
        $erro = true;
        echo "ERRO 1: $empresa"."<br>";
    }

    $insert = "INSERT INTO SOMA_ESTOQUE_PV
                SELECT 
                      P.CODI_EMP
                    , PI.CODI_PSV
                    , P.PEDI_PED
                    , P.SERI_PED
                FROM PEDIDO P
                    INNER JOIN IPEDIDO PI ON PI.CODI_EMP = P.CODI_EMP AND PI.PEDI_PED = P.PEDI_PED AND PI.SERI_PED = P.SERI_PED
                WHERE 1=1
                    AND PI.QTDE_IPE - NVL((SELECT SUM(QTDE_INO) FROM INOTA NI 
                            INNER JOIN NOTA N ON N.NPRE_NOT = NI.NPRE_NOT 
                            WHERE N.SITU_NOT  <> 9 
                                AND NI.EMPR_PED = PI.CODI_EMP 
                                AND NI.PEDI_PED = PI.PEDI_PED 
                                AND NI.SERI_PED = PI.SERI_PED 
                                AND NI.CODI_PSV = PI.CODI_PSV
                            ),0) > 0 
                    AND P.SITU_PED IN (0,1,5)
                ORDER BY PI.CODI_PSV,P.PEDI_PED";

    $res = $sql->execute($insert);

    if ($res) {
        $sql->commit();
        echo "OK";
    } 
    else {
        $erro = true;
        echo "ERRO 2: $empresa"."<br>";
    }

    $insert = "INSERT INTO SOMA_ESTOQUE_PC
                SELECT 
                      P.CODI_EMP
                    , PI.CODI_PSV
                    , P.NUME_PEC
                FROM PEDCOM P
                    INNER JOIN IPEDCOM PI ON PI.CODI_EMP = P.CODI_EMP AND PI.NUME_PEC = P.NUME_PEC 
                WHERE PI.QTDP_IPC > PI.QTDR_IPC
                    AND P.DCAN_PEC IS NULL
                ORDER BY P.NUME_PEC";

    $res = $sql->execute($insert);

    if ($res) {
        $sql->commit();
        echo "OK";
    } 
    else {
        $erro = true;
        echo "ERRO 3: $empresa"."<br>";
    }

    if($empresa === "SOMA")
    {
        $update = "UPDATE SOMA_ESTOQUE SET VF = VF - 3500 WHERE CODI_PSV = '0001335'";    // CURYOM 550 EC
        $sql->execute($update);
        $update = "UPDATE SOMA_ESTOQUE SET VF = VF - 4000 WHERE CODI_PSV = '0122274'";    // ELATUS
        $sql->execute($update);
        $update = "UPDATE SOMA_ESTOQUE SET VF = VF - 4000 WHERE CODI_PSV = '0001337'";    // ENGEO PLENO
        $sql->execute($update);
        $update = "UPDATE SOMA_ESTOQUE SET VF = VF - 3200 WHERE CODI_PSV = '0001213'";    // PRIORI XTRA
        $sql->execute($update);

        $sql->commit();
    }  

    $sql->close();

}

if ($erro) {
    $to      = "ti@somaagricola.com.br" . "\r\n";
    $subject = "Erro no Script: atualizaEstoque.php";
    $subject = mb_encode_mimeheader($subject,"UTF-8");
    $message = "Erro no Script atualizaEstoque.php";
    $headers  = "From: servidor@somaagricola.com.br\r\n";
    //$headers .= "Cc: dholer@gmail.com\r\n";
    $headers .= "Reply-To: nao_responder@somaagricola.com.br\r\n";
    $headers .= "X-Mailer: PHP/".phpversion();
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    //$headers .= "Subject: =?UTF-8?B?". base64_encode($subject)."?=\r\n";



    global $g_error_mail;
    if (! mail($to, $subject, $message, $headers)) {
      //echo 'Houve alguma falha...<br>';
    } else {
      //echo 'Sem problema...<br>';
      //echo $msg;
    }

} 
