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
class SeedDMS_View_SeleccionEstadisticasMateriales extends SeedDMS_Bootstrap_Style 
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

		$this->htmlStartPage("Gestionar items", "skin-blue sidebar-mini  sidebar-collapse");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		//$this->contentContainerStart("hoa");
		$this->contentStart();
          
		?>
		   <ol class="breadcrumb">
        <li><a href="../out.ViewFolder.php"><i class="fa fa-dashboard"></i> Portal</a></li>
        <li><a href="../out.GestionInterna.php"><i class="fa fa-wrench"></i> Subsistema de Gestión Interna ENAFOP</a></li>
        <li><a href="out.Materiales.php">Gestión de material</a></li>
        <li class="active">Estadísticas</li>
      </ol>
    <div class="gap-10"></div>
    <div class="row">
    <div class="col-md-12">
      

    <?php
    //en este bloque php va "mi" código
  
 $this->startBoxPrimary("Selección de estadísticas relativas a entrega de materiales");
$this->contentContainerStart();
//////INICIO MI CODIGO
?>
<div class="row">
        <div class="col-md-3">

        </div> <!-- FIN DE COLUMNA 1 -->

        <div class="col-md-6">
        		
                

                          <div class="box">
                            <div class="box-header">
                              <h3 class="box-title">Seleccione categoría de estadísticas</h3>
                            </div> <!-- /.box header-->
                              <div class="box-body">
                                  <div class="row">

                                    <div class="col-md-4">
                                      <a class="btn btn-app" href="out.EstadisticasGastoProyecto.php">
                                        <i class="fa  fa-money"></i> Datos de gasto según fuente de financiamiento
                                      </a>
                                    </div>


                                      <div class="col-md-4">
                                        <a class="btn btn-app" href="out.EstadisticasMateriales.php">
                                  
                                          <i class="fa fa-group"></i> Datos de entrega de materiales según grupo
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
