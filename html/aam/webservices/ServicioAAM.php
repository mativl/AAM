<?php
// ----------------------------------------------------------------------
// AAM - ANÁLISIS AUTOMÁTICO DE MALWARE
// ----------------------------------------------------------------------
// Fecha de Creación: 		05/11/2014
// Última Modificación: 	05/11/2014
// Descripción Objeto: 		Implementación de los webservices.
// ----------------------------------------------------------------------

// sin timeout php
set_time_limit(0);

// subir límite de memoria
ini_set('memory_limit', '5120M');

// include de configuración
include "../includes/configuracion.php";

// librería soap
require_once('../lib-nusoap/nusoap.php');

// include de archivos requeridos VirusTotal
include "../includes/mime.php";
include "../includes/virustotal_api/VirusTotalApiV2.php";


///////////////////////////////////////////////////////////////////////////////
// Servicio Web "ServicioAAM"
///////////////////////////////////////////////////////////////////////////////

$ns = 'ServicioAAM';
$server = new soap_server();
$server->configureWSDL($ns, 'urn:'.$ns);
$server->wsdl->schemaTargetNamespace = 'urn:'.$ns;


///////////////////////////////////////////////////////////////////////////////
// Tipos de dato WSDL
///////////////////////////////////////////////////////////////////////////////

// Recepcion
$server->wsdl->addComplexType('Recepcion',
	'complexType',
	'struct',
	'all',
	'',
	array(
		'FechaHoraRecepcion' => array('name' => 'FechaHoraRecepcion', 'type' => 'xsd:string'),
		'Usuario' => array('name' => 'Usuario', 'type' => 'xsd:string'),
		'MuestraNombreArchivo' => array('name' => 'MuestraNombreArchivo', 'type' => 'xsd:string'),
		'Extra' => array('name' => 'Extra', 'type' => 'xsd:string')
	));

// ResultadoHerramienta
$server->wsdl->addComplexType('ResultadoHerramienta',
	'complexType',
	'struct',
	'all',
	'',
	array(
		'Herramienta' => array('name' => 'Herramienta', 'type' => 'xsd:string'),
		'HerramientaDescripcion' => array('name' => 'HerramientaDescripcion', 'type' => 'xsd:string'),
		'Version' => array('name' => 'Version', 'type' => 'xsd:string'),
		'Comando' => array('name' => 'Comando', 'type' => 'xsd:string'),
		'CodigoError' => array('name' => 'CodigoError', 'type' => 'xsd:int'),
		'Salida' => array('name' => 'Salida', 'type' => 'xsd:string'),
		'FechaHoraAnalisis' => array('name' => 'FechaHoraAnalisis', 'type' => 'xsd:string'),
		'FechaHoraInicio' => array('name' => 'FechaHoraInicio', 'type' => 'xsd:string'),
		'FechaHoraFin' => array('name' => 'FechaHoraFin', 'type' => 'xsd:string'),
		'Extra' => array('name' => 'Extra', 'type' => 'xsd:string')
	));

// Nota
$server->wsdl->addComplexType('Nota',
	'complexType',
	'struct',
	'all',
	'',
	array(
		'FechaHoraNota' => array('name' => 'FechaHoraNota', 'type' => 'xsd:string'),
		'Usuario' => array('name' => 'Usuario', 'type' => 'xsd:string'),
		'Nota' => array('name' => 'Nota', 'type' => 'xsd:string'),
		'Extra' => array('name' => 'Extra', 'type' => 'xsd:string')
	));

// array de Recepcion
$server->wsdl->addComplexType('ArrayRecepcion',
	'complexType',
	'array',
	'',
	'SOAP-ENC:Array',
	array(),
	array(array('ref'=>'SOAP-ENC:arrayType', 'wsdl:arrayType'=>'tns:Recepcion[]')), 'tns:Recepcion');

// array de ResultadoHerramienta
$server->wsdl->addComplexType('ArrayResultadoHerramienta',
	'complexType',
	'array',
	'',
	'SOAP-ENC:Array',
	array(),
	array(array('ref'=>'SOAP-ENC:arrayType', 'wsdl:arrayType'=>'tns:ResultadoHerramienta[]')), 'tns:ResultadoHerramienta');

// array de Nota
$server->wsdl->addComplexType('ArrayNota',
	'complexType',
	'array',
	'',
	'SOAP-ENC:Array',
	array(),
	array(array('ref'=>'SOAP-ENC:arrayType', 'wsdl:arrayType'=>'tns:Nota[]')), 'tns:Nota');


///////////////////////////////////////////////////////////////////////////////

// resultado para registrarMuestra()
$server->wsdl->addComplexType('registrarMuestraRet',
	'complexType',
	'struct',
	'all',
	'',
	array(
        'SHA256' => array('name' => 'SHA256', 'type' => 'xsd:string'),
        'RetCodigo' => array('name' => 'RetCodigo', 'type' => 'xsd:int'),
        'RetDescripcion' => array('name' => 'RetDescripcion', 'type' => 'xsd:string')
	));

// resultado para analizarMuestra()
$server->wsdl->addComplexType('analizarMuestraRet',
	'complexType',
	'struct',
	'all',
	'',
	array(
        'RetCodigo' => array('name' => 'RetCodigo', 'type' => 'xsd:int'),
        'RetDescripcion' => array('name' => 'RetDescripcion', 'type' => 'xsd:string')
	));

// resultado para consultarMuestra()
$server->wsdl->addComplexType('consultarMuestraRet',
	'complexType',
	'struct',
	'all',
	'',
	array(
		'FechaHoraIngreso' => array('name' => 'FechaHoraIngreso', 'type' => 'xsd:string'),
		'Bytes' => array('name' => 'Bytes', 'type' => 'xsd:string'),
		'MD5' => array('name' => 'MD5', 'type' => 'xsd:string'),
		'SHA1' => array('name' => 'SHA1', 'type' => 'xsd:string'),
		'SHA256' => array('name' => 'SHA256', 'type' => 'xsd:string'),
		'SHA512' => array('name' => 'SHA512', 'type' => 'xsd:string'),
		'Estima' => array('name' => 'Estima', 'type' => 'xsd:string'),
		'EstimaDetalles' => array('name' => 'EstimaDetalles', 'type' => 'xsd:string'),
		'Extra' => array('name' => 'Extra', 'type' => 'xsd:string'),
		'Recepciones' => array('name' => 'Recepciones', 'type' => 'tns:ArrayRecepcion'),
		'ResultadosHerramientas' => array('name' => 'ResultadosHerramientas', 'type' => 'tns:ArrayResultadoHerramienta'),
		'Notas' => array('name' => 'Notas', 'type' => 'tns:ArrayNota'),
		'CuckooHTML' => array('name' => 'CuckooHTML', 'type' => 'xsd:string'),
		'RetDecHTML' => array('name' => 'RetDecHTML', 'type' => 'xsd:string'),
        'RetCodigo' => array('name' => 'RetCodigo', 'type' => 'xsd:int'),
        'RetDescripcion' => array('name' => 'RetDescripcion', 'type' => 'xsd:string')
	));

