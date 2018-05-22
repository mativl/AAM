<?php

///////////////////////////////////////////////////////////////////////////////

require_once("../includes/configuracion.php");
require_once('../lib-nusoap/nusoap.php');

///////////////////////////////////////////////////////////////////////////////

@session_start();
if (isset($_GET['cerrar']))	{
	@session_destroy();
	header('Location: index.php'); 
	die();
}
if (!isset($_SESSION['WS_USERNAME']) || !isset($_SESSION['WS_PASSWORD']))	{
	if (isset($_POST['login']) && isset($_POST['password']))	{
		$client = new soap_client($WS_URL);
		$client->debug_flag=true;
		$err = $client->getError();
		$param = array ('Usuario'=>$_POST['login'], 'Password'=>$_POST['password']);  
		$result = $client->call("verificarUsuarioWS", $param);
		if ($result['RetCodigo'] == 0)	{
			$_SESSION['WS_USERNAME'] = $_POST['login'];
			$_SESSION['WS_PASSWORD'] = $_POST['password'];
			$WS_USERNAME = $_SESSION['WS_USERNAME'];
			$WS_PASSWORD = $_SESSION['WS_PASSWORD'];
		} else {
			imprimirPaginaLogin("Usuario/password incorrectos.");
			die();
		}
	} else	{
		imprimirPaginaLogin();
		die();
	}
} else	{
	$WS_USERNAME = $_SESSION['WS_USERNAME'];
	$WS_PASSWORD = $_SESSION['WS_PASSWORD'];
}

///////////////////////////////////////////////////////////////////////////////

if (isset($_FILES['archivo']))	{
	$archivoTemp = $_FILES["archivo"]["tmp_name"];
	$archivoNombre = $_FILES["archivo"]["name"];
	$privada = $_POST['privada'];
	if(file_exists($archivoTemp)){
		$client = new soap_client($WS_URL);
		$client->debug_flag=true;
		$err = $client->getError();
		$param = array ('Usuario'=>$WS_USERNAME, 'Password'=>$WS_PASSWORD, 'MuestraNombreArchivo'=>$archivoNombre, 'MuestraContenidoBase64'=>base64_encode(file_get_contents($archivoTemp)), 'MuestraPrivada'=>$privada);  
		$result = $client->call("registrarMuestra", $param);
		$bak_sha256 = $result['SHA256'];
		// nota inicial
		if (isset($_POST['notainicial']))	{
			$param = array ('Usuario'=>$WS_USERNAME, 'Password'=>$WS_PASSWORD, 'SHA256'=>$result['SHA256'], 'Nota'=>$_POST['notainicial']);
			$result = $client->call("registrarNota", $param);
		}
		header('Location: index.php?msj=Archivo subido exitosamente - SHA256: '.$bak_sha256); 
		die();
	}
}

///////////////////////////////////////////////////////////////////////////////

if (isset($_GET['analizar']))	{
	$client = new soap_client($WS_URL);
	$client->debug_flag=true;
	$err = $client->getError();
	$param = array ('Usuario'=>$WS_USERNAME, 'Password'=>$WS_PASSWORD, 'SHA256'=>$_GET['analizar']);
	$result = $client->call("analizarMuestra", $param);
	header('Location: index.php?msj=Análisis realizado exitosamente - SHA256: '.$_GET['analizar']); 
	die();
}

///////////////////////////////////////////////////////////////////////////////

if (isset($_GET['enviarVT']))	{
	$client = new soap_client($WS_URL);
	$client->debug_flag=true;
	$err = $client->getError();
	$param = array ('Usuario'=>$WS_USERNAME, 'Password'=>$WS_PASSWORD, 'SHA256'=>$_GET['enviarVT']);
	$result = $client->call("enviarMuestraVT", $param);
	header('Location: index.php?msj='. urlencode('Muestra enviada exitosamente - SHA256: '.$_GET['enviarVT'].': '.$result['RetDescripcion'])); 
	die();
}


///////////////////////////////////////////////////////////////////////////////

if (isset($_GET['correrCuckoo']))	{
	$client = new soap_client($WS_URL);
	$client->debug_flag=true;
	$err = $client->getError();
	$param = array ('Usuario'=>$WS_USERNAME, 'Password'=>$WS_PASSWORD, 'SHA256'=>$_GET['correrCuckoo']);
	$result = $client->call("correrMuestraCuckoo", $param);
	if (isset($result['RetCodigo']) && $result['RetCodigo'] == 0)	{
		header('Location: index.php?msj=Prueba ejecutada exitosamente - SHA256: '.$_GET['correrCuckoo']); 
	} else	{
		header('Location: index.php?msj=Error: '.$result['RetCodigo'].'-->'.urlencode($result['RetDescripcion'])); 
	}
	die();
}

///////////////////////////////////////////////////////////////////////////////

if (isset($_POST['anotar']))	{
	$client = new soap_client($WS_URL);
	$client->debug_flag=true;
	$err = $client->getError();
	$param = array ('Usuario'=>$WS_USERNAME, 'Password'=>$WS_PASSWORD, 'SHA256'=>$_POST['anotar'], 'Nota'=>$_POST['nota']);
	$result = $client->call("registrarNota", $param);
	header('Location: index.php?msj=Nota registrada exitosamente - SHA256: '.$_POST['anotar']); 
	die();
}

if (isset($_GET['alerta']))	{
	if (isset($_GET['elimina']))	{
		mysql_query("DELETE FROM alertas WHERE id_alerta = ".$_GET['alerta'], $conn);
		header('Location: index.php?alertas=1&msj=Alerta eliminada.'); 
	} else	{
		mysql_query("UPDATE alertas SET activa = '".$_GET['activa']."' WHERE id_alerta = ".$_GET['alerta'], $conn);
		header('Location: index.php?alertas=1&msj=Alerta actualizada.'); 
	}
	die();
}

if (isset($_POST['alta_alerta']))	{
		mysql_query("INSERT INTO alertas (nombre, id_herramienta, horas_desde_analisis, patrones, emails, activa) ".
		 "VALUES ('".$_POST['nombre']."', '".$_POST['id_herramienta']."', '".$_POST['horas_desde_analisis']."', '".$_POST['patrones']."', '".$_POST['emails']."', '0".$_POST['activa']."')", $conn);
		header('Location: index.php?alertas=1&msj=Alerta agregada.'); 
}

if (isset($_GET['usuario']))	{
	if (isset($_GET['elimina']))	{
		mysql_query("DELETE FROM usuarios WHERE id_usuario = ".$_GET['usuario'], $conn);
		header('Location: index.php?usuarios=2&msj=Usuario eliminado.'); 
	}
	die();
}

