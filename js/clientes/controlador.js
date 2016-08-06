var clientes = {
    
datos : {
    
    ver : function(){
        clientes_vista.datos.ver();
    },
    
    editar : function(){
        var telefono = $("#md_telefono").val();
        if (telefono.length >=8){
            auxiliar.espera.lanzar();
            $.ajax({
                data:  {telefono: telefono},
                url:   '/PF/clientes/editar_telefono',
                type:  'post',
                error: function(response){
                    auxiliar.espera.detener();
                    auxiliar.mensaje('Se produjo un error en la conexión.', 5000,'toast-error');
                    auxiliar.mensaje('El servidor no está respondiendo nuestra solicitud.', 5000,'toast-error');
                    auxiliar.mensaje('La edición no se realizó correctamente.', 5000,'toast-error');
                },
                success: function (response){
                    var respuesta = JSON.parse(response);
                    auxiliar.espera.detener();
                    if (respuesta['error'] === undefined){
                        auxiliar.mensaje("Campo teléfono editado correctamente.",3500,'toast-ok');
                        clientes_vista.datos.editar(telefono);
                    }else{
                        auxiliar.mensaje(respuesta['error'], 5000, 'toast-error');
                    }
                }
            });
        }else{
            auxiliar.mensaje("Ingrese un número de teléfono válido.", 3500, 'toast-error');
        }
    }
},

viajes : {
    
    puede_confirmar : false,
       
    ver_historial : function(){
        clientes_vista.viajes.ver_historial();
    },
    
    solicitar : function(){
        clientes_vista.viajes.solicitar();
    },
    
    estado : function(){
        clientes_vista.viajes.estado();
    },
    
    set_margen: function(){
        minutos_adicionales = $('#hora_max').val();
        clientes_vista.viajes.set_margen(minutos_adicionales);
    },
    
    info : function(id,ingreso,estado,id_recurso){
        if (estado !== 'A_despachar' && estado !== 'Despachado' && estado !== 'Finalizado'){
            clientes_vista.viajes.info(id, ingreso, estado, "Sin asignar","Sin asignar","Sin asignar","Sin asignar","Sin asignar" );
        }else{
            auxiliar.espera.lanzar();
            $.ajax({
                data: { id_recurso : id_recurso },
                url:   '/PF/clientes/info_recurso',
                type:  'post',
                error: function(response){
                    auxiliar.espera.detener();
                    clientes_vista.viajes.info(id, ingreso, estado, "Error","Error","Error","Error","Error" );
                    auxiliar.mensaje('Se produjo un error en la conexión.', 5000,'toast-error');
                    auxiliar.mensaje('El servidor no está respondiendo nuestra solicitud.', 5000,'toast-error');
                    auxiliar.mensaje('El pedido no se realizó correctamente.', 5000,'toast-error');
                },
                success: function (response){
                    var respuesta = JSON.parse(response);
                    auxiliar.espera.detener();
                    if (respuesta['error'] === undefined){
                        var rta = respuesta['data'];
                        clientes_vista.viajes.info(id, ingreso, estado, rta['nombre'],rta['patente'], rta['marca'], rta['modelo'], rta['color']);
                    }else{
                        clientes_vista.viajes.info(id, ingreso, estado, "Error","Error","Error","Error","Error" );
                        auxiliar.mensaje(respuesta['error'], 5000, 'toast-error');
                    }
                }
            });
        }
    },
    
    validar : function(){
        var origen = $("#origen_viaje").val();
        var destino = $("#destino_viaje").val();
        var margen_calculado = $("#hora_arribo").val();
        
        var pos_origen, pos_destino;
        
        if (origen.length >= 3){
            if (destino.length >= 3){
                if (margen_calculado.length !== 0){
                    
                    //Agregamos información correspondiente a la ciudad actual
                    origen += mapa.ciudad_actual();
                    destino += mapa.ciudad_actual();
                    
                    //Localizamos el origen indicado.
                    mapa.geocoder().geocode( { 'address': origen }, function(results, status) {
                        if (status === google.maps.GeocoderStatus.OK) {
                            pos_origen = results[0].geometry.location;
                            mapa.marcas.estaticas.cambiar('Origen',pos_origen);
                            
                            //Localizamos el destino indicado.
                            mapa.geocoder().geocode( { 'address': destino }, function(results, status) {
                                if (status === google.maps.GeocoderStatus.OK) {
                                    pos_destino = results[0].geometry.location;
                                    mapa.marcas.estaticas.cambiar('Destino',pos_destino);
                                    
                                    if (pos_origen.toString() !== pos_destino.toString()){
                                        clientes.viajes.puede_confirmar = true;
                                        auxiliar.mensaje("Puede comprobar las ubicaciones en el mapa.", 3500, 'toast-ok');
                                    }else{
                                        clientes.viajes.puede_confirmar = false;
                                        auxiliar.mensaje("Error, origen y destino idénticos.", 3500, 'toast-error');
                                    }
                                }else{
                                    clientes.viajes.puede_confirmar = false;
                                    auxiliar.mensaje("Error al localizar la dirección destino.", 3500, 'toast-error');
                                }
                            });
                        }else{
                            clientes.viajes.puede_confirmar = false;
                            auxiliar.mensaje("Error al localizar la dirección origen.", 3500, 'toast-error');
                        }
                    });
                    
                }else{
                    clientes.viajes.puede_confirmar = false;
                    auxiliar.mensaje("Debe ingresar un margen de tiempo.",3500,'toast-error');
                }
            }else{
                clientes.viajes.puede_confirmar = false;
                auxiliar.mensaje("Debe ingresar un destino válido.",3500,'toast-error');
            }
        }else{
            clientes.viajes.puede_confirmar = false; 
            auxiliar.mensaje("Debe ingresar un origen válido.",3500,'toast-error'); 
        }
    }, //FIN VALIDAR
    
    
    pre_confirmar : function(){
        if (clientes.viajes.puede_confirmar){
            clientes_vista.viajes.pre_confirmar();
        }else{
            auxiliar.mensaje("Debe validar origen y destino.", 3500, 'toast-error');
        }
    },
    
    confirmar : function(){        
        if ($("#telefono").val().length >= 8){
            //LOGICA PARA CONFIRMAR VIAJE
            auxiliar.espera.lanzar();
            clientes_vista.viajes.confirmar();
            mapa.distancia.calcular(
                mapa.marcas.estaticas.latitud('Origen'), 
                mapa.marcas.estaticas.longitud('Origen'), 
                mapa.marcas.estaticas.latitud('Destino'), 
                mapa.marcas.estaticas.longitud('Destino'),
                clientes.viajes.confirmar_post
            );
        }else{  
            auxiliar.mensaje("Debe ingresar un número de teléfono válido.", 3500, 'toast-error');
        }
    },
    
    confirmar_post : function(error, demora, distancia){
        if (error === undefined){
            $.ajax({
                data: {
                    origen: $("#origen_viaje").val(),
                    destino: $("#destino_viaje").val(),
                    margen: $("#hora_max").val(),  
                    referencia: $("#referencia").val(),
                    telefono: $("#telefono").val(),
                    lat_origen: mapa.marcas.estaticas.latitud('Origen'), 
                    long_origen: mapa.marcas.estaticas.longitud('Origen'), 
                    lat_destino: mapa.marcas.estaticas.latitud('Destino'), 
                    long_destino: mapa.marcas.estaticas.longitud('Destino'),
                    demora : demora,
                    distancia : distancia
                },
                url:   '/PF/clientes/alta_pedido',
                type:  'post',
                error: function(response){
                    auxiliar.espera.detener();
                    auxiliar.mensaje('Se produjo un error en la conexión.', 5000,'toast-error');
                    auxiliar.mensaje('El servidor no está respondiendo nuestra solicitud.', 5000,'toast-error');
                    auxiliar.mensaje('El pedido no se realizó correctamente.', 5000,'toast-error');
                },
                success: function (response){
                    var respuesta = JSON.parse(response);
                    auxiliar.espera.detener();
                    if (respuesta['error'] === undefined){
                        auxiliar.mensaje("Pedido solicitado correctamente.",3500,'toast-ok');
                        clientes_vista.viajes.confirmar_post(respuesta['data']);
                    }else{
                        auxiliar.mensaje(respuesta['error'], 5000, 'toast-error');
                    }
                }
            });
            clientes.viajes.puede_confirmar = false;
        }else{
            auxiliar.espera.detener();
            auxiliar.mensaje(error, 3500, 'toast-error');
        }
        mapa.marcas.estaticas.visibilidad('Origen', false);
        mapa.marcas.estaticas.visibilidad('Destino', false);
    },
    
    calificacion : {
        calificar : function(id_viaje, id_recurso, estado){
            if (estado == "Finalizado"){
                clientes_vista.viajes.calificacion.calificar(id_viaje, id_recurso);
            }else{
                auxiliar.mensaje("El viaje aún no finalizó para calificarlo.", 5000, 'toast-error');
            }
        },

        confirmar : function(){
            var id = $('#cv_id').val(); 
            var id_recurso = $('#cv_id_recurso').val();
            var calif = $('#cv_valor').val();
            var comentarios = $('#cv_comentario').val();

            if (calif > 0 ){
                auxiliar.espera.lanzar();
                $.ajax({
                    data: { id_viaje : id, id_recurso: id_recurso, calificacion: calif, comentarios: comentarios },
                    url:   '/PF/clientes/calificar_viaje',
                    type:  'post',
                    error: function(response){
                        auxiliar.espera.detener();
                        auxiliar.mensaje('Se produjo un error en la conexión.', 5000,'toast-error');
                        auxiliar.mensaje('El servidor no está respondiendo nuestra solicitud.', 5000,'toast-error');
                        auxiliar.mensaje('La calificación no se realizó correctamente.', 5000,'toast-error');
                    },
                    success: function (response){
                        var respuesta = JSON.parse(response);
                        auxiliar.espera.detener();
                        if (respuesta['error'] === undefined){
                            clientes_vista.viajes.calificacion.confirmar();
                            auxiliar.mensaje("El viaje se califico correctamente.", 'toast-ok', 3000);
                        }else{
                            auxiliar.mensaje(respuesta['error'], 5000, 'toast-error');
                        }
                    }
                });
            }else{
                auxiliar.mensaje("Debe emitir calificación.", 5000, 'toast-error');
            }
        }
    },
    
    check : function(){
        $.ajax({
            data:  {},
            url:   '/PF/clientes/check',
            type:  'post',
            error: function(response){
                auxiliar.mensaje('Se produjo un error en la conexión.', 5000,'toast-error');
                auxiliar.mensaje('El servidor no está respondiendo nuestra solicitud.', 5000,'toast-error');
                auxiliar.mensaje('No se puede chequear el estado de los pedidos en este momento.', 5000,'toast-error');
            },
            success: function (response){
                var respuesta = JSON.parse(response);
                if (respuesta['error'] === undefined){
                    var data = respuesta['data'];
                    mapa.marcas.dinamicas.eliminar();
                    clientes_vista.viajes.refresh_historial(data['historial_viajes']);
                    clientes_vista.viajes.refresh_estado_viajes(data['estado_viajes']);
                    clientes_vista.recursos.refresh_posiciones(data['estado_recursos']);
                }else{
                    auxiliar.mensaje('El servidor respondió la solicitud y notificó error.', 5000,'toast-error');
                }  
            }
        });
        setTimeout('clientes.viajes.check()', 5000); 
    }
    
}, //FIN VIAJES

};//FIN CLIENTES

$( document ).ready(function(){
    setTimeout('clientes.viajes.check()', 5000); 
    
    // Indicamos la lógica ante el hacer click sobre una estrella
    $('.estrellasValoracion').bind('click', function() {
        // Obtenemos la estrella sobre la que se hizo click
        var estrella = this;
        var puntaje = parseFloat($(estrella).attr('id')[1]);
        $('#cv_valor').val(puntaje);
    }); 
    
});
