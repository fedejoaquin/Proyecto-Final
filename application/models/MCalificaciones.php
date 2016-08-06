<?php 
class MCalificaciones extends CI_Model {
    
    /**
     * Computa el alta de una calificacion, registrándola en las tablas Calificaciones con los datos parametrizados.
     * Retorna el ID del registro creado, si la operación fue exitosa; -1 en caso contrario.
     */
    public function alta($dni, $calificacion, $comentarios){
        
        $datos = array(
            'dni' => $dni,
            'calificacion' => $calificacion,
            'comentarios' => $comentarios
        );
        
        //Alta de la calificación.
        $resultado = $this->db->insert('Calificaciones',$datos);
        
        //Consultamos el ID de la calificación dada de alta.
        $id_pedido = $this->db->insert_id();
        
        if ($resultado){
            return $id_pedido;
        }else{
            return -1;
        }
    }
}