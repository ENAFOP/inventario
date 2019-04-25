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
function existeHost($dms)
	 {
	 	$existe=false;
		$db = $dms->getDB();
		$insertar = "SELECT * FROM forms_hosts";
		$res1 = $db->getResultArray($insertar);
		//print_r("res1".$res1);
		if(!empty($res1)){
			$existe=true;	
		}	
		return $existe;
	 }
class SeedDMS_View_DefinirBase extends SeedDMS_Bootstrap_Style 
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

		$this->htmlStartPage(getMLText("mi_sitio"), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		//$this->contentContainerStart("hoa");
		$this->contentStart();          
		?>
		}
    <div class="gap-10"></div>
    <div class="row">
    <div class="col-md-12">
      

    <?php
    //en este bloque php va "mi" código
  
 $this->startBoxPrimary("Defina los datos de conexión a la base de datos donde se guardan las respuestas a  formularios de la ENAFOP");
$this->contentContainerStart();
//////INICIO MI CODIGO
		if(existeHost($dms)==true)
		{
			
			//lo obtengo
			$db = $dms->getDB();
			$insertar = "SELECT * FROM forms_hosts";
			$res1 = $db->getResultArray($insertar);
			$filita=$res1[0];
			//
			$host=$filita['host'];
			$user=$filita['user'];
			$pwd=$filita['password'];
			$base=$filita['base'];
			//listo editable host

			echo '<ul>';

			echo '<li>';
				echo "<p>Host del servidor</p>";
				echo "<a href=\"#\" id=\"host\"  data-type=\"text\" data-pk=\"1\" data-url=\"../modificarHostEditable.php\" data-title=\"host\">".$host."</a>";
			echo '</li>';

			echo '<li>';
			echo "<p>Usuario de la base de datos</p>";
				echo "<a href=\"#\" id=\"user\"  data-type=\"text\" data-pk=\"1\" data-url=\"../modificarHostEditable.php\" data-title=\"user\">".$user."</a>";
			echo '</li>';

			echo '<li>';
			echo "<p>Password de la base de datos</p>";
				echo "<a href=\"#\" id=\"password\"  data-type=\"text\" data-pk=\"1\" data-url=\"../modificarHostEditable.php\" data-title=\"password\">".$pwd."</a>";
			echo '</li>';

			echo '<li>';
			echo "<p>Nombre de la base de datos (schema)</p>";
				echo "<a href=\"#\" id=\"base\"  data-type=\"text\" data-pk=\"1\" data-url=\"../modificarHostEditable.php\" data-title=\"base\">".$base."</a>";
			echo '</li>';

			echo '</ul>';
		}

		else //si no está el host seteado, 
		{
			echo '<div class="box box-success">
	<form class="form-horizontal" name="formularioBase" id="formularioBase" action="out.ProcesarBase.php" method="POST" enctype="multipart/form-data">
            <div class="box-header with-border">
              <h3 class="box-title">Ingrese IP y nombre de la base de datos</h3>
            </div>
            <div class="box-body">
            	 <label for="host" class="col-sm-2 control-label">Host</label>
              <input class="form-control input-lg" type="text" placeholder="ingrese texto"  name="host" id="host" required>
              <br>
               <label for="user" class="col-sm-2 control-label">Usuario en BD</label>
              <input class="form-control input-lg" type="text" placeholder="ingrese texto"  name="user" id="user" required>
              <br>
               <label for="pwd" class="col-sm-2 control-label">Password</label>
              <input class="form-control input-lg" type="text" placeholder="ingrese texto"  name="password" id="password" required>
              <br>
               <label for="base" class="col-sm-2 control-label">Nombre de la base de datos</label>
              <input class="form-control input-lg" type="text" placeholder="ingrese texto"  name="base" id="base" required>
              <br>             
              
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">guardar</button>
              </div>
            <!-- /.box-body -->
          </div>
      </form>';
		}

?>

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
		//$this->contentContainerEnd();
    echo "<script type='text/javascript' src='../modificarPerfil.js'></script>";
        echo '<script type="text/javascript" src="../styles/'.$this->theme.'/jquery-editable/js/jquery-editable-poshytip.min.js"></script>'."\n";
    echo '<script type="text/javascript" src="../styles/'.$this->theme.'/poshytip-1.2/src/jquery.poshytip.min.js"></script>'."\n";
		$this->htmlEndPage();
	} /* }}} */
}
?>
