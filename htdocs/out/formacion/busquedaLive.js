function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return pattern.test(emailAddress);
}

jQuery(document).ready(function()
{

    /////////////////////// AJAX PARA CREAR TABLA DE DOCENTES ///////////
    // crear tabla de docentes por si no existe en el ssitema
                $.ajax({
                    url:"crearTablaDocentes.php",
                    success:function(result)
                    {
                        //console.log("creada tabla de docentes");
                    }
                }); //fin del ajax

    ///////////////////////////////////////////////////////////////////////////
    //al presionar el botón "añadir nuevo docente " en el formulario de selección, añadilo.
        $("#anadirDocente").click(function(){
            //alert("clic en añadir docente"); 
            var nombre=$('#nombreDocente').val();
            var correo=$('#correoDocente').val();
            var tel=$('#telefonoDocente').val();
        
            
            
            if( !isValidEmailAddress( correo ) ) { alert("Formato de correo electrónico incorrecto. Por favor, corríjalo."); return false;}
            if((nombre.localeCompare("")==0) || (tel.localeCompare("")==0))
            {
                alert("Debe completar todos los campos del docente. Por favor llénelos.");
            }
            else
            {
                var x = document.getElementById("mensajeAgregadoDocente");
            var cheque="<p><b>Docente añadido correctamente a la base. </b><br> Ahora búsquelo en la el espacio de arriba para añadirlo a los docentes que se registrararán en el curso.<i class=\"fa fa-check-circle\"></i></p>";                        
            $.ajax({
                    url:"anadirDocenteNuevo.php?nombre="+nombre+"&correo="+correo+"&tel="+tel,
                     beforeSend: function(){
                    // Show image container
                    $(x).hide('fast');
                    $("#loader").show();
                   },
                    success:function(result)
                    {

                        
                    },
                    complete:function(data){
                    // Hide image container
                    $("#loader").hide();
                    $('#nombreDocente').val('');
                    $('#correoDocente').val('');
                    $('#telefonoDocente').val('');
                    if (x.style.display === "none") 
                                {
                                    $(x).show('slow');
                                } 
                                x.innerHTML=cheque; 
                   }

                }); //fin del ajax
            }//fin del else si no están vacios
            
        });
    ////////////////////////////////////////////////////////////////////////

   var tiempito=$("#tiempito").val();
   var tokencito=$("#tokencito").val();
   //alert("tokencito: "+tokencito);
    //var proceso=$("#proceso").val();


    var vecesclic=0;
    jQuery(".mySearch").ajaxlivesearch({
        loaded_at: tiempito,
        token: tokencito,
        max_input: 20,
        onResultClick: function(e, data) {
            // get the index 0 (first column) value
           // get the index 1 (second column) value
           vecesclic++;
          
            var selectedOne = jQuery(data.selected).find('td').eq('1').html();

            // set the input value
            jQuery('#ls_query').val(selectedOne);
            //si je añadido un docente via mini formulario anteriormente; esconder el mensaje que se genera.
            var x = document.getElementById("mensajeAgregadoDocente");
             if (!(x.style.display === "none")) 
            {
                $(x).hide('fast');
            } 
                                

            ///
            var idSeleccionado= jQuery(data.selected).find('td').eq('0').html();
            //alert("Seleccionado id: "+idSeleccionado);
            var idBorrado="eliminar-docente-"+vecesclic;
            var botonBorrado= "<button type=\"button\" id=\""+idBorrado+"\" class=\"remove btn btn-danger btn-xs\"><i class=\"fa fa-times\"></i></button> &nbsp;";
            var idLi="li-"+vecesclic;
              $( "#listaDocentes" ).append( "<li id=\""+idLi+"\" class=\"text-green\">"+selectedOne+botonBorrado+"</li>");            
            // hide the result
            $('#ls_query').val('');   
            jQuery("#ls_query").trigger('ajaxlivesearch:hide_result');


        },
        onResultEnter: function(e, data) 
        {
            // do whatever you want
            alert("hola");
            jQuery("#ls_query").trigger('ajaxlivesearch:search', {query: 'test'});
        },
        onAjaxComplete: function(e, data) {

        }
    });



}) //fin de doc ready
$(document).on("click", "button.remove" , function() {
            $(this).parent().remove();
        });
//al cerrar la selección de docentes 
$(document).on("click", "#botonCerrarDocentes" , function() 
{
            //alert("docentes cerrados");
            var x = document.getElementById("listaFinalDocentes");
             if ((x.style.display === "none")) 
            {
                $(x).show('fast');
            } 
           var contenidoDocentes= $('#listaDocentes').text();
           // var docentesSeparados = contenidoDocentes.split(' ');
           // var listadoBonito="<p>";
           // for(var i=0;i<docentesSeparados.length;i++)
           // {
           //       listadoBonito=listadoBonito+docentesSeparados[i]+","
           // }
           //  listadoBonito=listadoBonito+"</p>";
           x.innerHTML=contenidoDocentes;


});
