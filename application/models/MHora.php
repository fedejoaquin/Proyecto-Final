<?php 
class MHora extends CI_Model {
    
    /**
     * Computa y retorna la hora actual del sistema.
     * $resultado['hora_actual'] = Hora actual que figura en el sistema.
     */
    public function get_hora(){
        $consulta = 'SELECT * ';
        $consulta .= 'FROM Hora ';
        
        $query = $this->db->query($consulta);
        $resultado = $query->row_array();
        
        return $resultado;
    }
    
    /**
     * Actualiza el registro que indica la hora del sistema.
     */
    public function set_hora($time){
        
        $datos = array(
            'hora_actual' => $time
        );
        
        return $this->db->update('Hora',$datos);
    }
}