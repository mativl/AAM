<?php
// ----------------------------------------------------------------------
// AAM - ANÁLISIS AUTOMÁTICO DE MALWARE
// ----------------------------------------------------------------------
// Fecha de Creación: 		05/11/2014
// Última Modificación: 	05/11/2014
// Descripción Objeto: 		Configuración de acceso a BBDD y otros.
// ----------------------------------------------------------------------

///////////////////////////////////////////////////////////////////////////////

// acceso a BBDD
$conn = mysql_connect('localhost', 'aam', 'aam321', true);
mysql_select_db('aam', $conn);


///////////////////////////////////////////////////////////////////////////////

// ruta del web-server/app
$URL_WEB = "http://192.168.44.129/aam/";
$URL_WEB_CUCKOO = '192.168.44.129:8090';

// configuración de acceso a web-service (testing)
$WS_URL = $URL_WEB . 'webservices/ServicioAAM.php';

// carpetas del sistema
$CARPETA_GRAL_MUESTRAS = '/mnt/muestras/aam/';
$CARPETA_GRAL_AE_TMP = '/var/www/html/aam/analisis_estatico_tmp/fichero_tmp';
$CARPETA_GRAL_CUCKOO = '/var/www/html/aam/reportescuckoo/';
$CARPETA_GRAL_CUCKOO_TMP = '/var/www/html/aam/reportescuckoo/tmp/';
$CMD_SUBMIT_CUCKOO = 'sudo -u upa cuckoo submit';
$OUTPUT_CUCKOO_EXEC = '/tmp/aam.cuckoo.submit.log';
$CARPETA_CUCKOO_ANALYSES = '/home/upa/.cuckoo/storage/analyses/';

// RETDEC
$RETDEC_API_KEY = '5ab1fd2a-158d-4061-a97a-7d943b5c4e4b';
$RETDEC_URL = 'https://retdec.com/service/api';
$CARPETA_GRAL_RETDEC = '/var/www/html/aam/reportesretdec/';

// accceso API virustotal
$VIRUSTOTAL_API_KEY = 'b315ef2a2bae16915a6cf187844b4686301314a148e5d380616a16246ffd8c30';
$VIRUSTOTAL_URL = 'https://www.virustotal.com/vtapi/v2/file/scan';

// IDs de usuarios específicos
$ID_USUARIO_ADMIN = 1;
$ID_USUARIO_CRON = 10;
$ID_USUARIO_INVITADO = 50;

// commando file para recuperar tipo mime
$COMMANDO_FILE_MIME = "/usr/bin/file -b --mime-type %s";

// estimaciones malware
$ESTIMA_COLORES[-1]	= '';
$ESTIMA_COLORES[0]	= 'default';
$ESTIMA_COLORES[1]	= 'info';
$ESTIMA_COLORES[2]	= 'info';
$ESTIMA_COLORES[3]	= 'primary';
$ESTIMA_COLORES[4]	= 'primary';
$ESTIMA_COLORES[5]	= 'success';
$ESTIMA_COLORES[6]	= 'success';
$ESTIMA_COLORES[7]	= 'warning';
$ESTIMA_COLORES[8]	= 'warning';
$ESTIMA_COLORES[9]	= 'danger';
$ESTIMA_COLORES[10]	= 'danger';

$ESTIMA_COLORES_TXT['default']	= 'Limpio';
$ESTIMA_COLORES_TXT['info']		= 'Ambiguo';
$ESTIMA_COLORES_TXT['primary']	= 'Dudoso';
$ESTIMA_COLORES_TXT['success']	= 'Sospechoso';
$ESTIMA_COLORES_TXT['warning']	= 'Peligroso';
$ESTIMA_COLORES_TXT['danger']	= 'Malicioso';


///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////


function startFileScan($fileToScan) {
	$api = new VirusTotalAPIV2($VIRUSTOTAL_API_KEY);
	$result = $api->scanFile($fileToScan);
	return $api->getScanID($result);
}
function getVirusTotalReport($scanId) {
	$api = new VirusTotalAPIV2($VIRUSTOTAL_API_KEY);
	$report = $api->getFileReport($scanId);
	$submissionDate = $api->getSubmissionDate($report);
	$permalink = $api->getReportPermalink($report, TRUE);
	//json_encode($report)

}

?>
