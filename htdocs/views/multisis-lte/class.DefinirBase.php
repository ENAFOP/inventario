<?php
/**
 * Implementation of MyDocuments view
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx> DMS with modifications of José Mario López Leiva
 * @copyright  Copyright (C) 2017 José Mario López Leiva
 *             marioleiva2011@gmail.com    
 				San Salvador, El Salvador, Central America

 *             
 * @version    Release: @package_version@
 */

/**
 * Include parent class
 */
require_once("class.Bootstrap.php");


/**
 * Include class to preview documents
 */
require_once("SeedDMS/Preview.php");



/**
 * Class which outputs the html page for MyDocuments view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
 /**
 Función que muestra los documentos próximos a caducar de todos los usuarios
 mostrarTodosDocumentos(lista_usuarios,dias)
 -dias: documentos que van a caducar dentro de cúantos días
 */
function existeHost($dms)
	 {
	 	$res=true;
		$db = $dms->getDB();
		$insertar = "INSERT INTO app_grupo VALUES(NULL,'$nombre','$descripcion')";
		//echo "INSERTAR: ".$insertar;
		$res1 = $db->getResult($insertar);
		if (!$res1)
		{
			$res=false;
		}
		return $res;
	 }
class SeedDMS_View_DefinirBase extends SeedDMS_Bootstrap_Style 
{
 /**
 Método que muestra los documentos próximos a caducar sólo de 
 **/
	

	function show() 
	{ /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$orderby = $this->params['orderby'];
		$showInProcess = $this->params['showinprocess'];
		$cachedir = $this->params['cachedir'];
		$workflowmode = $this->params['workflowmode'];
		$previewwidth = $this->params['previewWidthList'];
		$timeout = $this->params['timeout'];

		$db = $dms->getDB();
		$previewer = new SeedDMS_Preview_Previewer($cachedir, $previewwidth, $timeout);

		$this->htmlStartPage(getMLText("mi_sitio"), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		//$this->contentContainerStart("hoa");
		$this->contentStart();
          
		?>
    <div class="gap-10"></div>
    <div class="row">
    <div class="col-md-12">
      

    <?php
    //en este bloque php va "mi" código
  
 $this->startBoxPrimary("Defina los datos de conexión a la base de datos donde se guardan las respuestas a  formularios de la ENAFOP");
$this->contentContainerStart();
//////INICIO MI CODIGO
?>
<div class="box box-success">
	<form class="form-horizontal" name="formularioBase" id="formularioBase" action="out.ProcesarBase.php" method="POST" enctype="multipart/form-data">
            <div class="box-header with-border">
              <h3 class="box-title">Ingrese IP y noombre de la base de datos</h3>
            </div>
            <div class="box-body">
            	 <label for="host" class="col-sm-2 control-label">Host</label>
              <input class="form-control input-lg" type="text" placeholder="ingrese texto"  name="host" id="host" required>
              <br>
               <label for="user" class="col-sm-2 control-label">Usuario en BD</label>
              <input class="form-control input-lg" type="text" placeholder="ingrese texto"  name="user" id="user" required>
              <br>
               <label for="pwd" class="col-sm-2 control-label">Password</label>
              <input class="form-control input-lg" type="text" placeholder="ingrese texto"  name="password" id="password" required>
              <br>
               <label for="base" class="col-sm-2 control-label">Nombre de la base de datos</label>
              <input class="form-control input-lg" type="text" placeholder="ingrese texto"  name="base" id="base" required>
              <br>



              
              
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">guardar</button>
              </div>
            <!-- /.box-body -->
          </div>
      </form>

<?php
 //////FIN MI CODIGO                 
$this->contentContainerEnd();


$this->endsBoxPrimary();
     ?>
	     </div>
		</div>
		</div>

		<?php	
		$this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();
		//$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>