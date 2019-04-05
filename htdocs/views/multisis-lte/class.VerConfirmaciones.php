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
function multiplo3($indice)
{
	if($indice%3==0)
	{
		return true;
	}
	else
		return false;
}
class SeedDMS_View_VerConfirmaciones extends SeedDMS_Bootstrap_Style 
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
		$idevento = $this->params['idevento'];

		$db = $dms->getDB();
		$previewer = new SeedDMS_Preview_Previewer($cachedir, $previewwidth, $timeout);

		$this->htmlStartPage("Ver confirmados para el evento", "skin-blue sidebar-mini sidebar-collapse");
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
                En esta pantalla se muestran los eventos de la ENAFOP que requieren confirmación. Encontrará la lista de estos eventos, debe hacer clic en el nombre de cada uno para ver la lista de personas confirmadas al mismo.
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
	//conseguir los labels o campos del form
	$miQuery1="SELECT label_order_current FROM wp_formmaker WHERE id=20;";
	 $resultado1=$manejador->getResultArray($miQuery1);
	 $labels=$resultado1[0]['label_order_current'];
	 $porciones = explode("#**", $labels);
	 //echo "Labels explotadas; <br>";
	 $arrayIds=array();
	 $arrayCampos=array();
	 for ($cont=0;$cont<sizeof($porciones);$cont++) 
	 {
	 	//echo "porcientes: ".$porciones[$cont]."<br>---<br>";
	 	if(multiplo3($cont))
	 	{
	 		$numerito=str_replace("**#", "", $porciones[$cont]);
	 		array_push($arrayIds, $numerito);
	 		//echo "numerito: ".$numerito."<br>";
	 		$nombrecampo=str_replace("**#", "", $porciones[$cont+1]);
	 		$nombrecampo = substr($nombrecampo, 2); //borro los dos primeros caracteres "id"
	 		array_push($arrayCampos, $nombrecampo);
	 		//echo "nombre cambpo: ".$nombrecampo."<br>";

	 	}
	 	
	 }




	$miQuery2="SELECT * FROM wp_formmaker_submits WHERE form_id=$idevento ORDER BY DATE desc;";
	echo '<table id="tablaEventos" class="table table-hover table-striped table-condensed">
              	<thead>
                <tr>
                <th width="15%">Número de evento o actividad</th>

                  <th>Título del evento o actividad</th>
                           
                </tr>
               </thead>
               <tbody>';
         
            $resultado=$manejador->getResultArray($miQuery2);
			foreach ($resultado as $evento) 
		    {
		    	echo ' <tr>';
		    	// $id=$evento['id'];
		    	// echo "<td>".$id."</td>";
		    	// //echo "<br>";
		    	// $titulo=$evento['title'];
		    	// echo "<td><a href=\"out.VerConfirmaciones.php?evento=".$id."\">".$titulo."</a></td>";
		    	echo "evento: ".$evento;



		    	echo ' </tr>';
		    }                              
   
            echo  '</tbody>
              <tfoot>
              </tfoot>
               
              </table>';
	
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
			echo '<script src="../styles/multisis-lte/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>';
        echo '<script src="../styles/multisis-lte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>';
        echo '<script src="../styles/multisis-lte/plugins/sorting/moment.min.js"></script>';
        echo '<script src="../styles/multisis-lte/plugins/sorting/datetime-moment.js"></script>';
        echo '<script src="../styles/multisis-lte/bower_components/jquery-knob/js/jquery.knob.js"></script>';
		echo '<script src="../tablasDinamicas.js"></script>';
		$this->htmlEndPage();
	} /* }}} */
}
?>