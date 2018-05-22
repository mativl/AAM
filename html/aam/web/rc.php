<?php

///////////////////////////////////////////////////////////////////////////////

require_once("../includes/configuracion.php");

///////////////////////////////////////////////////////////////////////////////

@session_start();
if (isset($_SESSION['WS_USERNAME']) && is_numeric($_GET['id']))	{
	$reporte_cukoo_html = file_get_contents("../reportescuckoo/".$_GET['id']."/reports/report.html");
	// <section id="signatures">
	// <footer class="footer">
	//$pos1 = strpos ($reporte_cukoo_html, '<section id="signatures">');
	//$pos2 = strpos ($reporte_cukoo_html, '<footer class="footer">');
	//$reporte_cukoo_html = substr($reporte_cukoo_html, $pos1, $pos2-$pos1);

$str = "<img src=\"data:image/png;base64,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
\" border=\"0\" />";

	$reporte_cukoo_html = str_replace($str, "", $reporte_cukoo_html);

	echo $reporte_cukoo_html;
}

///////////////////////////////////////////////////////////////////////////////
?>
