//SECUENCIA: 
//1 ELEGIR FECHA INICIAL DE ESTADÍSTICAS, SE MUESTRA EL SELECTOR DE FECHA FINAL. 2: SELECCIONO FECHA FINAL, SE MUESTRA SELECTOR DE ITEM,
//3 SELECCIONO ITEM, MUESTRO GRÁFICAS
$('#contenedor_fechafin').on('change','.form-control',function(){
 //^^^^parent div         ^^^dynamically added element
    //alert("Fecha FINAL cambiada con valor: "+this.value);
  var x = document.getElementById("contenedorSelectorItem");
      if (x.style.display === "none") 
      {
        $(x).show('slow');
        
      }
});
$('#fecha_inicio').on('change', function() {
  //alert("Fecha cambiada con valor: "+this.value);
  var fechaInicial=this.value;
  var x = document.getElementById("contenedor_fechafin");

      if (x.style.display === "none") 
      {
        $(x).show('fast');
        
      }
      var texto="<input type=\"date\"  class=\"form-control\" id=\"fecha_fin\" min=\""+fechaInicial+"\" name=\"fecha_fin\" placeholder=\"Indique fecha final\" required>"; 
      document.getElementById("inputFecha").innerHTML = texto
     
});

$('#itemElegido').on('change', function() {
  actualizaGrafica( this.value );
});

