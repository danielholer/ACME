<?php

include_once('/var/www/html/intranet/includes.inc');

header('Content-Type: text/html; charset=utf-8');

$sql = new clsDB();
$k = 10;

for($i=1; $i<$k; $i++)
{
    $j = $i + 1;
    $select =  "SELECT
            r.ctrl_cbr
           /* ,r.vlor_rec */
           /* ,NVL((SELECT SUM(vlor_bai) FROM crcbaixa c WHERE c.ctrl_rec = r.ctrl_rec AND situ_bai = 'N' AND ctrl_lan IS NOT NULL GROUP BY c.ctrl_rec),0) NORMAL */
           /* ,NVL((SELECT SUM(VLOR_BAI) FROM crcbaixa c WHERE c.ctrl_rec = r.ctrl_rec AND situ_bai = 'E' AND ctrl_lan IS NOT NULL GROUP BY c.ctrl_rec),0) ESTORNO */
           /* ,  NVL((SELECT SUM(vlor_bai) FROM crcbaixa c WHERE c.ctrl_rec = r.ctrl_rec AND ctrl_lan IS NOT NULL AND situ_bai = 'N' GROUP BY c.ctrl_rec),0)
            - NVL((SELECT SUM(vlor_bai) FROM crcbaixa c WHERE c.ctrl_rec = r.ctrl_rec AND ctrl_lan IS NOT NULL AND situ_bai = 'E' GROUP BY c.ctrl_rec),0) AS BAIXA */
           ,R.VLOR_REC - (NVL((SELECT SUM(VLOR_BAI) FROM crcbaixa c WHERE c.ctrl_rec = r.ctrl_rec AND ctrl_lan IS NOT NULL AND situ_bai = 'N' GROUP BY C.CTRL_REC),0)
                        - NVL((SELECT SUM(VLOR_BAI) FROM crcbaixa c WHERE c.ctrl_rec = r.ctrl_rec AND ctrl_lan IS NOT NULL AND situ_bai = 'E' GROUP BY C.CTRL_REC),0)) AS SALDO
        FROM           receber r
            INNER JOIN cabrec c ON c.ctrl_cbr = r.ctrl_cbr
        WHERE   r.situ_rec = 'A'
            AND c.codi_tdo <> 103
            AND c.codi_tdo <> 106
            AND c.codi_emp = 1
            AND (
              r.vlor_rec > (NVL((SELECT SUM(vlor_bai) FROM crcbaixa c WHERE c.ctrl_rec = r.ctrl_rec AND situ_bai = 'N' AND ctrl_lan IS NOT NULL GROUP BY c.ctrl_rec),0) 
                          - NVL((SELECT SUM(vlor_bai) FROM crcbaixa c WHERE c.ctrl_rec = r.ctrl_rec AND situ_bai = 'E' AND ctrl_lan IS NOT NULL GROUP BY c.ctrl_rec),0))
              )
            AND r.venc_rec >= TO_DATE('01/$i/2017 00:00:00','DD/MM/YYYY HH24:MI:SS') 
            AND r.venc_rec <  TO_DATE('01/$j/2017 00:00:00','DD/MM/YYYY HH24:MI:SS')
    ";

    foreach ($sql->getDados($select) as $row)
    {
        $ctrlCbr = $row["CTRL_CBR"];
        $saldo = $row["SALDO"];
        
        $select2 = "SELECT 
                distinct t.raza_tra
            FROM           nota n 
                INNER JOIN transac t ON t.codi_tra = n.codi_tra  
                INNER JOIN notacrc nc ON nc.ndoc_noc = n.nota_not and nc.sdoc_noc = n.seri_not
                INNER JOIN receber r ON r.ctrl_cbr = nc.ctrl_cbr
            WHERE r.ctrl_cbr = $ctrlCbr
            ";
        
        foreach ($sql->getDados($select2) as $row2)
        {
            $cliente = $row2["RAZA_TRA"];
        }
        
        if(isset($arr[$cliente][$i]))
        {
            $arr[$cliente][$i] += $saldo;
        }
        else
        {
            $arr[$cliente][$i] = $saldo;
        }

    }

    //sort($arr);
}

ksort($arr);

echo "<!DOCTYPE html>
        <html>
            <head>
                <style>
                    table, th, td {
                        border: 1px solid black;
                        border-collapse: collapse;
                    }
                </style>
            </head>
        ";

echo "<body>";
echo "<table>";
echo "<tr>";
echo "<th>Cliente</th>";
for($i=1; $i<$k; $i++)
{
    echo "<th>$i</th>";
}
echo "</tr>";

foreach($arr as $cliente=>$mes)
{
    echo "<tr>";
    echo "<td>$cliente</td>";
    
    for($i=1; $i<10; $i++)
    {
        $saldo = isset($mes["$i"]) ? str_replace(".",",",$mes["$i"]) : "-";
        echo "<td>$saldo</td>";
    }
    echo "</tr>";
}
echo "</table>";
echo "</body>";
echo "</html>";