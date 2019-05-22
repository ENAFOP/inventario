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
class SeedDMS_View_ProcesarCurso extends SeedDMS_Bootstrap_Style 
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
		////////del form
		$nombreCurso = $this->params['nombreCurso'];
		$nombreCorto = $this->params['nombreCorto'];
		$modalidad = $this->params['modalidad'];
		



		
		$previewer = new SeedDMS_Preview_Previewer($cachedir, $previewwidth, $timeout);

		$this->htmlStartPage("Curso o diplomado registrado correctamente", "skin-blue sidebar-mini  sidebar-collapse");
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
        <li><a href="out.GestionarFormaciones.php">Gestionar acciones formativas</a></li>
        <li><a href="out.AnadeCursoDiplomado.php">Añadir un nuevo curso o diplomado</a></li>
        <li class="active">Curso o diplomado registrado con éxito</li>
      </ol>

    <div class="gap-10"></div>
    <div class="row">
    <div class="col-md-12">
      

    <?php
    //en este bloque php va "mi" código
  
 $this->startBoxPrimary("Curso o diplomado registrado correctamente");
$this->contentContainerStart();
//////INICIO MI CODIGO
?>
          <div class="box box-default">
            <div class="box-header with-border">
              <i class="fa fa-bullhorn"></i>

              <h3 class="box-title">Curso o diplomado registrado correctamente</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
            
              <div class="callout callout-success">
                <h4>Resumen del curso o diplomado:</h4>
				<ul>
					<li>Nombre: <?php echo $nombreCurso ?></li>
					<li>Nombre corto:  <?php echo $nombreCorto ?></li>
					<li>Modalidad:   <?php echo $modalidad ?></li>
					<li></li>



				</ul>
		     </div>
            </div>
            <!-- /.box-body -->

            <a href="out.RegistroAcademico.php"><b>Retornar al inicio</b></a>
          </div>
          <!-- /.box -->
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
