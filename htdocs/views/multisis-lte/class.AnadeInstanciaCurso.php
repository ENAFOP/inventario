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
require_once("SeedDMS/Preview.php");
$rutaLibreria=__DIR__ ;
$quitado=dirname($rutaLibreria, 2);
$quitado=$quitado.'/styles/multisis-lte/live-search/core/';
file_exists($quitado. 'Handler.php') ? require_once $quitado. 'Handler.php' : die('Handler.php not found');
file_exists($quitado. 'Config.php') ? require_once $quitado. 'Config.php' : die('Config.php not found');
use AjaxLiveSearch\core\Config;
use AjaxLiveSearch\core\Handler;


/**
 * Include class to preview documents
 */



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

function imprimirCursos()
{
  //LOS DEPARTAMENTOS LEIDOS DE LA BD
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
    echo "out.AnadeItem.php[]Error: no se pudo conectar a la BD";
  } 
  //query de consulta:
  $miQuery="SELECT nombre,id,nombre_corto FROM procesos_formativos;";
  //echo "mi query: ".$miQuery;
  $resultado=$manejador->getResultArray($miQuery);
  ////////////////////// EL SELECT
  echo ' <select class="form-control "  name="proceso"  id="proceso"   data-placeholder="Seleccione qué proceso formativo se ejecutará..."  required>';
  echo "<option disabled  selected value>Seleccione un curso/diplomado</option>";
  foreach ($resultado as $a) 
  {
       echo "<option value=\"".$a['id']."\">".$a['nombre']."</option>";
  }

  echo "</select>";


}// fin de imprimir departamentos



function imprimeModalidades()
{

  //echo "mi query: ".$miQuery;
  $resultado=array('Presencial','Semipresencial','Virtual');
  ////////////////////// EL SELECT
  echo ' <select class="form-control "  name="modalidad"  id="modalidad"   data-placeholder="Seleccione..."  required>';
  echo "<option disabled  selected value>Seleccione la modalidad</option>";
  foreach ($resultado as $a) 
  {
       echo "<option value=\"".$a."\">".$a."</option>";
  }

  echo "</select>";
}// fin de imprimir tipos

class SeedDMS_View_AnadeInstanciaCurso extends SeedDMS_Bootstrap_Style 
{

 /**
 Método que muestra los documentos próximos a caducar sólo de 
 **/

