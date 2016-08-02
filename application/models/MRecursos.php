<?php 
class MRecursos extends CI_Model {
    
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
}