if (isset($_POST['alta_usuario']))	{
		$password_hash = hash('sha256', $_POST['password']);
		$password_hash = hash('sha256', $password_hash);
		mysql_query("INSERT INTO usuarios (nombres, apellidos, login, password, email, perfil, activo, extra) ".
		 "VALUES ('".$_POST['nombres']."', '".$_POST['apellidos']."', '".$_POST['login']."', '".$password_hash."', '".$_POST['email']."', 0,1,'')", $conn);
		header('Location: index.php?usuarios=2&msj=Usuario agregado.'); 
}

if (isset($_GET['herramienta']))	{
	if (isset($_GET['elimina']))	{
		mysql_query("DELETE FROM herramientas WHERE id_herramienta = ".$_GET['herramienta'], $conn);
		mysql_query("DELETE FROM herramientas_extensiones_archivo WHERE id_herramienta = ".$_GET['herramienta'], $conn);
		mysql_query("DELETE FROM herramientas_tipos_mime WHERE id_herramienta = ".$_GET['herramienta'], $conn);
		header('Location: index.php?herramientas=3&msj=Herramienta eliminada.'); 
	}
	die();
}

if (isset($_POST['alta_herramienta']))	{
		$ea = $_POST['extensiones_archivo2'];
		$tm = $_POST['tipos_mime2'];
		mysql_query("INSERT INTO herramientas (nombre, descripcion, invocacion, version, estatico, dinamico, local, remoto, secreto, extra) ".
			"VALUES ('".$_POST['nombre']."', '".$_POST['descripcion']."', '".$_POST['invocacion']."', '".$_POST['version']."',1,0,1,0,0,'')", $conn);
		$consulta_h = mysql_query("SELECT id_herramienta, nombre, descripcion FROM herramientas WHERE nombre = '".$_POST['nombre']."' AND descripcion = '".$_POST['descripcion']."'", $conn);
		while($registro_h = mysql_fetch_array($consulta_h)){
			$i=0;
			$j=0;
			$cantidad_ea = count($ea);
			$cantidad_tm = count($tm);
			while($j<$cantidad_tm){
				mysql_query("INSERT INTO herramientas_tipos_mime (id_herramienta, id_tipo_mime) ".
			 "VALUES ('".$registro_h['id_herramienta']."','".$tm[$j]."')", $conn);
				$j++;
			}
			while($i<$cantidad_ea){
				mysql_query("INSERT INTO herramientas_extensiones_archivo (id_herramienta, id_extension_archivo) ".
			 "VALUES ('".$registro_h['id_herramienta']."','".$ea[$i]."')", $conn);
				$i++;
			}
		}
		header('Location: index.php?herramientas=3&msj=Herramienta agregada.'); 
}

///////////////////////////////////////////////////////////////////////////////

