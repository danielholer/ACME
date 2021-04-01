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
            <th class='header textCenter'>Empresa</th>
            <th class='header textCenter'>CODI_PSV</th>
            <th class='header textCenter'>DESC_PSV</th>
            <th class='header textCenter'>MARC_PRO</th>
        </tr>
    </thead>
    <tbody>

<?php

$select =  "SELECT 
                 A.CODI_PSV
                ,A.DESC_PSV
                ,B.MARC_PRO
            FROM PRODSERV A
                INNER JOIN PRODUTO B ON B.CODI_PSV = A.CODI_PSV
            WHERE CODI_GPR IN (1,2,5,6,17)
                AND SITU_PSV = 'A'
                AND
                (               A.DESC_PSV NOT LIKE B.MARC_PRO || '%' 
                    AND REPLACE(A.DESC_PSV,'MANGANES','MN') NOT LIKE B.MARC_PRO || '%'
                    AND REPLACE(A.DESC_PSV,'MAGNESIO','MG') NOT LIKE B.MARC_PRO || '%'
                    AND REPLACE(A.DESC_PSV,'FERTILIZANTE','FERT') NOT LIKE B.MARC_PRO || '%'
                    AND REPLACE(A.DESC_PSV,'FERT ','') NOT LIKE B.MARC_PRO || '%'
                    AND REPLACE(A.DESC_PSV,'FERTILIZANTE ','') NOT LIKE B.MARC_PRO || '%'
                    AND REPLACE(A.DESC_PSV,'FERTILIZANTE UREIA ','') NOT LIKE B.MARC_PRO || '%'
                    AND REPLACE(A.DESC_PSV,'INOCULANTE','INOC') NOT LIKE B.MARC_PRO || '%'
                    AND REPLACE(A.DESC_PSV,'INOCULANTE ','') NOT LIKE B.MARC_PRO || '%'
                    AND REPLACE(A.DESC_PSV,'MONOAMONICO','MON') NOT LIKE B.MARC_PRO || '%'
                    AND REPLACE(A.DESC_PSV,'ESTIRPE SEMIA ','') NOT LIKE B.MARC_PRO || '%'
                    AND REPLACE(A.DESC_PSV,'SOLUCAO','SOL') NOT LIKE B.MARC_PRO || '%'
                    AND REPLACE(A.DESC_PSV,'PLATINUM','PLAT') NOT LIKE REPLACE(B.MARC_PRO,'IBC','') || '%'
                    AND A.DESC_PSV NOT LIKE REPLACE(B.MARC_PRO,'IBC','') || '%'
                )
                ORDER BY A.DESC_PSV";

    //echo $select;

    $sql1 = new clsDB("SOMA");
    $arrSoma = $sql1->getDados($select);
    $sql2 = new clsDB("SOMAR");
    $arrSomar = $sql2->getDados($select);
    $sql3 = new clsDB("CRISTALINA");
    $arrCristalina = $sql3->getDados($select);

    $seq = 1;
    foreach ($arrSoma as $row)
    {
        $trStyle = $seq%2 == 0 ? "parentEven" : "parentOdd";

        ?>
            <tr class='<?php echo $trStyle ?>'>
                <td class='textCenter'><?php echo "SOMA" ?></td>
                <td class='textCenter'><?php echo $row["CODI_PSV"] ?></td>
                <td class='textCenter'><?php echo $row["DESC_PSV"] ?></td>
                <td class='textCenter'><?php echo $row["MARC_PRO"] ?></td>
            </tr>
        <?php
        $seq++;
    }
    foreach ($arrSomar as $row)
    {
        $trStyle = $seq%2 == 0 ? "parentEven" : "parentOdd";

        ?>
            <tr class='<?php echo $trStyle ?>'>
                <td class='textCenter'><?php echo "SOMAR" ?></td>
                <td class='textCenter'><?php echo $row["CODI_PSV"] ?></td>
                <td class='textCenter'><?php echo $row["DESC_PSV"] ?></td>
                <td class='textCenter'><?php echo $row["MARC_PRO"] ?></td>
            </tr>
        <?php
        $seq++;
    }
    foreach ($arrCristalina as $row)
    {
        $trStyle = $seq%2 == 0 ? "parentEven" : "parentOdd";

        ?>
            <tr class='<?php echo $trStyle ?>'>
                <td class='textCenter'><?php echo "CRISTALINA" ?></td>
                <td class='textCenter'><?php echo $row["CODI_PSV"] ?></td>
                <td class='textCenter'><?php echo $row["DESC_PSV"] ?></td>
                <td class='textCenter'><?php echo $row["MARC_PRO"] ?></td>
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
    $msg = "Script verificaNomeProdutos foi executado pelo cron";
}

ob_end_clean();

$to      = 'ti@somaagricola.com.br' . "\r\n";
$subject = 'Verificação Nomes Produtos';
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
