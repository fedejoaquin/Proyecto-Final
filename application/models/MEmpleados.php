<?php 
class MEmpleados extends CI_Model {
           
    /**
     * Computa y retorna el registro de un empleado con dni $dni, si es que existe.
     */
    public function ver($dni){
        $consulta = 'SELECT * ';
        $consulta .= 'FROM Empleados ';
        $consulta .= 'WHERE dni = "'.$dni.'" ';
        
        $query = $this->db->query($consulta);
        $resultado = $query->row_array();
        
        return $resultado;
    }
    
    /**
     * Computa y retorna los roles de un empleado con dni $dni, si es que existe.
     */
    public function get_roles($dni){
        $consulta = 'SELECT rol ';
        $consulta .= 'FROM Empleado_roles ';
        $consulta .= 'WHERE dni ="'.$dni.'"';
        
        $query = $this->db->query($consulta);
        $resultado = $query->result_array();
        
        return $resultado;
    }
}