if (isset($_GET['sha256']))	{

	$client = new soap_client($WS_URL);
	$client->debug_flag=true;
	$err = $client->getError();
	$param = array ('Usuario'=>$WS_USERNAME, 'Password'=>$WS_PASSWORD, 'SHA256'=>$_GET['sha256']);  
	$result = $client->call("consultarMuestra", $param);

	?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<!--
	<h4 class="modal-title" id="detallesModalLabel">Detalles de muestra - SHA256: <?php echo $_GET['sha256']?></h4>
	-->
	<h4 class="modal-title" id="detallesModalLabel">DETALLES DE MUESTRA</h4>
</div>
<div class="modal-body">

	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab_detalles" data-toggle="tab">Información general</a>
		</li>
		<li><a href="#tab_recepciones" data-toggle="tab">Recepciones</a>
		</li>
		<li><a href="#tab_notas" data-toggle="tab">Notas</a>
		</li>
		<li><a href="#tab_estatico" data-toggle="tab">Análisis estático</a>
		</li>
		<li><a href="#tab_dinamico" data-toggle="tab">Análisis dinámico</a>
		</li>
	</ul>

	<div class="tab-content">
	<div class="tab-pane fade in active" id="tab_detalles">	

	<br />
	<h4>Información general</h4>
	<b>Fecha de primer ingreso</b>: <?php echo $result['FechaHoraIngreso']?><br />
	<b>Tamaño (bytes)</b>: <?php echo $result['Bytes']?><br />
	<b>MD5</b>: <?php echo $result['MD5']?><br />
	<b>SHA1</b>: <?php echo $result['SHA1']?><br />
	<b>SHA256</b>: <?php echo $result['SHA256']?><br />
	<b>SHA512</b>: <?php echo $result['SHA512']?><br />
	<hr />
	<b>Estimación</b>:
		<button class="btn btn-<?php echo $ESTIMA_COLORES[$result['Estima']] ?>" type="button">
			<?php echo $ESTIMA_COLORES_TXT[$ESTIMA_COLORES[$result['Estima']]] ?>
			<span class="badge"><?php echo $result['Estima']?></span>
		</button>
		
		<div style="float: right; text-aling: right; margin-top: -10px;">

			<button class="btn btn-secondary btn-xs" type="button" style="width: 120px; margin: 2px 2px 2px 2px;">
				NO ANALIZADO
				<span class="badge">-1</span>
			</button>
		
		<?php
		foreach ($ESTIMA_COLORES_TXT as $estima_color => $estima_texto)	{
			?>
			<button class="btn btn-<?php echo $estima_color ?> btn-xs" type="button" style="width: 120px; margin: 2px 2px 2px 2px;">
				<?php echo $estima_texto ?>
				<span class="badge">
				<?php 
				$numeros = '';
				for ($i=0; $i<=10; $i++)	{
					if ($ESTIMA_COLORES[$i] == $estima_color)	{
						if ($numeros != '')
							$numeros .= " - ";
						$numeros .= "$i";
					}
				}
				echo $numeros;
				?>
				</span>
			</button>
			<?php
			if ((++$ii * 2) == count($ESTIMA_COLORES_TXT))	{
				echo "<br /><div style='width: 120px; margin: 2px 2px 2px 2px; display:inline-block;'></div>";
			}
		}
		?>
		
		</div>
		
	<hr />
<pre>
<?php echo utf8_encode($result['EstimaDetalles'])?>
</pre>
	

	</div>
	<div class="tab-pane fade" id="tab_recepciones">	

	<br />
	<h4>Recepciones (<?php echo count($result['Recepciones'])?>)</h4>
	<?php
	foreach ($result['Recepciones'] as $rec_i => $rec) {
		?>
		<div class="panel-group" id="accordion_rec<?php echo $rec_i?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion_rec<?php echo $rec_i?>" href="#collapseaccordion_rec<?php echo $rec_i?>"><?php echo $result['Recepciones'][$rec_i]['FechaHoraRecepcion']?></a>
					</h4>
				</div>
				<div id="collapseaccordion_rec<?php echo $rec_i?>" class="panel-collapse collapse">
					<div class="panel-body">
						<b>Nombre de archivo: </b><?php echo $result['Recepciones'][$rec_i]['MuestraNombreArchivo']?><br />
						<b>Usuario: </b><?php echo $result['Recepciones'][$rec_i]['Usuario']?><br />
					</div>
				</div>
			</div>
		</div>
		<?php
	}	
	?>

	</div>
	<div class="tab-pane fade" id="tab_estatico">	

	<?php
	$cntRH = 0;
	foreach ($result['ResultadosHerramientas'] as $tmp_cnt) {
		if ($tmp_cnt['FechaHoraAnalisis'] != $tmp_fh)	{
			$tmp_fh = $tmp_cnt['FechaHoraAnalisis'];
			$cntRH++;
		}
	}
	?>
	<br />
	<h4>Análisis estático (<?php echo $cntRH ?>)</h4>
	<?php
	$FechaHoraAnalisis = '';
	$i = 99999;
	$j = 99999;
	foreach ($result['ResultadosHerramientas'] as $res_i => $res) {

		if ($result['ResultadosHerramientas'][$res_i]['FechaHoraAnalisis'] != $FechaHoraAnalisis)	{

			if ($FechaHoraAnalisis != '')	{
				?>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
		
			$FechaHoraAnalisis = $result['ResultadosHerramientas'][$res_i]['FechaHoraAnalisis'];
			$i++;
			?>
		<div class="panel-group" id="Xaccordion_res<?php echo $i?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#Xaccordion_res<?php echo $i?>" href="#Xcollapseaccordion_res<?php echo $i?>"><?php echo $result['ResultadosHerramientas'][$res_i]['FechaHoraAnalisis']?></a>
					</h4>
				</div>
				<div id="Xcollapseaccordion_res<?php echo $i?>" class="panel-collapse collapse">
					<div class="panel-body">
			<?php
		}

		?>
		<div class="panel-group" id="accordion_res<?php echo $res_i?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion_res<?php echo $res_i?>" href="#collapseaccordion_res<?php echo $res_i?>"><?php echo $result['ResultadosHerramientas'][$res_i]['Herramienta']?></a>
					</h4>
				</div>
				<div id="collapseaccordion_res<?php echo $res_i?>" class="panel-collapse collapse">
					<div class="panel-body">
						<b>Herramienta: </b><?php echo $result['ResultadosHerramientas'][$res_i]['Herramienta']?><br />
						<b>Descripción: </b><?php echo utf8_encode($result['ResultadosHerramientas'][$res_i]['HerramientaDescripcion'])?><br />
						<b>Versión: </b><input type="button" class="btn btn-primary btn-xs" onclick="document.getElementById('ver<?php echo $res_i?>').style.display='block'; this.style.display='none';" value="ver versión según herramienta" /><pre id="ver<?php echo $res_i?>" style="display:none"><?php echo htmlentities($result['ResultadosHerramientas'][$res_i]['Version'])?></pre><br />
						<b>Comando ejecutado: </b><?php echo $result['ResultadosHerramientas'][$res_i]['Comando']?><br />
						<b>Inicio de ejecución: </b><?php echo $result['ResultadosHerramientas'][$res_i]['FechaHoraInicio']?><br />
						<b>Fin de ejecución: </b><?php echo $result['ResultadosHerramientas'][$res_i]['FechaHoraFin']?><br />
						<div class="panel-group" id="accordion_res<?php echo $j?>">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion_res<?php echo $j?>" href="#collapseaccordion_res<?php echo $j?>" class="btn btn-primary btn-xs"><font color="white"><?php echo 'Ver Salida de Herramienta'?></font></a>
									</h4>
								</div>
							</div>
						</div>
						<div id="collapseaccordion_res<?php echo $j?>" class="panel-collapse collapse">
							<pre><?php echo htmlentities($result['ResultadosHerramientas'][$res_i]['Salida'])?></pre><br />
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		$j--;
	}
	if (count($result['ResultadosHerramientas']) != 0)	{
	?>

	
							</div>
						</div>
					</div>
				</div>
	<?php
	}
	?>

	</div>
	<div class="tab-pane fade" id="tab_dinamico">	

	<br />
	<h4>Análisis dinámico</h4>
	<?php
	$task_cuckoo = 0;
	$consulta_id_muestra = mysql_query("SELECT id_muestra FROM muestras WHERE sha256 = '".$result['SHA256']."'", $conn);
	while ($registro_id_muestra = mysql_fetch_array($consulta_id_muestra)){
		$consulta_id_informe_cuckoo = mysql_query("SELECT id_task_cuckoo FROM informes_cuckoo WHERE id_muestra = '".$registro_id_muestra['id_muestra']."'", $conn);
		while ($registro_id_informe_cuckoo = mysql_fetch_array($consulta_id_informe_cuckoo)){
			$task_cuckoo = $registro_id_informe_cuckoo['id_task_cuckoo'];
		}
	} 
	?>
	<input type ='button' class="btn btn-primary btn-xs btn-block" value = 'Ver reporte detallado Cuckoo Sandbox' onclick="window.open('http://<?php echo $URL_WEB_CUCKOO?>/analysis/<?php echo $task_cuckoo?>/summary/', 'width=800,height=600');"/><br /> 	
	<?php echo $result['CuckooHTML']?>
	<br />
	<br />

	</div>
	<div class="tab-pane fade" id="tab_notas">	
	
	<br />
	<h4>Notas (<?php echo count($result['Notas'])?>)</h4>
	<?php
	foreach ($result['Notas'] as $not_i => $not) {
		?>
		<div class="panel-group" id="accordion_not<?php echo $not_i?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion_not<?php echo $not_i?>" href="#collapseaccordion_not<?php echo $not_i?>"><?php echo $result['Notas'][$not_i]['FechaHoraNota']?> - <?php echo $result['Notas'][$not_i]['Usuario']?></a>
					</h4>
				</div>
				<div id="collapseaccordion_not<?php echo $not_i?>" class="panel-collapse collapse">
					<div class="panel-body">
						<b>Nota:</b><br /><pre><?php echo utf8_encode($result['Notas'][$not_i]['Nota'])?></pre><br />
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	?>
	<form role="form" action="index.php" method="post">
	<input type="hidden" name="anotar" value="<?php echo $_GET['sha256']?>" />
	<div class="form-group">
		<label>Agregar nueva nota</label>
		<textarea class="form-control" rows="4" name="nota"></textarea>
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-primary">Enviar</button>
	</div>
	</form>

	</div>
	</div>

</div>
<div class="modal-footer">
	<!--
	<button type="button" class="btn btn-primary" onclick="location.href='index.php?analizar=<?php echo $_GET['sha256']?>'">Analizar muestra</button>
	-->
	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div>
	<?php
	die();
}

///////////////////////////////////////////////////////////////////////////////

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>AAM - ANÁLISIS AUTOMÁTICO DE MALWARE</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="css/plugins/dataTables.bootstrap.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0; background-color: #dddddd; border: solid 3px #555555;">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php" style="color: #428BCA; text-shadow: 1px 1px 1px #000, 1px 1px 1px #ccc; "><img src="ub.png" style="height: 60px; margin-top: -20px; float: left;" />&nbsp;&nbsp;&nbsp;<i><b>AAM</b> - ANÁLISIS AUTOMÁTICO DE MALWARE</i></a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">

				<!-- 

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                    </ul>
                    <!-- /.dropdown-messages -- >
                </li>
                <!-- /.dropdown -- >
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-tasks fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-tasks">
                    </ul>
                </li>
                <!-- /.dropdown -- >
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-alerts">
                    </ul>
                    <!-- /.dropdown-alerts -- >
                </li>
                <!-- /.dropdown -- >
				
				-->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
						<?php echo $_SESSION['WS_USERNAME'] ?>
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
						<li><a href="index.php?cerrar=1">Cerrar sesión</a></li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation" style="margin-top: 55px;">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">

                        <li>
                            <a class="active" href="#"><i class="fa fa-files-o fa-fw"></i>&nbsp;&nbsp;Muestras/Archivos<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level in">
                                <li>
                                    <a href="index.php">Listado de Muestras</a>
                                </li>
                                <li>
                                    <a href="index.php#upload">Upload de Muestra</a>
                                </li>
                                <li>
                                    <a href="index.php?alertas=1">Alertas vía mail (Malware)</a>
                                </li>
                                <?php
								if($WS_USERNAME == 'admin'){?>
                                <li>
                                    <a href="index.php?usuarios=2"> ABM de Usuarios</a>
                                </li>
                                <li>
                                    <a href="index.php?herramientas=3"> ABM Herramientas Estáticas</a>
                                </li><?php
                            	}?>
                                <li>
                                    <a href="index.php?estadisticas=1">Estadísticas generales</a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Buscar muestras..." id="PatronBusqueda" value="<?php echo $_GET['PB']?>">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button" onclick="buscar()">
                                    <i class="fa fa-search"></i>
                                </button>
								</span>
                            </div>

                            <div class="input-group custom-search-form" style="font-size: 12px; margin-left: 20px;">
								<div class="radio">
									<label>
										<input name="TipoBusqueda" id="TipoBusqueda1" value="HASH" <?php if (!isset($_GET['TB']) || $_GET['TB'] == 'HASH') { ?> checked="checked" <?php } ?> type="radio">Por md5/sha1/sha256/sha512
									</label>
								</div>
								<div class="radio">
									<label>
										<input name="TipoBusqueda" id="TipoBusqueda2" value="NOMBRE" <?php if ($_GET['TB'] == 'NOMBRE') { ?> checked="checked" <?php } ?> type="radio">Por nombre de archivo
									</label>
								</div>
								<div class="radio">
									<label>
										<input name="TipoBusqueda" id="TipoBusqueda3" value="FECHA" <?php if ($_GET['TB'] == 'FECHA') { ?> checked="checked" <?php } ?> type="radio">Por fecha de recepción
									</label>
								</div>
								<div class="radio">
									<label>
										<input name="TipoBusqueda" id="TipoBusqueda4" value="USUARIO" <?php if ($_GET['TB'] == 'USUARIO') { ?> checked="checked" <?php } ?> type="radio">Por usuario remitente
									</label>
								</div>
								<div class="radio">
									<label>
										<input name="TipoBusqueda" id="TipoBusqueda5" value="HERRAMIENTA" <?php if ($_GET['TB'] == 'HERRAMIENTA') { ?> checked="checked" <?php } ?> type="radio">Por output de herramientas
									</label>
								</div>
								<div class="radio">
									<label>
										<input name="TipoBusqueda" id="TipoBusqueda6" value="NOTA" <?php if ($_GET['TB'] == 'NOTA') { ?> checked="checked" <?php } ?> type="radio">Por notas agregadas
									</label>
								</div>
								
                            </div>
                        </li>
						
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

		<?php
		#######################################################################
		if (isset($_GET['alertas']) && $_GET['alertas'] == '1')	{
			?>
			
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Alertas vía mail (Malware)</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
					<?php if (isset($_GET['msj']))	{?>
					<div style="color: green; font-weight: bold;"><?php echo $_GET['msj']?></div><br /><br />
					<?php } ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Lista de alertas configuradas.<br />Búsqueda de patrones sobre nombres de archivos, notas y salida/output de herramientas configuradas.
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example-2">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nombre</th>
                                            <th>Herramienta</th>
                                            <th>Hs. ant.</th>
                                            <th>Patrones&nbsp;de&nbsp;búsqueda</th>
                                            <th>Emails&nbsp;destinatarios</th>
                                            <th>Activa</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php
										$consulta = mysql_query("SELECT a.id_alerta, a.nombre, a.id_herramienta, a.horas_desde_analisis, a.patrones, a.emails, a.activa, h.nombre as nombre_herramienta FROM alertas a INNER JOIN herramientas h ON a.id_herramienta = h.id_herramienta ORDER BY nombre", $conn);
										while ($registro = mysql_fetch_array($consulta)) {
										?>
										<tr class="odd gradeA">
                                            <td><?php echo $registro['id_alerta'] ?></td>
                                            <td><?php echo $registro['nombre'] ?></td>
                                            <td><?php echo $registro['nombre_herramienta'] ?> (# <?php echo $registro['id_herramienta'] ?>)</td>
                                            <td><?php echo $registro['horas_desde_analisis'] ?></td>
                                            <td><?php echo utf8_encode($registro['patrones']) ?></td>
                                            <td><?php echo $registro['emails'] ?></td>
                                            <td><b><?php echo $registro['activa'] == 1 ? 'SI' : 'NO' ?></b></td>
											<td class="center" width="110">
												<?php
												if ($registro['activa'] == 1)	{
												?>
												<input type="button" class="btn btn-primary btn-xs btn-block" value="Desactivar" onclick="location.href='index.php?alerta=<?php echo $registro['id_alerta'] ?>&activa=0'" />
												<?php
												} else	{
												?>
												<input type="button" class="btn btn-primary btn-xs btn-block" value="Activar" onclick="location.href='index.php?alerta=<?php echo $registro['id_alerta'] ?>&activa=1'"/>
												<?php
												}
												?>
												<input type="button" class="btn btn-primary btn-xs btn-block" value="Eliminar" onclick="location.href='index.php?alerta=<?php echo $registro['id_alerta'] ?>&elimina=1'"/>
											</td>
										</tr>
										<?php
										}
										?>

										<form action="index.php" method="post" id="faa">
										<input type="hidden" name="alta_alerta" value="1" />
										<tr class="odd gradeB"><td colspan="8"><b>Configurar nueva</b></td></tr>
										<tr class="odd gradeB">
											<td>&nbsp;</td>
                                            <td><input name="nombre" type="text" style="width:100% !important;" /></td>
											<td><select name="id_herramienta" style="width:100% !important;">
												<?php
												$consulta2 = mysql_query("SELECT id_herramienta, nombre FROM herramientas ORDER BY id_herramienta", $conn);
												while ($registro2 = mysql_fetch_array($consulta2)) {
													?>
													<option value="<?php echo $registro2['id_herramienta'] ?>"><?php echo $registro2['nombre'] ?></option>
													<?php
													}
												?>
												</select></td>
                                            <td><input name="horas_desde_analisis" type="text" style="width:100% !important;" /></td>
                                            <td><textarea name="patrones" style="width:100% !important;"></textarea></td>
                                            <td><input name="emails" type="text" style="width:100% !important;" /></td>
                                            <td><input name="activa" value="1" type="checkbox" /></td>
											<td>
												<input type="button" class="btn btn-primary btn-xs btn-block" value="Agregar" onclick="document.getElementById('faa').submit()" />
											</td>
										</tr>
										</form>
                                    </tbody>
                                </table>
                            </div>
						</div>
					</div>
				</div>
			</div>			
			<?php

		#######################################################################
		} elseif (isset($_GET['usuarios']) && $_GET['usuarios'] == '2')	{
			?>
			
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Alta, Baja y Modificacion de Usuarios</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
					<?php if (isset($_GET['msj']))	{?>
					<div style="color: green; font-weight: bold;"><?php echo $_GET['msj']?></div><br /><br />
					<?php } ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Alta, Baja y Modificacion de Usuarios.<br />Lista de usuarios registrados en el AAM.
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example-2">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Login</th>
                                            <th>Password</th>
                                            <th>Emails</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php
										$consulta = mysql_query("SELECT u.id_usuario, u.nombres, u.apellidos, u.login, u.password, u.email FROM usuarios u ORDER BY id_usuario", $conn);
										while ($registro = mysql_fetch_array($consulta)) {
										?>
										<tr class="odd gradeA">
                                            <td><?php echo $registro['id_usuario'] ?></td>
                                            <td><?php echo $registro['nombres'] ?></td>
                                            <td><?php echo $registro['apellidos'] ?></td>
                                            <td><?php echo $registro['login'] ?></td>
                                            <td><?php echo $registro['password'] ?></td>
                                            <td><?php echo $registro['emails'] ?></td>
                                            <td><input type="button" class="btn btn-primary btn-xs btn-block" value="Eliminar" onclick="location.href='index.php?usuario=<?php echo $registro['id_usuario'] ?>&elimina=1'"/></td>
										</tr>
										<?php
										}
										?>

										<form action="index.php" method="post" id="faa">
										<input type="hidden" name="alta_usuario" value="1" />
										<tr class="odd gradeB"><td colspan="8"><b>Agregar nuevo</b></td></tr>
										<tr class="odd gradeB">
											<td>&nbsp;</td>
                                            <td><input name="nombres" type="text" style="width:100% !important;" /></td>
                                            <td><input name="apellidos" type="text" style="width:100% !important;" /></td>
                                            <td><input name="login" type="text" style="width:100% !important;" /></td>
                                            <td><input name="password" type="text" style="width:100% !important;" /></td>
                                            <td><input name="email" type="text" style="width:100% !important;" /></td>
											<td>
												<input type="button" class="btn btn-primary btn-xs btn-block" value="Agregar" onclick="document.getElementById('faa').submit()" />
											</td>
										</tr>
										</form>
                                    </tbody>
                                </table>
                            </div>
						</div>
					</div>
				</div>
			</div>			
			<?php
			
		#######################################################################
		} elseif (isset($_GET['herramientas']) && $_GET['herramientas'] == '3')	{
			?>
			
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Alta, Baja y Modificacion de Herramientas</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
					<?php if (isset($_GET['msj']))	{?>
					<div style="color: green; font-weight: bold;"><?php echo $_GET['msj']?></div><br /><br />
					<?php } ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Alta, Baja y Modificacion de herramientas estáticas.<br />Lista de herramientas registrados en el AAM
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example-2">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nombre</th>
                                            <th>Descripcion</th>
                                            <th>Invocacion</th>
                                            <th>Version</th>
                                            <th>Extensiones de archivos</th>
                                            <th>Tipos MIMEs</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php
										$consulta = mysql_query("SELECT h.id_herramienta, h.nombre, h.descripcion, h.invocacion, h.version  FROM herramientas h ORDER BY id_herramienta", $conn);
										while ($registro = mysql_fetch_array($consulta)) {
										?>
										<tr class="odd gradeA">
                                            <td><?php echo $registro['id_herramienta'] ?></td>
                                            <td><?php echo $registro['nombre'] ?></td>
                                            <td><?php echo $registro['descripcion'] ?></td>
                                            <td><?php echo $registro['invocacion'] ?></td>
                                            <td><?php echo $registro['version'] ?></td>
                                            <td><select name="extensiones_archivo" style="width:100% !important;">
												<?php
												$consulta_ea = mysql_query("SELECT ea.id_extension_archivo, ea.extension_archivo FROM extensiones_archivo ea LEFT JOIN herramientas_extensiones_archivo hea ON hea.id_extension_archivo = ea.id_extension_archivo LEFT JOIN herramientas h ON h.id_herramienta = hea.id_herramienta WHERE h.id_herramienta = ".$registro['id_herramienta']. " ORDER BY ea.id_extension_archivo", $conn);
												while ($registro2 = mysql_fetch_array($consulta_ea)) {
													?>
													<option value="<?php echo $registro2['id_extension_archivo'] ?>"><?php echo $registro2['extension_archivo'] ?></option>
													<?php
													}
												?>
												</select></td>
												<td><select name="tipos_mime" style="width:100% !important;">
												<?php
												$consulta_tm = mysql_query("SELECT tm.id_tipo_mime, tm.tipo_mime FROM tipos_mime tm LEFT JOIN herramientas_tipos_mime htm ON htm.id_tipo_mime = tm.id_tipo_mime LEFT JOIN herramientas h ON h.id_herramienta = htm.id_herramienta WHERE h.id_herramienta = ".$registro['id_herramienta']. " ORDER BY tm.id_tipo_mime", $conn);
												while ($registro3 = mysql_fetch_array($consulta_tm)) {
													?>
													<option value="<?php echo $registro3['id_tipo_mime'] ?>"><?php echo $registro3['tipo_mime'] ?></option>
													<?php
													}
												?>
												</select></td>
                                            <td><input type="button" class="btn btn-primary btn-xs btn-block" value="Eliminar" onclick="location.href='index.php?herramienta=<?php echo $registro['id_herramienta'] ?>&elimina=1'"/></td>
										</tr>
										<?php
										}
										?>

										<form action="index.php" method="post" id="faa">
										<input type="hidden" name="alta_herramienta" value="1" />
										<tr class="odd gradeB"><td colspan="8"><b>Agregar nueva</b></td></tr>
										<tr class="odd gradeB">
											<td>&nbsp;</td>
                                            <td><input name="nombre" type="text" style="width:100% !important;" /></td>
                                            <td><input name="descripcion" type="text" style="width:100% !important;" /></td>
                                            <td><input name="invocacion" type="text" style="width:100% !important;" /></td>
                                            <td><input name="version" type="text" style="width:100% !important;" /></td>
                                            <td><select name="extensiones_archivo2[]" multiple style="width:100% !important;">
												<?php
												$consulta4 = mysql_query("SELECT id_extension_archivo, extension_archivo FROM extensiones_archivo ORDER BY id_extension_archivo", $conn);
												while ($registro4 = mysql_fetch_array($consulta4)) {
													?>
													<option value="<?php echo $registro4['id_extension_archivo'] ?>"><?php echo $registro4['extension_archivo'] ?></option>
													<?php
													}
												?>
												</select></td>
											<td><select name="tipos_mime2[]" multiple style="width:100% !important;">
												<?php
												$consulta5 = mysql_query("SELECT id_tipo_mime, tipo_mime FROM tipos_mime ORDER BY id_tipo_mime", $conn);
												while ($registro5 = mysql_fetch_array($consulta5)) {
													?>
													<option value="<?php echo $registro5['id_tipo_mime'] ?>"><?php echo $registro5['tipo_mime'] ?></option>
													<?php
													}
												?>
												</select></td>
											<td>
												<input type="button" class="btn btn-primary btn-xs btn-block" value="Agregar" onclick="document.getElementById('faa').submit()" />
											</td>
										</tr>
										</form>
                                    </tbody>
                                </table>
                            </div>
						</div>
					</div>
				</div>
			</div>			
			<?php
			
		#######################################################################	
		} elseif (isset($_GET['estadisticas']))	{
			?>
			
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Estadísticas generales</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <!-- /.panel-heading -->
                        <div class="panel-body">

						<div id="chart_div_1" style="float: right;"></div>
						
						<?php

						echo "<font size='+1'>";
						$consulta = mysql_query("SELECT max(fecha_hora) as tmp FROM recepciones", $conn);
						if ($registro = mysql_fetch_array($consulta)) { echo "FECHA/HORA DE ÚLTIMA RECEPCIÓN: <b>" . $registro['tmp'] ."</b><br />"; }
						$consulta = mysql_query("SELECT max(fecha_hora_fin) as tmp FROM resultados_herramientas", $conn);
						if ($registro = mysql_fetch_array($consulta)) { echo "FECHA/HORA DE ÚLTIMO ANÁLISIS: <b>" . $registro['tmp'] ."</b><br />"; }
						echo "</font>";

						echo "<hr /><b>";
						
						$consulta = mysql_query("SELECT count(*) as tmp FROM muestras", $conn);
						if ($registro = mysql_fetch_array($consulta)) { echo "Total de muestras registradas: " . $registro['tmp'] ."<br />"; }
						$total_registros = $registro['tmp'];

						$consulta = mysql_query("SELECT count(*) as tmp FROM recepciones", $conn);
						if ($registro = mysql_fetch_array($consulta)) { echo "Total de recepciones registradas: " . $registro['tmp'] ."<br />"; }
						$total_recepciones = $registro['tmp'];

						echo "</b>";
						$consulta = mysql_query("SELECT count(*) as tmp FROM recepciones WHERE id_usuario = 10", $conn);
						if ($registro = mysql_fetch_array($consulta)) { echo "&nbsp;&nbsp;&nbsp;&bull; muestras subidas automáticamente: " . $registro['tmp'] ."<br />"; }
						$consulta = mysql_query("SELECT count(*) as tmp FROM recepciones WHERE id_usuario != 10", $conn);
						if ($registro = mysql_fetch_array($consulta)) { echo "&nbsp;&nbsp;&nbsp;&bull; muestras subidas por usuarios: " . $registro['tmp'] ."<br />"; }
						echo "<b>";

						$consulta = mysql_query("SELECT count(*) as tmp FROM notas", $conn);
						if ($registro = mysql_fetch_array($consulta)) { echo "Total de notas registradas: " . $registro['tmp'] ."<br />"; }

						$consulta = mysql_query("SELECT count(*) as tmp FROM resultados_herramientas", $conn);
						if ($registro = mysql_fetch_array($consulta)) { echo "Total de ejecuciones de herramientas registradas: " . $registro['tmp'] ."<br />"; }

						?>
						</b>
						
						<div class="clearfix"></div>


						<div id="chart_div_3" style="margin-top: -100px; float: left; width:60%;"></div>

						<div id="chart_div_2" style="float: right; width:30%;"></div>

						<!-- <table width="600"> -->
						<?php
						/*
						$i=0;
						$consulta = mysql_query("SELECT SUBSTRING(salida, INSTR(salida, ': ') + 2) as tmp1, count(*) as tmp2 FROM resultados_herramientas WHERE id_herramienta = 1 GROUP BY tmp1 HAVING tmp2 > 20 ORDER BY tmp2 DESC", $conn);
						while ($registro = mysql_fetch_array($consulta)) {
							echo "<tr><td style='border-bottom: 1px solid black;'><!--&nbsp;&nbsp;&nbsp;&bull;--> " . $registro['tmp1'] ."</td><td align='right' style='border-bottom: 1px solid black;'>&nbsp;&nbsp;&nbsp;<b>" . $registro['tmp2'] ."</b></td></tr>";
							if ($i++ <= 10)	{
								echo "\n<script>var c2_tit_".$i."='".$registro['tmp1']."'; var c2_val_".$i."=".$registro['tmp2'].";</script>\n";
							}
						}
						*/
						
						$datos_2 = '';
						//$consulta = mysql_query("SELECT SUBSTRING(salida, INSTR(salida, ': ') + 2) as tmp1, count(*) as tmp2 FROM resultados_herramientas WHERE id_herramienta = 1 GROUP BY tmp1 ORDER BY tmp2 DESC limit 0, 10", $conn);
						$consulta = mysql_query("SELECT SUBSTRING(SUBSTRING(salida, INSTR(salida, ': ') + 2), 1, 4) as tmp1, count(*) as tmp2 FROM resultados_herramientas WHERE id_herramienta = 1 GROUP BY tmp1 ORDER BY tmp2 DESC limit 0, 6", $conn);
						while ($registro = mysql_fetch_array($consulta)) {
								if ($datos_2 != '')
										$datos_2 .= ", \n";
								$datos_2 .= "['".trim($registro['tmp1'])."', ".$registro['tmp2']."]";
						}						

						$datos_3 = '';
						/*
						$consulta = mysql_query("SELECT estima_malware as tmp1, count(*) as tmp2 FROM muestras WHERE estima_malware >= 0 GROUP BY tmp1 ORDER BY tmp2 DESC limit 0, 10", $conn);
						while ($registro = mysql_fetch_array($consulta)) {
								if ($datos_3 != '')
										$datos_3 .= ", \n";
								$datos_3 .= "['".trim($registro['tmp1'])."', ".$registro['tmp2']."]";
						}
						*/

						$consulta = mysql_query("SELECT count(*) as tmp1 FROM muestras WHERE estima_malware = 0", $conn);
						$registro = mysql_fetch_array($consulta);
						$tmp_arr[0] = $registro['tmp1'];
						
						$consulta = mysql_query("SELECT count(*) as tmp1 FROM muestras WHERE estima_malware IN (1,2)", $conn);
						$registro = mysql_fetch_array($consulta);
						$tmp_arr[1] = $registro['tmp1'];

						$consulta = mysql_query("SELECT count(*) as tmp1 FROM muestras WHERE estima_malware IN (3,4)", $conn);
						$registro = mysql_fetch_array($consulta);
						$tmp_arr[2] = $registro['tmp1'];

						$consulta = mysql_query("SELECT count(*) as tmp1 FROM muestras WHERE estima_malware IN (5,6)", $conn);
						$registro = mysql_fetch_array($consulta);
						$tmp_arr[3] = $registro['tmp1'];

						$consulta = mysql_query("SELECT count(*) as tmp1 FROM muestras WHERE estima_malware IN (7,8)", $conn);
						$registro = mysql_fetch_array($consulta);
						$tmp_arr[4] = $registro['tmp1'];

						$consulta = mysql_query("SELECT count(*) as tmp1 FROM muestras WHERE estima_malware IN (9,10)", $conn);
						$registro = mysql_fetch_array($consulta);
						$tmp_arr[5] = $registro['tmp1'];

						$tmp_total = $tmp_arr[0] + $tmp_arr[1] + $tmp_arr[2] + $tmp_arr[3] + $tmp_arr[4] + $tmp_arr[5];
						$datos_3 = '';
						for ($i=0; $i<=5; $i++)	{
							$datos_3 .= number_format(($tmp_arr[5-$i] / $tmp_total) * 100, 2) . ",";
						}
						
						
						?>

						</div>
					</div>
				</div>
			</div>
		</div>


<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load('visualization', '1.0', {'packages':['corechart']});
google.setOnLoadCallback(drawChart);
function drawChart() {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Tipo');
	data.addColumn('number', 'Cantidad');
	data.addRows([
	  ['Repeticiones', <?php echo ($total_recepciones -  $total_registros) ?>],
	  ['Ingresos', <?php echo $total_registros ?>]
	]);
	/*
	var data2 = new google.visualization.DataTable();
	data2.addColumn('string', 'Tipo');
	data2.addColumn('number', 'Cantidad');
	data2.addRows([
	  [c2_tit_1, c2_val_1],
	  [c2_tit_2, c2_val_2],
	  [c2_tit_3, c2_val_3],
	  [c2_tit_4, c2_val_4],
	  [c2_tit_5, c2_val_5],
	  [c2_tit_6, c2_val_6],
	  [c2_tit_7, c2_val_7],
	  [c2_tit_8, c2_val_8],
	  [c2_tit_9, c2_val_9],
	  [c2_tit_10, c2_val_10]
	]);
	*/
	var options1 = {'title':'Recepciones repetidas',
				   'width':400,
				   'height':400};
	/*
	var options2 = {'title':'Tipos de muestra/archivo - Top 10',
				   'width':400,
				   'height':900};
	*/
	var chart1 = new google.visualization.PieChart(document.getElementById('chart_div_1'));
	chart1.draw(data, options1);
	/*
	var chart2 = new google.visualization.ColumnChart(document.getElementById('chart_div_2'));
	chart2.draw(data2, options2);
	*/
}

</script>
		
			<?php
		#######################################################################
		} else	{
			
			?>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Muestras/Archivos</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
					<?php if (isset($_GET['msj']))	{?>
					<div style="color: green; font-weight: bold;"><?php echo $_GET['msj']?></div><br /><br />
					<?php } ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Lista de últimas 100 muestras registradas
							<?php
							if (isset($_GET['TB']) && isset($_GET['PB']))	{
								echo "<br /><b><i>Buscando '".$_GET['PB']."' por ".$_GET['TB']."</i></b>";
							}
							?>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive" id="dCargando">
								<img src="cargando.gif" />
							</div>
							<?php
							if (ob_get_level() == 0) ob_start();
							ob_flush();
							flush();
							usleep(50000);
							?>
                            <div class="table-responsive" id="dTabla" style="display:none">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>SHA256</th>
                                            <th>Fecha de ingreso</th>
											<!--
                                            <th>Tamaño (kb)</th>
											-->
                                            <th>Última recepción</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										
<?php

$TipoBusqueda = '';
$PatronBusqueda = '';
if (isset($_GET['TB']) && isset($_GET['PB']))	{
	$TipoBusqueda = $_GET['TB'];
	$PatronBusqueda = $_GET['PB'];
}

$client = new soap_client($WS_URL);
$client->debug_flag=true;
$err = $client->getError();
$param = array ('Usuario'=>$WS_USERNAME, 'Password'=>$WS_PASSWORD, 'TipoBusqueda'=>$TipoBusqueda, 'PatronBusqueda'=>$PatronBusqueda);  
$resultado = $client->call("buscarMuestras", $param);
$resultado_arr = explode("\n", $resultado['ResultadosBusqueda']);
foreach($resultado_arr as $linea)	{
	$linea_arr = explode("|", $linea);
	if ($linea_arr[0] != '')	{
		?>
                                        <tr class="odd gradeA">
                                            <td><?php echo $linea_arr[0]?></td>
                                            <td><?php echo $linea_arr[1]?></td>
											<!--
                                            <td><?php echo $linea_arr[2] > 1024 ? floor($linea_arr[2]/1024) : number_format($linea_arr[2]/1024,2)?></td>
											-->
                                            <td><font size="-1">
												<b><?php echo $linea_arr[3]?></b>
												<br />
												Archivo: <?php echo strlen($linea_arr[4]) > 20 ? substr($linea_arr[4], 0, 19)."..." : $linea_arr[4]?>
												<br />
												Usuario: <?php echo $linea_arr[5]?>
												</font>
												</td>
                                            <td class="center" width="110">
												<input style="font-weight: bold;" type="button" class="btn btn-primary btn-xs btn-block" value="VER DETALLES" data-toggle="modal" data-target="#detallesModal" onclick="limpia_detallesModal()" href="index.php?sha256=<?php echo $linea_arr[0] ?>" />
												<input style="margin-top: 2px;" type="button" class="btn btn-primary btn-xs btn-block" value="A. Estático" onclick="location.href='index.php?analizar=<?php echo $linea_arr[0] ?>'" />
												<input style="margin-top: 2px;" type="button" class="btn btn-primary btn-xs btn-block" value="A. Dinámico" onclick="location.href='index.php?correrCuckoo=<?php echo $linea_arr[0] ?>'" />
												<input style="margin-top: 2px;" type="button" class="btn btn-primary btn-xs btn-block" value="Enviar a VT" onclick="location.href='index.php?enviarVT=<?php echo $linea_arr[0] ?>'" />
											</td>
                                        </tr>
		<?php
	}
}
?>

                                    </tbody>
                                </table>
                            </div>
							
							<div id="detallesModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="detallesModalLabel" aria-hidden="true">
								<div class="modal-dialog" style="width: 80%; height:100%;">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										</div>
										<div class="modal-body">
											<img src="cargando.gif" /> Cargando información de detalles...
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
										</div>
									</div>
									<!-- /.modal-content -->
								</div>
							</div>
							
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->


			<hr />
			<a name="upload"></a>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Upload de Muestra/Archivo
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-8">
                                    <form role="form" action="index.php" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label>Selección de archivo</label>
                                            <input type="file" name="archivo" />
										</div>
                                        <div class="form-group">
                                            <label>Nota incial (opcional)</label>
											<textarea name="notainicial" class="form-control" rows="4"></textarea>
										</div>

                                        <div class="form-group">
											<input type="checkbox" name="privada" id="privada" value="1" style="width: 20px;" />Registrar envío como <b>PRIVADO</b>
										</div>
										
                                        <div class="form-group">
											<button type="submit" class="btn btn-primary">Enviar archivo</button>
										</div>
    								</form>
								</div>
							</div>
						</div>
					</div>
					<hr />
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					<p>&nbsp;</p>
				</div>
			</div>


        </div>
        <!-- /#page-wrapper -->

		<?php
		}
		?>

    </div>
    <!-- /#wrapper -->

    <!-- jQuery Version 1.11.0 -->
    <script src="js/jquery-1.11.0.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="js/plugins/metisMenu/metisMenu.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="js/plugins/dataTables/jquery.dataTables.js"></script>
    <script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>

	
	<?php
	if (isset($_GET['estadisticas']))	{
	?>

<script>
$(function () {
    $('#chart_div_2').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45
            }
        },
        title: {
            text: 'Totales por tipo de muestra/archivo'
        },
        subtitle: {
            text: 'Tipos de archivos'
        },
        plotOptions: {
            pie: {
                innerSize: 100,
                depth: 45
            }
        },
        series: [{
            name: 'Cantidad de muestras',
            data: [
				/*
                ['Bananas', 8],
                ['Kiwi', 3],
                ['Mixed nuts', 1],
                ['Oranges', 6],
                ['Apples', 8],
                ['Pears', 4],
                ['Clementines', 4],
                ['Reddish (bag)', 1],
                ['Grapes (bunch)', 1]
				*/
				<?php echo $datos_2 ?>
            ]
        }]
    });
});


