

$('#itemElegido').on('change', function() {
  actualizaGrafica( this.value );
});
function actualizaGrafica(selectedOption) {
  alert("cambiado con valor "+selectedOption);
  var x = document.getElementById("grupo").value; //console.log("ID grupo: "+x);
  //document.getElementById("demo").innerHTML = "You selected: " + x;
   var dataf1=[];
    var arrayNombres=[];
   var arrayCantidades=[];
    $.ajax({
                        url:"/expedientesGrupo.php?id="+x,
                        success:function(result)
                        {
                                                      
                              datosf1 = JSON.parse(result);
                              for (var index in datosf1) 
                              {
                              //console.log("key: "+datosf1[index]['key']);
                              //console.log("total: "+datosf1[index]['total']);
                              arrayNombres.push(datosf1[index][0]); //console.log("Metido a array nombres; "+datosf1[index][0])
                              arrayCantidades.push(Number(datosf1[index][1])); //console.log("Metido a array cantidades; "+Number(datosf1[index][1]))
                             }
                                Highcharts.chart('container', {
                                chart: {
                                    type: 'column'
                                },
                                title: {
                                    text: 'Cantidad de expedientes por consejero/a'
                                },
                                xAxis: {
                                    title: {
                                        text: 'Nombre de la persona consejera'
                                    },
                                    categories: arrayNombres,
                                    crosshair: true
                                },
                                yAxis: {
                                    min: 0,
                                    title: {
                                        text: 'Número de expedientes'
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
                                    name: 'Cantidad de expedientes',
                                    data: arrayCantidades

                                }]
                            }); //FIN DE DIBUJAR CHARTS
                                
            var textofinal=""   
            var cabecita='<div class="box box-success box-solid"><div class="box-header with-border"><h3 class="box-title">Cantidad de expedientes asignados a cada consejero</h3></div><div class="box-body">'             
            var texto="<table id='data2' class=\"table table-bordered table-striped\"><thead><tr><th>Nombre</th><th>Cantidad de expedientes</th></tr></thead><tbody>" 
            var contenido=""
             for (var index in arrayNombres) 
                              {
                              contenido=contenido+"<tr>"
                              contenido=contenido+"<td>"
                              contenido=contenido+arrayNombres[index]
                              contenido=contenido+"</td>"

                              contenido=contenido+"<td>"
                              contenido=contenido+arrayCantidades[index]
                              contenido=contenido+"</td>"
                              contenido=contenido+"</tr>"
                             }
             var totalito= arrayCantidades.reduce(function(a, b) { return a + b; }, 0);
            var finalTabla="<tfoot><tr><td>TOTAL</td><td><b>"+totalito+"</b></td></tr></tfoot></tbody></table></div></div>"
            textofinal=cabecita+texto+contenido+finalTabla
            document.getElementById("tabla").innerHTML = textofinal
            $('#data2').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        { extend: 'copy', text: 'Copiar' },
                        { extend: 'csvHtml5', title: 'CSV' },
                        { extend: 'excelHtml5', title: 'Excel' },
                        { extend: 'pdfHtml5', title: 'PDF' },
                        { extend: 'print', text: 'Imprimir' },
                    ],
                    'language': {
                        'search': 'Buscar',
                        'emptyTable': 'Tabla vacía',
                        'info': 'Mostrando todos los consejeros de este grupo',
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