// resultado para buscarMuestras()
$server->wsdl->addComplexType('buscarMuestrasRet',
	'complexType',
	'struct',
	'all',
	'',
	array(
        'ResultadosBusqueda' => array('name' => 'ResultadosBusqueda', 'type' => 'xsd:string'),
        'RetCodigo' => array('name' => 'RetCodigo', 'type' => 'xsd:int'),
        'RetDescripcion' => array('name' => 'RetDescripcion', 'type' => 'xsd:string')
	));

// resultado para enviarMuestraVT()
$server->wsdl->addComplexType('enviarMuestraVTRet',
	'complexType',
	'struct',
	'all',
	'',
	array(
        'RetCodigo' => array('name' => 'RetCodigo', 'type' => 'xsd:int'),
        'RetDescripcion' => array('name' => 'RetDescripcion', 'type' => 'xsd:string')
	));

// resultado para correrMuestraCuckoo()
$server->wsdl->addComplexType('correrMuestraCuckooRet',
	'complexType',
	'struct',
	'all',
	'',
	array(
        'RetCodigo' => array('name' => 'RetCodigo', 'type' => 'xsd:int'),
        'RetDescripcion' => array('name' => 'RetDescripcion', 'type' => 'xsd:string')
	));

// resultado para registrarNota()
$server->wsdl->addComplexType('registrarNotaRet',
	'complexType',
	'struct',
	'all',
	'',
	array(
        'RetCodigo' => array('name' => 'RetCodigo', 'type' => 'xsd:int'),
        'RetDescripcion' => array('name' => 'RetDescripcion', 'type' => 'xsd:string')
	));

// resultado para verificarUsuarioWS()
$server->wsdl->addComplexType('verificarUsuarioWSRet',
	'complexType',
	'struct',
	'all',
	'',
	array(
        'Id' => array('name' => 'Id', 'type' => 'xsd:string'),
        'Usuario' => array('name' => 'Usuario', 'type' => 'xsd:string'),
        'Email' => array('name' => 'Usuario', 'type' => 'xsd:string'),
        'RetCodigo' => array('name' => 'RetCodigo', 'type' => 'xsd:int'),
        'RetDescripcion' => array('name' => 'RetDescripcion', 'type' => 'xsd:string')
	));


///////////////////////////////////////////////////////////////////////////////
// Definición de funciones WSDL
///////////////////////////////////////////////////////////////////////////////

// función registrarMuestra()
$server->register('registrarMuestra',
	array(
		'Usuario'					=> 'xsd:string',
		'Password'					=> 'xsd:string',
		'MuestraPrivada'			=> 'xsd:string',
		'MuestraNombreArchivo'		=> 'xsd:string',
		'MuestraContenidoBase64'	=> 'xsd:string',
		'MuestraPrivada'			=> 'xsd:string'),
	array('return' => 'tns:registrarMuestraRet'), 'urn:'.$ns, 'urn:'.$ns.'#registrarMuestra', 'rpc', 'encoded', 'Envío y registro de muestra en el sistema.');

// función analizarMuestra()
$server->register('analizarMuestra',
	array(
		'Usuario'			=> 'xsd:string',
		'Password'			=> 'xsd:string',
		'SHA256'			=> 'xsd:string',
		'ConfigAnalisis'	=> 'xsd:string'),
	array('return' => 'tns:analizarMuestraRet'), 'urn:'.$ns, 'urn:'.$ns.'#analizarMuestra', 'rpc', 'encoded', 'Análisis/ejecución de herramientas sobre muestra ya registrada en el sistema.');

// función consultarMuestra()
$server->register('consultarMuestra',
	array(
		'Usuario'			=> 'xsd:string',
		'Password'			=> 'xsd:string',
		'SHA256'			=> 'xsd:string'),
	array('return' => 'tns:consultarMuestraRet'), 'urn:'.$ns, 'urn:'.$ns.'#consultarMuestra', 'rpc', 'encoded', 'Retorna un array de resultados de herramientas ejecutadas sobre la muestra.');

// función buscarMuestras()
$server->register('buscarMuestras',
	array(
		'Usuario'			=> 'xsd:string',
		'Password'			=> 'xsd:string',
		'TipoBusqueda'		=> 'xsd:string',
		'PatronBusqueda'	=> 'xsd:string'),
	array('return' => 'tns:buscarMuestrasRet'), 'urn:'.$ns, 'urn:'.$ns.'#buscarMuestras', 'rpc', 'encoded', 'Retorna los resultados de la búsqueda especificada.');

// función enviarMuestraVT()
$server->register('enviarMuestraVT',
	array(
		'Usuario'			=> 'xsd:string',
		'Password'			=> 'xsd:string',
		'SHA256'			=> 'xsd:string'),
	array('return' => 'tns:enviarMuestraVTRet'), 'urn:'.$ns, 'urn:'.$ns.'#enviarMuestraVT', 'rpc', 'encoded', 'Envía la muestra a VirusTotal.');

// función correrMuestraCuckoo()
$server->register('correrMuestraCuckoo',
	array(
		'Usuario'			=> 'xsd:string',
		'Password'			=> 'xsd:string',
		'SHA256'			=> 'xsd:string'),
	array('return' => 'tns:correrMuestraCuckooRet'), 'urn:'.$ns, 'urn:'.$ns.'#correrMuestraCuckoo', 'rpc', 'encoded', 'Analiza la muestra en Cuckoo.');

// función registrarNota()
$server->register('registrarNota',
	array(
		'Usuario'			=> 'xsd:string',
		'Password'			=> 'xsd:string',
		'SHA256'			=> 'xsd:string',
		'Nota'				=> 'xsd:string'),
	array('return' => 'tns:registrarNotaRet'), 'urn:'.$ns, 'urn:'.$ns.'#registrarNota', 'rpc', 'encoded', 'Registra nota/comentario del usuario para la muestra.');

// función verificarUsuarioWS()
$server->register('verificarUsuarioWS',
	array(
		'Usuario'			=> 'xsd:string',
		'Password'			=> 'xsd:string'),
	array('return' => 'tns:verificarUsuarioWSRet'), 'urn:'.$ns, 'urn:'.$ns.'#verificarUsuarioWS', 'rpc', 'encoded', 'Verificar datos de usuario (login/password).');


