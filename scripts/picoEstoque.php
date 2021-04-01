<?php

//include_once('/var/www/html/intranet/classes/clsProduto.php');
include_once('/var/www/html/intranet/includes.inc');
//include_once('./includes/includes.php');

header('Content-Type: text/html; charset=utf-8');

$status = 'A';
$arrGrupos = [1,2,5,17];
$vincular = false;
$calcular = ['EF'];
$sql = new clsDB();

    $select =  "select 
                    a.codi_psv
                   ,a.desc_psv
                from prodserv a
                    inner join produto b on b.codi_psv = a.codi_psv
                    inner join transac c on c.codi_tra = b.codi_tra
                where a.situ_psv = 'A'
                    and a.codi_gpr in (1,2,5,17)
                    /* and a.codi_psv = '0001112' */
                    and c.raza_tra like 'SYNG%'                    
                ";

    foreach ($sql->getDados($select) as $row)
    {
        $produto = new Produto($row['CODI_PSV'], $row['DESC_PSV']);
        $produto->setCustoTabela();
        $arrProdutosSyngenta[] = $produto;
    }

    $select =  "select 
                    a.codi_psv
                   ,a.desc_psv
                from prodserv a
                    inner join produto b on b.codi_psv = a.codi_psv
                    inner join transac c on c.codi_tra = b.codi_tra
                where a.situ_psv = 'A'
                    and a.codi_gpr in (1,2,5,17)
                    /* and a.codi_psv = '0001317' */
                    and c.raza_tra not like 'SYNG%'                    
                ";

    foreach ($sql->getDados($select) as $row)
    {
        $produto = new Produto($row['CODI_PSV'], $row['DESC_PSV']);
        $produto->setCustoTabela();
        $arrProdutosNaoSyngenta[] = $produto;
    }
    
    
    $sql->close();

$maiorSyngenta = 0;
$maiorNaoSyngenta = 0;
$iSyngenta = 0;
$iNaoSyngenta = 0;
for($i=0; $i<365; $i+=15)
{
    
    $totalSyngenta = 0;
    foreach($arrProdutosSyngenta as $produto)
    {
        $produto->calculaEstoque("EF", "to_date('01/06/2016','DD/MM/YYYY') + $i");
        //echo $i." - ".$produto->getCodi()." - ".$produto->getCustoTabela()." - ".$produto->getEF()." - ".$produto->getCustoTabela() * $produto->getEF()."<br>";
        $totalSyngenta += $produto->getCustoTabela() * $produto->getEF();
    }

    $totalNaoSyngenta = 0;
    foreach($arrProdutosNaoSyngenta as $produto)
    {
        $produto->calculaEstoque("EF", "to_date('01/06/2016','DD/MM/YYYY') + $i");
        //echo $i." - ".$produto->getCodi()." - ".$produto->getCustoTabela()." - ".$produto->getEF()." - ".$produto->getCustoTabela() * $produto->getEF()."<br>";
        $totalNaoSyngenta += $produto->getCustoTabela() * $produto->getEF();
    }
    
    if($totalSyngenta > $maiorSyngenta)
    {
        $maiorSyngenta = $totalSyngenta;
        $iSyngenta = $i;
    }

    if($totalNaoSyngenta > $maiorNaoSyngenta)
    {
        $maiorNaoSyngenta = $totalNaoSyngenta;
        $iNaoSyngenta = $i;
    }
    
    echo $i." - ".$totalSyngenta." - ".$totalNaoSyngenta."<br>";
}

echo "<br><br>Maior Syngenta<br>";
echo $iSyngenta." - ".$maiorSyngenta."<br>";
echo "<br><br>Maior Não Syngenta<br>";
echo $iNaoSyngenta." - ".$maiorNaoSyngenta."<br>";



/*
$arrProdutos = Produto::getProdutos($status, $arrGrupos, $vincular, $calcular);

$dataHora = new clsDataHora();


echo "<div class='panel panel-primary'>";
echo "  <div class='panel-heading' style='background-color: #286090; border-color: #286090'>";
echo "    <div class='panel-title textCenter'>Pico Estoque</div>";
echo "    <div class='textCenter' style='font-size: small;'>".$dataHora->getDataHora()."</div>";
echo "  </div>";
echo "</div>";

    
echo "<table class='table table-bordered treetable'>";
echo "<thead>";
echo "  <tr>";
echo "    <th class='header textCenter' style='vertical-align: middle;'>Seq</th>";
echo "    <th class='header textCenter'>Produto</th>";
echo "    <th class='header textCenter'>Estoque Calculado</th>";
echo "    <th class='header textCenter'>EF(+)</th>";
echo "    <th class='header textCenter'>PCNR(+)</th>";
echo "    <th class='header textCenter'>PVNE(-)</th>";
echo "    <th class='header textCenter'>VF(-)</th>";
echo "    <th class='header textCenter'>RDE(+)</th>";
echo "  </tr>";
echo "</thead>";
echo "<tbody>";

$date = date_date;
$dataHora->setDate('01/01/2016');
for($i=1; $i<=365; $i++)
{
    
    echo $dataHora->getData();
    
    //echo getdate()."<br>";
    
}
/*
$seq = 1;
$total = 0;
foreach($arrProdutos as $produto)
{
    $EF = $produto->getEF();
    
    $treetable = '';
    $child = '';
    $count = 1;
    $parentSeq = "style='padding-left: 32px;'";
    $trParent = $seq%2 == 0 ? 'even' : 'odd';

    $produto->setCustoTabela();
    
    $total += $produto->getCustoTabela();
}
*/
