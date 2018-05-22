<?php

///////////////////////////////////////////////////////////////////////////////

require_once("../../includes/configuracion.php");
require_once('../../lib-nusoap/nusoap.php');

///////////////////////////////////////////////////////////////////////////////


// sin timeout php
set_time_limit(0);

// subir límite de memoria
ini_set('memory_limit', '512M');

?>
<html>
<body>
<hr />
<?

// incio
echo date("Y-m-d H:i:s") . " - INICIO DE PROCESO<hr /><br /><br />";

// url de lista de malware de clean-mx
$URL = "http://support.clean-mx.de/clean-mx/viruses.php";

// registrar como usuario "cron"
$WS_USERNAME = 'cron';
$WS_PASSWORD = 'cron123';

// proceso principal
$totalProcesados=0;



//xxx
$options = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"Accept-language: en\r\n" .
              "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n"
  )
);
$context = stream_context_create($options);


$pagina = $URL;
echo "- PROCESANDO: ".$pagina." - ".date("Y-m-d H:i:s")."<br />\n";
// descargo web
$htmlWeb=file_get_contents($pagina, false, $context);
//$tablaVirus=substr($htmlWeb, strpos($htmlWeb, "<TABLE"));
$tablaVirus = $htmlWeb;

#echo $tablaVirus;

$LineasTabla=explode("</tr>", $tablaVirus);

// parseo
foreach($LineasTabla as $unaLinea){

	// obtengo columnas
	if (preg_match_all("/<td.*?>(.*?)<\/td>/i", $unaLinea, $matches)){
	
		$Date=trim(strip_tags($matches[0][1]));
		$Date=substr($Date, 0, 10);
		$Domain=$matches[0][6];

		if (preg_match_all("/open Url in new Browser at your own risk .*? href=\"(.*?)\"/i", $Domain, $matches2))	{
			$Domain=$matches2[1][0];
		}

		$filename=substr($Domain, strrpos($Domain, "/")+1);
		if ($filename == '')	{
			$filename = 'Sin nombre';
		}

		//xxx
		if ($Date == '' || (substr($filename, -4) != '.exe' && substr($filename, -5) != '.html' && substr($filename, -4) != '.htm' && substr($filename, -3) != '.js'))
			continue;
		
		echo "-- MUESTRA DEL: ".$Date.": ".$Domain.": ";


		if($Date==date("Y-m-d")){

			// descarga de archivo
			$content=file_get_contents($Domain);
			if ($content === false || strlen($content) < 1){
				echo "PROBLEMA AL DESCARGAR.<br />\n";
			}else{
				
				// registro vía webservice
				$client = new soap_client($WS_URL);
				$client->debug_flag=true;
				$err = $client->getError();
				$param = array('Usuario'=>$WS_USERNAME, 'Password'=>$WS_PASSWORD, 'MuestraNombreArchivo'=>$filename, 'MuestraContenidoBase64'=>base64_encode($content));
				$result = $client->call("registrarMuestra", $param);
				$bak_sha256 = $result['SHA256'];

				// agregado de nota
				$nota = "Muestra descargada automáticamente desde $URL";
				$nota .= "\nDominio: $Domain\n";
				$param = array ('Usuario'=>$WS_USERNAME, 'Password'=>$WS_PASSWORD, 'SHA256'=>$bak_sha256, 'Nota'=>$nota);
				$result = $client->call("registrarNota", $param);

				// pedido de análisis (si no fue ya analizado recientemente)
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
						echo "CORTE POR PARÁMETRO MAXPROC.<br />\n";
						break 2;
					}
				}
			}

		}else{
			echo "NO DEL DÍA.<br />\n";
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
