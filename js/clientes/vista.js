var clientes_vista = {
    
datos : {
    
    ver : function(){
        $("#div_nuevo_viaje").hide();
        $("#div_viaje_estado").hide();
        $("#div_mis_viajes").hide();
        $("#div_mis_datos").show("size");
    },
    
    editar : function(telefono){
        $("#telefono").val(telefono);
    }
},

viajes : {
    
    ver_historial : function(){
        $("#div_nuevo_viaje").hide();
        $("#div_viaje_estado").hide();
        $("#div_mis_datos").hide();
        $("#div_mis_viajes").show("size");
    },
    
    solicitar : function(){
        $("#div_nuevo_viaje").show("size");
        $("#div_viaje_estado").hide();
        $("#div_mis_viajes").hide();
        $("#div_mis_datos").hide();
    },
    
    estado : function(){
        $("#div_nuevo_viaje").hide();
        $("#div_viaje_estado").show("size");
        $("#div_mis_viajes").hide();
        $("#div_mis_datos").hide();
    },
    
    set_margen : function(minutos_adicionales){
        var fecha = new Date();
        fecha.setSeconds(fecha.getSeconds() + minutos_adicionales * 60);
        
        dia = fecha.getDate();
        mes = fecha.getMonth() + 1;
        hora = fecha.getHours();
        minutos = fecha.getMinutes();
        
        fecha_parseada = (dia < 10 ? "0"+dia : dia ) + "/" + (mes < 10 ? "0"+mes : mes ) + " - ";
        fecha_parseada += (hora < 10 ? "0"+hora : hora ) + ":" + (minutos<10 ? "0"+minutos : minutos);
        
        $("#hora_arribo").val(fecha_parseada);
    },
    
    info : function(id, ingreso,estado, conductor, patente, marca, modelo, color){
        $("#ev_id").val(id);
        $("#ev_ingreso").val(ingreso);
        $("#ev_estado").val(estado);
        $('#ev_conductor').val(conductor);
        $('#ev_patente').val(patente);
        $('#ev_marca').val(marca);
        $('#ev_modelo').val(modelo);
        $('#ev_color').val(color);
        $('#estado_viaje').openModal();
    },
    
    pre_confirmar : function(){
        $("#confirmar_viaje").openModal();
    },
    
    confirmar : function(){
        $("#confirmar_viaje").closeModal();
    },
    
    confirmar_post : function(datos){
        $("#tblEstadoViajes").empty();
        
        for(i=0; i<datos.length; i++){
            
            var row = datos[i];
            
            tr = $("<tr></tr>");
            
            td = $('<td></td>');
            $(td).text(row['origen']);
            $(tr).append(td);
            
            td = $('<td></td>');
            $(td).text(row['destino']);
            $(tr).append(td);
            
            td = $('<td></td>');
            $(td).text(row['max_arribo']);
            $(tr).append(td);
            
            td = $('<td></td>');
            $(td).text(row['estado']);
            $(tr).append(td);
            
            td = $('<td></td>');
            a = $('<a></a>');
            i_info = $('<i></i>');
            $(i_info).attr('class','material-icons');
            $(i_info).text('info');
            $(a).attr('class', 'btn-flat');
            $(a).attr('onClick', 'clientes.viajes.info("'+row['id']+'","'+row['ingreso']+'","'+row['estado']+'",'+row['id_recurso']+')');
            $(a).append(i_info);
            $(td).append(a);
            $(tr).append(td);
            
            $("#tblEstadoViajes").append(tr);
        }
        
        $("#confirmar_viaje").closeModal();

        $("#hora_arribo").val("");
        $("#origen_viaje").val("");
        $("#destino_viaje").val("");
        $("#referencia").val("");
        
        $("#div_nuevo_viaje").hide();
        $("#div_viaje_estado").show("size");
    },
    
    refresh_historial : function(datos){
        $("#tblHistorialViajes").empty();
        
        for(i=0; i<datos.length; i++){
            
            var row = datos[i];
            
            tr = $("<tr></tr>");

            td = $('<td></td>');
            $(td).text(row['fecha']);
            $(tr).append(td);
            
            td = $('<td></td>');
            $(td).text(row['origen']);
            $(tr).append(td);
            
            td = $('<td></td>');
            $(td).text(row['destino']);
            $(tr).append(td);
            
            td = $('<td></td>');
            if (row['max_arribo']){
                $(td).text('Sí');
            }else{
                $(td).text('No');
            }
            $(tr).append(td);
            
            td = $('<td></td>');
            $(td).text(row['nombre']);
            $(tr).append(td);
            
            $("#tblHistorialViajes").append(tr);
        }
    },
    
    refresh_estado_viajes : function(datos){
        $("#tblEstadoViajes").empty();
        
        for(i=0; i<datos.length; i++){
            
            var row = datos[i];
            
            tr = $("<tr></tr>");
            
            td = $('<td></td>');
            $(td).text(row['origen']);
            $(tr).append(td);
            
            td = $('<td></td>');
            $(td).text(row['destino']);
            $(tr).append(td);
            
            td = $('<td></td>');
            $(td).text(row['max_arribo']);
            $(tr).append(td);
            
            td = $('<td></td>');
            $(td).text(row['estado']);
            $(tr).append(td);
            
            td = $('<td></td>');
            a = $('<a></a>');
            a_1 = $('<a></a>');
            i_info = $('<i></i>');
            i_star = $('<i></i>');
            $(i_info).attr('class','material-icons');
            $(i_info).text('info');
            $(i_star).attr('class','material-icons');
            $(i_star).text('stars');
            $(a).attr('class', 'btn-floating');
            $(a).attr('onClick', 'clientes.viajes.info('+row['id']+',"'+row['ingreso']+'","'+row['estado']+'",'+row['id_recurso']+')');
            $(a_1).attr('class', 'btn-floating');
            $(a_1).attr('onClick', 'clientes.viajes.calificacion.calificar('+row['id']+','+row['id_recurso']+',"'+row['estado']+'")');
            $(a).append(i_info);
            $(a_1).append(i_star);
            $(td).append(a);
            $(td).append(a_1);
            $(tr).append(td);
            
            $("#tblEstadoViajes").append(tr);
            
            pos_o = {'lat': parseFloat(row['lat_origen']), 'lng': parseFloat(row['long_origen'])};
            pos_d = {'lat': parseFloat(row['lat_destino']), 'lng': parseFloat(row['long_destino'])};
            
            mapa.marcas.dinamicas.agregar(row['id'], "Origen_"+row['estado'], pos_o);
            mapa.marcas.dinamicas.agregar(row['id'], "Destino_"+row['estado'], pos_d);
        }
    },
    
    calificacion : {
        calificar : function(id_viaje, id_recurso){
            $('#cv_id').val(id_viaje);
            $('#cv_id_recurso').val(id_recurso);
            $('#cv_valor').val(0);
            $('#cv_comentario').val("");
            $('#calificar_viaje').openModal();
        },

        confirmar : function(){
            $('#calificar_viaje').closeModal();
        }
    }
    
},//FIN VIAJES

recursos : {
    
    refresh_posiciones : function(recursos){
        for (i=0; i<recursos.length; i++){
            recurso = recursos[i];
            pos = {'lat': parseFloat(recurso['ult_latitud']), 'lng': parseFloat(recurso['ult_longitud'])};
            mapa.marcas.dinamicas.agregar("R"+recurso['id_recurso'],"Recurso",pos);
        }
    }    
}//FIN RECURSOS

}//FIN CLIENTES   