<?php  
include("../../inc/inc.Settings.php");
include("../../inc/inc.Utils.php");
include("../../inc/inc.Init.php");
include("../../inc/inc.DBInit.php");

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
    echo "entregados por grupo.php: Error: no se pudo conectar a la BD";
	exit;
  } 
 $idItem=$_GET["idItem"];
$arrayidsReceptores=array(); //ids de todos los grupos receptores.
$arrayNombresReceptores=array(); //ids de todos los grupos receptores.
$arrayCantidades=array(); //se corresponde con un id, cuantos items de ese id se entregaron al grupo.
$arrayMacizo=array();
//////// querys
$gruposReceptores="SELECT id, nombre FROM app_grupos_receptores;";
//echo "query: ".$gruposReceptores;
$resultado1=$manejador->getResultArray($gruposReceptores);
foreach ($resultado1 as $fila) 
{
	$arrayTmp=array();
	$idReceptor=$fila['id'];	
	$cantidadEntregada="SELECT SUM(cantidad_variada) FROM app_transaccion WHERE tipo_transaccion=1 AND id_item=$idItem AND id_grupo_receptor=$idReceptor AND (fecha BETWEEN '2018-01-01' AND '2020-01-01');";
	//echo "cantidad enregada:".$cantidadEntregada;
	  $resultado2=$manejador->getResultArray($cantidadEntregada);
	  $valor=$resultado2[0]['SUM(cantidad_variada)'];
	  array_push($arrayTmp, $fila['nombre']);
	  array_push($arrayTmp, $resultado2[0]['SUM(cantidad_variada)']);
	  array_push($arrayMacizo, $arrayTmp);
}

echo json_encode($arrayMacizo);

?>