$(function () {
    $('#chart_div_3').highcharts({
       chart: {
            type: 'column',
            options3d: {
                enabled: true,
                alpha: 10,
                beta: 25,
                depth: 70
            }
        },
        title: {
            text: 'Clasificación de muestras/archivos'
        },
        subtitle: {
            text: 'Clasificaciones '
        },
        plotOptions: {
            column: {
                depth: 25
            }
        },
        xAxis: {
            //categories: Highcharts.getOptions().lang.shortMonths
			categories: ['Malicioso', 'Peligroso', 'Sospechoso', 'Dudoso', 'Ambiguo', 'Limpio']
        },
        yAxis: {
            title: {
                text: null
            }
        },
        series: [{
			color: '#dd3333',
            name: 'Clasificación (%)',
            data: [<?php echo $datos_3 ?>]
        }]
    });
});
</script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

	<?php
	}
	?>
	
	
    <!-- Page-Level Demo Scripts - Tables - Use for reference -->
    <script>
    $(document).ready(function() {
        var oTabla = $('#dataTables-example').dataTable( {
			//"deferRender": true,
			//"bDeferRender": true,
			//"orderClasses": false,
			//"bJQueryUI": true,
			"fnDrawCallback": function(oSettings) {
				$('#dCargando').hide();
				$('#dTabla').show();
			},

			"columnDefs": [
				{ "orderable": false, "targets": [0,3] }
			],
            "language": {
                //"url": "dataTables.spanish.lang"
				//"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
				"url": "Spanish.json"
            }
        } );
		// xxx
		//oTabla.fnSort( [ [3,'desc'] ] );
		oTabla.fnSort( [ [1,'desc'] ] );
    });

	$('body').on('hidden.bs.modal', '.modal', function () {
		$(this).removeData('bs.modal');
	});

	function buscar()	{
		var TipoBusqueda = $("input[name=TipoBusqueda]:checked").val();
		var PatronBusqueda = $("#PatronBusqueda").val();
		location.href='index.php?TB='+TipoBusqueda+'&PB='+encodeURI(PatronBusqueda);
	}

	var detallesModal_html_bak = '';
	function limpia_detallesModal()	{
		if (detallesModal_html_bak == '')	{
			detallesModal_html_bak = document.getElementById('detallesModal').innerHTML;
		}
		document.getElementById('detallesModal').innerHTML = detallesModal_html_bak;
	}

    </script>

