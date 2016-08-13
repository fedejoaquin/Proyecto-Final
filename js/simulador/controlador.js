var fecha;
var timeOut = 2000;
var refresh = 6;
var viajes_a_simular = [];

var simulador = {
    
    tiempo_timeOut : function(){ return timeOut; },
    
    tiempo_refresh : function(){ return refresh; },
       
    main : function(){
        
        //Actualiza y notifica la hora del sistema
        simulador.fecha.actualizar_notificar();
        
        //Consulta las simulaciones a generar.
        simulador.recorridos.consultar_nuevas_simulaciones();
        
        //Mueve los recursos y actualiza su ultima posición.
        simulador.recorridos.simular_paso();
        
        //Notifica la última posición de todos los recursos simulados.
        simulador.recorridos.informar_ultima_posicion();
        
        //Notifica aquellos viajes finalizados.
        simulador.recorridos.informar_finalizados();
        
        //Se corre una nueva iteración de simulación al cabo de tiempo_timeOut milisegundos.
        setTimeout('simulador.main()', simulador.tiempo_timeOut());
    },
       
    recorridos : {
        
        consultar_nuevas_simulaciones : function(){
            $.ajax({
                async: true,
                data: {},
                url:   '/PF/simulador/generar_nuevas_simulaciones',
                type:  'post',
                error: function(response){
                    auxiliar.mensaje('Nuevas simulaciones: FALLO', 2500,'toast-error');
                },
                success: function (response){
                    var respuesta = JSON.parse(response);
                    if (respuesta['error'] === undefined){
                        auxiliar.mensaje('Nuevas simulaciones: OK', 1000, 'toast-ok');
                        simulador.logica.construir_recorridos(respuesta['data']);
                    }else{
                        auxiliar.mensaje('Nuevas simulaciones: ERROR', 2500,'toast-error');
                        auxiliar.mensaje(respuesta['error'], 5000, 'toast-error');
                    }
                }
            });
        },// FIN CONSULTAR_NUEVAS_SIMULACIONES

        //Mueve los recursos y actualiza su ultima posición.
        simular_paso : function(){
            //Para cada viaje a simular
            for(i=0; i<viajes_a_simular.length; i++){
                var viaje = viajes_a_simular[i];
                var paso_actual = viaje.pasos[viaje.paso_actual];
                var pendiente = paso_actual.pendiente;
                var tita = paso_actual.tita;
                var velocidad = paso_actual.velocidad;
                
                //Latitud y longitud actual de la marca del viaje simulado.
                var latitud_actual = mapa.marcas.estaticas.latitud("t"+viaje.id);
                var longitud_actual = mapa.marcas.estaticas.longitud("t"+viaje.id);
                
                console.log("Latitud_actual:");
                console.log(latitud_actual);
                console.log("Longitud actual:");
                console.log(longitud_actual);
                
                //Distancia recorrida actualmente, proyectada a partir de la ubicacion actual.
                var recorrido_actual = latitud_actual / Math.sin(paso_actual.tita);
                
                //Tiempo requerido para llegar al punto final del paso actual, en funcion de velocidad del tramo.
                //var tRequerido = Math.abs(( paso_actual.fin - recorrido_actual) / ( pendiente * velocidad ));
                var tRequerido = Math.abs(( (paso_actual.fin.lat()/Math.sin( tita )) - recorrido_actual) / ( pendiente * velocidad ));
                
                
                console.log("Tiempo requerido");
                console.log(tRequerido);
                
                //Si aun se debe avanzar por sobre el tramo del paso actual
                if ( tRequerido >= ( simulador.tiempo_refresh() / 1000) ){
                    //Calculamos el avance a proyectar en funcion de la velocidad y el tiempo de refresh
                    var avance = Math.abs(pendiente) * velocidad * (simulador.tiempo_refresh()/1000);
                    
                    var nueva_posicion = {
                        lat: latitud_actual + avance * Math.sin(tita), 
                        lng: longitud_actual + avance * Math.cos(tita)
                    };
                    
                    console.log("Alcanzo el tiempo y actualice marca");
                    mapa.marcas.estaticas.cambiar("t"+viaje['id'], nueva_posicion);
                    
                    //Si el tiempo requerido es igual al step de simulacion, el step y/o el viaje estan terminados.
                    if ( tRequerido === ( simulador.tiempo_refresh()/1000) ){
                        if ((viaje.paso_actual + 1) === viaje.pasos_totales){
                            viaje.terminado = true;
                            console.log("Viaje finalizado con tRequerdio igual a simu y no mas pasos.");
                        }else{
                            console.log("Aumento paso con tRequerido igual a simu.");
                            viaje.paso_actual = viaje.paso_actual + 1;
                        }
                    }
                
                }else{
                    //Si aún quedan pasos por simular, avanzo lo que corresponda por sobre el siguiente paso
                    if ((viaje.paso_actual + 1) < viaje.pasos_totales){
                        
                        console.log("tRequerido menor a simu y mas pasos por realizar");
                        
                        //Calculamos la posicion final para el tramo finalizado
                        //var avance = Math.abs(pendiente) * velocidad * (tRequerido);
                        var nueva_posicion = paso_actual.fin;
                        /**var nueva_posicion = {
                            lat: latitud_actual + avance * Math.sin(tita), 
                            lng: longitud_actual + avance * Math.cos(tita)
                        };*/
                        
                        console.log("Nueva posicion:");
                        console.log(nueva_posicion);

                        //Calculamos el tiempo restante de la simulación que debe utilizarse en el proximo tramo
                        var tiempo_restante = (simulador.tiempo_refresh()/1000) - tRequerido;
                        
                        console.log("Tiempo restante");
                        console.log(tiempo_restante);
                        
                        //Estimamos los valores del siguiente paso.
                        var paso_siguiente = viaje.pasos[viaje.paso_actual+1];
                        var pendiente_siguiente = paso_siguiente.pendiente;
                        var tita_siguiente = paso_siguiente.tita;
                        var velocidad_siguiente = paso_siguiente.velocidad;
                        
                        //Estimamos el avance por sobre el paso siguiente considerando su velocidad y el tiempo restante.
                        var avance_siguiente = Math.abs(pendiente_siguiente) * velocidad_siguiente * (tiempo_restante);
                        
                        console.log("Avance siguiente");
                        console.log(avance_siguiente);
                        
                        var nueva_posicion_siguiente = {
                            lat: nueva_posicion.lat() + avance_siguiente * Math.sin(tita_siguiente), 
                            lng: nueva_posicion.lng() + avance_siguiente * Math.cos(tita_siguiente)
                        };
                        
                        console.log("Nueva posicion siguiente");
                        console.log(nueva_posicion_siguiente);
                        
                        //Actualizamos el valor del paso actual.
                        viaje.paso_actual = viaje.paso_actual + 1;
                        
                        //Actualizamos la marca para que se posiciones donde corresponde el el tramo siguiente.
                        mapa.marcas.estaticas.cambiar("t"+viaje['id'], nueva_posicion_siguiente);
                      
                    }else{
                        console.log("tRequerido es menor a simu y no hay mas pasos");
                        console.log(tRequerido);
                        
                        //Si no quedan pasos, el viaje está finalizado.
                        viaje.terminado = true;
                        
                        //Estimo el avance con el tiempo que se restaba simular y era menor al de refresh 
                        //var avance = Math.abs(pendiente) * velocidad * (tRequerido);
                        var nueva_posicion = paso_actual.fin;
                        /**var nueva_posicion = {
                            lat: latitud_actual + avance * Math.sin(tita), 
                            lng: longitud_actual + avance * Math.cos(tita)
                        };**/
                        mapa.marcas.estaticas.cambiar("t"+viaje['id'], nueva_posicion);
                    }
                }
            }
            
        }, //FIN SIMULAR PASO
        
        informar_ultima_posicion : function(){
            var ids_recursos = [];
            var lat_recursos = [];
            var long_recursos = [];
            
            //Si hay posiciones que notificar
            if (viajes_a_simular.length > 0){
                for(i=0; i<viajes_a_simular.length; i++){
                    var viaje = viajes_a_simular[i];
                    var id_viaje = viaje.id;
                    var id_recurso = viaje.id_recurso;
                    var lat = mapa.marcas.estaticas.latitud("t"+id_viaje);
                    var long = mapa.marcas.estaticas.longitud("t"+id_viaje);
                    ids_recursos.push(id_recurso);
                    lat_recursos.push(lat);
                    long_recursos.push(long);
                }
                $.ajax({
                    data: {ids_recursos: ids_recursos, latitudes : lat_recursos, longitudes: long_recursos },
                    url:   '/PF/simulador/actualizar_ultimas_posiciones',
                    type:  'post',
                    error: function(response){
                        auxiliar.mensaje('Últimas posiciones: FALLO', 2500,'toast-error');
                    },
                    success: function (response){
                        var respuesta = JSON.parse(response);
                        if (respuesta['error'] === undefined){
                            auxiliar.mensaje('Últimas posiciones: OK', 1000, 'toast-ok');
                        }else{
                            auxiliar.mensaje('Últimas posiciones: ERROR', 2500,'toast-error');
                            auxiliar.mensaje(respuesta['error'], 5000, 'toast-error');
                        }
                    }
                });
            }
        },
        
        //Notifica aquellos viajes finalizados.
        informar_finalizados : function(){
            //Viajes a notificar como finalizados
            var ids_viajes = [];
            var ids_recursos = [];
            var pos_a_eliminar = [];
            
            for(var i=0; i<viajes_a_simular.length; i++){
                var viaje = viajes_a_simular[i];
                if (viaje.terminado){
                    ids_viajes.push(viaje['id']); 
                    ids_recursos.push(viaje['id_recurso']);
                    pos_a_eliminar.push(i);
                }
            }
            
            if (pos_a_eliminar.length > 0){
                cantidad = pos_a_eliminar.length;
                for (i=0; i<cantidad; i++){
                    posEliminar = pos_a_eliminar[pos_a_eliminar.length - 1];
                    viajes_a_simular.splice(posEliminar, 1);
                    pos_a_eliminar.splice(pos_a_eliminar.length-1, 1);
                }
                
                $.ajax({
                    async: true,
                    data: { ids_pedidos: ids_viajes, ids_recursos: ids_recursos },
                    url:   '/PF/simulador/actualiza_viajes_finalizados',
                    type:  'post',
                    error: function(response){
                        auxiliar.mensaje('Actualizar finalizados: FALLO', 2500,'toast-error');
                    },
                    success: function (response){
                        var respuesta = JSON.parse(response);
                        if (respuesta['error'] === undefined){
                            auxiliar.mensaje('Actualizar finalizados: OK', 2500, 'toast-ok');
                            simulador_vista.recorridos.informar_finalizados(ids_viajes);
                        }else{
                            auxiliar.mensaje('Actualizar finalizados: ERROR', 2500,'toast-error');
                            auxiliar.mensaje(respuesta['error'], 5000, 'toast-error');
                        }
                    }
                });
            }
        } // FIN INFORMAR FINALIZADOS
    },
    
    logica : {
        construir_recorridos : function(nuevos_viajes){
            //Por cada viaje nuevo a simular
            for(i=0; i<nuevos_viajes.length; i++){
                var viaje = nuevos_viajes[i];
                
                var posOrigen = { lat: parseFloat(viaje['lat_recurso']), lng: parseFloat(viaje['long_recurso'])};
                var posIntermedia = { lat: parseFloat(viaje['lat_origen']), lng: parseFloat(viaje['long_origen'])};
                var posDestino = { lat: parseFloat(viaje['lat_destino']), lng: parseFloat(viaje['long_destino'])}; 
                
                mapa.marcas.estaticas.agregar("t"+viaje['id_pedido'], posOrigen);
                mapa.marcas.estaticas.agregar("o"+viaje['id_pedido'], posIntermedia);
                mapa.marcas.estaticas.agregar("d"+viaje['id_pedido'], posDestino);
                
                mapa.direcciones.dibujar(viaje['id_pedido'],viaje['id_recurso'],posOrigen, posIntermedia, posDestino, simulador.logica.generar_metadatos);
            }
        },
        
        generar_metadatos : function(recorrido, id_pedido, id_recurso){
            var KPH_TO_GPS = 1/312336;
            var steps = recorrido.routes[0].legs[0].steps;
            var viaje = { id : id_pedido, id_recurso: id_recurso, terminado: false, pasos_totales : steps.length, paso_actual : 0, pasos : [] };
            
            //Por cada paso requerido, generamos los metadatos del paso
            for(i=0; i<steps.length; i++){
                var paso = {pendiente: -1, tita: -1, fin: -1, velocidad: -1 };
                
                //Punto de origen y destino del step.
                var inicio = steps[i].start_location;
                var fin = steps[i].end_location;
                
                //Calculamos la pendiente de la recta y el angulo tita (para proyecciones respecto del sistema coordenado) 
                //que une los puntos de inicio y fin.
                paso.pendiente = ( ( fin.lat() - inicio.lat() ) / ( fin.lng() - inicio.lng() ) );
                paso.tita = Math.atan2( ( fin.lat() - inicio.lat()), ( fin.lng() - inicio.lng() ) );
                
                //Calculamos la latitud final proyectada segun el angulo tita, para la recta entre inicio y fin.
                //paso.fin = fin.lat() / Math.sin( paso.tita );
                paso.fin = fin;
                
                //Calculamos la velocidad real requerida por el tramo para cumplir con la demora estimada.
                paso.velocidad = (steps[i].distance.value / 1000) / (steps[i].duration.value / 3600 ) * KPH_TO_GPS;
                
                viaje.pasos.push(paso);
            }           
            viajes_a_simular.push(viaje);
        }
    },//FIN LOGICA
    
    
    fecha : {
        
        init: function(){
            fecha = new Date();
            simulador_vista.fecha.init( simulador.fecha.parsear(fecha) );
        },
       
        actualizar_notificar: function(){
            //Actualizo la hora sumandole los segundos del step.
            fecha.setSeconds(fecha.getSeconds() + simulador.tiempo_refresh());
            var fecha_parseada = simulador.fecha.parsear(fecha);
            
            simulador_vista.fecha.actualizar_notificar(fecha_parseada);
            
            $.ajax({
                data: { fecha_actual: fecha_parseada },
                url:   '/PF/simulador/actualizar_hora',
                type:  'post',
                error: function(response){
                    auxiliar.mensaje('Actualizar hora: FALLO', 2500,'toast-error');
                },
                success: function (response){
                    var respuesta = JSON.parse(response);
                    if (respuesta['error'] !== undefined){
                        auxiliar.mensaje('Actualizar hora: ERROR', 2500,'toast-error');
                        auxiliar.mensaje(respuesta['error'], 5000, 'toast-error');
                    }
                }
            });
        },
        
        parsear : function(fecha){
            dia = fecha.getDate();
            mes = fecha.getMonth() + 1;
            anio = fecha.getFullYear();
            
            hora = fecha.getHours();
            minutos = fecha.getMinutes();
            segundos = fecha.getSeconds();

            fecha_parseada = anio + "/" + (mes < 10 ? "0"+mes : mes ) + "/" + (dia < 10 ? "0"+dia : dia ) + " ";
            fecha_parseada += (hora < 10 ? "0"+hora : hora ) + ":" + (minutos<10 ? "0"+minutos : minutos) + ":" + (segundos<10 ? "0"+segundos : segundos);
            
            return fecha_parseada;
        }
    }

};//FIN SIMULADOR

$( document ).ready(function(){
    simulador.fecha.init();
    setTimeout('simulador.main()', simulador.tiempo_timeOut());
});
