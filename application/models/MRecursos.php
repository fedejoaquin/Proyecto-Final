<?php 
class MRecursos extends CI_Model {
    
    /**
     * Computa la liberación de los recursos indicados en $ids_recursos, asignándoles el estado "Desocupado"
     * en la tabla Recursos.
     * Retorna true o false, indicando operación exitosa o fallida.
     */
    public function liberar($ids_recursos){
        
        $data = array( 'estado' => 'Desocupado' );
        $this->db->where_in('id', $ids_recursos);
        return $this->db->update('Recursos', $data);
    }
    
    /**
     * Computa la ocupación de los recursos indicados en $ids_recursos, asignándoles el estado "Ocupado"
     * en la tabla Recursos, como así también actualizando el valor de su Stress asociado.
     * Retorna true o false, indicando operación exitosa o fallida.
     */
    public function ocupar($recursos){
        $resultado = true;
        
        foreach($recursos as $recurso){
            $consulta = 'UPDATE Recursos ';
            $consulta .= 'SET estado = "Ocupado", stress = stress +'.$recurso['stress'].' ';
            $consulta .= 'WHERE id = '.$recurso['id'];
            
            $resultado = $resultado && $this->db->query($consulta);
        }
       
        return $resultado;
    }
    
    /**
     * Computa la actualización de las posiciones de los recursos cuyos ids pertenecen a $ids.
     * Considera las latitudes y longitudes indicadas en $latitudes y $longitides.
     * Retorna true o false, indicando operación exitosa o fallida.
     */
    public function actualizar_posiciones($ids, $latitudes, $longitudes){
        $resultado = true;
        for($i=0; $i<count($ids); $i++){
            $data = array(
                'ult_latitud' => $latitudes[$i],
                'ult_longitud' => $longitudes[$i]
            );
            $this->db->where('id', $ids[$i]);
            $resultado = $resultado && $this->db->update('Recursos', $data);
        }
        return $resultado;
    }
    
    /**
     * Computa y retorna los registros de recursos que se encuentran desocupados
     * (estado = desocupado)
     * $resultado = Array(Id, Dni, Patente, Ult_latitud, Ult_longitud).
     */
    public function get_desocupados(){
        $consulta = 'SELECT id, dni, patente, ult_latitud, ult_longitud ';
        $consulta .= 'FROM Recursos ';
        $consulta .= 'WHERE estado = "Desocupado" ';
        
        $query = $this->db->query($consulta);
        $resultado = $query->result_array();
        
        return $resultado;
    }
    
        /**
     * Computa y retorna los registros de recursos que se encuentran desocupados
     * (estado = desocupado)
     * $resultado = Array(Id).
     */
    public function get_desocupados_resumido(){
        $consulta = 'SELECT id ';
        $consulta .= 'FROM Recursos ';
        $consulta .= 'WHERE estado = "Desocupado" ';
        
        $query = $this->db->query($consulta);
        $resultado = $query->result_array();
        
        return $resultado;
    }
    
    /**
     * Computa y retorna la información asociada a un recurso cuyo id es $id.
     * $resultado = Array(Id, Dni, Nombre, Patente, Marca, Modelo, Color).
     */
    public function get_info($id){
        $consulta = 'SELECT r.id, r.dni, e.nombre, r.patente, t.marca, t.modelo, t.color ';
        $consulta .= 'FROM ( Recursos r LEFT JOIN Empleados e ON r.dni = e.dni ) ';
        $consulta .= 'LEFT JOIN Taxis t ON r.patente = t.patente ';
        $consulta .= 'WHERE r.id = '.$id;
        
        $query = $this->db->query($consulta);
        $resultado = $query->row_array();
        
        return $resultado;        
    }
    
    /**
     * Computa y retorna los recursos asociados a un dado cliente cuyo id es $cid,
     * contemplando a aquellos pedidos que se encuentran en estado de despacho o atención
     * efectiva (estado = A_despachar, Despachado).
     * $resultado = Array(Id_pedido, Id_recurso, Ult_latitud, Ult_longitud).
     */
    public function get_asociados($cid){
        $consulta = 'SELECT p.id as id_pedido, r.id as id_recurso, r.ult_latitud, r.ult_longitud  ';
        $consulta .= 'FROM ( Pedidos p LEFT JOIN Pedidos_procesados pp ON p.id = pp.id_pedido ) ';
        $consulta .= 'LEFT JOIN Recursos r ON r.id = pp.id_recurso ';
        $consulta .= 'WHERE ( p.id_cliente = '.$cid.') AND ( pp.estado = "A_despachar" OR pp.estado = "Despachado") ';
        
        $query = $this->db->query($consulta);
        $resultado = $query->result_array();
        
        return $resultado;    
    }
}