///////////////////////////////////////////////////////////////////////////////
// Implementación de funciones WSDL
///////////////////////////////////////////////////////////////////////////////

// función registrarMuestra
function registrarMuestra($Usuario = "", $Password = "", $MuestraNombreArchivo = "", $MuestraContenidoBase64 = "", $MuestraPrivada = "")	{

	// variables globales
	global $CARPETA_GRAL_MUESTRAS;
	global $conn;

	// parámetros - xxx - revisar
	$Usuario = mysql_real_escape_string($Usuario, $conn);
	$Password = mysql_real_escape_string($Password, $conn);
	if ($MuestraPrivada != '1')	{
		$MuestraPrivada = '0';
	}
	$MuestraNombreArchivo = mysql_real_escape_string($MuestraNombreArchivo, $conn);

	$registrarMuestraRet["SHA256"] = '';

	// compruebo usuario y password 
	$id_usuario = verificarUsuario($Usuario, $Password);
	if($id_usuario <= 0)	{
		$registrarMuestraRet["RetCodigo"] = 1;
		$registrarMuestraRet["RetDescripcion"] = "Usuario/Password de web-service no válidos.";
		return $registrarMuestraRet;
	}

	$MuestraContenido = base64_decode($MuestraContenidoBase64);
	$hash_md5 = hash('md5', $MuestraContenido);
	$hash_sha1 = hash('sha1', $MuestraContenido);
	$hash_sha256 = hash('sha256', $MuestraContenido);
	$hash_sha512 = hash('sha512', $MuestraContenido);

	// consultar si ya existe antes de insertar
	$existente = false;
	$consulta = mysql_query("SELECT id_muestra FROM muestras WHERE `sha256` = '$hash_sha256'", $conn);
	if ($registro = mysql_fetch_array($consulta)) {

		$id_muestra = $registro['id_muestra'];
		$existente = true;
	
	} else	{

		// insert a bbdd
		$insert_ok = mysql_query("INSERT INTO muestras (`fecha_hora`, `tamanio`, `md5`, `sha1`, `sha256`, `sha512`) ".
		 "VALUES (NOW(), ".strlen($MuestraContenido).", '$hash_md5', '$hash_sha1', '$hash_sha256', '$hash_sha512')", $conn);
		if (! $insert_ok)	{
			$registrarMuestraRet["RetCodigo"] = 2;
			$registrarMuestraRet["RetDescripcion"] = "Error al agregar muestra a bbdd: ".mysql_error();
			return $registrarMuestraRet;
		}
		$id_muestra = mysql_insert_id();

		// archivo a filesystem
		if (! ($fp = fopen($CARPETA_GRAL_MUESTRAS.$id_muestra, 'w')))	{
			$registrarMuestraRet["RetCodigo"] = 3;
			$registrarMuestraRet["RetDescripcion"] = "Error al agregar muestra a filesystem: ".$CARPETA_GRAL_MUESTRAS.$id_muestra;
			return $registrarMuestraRet;
		}
		fwrite($fp, $MuestraContenido);
		fclose($fp);
	}

	// recepción
	$insert_ok = mysql_query("INSERT INTO recepciones (`id_usuario`, `id_muestra`, `fecha_hora`, `nombre_archivo`, `privado`) ".
	 "VALUES ('$id_usuario', '$id_muestra', NOW(), '$MuestraNombreArchivo', '$MuestraPrivada')", $conn);
	if (! $insert_ok)	{
		$registrarMuestraRet["RetCodigo"] = 4;
		$registrarMuestraRet["RetDescripcion"] = "Error al registrar recepción en bbdd: ".mysql_error();
		return $registrarMuestraRet;
	}
	
	$registrarMuestraRet["SHA256"] = $hash_sha256;
	$registrarMuestraRet["RetCodigo"] = 0;
	$registrarMuestraRet["RetDescripcion"] = "";
	return $registrarMuestraRet;
}

// función analizarMuestra
function analizarMuestra($Usuario = "", $Password = "", $SHA256 = "")	{

	// variables globales
	global $CARPETA_GRAL_MUESTRAS;
	global $conn;

	// parámetros - xxx - revisar
	$Usuario = mysql_real_escape_string($Usuario, $conn);
	$Password = mysql_real_escape_string($Password, $conn);
	$SHA256 = mysql_real_escape_string($SHA256, $conn);

	// compruebo usuario y password 
	$id_usuario = verificarUsuario($Usuario, $Password);
	if($id_usuario <= 0)	{
		$analizarMuestraRet["RetCodigo"] = 1;
		$analizarMuestraRet["RetDescripcion"] = "Usuario/Password de web-service no válidos.";
		return $analizarMuestraRet;
	}

	// consultar si existe la muestra
	$id_muestra = 0;
	$consulta = mysql_query("SELECT id_muestra FROM muestras WHERE `sha256` = '$SHA256'", $conn);
	if ($registro = mysql_fetch_array($consulta)) {
		$id_muestra = $registro['id_muestra'];
	} else	{
		$analizarMuestraRet["RetCodigo"] = 2;
		$analizarMuestraRet["RetDescripcion"] = "Muestra inexistente.";
		return $analizarMuestraRet;
	}
	
	// ejecutar herramientas
	ejecutarHerramientas($id_muestra);
	
	$analizarMuestraRet["RetCodigo"] = 0;
	$analizarMuestraRet["RetDescripcion"] = "";
	return $analizarMuestraRet;
}

