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
class SeedDMS_View_GestionFormacion extends SeedDMS_Bootstrap_Style {
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
		$meActual=date("M");
		$diaActual=date("d");

		$this->contentStart();		
		//echo $this->getFolderPathHTML($folder);
		echo ' <ol class="breadcrumb">
         <li><a href="out.ViewFolder.php"><i class="fa fa-dashboard"></i> Portal</a></li>
      
         <li class="active"><i class="fa fa-wrench"></i> Subsistema de Gestión de la Formación ENAFOP</li>
      </ol>';
		echo "<div class=\"row\">";
		echo "<h3>Bienvenid@ al subsistema de gestión de la formación ENAFOP!</h3>";

		//// Add Folder ////
		// echo "<div class=\"col-md-12 div-hidden\" id=\"div-add-folder\">";
		// echo "<div class=\"box box-success div-green-border\" id=\"box-form1\">";
  //   echo "<div class=\"box-header with-border\">";
  //   echo "<h3 class=\"box-title\">".getMLText("add_subfolder")."</h3>";
  //   echo "<div class=\"box-tools pull-right\">";
  //   echo "<button type=\"button\" class=\"btn btn-box-tool\" data-widget=\"remove\"><i class=\"fa fa-times\"></i></button>";
  //   echo "</div>";
  //   echo "<!-- /.box-tools -->";
  //   echo "</div>";
  //   echo "<!-- /.box-header -->";
  //   echo "<div class=\"box-body\">";
    ?>


   	
    <?php
  //   echo "</div>";
  //   echo "<!-- /.box-body -->";
  //   echo "</div>";
		// echo "</div>";
		//// Folder content ////
	////////////// AQUI VA MI CONTENIDO DE ENAFOP		
		?>
		  <div class="row">

        <!-- ./col -->
         <div class="col-lg-3 col-xs-3">
         		 <img src="../images/togaenafop.png" alt="Cohete ENAFOP"  class="img-fluid" alt="Responsive image">

        </div>

        <div class="col-lg-6 col-xs-3">
          <!-- small box -->

          <div class="small-box bg-maroon">
            <div class="inner">
              <h3>HERRAMIENTA</h3>
              <p>de Gestión de Docentes ENAFOP</p>
            </div>
            <div class="icon">
              <i class="fa  fa-male"></i>
            </div>
            <a href="confirmaciones/out.TiposConvocatorias.php" class="small-box-footer">Acceder<i class="fa fa-arrow-circle-right"></i></a>
          </div>

           <div class="small-box bg-blue">
            <div class="inner">
              <h3>HERRAMIENTA</h3>
              <p>de Registro Académico ENAFOP</p>
            </div>
            <div class="icon">
              <i class="fa  fa-university"></i>
            </div>
            <a href="formacion/out.RegistroAcademico.php" class="small-box-footer">Acceder<i class="fa fa-arrow-circle-right"></i></a>
          </div>



        </div>

        <div class="col-lg-3 col-xs-3">

        </div>

        <!-- ./col -->
     
      </div>
      <!-- /.row -->



<?php
		echo "</div>\n"; // End of row
		echo "</div>\n"; // End of container

		//echo $this->callHook('postContent');

		$this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();

		//echo "<script type='text/javascript' src='/formularioSubida.js'></script>";
		echo '<script src="../styles/multisis-lte/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>';
		echo '<script src="../styles/multisis-lte/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>';
        echo '<script src="../styles/multisis-lte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>';

        //echo '<script src="../styles/multisis-lte/bower_components/Chart.js/Chart.js"></script>';


        //echo '<script src="../styles/multisis-lte/plugins/sorting/moment.min.js"></script>';
        //echo '<script src="../styles/multisis-lte/plugins/sorting/datetime-moment.js"></script>';
        //echo '<script src="../styles/multisis-lte/bower_components/jquery-knob/js/jquery.knob.js"></script>';
		//echo '<script src="../tablasDinamicas.js"></script>';
		//echo '<script src="../graficaInicial.js"></script>';

		$this->htmlEndPage();
	} /* }}} */
}

?>