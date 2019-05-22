$(document).ready(function () 
{
	$('#proceso').change(function() {
		//console.log("cambio en usuario");
		var proceso=$("#proceso").val();
					$.ajax({
					url:"comprobarCantidadInstancia.php?proceso="+proceso,
					success:function(result)
					{
						  
						//datosf1 = JSON.parse(result); alert("datosf1: "+datosf1);
                         cantidad=result[0];
                         nombreCorto=result[1];

						var x = document.getElementById("cuantos");
						var divInstancia = document.getElementById("instancia");
						var inputCodigo = document.getElementById("inputCodigo");
						var icono= 	document.createElement("i");
						icono.setAttribute("class", "fa fa-check-circle");
						var mensaje = document.createElement("p");
						var node = document.createTextNode("Este será la vez  "+parseInt(cantidad+1)+" que se realizará este curso o diplomado");
						mensaje.appendChild(node);
						mensaje.appendChild(icono);
						x.appendChild(mensaje);
								if (x.style.display === "none") 
								{
									$(x).show('fast');
								} 	

								if (divInstancia.style.display === "none") 
								{
									inputCodigo.innerHTML='<input type="text"  class="form-control" id="codigoInstancia" name="codigoInstancia" value="'+nombreCorto+1+'" readonly>'
									$(divInstancia).show('fast');

								} 	
								
						 
					}
				}); //fin del ajax

		//2: ver si ya está registrado
	});	
});