// función consultarMuestra
function consultarMuestra($Usuario = "", $Password = "", $SHA256 = "")	{

	// variables globales
	global $CARPETA_GRAL_MUESTRAS;
	global $conn;

	// parámetros - xxx - revisar
	$Usuario = mysql_real_escape_string($Usuario, $conn);
	$Password = mysql_real_escape_string($Password, $conn);
	$SHA256 = mysql_real_escape_string($SHA256, $conn);
	
	// compruebo usuario y password 
	$id_usuario = verificarUsuario($Usuario, $Password);
	if($id_usuario <= 0)	{
		$consultarMuestraRet["RetCodigo"] = 1;
		$consultarMuestraRet["RetDescripcion"] = "Usuario/Password de web-service no válidos.";
		return $consultarMuestraRet;
	}

	// generales de la muestra
	$consulta = mysql_query("SELECT m.id_muestra, m.fecha_hora, m.tamanio, m.md5, m.sha1, m.sha256, m.sha512, m.estima_malware ".
	 "FROM muestras m ".
	 "WHERE m.sha256 = '$SHA256'", $conn);
	if ($registro = mysql_fetch_array($consulta)) {
		$consultarMuestraRet["FechaHoraIngreso"] = $registro['fecha_hora'];
		$consultarMuestraRet["Bytes"] = $registro['tamanio'];
		$consultarMuestraRet["MD5"] = $registro['md5'];
		$consultarMuestraRet["SHA1"] = $registro['sha1'];
		$consultarMuestraRet["SHA256"] = $registro['sha256'];
		$consultarMuestraRet["SHA512"] = $registro['sha512'];
		$consultarMuestraRet["Estima"] = $registro['estima_malware'];
		$id_muestra = $registro['id_muestra'];
	} else	{
		$consultarMuestraRet["RetCodigo"] = 2;
		$consultarMuestraRet["RetDescripcion"] = "Muestra inexistente.";
		return $consultarMuestraRet;
	}

	// estimación malware
	$consultarMuestraRet["EstimaDetalles"] = "";
	$consulta = mysql_query("SELECT fecha_hora_analisis, fecha_hora_estima, estima_malware, estima_malware_txt ".
	 "FROM estima_malware_log ".
	 "WHERE id_muestra = $id_muestra ".
	 "ORDER BY fecha_hora_analisis DESC", $conn);
	while ($registro = mysql_fetch_array($consulta)) {
		$consultarMuestraRet["EstimaDetalles"] .= "Fecha de análisis/estimación: ".$registro['fecha_hora_analisis']." / ".$registro['fecha_hora_estima']." - Valor estimado: ".$registro['estima_malware']."\n";
		$consultarMuestraRet["EstimaDetalles"] .= $registro['estima_malware_txt']."\n\n";
	}
	
	// análisis/herramientas
	$consultarMuestraRet["ResultadosHerramientas"]= array();
	$cant_resultados = 0;
	$consulta = mysql_query("SELECT h.nombre, h.descripcion, rh.fecha_hora_analisis, rh.fecha_hora_inicio, rh.fecha_hora_fin, rh.invocacion, rh.version, rh.codigo_error, rh.salida, rh.extra ".
	 "FROM muestras m ".
	 "INNER JOIN resultados_herramientas rh ON rh.id_muestra = m.id_muestra ".
	 "INNER JOIN herramientas h ON h.id_herramienta = rh.id_herramienta ".
	 "WHERE m.sha256 = '$SHA256' ".
	 "ORDER BY rh.fecha_hora_analisis DESC, rh.fecha_hora_inicio", $conn);
	while ($registro = mysql_fetch_array($consulta)) {

		// # XXX - sólo último análisis
		// if ($cant_resultados != 0 && $consultarMuestraRet["ResultadosHerramientas"][$cant_resultados-1]['FechaHoraAnalisis'] != $registro['fecha_hora_analisis'])	{
			// break;
		// }

		$arr_tmp = array();
		$arr_tmp['Herramienta'] = $registro['nombre'];
		$arr_tmp['HerramientaDescripcion'] = $registro['descripcion'];
		$arr_tmp['Version'] = preg_replace('/[^[:print:]\s]/', '', $registro['version']);
		$arr_tmp['Comando'] = $registro['invocacion'];
		$arr_tmp['CodigoError'] = $registro['codigo_error'];
		$arr_tmp['Salida'] = preg_replace('/[^[:print:]\s]/', '', $registro['salida']);
		$arr_tmp['FechaHoraAnalisis'] = $registro['fecha_hora_analisis'];
		$arr_tmp['FechaHoraInicio'] = $registro['fecha_hora_inicio'];
		$arr_tmp['FechaHoraFin'] = $registro['fecha_hora_fin'];
		$arr_tmp['Extra'] = $registro['extra'];
		$consultarMuestraRet["ResultadosHerramientas"][$cant_resultados] = $arr_tmp;
		$cant_resultados++;
	}

	// recepciones
	$consultarMuestraRet["Recepciones"] = array();
	$cant_resultados_rec = 0;
	$consulta = mysql_query("SELECT r.fecha_hora, r.nombre_archivo, u.login, u.email ".
	 "FROM muestras m ".
	 "INNER JOIN recepciones r ON r.id_muestra = m.id_muestra ".
	 "INNER JOIN usuarios u ON u.id_usuario = r.id_usuario ".
	 "WHERE m.sha256 = '$SHA256' ".
	 "ORDER BY r.fecha_hora DESC", $conn);
	while ($registro = mysql_fetch_array($consulta)) {
		$arr_tmp = array();
		$arr_tmp['FechaHoraRecepcion'] = $registro['fecha_hora'];
		$arr_tmp['Usuario'] = $registro['login'];
		$arr_tmp['MuestraNombreArchivo'] = $registro['nombre_archivo'];
		$consultarMuestraRet["Recepciones"][$cant_resultados_rec] = $arr_tmp;
		$cant_resultados_rec++;
	}

	// notas
	$consultarMuestraRet["Notas"] = array();
	$cant_resultados_not = 0;
	$consulta = mysql_query("SELECT n.fecha_hora, n.nota, u.login, u.email ".
	 "FROM muestras m ".
	 "INNER JOIN notas n ON n.id_muestra = m.id_muestra ".
	 "INNER JOIN usuarios u ON u.id_usuario = n.id_usuario ".
	 "WHERE m.sha256 = '$SHA256' ".
	 "ORDER BY n.fecha_hora DESC", $conn);
	while ($registro = mysql_fetch_array($consulta)) {
		$arr_tmp = array();
		$arr_tmp['FechaHoraNota'] = $registro['fecha_hora'];
		$arr_tmp['Usuario'] = $registro['login'];
		$arr_tmp['Nota'] = $registro['nota'];
		$consultarMuestraRet["Notas"][$cant_resultados_not] = $arr_tmp;
		$cant_resultados_not++;
	}

	// xxx - cuckoo
	$consultarMuestraRet["CuckooHTML"] = 'NO ANALIZADO';
	if (file_exists("../reportescuckoo/".$id_muestra."/reports/report.html"))	{
		
		$consultarMuestraRet["CuckooHTML"] = '<iframe style="width:100%; height: 600px; border: none;" src="rc.php?id='.$id_muestra.'"></iframe>';

	} elseif (file_exists("../reportescuckoo/".$id_muestra))	{
		$consultarMuestraRet["CuckooHTML"] = 'EN PROCESO';
	}

	
	// retorno ok
	$consultarMuestraRet["RetCodigo"] = 0;
	$consultarMuestraRet["RetDescripcion"] = "";
	return $consultarMuestraRet;
}

