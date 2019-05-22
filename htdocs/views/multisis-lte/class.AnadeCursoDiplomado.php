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
function imprimirUbicaciones()
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
    echo "out.AnadePersona.php[]Error: no se pudo conectar a la BD";
  } 
  //query de consulta:
  $miQuery="SELECT nombre,id FROM app_ubicacion;";
  //echo "mi query: ".$miQuery;
  $resultado=$manejador->getResultArray($miQuery);
  $arrayDepartamentos=$resultado[0]['nombre'];
  ////////////////////// EL SELECT
  echo ' <select class="form-control chzn-select"  name="ubicacion[]"  id="ubicacion" multiple="multiple" data-placeholder="Seleccione una o varias ubicaciones..."  >';
  echo "<option disabled  value>Seleccione una ubicación donde se va a guardar</option>";
  foreach ($resultado as $a) 
  {
       echo "<option value=\"".$a['id']."\">".$a['nombre']."</option>";
  }

  echo "</select>";
}// fin de imprimir departamentos

function imprimirOrigen()
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
  $miQuery="SELECT nombre,id FROM app_proyecto;";
  //echo "mi query: ".$miQuery;
  $resultado=$manejador->getResultArray($miQuery);
  ////////////////////// EL SELECT
  echo ' <select class="form-control "  name="origen"  id="origen"   data-placeholder="Seleccione proyecto con el que se adquiere..."  required>';
  echo "<option disabled  selected value>Seleccione un proyecto</option>";
  foreach ($resultado as $a) 
  {
       echo "<option value=\"".$a['id']."\">".$a['nombre']."</option>";
  }

  echo "</select>";
}// fin de imprimir departamentos

function imprimeTipos()
{

  //echo "mi query: ".$miQuery;
  $resultado=array('Material divulgativo','Material de oficina','Equipo y accesorios');
  ////////////////////// EL SELECT
  echo ' <select class="form-control "  name="tipo"  id="tipo"   data-placeholder="Seleccione el tipo..."  required>';
  echo "<option disabled  selected value>Seleccione un tipo</option>";
  foreach ($resultado as $a) 
  {
       echo "<option value=\"".$a."\">".$a."</option>";
  }

  echo "</select>";
}// fin de imprimir tipos

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
function imprimeCategorias()
{

  //echo "mi query: ".$miQuery;
  $resultado=array('Personal Técnico','Personal Gerencial','Personal Directivo','Personal Administrativo y de Apoyo');
  ////////////////////// EL SELECT
  echo ' <select class="form-control chzn-select"   name="categoria[]"  id="categoria"   multiple="multiple" data-placeholder="Seleccione..."  required>';
  echo "<option disabled   value>Seleccione una o varias categorías</option>";
  foreach ($resultado as $a) 
  {
       echo "<option value=\"".$a."\">".$a."</option>";
  }

  echo "</select>";
}// fin de imprimir tipos
function imprimeProyectos()
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
  $miQuery="SELECT nombre,id,origen_fondos FROM app_proyecto;";
  //echo "mi query: ".$miQuery;
  $resultado=$manejador->getResultArray($miQuery);
  ////////////////////// EL SELECT
  echo ' <select class="form-control chzn-select"  name="proyecto"  id="proyecto"  data-placeholder="Seleccione un proyecto..."  >';
  echo "<option disabled  value>Seleccione el proyecto que financia compra</option>";
  foreach ($resultado as $a) 
  {
    $fondos=$a['origen_fondos']; echo "fondos: ".$fondos;
       echo "<option value=\"".$a['id']."\">"."($fondos)".$a['nombre']."</option>";
  }

  echo "</select>";
}// fin de imprimir departamentos
class SeedDMS_View_AnadeCursoDiplomado extends SeedDMS_Bootstrap_Style 
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
        <div class="col-md-3">

        </div> <!-- FIN DE COLUMNA 1 -->

        <div class="col-md-6">
        		<div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Datos del curso o diplomado</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
      <form class="form-horizontal" name="formularioRegistroCurso" id="formularioRegistroCurso" action="out.ProcesarCurso.php" method="POST" enctype="multipart/form-data">
              <div class="box-body">

                <div class="form-group">
                  <label for="nombreCurso" class="col-sm-2 control-label">Nombre</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="nombreCurso" name="nombreCurso" placeholder="por ejemplo Diplomado en Gerencia Pública  ..." required>
                  </div>
                </div>


                <div class="form-group">
                  <label for="nombreCorto" class="col-sm-2 control-label">Nombre corto que identifique el curso</label>

                  <div class="col-sm-10">
                    <input type="text" maxlength="10" class="form-control" id="nombreCorto" name="nombreCorto" placeholder="Máximo 10 caracteres, por ejemplo DGPUB  ..." required>
                  </div>
                </div>
                          
                 <div class="form-group">
                  <label for="descripcion" class="col-sm-2 control-label">Modalidad</label>

                  <div class="col-sm-10">
                   <?php imprimeModalidades(); ?>
                  </div>
                </div> 


                <div class="form-group">
                  <label for="tipo" class="col-sm-2 control-label">Duración en horas académicas</label>

                  <div class="col-sm-10">
                    <input type="number" step="1" min="1" max="500" class="form-control" id="duracion" name="duracion" placeholder="por ejemplo 20  ..." required>
                  </div>

                </div> 

                <div class="form-group">
                  <label for="empresa" class="col-sm-2 control-label">Categorías de cargos a los que va dirigido el curso/diplomado</label>

                  <div class="col-sm-10">
                   <?php imprimeCategorias(); ?>
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
