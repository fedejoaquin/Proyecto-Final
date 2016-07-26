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
    
    pre_confirmar : function(){
        $("#confirmar_viaje").openModal();
    },
    
    confirmar : function(datos){
        $("#tblEstadoViajes").empty();
        
        for(i=0; i<datos.length; i++){
            
            var row = datos[i];
            
            tr = $("<tr></tr>");
            
            td = $('<td></td>');
            $(td).text(row['id']);
            $(tr).append(td);

            td = $('<td></td>');
            $(td).text(row['ingreso']);
            $(tr).append(td);
            
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
            $(td).text(row['id']);
            $(tr).append(td);

            td = $('<td></td>');
            $(td).text(row['ingreso']);
            $(tr).append(td);
            
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
            
            $("#tblEstadoViajes").append(tr);
        }
    }
    
}//FIN VIAJES

}//FIN CLIENTES