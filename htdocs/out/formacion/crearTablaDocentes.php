<?php
////// SE LLAMA DESDE el JS checkRegistro.js; mediante llamada Ajax. COmprueba si un nombre de usuario existe en la BD, devuelve true si existe o false si no (o sea, el nombre está disponible y se puede tomar)
header("Content-type:application/json");
include("../../inc/inc.Settings.php");
include("../../inc/inc.Utils.php");
include("../../inc/inc.Init.php");
include("../../inc/inc.DBInit.php");
/////////////////////////////////////////////////////////////////////////////////////////////////////////// MAIN ////////////////////////////////////////////////////////////////////////////////////////////////
$respuesta=array(); //devuelvo un array con dos elementos: cantidad y nombre del productom o item a consutlar
$proceso = $_GET['proceso']; //obtengo el id del ítem

											  
$settings = new Settings(); //acceder a parámetros de settings.xml con _antes
$driver=$settings->_dbDriver;
$host=$settings->_dbHostname;
$user=$settings->_dbUser;
$pass=$settings->_dbPass;
$base=$settings->_dbDatabase;
$manejador=new SeedDMS_Core_DatabaseAccess($driver,$host,$user,$pass,$base);
  $estado=$manejador->connect();
  //echo "Conectado: ".$estado;
  if($estado!=1)
  {
    echo "comprobarExistencias.php: Error: no se pudo conectar a la BD para modificar a ";
	exit;
  } 
 
    $crearTablaInstancias="CREATE TABLE IF NOT EXISTS docentes (
		id INT not null PRIMARY key AUTO_INCREMENT,
		nombre varchar(60) NOT NULL,
		correo varchar(100) NOT NULL,
		telefono varchar(30) NOT NULL,
		link_cv varchar(256)
		);";
	$resultado0=$manejador->getResult($crearTablaInstancias);
	   if(!$resultado0)
		{
			UI::exitError("comprobarCantidadInstancia: No se pudo crear la tabla","No se pudo crear la tabla  (instancias_procesos_formativos)");
		}

?>