// función buscarMuestras
function buscarMuestras($Usuario = "", $Password = "", $TipoBusqueda = "", $PatronBusqueda)	{

	// variables globales
	global $CARPETA_GRAL_MUESTRAS;
	global $ID_USUARIO_ADMIN;
	global $ID_USUARIO_CRON;
	global $ID_USUARIO_INVITADO;
	global $conn;

	// parámetros - xxx - revisar
	$Usuario = mysql_real_escape_string($Usuario, $conn);
	$Password = mysql_real_escape_string($Password, $conn);
	$TipoBusqueda = mysql_real_escape_string($TipoBusqueda, $conn);
	$PatronBusqueda = mysql_real_escape_string($PatronBusqueda, $conn);
	
	// compruebo usuario y password 
	$id_usuario = verificarUsuario($Usuario, $Password);
	if($id_usuario <= 0)	{
		$buscarMuestrasRet["RetCodigo"] = 1;
		$buscarMuestrasRet["RetDescripcion"] = "Usuario/Password de web-service no válidos.";
		return $buscarMuestrasRet;
	}

	// condiciones/búsqueda
	$condiciones = '1=1';
	if ($id_usuario != $ID_USUARIO_ADMIN)	{
		$condiciones = '(m.id_muestra IN (SELECT id_muestra FROM recepciones WHERE privado = 0) OR m.id_muestra IN (SELECT id_muestra FROM recepciones WHERE id_usuario IN ('.$id_usuario.','.$ID_USUARIO_CRON.','.$ID_USUARIO_INVITADO.')))';
	}
	
	if ($TipoBusqueda == 'NOMBRE')	{
		$condiciones .= " AND m.id_muestra IN ".
			"(SELECT id_muestra FROM recepciones WHERE nombre_archivo LIKE '%$PatronBusqueda%') ";
	} elseif ($TipoBusqueda == 'HASH')	{
		$condiciones .= " AND (m.md5 LIKE '%$PatronBusqueda%' OR ".
			"m.sha1 LIKE '%$PatronBusqueda%' OR ".
			"m.sha256 LIKE '%$PatronBusqueda%' OR ".
			"m.sha512 LIKE '%$PatronBusqueda%') ";
	} elseif ($TipoBusqueda == 'FECHA')	{
		//$PatronBusqueda = date("Y-m-d", strtotime(str_replace('/', '-', $PatronBusqueda)));
		$condiciones .= " AND m.id_muestra IN ".
			"(SELECT id_muestra FROM recepciones WHERE DATE(fecha_hora) = '$PatronBusqueda') ";
	} elseif ($TipoBusqueda == 'USUARIO')	{
		// $condiciones .= " AND m.id_muestra IN ".
			// "(SELECT id_muestra FROM recepciones WHERE id_usuario IN ".
			// " (SELECT id_usuario FROM usuarios WHERE ".
			// "  nombres LIKE '%$PatronBusqueda%' OR ".
			// "  apellidos LIKE '%$PatronBusqueda%' OR ".
			// "  email LIKE '%$PatronBusqueda%' OR ".
			// "  login LIKE '%$PatronBusqueda%')) ";
		$tmp_ids = '0';
		$consulta0 = mysql_query("SELECT id_usuario FROM usuarios WHERE ".
			"  nombres LIKE '%$PatronBusqueda%' OR ".
			"  apellidos LIKE '%$PatronBusqueda%' OR ".
			"  email LIKE '%$PatronBusqueda%' OR ".
			"  login LIKE '%$PatronBusqueda%'", $conn);
		while ($registro0 = mysql_fetch_array($consulta0)) {
			$tmp_ids .= ','.$registro0['id_usuario'];
		}
		$condiciones .= " AND m.id_muestra IN (SELECT id_muestra FROM recepciones WHERE id_usuario IN ($tmp_ids)) ";
	} elseif ($TipoBusqueda == 'HERRAMIENTA')	{
		//$condiciones .= " AND m.id_muestra IN ".
		//	"(SELECT id_muestra FROM resultados_herramientas WHERE salida LIKE '%$PatronBusqueda%') ";
		$tmp_ids = '0';
		$consulta0 = mysql_query("SELECT id_muestra FROM resultados_herramientas WHERE salida LIKE '%$PatronBusqueda%'", $conn); // LIMIT 0,20
		while ($registro0 = mysql_fetch_array($consulta0)) {
			$tmp_ids .= ','.$registro0['id_muestra'];
		}
		$condiciones .= " AND m.id_muestra IN ($tmp_ids) ";
	} elseif ($TipoBusqueda == 'NOTA')	{
		//$condiciones .= " AND m.id_muestra IN ".
		//	"(SELECT id_muestra FROM notas WHERE nota LIKE '%$PatronBusqueda%') ";
		$tmp_ids = '0';
		$consulta0 = mysql_query("SELECT id_muestra FROM notas WHERE nota LIKE '%$PatronBusqueda%'", $conn);
		while ($registro0 = mysql_fetch_array($consulta0)) {
			$tmp_ids .= ','.$registro0['id_muestra'];
		}
		$condiciones .= " AND m.id_muestra IN ($tmp_ids) ";

	}

	// resultados generales
	$buscarMuestrasRet["ResultadosBusqueda"] = "";
	$consulta = mysql_query("SELECT m.id_muestra, m.fecha_hora, m.tamanio, m.md5, m.sha1, m.sha256, m.sha512 ".
	 "FROM muestras m ".
	 // xxx
	 //"WHERE ".$condiciones." LIMIT 0,500", $conn);
	 "WHERE ".$condiciones." ORDER BY m.id_muestra DESC LIMIT 0,100 ", $conn);
	while ($registro = mysql_fetch_array($consulta)) {
		$consulta2 = mysql_query("SELECT r.fecha_hora, r.nombre_archivo, u.login ".
		 "FROM recepciones r ".
		 "INNER JOIN usuarios u ON u.id_usuario = r.id_usuario ".
		 "WHERE r.id_muestra = ".$registro['id_muestra']." ".
		 "ORDER BY r.fecha_hora DESC LIMIT 0,1", $conn);
		$registro2 = mysql_fetch_array($consulta2);
		$buscarMuestrasRet["ResultadosBusqueda"] .= $registro['sha256']."|".$registro['fecha_hora']."|".$registro['tamanio']."|".$registro2['fecha_hora']."|".$registro2['nombre_archivo']."|".$registro2['login']."\n";
	}

	$buscarMuestrasRet["RetCodigo"] = 0;
	$buscarMuestrasRet["RetDescripcion"] = "";
	return $buscarMuestrasRet;
}

