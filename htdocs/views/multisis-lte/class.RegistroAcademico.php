<?php
/**
 * Implementation of ViewFolder view
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
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
 * Class which outputs the html page for ViewFolder view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
 function contarEventos($dms)
	 {
	 	$res=true;
		$db = $dms->getDB();
		$consultar = "SELECT COUNT(*) FROM app_evento;";
		//echo "Consultar: ".$consultar;
		$res1 = $db->getResultArray($consultar);
		return $res1[0]['COUNT(*)'];
	 }

	  function dameEventos($dms)
	 {
	 	$res=true;
		$db = $dms->getDB();
		$consultar = "SELECT * FROM app_evento;";
		//echo "Consultar: ".$consultar;
		$res1 = $db->getResultArray($consultar);
		return $res1[0]['COUNT(*)'];
	 }

function contarItems($fechaInicio,$fechaFin) //le puedo pasar "postulado " o "aprobado"
//me da el conteo de cuantos postulados o aprobados hay entre fecha inicio y fecha fin
{
	//echo "funcion fechaInicio: ".$fechaInicio;
	$contador=0;
	//echo "Función getDefaultUserFolder. Se ha pasado con argumento: ".$id_usuario;
	 $settings = new Settings(); //acceder a parámetros de settings.xml con _antes
  	$driver=$settings->_dbDriver;
    $host=$settings->_dbHostname;
    $user=$settings->_dbUser;
    $pass=$settings->_dbPass;
    $base=$settings->_dbDatabase;
	$manejador=new SeedDMS_Core_DatabaseAccess($driver,$host,$user,$pass,$base);
	$estado=$manejador->connect();
	//echo "Conectado: ".$estado;
	$miQuery="";
	if($estado!=1)
	{
		UI::exitError("Error en consulta base de datos estadísitcas de items entregados","ViewFolders: No se pudo conectar a la BD");
	}	
	//query de consulta:

	$miQuery="SELECT sum(cantidad_variada) FROM app_transaccion WHERE tipo_transaccion=1 AND (fecha between '$fechaInicio' and '$fechaFin');";
	//echo "i quer_;".$miQuery;
	$resultado=$manejador->getResultArray($miQuery);
	$contador=$resultado[0]['sum(cantidad_variada)'];
	if(!$resultado)
	{
		UI::exitError("Error mostrando estadísticas","mostrarEstadisticas: No se pudo ejecutar $miQuery");
	}
	//echo "Contados: ".$contador;
    return intval($contador);
}	 
class SeedDMS_View_RegistroAcademico extends SeedDMS_Bootstrap_Style {
	function getAccessModeText($defMode) { /* {{{ */
		switch($defMode) {
			case M_NONE:
				return getMLText("access_mode_none");
				break;
			case M_READ:
				return getMLText("access_mode_read");
				break;
			case M_READWRITE:
				return getMLText("access_mode_readwrite");
				break;
			case M_ALL:
				return getMLText("access_mode_all");
				break;
		}
	} /* }}} */
	function printAccessList($obj) { /* {{{ */
		$accessList = $obj->getAccessList();
		if (count($accessList["users"]) == 0 && count($accessList["groups"]) == 0)
			return;
		$content = '';
		for ($i = 0; $i < count($accessList["groups"]); $i++)
		{
			$group = $accessList["groups"][$i]->getGroup();
			$accesstext = $this->getAccessModeText($accessList["groups"][$i]->getMode());
			$content .= $accesstext.": ".htmlspecialchars($group->getName());
			if ($i+1 < count($accessList["groups"]) || count($accessList["users"]) > 0)
				$content .= "<br />";
		}
		for ($i = 0; $i < count($accessList["users"]); $i++)
		{
			$user = $accessList["users"][$i]->getUser();
			$accesstext = $this->getAccessModeText($accessList["users"][$i]->getMode());
			$content .= $accesstext.": ".htmlspecialchars($user->getFullName());
			if ($i+1 < count($accessList["users"]))
				$content .= "<br />";
		}
		if(count($accessList["groups"]) + count($accessList["users"]) > 3) {
			$this->printPopupBox(getMLText('list_access_rights'), $content);
		} else {
			echo $content;
		}
	} /* }}} */


	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$orderby = $this->params['orderby'];
		$enableFolderTree = $this->params['enableFolderTree'];
		$enableClipboard = $this->params['enableclipboard'];
		$enableDropUpload = $this->params['enableDropUpload'];
		$expandFolderTree = $this->params['expandFolderTree'];
		$showtree = $this->params['showtree'];
		$cachedir = $this->params['cachedir'];
		$workflowmode = $this->params['workflowmode'];
		$enableRecursiveCount = $this->params['enableRecursiveCount'];
		$maxRecursiveCount = $this->params['maxRecursiveCount'];
		$previewwidth = $this->params['previewWidthList'];
		$timeout = $this->params['timeout'];
		$folderid = $folder->getId();
		$this->htmlAddHeader('<link href="../styles/'.$this->theme.'/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet">'."\n", 'css');
		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/plugins/datatables/jquery.dataTables.min.js"></script>'."\n", 'js');
		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/plugins/datatables/dataTables.bootstrap.min.js"></script>'."\n", 'js');
		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/validate/jquery.validate.js"></script>'."\n", 'js');
		
		echo $this->callHook('startPage');
		$this->htmlStartPage("Gestor de eventos de la ENAFOP", "skin-blue sidebar-mini sidebar-collapse");
		$this->containerStart();

		$this->mainHeader();
		$this->mainSideBar($folder->getID(),0,0);
		$previewer = new SeedDMS_Preview_Previewer($cachedir, $previewwidth, $timeout);
		    /////aprovecho de calcular cuantos items fueron entregados en cada mes.
		$añoActual=date("Y");

		$this->contentStart();		
		//echo $this->getFolderPathHTML($folder);
		echo ' <ol class="breadcrumb">
         <li><a href="../out.ViewFolder.php"><i class="fa fa-dashboard"></i> Portal</a></li>
        <li><a href="../out.GestionFormacion.php"><i class="fa fa-wrench"></i> Subsistema de Gestión de la Formación ENAFOP</a></li>
        <li class="active">Aplicación de Registro Académico</li>
      </ol>';
		echo "<div class=\"row\">";
		echo "<h3>¡Bienvenid@ a la Aplicación de Registro Académico de la ENAFOP</h3>";

		//// Add Folder ////
		
		echo "<div class=\"col-md-12 div-hidden\" id=\"div-add-folder\">";

		echo "<div class=\"box box-success div-green-border\" id=\"box-form1\">";
    echo "<div class=\"box-header with-border\">";
    echo "<h3 class=\"box-title\">".getMLText("add_subfolder")."</h3>";
    echo "<div class=\"box-tools pull-right\">";
    echo "<button type=\"button\" class=\"btn btn-box-tool\" data-widget=\"remove\"><i class=\"fa fa-times\"></i></button>";
    echo "</div>";
    echo "<!-- /.box-tools -->";
    echo "</div>";
    echo "<!-- /.box-header -->";
    echo "<div class=\"box-body\">";
    ?>
     <style>
        .error {
            color: red;
        }
    </style>


    <?php
    echo "</div>";
    echo "<!-- /.box-body -->";
    echo "</div>";
		echo "</div>";
		//// Add Document (Añadir resolución del TEG. Modificado por Mario López Leiva marioleiva2011@gmail.com)////
		echo "<div class=\"col-md-12 div-hidden\" id=\"div-add-document\">";
		echo "<div class=\"box box-warning div-bkg-color\" id=\"box-form2\">";
    echo "<div class=\"box-header with-border\">";
    echo "<h3 class=\"box-title\">".getMLText("anadir_resolucion")."</h3>";
    echo "<div class=\"box-tools pull-right\">";
    echo "<button id=\"btn-add-document-hide\" type=\"button\" class=\"btn btn-box-tool\"><i class=\"fa fa-times\"></i></button>";
    echo "</div>";
    echo "<!-- /.box-tools -->";
    echo "</div>";
    echo "<!-- /.box-header -->";
    echo "<div class=\"box-body\">";
    ?>

   	
    <?php
    echo "</div>";
    echo "<!-- /.box-body -->";
    echo "</div>";
		echo "</div>";
		//// Folder content ////
	////////////// AQUI VA MI CONTENIDO DE ENAFOP		
		?>
		  <div class="row">

        <!-- ./col -->
         <div class="col-lg-4 col-xs-6">

         	  <div class="small-box bg-green">
            <div class="inner">
              <h3>Procesos Formativos</h3>

              <p>Ver y editar</p>
            </div>
            <div class="icon">
              <i class="fa fa-book"></i>
            </div>
            <a href="out.GestionarFormaciones.php" class="small-box-footer">Acceder<i class="fa fa-arrow-circle-right"></i></a>
          </div>


        </div>

        <div class="col-lg-4 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-navy">
            <div class="inner">
              <h3>Personas participantes </h3>

              <p>Ver listas</p>
            </div>
            <div class="icon">
              <i class="fa fa-group"></i>
            </div>
            <a href="out.GestionarItems.php" class="small-box-footer">Acceder<i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-4 col-xs-6">
        	<div class="small-box bg-primary">
            <div class="inner">
              <h3>Estadísticas y reportes </h3>

              <p>de la Formación</p>
            </div>
            <div class="icon">
              <i class="fa fa-area-chart"></i>
            </div>
            <a href="out.GestionarItems.php" class="small-box-footer">Acceder<i class="fa fa-arrow-circle-right"></i></a>
          </div>


        </div>

        <!-- ./col -->
     
      </div>
      <!-- /.row -->

      <div class="row">

      	        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3>Ver  instituciones</h3>

              <p>en el universo ENAFOP</p>
            </div>
            <div class="icon">
              <i class="fa fa-hand-rock-o"></i>
            </div>
            <a href="out.RealizarTransaccion.php" class="small-box-footer">Acceder<i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>

         <div class="col-lg-3 col-xs-6">

         		 <div class="small-box bg-yellow">
            <div class="inner">
              <h3>Ver docentes</h3>

              <p>que han impartido formación para ENAFOP</p>
            </div>
            <div class="icon">
              <i class="fa fa-hand-rock-o"></i>
            </div>
            <a href="out.RealizarTransaccion.php" class="small-box-footer">Acceder<i class="fa fa-arrow-circle-right"></i></a>
          </div>

        </div>

         <div class="col-sm-6 col-xs-6">
         	 <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Actividades formativas en curso</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>Order ID</th>
                    <th>Item</th>
                    <th>Status</th>
                    <th>Popularity</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR9842</a></td>
                    <td>Call of Duty IV</td>
                    <td><span class="label label-success">Shipped</span></td>
                    <td>
                      <div class="sparkbar" data-color="#00a65a" data-height="20">90,80,90,-70,61,-83,63</div>
                    </td>
                  </tr>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR1848</a></td>
                    <td>Samsung Smart TV</td>
                    <td><span class="label label-warning">Pending</span></td>
                    <td>
                      <div class="sparkbar" data-color="#f39c12" data-height="20">90,80,-90,70,61,-83,68</div>
                    </td>
                  </tr>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR7429</a></td>
                    <td>iPhone 6 Plus</td>
                    <td><span class="label label-danger">Delivered</span></td>
                    <td>
                      <div class="sparkbar" data-color="#f56954" data-height="20">90,-80,90,70,-61,83,63</div>
                    </td>
                  </tr>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR7429</a></td>
                    <td>Samsung Smart TV</td>
                    <td><span class="label label-info">Processing</span></td>
                    <td>
                      <div class="sparkbar" data-color="#00c0ef" data-height="20">90,80,-90,70,-61,83,63</div>
                    </td>
                  </tr>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR1848</a></td>
                    <td>Samsung Smart TV</td>
                    <td><span class="label label-warning">Pending</span></td>
                    <td>
                      <div class="sparkbar" data-color="#f39c12" data-height="20">90,80,-90,70,61,-83,68</div>
                    </td>
                  </tr>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR7429</a></td>
                    <td>iPhone 6 Plus</td>
                    <td><span class="label label-danger">Delivered</span></td>
                    <td>
                      <div class="sparkbar" data-color="#f56954" data-height="20">90,-80,90,70,-61,83,63</div>
                    </td>
                  </tr>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR9842</a></td>
                    <td>Call of Duty IV</td>
                    <td><span class="label label-success">Shipped</span></td>
                    <td>
                      <div class="sparkbar" data-color="#00a65a" data-height="20">90,80,90,-70,61,-83,63</div>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              <a href="javascript:void(0)" class="btn btn-sm btn-info btn-flat pull-left">Place New Order</a>
              <a href="javascript:void(0)" class="btn btn-sm btn-default btn-flat pull-right">View All Orders</a>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->

        </div>


      </div>





<?php
		echo "</div>\n"; // End of row
		echo "</div>\n"; // End of container

		//echo $this->callHook('postContent');

		$this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();

		echo "<script type='text/javascript' src='/formularioSubida.js'></script>";
		echo '<script src="../styles/multisis-lte/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>';
		echo '<script src="../styles/multisis-lte/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>';
        echo '<script src="../styles/multisis-lte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>';

        echo '<script src="../styles/multisis-lte/bower_components/Chart.js/Chart.js"></script>';


        echo '<script src="../styles/multisis-lte/plugins/sorting/moment.min.js"></script>';
        echo '<script src="../styles/multisis-lte/plugins/sorting/datetime-moment.js"></script>';
        echo '<script src="../styles/multisis-lte/bower_components/jquery-knob/js/jquery.knob.js"></script>';
		echo '<script src="../tablasDinamicas.js"></script>';
		echo '<script src="../graficaInicial.js"></script>';

		$this->htmlEndPage();
	} /* }}} */
}

?>