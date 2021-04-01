<?php
ob_start();
//include_once(__DIR__.'/../../includes.inc');
include_once('../includes.inc');

header('Content-Type: text/html; charset=utf-8');

//$consultar = filter_input(INPUT_POST, 'btnConsultar');

$sql = new clsDB();

/* SELECIONA OS PEDIDOS ATIVOS DA EMPRESA 99 */
$select = "SELECT PEDI_PED
            FROM PEDIDO A
            WHERE CODI_EMP = 99
              AND DEMI_PED > TO_DATE('01/01/2018','DD/MM/YYYY')
              AND SITU_PED <> 9
              AND PEDI_PED IN (2551)
            ORDER BY PEDI_PED";

    $pedidos = [];
    foreach ($sql->getDados($select) as $row)
    {
        $pedidos[] = $row["PEDI_PED"];
    }

/* PEGA O PRÓXIMO ID DE PEDIDO */
$select = "SELECT MAX(PEDI_PED) + 1 AS ID FROM PEDIDO WHERE CODI_EMP = 1";

    foreach ($sql->getDados($select) as $row)
    {
        $next = $row["ID"];
    }

$cont = 1;
foreach($pedidos as $idPedido)
{
    $insert = "INSERT INTO PEDIDO
               SELECT
                    1 AS CODI_EMP, $next, SERI_PED, PROP_PRO, CODI_IND, DATA_VLR, CCFO_CFO, CODI_TOP, CODI_OBS, CODI_MTC, CODI_TRA, COND_CON, COD1_PES, COD2_PES, CFOR_TRA, TFAT_PED, DEMI_PED, VCTO_PED, DCON_PED, DANT_PED, JURO_PED, JATV_PED, JAPV_PED, DINI_PED, COMI_PED, TOTA_PED, TPRO_PED, FRET_PED, SEGU_PED, OUTR_PED, DESC_PED, ACRE_PED, ALIR_PED, VLIR_PED, OBSE_PED, SITU_PED, LDES_PED, DPON_PED, MOTC_PED, VIPI_PED, COM1_PED, COM2_PED, CODI_LOC, OBSC_PED, PEDO_PED, DUMANUT, SVDI_PED, SASS_PED, COD1_SUP, COD2_SUP, COD3_SUP, COD4_SUP, CODI_ZON, CODI_CIC, COBC_OBS, OCOM_PED, ORIG_PED, CODI_END, CTRA_PED, DTRA_PED, TTRA_PED, CNPT_PED, ENDT_PED, CODT_PED, INST_PED, TPFR_PED, PLAC_PED, MARC_PED, QTDE_PED, ESPE_PED, PLIQ_PED, PBRU_PED, VTRA_PED, DSAI_PED, HSAI_PED, UFPL_PED, AREA_PED, ALFR_PED, VLFR_PED, ACPT_PED, STAT_PED, MOTD_PED, CODI_USU, CODI_TLH, VIAT_PED, PEDI_TRC, CODI_CPP, VERS_CPP, VLME_PED, INCT_PED, CARR_PED, DOCS_PED, LPMC_PED, FPGT_PED, DLIB_PED, DCMT_VLR, LPMF_PED, TABE_CTA, PDME_PED, SDME_PED, SPAF_PED, SEQU_DAV, SERI_DAV, RFRT_PED, VFRT_PED, ICMS_TRA, RNTC_PED, BICS_PED, AICS_PED, VICS_PED, BICM_PED, VICM_PED, SOMS_PED, CODI_CTC, NOME_PED, ENDE_PED, CGC_PED, TJUR_PED, UMPD_PED, DMPD_PED, RMPD_PED, CTRL_NEG, LANP_PED, CANP_PED, VRIT_PED, PCNG_PED, DCAN_PED, HCAN_PED, CAFV_PED, NUME_CCP, IPRE_PED, HEMI_PED, QTDA_PED, CFIN_PED, PEDF_PED, EXFR_PED, VPFR_PED, BCFR_PED, AFST_PED, ISFR_PED, VLAM_PED, PERE_PED, ESTA_PED, DINSERT, OPSE_TRA, CODI_ERT, DTSYNCAPI, NUME_PED, CODI_VET
                    FROM PEDIDO
                    WHERE CODI_EMP = 99
                    AND PEDI_PED = $idPedido";

    $res = $sql->execute($insert);

    if ($res) {
        $sql->commit();
    } 
    else {
        echo "Erro no cabeçalho do Pedido:<br>";
        echo "Pedido 99 -> $idPedido <br>";
        echo "Pedido 1 -> $next <br>";
        echo "Cont -> $cont <br>";
        die($insert);
    }

    $insert = "INSERT INTO IPEDIDO
               SELECT
                    1 as CODI_EMP, $next, SERI_PED, CODI_PSV, CCFO_CFO, CODI_PRV, CODI_CUL, TABE_CTA, QTDE_IPE, QENC_IPE, QPER_IPE, QPAG_IPE, VLOR_IPE, VLOM_IPE, DSAC_IPE, VLIQ_IPE, COM1_IPE, COM2_IPE, CEMP_IPE, DUMANUT, CMVD_IPE, CTAB_IPE, PVTB_IPE, CONS_IPE, CSP1_IPE, CSP2_IPE, CSP3_IPE, CSP4_IPE, CGFI_IPE, ORCM_IPE, DTPR_IPE, CCPD_IPE, AIPI_IPE, VIPI_IPE, SREC_IPE, CODI_DPT, DAOM_IPE, VLRO_IPE, DEMI_PED, BIPI_IPE, CSTI_IPE, VFRT_IPE, CODI_BAR, DANT_TAB, ALIQ_IPE, VICM_IPE, ADIC_IPE, BICS_IPE, AICS_IPE, VICS_IPE, BICM_IPE, AICM_IPE, ICMS_IPE, CODI_ORI, QSUB_IPE, COD2_ORI, QSU2_IPE, COD3_ORI, QSU3_IPE, STAT_IPE, CODI_TRA, PDES_IPE, CIGR_COM, MDIC_IPE, MCTB_IPE, UIND_IPE, ITEM_IPE, CAFV_IPE, ATUC_IPE, QTDH_IPE, VLOH_IPE, VOMH_IPE, SUPN_IPE, CODI_EQP, LRET_IPE, PB2B_IPE, ITPE_IPE, LPMC_IPE, DSOF_IPE, VDOF_IPE, VLDO_IPE, IFSM_IPE, AIIC_IPE, BIIC_IPE, VIIC_IPE, COAJ_IPE, PDIF_IPE, CODI_TOP, DINSERT, DTSYNCAPI, PVTL_IPE, IMGV_IPE, PMGV_IPE, PCVI_IPE, NNRA_IPE, CODI_CPC, BICE_IPE, AICE_IPE, VICE_IPE, RICE_IPE
                    FROM IPEDIDO
                    WHERE CODI_EMP = 99
                      AND PEDI_PED = $idPedido";

    $res = $sql->execute($insert);

    if ($res) {
        $sql->commit();
    } 
    else {
        echo "Erro na linha do Pedido:<br>";
        echo "Pedido 99 -> $idPedido <br>";
        echo "Pedido 1 -> $next <br>";
        echo "Cont -> $cont <br>";
        die($insert);
    }

    $update = "UPDATE PEDIDO SET SITU_PED = 9, PEDF_PED = $next, CODI_USU = 'DANIEL'
               WHERE CODI_EMP = 99
                 AND PEDI_PED = $idPedido";

    $res = $sql->execute($update);

    if ($res) {
        $sql->commit();
    } 
    else {
        echo "Erro na linha do Pedido:<br>";
        echo "Pedido 99 -> $idPedido <br>";
        echo "Pedido 1 -> $next";
        echo "Cont -> $cont <br>";
        die($update);
    }

    $next++;
    $cont++;
}

echo "Copiado com sucesso";