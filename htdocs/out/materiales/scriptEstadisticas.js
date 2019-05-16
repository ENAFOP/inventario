var totalGrita = $("#totalGrita").val(); //console.log("total grita: "+totalGrita);
var totalPelea = $("#totalPelea").val(); //console.log("total totalPelea: "+totalPelea);
var totalMalasPalabras = $("#totalMalasPalabras").val(); //console.log("totalMalasPalabras totalMalasPalabras: "+totalMalasPalabras);
var totalOtrasAcciones = $("#totalOtrasAcciones").val(); //console.log("totalOtrasAcciones totalOtrasAcciones: "+totalOtrasAcciones);
var totalMiente = $("#totalMiente").val(); //console.log("totalMiente : "+totalMiente);
////////////////INACCIONES
var noTareas = $("#noTareas").val();
var noDeberes = $("#noDeberes").val();
var noEscucha = $("#noEscucha").val();
var otrasInacciones = $("#otrasInacciones").val();




Highcharts.chart('grafAcciones', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Acciones'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.2f} %',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    series: [{
        name: 'Acciones de los jóvenes',
        colorByPoint: true,
        data: [{
            name: 'Grita',
            y: Number(totalGrita),
            sliced: true,
            selected: true
        }, {
            name: 'Pelea',
            y: Number(totalPelea)
        },{
            name: 'Dice malas palabras',
            y: Number(totalMalasPalabras)
        }
        , {
            name: 'Miente',
            y: Number(totalMiente)
        }
        , {
            name: 'Otros',
            y: Number(totalOtrasAcciones)
        }]
    }]
});

Highcharts.chart('container2', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Inacciones'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.2f} %',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    series: [{
        name: 'Inacciones de los jóvenes',
        colorByPoint: true,
        data: [{
            name: 'No hace las tareas escolares',
            y: Number(noTareas),
            sliced: true,
            selected: true
        }, {
            name: 'No cumple con los deberes',
            y: Number(noDeberes)
        }, {
            name: 'No escucha',
            y: Number(noEscucha)
        }, {
            name: 'Otros',
            y: Number(otrasInacciones)
        }]
    }]
});