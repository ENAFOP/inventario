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

function multiplon($indice,$numero)
{
	if($indice%$numero==0)
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
		$totalHombres=0;
		$totalMujeres=0;

		$db = $dms->getDB();
		$previewer = new SeedDMS_Preview_Previewer($cachedir, $previewwidth, $timeout);
			$driver="mysql";
	//echo "intentando acceder con usuario: ".$usuarito;
        $manejador=new SeedDMS_Core_DatabaseAccess($driver,(string) $host,(string)$usuarito,(string)$password,(string)$base);
        $estado=$manejador->connect();

        $miQuery0="SELECT title FROM wp_formmaker WHERE id=$idevento;";
	 $resultado0=$manejador->getResultArray($miQuery0);
	 $nombreEvento=$resultado0[0]['title'];


	     $qtotal="SELECT COUNT(distinct group_id) FROM wp_formmaker_submits WHERE form_id=$idevento";
	            $tconf=$manejador->getResultArray($qtotal);
	            $totalito=$tconf[0]['COUNT(distinct group_id)'];
	           // echo "totalito: ".$totalito;
          

		$this->htmlStartPage("Ver confirmados para el evento $nombreEvento", "skin-blue sidebar-mini sidebar-collapse");
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
                En esta pantalla se muestra la lista de personas que han confirmado el evento, empezando por la confirmación más reciente.
              </div>
 <div class="row">

 	<div class="col-md-3">





          </div>
<div class="col-md-6">
              <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?php  echo $totalito; ?></h3>

              <p>Personas confirmadas al evento</p>
            </div>
            <div class="icon">
              <i class="ion ion-person"></i>
            </div>
            
          </div> 

          </div> 


           


        </div>  

    <?php
    //en este bloque php va "mi" código
  
 $this->startBoxPrimary("Listado de fichas de confirmación a evento <b>$nombreEvento</b>");
$this->contentContainerStart();
//////INICIO MI CODIGO

	
	//echo "Conectado: ".$estado;
	if($estado!=1)
	{
		UI::exitError(getMLText("my_documents"),"Error en la operación base de datos conectar al host $host.<br> Ver lista de confirmaciones");
	}
	//conseguir los labels o campos del form
	$miQuery1="SELECT label_order_current FROM wp_formmaker WHERE id=$idevento;";
	 $resultado1=$manejador->getResultArray($miQuery1);
	 $labels=$resultado1[0]['label_order_current'];
	  echo "Labels explotadas; <br>".$labels;
	 $porciones = explode("#**", $labels);
	
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
	 		//echo "nombre cambo cmpleto: ".$nombrecampo
	 		$nombrecampo = substr($nombrecampo, 2); //borro los dos primeros caracteres "id"
	 		array_push($arrayCampos, $nombrecampo);
	 		echo "nombre cambpo: ".$nombrecampo."<br>";

	 	}
	 	
	 }
	echo '<div class="table-responsive">';
	echo '<table id="tablaConfirmaciones" class="table table-hover table-striped table-condensed">
              	<thead>
                <tr>';
                //labels efectivas
	            $queryLabels="SELECT COUNT(distinct element_label) FROM wp_formmaker_submits WHERE form_id=$idevento";
	            $labelscont=$manejador->getResultArray($queryLabels);
	            $labelsEfectivas=$labelscont[0]['COUNT(distinct element_label)'];
                $logCampos=sizeof($arrayCampos); 



                
                for ($a=0; $a<=$labelsEfectivas; $a++) //counter empieza en 1 porque me mete un campo Custom HTML 9 que NO quiero
                {
                	//echo "a: ".$a;
	                		$campo=$arrayCampos[$a];
	                		//añadido 25 abril 19: si contiene "Custom " no sirve 
	      //           		if (strpos($campo, 'Custom') !== false) {
							//    continue;
							// }
	                		echo "<th>$campo</th>";               		
                  
                }
                // echo "<th>Fecha en que confirmó</th>";
                           
                echo'</tr>
               </thead>
               <tbody>';
                 $miQueryNum="SELECT distinct element_label FROM wp_formmaker_submits WHERE form_id=$idevento";
	            $resum=$manejador->getResultArray($miQueryNum);
	            $arrayLabels=array();
	            foreach ($resum as $label) 
	            {
	            	$elem=$label['element_label'];
	            	array_push($arrayLabels, $elem);
	  
	            }
	            $confirmados=$resum[0]['COUNT(distinct group_id)'];
	            // la
	            
	            //echo "labelsEfectivas: ".$labelsEfectivas;
         		$tam=sizeof($arrayCampos);
         	
				    //
				    $miQuery2="SELECT * FROM wp_formmaker_submits WHERE form_id=$idevento  order by date desc";
		            $resultado=$manejador->getResultArray($miQuery2);
		            //echo "miQuery2: ".$miQuery2;
		            $longi=sizeof($resultado);
		            //echo "Longi: $longi";
		              for ($i=0; $i<$longi; $i++) 
		              {
		              	
		              	//echo "valor de i: ".$i;
		              	$filita=$resultado[$i];
		              	$fechaconfirma=$filita['date'];
		              	
			              	if(multiplon($i,$labelsEfectivas))
			              	{
			              		//echo "<td>$fechaconfirma</td>";
			              		echo ' </tr>';
			              		//echo "<br>$fechaconfirma<br>";
			              		echo "<tr>";
			              	}						  	 
				            $valor=$filita['element_value'];
				            $valor=str_replace("@@@"," ",$valor);
				            if(strcmp($valor, "Hombre")==0)
				            {
				            	$totalHombres++;
				            }
				            else
				            {
				            	$totalMujeres++;
				            }
				            
				            echo "<td>$valor</td>";
	
				    	
					  }	
					  				 

		           	    	
			   // } 
			    echo ' </tr>';
			      
	                         
  

            echo  '</tbody>
              <tfoot>
              </tfoot>
               
              </table>';

              echo '</div>';
	
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
			echo '<script src="../../styles/multisis-lte/bower_components/datatables.net/js/buttons.print.min.js"></script>';
			echo '<script src="../../styles/multisis-lte/bower_components/datatables.net/js/dataTables.buttons.min.js"></script>';
        echo '<script src="../../styles/multisis-lte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>';
        echo '<script src="../../styles/multisis-lte/plugins/sorting/moment.min.js"></script>';
        echo '<script src="../../styles/multisis-lte/plugins/sorting/datetime-moment.js"></script>';
        echo '<script src="../../styles/multisis-lte/bower_components/jquery-knob/js/jquery.knob.js"></script>';
		echo '<script src="../../tablasDinamicas.js"></script>';
		$this->htmlEndPage();
	} /* }}} */
}
?>