// función enviarMuestraVT
function enviarMuestraVT($Usuario = "", $Password = "", $SHA256 = "")	{

	// variables globales
	global $CARPETA_GRAL_MUESTRAS;
	global $conn;
	global $VIRUSTOTAL_API_KEY;

	// parámetros - xxx - revisar
	$Usuario = mysql_real_escape_string($Usuario, $conn);
	$Password = mysql_real_escape_string($Password, $conn);
	$SHA256 = mysql_real_escape_string($SHA256, $conn);

	// compruebo usuario y password 
	$id_usuario = verificarUsuario($Usuario, $Password);
	if($id_usuario <= 0)	{
		$enviarMuestraVTRet["RetCodigo"] = 1;
		$enviarMuestraVTRet["RetDescripcion"] = "Usuario/Password de web-service no válidos.";
		return $enviarMuestraVTRet;
	}

	// consultar si existe la muestra
	$id_muestra = 0;
	$consulta = mysql_query("SELECT id_muestra FROM muestras WHERE `sha256` = '$SHA256'", $conn);
	if ($registro = mysql_fetch_array($consulta)) {
		$id_muestra = $registro['id_muestra'];
	} else	{
		$enviarMuestraVTRet["RetCodigo"] = 2;
		$enviarMuestraVTRet["RetDescripcion"] = "Muestra inexistente.";
		return $enviarMuestraVTRet;
	}

	// envío a VirusTotal
	/*
	$api = new VirusTotalAPIV2($VIRUSTOTAL_API_KEY);
	$result = $api->scanFile($CARPETA_GRAL_MUESTRAS.$id_muestra);
	$scanId = $api->getScanID($result);
	*/
	$salidaArr = array();
	$invocacion = "python /opt/VirusTotalApi/vt/vt.py -f ". $CARPETA_GRAL_MUESTRAS.$id_muestra . " 2>&1";
	@exec($invocacion, $salidaArr, $codigo_error);
	$salida = join("</br>", $salidaArr);

	$enviarMuestraVTRet["RetCodigo"] = 0;
	$enviarMuestraVTRet["RetDescripcion"] = "Respuesta VT:</br> $salida";
	return $enviarMuestraVTRet;
}

// función correrMuestraCuckoo
function correrMuestraCuckoo($Usuario = "", $Password = "", $SHA256 = "")	{

	// variables globales
	global $CARPETA_GRAL_MUESTRAS;
	global $CARPETA_GRAL_CUCKOO;
	global $CARPETA_GRAL_CUCKOO_TMP;
	global $CMD_SUBMIT_CUCKOO;
	global $OUTPUT_CUCKOO_EXEC;
	global $CARPETA_CUCKOO_ANALYSES;
	global $conn;

	// parámetros - xxx - revisar
	$Usuario = mysql_real_escape_string($Usuario, $conn);
	$Password = mysql_real_escape_string($Password, $conn);
	$SHA256 = mysql_real_escape_string($SHA256, $conn);

	// compruebo usuario y password 
	$id_usuario = verificarUsuario($Usuario, $Password);
	if($id_usuario <= 0)	{
		$correrMuestraCuckooRet["RetCodigo"] = 1;
		$correrMuestraCuckooRet["RetDescripcion"] = "Usuario/Password de web-service no válidos.";
		return $correrMuestraCuckooRet;
	}

	// consultar si existe la muestra
	$id_muestra = 0;
	$consulta = mysql_query("SELECT m.id_muestra, r.nombre_archivo FROM muestras m INNER JOIN recepciones r ON r.id_muestra = m.id_muestra WHERE `sha256` = '$SHA256'", $conn);
	if ($registro = mysql_fetch_array($consulta)) {
		$id_muestra = $registro['id_muestra'];
		$nombre_archivo = $registro['nombre_archivo'];
	} else	{
		$correrMuestraCuckooRet["RetCodigo"] = 2;
		$correrMuestraCuckooRet["RetDescripcion"] = "Muestra inexistente.";
		return $correrMuestraCuckooRet;
	}

	// xxx - normalizar nombre de archivo
	if (preg_match_all("/([a-zA-Z0-9\_\-]+\.[a-zA-Z0-9\_\-]+)$/", $nombre_archivo, $matches))	{
		$nombre_archivo = $matches[1][0];
	} else	{
		$correrMuestraCuckooRet["RetCodigo"] = 4;
		$correrMuestraCuckooRet["RetDescripcion"] = "Problema al determinar nombre de archivo: ".$nombre_archivo;
		return $correrMuestraCuckooRet;
	}
	
	// copia temporaria
	copy($CARPETA_GRAL_MUESTRAS.$id_muestra, $CARPETA_GRAL_CUCKOO_TMP.$nombre_archivo);
	
	// ejecución de pueba
	exec($CMD_SUBMIT_CUCKOO." ".$CARPETA_GRAL_CUCKOO_TMP.$nombre_archivo." 1>".$OUTPUT_CUCKOO_EXEC." 2>&1");
	$tmp = file_get_contents($OUTPUT_CUCKOO_EXEC);
	$lineas = explode("\n", $tmp);
	$ID = 0;
	foreach($lineas as $linea)	{
		if (preg_match_all("/ID #(\d+)/", $linea, $matches))     {
			$ID = intval($matches[1][0]);
			break;
		}
	}

	$insert_ok = mysql_query("INSERT INTO informes_cuckoo (`id_muestra`, `id_task_cuckoo`) ".
	 "VALUES ('$id_muestra', '$ID')", $conn);
	if (! $insert_ok)	{
		$registrarNotaRet["RetCodigo"] = 5;
		$registrarNotaRet["RetDescripcion"] = "Error al registrar ID_Informe_Cuckoo en bbdd: ".mysql_error();
		return $registrarNotaRet;
	}

	if ($ID == 0)	{
		$correrMuestraCuckooRet["RetCodigo"] = 3;
		$correrMuestraCuckooRet["RetDescripcion"] = "Problema al enviar a cuckoo: ".$tmp;
		return $correrMuestraCuckooRet;
	}
	// linkeo de resultados (xxx - se elimna linkeo a analisis anterior)
	unlink($CARPETA_GRAL_CUCKOO.$id_muestra);
	exec("/bin/ln -s ".$CARPETA_CUCKOO_ANALYSES.$ID." ".$CARPETA_GRAL_CUCKOO.$id_muestra);

	// eliminación de temporario
	// xxx - cuckoo lo lee hasta varios minutos después
	//unlink($CARPETA_GRAL_CUCKOO_TMP.$nombre_archivo);

	$correrMuestraCuckooRet["RetCodigo"] = 0;
	$correrMuestraCuckooRet["RetDescripcion"] = "a ver";
	return $correrMuestraCuckooRet;
}


