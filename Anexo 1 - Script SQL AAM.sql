-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Table structure for table `alertas`
--

DROP TABLE IF EXISTS `alertas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alertas` (
  `id_alerta` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `id_herramienta` int(11) NOT NULL,
  `horas_desde_analisis` int(11) NOT NULL DEFAULT '48',
  `patrones` text NOT NULL,
  `emails` varchar(255) NOT NULL DEFAULT '',
  `activa` tinyint(1) NOT NULL DEFAULT '0',
  `extra` text NOT NULL,
  PRIMARY KEY (`id_alerta`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `estima_malware_log`
--

DROP TABLE IF EXISTS `estima_malware_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estima_malware_log` (
  `id_estima_malware_log` int(11) NOT NULL AUTO_INCREMENT,
  `id_muestra` int(11) NOT NULL,
  `fecha_hora_analisis` datetime NOT NULL,
  `fecha_hora_estima` datetime NOT NULL,
  `estima_malware` smallint(6) NOT NULL DEFAULT '-1',
  `estima_malware_txt` text NOT NULL,
  PRIMARY KEY (`id_estima_malware_log`)
) ENGINE=InnoDB AUTO_INCREMENT=26002 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `extensiones_archivo`
--

DROP TABLE IF EXISTS `extensiones_archivo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `extensiones_archivo` (
  `id_extension_archivo` int(11) NOT NULL AUTO_INCREMENT,
  `extension_archivo` varchar(20) NOT NULL DEFAULT '',
  `descripcion` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_extension_archivo`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `extensiones_archivo` (`id_extension_archivo`,`extension_archivo`,`descripcion`) VALUES (1,'*','*');
INSERT INTO `extensiones_archivo` (`id_extension_archivo`,`extension_archivo`,`descripcion`) VALUES (2,'exe','Ejecutable Windows');
INSERT INTO `extensiones_archivo` (`id_extension_archivo`,`extension_archivo`,`descripcion`) VALUES (3,'dll','Librería Windows');
INSERT INTO `extensiones_archivo` (`id_extension_archivo`,`extension_archivo`,`descripcion`) VALUES (4,'doc','Word');
INSERT INTO `extensiones_archivo` (`id_extension_archivo`,`extension_archivo`,`descripcion`) VALUES (5,'docx','Word');
INSERT INTO `extensiones_archivo` (`id_extension_archivo`,`extension_archivo`,`descripcion`) VALUES (6,'xls','Excel');
INSERT INTO `extensiones_archivo` (`id_extension_archivo`,`extension_archivo`,`descripcion`) VALUES (7,'xlsx','Excel');
INSERT INTO `extensiones_archivo` (`id_extension_archivo`,`extension_archivo`,`descripcion`) VALUES (8,'apk','Aplicación Android');
INSERT INTO `extensiones_archivo` (`id_extension_archivo`,`extension_archivo`,`descripcion`) VALUES (9,'swf','Flash');
INSERT INTO `extensiones_archivo` (`id_extension_archivo`,`extension_archivo`,`descripcion`) VALUES (10,'rtf','rtf (rich text format)');
INSERT INTO `extensiones_archivo` (`id_extension_archivo`,`extension_archivo`,`descripcion`) VALUES (11,'html','Página html');
INSERT INTO `extensiones_archivo` (`id_extension_archivo`,`extension_archivo`,`descripcion`) VALUES (12,'htm','Página htm');
INSERT INTO `extensiones_archivo` (`id_extension_archivo`,`extension_archivo`,`descripcion`) VALUES (13,'js','Javascript');
INSERT INTO `extensiones_archivo` (`id_extension_archivo`,`extension_archivo`,`descripcion`) VALUES (14,'elf','ELF');
INSERT INTO `extensiones_archivo` (`id_extension_archivo`,`extension_archivo`,`descripcion`) VALUES (15,'ole2','Ole2');
INSERT INTO `extensiones_archivo` (`id_extension_archivo`,`extension_archivo`,`descripcion`) VALUES (16,'pdf','PDF');
INSERT INTO `extensiones_archivo` (`id_extension_archivo`,`extension_archivo`,`descripcion`) VALUES (17,'ppt','PowerPoint');

--
-- Table structure for table `herramientas`
--

DROP TABLE IF EXISTS `herramientas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `herramientas` (
  `id_herramienta` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `descripcion` text NOT NULL,
  `invocacion` varchar(255) NOT NULL DEFAULT '',
  `version` varchar(255) NOT NULL DEFAULT '',
  `estatico` tinyint(1) NOT NULL DEFAULT '0',
  `dinamico` tinyint(1) NOT NULL DEFAULT '0',
  `local` tinyint(1) NOT NULL DEFAULT '0',
  `remoto` tinyint(1) NOT NULL DEFAULT '0',
  `secreto` tinyint(1) NOT NULL DEFAULT '0',
  `extra` text NOT NULL,
  PRIMARY KEY (`id_herramienta`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (1,'File','Comando \"file\" de linux. File es un comando de los sistemas operativos Unix, que permite detectar el tipo y formato de un archivo. Para lograrlo, analiza el encabezado, el número mágico o bien el contenido que el archivo posea.','/usr//bin/file %s','/usr//bin/file -v',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (2,'String','Comando \"strings\" de linux. Strings es un comando de los sistemas operativos Unix, que permite encontrar e imprimir \"strings\" de texto embebidos en un archivo binario.','/usr/bin/strings --all %s','/usr//bin/strings -v',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (3,'Stat','Comando \"stat\" de linux - comando de los sistemas operativos Unix - stat - display file or file system status.','/usr/bin/stat %s','/usr//bin/stat --version',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (4,'ClamAV','Escaneo con ClamAV, motor antivirus de codigo abierto para detectar Malware','clamscan -r %s','clamscan -V',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (5,'FprotAV','Escaneo con AV F-Prot','/usr/local/bin/fpscan %s','/usr/local/bin/fpscan --version',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (6,'ComodoAV','Escaneo con AV Comodo','/opt/COMODO/cmdscan -v -s %s','/opt/COMODO/cmdscan',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (7,'BitDefenderAV','Escaneo con AV BitDefender','bdscan %s','bdscan -v',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (8,'Maldet (LMD)','Linux Malware Detect es un escaner de Malware',' maldet -a %s','No aplica',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (9,'SSMA','Analizador simple de Malware de ficheros PE y ELF','python3 /opt/SSMA/ssma.py %s','No aplica',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (10,'Mastiff','Automatiza proceso de extransion y analisis de ficheros','mas.py %s','mas.py -v',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (11,'Peframe','Analisis de PE, en busca de paquetes, xor, firmas digitales, mutex, tenicas anti-debug, anti-vm, secciones y funciones sospechosas, etc.','peframe %s','peframe -v',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (13,'Pev - readpe','Parseado basico del fichero PE','readpe %s','readpe -V',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (14,'Pev - readpe dir','Permite ver las direcciones de datos del PE','readpe -d %s','readpe -V',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (15,'Pev - readpe fxi','Permite ver funciones importadas de directorios imporatados de un PE','readpe -i %s','readpe -V',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (16,'Pev - readpe fxe','Permite ver funciones exportadas de directorios exportados de un PE','readpe -e %s','readpe -V',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (17,'Pev - pehash','Calcula el checksum de cada seccion y pieza del PE','pehash -a %s','pehash -V',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (18,'Pev - pescan','Busca caracteristicas sospechosas del PE','pescan -v %s','pescan -V',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (19,'Pev - pestr','Busca strings en el PE, tanto ASCII como Unicode, muestra donde se ubica','pestr -so %s','pestr -V',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (20,'Pev - pepack','Busca paquetes incrustados en un PE','pepack %s','pepack -V',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (21,'Pescanner','Analiza ficheros PE pudiendo entender el comportamiento y clasificar malware','python /opt/sift-files/scripts/pescanner.py %s','No aplica',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (22,'PackerId','Identificar empaquetados en ficheros PE','python /opt/sift-files/scripts/packerid.py %s','No aplica',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (23,'VirtusTotal','API de Virus Total para buscar informe de la muestra mediante su Hash','python /opt/VirusTotalApi/vt/vt.py -fs %s -v','python /opt/VirusTotalApi/vt/vt.py -V',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (24,'TrId','Identificador de ficheros mediante su firmas de binarios','/opt/trid/trid %s','/opt/trid/trid',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (25,'Yara','Utilidad para detectar y clasificar Malware mediante reglas definidas','yara /opt/yara-3.7.1/rules/index1.yar %s','yara -v',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (26,'Ssdeep','Realiza Fuzzy Hashing permitiendo comparar similitudes entre varios hashes','ssdeep %s','ssdeep -V',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (27,'Origami - Cop','Ejecuta controles heuristicos para detectar contenidos peligrosos en ficheros PDF','pdfcop %s','No aplica',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (28,'Origami - Metadata','Busca los metadatos contenidos en un fichero PDF','pdfmetadata %s','No aplica',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (29,'PdfId','Utilidad para detectar actividad sospechosa de un fichero PDF','python /opt/pdftools/pdfid.py %s','python /opt/pdftools/pdfid.py --version',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (30,'Pdf-Parser','','python /opt/pdftools/pdf-parser.py %s','python /opt/pdftools/pdf-parser.py --version',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (31,'PyOlescanner','Utilidad para detectar actividad sospechosa de ficheros Offces','python /opt/pyOLEScanner/pyOLEScanner.py %s','python /opt/pyOLEScanner/pyOLEScanner.py --version',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (32,'OleId','Utilidad para analizar ficheros OLE en busca de potenciales indicativos de actividad sospechosa','python /usr/local/lib/python2.7/dist-packages/oletools/oleid.py %s','python /usr/local/lib/python2.7/dist-packages/oletools/oleid.py',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (33,'Olemeta','Utilidad para extraer metadatos de ficheros OLE','python /usr/local/lib/python2.7/dist-packages/oletools/olemeta.py %s','python /usr/local/lib/python2.7/dist-packages/oletools/olemeta.py',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (34,'Oletimes','Utilidad para extraer de ficheros OLE','python /usr/local/lib/python2.7/dist-packages/oletools/oletimes.py %s','python /usr/local/lib/python2.7/dist-packages/oletools/oletimes.py',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (35,'Olevba','Utilidad para extraer codigo fuente de VBA Macros de ficheros Offices','python /usr/local/lib/python2.7/dist-packages/oletools/olevba.py %s','python /usr/local/lib/python2.7/dist-packages/oletools/olevba.py',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (36,'EXIF','Utilidad para mostrar y cambiar informacion EXIF de ficheros','/opt/exiftool/./exiftool %s','/opt/exiftool/./exiftool -ver',1,0,1,0,0,'');
INSERT INTO `herramientas` (`id_herramienta`,`nombre`,`descripcion`,`invocacion`,`version`,`estatico`,`dinamico`,`local`,`remoto`,`secreto`,`extra`) VALUES (37,'Xortool','Script para analizar ficheros encriptados pudiendo obtener la clave, longitud y descifrar','xortool %s','xortool --version',1,0,1,0,0,'');

--
-- Table structure for table `herramientas_extensiones_archivo`
--

DROP TABLE IF EXISTS `herramientas_extensiones_archivo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `herramientas_extensiones_archivo` (
  `id_herramienta` int(11) NOT NULL,
  `id_extension_archivo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (1,1); /* File - * */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (2,1); /* String - * */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (3,1); /* Stat - * */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (4,1); /* ClamAV - * */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (5,1); /* FprotAV - * */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (6,1); /* ComodoAV - * */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (7,1); /* BitDefenderAV - * */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (8,1); /* Maldet(LMD) - * */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (9,1); /* SSMA - * */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (10,1); /* Mastiff - * */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (11,1); /* Peframe - * */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (12,2); /* Pyew - exe*/
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (12,3); /* Pyew - dll*/
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (12,14); /* Pyew - elf*/
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (12,15); /* Pyew - OLE2*/
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (12,16); /* Pyew - OLE2*/
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (13,2); /* Pev readpe - exe */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (13,3); /* Pev readpe - dll */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (14,2); /* Pev readpe dir - exe */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (14,4); /* Pev readpe dir - dll */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (15,2); /* Pev read fxi - exe */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (15,3); /* Pev read fxi - dll */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (16,2); /* Pev read fxe - exe */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (16,3); /* Pev read fxe - dll */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (17,2); /* Pev pehash - exe */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (17,3); /* Pev pehash - dll */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (18,2); /* Pev pescan - exe */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (18,3); /* Pev pescan - dll */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (19,2); /* Pev pestr - exe */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (19,3); /* Pev pestr - dll */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (20,2); /* Pev pepack - exe*/
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (20,3); /* Pev pepack - dll*/
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (21,2); /* Pescanner - exe*/
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (21,3); /* Pescanner - dll*/
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (22,2); /* PackerId - exe */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (22,3); /* PackerId - dll */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (23,1); /* VirusTotal - * */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (24,1); /* TrId - * */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (25,1); /* Yara - * */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (26,1); /* Ssdeep - * */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (27,16); /* Origami Cop . pdf */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (28,16); /* Origami Metadata - pdf */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (29,16); /* PdfId - pdf */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (30,16); /* PdfPArser - pdf */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (31,4); /* PyOlescanner - doc */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (31,5); /* PyOlescanner - docx */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (31,6); /* PyOlescanner - xls */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (31,7); /* PyOlescanner - docx */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (31,17); /* PyOlescanner - ppt */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (32,4); /* OleId - doc */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (32,6); /* OleId - xls */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (32,17); /* OleId - ppt */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (33,4); /* OleMeta - doc */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (33,6); /* OleMeta - xls */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (33,17); /* OleMeta - ppt */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (34,4); /* OleTimes - doc */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (34,6); /* OleTimes - xls */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (34,17); /* OleTimes - ppt */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (35,5); /* Olevba - docx*/
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (35,7); /* Olevba - xlsx*/
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (36,1); /* EXIF - * */
INSERT INTO `herramientas_extensiones_archivo` (`id_herramienta`,`id_extension_archivo`) VALUES (37,1); /* XorTool - * */

--
-- Table structure for table `herramientas_tipos_mime`
--

DROP TABLE IF EXISTS `herramientas_tipos_mime`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `herramientas_tipos_mime` (
  `id_herramienta` int(11) NOT NULL,
  `id_tipo_mime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (12,2); /* Pyew - ejecutable windows */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (12,3); /* Pyew - - ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (12,4); /* Pyew - binario general */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (13,2); /* Pev read - ejecutable windows */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (13,3); /* Pev read - ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (13,4); /* Pev read - binario general */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (14,2); /* Pev read dir - ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (14,3); /* Pev read dir - ejecutable linux */ 
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (14,4); /* Pev read dir - binario general */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (15,2); /* Pev read fxi - ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (15,3); /* Pev read fxi - ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (15,4); /* Pev read fxi - binario general */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (16,2); /* Pev read fxe - ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (16,3); /* Pev read fxe ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (16,4); /* Pev read fxe - binario general*/
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (17,2); /* Pev pehash - ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (17,3); /* Pev pehash - ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (17,4); /* Pev pehash - binario general*/
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (18,2); /* Pev pescan - ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (18,3); /* Pev pescan - ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (18,4); /* Pev pescan - binario general */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (19,2); /* Pev pestr - ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (19,3); /* Pev pestr - ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (19,4); /* Pev pestr - binario general */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (20,2); /* Pev pepack - ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (20,3); /* Pev pepack - ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (20,4); /* Pev pepack - binario general */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (21,2); /* Pescanner - ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (21,3); /* Pescanner - ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (21,4); /* Pescanner - binario general */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (22,2); /* PackerId - ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (22,3); /* PackerId - ejecutable linux */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (27,5); /* Origami Cop - pdf */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (28,5); /* Origami Metadata - pdf */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (29,5); /* PdfId - pdf */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (30,5); /* PdfParser - pdf */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (31,6); /* PyOlescanner - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (31,7); /* PyOlescanner - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (31,8); /* PyOlescanner - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (31,9); /* PyOlescanner - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (31,10); /* PyOlescanner - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (31,11); /* PyOlescanner - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (31,12); /* PyOlescanner - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (31,13); /* PyOlescanner - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (31,14); /* PyOlescanner - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (31,15); /* PyOlescanner - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (31,16); /* PyOlescanner - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (31,17); /* PyOlescanner - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (31,18); /* PyOlescanner - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (31,19); /* PyOlescanner - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (31,20); /* PyOlescanner - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (31,21); /* PyOlescanner - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (31,22); /* PyOlescanner - Project  */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (31,23); /* PyOlescanner - Access */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (32,6); /* OleId - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (32,7); /* OleId - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (32,8); /* OleId - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (32,9); /* OleId - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (32,10); /* OleId - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (32,11); /* OleId - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (32,12); /* OleId - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (32,13); /* OleId - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (32,14); /* OleId - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (32,15); /* OleId - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (32,16); /* OleId - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (32,17); /* OleId - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (32,18); /* OleId - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (32,19); /* OleId - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (32,20); /* OleId - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (32,21); /* OleId - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (32,22); /* OleId - Project  */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (32,23); /* OleId - Access */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (33,6); /* Olemeta - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (33,7); /* Olemeta - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (33,8); /* Olemeta - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (33,9); /* Olemeta - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (33,10); /* Olemeta - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (33,11); /* Olemeta - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (33,12); /* Olemeta - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (33,13); /* Olemeta - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (33,14); /* Olemeta - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (33,15); /* Olemeta - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (33,16); /* Olemeta - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (33,17); /* Olemeta - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (33,18); /* Olemeta - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (33,19); /* Olemeta - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (33,20); /* Olemeta - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (33,21); /* Olemeta - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (33,22); /* Olemeta - Project  */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (33,23); /* Olemeta - Access */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (34,6); /* Oletimes - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (34,7); /* Oletimes - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (34,8); /* Oletimes - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (34,9); /* Oletimes - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (34,10); /* Oletimes - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (34,11); /* Oletimes - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (34,12); /* Oletimes - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (34,13); /* Oletimes - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (34,14); /* Oletimes - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (34,15); /* Oletimes - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (34,16); /* Oletimes - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (34,17); /* Oletimes - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (34,18); /* Oletimes - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (34,19); /* Oletimes - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (34,20); /* Oletimes - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (34,21); /* Oletimes - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (34,22); /* Oletimes - Project  */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (34,23); /* Oletimes - Access */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (35,6); /* Olevba - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (35,7); /* Olevba - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (35,8); /* Olevba - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (35,9); /* Olevba - Word */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (35,10); /* Olevba - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (35,11); /* Olevba - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (35,12); /* Olevba - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (35,13); /* Olevba - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (35,14); /* Olevba - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (35,15); /* Olevba - Excel */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (35,16); /* Olevba - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (35,17); /* Olevba - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (35,18); /* Olevba - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (35,19); /* Olevba - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (35,20); /* Olevba - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (35,21); /* Olevba - PowerPoint */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (35,22); /* Olevba - Project  */
INSERT INTO `herramientas_tipos_mime` (`id_herramienta`,`id_tipo_mime`) VALUES (35,23); /* Olevba - Access */

--
-- Table structure for table `muestras`
--

DROP TABLE IF EXISTS `muestras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `muestras` (
  `id_muestra` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_hora` datetime NOT NULL,
  `tamanio` bigint(20) NOT NULL,
  `md5` varchar(32) NOT NULL DEFAULT '',
  `sha1` varchar(40) NOT NULL DEFAULT '',
  `sha256` varchar(64) NOT NULL DEFAULT '',
  `sha512` varchar(128) NOT NULL DEFAULT '',
  `extra` text NOT NULL,
  `estima_malware` smallint(6) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id_muestra`),
  KEY `idx1` (`fecha_hora`),
  KEY `idx2` (`md5`),
  KEY `idx3` (`sha1`),
  KEY `idx4` (`sha256`),
  KEY `idx5` (`sha512`)
) ENGINE=InnoDB AUTO_INCREMENT=32054 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notas`
--

DROP TABLE IF EXISTS `notas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notas` (
  `id_nota` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_muestra` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `privado` tinyint(1) NOT NULL DEFAULT '0',
  `secreto` tinyint(1) NOT NULL DEFAULT '0',
  `nota` text NOT NULL,
  PRIMARY KEY (`id_nota`),
  KEY `idx1` (`id_usuario`),
  KEY `idx2` (`id_muestra`),
  KEY `idx3` (`fecha_hora`),
  KEY `idx4` (`nota`(255))
) ENGINE=InnoDB AUTO_INCREMENT=141530 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recepciones`
--

DROP TABLE IF EXISTS `recepciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recepciones` (
  `id_recepcion` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_muestra` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL DEFAULT '',
  `privado` tinyint(1) NOT NULL DEFAULT '0',
  `secreto` tinyint(1) NOT NULL DEFAULT '0',
  `extra` text NOT NULL,
  PRIMARY KEY (`id_recepcion`),
  KEY `idx1` (`id_usuario`),
  KEY `idx2` (`id_muestra`),
  KEY `idx3` (`fecha_hora`),
  KEY `idx4` (`nombre_archivo`)
) ENGINE=InnoDB AUTO_INCREMENT=143952 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resultados_herramientas`
--

DROP TABLE IF EXISTS `resultados_herramientas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resultados_herramientas` (
  `id_resultado_herramienta` int(11) NOT NULL AUTO_INCREMENT,
  `id_muestra` int(11) NOT NULL,
  `id_herramienta` int(11) NOT NULL,
  `fecha_hora_analisis` datetime NOT NULL,
  `fecha_hora_inicio` datetime NOT NULL,
  `fecha_hora_fin` datetime NOT NULL,
  `invocacion` varchar(255) NOT NULL DEFAULT '',
  `version` varchar(255) NOT NULL DEFAULT '',
  `salida` text NOT NULL,
  `secreto` tinyint(1) NOT NULL DEFAULT '0',
  `codigo_error` smallint(5) NOT NULL DEFAULT '0',
  `extra` text NOT NULL,
  PRIMARY KEY (`id_resultado_herramienta`),
  KEY `idx1` (`id_muestra`),
  KEY `idx2` (`id_herramienta`),
  KEY `idx3` (`fecha_hora_analisis`),
  KEY `idx4` (`fecha_hora_inicio`),
  KEY `idx5` (`fecha_hora_fin`),
  FULLTEXT KEY `salida` (`salida`)
) ENGINE=MyISAM AUTO_INCREMENT=882910 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipos_mime`
--

DROP TABLE IF EXISTS `tipos_mime`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipos_mime` (
  `id_tipo_mime` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_mime` varchar(255) NOT NULL DEFAULT '',
  `descripcion` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_tipo_mime`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (1,'*/*','*');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (2,'application/x-dosexec','ejecutable windows');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (3,'application/x-executable','ejecutable linux');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (4,'application/octet-stream','binario general');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (5,'application/pdf','pdf');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (6,'application/msword','word');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (7,'application/vnd.ms-word.document.macroEnabled.12','word');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (8,'application/vnd.ms-word.template.macroEnabled.12','word');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (9,'application/vnd.ms-works','word');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (10,'application/msexcel','excel');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (11,'application/vnd.ms-excel','excel');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (12,'application/vnd.ms-excel.addin.macroEnabled.12','excel');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (13,'application/vnd.ms-excel.sheet.binary.macroEnabled.12','excel');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (14,'application/vnd.ms-excel.sheet.macroEnabled.12','excel');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (15,'application/vnd.ms-excel.template.macroEnabled.12','excel');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (16,'application/vnd.ms-powerpoint','powerpoint');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (17,'application/vnd.ms-powerpoint.addin.macroEnabled.12','powerpoint');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (18,'application/vnd.ms-powerpoint.presentation.macroEnabled.12','powerpoint');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (19,'application/vnd.ms-powerpoint.slide.macroEnabled.12','powerpoint');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (20,'application/vnd.ms-powerpoint.slideshow.macroEnabled.12','powerpoint');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (21,'application/vnd.ms-powerpoint.template.macroEnabled.12','powerpoint');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (22,'application/vnd.ms-project','project');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (23,'application/msaccess','access');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (24,'application/zip','zip');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (25,'application/x-rar','rar');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (26,'application/java-archive','jar/war/apk');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (27,'text/html','html');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (28,'text/plain','txt');
INSERT INTO `tipos_mime` (`id_tipo_mime`,`tipo_mime`,`descripcion`) VALUES (29,'application/javascript','javascript');

--
-- Table structure for table `informes_cuckoo`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `informes_cuckoo` (
  `id_informe_cuckoo` int(11) NOT NULL AUTO_INCREMENT,
  `id_muestra` int(11) NOT NULL,
  `id_task_cuckoo` int(11) NOT NULL,
    PRIMARY KEY (`id_informe_cuckoo`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombres` varchar(80) NOT NULL DEFAULT '',
  `apellidos` varchar(80) NOT NULL DEFAULT '',
  `login` varchar(40) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `perfil` tinyint(1) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `extra` text NOT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;
INSERT INTO `usuarios` (`id_usuario`, `nombres`, `apellidos`, `login`, `password`, `email`, `perfil`, `activo`, `extra`) VALUES
(1, 'Usuario', 'Administrador', 'admin', 'da1a51f975cd750be255d21548eaae1dbaa96ffc997283a6a204f9213a8aca71', 'maiorano@gmail.com', 1, 1, ''),
(2, 'Usuario', 'Test', 'test', 'bdaa62c79c1f74a96290af23ac66a3bd21cf61f841c0df7d1477b9f4df37e6ae', 'maiorano@gmail.com', 0, 1, ''),
(10, 'Para proceso desatendido', 'Cron', 'cron', '5050aaec472538ef22f7fef7bc2c93c47e60f959cff7486bd202bed43e193d4b', 'maiorano@gmail.com', 0, 1, ''),
(50, 'Usuario Invitado', 'Anónimo', 'invitado', '3bff3fd95557bdd4904200474e453b1019df2574a6afa1722e9331a226357c1a', 'invitado@cciber.mil.ar', 0, 1, '');
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
