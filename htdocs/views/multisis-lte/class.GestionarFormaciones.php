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
 function contarItems($dms)
   {
    $res=true;
    $db = $dms->getDB();
    $consultar = "SELECT COUNT(*) FROM app_item;";
    //echo "Consultar: ".$consultar;
    $res1 = $db->getResultArray($consultar);
    return $res1[0]['COUNT(*)'];
   }
   function contarUbicaciones($dms)
   {
    $res=true;
    $db = $dms->getDB();
    $consultar = "SELECT COUNT(*) FROM app_ubicacion;";
    //echo "Consultar: ".$consultar;
    $res1 = $db->getResultArray($consultar);
    return $res1[0]['COUNT(*)'];
   }

   function contarDineroFuentes($dms)
   {
    $res=true;
    $db = $dms->getDB();
    $consultar = "SELECT COUNT(*) FROM app_ubicacion;";
    //echo "Consultar: ".$consultar;
    $res1 = $db->getResultArray($consultar);
    return $res1[0]['COUNT(*)'];
   }
class SeedDMS_View_GestionarFormaciones extends SeedDMS_Bootstrap_Style 
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

		$this->htmlStartPage("Gestionar formaciones ENAFOP", "skin-blue sidebar-mini  sidebar-collapse");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		//$this->contentContainerStart("hoa");
		$this->contentStart();
          
		?>
		   <ol class="breadcrumb">
        <li><a href="../out.ViewFolder.php"><i class="fa fa-dashboard"></i> Portal</a></li>
        <li><a href="../out.GestionFormacion.php"><i class="fa fa-wrench"></i> Subsistema de Gestión de la Formación ENAFOP</a></li>
        <li><a href="out.RegistroAcademico.php">Registro Académico</a></li>
        <li class="active">Gestionar actividades formativas</li>
      </ol>


      <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-check"></i> Indicaciones</h4>
               En esta pantalla usted puede:<br>
               1) Añadir una nueva instancia o repetición de un curso o diplomado perteneciente a la Oferta Académica ENAFOP.<br>
               2) Ver el listado de cursos o diplomados realizados.<br>
               3) Registrar un nuevo curso o diplomado que impartirá la ENAFOP.
              </div>
    <div class="gap-10"></div>
    <div class="row">
    <div class="col-md-12">
      

    <?php
    //en este bloque php va "mi" código
  
 $this->startBoxPrimary("Gestión actividades formativas ENAFOP");
$this->contentContainerStart();
//////INICIO MI CODIGO
?>
<div class="row">
        <div class="col-md-3">

        </div> <!-- FIN DE COLUMNA 1 -->

        <div class="col-md-6">
        		
                

                          <div class="box">
                            <div class="box-header">
                              <h3 class="box-title">Acciones sobre procesos formativos</h3>
                            </div> <!-- /.box header-->
                              <div class="box-body">
                          

                                   <div class="row">

                                          <div class="col-md-4">
                                            <a class="btn btn-app" href="out.AnadeInstanciaCurso.php">
                                              <span class="badge bg-teal"><?php print contarItems($dms);?></span>
                                              <i class="fa fa-list-ol"></i> Añadir instancia de curso
                                          </a> 

                                          </div>

                                          <div class="col-md-4">

                                              <a class="btn btn-app" href="out.VerProcesosFormativos.php">
                                                <span class="badge bg-teal"><?php print contarUbicaciones($dms);?></span>
                                                <i class="fa fa-list-alt"></i> Ver procesos
                                             </a>

                                          </div>

                                          <div class="col-md-4">
                                              <a class="btn btn-app" href="out.AnadeCursoDiplomado.php">
                                                <span class="badge bg-teal"><?php print contarDineroFuentes($dms);?></span>
                                                <i class="fa fa-usd"></i> Registrar nueva acción formativa
                                             </a>

                                          </div>
                                   </div> 
                                                        
                              </div>
                        <!-- /.box ody -->
                      </div>
                      <!-- /.box -->
      

            <!-- /.fila -->
        </div> <!-- FIN DE COLUMNA INTERNA DE LA CAJA -->


        <div class="col-md-3">

        </div> <!-- FIN DE COLUMNA 3 -->
</div> <!-- FIN DE FILA -->

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
