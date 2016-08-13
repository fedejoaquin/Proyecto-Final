<?php 
class MConexiones extends CI_Model {
    
    public function generar($pid, $lat_origen, $long_origen, $lat_destino, $long_destino){
        
        $resultado = true;

        //Destinos desde donde y hacia donde hay que generar conexiones con el pedido actual.
        $destinos_desde_hacia = $this->MPedidos->get_sin_despachar();
        
        //Destinos desde donde hay que generar conexiones con el pedido actual.
        $destinos_desde = $this->MPedidos->get_despachados();
        
        //Recursos desde donde hay que generar conexiones con el origen del pedido actual. 
        $recursos_desocupados = $this->MRecursos->get_desocupados();
        
        $destino_unico = "$lat_origen,$long_origen";
        $conexiones_desde = "";
        $conexiones_desde_ids = array();
        
        $origen_unico = "$lat_destino,$long_destino";
        $conexiones_hacia = "";
        $conexiones_hacia_ids = array();
        
        $conexiones_recursos = "";
        $conexiones_recursos_ids = array();
        
        //Generacion de string para consulta por pedidos procesados y/o aceptados, que provocan
        //conexiones desde y hacia el pedido actual.
        // DESDE :: COORD. DESTINO PEDIDOS --->> COORD. ORIGEN PEDIDO ACTUAL
        // HACIA :: COORD. DESTINO PEDIDO ACTUAL --->> COOR. ORIGEN PEDIDOS
        foreach ($destinos_desde_hacia as $pedido){
            //Si no es el pedido que provoca la generación de las conexiones.
            if ( $pedido['id'] != $pid ){
                $conexiones_desde .= $pedido['lat_destino'].",".$pedido['long_destino']."|";
                array_push($conexiones_desde_ids, $pedido['id']);
                
                $conexiones_hacia .= $pedido['lat_origen'].",".$pedido['long_origen']."|";
                array_push($conexiones_hacia_ids, $pedido['id']);   
            }    
        }
        
        //Generacion de string para consulta por pedidos despachados y/o a_despachar, que provocan
        //conexiones hacia el pedido actual.
        // COORD. DESTINO PEDIDOS --->> COORD. ORIGEN PEDIDO ACTUAL
        foreach ($destinos_desde as $pedido){
            //Si no es el pedido que provoca la generación de las conexiones.
            if ($pedido['id'] != $pid ){
                $conexiones_desde .= $pedido['lat_destino'].",".$pedido['long_destino']."|";
                array_push($conexiones_desde_ids, $pedido['id']);
            }    
        }
        
        //Generacion de string para consulta por recursos libres, que provocan conexiones hacia el pedido actual.
        foreach($recursos_desocupados as $recurso){
            $conexiones_recursos .= $recurso['ult_latitud'].",".$recurso['ult_longitud']."|";
            array_push($conexiones_recursos_ids, $recurso['id']);
        }
        
        if (!(empty($conexiones_desde_ids))){
            $rta_desde = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$conexiones_desde."&destinations=".$destino_unico."&key=AIzaSyDroafadj_pILISoUsU2m1WJO7rxQGC4_M");
            $json_desde = json_decode($rta_desde);
        
            $cant = 0;
            foreach($json_desde->rows as $row){
                $element = $row->elements[0];
                $tupla = array(
                    'id_pedido_A' => $conexiones_desde_ids[$cant++],
                    'id_pedido_B' => $pid,
                    'demora' => $element->duration->value,
                    'distancia' => $element->distance->value
                );
                $resultado = $resultado && $this->db->insert('Conexiones',$tupla);
            }
        }
        
        if(!(empty($conexiones_hacia_ids))){
            $rta_hacia = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$origen_unico."&destinations=".$conexiones_hacia."&key=AIzaSyDroafadj_pILISoUsU2m1WJO7rxQGC4_M");
            $json_hacia = json_decode($rta_hacia);
            
            $cant = 0;
            foreach($json_hacia->rows[0]->elements as $element){
                $tupla = array(
                    'id_pedido_A' => $pid,
                    'id_pedido_B' => $conexiones_hacia_ids[$cant++],
                    'demora' => $element->duration->value,
                    'distancia' => $element->distance->value
                );
                $resultado = $resultado && $this->db->insert('Conexiones',$tupla);
            }
        }
        
        if(!(empty($conexiones_recursos_ids))){
            $rta_recursos = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$conexiones_recursos."&destinations=".$destino_unico."&key=AIzaSyDroafadj_pILISoUsU2m1WJO7rxQGC4_M");
            $json_recursos = json_decode($rta_recursos);
            
            $cant = 0;
            foreach($json_recursos->rows as $row){
                $element = $row->elements[0];
                $tupla = array(
                    'id_recurso' => $conexiones_recursos_ids[$cant++],
                    'id_pedido' => $pid,
                    'demora' => $element->duration->value,
                    'distancia' => $element->distance->value
                );
                $resultado = $resultado && $this->db->insert('Conexiones_taxis',$tupla);
            }
        }
        
        return $resultado;
    }
    
    /**
     * Elimina todas las conexiones existentes en las tablas Conexiones y Conexiones_taxi, referentes al 
     * pedido cuyo id es $id.
     * Retorna true o false indicando operacion exitosa o fallida.
     */
    public function eliminar($id){
        $this->db->trans_start();
        
        $this->db->where('id_pedido_a', $id);
        $resultado = $this->db->delete('Conexiones');
        
        $this->db->where('id_pedido_b', $id);
        $resultado = $resultado && $this->db->delete('Conexiones');
        
        $this->db->where('id_pedido', $id);
        $resultado = $resultado && $this->db->delete('Conexiones_taxis');
        
        if ($resultado){
            $this->db->trans_complete();
            return true;
        }else{
            return false;
        }
    }
}