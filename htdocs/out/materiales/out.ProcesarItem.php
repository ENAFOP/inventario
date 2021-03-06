<?php
//    
//    Copyright (C) José Mario López Leiva. marioleiva2011@gmail.com_addre
//    September 2017. San Salvador (El Salvador)
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

include("../../inc/inc.Settings.php");
include("../../inc/inc.Language.php");
include("../../inc/inc.Init.php");
include("../../inc/inc.Extension.php");
include("../../inc/inc.DBInit.php");
include("../../inc/inc.ClassUI.php");
include("../../inc/inc.Authentication.php");
/////////////////////////////        FUNCIONES AUXILIARES ///////////////////////////////////////////////////////
//devuelve el id del item recien creado
 function crearItem($nombreItem,$descripcion,$empresa,$costo,$costoCompra,$origen,$cantidadInicial,$tipo,$dms)
	 {
	 	$res=true;
		$db = $dms->getDB();
		$insertar = "INSERT INTO app_item VALUES(NULL,'$nombreItem','$descripcion','$empresa',$costo,$costoCompra,'$origen','$cantidadInicial','$cantidadInicial','$tipo'); ";
		//echo "INSERTAR: ".$insertar;
		$res1 = $db->getResult($insertar);
		$idCreado=$db->getInsertID();
		return $idCreado;
	 }
	 function registrarGasto($idProyecto,$costoCompra,$dms)
	 {
	 	$res=true;
		$db = $dms->getDB();
		$insertar = "UPDATE gasto_proyecto SET cantidad_gastado = cantidad_gastado + $costoCompra WHERE id_proyecto = $idProyecto";
		//echo "INSERTAR: ".$insertar;
		$res1 = $db->getResult($insertar);
		if (!$res1)
		{
			$res=false;
		}
		return $res;
	 }

	 function insertarUbicacionItem($idItem,$idUbicacion,$dms)
	 {
	 	$res=true;
		$db = $dms->getDB();
		$insertar = "INSERT INTO app_ubicacion_item VALUES(NULL,$idItem,$idUbicacion)";
		//echo "INSERTAR: ".$insertar;
		$res1 = $db->getResult($insertar);
		if (!$res1)
		{
			$res=false;
		}
		return $res;
	 }
////////////////////////////////////////////////////////////////////////////////////
//tabla seeddms.tblattributedefinitions;
 //generan
if ($user->isGuest()) {
	UI::exitError(getMLText("no_permitido"),getMLText("access_denied"));
}

// Check to see if the user wants to see only those documents that are still
// in the review / approve stages.
$showInProcess = false;
if (isset($_GET["inProcess"]) && strlen($_GET["inProcess"])>0 && $_GET["inProcess"]!=0) {
	$showInProcess = true;
}

$orderby='n';
if (isset($_GET["orderby"]) && strlen($_GET["orderby"])==1 ) {
	$orderby=$_GET["orderby"];
}

$tmp = explode('.', basename($_SERVER['SCRIPT_FILENAME']));
$view = UI::factory($theme, $tmp[1], array('dms'=>$dms, 'user'=>$user));

//---------PESTAÑA 1: DATOS GENERALES:
$nombreItem="";
$tipo="";
$descripcion="";
$empresa="";
$costo="";
$costoCompra="";
$origen="";
$cantidadInicial="";
$arrayUbicaciones=array();

/////////////////
if (isset($_POST["tipo"])) 
{
    $tipo=$_POST["tipo"]; 
}
if (isset($_POST["nombreItem"])) 
{
    $nombreItem=$_POST["nombreItem"]; 
}
if (isset($_POST["descripcion"])) 
{
    $descripcion=$_POST["descripcion"]; 
}
if (isset($_POST["empresa"])) 
{
    $empresa=$_POST["empresa"]; 
}
//editado: se calcula costo unitario y no se pide 15 mayo 19
// if (isset($_POST["costo"])) 
// {
//     $costo=$_POST["costo"]; 
// }
if (isset($_POST["costoCompra"])) 
{
    $costoCompra=$_POST["costoCompra"]; 
}
if (isset($_POST["origen"])) 
{
    $origen=$_POST["origen"]; 
}
if (isset($_POST["cantidadInicial"])) 
{
    $cantidadInicial=$_POST["cantidadInicial"]; 
}

$costo=intval($costoCompra/$cantidadInicial);

////////hago metida en BD
$idCreado=crearItem($nombreItem,$descripcion,$empresa,$costo,$costoCompra,$origen,$cantidadInicial,$tipo,$dms);
registrarGasto($origen,$costoCompra,$dms);
if (isset($_POST["ubicacion"])) 
{
    $arrayUbicaciones=$_POST["ubicacion"];

    foreach ($arrayUbicaciones as $idUbicacion) 
    {
      insertarUbicacionItem($idCreado,$idUbicacion,$dms);
    }
}
if($view) 
{
	$view->setParam('orderby', $orderby);
	$view->setParam('showinprocess', $showInProcess);
	$view->setParam('workflowmode', $settings->_workflowMode);
	$view->setParam('cachedir', $settings->_cacheDir);
	$view->setParam('previewWidthList', $settings->_previewWidthList);
	$view->setParam('timeout', $settings->_cmdTimeout);
	$view->setParam('nombreItem', $nombreItem);
	$view->setParam('descripcion', $descripcion);
	$view($_GET);
	exit;
}
?>
