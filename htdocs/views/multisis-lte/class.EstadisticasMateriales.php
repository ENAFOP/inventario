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
function imprimirItems($dms)
{

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
    echo "EstadisticasMateriales.php: Error: no se pudo conectar a la BD";
	exit;
  } 
 
  /////////////// CONTEO DE INACCIONES
  $items="SELECT * FROM app_item";
  $resultado1=$manejador->getResultArray($items);
  $listaItems=array();
  echo '<select class="form-control chzn-select" id="itemElegido" name="itemElegido" >';
  echo "<option disabled selected value>Seleccione el artículo del que desea ver estadísticas</option>";
  foreach ($resultado1 as $r) 
  {
  	echo "<option value=\"". $r['id']."\">".$r['nombre']."</option>";
  }
  echo "</select>";
  ////////////////////// EL SELECT
  

  
}// fin de imprimir departamentos




///**********************************************************************************
class SeedDMS_View_EstadisticasMateriales extends SeedDMS_Bootstrap_Style 
{
//recibe: un string indicando parentesco (M,P,Ma,Pa,H,A,T,Pr,O,Na)
//devuelve: cantidad de esos parentescos que hay en la base, viviendo en el hogar y fuera 




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
		$data = $this->params['data'];
		$db = $dms->getDB();
		$previewer = new SeedDMS_Preview_Previewer($cachedir, $previewwidth, $timeout);
			$this->htmlAddHeader('<link href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css" rel="stylesheet">'."\n", 'css');
		$this->htmlAddHeader('<link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">'."\n", 'css');
		//añado Header para incluir este gràfico de lineas
		$this->htmlAddHeader('<script type="text/javascript" src="../../styles/'.$this->theme.'/highcharts/modules/exporting.js"></script>');
		$this->htmlAddHeader('<script type="text/javascript" src="../../styles/'.$this->theme.'/highcharts/modules/export-data.js"></script>');
		//$this->htmlAddHeader('<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>');


		$this->htmlStartPage("Cantidad de artículos ENAFOP entregados", "skin-blue sidebar-mini sidebar-collapse");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		//$this->contentContainerStart("hoa");
		$this->contentStart();
          
		?>
    <div class="gap-10"></div>
    <div class="row">
    <div class="col-md-12">

    <ol class="breadcrumb">
        <li><a href="../out.ViewFolder.php"><i class="fa fa-dashboard"></i> Portal</a></li>
        <li><a href="../out.GestionInterna.php"><i class="fa fa-wrench"></i> Subsistema de Gestión Interna ENAFOP</a></li>
        <li><a href="out.Materiales.php">Subsistema de gestión de material e inventario</a></li>
        <li><a href="out.SeleccionEstadisticasMateriales.php">Estadísticas de materiales</a></li>
        <li class="active">Estadísticas por artículo individual</li>
      </ol>
      
<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-check"></i> Significado de este gráfico</h4>
               Estos gráficos los comportamientos (acciones e inacciones) más comunes en los jóvenes.
              </div>
    <?php
    //en este bloque php va "mi" código
  
 $this->startBoxPrimary("Estadísticas de entrega de materiales");
//$this->contentContainerStart();



 //$a=calculaParentescos("T"); echo "Tios: ".$a;
?>

             <div class="row">

         			<div class="col-md-4">
         			</div>
         			<div class="col-sm-4">
         					<?php
							imprimirItems($dms)
							?>
         					    <br>       
         		    </div>
							    

         			<div class="col-md-4">
         			</div>
         	</div>  



 <div class="row">

         	<div class="col-md-12">
         			 <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title"><div id="tituloCaja"></div></h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">

            	<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
            	<div id="tablaDatos"></div>
              
             
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

         	</div>


         </div>


<?php
 //////FIN MI CODIGO                 
//$this->contentContainerEnd();


$this->endsBoxPrimary();
     ?>
	     </div>
		</div>
		</div>
  
		<?php	
		$this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();
		//INICIO: NECESARIO PARA BOTONES DE ACCIONES EN TABLAS
		echo '<script src="../../styles/multisis-lte/bower_components/datatables.net/js/jquery-3.3.1.js"></script>';
		echo '<script src="../../styles/multisis-lte/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>';
		echo '<script src="../../styles/multisis-lte/bower_components/datatables.net/js/dataTables.buttons.min.js"></script>';
        echo '<script src="../../styles/multisis-lte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>';
		echo '<script src="../../styles/multisis-lte/bower_components/datatables.net/js/pdfmake.min.js"></script>';
		echo '<script src="../../styles/multisis-lte/bower_components/datatables.net/js/vfs_fonts.js"></script>';
		echo '<script src="../../styles/multisis-lte/bower_components/datatables.net/js/buttons.html5.min.js"></script>';
		echo '<script src="../../styles/multisis-lte/bower_components/datatables.net/js/jszip.min.js"></script>';
		echo '<script src="../../styles/multisis-lte/bower_components/datatables.net/js/buttons.flash.min.js"></script>';
		echo '<script src="../../styles/multisis-lte/bower_components/datatables.net/js/buttons.print.min.js"></script>';
		echo '<script src="../../tablasDinamicas.js"></script>';
     	// fin de lo necesario para botnones
	 	echo '<script src="../../styles/multisis-lte/plugins/sorting/moment.min.js"></script>';
        echo '<script src="../../styles/multisis-lte/plugins/sorting/datetime-moment.js"></script>';
      	
		echo "<script type='text/javascript'  src='scriptDibujaGraficas.js'></script>";
		
		$this->htmlEndPage();
	} /* }}} */
}
?>