// función registrarNota
function registrarNota($Usuario = "", $Password = "", $SHA256 = "", $Nota = "")	{

	// variables globales
	global $CARPETA_GRAL_MUESTRAS;
	global $conn;

	// parámetros - xxx - revisar
	$Usuario = mysql_real_escape_string($Usuario, $conn);
	$Password = mysql_real_escape_string($Password, $conn);
	$SHA256 = mysql_real_escape_string($SHA256, $conn);
	$Nota = mysql_real_escape_string($Nota, $conn);

	// compruebo usuario y password 
	$id_usuario = verificarUsuario($Usuario, $Password);
	if($id_usuario <= 0)	{
		$registrarNotaRet["RetCodigo"] = 1;
		$registrarNotaRet["RetDescripcion"] = "Usuario/Password de web-service no válidos.";
		return $registrarNotaRet;
	}

	// consultar si existe la muestra
	$id_muestra = 0;
	$consulta = mysql_query("SELECT id_muestra FROM muestras WHERE `sha256` = '$SHA256'", $conn);
	if ($registro = mysql_fetch_array($consulta)) {
		$id_muestra = $registro['id_muestra'];
	} else	{
		$registrarNotaRet["RetCodigo"] = 2;
		$registrarNotaRet["RetDescripcion"] = "Muestra inexistente.";
		return $registrarNotaRet;
	}

	// registro de nota
	$insert_ok = mysql_query("INSERT INTO notas (`id_usuario`, `id_muestra`, `fecha_hora`, `nota`) ".
	 "VALUES ('$id_usuario', '$id_muestra', NOW(), '$Nota')", $conn);
	if (! $insert_ok)	{
		$registrarNotaRet["RetCodigo"] = 4;
		$registrarNotaRet["RetDescripcion"] = "Error al registrar nota en bbdd: ".mysql_error();
		return $registrarNotaRet;
	}

	$registrarNotaRet["RetCodigo"] = 0;
	$registrarNotaRet["RetDescripcion"] = "";
	return $registrarNotaRet;
}

// función verificarUsuarioWS
function verificarUsuarioWS($Usuario = "", $Password = "")	{

	// variables globales
	global $conn;

	// parámetros - xxx - revisar
	$Usuario = mysql_real_escape_string($Usuario, $conn);
	$Password = mysql_real_escape_string($Password, $conn);

	// compruebo usuario y password 
	$id_usuario = verificarUsuario($Usuario, $Password);
	if($id_usuario <= 0)	{
		$verificarUsuarioWSRet["RetCodigo"] = 1;
		$verificarUsuarioWSRet["RetDescripcion"] = "Usuario/Password de web-service no válidos.";
		return $verificarUsuarioWSRet;
	}

	$consulta = mysql_query("SELECT * FROM usuarios WHERE id_usuario = '$id_usuario'", $conn);
	if ($registro = mysql_fetch_array($consulta)) {
		$verificarUsuarioWSRet["Id"] = $id_usuario;
		$verificarUsuarioWSRet["Usuario"] = $registro['login'];
		$verificarUsuarioWSRet["Email"] = $registro['email'];
	} else	{
		$verificarUsuarioWSRet["RetCodigo"] = 2;
		$verificarUsuarioWSRet["RetDescripcion"] = "Problemas al obtener datos de usuario.";
		return $verificarUsuarioWSRet;
	}

	$verificarUsuarioWSRet["RetCodigo"] = 0;
	$verificarUsuarioWSRet["RetDescripcion"] = "";
	return $verificarUsuarioWSRet;
}


///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////


// función para la validación de usuario/password de web-service
function verificarUsuario($Usuario, $Password)	{
	
	// global de conexión a bbdd
	global $conn;
	
	// AD
	global $ACTIVE_DIRECTORY_SERVER;
	global $ACTIVE_DIRECTORY_DOM_PREFIX;

	//xxx
	// consulta a bbdd por usuario activo original
	$sql_user = "SELECT id_usuario, activo FROM usuarios  WHERE login = '".$Usuario."' AND password = SHA2(SHA2('".$Password."', 256), 256) ";
	$consulta = mysql_query($sql_user, $conn);
	if($registro = mysql_fetch_array($consulta)){
		if(((bool)$registro['activo']) == 1){
			// usuario válidos que puede acceder al ws
			return $registro['id_usuario'];
		}
	}
	// usuario no válido
	return 0;
}