	function show() 
	{ /* {{{ */
    if (session_id() == '') {
    session_start();
  }

    $handler = new Handler();
    $handler->getJavascriptAntiBot();
    //echo "HANDLER: ".$handler;

    $tokencito="'" . $handler->getToken() . "'";

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
    
		$this->htmlStartPage("Añadir item ", "skin-blue sidebar-mini  sidebar-collapse");
    //$this->htmlAddHeader('<link rel="stylesheet"  src="../../styles/'.$this->theme.'/live-search/css/fontello.css"></script>');
    //$this->htmlAddHeader('<link rel="stylesheet"  src="../../styles/'.$this->theme.'/live-search/css/animation.css"></script>');
   //$this->htmlAddHeader('<link rel="stylesheet" type="text/css" src="../../styles/'.$this->theme.'/live-search/css/ajaxlivesearch.min.css"></script>');
    //$this->htmlAddHeader('<link rel="stylesheet" type="text/css" href="css/ajaxlivesearch.min.css">');
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		//$this->contentContainerStart("hoa");
		$this->contentStart();
           echo "<input id=\"tokencito\" type=\"hidden\" value=\"$tokencito\">";
		?>
      <ol class="breadcrumb">
        <li><a href="../out.ViewFolder.php"><i class="fa fa-dashboard"></i> Portal</a></li>
        <li><a href="../out.GestionFormacion.php"><i class="fa fa-wrench"></i> Subsistema de Gestión de la Formación ENAFOP</a></li>
        <li><a href="out.RegistroAcademico.php">Registro Académico</a></li>
        <li><a href="out.GestionarFormaciones.php">Gestionar acciones formativas</a></li>
        <li class="active">Añadir un nuevo curso o diplomado a la oferta académica</li>
      </ol>

    <div class="gap-10"></div>
    <div class="row">
    <div class="col-md-12">
      

    <?php
    //en este bloque php va "mi" código
  
 $this->startBoxPrimary("Añadir nuevo proceso de formación de la oferta académica");
$this->contentContainerStart();
//////INICIO MI CODIGO
?>
<!-- ***************** UNA FILA TRES COLUMNAS *********************-->

<div class="row">
        <div class="col-md-1">

          

        </div> <!-- FIN DE COLUMNA 1 -->

        <div class="col-md-10">
        		<div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Datos del curso o diplomado</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
      <form class="form-horizontal" name="formularioRegistroCurso" id="formularioRegistroCurso" action="out.ProcesarCurso.php" method="POST" enctype="multipart/form-data">
              <div class="box-body">

                <div class="form-group">
                  <label for="proceso" class="col-sm-2 control-label">Seleccione curso o diplomado a desarrollar:</label>

                  <div class="col-sm-6">
                   <?php imprimirCursos(); ?>
                  </div>


                  <label id="cuantos" class="control-label" for="inputSuccess" style="display: none;">
                  
                  </label>


                </div>

                <div id="instancia" class="form-group" style="display: none;" >
                  <label for="instancia" class="col-sm-2 control-label">Código del curso (manejo interno del sistema)</label>

                  <div id="inputCodigo"class="col-sm-10">
                    
                  </div>

                </div> 

                 


    
    


                <div class="form-group">
                  <label for="tipo" class="col-sm-2 control-label">Cuerpo docente que impartirá este curso</label>

                  <div class="col-sm-10">
                    <button  type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-default">
                    Seleccionar docentes
                  </button>
                  </div>

                  <div id="listaFinalDocentes" class="form-group" style="display: none;" >
                     <label for="listaFinalDocentes" class="col-sm-2 control-label">Docentes seleccionados:</label>

                  

                 </div> 


                </div> 





                 <div class="modal fade" id="modal-default">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title">Seleccione lista de personas docentes y facilitadoras. Si aún no está en el sistema, añádala</h4>
                        </div>
                        <div class="modal-body">

                          <div class="row">

                            <div class="col-md-1">
                            

                            </div>

                            <div class="col-sm-8">

                              <div style="clear: both">
                          <input type="text" class='mySearch' id="ls_query" placeholder="Escriba el nombre de un/a docente ...">
                          </div>  

                            </div>

                            


                           </div> 


                           <div id="filaAnadirDocente" class="row" >

                              <div class="col-sm-5">
                                  <h5>Lista de personas docentes seleccionadas</h5>

                                    <ol id="listaDocentes">
                                  

                                    </ol>

                               </div> 

                               <div class="col-sm-7">
                                  <h5>¿No aparece la persona en la búsqueda anterior? Añádalo con los siguientes campos: </h5>


                                   <label for="inputEmail3">Nombre</label>
                                  <div class="form-group">
                                 

                                  <div class="col-sm-10">
                                    <input type="text" class="form-control" id="nombreDocente"  name="nombreDocente"placeholder="Nombre" required>
                                  </div>
                                </div>

                                <label for="inputEmail3">Correo</label>
                                <div class="form-group">
                                  

                                  <div class="col-sm-10">
                                    <input type="text" class="form-control" id="correoDocente"  nombre="correoDocente" placeholder="Correo">
                                  </div>
                                </div>

                                <label for="inputEmail3" >Teléfono</label>
                                <div class="form-group">
                                  

                                  <div class="col-sm-10">
                                    <input type="text" class="form-control" id="telefonoDocente"  nombre="telefonoDocente" placeholder="Teléfono">
                                  </div>
                                </div>


                                <button id="anadirDocente" type="button" class="btn bg-orange margin"><div id='loader' style='display: none;'>
                                 <img src="../../images/carga.gif" id="loadingGif">
                              </div>Agregar docente a la base</button>

                            


                              <label id="mensajeAgregadoDocente" class="control-label" for="inputSuccess" style="display: none;">
                  
                              </label>
                                


                                


                               </div> 

                           </div>

                          

                          <div >
                         
                          </div>  

                                              



               </div>
                        <div class="modal-footer">
                          
                          <button id="botonCerrarDocentes" type="button" class="btn btn-primary" data-dismiss="modal">Guardar lista de docentes</button>
                        </div>
                      </div>
                      <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                  </div>
                  <!-- /.modal -->



                 <div class="form-group">
                  <label for="objetivo" class="col-sm-2 control-label">Objetivo</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="objetivo" name="objetivo" placeholder="Indique objetivo" required>
                  </div>

                </div> 

                <div class="form-group">
                  <label for="metodologia" class="col-sm-2 control-label">Metodología</label>

                  <div class="col-sm-10">
                     <input type="text" class="form-control" id="metodologia" name="metodologia" placeholder="Explique brevemente la metodología" required>
                  </div>

                </div> 


                <div class="form-group">
                  <label for="evaluacion" class="col-sm-2 control-label">Evaluación</label>

                  <div class="col-sm-10">
                   <input type="text" class="form-control" id="evaluacion" name="evaluacion" placeholder="Explique brevemente la forma de evaluación" required>
                  </div>

                </div> 


              

                </div> 






              <!-- /.box-body -->
              <div class="box-footer">
                <button type="reset" class="btn btn-default">Borrar campos</button>
                <button type="submit" class="btn btn-info pull-right">Enviar formulario de registro de formación</button>
              </div>
              <!-- /.box-footer -->
            </form>
          </div>




        </div> <!-- FIN DE COLUMNA 2 -->


        <div class="col-md-1">

        </div> <!-- FIN DE COLUMNA 3 -->
</div> <!-- FIN DE FILA -->


<?php
 //////FIN MI CODIGO                 
$this->contentContainerEnd();
 echo '<script  src="../../styles/'.$this->theme.'/live-search/js/jquery-1.11.1.min.js"></script>';
 echo '<script type="text/javascript" src="../../styles/'.$this->theme.'/live-search/js/ajaxlivesearch.js"></script>';

 $time= time();
 echo "time:".$time;
 echo "<input id=\"tiempito\" type=\"hidden\" value=\"$time\">";

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
    echo '<script src="checkInstanciaCurso.js"></script>;';
    echo '<script src="busquedaLive.js"></script>;';
    //echo '<script src="busquedaLive.js"></script>;';

   

   


		$this->htmlEndPage();
	} /* }}} */
}
?>
