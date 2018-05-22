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

// path (local, del filesystem) de malware a ingresar
$PATH = "/mnt/muestras/a-procesar";

// registrar como usuario "cron"
$WS_USERNAME = 'cron';
$WS_PASSWORD = 'cron123';

// proceso principal
$totalProcesados=0;

echo "- PROCESANDO: ".$PATH." - ".date("Y-m-d H:i:s")."<br />\n";

#if ($handle = opendir($PATH))	{
#	while (false !== ($archivo = readdir($handle))) {

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($PATH));
foreach ($rii as $file) {

		if ($file->isDir())	{
			continue;
		}

		$archivo = $file->getPathname(); 

		//if ($archivo != "." && $archivo != "..") {
		if (true)	{

			$Domain = $archivo;

			$filename=substr($Domain, strrpos($Domain, "/")+1);
			if ($filename == '')	{
				$filename = 'Sin nombre';
			}
			
			echo "-- MUESTRA: ".$Domain.": ";

			// descarga de archivo
			$content=file_get_contents($Domain);
			if ($content === false || strlen($content) < 1){
				echo "PROBLEMA AL CARGAR.<br />\n";
			}else{
				
				// registro vía webservice
				$client = new soap_client($WS_URL);
				$client->debug_flag=true;
				$err = $client->getError();
				$param = array('Usuario'=>$WS_USERNAME, 'Password'=>$WS_PASSWORD, 'MuestraNombreArchivo'=>$filename, 'MuestraContenidoBase64'=>base64_encode($content));
				$result = $client->call("registrarMuestra", $param);
				$bak_sha256 = $result['SHA256'];

				// agregado de nota
				$nota = "Muestra cargada automáticamente --- LOCAL";
				$nota .= "\nArchivo: $Domain\n";
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
				
				flush();
				ob_flush();

			}
		}
				
		// borrar archivo
		unlink($file->getPathname());
	}

//	closedir($handle);
//}



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