</body>

</html>


<?php
///////////////////////////////////////////////////////////////////////////////
function imprimirPaginaLogin($msj="")	{
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AAM - INGRESO</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
</head>
<body>

	<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0; background-color: #dddddd; border: solid 3px #555555;">
		<div class="navbar-header">
			<a class="navbar-brand" href="index.php" style="color: #428BCA; text-shadow: 1px 1px 1px #000, 1px 1px 1px #ccc; "><img src="ub.png" style="height: 60px; margin-top: -20px; float: left;" />&nbsp;&nbsp;&nbsp;<i><b>AAM</b> - ANÁLISIS AUTOMÁTICO DE MALWARE</i></a>
		</div>
	</nav>

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">                
				<div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Control de ingreso</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form" action="index.php" method="post">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Nombre de usuario" name="login" type="text" autofocus id="login" />
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="password" type="password" value="" id="password" />
                                </div>
                                <input type="submit" class="btn btn-lg btn-primary btn-block" value="Ingresar" />
                            </fieldset>

							<br />
							<hr />
							<input type="button" class="btn btn-lg btn-info btn-block" value="Envío Público de Muestras/Archivos" onclick="location.href='publico.php';" />

                        </form>
                    </div>
                </div>
				<?php
				if ($msj != '')	{
					echo "<font color='red'>".$msj."</font>";
				}
				?>
            </div>
        </div>
    </div>
    <script src="js/jquery-1.11.0.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/metisMenu.min.js"></script>
    <script src="js/sb-admin-2.js"></script>
</body>
</html>

<?php
}
///////////////////////////////////////////////////////////////////////////////
?>
