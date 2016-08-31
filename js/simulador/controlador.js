var fecha;
var timeOut = 2000;
var refresh = 20;
var viajes_a_simular = [];
var pausado = 1009;
var corriendo = 1010;
var estado = pausado;

var simulador = {
    
    set_tiempo_refresh : function(){
        refresh = parseInt($('#tiempo_refresh').val());
    },
    
    tiempo_timeOut : function(){ return timeOut; },
    
    tiempo_refresh : function(){ return refresh; },
    
    estado : {
        cambiar : function(){
            if (estado===pausado){
                estado = corriendo;
                simulador_vista.estado.cambiar(corriendo);
            }else{
                estado = pausado;
                simulador_vista.estado.cambiar(pausado);
            }
        }
    },
       
    main : function(){
        
        //Si la simulación no está pausada.
        if (estado === corriendo){

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
            
            //Actualiza y establece la posición de los recursos disponibles
            simulador.recursos_no_disponibles.consultar_graficar();
        
        }
        
        //Se corre una nueva iteración de simulación al cabo de tiempo_timeOut milisegundos.
        setTimeout('simulador.main()', simulador.tiempo_timeOut());
    },
       
    recorridos : {
        
        consultar_nuevas_simulaciones : function(){
            $.ajax({
                async: false,
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
            
            for(i=0; i<viajes_a_simular.length; i++){
                var viaje = viajes_a_simular[i];
                
                //Metadatos asociados al paso a simular del viaje actual.
                var paso_actual = viaje.pasos[viaje.paso_actual];
                var tita = paso_actual.tita;
                var velocidad = viaje.velocidad;
                
                //Variables auxiliares
                var tSobrante = 0;
                
                //Latitud y longitud actual de la marca del viaje simulado.
                var latitud_actual = mapa.marcas.estaticas.latitud("R"+viaje['id_recurso']);
                var longitud_actual = mapa.marcas.estaticas.longitud("R"+viaje['id_recurso']);

                //Distancia recorrida actualmente, proyectada a partir de la latitud de la ubicación actual.
                var recorrido_actual = latitud_actual / Math.sin(tita);

                //Tiempo requerido para llegar al punto final del paso actual, en función de velocidad del tramo.
                var tRequerido = Math.abs(((paso_actual.fin.lat() / Math.sin(tita)) - recorrido_actual) / (velocidad * (0.85 + 0.15 * Math.random())) );
               
                //Si aún se debe avanzar por sobre el tramo del paso actual.
                if ( tRequerido >= ( simulador.tiempo_refresh() ) ){
                        
                    var nueva_posicion = {
                        lat: latitud_actual + velocidad * simulador.tiempo_refresh() * Math.sin(tita), 
                        lng: longitud_actual + velocidad * simulador.tiempo_refresh() * Math.cos(tita)
                    };
                    
                    //Actualizamos la ubicación de la marca.
                    mapa.marcas.estaticas.cambiar("R"+viaje['id_recurso'], nueva_posicion);

                    //Si el tiempo requerido es igual al step de simulación, el step y/o el viaje están terminados.
                    if ( tRequerido === ( simulador.tiempo_refresh() ) ){
                        if ((viaje.paso_actual + 1) === viaje.pasos_totales){
                            viaje.paso_actual = viaje.paso_actual + 1;
                            viaje.terminado = true;
                        }else{
                            viaje.paso_actual = viaje.paso_actual + 1;
                        }
                    }
                }else{
                    //Indicamos que el tiempo sobrante es el tiempo de refresh de la simulación.
                    tSobrante = simulador.tiempo_refresh();

                    //Mientras el tRequerido por el step al que voy a saltear sea menor al tSobrante de los steps salteados, 
                    //y tenga pasos para saltear, los salteo.
                    while((tRequerido < tSobrante) && ((viaje.paso_actual + 1) !== viaje.pasos_totales)){

                        //Decrementamos el tSobrante en función del tRequerido por el paso salteado.
                        tSobrante -= tRequerido;
                        
                        //Actualizamos la posición inicial del viaje simulado, como la posición final del paso salteado.
                        latitud_actual = paso_actual.fin.lat();
                        longitud_actual = paso_actual.fin.lng();

                        //Avanzamos hacia el próximo step del viaje simulado.
                        viaje.paso_actual = viaje.paso_actual + 1;

                        //Obtenemos los metadatos asociados al nuevo paso actual simulado.
                        paso_actual = viaje.pasos[viaje.paso_actual];
                        tita = paso_actual.tita;

                        //Distancia recorrida actualmente, proyectada a partir de la latitud de la nueva ubicación actual.
                        recorrido_actual = latitud_actual / Math.sin(tita);

                        //Tiempo requerido para llegar al punto final del nuevo paso actual, en función de velocidad del tramo.
                        tRequerido = Math.abs(((paso_actual.fin.lat() / Math.sin(tita)) - recorrido_actual) / (velocidad * (0.85 + 0.15 * Math.random())) );
                    }

                    //Si avancé hasta un step en el que se requiere un tiempo mayor o igual al sobrante de la simulación actual
                    if (tRequerido >= tSobrante ){
                        //Actualizamos la posición del viaje.
                        var nueva_posicion = {
                            lat: latitud_actual + velocidad * tSobrante * Math.sin(tita), 
                            lng: longitud_actual + velocidad * tSobrante * Math.cos(tita)
                        };

                        //Actualizamos la ubicación de la marca.
                        mapa.marcas.estaticas.cambiar("R"+viaje['id_recurso'], nueva_posicion);

                        //Si el tiempo requerido es igual al tiempo que sobró del step de simulación, el step y/o el viaje están terminados.
                        if ( tRequerido === tSobrante ){
                            if ((viaje.paso_actual + 1) === viaje.pasos_totales){
                                viaje.paso_actual = viaje.paso_actual + 1;
                                viaje.terminado = true;
                            }else{
                                viaje.paso_actual = viaje.paso_actual + 1;
                            }
                        }
                    }else{
                        //El tiempo tRequerido < tSobrante pero no tengo más pasos que saltear, el viaje está finalizado.
                        //Actualizamos la posición del viaje simulado, como la posición final del paso salteado.
                        var nueva_posicion = {
                            lat: paso_actual.fin.lat(), 
                            lng: paso_actual.fin.lng()
                        };
                        
                        //Actualizamos la ubicación de la marca.
                        mapa.marcas.estaticas.cambiar("R"+viaje['id_recurso'], nueva_posicion);
                        
                        viaje.paso_actual = viaje.paso_actual + 1;
                        viaje.terminado = true;
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
                    var id_recurso = viaje.id_recurso;
                    var lat = mapa.marcas.estaticas.latitud("R"+id_recurso);
                    var long = mapa.marcas.estaticas.longitud("R"+id_recurso);
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
                    async: false,
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
                            simulador_vista.recorridos.informar_finalizados(ids_viajes, ids_recursos);
                        }else{
                            auxiliar.mensaje('Actualizar finalizados: ERROR', 2500,'toast-error');
                            auxiliar.mensaje(respuesta['error'], 5000, 'toast-error');
                        }
                    }
                });
            }
        } // FIN INFORMAR FINALIZADOS
    }, //FIN RECORRIDOS
    
    logica : {
        construir_recorridos : function(nuevos_viajes){
            //Por cada viaje nuevo a simular
            for(i=0; i<nuevos_viajes.length; i++){
                var viaje = nuevos_viajes[i];
                
                var posOrigen = { lat: parseFloat(viaje['lat_recurso']), lng: parseFloat(viaje['long_recurso'])};
                var posIntermedia = { lat: parseFloat(viaje['lat_origen']), lng: parseFloat(viaje['long_origen'])};
                var posDestino = { lat: parseFloat(viaje['lat_destino']), lng: parseFloat(viaje['long_destino'])}; 
                
                mapa.marcas.estaticas.agregar("R"+viaje['id_recurso'], posOrigen, "Recurso_Despachado");
                mapa.marcas.estaticas.agregar("o"+viaje['id_pedido'], posIntermedia, "Origen_Despachado");
                mapa.marcas.estaticas.agregar("d"+viaje['id_pedido'], posDestino, "Destino_Despachado");
                
                mapa.direcciones.dibujar(viaje['id_pedido'],viaje['id_recurso'],posOrigen, posIntermedia, posDestino, simulador.logica.generar_metadatos);
            }
        },
        
        generar_metadatos : function(recorrido, id_pedido, id_recurso){
            var KPH_TO_GPS = 1/312336; //365000; //400748;//365000;
            var steps = recorrido.routes[0].overview_path;
            
            var distancia = recorrido.routes[0].legs[0].distance.value;
            var duracion = recorrido.routes[0].legs[0].duration.value;
            var velocidad = (distancia/ 1000) / (duracion / 3600 ) * KPH_TO_GPS;
            
            var viaje = { id : id_pedido, id_recurso: id_recurso, terminado: false, pasos_totales : steps.length-1, paso_actual : 0, pasos : [], velocidad: velocidad};
               
            //Por cada paso requerido, generamos los metadatos del paso.
            for(i=0; i+1<steps.length; i++){
                var paso = {tita: -1, fin: -1};
              
                //Punto de origen y destino del step.
                var inicio = steps[i];
                var fin = steps[i+1];
                
                //Calculamos el angulo tita (para proyecciones respecto del sistema coordenado) que une los puntos 
                //de inicio y fin.
                paso.tita = Math.atan2((fin.lat() - inicio.lat()), (fin.lng() - inicio.lng() ));
                
                //Indicamos la coordenada de finalización del paso.
                paso.fin = fin;
                
                //Agregamos el paso al viaje.
                viaje.pasos.push(paso);
            }
            
            //Agregamos al viaje con sus respectivos pasos, como uno más para simular.
            viajes_a_simular.push(viaje);
        }
        
    },//FIN LOGICA
    
    recursos_no_disponibles : {
        consultar_graficar : function(){
            $.ajax({
                data: {},
                url:   '/PF/simulador/consultar_recursos_no_disponibles',
                type:  'post',
                error: function(response){
                    auxiliar.mensaje('Recursos no disponibles: FALLO', 2500,'toast-error');
                },
                success: function (response){
                    var respuesta = JSON.parse(response);
                    if (respuesta['error'] === undefined){
                        simulador_vista.recursos_no_disponibles.consultar_graficar(respuesta['data']);
                    }else{
                        auxiliar.mensaje('Recursos no disponibles: ERROR', 2500,'toast-error');
                        auxiliar.mensaje(respuesta['error'], 5000, 'toast-error');
                    }
                }
            });
        }
    }, //FIN RECURSOS NO DISPONIBLES
    
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
    } //FIN FECHA

};//FIN SIMULADOR

$( document ).ready(function(){
    simulador.fecha.init();
    setTimeout('simulador.main()', simulador.tiempo_timeOut());
});
