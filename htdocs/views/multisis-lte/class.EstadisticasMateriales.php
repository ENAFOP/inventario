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
  echo '<button id="myButton">Click me</button>';
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

 function cuentaAcciones($acccion,$exacto)
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
    echo "cuentaAcciones.php: Error: no se pudo conectar a la BD";
	exit;
  } 
  /////////////// CONTEO DE INACCIONES
  $inacciones="SELECT id FROM tblAttributeDefinitions WHERE name='$acccion'";
  $resultado1=$manejador->getResultArray($inacciones);
  $listaInacciones=array();
  foreach ($resultado1 as $r) 
  {
  	array_push($listaInacciones, $r['id']);
  }
  //ya tengo la lista de id de atributos de inacciòn
  //recorro esa lista y para cada una de ellas cuento las palabras
  $contador=0;
  foreach ($listaInacciones as $id) 
  {
  	$consultar="SELECT value FROM tblFormAttributes WHERE attrdef=$id";
  	 $resultado2=$manejador->getResultArray($consultar);
  	 foreach ($resultado2 as $k) 
  	 {
  	 	$textoCompleto=$k['value'];
  	 	
  	 	if(!empty($textoCompleto))
  	 	{
  	 		//echo "----------*/*/*";
  	 		//echo "inaccion encontrada: ".$textoCompleto."<br>";
  	 		$porciones = explode("-", $textoCompleto);
  	 		if(in_array($exacto, $porciones))
	  	 	{
	  	 		$contador=$contador+1;	  	
	  	 	}
  	 	}
  	 }
  }
  ///////////////////////////// FINALIZAMOS
return $contador;

 }
function damePromedio($sexo)
{
	$contadorFinal=0;
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
		UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("class.FrecuenciCambio.php: error al conectar a bd"));
	}	
	//primero veo viviendo en el hogar, atributo 93 (campo 5)

	$consulta="SELECT attr.`name` `key`, AVG(da.`value`) `total` FROM tblDocumentAttributes da INNER JOIN tblAttributeDefinitions attr ON attr.`id` = da.`attrdef` WHERE attr.`name` LIKE '%en genograma%' GROUP BY `key`;
";

	//echo "mi query: ".$miQuery;
	$resultado=$manejador->getResultArray($consulta);
	foreach ($resultado as $fila) 
	{
		$clave=$fila['key'];
		$total=$fila['total'];

		if(strcmp($clave, $sexo)==0)
		{
			return $total;
		}	 
	}

}//fin de calculaParentescos



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


		$this->htmlStartPage("Comportamientos mas comunes", "skin-blue sidebar-mini sidebar-collapse");
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
        <li><a href="/out/out.ChartSelector.php"><i class="fa fa-dashboard"></i> Estadísticas</a></li>
        <li>Gráficos relacionados con evolución de los jóvenes</li>
        <li class="active">Comportamientos más comunes</li>
      </ol>
      

    <?php
    //en este bloque php va "mi" código
  
 $this->startBoxPrimary("Estadísticas de entrega de materiales");
$this->contentContainerStart();

imprimirItems($dms)

 //$a=calculaParentescos("T"); echo "Tios: ".$a;
?>

              <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-check"></i> Significado de este gráfico</h4>
               Estos gráficos los comportamientos (acciones e inacciones) más comunes en los jóvenes.
              </div>

 <div class="row">


         	<div class="col-md-6">
         			<div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Gráfico de acciones más comunes</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div id="grafAcciones" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

         	</div>



         	<div class="col-md-6">

         			<div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title">Gráfico de inacciones más comunes</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div id="container2" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

         	</div>

         </div>



         <div class="row">
         	<div class="col-md-6">
         			<div class="box box-warning box-solid">
	<div class="box-header with-border">
		<h3 class="box-title">Acciones</h3>
	</div>

	<div class="box-body">	
	<table id='data' class="table table-bordered table-striped">
			<thead>
				<tr>
					<th><?php echo "Sexo"; ?></th>
					<th><?php echo "Cantidad media de personas"; ?></th>
				</tr>
			</thead>
			<tbody>
<?php
		//calculo los valores de frecuencia
		 //ACCIONES
