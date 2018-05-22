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

$totalProcesados = 0;

$consulta = mysql_query("SELECT id_alerta_url, nombre, url, pagina_desde, pagina_hasta, patrones, emails, activa FROM alertas_url WHERE activa = 1", $conn);
while ($registro = mysql_fetch_array($consulta)){

	echo "SE PROCESARÁ ALERTA: ".$registro['nombre']."<br />\n";

	$mail_msj = "";

	$pagina_desde = intval($registro['pagina_desde']);
	$pagina_hasta = intval($registro['pagina_hasta']);

	for ($pagina=$pagina_desde; $pagina<=$pagina_hasta; $pagina++)	{

		$url = str_replace('PAGINA', $pagina, $registro['url']);

		echo "PROCESANDO $url...<br />\n";

		$html = file_get_contents($url);
		
		preg_match_all('/([a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,4}[^\s\<\>\[\]]*)/i', $html, $result);

		$result[0] = array_unique($result[0]);
		
		foreach ($result[0] as $dominio)	{
			//echo $dominio . "<br />\n";
			$arr_patrones = explode('||', $registro['patrones']);
			foreach ($arr_patrones as $patron)	{
				if (strpos($dominio, $patron) !== false)	{
					$consulta2 = mysql_query("SELECT id_alerta_url_log FROM alertas_url_log WHERE id_alerta_url = '".$registro['id_alerta_url']."' AND alerta_enviada = '".$dominio."' AND fecha_hora >= date_sub(now(), INTERVAL 3 day)", $conn);
					if ($registro2 = mysql_fetch_array($consulta2))	{
					} else	{
						$mail_msj .= "- Patrón '".$patron."' encontrado en dominio '".$dominio."' en listado '".$url."'.\n\n";
						mysql_query("INSERT INTO alertas_url_log (id_alerta_url, alerta_enviada, fecha_hora) VALUES ('".$registro['id_alerta_url']."', '".$dominio."', NOW())", $conn);
					}
				}
			}
		}
	}

	// envío de mail (si hubo alerta)
	if ($mail_msj != "")	{
		$mail_sub = "AAM - Alerta automática: ".$registro['nombre'];
		$mail_msj = $mail_sub . "\n\n\n" . $mail_msj;
		echo "SE ENVIARÁN MAILS DE ALERTA: ".$registro['emails']."<br />\n";
		$arr_emails = explode(',', $registro['emails']);
		foreach ($arr_emails as $email){
			mail($email, $mail_sub, $mail_msj);
		}
	}
	$totalProcesados++;
}

?>
<br />
<br />
Total de alertas procesadas: <?php echo $totalProcesados ?>
<hr />
<?php echo date("Y-m-d H:i:s"); ?> - PROCESO TERMINADO.
<hr />
<br />
<br />
</body>
</html>
