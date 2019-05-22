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
 function crearCurso($nombre,$nombreCorto,$modalidad,$duracion,$categoriaCargo/*esto es array*/,$metodologia,$evaluacion,$dms)
	 {
	 	$res=true;
		$db = $dms->getDB();
		//crear tablas si no existemn
		$crearCurso="CREATE TABLE IF NOT EXISTS procesos_formativos (
		id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
		nombre_corto VARCHAR(10) NOT NULL,
		nombre VARCHAR(128) NOT NULL,
		modalidad ENUM('Presencial','Semipresencial','Virtual'),
		duracion INT NOT NULL,
		metodologia VARCHAR(512) NOT NULL,
		evaluacion VARCHAR(512) NOT NULL
		);";
		$res0 = $db->getResult($crearCurso);
		$insertarCurso = "INSERT INTO procesos_formativos VALUES(NULL,'$nombreCorto','$nombre','$modalidad',$duracion,'$metodologia','$evaluacion')";
		
		$res1 = $db->getResult($insertarCurso);
		$idCreado=$db->getInsertID();
		////////////// creo tabla que relaciona curso con perfiles a los que va dirigido
		$crearCategoriasCursos="CREATE TABLE IF NOT EXISTS categorias_procesos_formativos (
		id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
		id_proceso_formativo INT NOT NULL,
		categoria ENUM('Personal Técnico','Personal Gerencial','Personal Directivo','Personal Administrativo y de Apoyo'),
		FOREIGN KEY (id_proceso_formativo) REFERENCES procesos_formativos(id) ON DELETE CASCADE ON UPDATE CASCADE
		);";
		$res2 = $db->getResult($crearCategoriasCursos);
		foreach ($categoriaCargo as $catego) 
		{
		$insertarCategoriaCurso = "INSERT INTO categorias_procesos_formativos VALUES(NULL,$idCreado,'$catego')";
		$res3 = $db->getResult($insertarCategoriaCurso);

		}
		
	 }

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

//---------PESTAÑA 1: DATOS GENERALES: recibo un array de cada uno.
$arrayNuevaCantidad=array();
$nombreCorto=""; 
$nombreCurso=""; 
$modalidad="";
$duracion="";
$categoria=array();
$objetivo="";
$metodologia=""; //1 si es quitar y 2 si es añadir
$evaluacion="";

/////////////////
    if (isset($_POST["nombreCurso"])) //puede tomar valor "sumar" o restar"
		{
		    $nombreCurso=$_POST["nombreCurso"]; 
		}
		if (isset($_POST["nombreCorto"])) //puede tomar valor "sumar" o restar"
		{
		    $nombreCorto=$_POST["nombreCorto"]; 
		}
		if (isset($_POST["modalidad"])) 
		{
		    $modalidad=$_POST["modalidad"]; 
		}
		if (isset($_POST["duracion"])) 
		{
		    $duracion=$_POST["duracion"]; 
		}
		if (isset($_POST["categoria"])) 
		{
		    $categoria=$_POST["categoria"]; 
		}
		if (isset($_POST["metodologia"])) 
		{
		    $metodologia=$_POST["metodologia"]; 
		}
		if (isset($_POST["evaluacion"])) 
		{
		    $evaluacion=$_POST["evaluacion"]; 
		}

        crearCurso($nombreCurso,$nombreCorto,$modalidad,$duracion,$categoria/*esto es array*/,$metodologia,$evaluacion,$dms);

////////hago metida en BD
if($view) 
{
	$view->setParam('orderby', $orderby);
	$view->setParam('showinprocess', $showInProcess);
	$view->setParam('workflowmode', $settings->_workflowMode);
	$view->setParam('cachedir', $settings->_cacheDir);
	$view->setParam('previewWidthList', $settings->_previewWidthList);
	$view->setParam('timeout', $settings->_cmdTimeout);
	$view->setParam('nombreCurso', $nombreCurso);
	$view->setParam('nombreCorto', $nombreCorto);
	$view->setParam('modalidad', $modalidad);
	

	$view($_GET);
	exit;
}
?>
