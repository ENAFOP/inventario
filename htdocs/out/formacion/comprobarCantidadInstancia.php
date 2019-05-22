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
 
    $crearTablaInstancias="CREATE TABLE IF NOT EXISTS instancias_procesos_formativos (
		id INT not null PRIMARY key AUTO_INCREMENT,
		id_curso INT NOT NULL,
		codigo varchar(15),
		fecha_inicio DATE NOT NULL,
		fecha_fin DATE NOT NULL,
		lugar varchar(128),
		cupos INT not null,
		id_financiamiento INT  NOT NULL,
		FOREIGN KEY (id_curso) REFERENCES procesos_formativos (id) ON UPDATE CASCADE,
		FOREIGN KEY (id_financiamiento) REFERENCES app_proyecto (id) ON UPDATE CASCADE,
		UNIQUE KEY codigo (codigo)
		);";
	$resultado0=$manejador->getResult($crearTablaInstancias);
	   if(!$resultado0)
		{
			UI::exitError("comprobarCantidadInstancia: No se pudo crear la tabla","No se pudo crear la tabla  (instancias_procesos_formativos)");
		}
    //veo si se ha dado este curso anteriormente, y devuelvo el número
	//echo "Consultar: ".$consultar;
	$verInstancias="SELECT COUNT(*) FROM instancias_procesos_formativos WHERE id_curso=$proceso;";
	$resultado1=$manejador->getResultArray($verInstancias);
	$cantidad=$resultado1[0]['COUNT(*)'];
	//obtener nombre corto del curso
	$nomCorto="SELECT nombre_corto FROM procesos_formativos WHERE id=$proceso;";
	$resultado2=$manejador->getResultArray($nomCorto);
	$corto=$resultado2[0]['nombre_corto'];

	$arrayRespuesta=array();
	array_push($arrayRespuesta, $cantidad);
	array_push($arrayRespuesta, $corto);
	  
	echo json_encode($arrayRespuesta);
?>