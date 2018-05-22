#!/bin/bash

fechaInicio=$(date)

cd /opt/SSMA/rules/
/usr/bin/git pull


cantInicio=$(ls -l /mnt/muestras/aam | wc -l)

wget --timeout=9000 --tries=1 -S -O - http://localhost/aam/web/tmp/descarga1.php 1>/var/log/aam/aam.d1.cron.log 2>&1
#wget --timeout=9000 --tries=1 -S -O - http://localhost/aam/web/tmp/descarga2.php 1>/var/log/aam/aam.d2.cron.log 2>&1
#wget --timeout=9000 --tries=1 -S -O - http://localhost/aam/web/tmp/descarga3.php 1>/var/log/aam/aam.d3.cron.log 2>&1
#wget --timeout=9000 --tries=1 -S -O - http://localhost/aam/web/tmp/descarga4.php 1>/var/log/aam/aam.d4.cron.log 2>&1
#wget --timeout=9000 --tries=1 -S -O - http://localhost/aam/web/tmp/descarga5.php 1>/var/log/aam/aam.d5.cron.log 2>&1
#wget --timeout=9000 --tries=1 -S -O - http://localhost/aam/web/tmp/carga_local.php 1>/var/log/aam/aam.cl.cron.log 2>&1


cantFin=$(ls -l /mnt/muestras/aam | wc -l)

cantTotal=$(($cantFin-$cantInicio))

espacio=$(df -h /dev/sdb1 | grep muestras | awk '{print $5}')

fechaFin=$(date)

leyenda="Inicio: "$fechaInicio" Fin: "$fechaFin" Espacio ocupado: "$espacio" Cant. muestras: "$cantTotal
echo $leyenda>>/var/log/aam/aam.descargas.log

