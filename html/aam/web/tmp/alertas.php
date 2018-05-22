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

$consulta = mysql_query("SELECT nombre, id_herramienta, horas_desde_analisis, patrones, emails, activa FROM alertas WHERE activa = 1", $conn);
while ($registro = mysql_fetch_array($consulta)){

	echo "SE PROCESARÁ ALERTA: ".$registro['nombre']."<br />\n";

	$mail_msj = "";

	// xxx
	$registro['horas_desde_analisis'] = 24;
	
	$arr_patrones = explode('||', $registro['patrones']);
	foreach ($arr_patrones as $patron){
		
		// salida de herramienta seleccionada
		$consulta2 = mysql_query("SELECT DISTINCT m.sha256 ".
		 "FROM resultados_herramientas rh ".
		 "INNER JOIN muestras m ON rh.id_muestra = m.id_muestra ".
		 "WHERE rh.salida LIKE '%".mysql_real_escape_string($patron)."%' ".
		 "AND rh.id_herramienta = '".$registro['id_herramienta']."' ".
		 "AND rh.fecha_hora_analisis >= (date_sub(now(), INTERVAL ".$registro['horas_desde_analisis']." hour)) ", $conn);
		while ($registro2 = mysql_fetch_array($consulta2)){
			$mail_msj .= "- Patrón '".$patron."' encontrado en salida de herramienta #".$registro['id_herramienta']." para muestra:\n".$registro2['sha256']."\n\n";
		}

		// nombres de archivos
		$consulta3 = mysql_query("SELECT DISTINCT m.sha256 ".
		 "FROM recepciones r ".
		 "INNER JOIN muestras m ON r.id_muestra = m.id_muestra ".
		 "WHERE r.nombre_archivo LIKE '%".mysql_real_escape_string($patron)."%' ".
		 "AND r.fecha_hora >= (date_sub(now(), INTERVAL ".$registro['horas_desde_analisis']." hour)) ", $conn);
		while ($registro3 = mysql_fetch_array($consulta3)){
			$mail_msj .= "- Patrón '".$patron."' encontrado en nombre de archivo para muestra:\n".$registro3['sha256']."\n\n";
		}

		// notas
		$consulta4 = mysql_query("SELECT DISTINCT m.sha256 ".
		 "FROM notas n ".
		 "INNER JOIN muestras m ON n.id_muestra = m.id_muestra ".
		 "WHERE n.nota LIKE '%".mysql_real_escape_string($patron)."%' ".
		 "AND n.fecha_hora >= (date_sub(now(), INTERVAL ".$registro['horas_desde_analisis']." hour)) ", $conn);
		while ($registro4 = mysql_fetch_array($consulta4)){
			$mail_msj .= "- Patrón '".$patron."' encontrado en notas para muestra:\n".$registro4['sha256']."\n\n";
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
