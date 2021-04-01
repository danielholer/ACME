#!/bin/bash

#################################################################################
# atualizaGRRF.sh
#
# Autor: Daniel Holer
# Data Criacao: 17/01/2019
#
# Descricao: Script que faz o download do arquivo de atualizacao do GRRF
#            e copia para o PC da contabilidade
#
# Observacao: 
#
# Update - 12/06/2019: Acrescentada a atualizacao do SEFIP/GRF
#################################################################################

DATA=$(date +%d/%m/%Y)
DIA=$(date +%d)
MES=$(date +%m)
ANO=$(date +%Y)

cd /var/www/html/intranet/scripts/cron/GRRF

#verifica se o arquivo de atualizacao para o mes corrente ja foi baixado
#GRRF
if [ ! -f "GR$ANO$MES.zip" ]
then
    rm GR*   #apaga todos os arquivos antigos
    #wget "http://www.caixa.gov.br/Downloads/fgts-grrf-aplicativo-arquivos/GR$ANO$MES.zip"   #download do arquivo atual #comentado em 24/08/2020
    #wget "http://www.caixa.gov.br/Downloads/fgts-grrf-aplicativo-arquivos/GR$ANO$MES.EXE"   #download do arquivo atual #comentado em 29/10/2020
    wget "https://www.caixa.gov.br/Downloads/fgts-grrf-aplicativo-arquivos/GR$ANO$MES.zip"   #download do arquivo atual
    unzip -o GR$ANO$MES.zip #comentado em 24/08/2020 #descomentado em 29/10/2020
    unzip -o GR$ANO$MES.EXE
fi

#SEFIP/GRF
if [ ! -f "TF$ANO$MES.zip" ]
then
    rm TF*
    #wget "http://www.caixa.gov.br/Downloads/fgts-sefip-grf/TF$ANO$MES.zip" #comentado em 24/08/2020
    #wget "http://www.caixa.gov.br/Downloads/FGTS-SEFIP-GRF-Tabela-Coeficientes-FGTS-em-Atraso-TF/TF$ANO$MES.EXE" #comentado em 29/10/2020
    wget "https://www.caixa.gov.br/Downloads/FGTS-SEFIP-GRF-Tabela-Coeficientes-FGTS-em-Atraso-TF/TF$ANO$MES.zip"
    unzip -o TF$ANO$MES.zip #comentado em 24/08/2020 #descomentado em 29/10/2020
    unzip -o TF$ANO$MES.EXE
fi

if [ ! -f "SE$ANO$MES.zip" ]
then
    rm SE*
    #wget "http://www.caixa.gov.br/Downloads/fgts-sefip-grf/SE$ANO$MES.zip" #comentado em 24/08/2020
    #wget "http://www.caixa.gov.br/Downloads/FGTS-SEFIP-GRF-%C3%8Dndices-Recolhimento-INSS-em-Atraso-SE/SE$ANO$MES.EXE" #comentado em 29/10/2020
    wget "https://www.caixa.gov.br/Downloads/FGTS-SEFIP-GRF-%C3%8Dndices-Recolhimento-INSS-em-Atraso-SE/SE$ANO$MES.zip"
    unzip -o SE$ANO$MES.zip #comentado em 24/08/2020 #descomentado em 29/10/2020
    unzip -o SE$ANO$MES.EXE
fi

#if [ ! -f "AUXILIAR_$MES_$ANO.zip" ]
#then
#    rm AUXILIAR*
#    wget "http://www.caixa.gov.br/Downloads/fgts-sefip-grf/AUXILIAR_$MES_$ANO.zip"
#    unzip -o AUXILIAR_$MES_$ANO.zip
#fi


#monta a unidade de rede e copia o arquivo
sudo mount //wscat11/grrf -t cifs -o uid=1000,gid=1000,username=daniel,password=SOMA101. /mnt/GRRF

if mount | grep /mnt/GRRF > /dev/null
then
    #GRRF
    #cp "GR$ANO$MES.zip" /mnt/GRRF #comentado em 24/08/2020
    #cp "GR$ANO$MES.EXE" /mnt/GRRF #comentado em 29/10/2020
    cp -p "Ind_GRRF.Zip" /mnt/GRRF
    
    #SEFIP/GRF
    #cp "TF$ANO$MES.zip" /mnt/GRRF #comentado em 24/08/2020
    #cp "TF$ANO$MES.EXE" /mnt/GRRF #comentado em 29/10/2020
    cp -p "Indices.001" /mnt/GRRF
    cp -p "Indices.txt" /mnt/GRRF
    #cp "SE$ANO$MES.zip" /mnt/GRRF #comentado em 24/08/2020
    #cp "SE$ANO$MES.EXE" /mnt/GRRF #comentado em 29/10/2020
    cp -p "Selic.001" /mnt/GRRF
    cp -p "Selic.TXT" /mnt/GRRF
    #cp "AUXILIAR.001" /mnt/GRRF
    #cp "AUXILIAR.TXT" /mnt/GRRF
fi
