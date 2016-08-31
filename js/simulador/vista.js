var simulador_vista = {
    
    estado : {
        cambiar : function(estado){
            if(estado === pausado){
                $('#btn_play_pause').text("play_circle_filled");
            }else{
                $('#btn_play_pause').text("pause_circle_outline"); 
            }
        }
    },

    recorridos : {
        informar_finalizados : function(ids_viajes, ids_recursos){
            for(i=0; i<ids_viajes.length; i++){
                mapa.marcas.estaticas.eliminar("o"+ids_viajes[i], false);
                mapa.marcas.estaticas.eliminar("d"+ids_viajes[i], false);
                mapa.marcas.estaticas.eliminar("R"+ids_recursos[i], false);
                mapa.direcciones.eliminar(ids_viajes[i]);
            }
        }
    }, //FIN RECORRIDOS
    
    recursos_no_disponibles : {
        
        consultar_graficar : function(data){
            mapa.marcas.dinamicas.eliminar();
            for(i=0; i<data.length; i++){
                recurso = data[i];
                pos = {'lat': parseFloat(recurso['ult_latitud']), 'lng': parseFloat(recurso['ult_longitud'])};
                mapa.marcas.dinamicas.agregar("R"+recurso['id'],"Recurso",pos);
            }
        }
        
    },//FIN RECURSOS NO DISPONIBLES

    fecha : {
        init: function(fecha){
            $('#fecha_sistema').text(fecha);
        },
        
        actualizar_notificar : function(fecha){
            $('#fecha_sistema').text(fecha);
        }
        
    } //FIN FECHA

}//FIN SIMULADOR_VISTA   