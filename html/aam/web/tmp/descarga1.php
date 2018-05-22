<?php

///////////////////////////////////////////////////////////////////////////////

require_once("../../includes/configuracion.php");
require_once('../../lib-nusoap/nusoap.php');

///////////////////////////////////////////////////////////////////////////////


// sin timeout php
set_time_limit(0);

// subir l�mite de memoria
ini_set('memory_limit', '512M');

?>
<html>
<body>
<hr />
<?php

// incio
echo date("Y-m-d H:i:s") . " - INICIO DE PROCESO<hr /><br /><br />";

// url de db online de malc0de
$URL = "http://malc0de.com/database/index.php?&page=";

// registrar como usuario "cron"
$WS_USERNAME = 'cron';
$WS_PASSWORD = 'cron123';

// proceso principal
$num_pagina = 0;
$totalProcesados=0;
while($num_pagina < 50){

	$num_pagina++;
	$pagina = $URL . $num_pagina;
	echo "- PROCESANDO: ".$pagina." - ".date("Y-m-d H:i:s")."<br />\n";
	// descargo web
	$htmlWeb=file_get_contents($pagina);
	$tablaVirus=substr($htmlWeb, strpos($htmlWeb, "<table class='prettytable'>"));
	//$LineasTabla=explode("\n", $tablaVirus);
	$LineasTabla=explode("</tr>", $tablaVirus);
	//shift_array($LineasTabla);

	// parseo
	foreach($LineasTabla as $unaLinea){
	
		// obtengo columnas
		if (preg_match_all("/<td.*?>(.*?)<\/td>/i", $unaLinea, $matches)){
		
			$Date=substr(strip_tags($matches[0][0]), 0, 10);
			$Domain="http://".strip_tags($matches[0][1]);
			$IP=strip_tags($matches[0][2]);
			$CC=strip_tags($matches[0][3]);
			$ASN=strip_tags($matches[0][4]);
			$N_ASN=strip_tags($matches[0][5]);
			$MD5=strip_tags($matches[0][6]);
			$DomainExtension=strtolower(substr($Domain, strrpos($Domain, ".")));
			$filename=substr($Domain, strrpos($Domain, "/")+1);
			
			echo "-- MUESTRA DEL: ".$Date.": ".$Domain.": ";
			if($Date==date("Y-m-d") || $Date==date("Y-m-d", strtotime("yesterday"))){
				
				// descarga de archivo
				$content=file_get_contents($Domain);
				if ($content === false || strlen($content) < 1){
					echo "PROBLEMA AL DESCARGAR.<br />\n";
				}else{
					
					// registro v�a webservice
					$client = new soap_client($WS_URL);
					$client->debug_flag=true;
					$err = $client->getError();
					$param = array ('Usuario'=>$WS_USERNAME, 'Password'=>$WS_PASSWORD, 'MuestraNombreArchivo'=>$filename, 'MuestraContenidoBase64'=>base64_encode($content));
					$result = $client->call("registrarMuestra", $param);
					$bak_sha256 = $result['SHA256'];

					// agregado de nota
					$nota = "Muestra descargada autom�ticamente desde http://malc0de.com/database/";
					$nota .= "\nFecha: $Date\nCC: $CC\nDominio: $Domain\nIP: $IP\nASN: $N_ASN ($ASN)";
					$param = array ('Usuario'=>$WS_USERNAME, 'Password'=>$WS_PASSWORD, 'SHA256'=>$bak_sha256, 'Nota'=>$nota);
					$result = $client->call("registrarNota", $param);

					// pedido de an�lisis (si no fue ya analizado recientemente)
					$debe_analizar = true;
					// en el dia
					// $consulta = mysql_query("SELECT m.id_muestra FROM muestras m INNER JOIN resultados_herramientas rh ON m.id_muestra = rh.id_muestra WHERE DATE(rh.fecha_hora_analisis) = DATE(NOW()) AND m.sha256 = '".$bak_sha256."'", $conn);
					// en la semana
					$consulta = mysql_query("SELECT m.id_muestra FROM muestras m INNER JOIN resultados_herramientas rh ON m.id_muestra = rh.id_muestra WHERE DATE(rh.fecha_hora_analisis) >= DATE(DATE_SUB(NOW(), INTERVAL 7 DAY)) AND m.sha256 = '".$bak_sha256."'", $conn);

					if ($registro = mysql_fetch_array($consulta)){
						$debe_analizar = false;
					}
					if ($debe_analizar){
						$param = array ('Usuario'=>$WS_USERNAME, 'Password'=>$WS_PASSWORD, 'SHA256'=>$bak_sha256);
						$result = $client->call("analizarMuestra", $param);
					}

					echo "PROCESADA (ANALIZADA: ".$debe_analizar.").<br />\n";
					$totalProcesados++;
					// xxx
					if (isset($_GET['maxproc']))	{
						if ($totalProcesados >= $_GET['maxproc'])	{
							echo "CORTE POR PAR�METRO MAXPROC.<br />\n";
							break 2;
						}
					}
				}
			}else{
				echo "DESCARTE Y CORTE (FECHA).<br />\n";
				break 2;
			}
		}
	}
	
	flush();
	ob_flush();
}

?>
<br />
<br />
Total de muestras procesadas: <?php echo $totalProcesados ?>
<hr />
<?php echo date("Y-m-d H:i:s"); ?> - PROCESO TERMINADO.
<hr />
<br />
<br />
</body>
</html>
