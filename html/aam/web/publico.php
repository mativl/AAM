<?php

///////////////////////////////////////////////////////////////////////////////

require_once("../includes/configuracion.php");
require_once('../lib-nusoap/nusoap.php');

///////////////////////////////////////////////////////////////////////////////

$WS_USERNAME = 'invitado';
$WS_PASSWORD = 'invitado';

///////////////////////////////////////////////////////////////////////////////

if (isset($_FILES['archivo']))	{
	$archivoTemp = $_FILES["archivo"]["tmp_name"];
	$archivoNombre = $_FILES["archivo"]["name"];
	$privada = 0; // $_POST['privada'];
	if(file_exists($archivoTemp)){
		$client = new soap_client($WS_URL);
		$client->debug_flag=true;
		$err = $client->getError();
		$param = array ('Usuario'=>$WS_USERNAME, 'Password'=>$WS_PASSWORD, 'MuestraNombreArchivo'=>$archivoNombre, 'MuestraContenidoBase64'=>base64_encode(file_get_contents($archivoTemp)), 'MuestraPrivada'=>$privada);  
		$result = $client->call("registrarMuestra", $param);
		$bak_sha256 = $result['SHA256'];
		// nota inicial
		$param = array ('Usuario'=>$WS_USERNAME, 'Password'=>$WS_PASSWORD, 'SHA256'=>$result['SHA256'], 'Nota'=>'ENVIO PUBLICO ['.$_SERVER['REMOTE_ADDR'].']: '.$_POST['notainicial']);
		$result = $client->call("registrarNota", $param);
		// análisis estático		
		$param = array ('Usuario'=>$WS_USERNAME, 'Password'=>$WS_PASSWORD, 'SHA256'=>$bak_sha256);
		$result = $client->call("analizarMuestra", $param);

		header('Location: publico.php?sha256='.$bak_sha256); 
		die();
	}
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
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation" style="margin-top: 55px;">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">

                        <li>
                            <a class="active" href="#"><i class="fa fa-files-o fa-fw"></i>&nbsp;&nbsp;Muestras/Archivos<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level in">
                                <li>
                                    <a href="publico.php">Envio Público de Muestra</a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Envío Público de Muestras/Archivos</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">

		<?php if (isset($_GET['sha256']))	{
		
				$client = new soap_client($WS_URL);
				$client->debug_flag=true;
				$err = $client->getError();
				$param = array ('Usuario'=>$WS_USERNAME, 'Password'=>$WS_PASSWORD, 'SHA256'=>$_GET['sha256']);  
				$result = $client->call("consultarMuestra", $param);
		
		?>

			<div style="color: black; font-weight: bold;">Archivo recibido y procesado exitosamente.</div><br />

              <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Información General del Archivo/Muestra Enviado
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-8">

									<b>Fecha de primer ingreso</b>: <?php echo $result['FechaHoraIngreso']?><br />
									<b>Tamaño (bytes)</b>: <?php echo $result['Bytes']?><br />
									<b>MD5</b>: <?php echo $result['MD5']?><br />
									<b>SHA1</b>: <?php echo $result['SHA1']?><br />
									<b>SHA256</b>: <?php echo $result['SHA256']?><br />
									<br />
									<b>Tipo de archivo</b>: 
									<?php 
									foreach ($result['ResultadosHerramientas'] as $tmp_cnt) {
										if ($tmp_cnt['Herramienta'] == 'file')	{
											echo htmlentities(substr($tmp_cnt['Salida'], strpos($tmp_cnt['Salida'], ":")+2));
											break;
										}
									}
									?>
									<br />
									<hr />
									<b>Estimación</b>:
									<button class="btn btn-<?php echo $ESTIMA_COLORES[$result['Estima']] ?>" type="button">
										<?php echo $ESTIMA_COLORES_TXT[$ESTIMA_COLORES[$result['Estima']]] ?>
										<span class="badge"><?php echo $result['Estima']?></span>
									</button>
									
									<div style="float: right; text-aling: right; margin-top: -10px;">
									<?php
									foreach ($ESTIMA_COLORES_TXT as $estima_color => $estima_texto)	{
										?>
										<button class="btn btn-<?php echo $estima_color ?> btn-xs" type="button" style="width: 120px; margin: 2px 2px 2px 2px;">
											<?php echo $estima_texto ?>
											<span class="badge">
											<?php 
											$numeros = '';
											//for ($i=-1; $i<=10; $i++)	{
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
										if ((++$ii * 2) == count($ESTIMA_COLORES_TXT))
											echo "<br />";
									}
									?>
									</div>
									<hr />
<pre>
<?php 
//echo utf8_encode($result['EstimaDetalles'])
echo str_replace("/estimación", "", utf8_encode(substr($result['EstimaDetalles'], 0, strpos($result['EstimaDetalles'], "\n\n"))));

?>
</pre>

									<button type="button" class="btn btn-primary" onclick="location.href='publico.php'">Volver</button>
								</div>
							</div>
						</div>
					</div>
					<hr />
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					<p>&nbsp;</p>
				</div>
			</div>

		<?php } else	{ ?>
            
              <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Envio Público de Muestra
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-8">
                                    <form role="form" action="publico.php" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label>Selección de archivo</label>
                                            <input type="file" name="archivo" />
										</div>
                                        <div class="form-group">
                                            <label>Nota incial (opcional)</label>
											<textarea name="notainicial" class="form-control" rows="4"></textarea>
										</div>

                                        <div class="form-group">
											
											<div class="table-responsive" id="dBoton">
												<button type="submit" class="btn btn-primary" id="" onclick="$('#dBoton').hide(); $('#dCargando').show();">Enviar archivo</button>
											</div>
											
											<div class="table-responsive" id="dCargando" style="display: none;">
												<img src="cargando.gif" />
											</div>

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
				</div>
			</div>
			
		<?php } ?>


        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery Version 1.11.0 -->
    <script src="js/jquery-1.11.0.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="js/plugins/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>

    <!-- Page-Level Demo Scripts - Tables - Use for reference -->
    <script>
    </script>

</body>

</html>