// función para la ejecución de herramientas
function ejecutarHerramientas($id_muestra)	{
	
	// global de conexión a bbdd
	global $conn;

	// variable global de carpeta de muestras
	global $CARPETA_GRAL_MUESTRAS;
	
	// variable global para ejecutar comando para obtener tipo mime
	global $COMMANDO_FILE_MIME;

	// fecha análisis
	$fecha_analisis = date('Y-m-d H:i:s');

	// obtener tipo mime
	$tipos = "'*/*'";
	$tipoArr = array();
	// Hago una copia temporaria
	//copy($CARPETA_GRAL_MUESTRAS.$id_muestra, $CARPETA_GRAL_AE_TMP);
	$invocacion_tipo = sprintf($COMMANDO_FILE_MIME, $CARPETA_GRAL_MUESTRAS.$id_muestra) . " 2>&1";
	@exec($invocacion_tipo, $tipoArr, $codigo_error);
	if (isset($tipoArr[0]))	{
		$tipos .= ",'".$tipoArr[0]."'";
	}
	// Borro la copia
	//@exec("rm ".$CARPETA_GRAL_AE_TMP.$id_muestra);

	// obtener extensiones
	$extensiones = "'*'";
	$sql_extensiones = "SELECT nombre_archivo FROM recepciones WHERE id_muestra = ".$id_muestra;
	$con_extensiones = mysql_query($sql_extensiones, $conn);
	while ($reg_extensiones = mysql_fetch_array($con_extensiones))	{
		if (preg_match_all("/\.([a-zA-Z0-9\_\-]{3,4})$/", $reg_extensiones['nombre_archivo'], $matches))	{
			$extensiones .= ",'".$matches[1][0]."'";
		}
	}

	// para estimación de malware
	$estima_malware = 0;
	$estima_malware_txt = '';
	
	
	// consulta a bbdd por herramientas disponibles
	// xxx - $sql_herramientas = "SELECT id_herramienta, invocacion, version FROM herramientas WHERE estatico = 1";
	$sql_herramientas = "SELECT DISTINCT h.id_herramienta, h.invocacion, h.version FROM herramientas h ".
		"LEFT JOIN herramientas_extensiones_archivo hea ON hea.id_herramienta = h.id_herramienta ".
		"LEFT JOIN herramientas_tipos_mime htm ON htm.id_herramienta = h.id_herramienta ".
		"LEFT JOIN extensiones_archivo ea ON ea.id_extension_archivo = hea.id_extension_archivo ".
		"LEFT JOIN tipos_mime tm ON tm.id_tipo_mime = htm.id_tipo_mime ".
		"WHERE h.estatico = 1 AND ".
		"( tm.tipo_mime IN ($tipos) OR ea.extension_archivo IN ($extensiones) ) ";
	
	$consulta = mysql_query($sql_herramientas, $conn);
	while ($registro = mysql_fetch_array($consulta))	{

		$fecha_hora_inicio = date('Y-m-d H:i:s');

		$salidaArr = array();
		// Hago una copia temporaria
		//copy($CARPETA_GRAL_MUESTRAS.$id_muestra, $CARPETA_GRAL_AE_TMP);
		$invocacion = sprintf($registro['invocacion'], $CARPETA_GRAL_MUESTRAS.$id_muestra) . " 2>&1";
		@exec($invocacion, $salidaArr, $codigo_error);
		$salida = join("\n", $salidaArr);
		// Borro la copia
		//@exec("rm ".$CARPETA_GRAL_ANALESTAT_TMP.$id_muestra);

		$versionArr = array();
		@exec($registro['version'], $versionArr, $codigo_error_version);
		$version = join("\n", $versionArr);

		$salida = mysql_real_escape_string($salida, $conn);
		$version = mysql_real_escape_string($version, $conn);

		$insert_ok = mysql_query("INSERT INTO resultados_herramientas (`id_muestra`, `id_herramienta`, `fecha_hora_analisis`, `fecha_hora_inicio`, `fecha_hora_fin`,
		`invocacion`, `version`, `salida`, `codigo_error`, `secreto`, `extra`) ".
		 "VALUES ('$id_muestra', '".$registro['id_herramienta']."', '$fecha_analisis', '$fecha_hora_inicio', NOW(), '$invocacion', '$version', '$salida', '$codigo_error', '0', '')", $conn);

		// xxx
		//if (! $insert_ok)	{
		//	die("ERROR: ".mysql_error());
		//}

		// xxx - estimación de malware
		if ($registro['id_herramienta'] == 23)	{	// virustotal
			if (strpos($salida, "Scanned on :") !== false)	{
					$cant_VT = substr_count($salida, "|   True    |");
					if ($cant_VT >= 1 && $cant_VT <= 5) {
						$estima_malware += 2;
					}
					if ($cant_VT >= 6 && $cant_VT <= 15) {
						$estima_malware += 7;
					}
					if ($cant_VT >= 16) {
						$estima_malware += 10;
					}
					$estima_malware_txt .= $cant_VT." detecciones en VirusTotal.\n";								
			}
		} elseif ($registro['id_herramienta'] == 4)	{	// Clamav
			if (strpos($salida, "Infected files: ") !== false)	{
				if (strpos($salida, "Infected files: 0") === false)	{
					$estima_malware += 7;
					$estima_malware_txt .= "Detectado por ClamAV.\n";
				}
			}
		} elseif ($registro['id_herramienta'] == 5)	{	// Fprot av
			if (strpos($salida, "Infected files: ") !== false)	{
				if (strpos($salida, "Infected files: 0") === false)	{
					$estima_malware += 7;
					$estima_malware_txt .= "Detectado por Fprot AV.\n";
				}
			}
		} elseif ($registro['id_herramienta'] == 6)	{	// Comodo av
			if (strpos($salida, "Number of Found Viruses: ") !== false)	{
				if (strpos($salida, "Number of Found Viruses: 0") === false)	{
					$estima_malware += 7;
					$estima_malware_txt .= "Detectado por Comodo AV.\n";
				}
			}
		} elseif ($registro['id_herramienta'] == 7)	{	// BitDefender av
			if (strpos($salida, "Infected files     :") !== false)	{
				if (strpos($salida, "Infected files     : 0") === false)	{
					$estima_malware += 7;
					$estima_malware_txt .= "Detectado por BitDefender AV.\n";
				}
			}
		} elseif ($registro['id_herramienta'] == 21)	{	// Pescanner
			if (strpos($salida, "[SUSPICIOUS]") !== false)	{
				$estima_malware += 2;
				$estima_malware_txt .= "Sospechoso por PEScanner.\n";
			}
		} elseif ($registro['id_herramienta'] == 18)	{	// Pev - pescan
			if (strpos($salida, "suspicious") !== false)	{
				$estima_malware += 2;
				$estima_malware_txt .= "Sospechoso por Pev - Pescan.\n";
			}
		} elseif ($registro['id_herramienta'] == 18)	{	// PEframe
			if (strpos($salida, "Sections suspicious") !== false)	{
				$estima_malware += 4;
				$estima_malware_txt .= "Sospechoso por Peframe.\n";
			}
		} elseif ($registro['id_herramienta'] == 22)	{	// Packerid
			if (strpos($salida, "UPX") !== false ||
			 strpos($salida, "Delphi") !== false ||
			 strpos($salida, "Pack") !== false ||
			 strpos($salida, "Protect") !== false ||
			 strpos($salida, "Installer") !== false ||
			 strpos($salida, "PE") !== false ||
			 strpos($salida, "Basic") !== false)	{
				$estima_malware += 3;
				$estima_malware_txt .= "Patrón matcheado por PackerId.\n";
			}
		}
	}
	if ($estima_malware > 10) {
		$estima_malware =10;
	}

	//COMIENZO - Ordena el texto de mayor a menor
	function cmp($a, $b) {
		if (strlen($a) == strlen($b)) {
			return 0;
		}
		return (strlen($a) > strlen($b)) ? -1 : 1;
	}

	$estima_malware_txt_arr = explode("\n", $estima_malware_txt);
	$estima_malware_txt = "";
	uasort($estima_malware_txt_arr, cmp);
	foreach ($estima_malware_txt_arr as $key => $valor) {
		$estima_malware_txt .= $valor . "\n";
	}
	//FIN - Ordena el texto de mayor a menor


	// registro de estimación de malware
	$insert_ok = mysql_query("INSERT INTO estima_malware_log (`id_muestra`, `fecha_hora_analisis`, `fecha_hora_estima`, `estima_malware`, `estima_malware_txt`) ".
	 "VALUES ('$id_muestra', '$fecha_analisis', NOW(), '".$estima_malware."', '".$estima_malware_txt."')", $conn);
	$update_ok = mysql_query("UPDATE muestras SET `estima_malware` = '$estima_malware' WHERE `id_muestra` = '$id_muestra'", $conn);

}


///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

$HTTP_RAW_POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
$HTTP_RAW_POST_DATA = file_get_contents("php://input");
$server->service($HTTP_RAW_POST_DATA);
exit();


?>
