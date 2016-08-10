var viajes_graficados = [];

var simulador = {
    
    tiempo_refresh : function(){ return 5000; },
    
    main : function(){
        //Consulta las simulaciones a generar.
        simulador.recorridos.consultar_nuevas_simulaciones();
        
        //Mueve los recursos y actualiza su ultima posición.
        simulador.recorridos.simular_paso();
        
         //Notifica aquellos viajes finalizados.
        simulador.recorridos.informar_finalizados();
        
        //Se corre una nueva iteración de simulación al cabo de tiempo_refresh milisegundos.
        setTimeout('simulador.main()', simulador.tiempo_refresh());
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
                        auxiliar.mensaje('Nuevas simulaciones: OK', 2500, 'toast-ok');
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
            
        }, //FIN SIMULAR PASO
        
        //Notifica aquellos viajes finalizados.
        informar_finalizados : function(){
            
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
                
                mapa.direcciones.dibujar(viaje['id_pedido'],posOrigen, posIntermedia, posDestino, simulador.logica.generar_metadatos);
            }
        },
        
        generar_metadatos : function(response){
            
        }

    }

};//FIN SIMULADOR

$( document ).ready(function(){
    setTimeout('simulador.main()', simulador.tiempo_refresh());
});