$totalGrita=0;
$totalPelea=0;
$totalMalasPalabras=0;
$totalMiente=0;
$totalOtrasAcciones=0;
//
$totalGrita=cuentaAcciones("Acción","Grita");
$totalPelea=cuentaAcciones("Acción","Pelea");
$totalMalasPalabras=cuentaAcciones("Acción","Dice malas palabras");
$totalMiente=cuentaAcciones("Acción","Miente");
$totalOtrasAcciones=cuentaAcciones("Acción","Otros");
//INACCIONES
$noTareas=0;
$noDeberes=0;
$noEscucha=0;
$otrasInacciones=0;
$noTareas=cuentaAcciones("Inacción","No hace las tareas escolares");
$noDeberes=cuentaAcciones("Inacción","No cumple con los deberes");
$noEscucha=cuentaAcciones("Inacción","No escucha");
$otrasInacciones=cuentaAcciones("Inacción","Otros");

	
						echo "<tr>";
								echo "<td>"."Grita"."</td>";
								echo "<td>".$totalGrita."</td>";
						echo "</tr>";


						echo "<tr>";
								echo "<td>"."Pelea"."</td>";
								echo "<td>". $totalPelea."</td>";
						echo "</tr>";

						echo "<tr>";
								echo "<td>"."Dice malas palabras"."</td>";
								echo "<td>". $totalMalasPalabras."</td>";
						echo "</tr>";

						echo "<tr>";
								echo "<td>"."Miente"."</td>";
								echo "<td>". $totalMiente."</td>";
						echo "</tr>";

						echo "<tr>";
								echo "<td>"."Otros"."</td>";
								echo "<td>". $totalOtrasAcciones."</td>";
						echo "</tr>";
					

					//IMPRIMO TOTALES
					echo "<tfoot>";
					echo "<tr>";
								echo "<td><b>SUMA TOTAL DE ACCIONES</b></td>";
					$total=intval($totalGrita)+intval($totalPelea)+intval($totalMalasPalabras)+intval($totalMiente)+intval($totalOtrasAcciones);
								echo "<td><font color=\"blue\">".$total."</font></td>";
						echo "</tr>";
					echo "</tfoot>";
				
?>
			</tbody>
		</table>
	</div>
	</div>
         	</div><!-- /FIN DE LA PRIMERA COLUMNA -->

         	<div class="col-md-6">
         		         			<div class="box box-danger box-solid">
	<div class="box-header with-border">
		<h3 class="box-title">Inacciones</h3>
	</div>

	<div class="box-body">	
	<table id='dataInacciones' class="table table-bordered table-striped">
			<thead>
				<tr>
					<th><?php echo "Sexo"; ?></th>
					<th><?php echo "Cantidad media de personas"; ?></th>
				</tr>
			</thead>
			<tbody>
<?php
		//calculo los valores de frecuencia
	
						
						echo "<tr>";
								echo "<td>"."No hace las tareas escolares"."</td>";
								echo "<td>".$noTareas."</td>";
						echo "</tr>";


						echo "<tr>";
								echo "<td>"."No cumple con los deberes"."</td>";
								echo "<td>". $noDeberes."</td>";
						echo "</tr>";

						echo "<tr>";
								echo "<td>"."No escucha"."</td>";
								echo "<td>". $noEscucha."</td>";
						echo "</tr>";

						echo "<tr>";
								echo "<td>"."Otros"."</td>";
								echo "<td>". $otrasInacciones."</td>";
						echo "</tr>";

					
					

					//IMPRIMO TOTALES
					echo "<tfoot>";
					echo "<tr>";
								echo "<td><b>SUMA TOTAL</b></td>";
								$total2=intval($noTareas)+intval($noDeberes)+intval($noEscucha)+intval($otrasInacciones);
								echo "<td><font color=\"blue\">".$total2."</font></td>";
						echo "</tr>";
					echo "</tfoot>";
				
?>
			</tbody>
		</table>
	</div>
	</div>
         	</div><!-- /FIN DE LA SEGUNDA COLUMNA -->
         </div><!-- /FIN DE LA FILA -->



<?php
 //////FIN MI CODIGO                 
$this->contentContainerEnd();


$this->endsBoxPrimary();
     ?>
	     </div>
		</div>
		</div>

           <input type="hidden" id="totalGrita" value="<?php echo $totalGrita?>" />
           <input type="hidden" id="totalPelea" value="<?php echo $totalPelea?>" />
           <input type="hidden" id="totalMalasPalabras" value="<?php echo $totalMalasPalabras?>" />
           <input type="hidden" id="totalMiente" value="<?php echo $totalMiente?>" />
           <input type="hidden" id="totalOtrasAcciones" value="<?php echo $totalOtrasAcciones?>" />
           <input type="hidden" id="noTareas" value="<?php echo $noTareas?>" />
           <input type="hidden" id="noDeberes" value="<?php echo $noDeberes?>" />
           <input type="hidden" id="noEscucha" value="<?php echo $noEscucha?>" />
           <input type="hidden" id="otrasInacciones" value="<?php echo $otrasInacciones?>" />

  
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
		echo '<script src="../../tablasDinamicas.js"></script>';
     	// fin de lo necesario para botnones
	 	echo '<script src="../../styles/multisis-lte/plugins/sorting/moment.min.js"></script>';
        echo '<script src="../../styles/multisis-lte/plugins/sorting/datetime-moment.js"></script>';
      	
		echo "<script type='text/javascript'  src='scriptDibujaGraficas.js'></script>";
		
		$this->htmlEndPage();
	} /* }}} */
}
?>
