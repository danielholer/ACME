<?php
ob_start();
include_once(__DIR__.'/../../includes.inc');

header('Content-Type: text/html; charset=utf-8');

//$consultar = filter_input(INPUT_POST, 'btnConsultar');

?>

<style type="text/css">
table{
    border: 1px solid white;
    border-collapse: collapse;
    /*margin-top: 50px;*/
}

th, tr, td{
    border: 1px solid white;
    padding: 5px;
}

.parentEven {
    background-color: #c4c4c4;
}

.parentOdd {
    background-color: #e4e4e4;
}

.childEven {
    font-family: Times;
    background-color: #EEE8AA;
}

.childOdd {
    font-family: Times;
    background-color: #FEF8CA;
}

.negativo {
    color:#c50000; 
    /* background-color:pink; */
}

.textLeft {
    text-align: left;
}

.textRight {
    text-align: right;
}

.textCenter {
    text-align: center;
}

.middle {
    vertical-align: middle;
}

.header {
    background-color: #286090;
    color: white;
    vertical-align: middle;
}
</style>

<table class='table table-bordered treetable'>
    <thead>
        <tr>
            <th class='header textCenter'>Seq</th>
            <th class='header textCenter'>Empresa</th>
            <th class='header textCenter'>Pedido</th>
            <th class='header textCenter'>NF</th>
            <th class='header textCenter'>Emissão</th>
            <th class='header textCenter'>Cliente</th>
            <th class='header textCenter'>Cod. Op.</th>
            <th class='header textCenter'>Desc. Operação</th>
            <th class='header textCenter'>Acréscimo</th>
            <th class='header textCenter'>Desconto</th>
            <th class='header textCenter'>Cond. Pagamento</th>
        </tr>
    </thead>
    <tbody>

<?php

$select =  "SELECT
                 A.CODI_EMP
                ,A.PEDI_PED
                ,A.NOTA_NOT
                ,TO_CHAR(A.DEMI_NOT,'DD/MM/YYYY') AS DEMI_NOT
                ,D.RAZA_TRA
                ,E.CODI_TOP
                ,E.DESC_TOP
                ,TO_CHAR(A.TPRO_NOT,'FM999G999G990D00','nls_numeric_characters='',.''') AS TPRO_NOT
                ,TO_CHAR(A.ACRE_NOT,'FM999G999G990D00','nls_numeric_characters='',.''') AS ACRE_NOT
                ,TO_CHAR(A.TOTA_NOT,'FM999G999G990D00','nls_numeric_characters='',.''') AS TOTA_NOT
                ,TO_CHAR(C.VLOR_REC,'FM999G999G990D00','nls_numeric_characters='',.''') AS VLOR_REC
                ,TO_CHAR(C.DPON_REC,'FM999G999G990D00','nls_numeric_characters='',.''') AS DPON_REC
                ,C.COND_CON
            FROM NOTA A
            INNER JOIN NOTACRC B ON B.CODI_EMP = A.CODI_EMP AND B.NDOC_NOC = A.NOTA_NOT AND B.SDOC_NOC = A.SERI_NOT
            INNER JOIN RECEBER C ON C.CTRL_CBR = B.CTRL_CBR
            INNER JOIN TRANSAC D ON D.CODI_TRA = A.CODI_TRA
            INNER JOIN TIPOOPER E ON E.CODI_TOP = A.CODI_TOP
            WHERE A.DEMI_NOT >= TO_DATE('01/10/2017','DD/MM/YYYY')
                AND (
			(ACRE_NOT = 0 OR ACRE_NOT IS NULL) AND DPON_REC > 0
                    OR  ACRE_NOT > 0 AND DPON_REC = 0
                    )
                AND A.SITU_NOT <> 9
            ORDER BY A.NOTA_NOT";

    //echo $select;

    $sql1 = new clsDB("SOMA");
    $arrSoma = $sql1->getDados($select);
    $sql2 = new clsDB("SOMAR");
    $arrSomar = $sql2->getDados($select);
    $sql3 = new clsDB("CRISTALINA");
    $arrCristalina = $sql3->getDados($select);

    $arr = array_merge($arrSoma,$arrSomar);
    $arr = array_merge($arr,$arrCristalina);

    $seq = 1;
    foreach ($arr as $row)
    {
        $trStyle = $seq%2 == 0 ? "parentEven" : "parentOdd";

        ?>
            <tr class='<?php echo $trStyle ?>'>
                <td class='textCenter' ><?php echo $seq ?></td>
                <td class='textCenter'><?php echo $row["CODI_EMP"] ?></td>
                <td class='textCenter'><?php echo $row["PEDI_PED"] ?></td>
                <td class='textCenter'><?php echo $row["NOTA_NOT"] ?></td>
                <td class='textCenter'><?php echo $row["DEMI_NOT"] ?></td>
                <td class='textCenter'><?php echo $row["RAZA_TRA"] ?></td>
                <td class='textCenter'><?php echo $row["CODI_TOP"] ?></td>
                <td class='textCenter'><?php echo $row["DESC_TOP"] ?></td>
                <td class='textCenter'><?php echo $row["ACRE_NOT"] ?></td>
                <td class='textCenter'><?php echo $row["DPON_REC"] ?></td>
                <td class='textCenter'><?php echo $row["COND_CON"] ?></td>
            </tr>
        <?php
        $seq++;
    }

?>

    </tbody>
</table>

<?php

if($seq > 1)
{
    $msg = ob_get_contents();
}
else
{
    $msg = "Script verificaErroPontualidade foi executado pelo cron";
}

ob_end_clean();

$to      = 'ti@somaagricola.com.br' . "\r\n";
$subject = 'Verificação Desonto Pontualidade';
$subject = mb_encode_mimeheader($subject,"UTF-8");
$message = $msg;
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
