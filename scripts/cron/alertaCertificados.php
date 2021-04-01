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

.bgYellow {
    background-color: gold;
}

.bgRed {
    background-color: crimson;
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
            <th class='header textCenter'>Descrição</th>
            <th class='header textCenter'>Tipo</th>
            <th class='header textCenter'>Vencimento</th>
        </tr>
    </thead>
    <tbody>

<?php

$select =  "SELECT
                 A.CODI_EMP
                ,A.DESCR
                ,A.TIPO
                ,TO_CHAR(A.VENCIMENTO, 'DD/MM/YYYY') AS VENCIMENTO
                ,A.VENCIMENTO - SYSDATE AS DIAS
            FROM SOMA_CERTIFICADO A
            WHERE A.SITU = 'A'
              AND A.VENCIMENTO <= SYSDATE + 30
            ORDER BY A.VENCIMENTO ASC";

    //die($select);

    $sql = new clsDB("SOMA");
    $arr = $sql->getDados($select);

    $seq = 1;
    foreach ($arr as $row)
    {
        $trStyle = $seq%2 == 0 ? "parentEven" : "parentOdd";
        if($row["DIAS"] <= 10)
        {
            $trStyle = "bgYellow";
        }
        if($row["DIAS"] < 0)
        {
            $trStyle = "bgRed";
        }

        ?>
            <tr class='<?php echo $trStyle ?>'>
                <td class='textCenter'><?php echo $row["CODI_EMP"] ?></td>
                <td class='textCenter'><?php echo $row["DESCR"] ?></td>
                <td class='textCenter'><?php echo $row["TIPO"] ?></td>
                <td class='textCenter'><?php echo $row["VENCIMENTO"] ?></td>
            </tr>
        <?php
        $seq++;
    }

?>

    </tbody>
</table>

<?php

$to  = 'ti@somaagricola.com.br' . "\r\n";
if($seq > 1)
{
    $msg = ob_get_contents();
    $to  = 'alertacertificados@somaagricola.com.br' . "\r\n";
}
else
{
    $msg = "Script alertaCertificados foi executado pelo cron";
}

ob_end_clean();

//$to      = 'ti@somaagricola.com.br' . "\r\n";
$subject = 'Alerta de vencimento de Certificados';
$subject = mb_encode_mimeheader($subject,"UTF-8");
$message = $msg;
$headers  = "From: servidor@somaagricola.com.br\r\n";
$headers .= "Cc: ti@somaagricola.com.br\r\n";
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
  echo $msg;
}
