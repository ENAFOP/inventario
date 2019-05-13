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

class SeedDMS_View_TiposConvocatorias extends SeedDMS_Bootstrap_Style 
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

		$host = $this->params['host'];
		$usuarito = $this->params['usuarito'];
		$password = $this->params['password'];
		$base = $this->params['base'];

		$db = $dms->getDB();
		$previewer = new SeedDMS_Preview_Previewer($cachedir, $previewwidth, $timeout);

		$this->htmlStartPage("Ver listados de inscripción para eventos ENAFOP", "skin-blue sidebar-mini sidebar-collapse");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		//$this->contentContainerStart("hoa");
		$this->contentStart();
          
		?>
    <div class="gap-10"></div>
    <div class="row">
    <div class="col-md-12">
    	<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-check"></i> Indicación</h4>
                En esta pantalla se muestran distintos tipo de convocatorias de la ENAFOP, haciendo clic en 
              </div>
      

    <?php
    //en este bloque php va "mi" código
  
 $this->startBoxPrimary("Listado de fichas de confirmación a eventos ENAFOP");
$this->contentContainerStart();
//////INICIO MI CODIGO
	$driver="mysql";
	//echo "intentando acceder con usuario: ".$usuarito;
    $manejador=new SeedDMS_Core_DatabaseAccess($driver,(string) $host,(string)$usuarito,(string)$password,(string)$base);
	$estado=$manejador->connect();
	//echo "Conectado: ".$estado;
	if($estado!=1)
	{
		UI::exitError(getMLText("my_documents"),"Error en la operación base de datos conectar al host $host.<br> Ver lista de confirmaciones");
	}

	$miQuery="SELECT * FROM convocatorias_tipos_eventos ORDER BY id DESC";
echo '<div class="row">';
echo ' <div class="col-md-6">'; //inicio col
	echo '<div class="info-box">
            <span class="info-box-icon bg-green"><i class="glyphicon glyphicon-camera"></i></span>

            <div class="info-box-content">
              <span class="info-box-text"><a href="out.VerInscripciones.php?tipo=eventos">Convocatorias a eventos públicos ENAFOP</a></span>
              <span class="info-box-number">760</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->';

     echo '</div>';//cierre primera col

    echo ' <div class="col-md-6">'; //inicio col 2
    		echo '<div class="info-box">
            <span class="info-box-icon bg-teal"><i class="glyphicon glyphicon-paste"></i></span>

            <div class="info-box-content">
              <span class="info-box-text"><a href="out.VerInscripciones.php?tipo=formaciones">Fichas de inscripción a procesos formativos</a></span>
              <span class="info-box-number">760</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->';

     echo '</div>';//cierre 2 col

   echo '</div>';//cierre row


	
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
			echo '<script src="../../styles/multisis-lte/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>';
        echo '<script src="../../styles/multisis-lte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>';
        echo '<script src="../../styles/multisis-lte/plugins/sorting/moment.min.js"></script>';
        echo '<script src="../../styles/multisis-lte/plugins/sorting/datetime-moment.js"></script>';
        echo '<script src="../../styles/multisis-lte/bower_components/jquery-knob/js/jquery.knob.js"></script>';
		echo '<script src="../../tablasDinamicas.js"></script>';
		$this->htmlEndPage();
	} /* }}} */
}
?>
