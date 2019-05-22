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
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		//$this->contentContainerStart("hoa");
		$this->contentStart();
          
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
                  <label for="tipo" class="col-sm-2 control-label">Duración en horas académicas</label>

                  <div class="col-sm-10">
                    <input type="number" step="1" min="1" max="500" class="form-control" id="duracion" name="duracion" placeholder="por ejemplo 20  ..." required>
                  </div>

                </div> 



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
		$this->htmlEndPage();
	} /* }}} */
}
?>