function actualizaGrafica(selectedOption) {
  var item=selectedOption;
  //var grupo = document.getElementById("grupo").value; //console.log("ID grupo: "+x);
    var fechaIni=$('#fecha_inicio').val();
    var fechaFin=$('#fecha_fin').val(); 
    console.log("fecha inicio: "+fechaIni); console.log("fecha final: "+fechaFin);
    var arrayCantidadesEntregadas=[];
   var arrayGrupos=[];
   var arrayTotal=[];

    $.ajax({
                        url:"entregadosPorGrupo.php?idItem="+item+"&fechaInicio="+fechaIni+"&fechaFin="+fechaFin,
                        success:function(result)
                        {
                                                      
                             datosf1 = JSON.parse(result);
                              for (var index in datosf1) 
                              {
                              //console.log("key: "+datosf1[index]['key']);
                              //console.log("total: "+datosf1[index]['total']);
                              arrayGrupos.push(datosf1[index][0]); //console.log("Metido a array nombres; "+datosf1[index][0])
                              arrayCantidadesEntregadas.push(Number(datosf1[index][1])); //console.log("Metido a array cantidades; "+Number(datosf1[index][1]))
                             }

                             var nameItem=$("#itemElegido option:selected").text();   
                                Highcharts.chart('container', {
                                chart: {
                                    type: 'column'
                                },
                                title: {
                                    text: "Cantidad de "+ nameItem+ " entregados a grupos estratégicos de ENAFOP"
                                },
                                xAxis: {
                                    title: {
                                        text: 'Grupo receptor del artículo'
                                    },
                                    categories: arrayGrupos,
                                    crosshair: true
                                },
                                yAxis: {
                                    min: 0,
                                    title: {
                                        text: 'Cantidad de artículos entregados'
                                    }
                                },
                                tooltip: {
                                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                                        '<td style="padding:0"><b>{point.y}</b></td></tr>',
                                    footerFormat: '</table>',
                                    shared: true,
                                    useHTML: true
                                },
                                plotOptions: {
                                    column: {
                                        pointPadding: 0.2,
                                        borderWidth: 0
                                    }
                                },
                                series: [{
                                    name: 'Cantidad de materiales entregados',
                                    data: arrayCantidadesEntregadas

                                }]
                            }); //FIN DE DIBUJAR CHARTS
                                
            var textofinal=""

            var cabecita='<div class="box box-success box-solid"><div class="box-header with-border"><h3 class="box-title">Resumen de la entrega de este material</h3></div><div class="box-body">'             
            var texto="<table id='tablita' class=\"table table-bordered table-striped\"><caption>"+nameItem+" Entregados en el periodo comprendido entre "+fechaIni+" y "+fechaFin+"</caption><thead><tr><th>Nombre del grupo al que se entrega</th><th>Cantidad de ítems entregados</th></tr></thead><tbody>" 
            var contenido=""
             for (var index in arrayGrupos) 
                              {
                              contenido=contenido+"<tr>"
                              contenido=contenido+"<td>"
                              contenido=contenido+arrayGrupos[index]
                              contenido=contenido+"</td>"

                              contenido=contenido+"<td>"
                              contenido=contenido+arrayCantidadesEntregadas[index]
                              contenido=contenido+"</td>"
                              contenido=contenido+"</tr>"
                             }
             var totalito= arrayCantidadesEntregadas.reduce(function(a, b) { return a + b; }, 0);
            var finalTabla="<tfoot><tr><td>TOTAL ENTREGADO</td><td><b>"+totalito+"</b></td></tr></tfoot></tbody></table></div></div>"
            textofinal=cabecita+texto+contenido+finalTabla
            document.getElementById("tablaDatos").innerHTML = textofinal
            
            document.getElementById("tituloCaja").innerHTML = nameItem+ " entregados en el periodo comprendido entre "+fechaIni+" y "+fechaFin

            var currentdate = new Date(); 
              var datetime =  currentdate.getDate() + "/"
                + (currentdate.getMonth()+1)  + "/" 
                + currentdate.getFullYear() + " a las "  
                + currentdate.getHours() + ":"  
                + currentdate.getMinutes() + ":" 
                + currentdate.getSeconds();
                //////////////// dibujo tabla con Data Tables 
            $('#tablita').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        { extend: 'copy', text: 'Copiar' },
                        { extend: 'csvHtml5', title: 'CSV' },
                        { extend: 'excelHtml5', title: 'Excel' },
                        { 

                      extend: 'pdfHtml5', title: 'PDF',
                      customize: function ( doc ) {
                        var cols = [];
                    cols[0] = {text: datetime, alignment: 'left', margin:[20] };
                    cols[1] = {text: 'Reporte generado desde el sistema de información ENAFOP. www.enafop.gob.sv', alignment: 'right', margin:[0,0,20] };
                    var objFooter = {};
                    objFooter['alignment'] = 'center';
                    objFooter['columns'] = cols;
                    doc["footer"]=objFooter;
                    doc.content.splice( 1, 0, {
                        margin: [ 0, 0, 0, 12 ],
                        alignment: 'center',
                        image: 'data:image/png;base64,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s'
                        //image: 'data:image/png;base64,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',
                    }, 
                    {
                        text: "Fecha de generación del reporte: "+datetime,
                        alignment: 'center'
                    }
                 );
                }
                      



                      },
                        { extend: 'print', text: 'Imprimir', 



                        messageTop: function () {
                                            return "<h2>Reporte del artículo \""+nameItem+"\"</h2>"+"<h4>Fecha del reporte generado desde www.enafop.gob.sv: "+datetime+"</h4>";
                                        },


                         },
                        
                    ],
                    'language': {
                        'search': 'Buscar',
                        'emptyTable': 'Tabla vacía',
                        'info': 'Mostrando la cantidad de este artículo entregado a cada grupo',
                        'infoEmpty': 'Está vacía esta tabla',
                        'infoFiltered': 'Filtrada',
                        'lengthMenu': 'Longitud',
                        'loadingRecords': 'Cargando registros',
                        'processing': 'Procesando',
                        'zeroRecords': 'Sin registros',
                        'paginate': {
                            'first': 'Primera',
                            'last': 'Última',
                            'next': 'Siguiente',
                            'previous': 'Anterior'
                        }
                    }
                });// fin de dibujar botones
                                
                        } // fin de success                     
                        //console.log("b: "+dataf1[1])
                    }); //fin del ajax

}
