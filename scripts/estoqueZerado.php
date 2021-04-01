<?php

include_once('includes.inc');

header('Content-Type: text/html; charset=utf-8');

$status = 'A';
$arrGrupos = [1,2,5,17];
$vincular = true;
$calcular = ['EF','PCNR','PVNE','VF','RDE'];

$arrProdutos = Produto::getProdutos($status, $arrGrupos, $vincular, $calcular);

$dataHora = new clsDataHora();


echo "<div class='panel panel-primary'>";
echo "  <div class='panel-heading' style='background-color: #286090; border-color: #286090'>";
echo "    <div class='panel-title textCenter'>Produtos com estoque ZERADO</div>";
echo "    <div class='textCenter' style='font-size: small;'>".$dataHora->getDataHora()."</div>";
echo "  </div>";
echo "</div>";


echo "<table class='table table-bordered treetable'>";
echo "<thead>";
echo "  <tr>";
//echo "    <th class='header textCenter' style='vertical-align: middle;'>Seq</th>";
echo "    <th class='header textLeft'>Produto</th>";
//echo "    <th class='header textCenter'>Estoque Calculado</th>";
//echo "    <th class='header textCenter'>EF(+)</th>";
//echo "    <th class='header textCenter'>PCNR(+)</th>";
//echo "    <th class='header textCenter'>PVNE(-)</th>";
//echo "    <th class='header textCenter'>VF(-)</th>";
//echo "    <th class='header textCenter'>RDE(+)</th>";
echo "  </tr>";
echo "</thead>";
echo "<tbody>";
    

$seq = 1;
foreach($arrProdutos as $produto)
{
    $EF = $produto->getEF();
    $PCNR = $produto->getPCNR();
    $PVNE = $produto->getPVNE();
    $VF = $produto->getVF();
    $RDE = $produto->getRDE();
    $EC = $produto->getEC();
    
    $treetable = '';
    $child = '';
    $count = 1;
    $parentSeq = "style='padding-left: 32px;'";
    $trParent = $seq%2 == 0 ? 'even' : 'odd';

    $arr = $produto->getArrVinc();
    if($arr)
    {
        $parentSeq = '';
        $treetable = "data-tt-id='$seq'";
        $trStyle = "class='child' data-tt-id='$seq.$count' data-tt-parent-id='$seq'";
        $childSeq = "style='border-top-color:transparent; border-left-color:transparent; border-bottom-color:transparent;'";
        //$childEC = "style='border-top-color:transparent; border-bottom-color:transparent;'";
        
        $child .= "<tr $trStyle>";
        $child .= "  <td $childSeq></td>";
        $child .= "  <td class='textLeft'>".$produto->getDesc()."</td>";
        $child .= "  <td class='textRight'>".number_format($produto->getEC(), 2, ',', '.')."</td>";
        $child .= "  <td class='textRight'>".number_format($produto->getEF(), 2, ',', '.')."</td>";
        $child .= "  <td class='textRight'>".number_format($produto->getPCNR(), 2, ',', '.')."</td>";
        $child .= "  <td class='textRight'>".number_format($produto->getPVNE(), 2, ',', '.')."</td>";
        $child .= "  <td class='textRight'>".number_format($produto->getVF(), 2, ',', '.')."</td>";
        $child .= "  <td class='textRight'>".number_format($produto->getRDE(), 2, ',', '.')."</td>";
        
        foreach($arr as $vinc)
        {
            $count++;
            
            if($vinc === end($arr) and $produto !== end($arrProdutos))
            {
                $childSeq = "style='border-top-color:transparent; border-left-color:transparent;'";
                //$childEC = "style='border-top-color:transparent;'";
            }
            
            $child .= "<tr $trStyle>";
            $child .= "  <td $childSeq></td>";
            $child .= "  <td class='textLeft'>".$vinc->getDesc()."</td>";
            $child .= "  <td class='textRight'>".number_format($vinc->getEC(), 2, ',', '.')."</td>";
            $child .= "  <td class='textRight'>".number_format($vinc->getEF(), 2, ',', '.')."</td>";
            $child .= "  <td class='textRight'>".number_format($vinc->getPCNR(), 2, ',', '.')."</td>";
            $child .= "  <td class='textRight'>".number_format($vinc->getPVNE(), 2, ',', '.')."</td>";
            $child .= "  <td class='textRight'>".number_format($vinc->getVF(), 2, ',', '.')."</td>";
            $child .= "  <td class='textRight'>".number_format($vinc->getRDE(), 2, ',', '.')."</td>";
            
            $EF += $vinc->getEF();
            $PCNR += $vinc->getPCNR();
            $PVNE += $vinc->getPVNE();
            $VF += $vinc->getVF();
            $RDE += $vinc->getRDE();
            $EC += $vinc->getEC();
        }
    }
    
    $negativo = $EC < 0 ? 'negativo' : '';

    if($EF + $RDE <= 0 and ($PVNE <> 0 or $VF <> 0))
    {
        echo "  <tr class='$trParent'>";
        //echo "    <td class='textCenter' $parentSeq><a href='#' data-toggle='tooltip' title='Teste' style='text-decoration:none; cursor:default; color:black;'>".$seq++."</a></td>";
        //echo "    <td class='textCenter' $parentSeq>".$seq."</td>";
        //echo "    <td>".$produto->getCodi()."</td>";
        echo "    <td class='textLeft'>".$produto->getDesc(true)."</td>";
        //echo "    <td class='textRight $negativo tpEC'>".number_format($EC, 2, ',', '.')."</td>";
        //echo "    <td class='textRight tpEF'>".number_format($EF, 2, ',', '.')."</td>";
        //echo "    <td class='textRight tpPCNR'>".number_format($PCNR, 2, ',', '.')."</td>";
        //echo "    <td class='textRight tpPVNE'>".number_format($PVNE, 2, ',', '.')."</td>";
        //echo "    <td class='textRight tpVF'>".number_format($VF, 2, ',', '.')."</td>";
        //echo "    <td class='textRight tpRDE'>".number_format($RDE, 2, ',', '.')."</td>";
        echo "  </tr>";
        $seq++;
        //echo $child;
    }
}

echo "</tbody>";
echo "</table>";
