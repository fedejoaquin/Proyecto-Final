<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Simulador extends CI_Controller {
    
    /**
     * Carga la vista del simulador.
     */
    public function index(){
        $this->load->view('simulador');
    }
    
    /**
     * Consulta si existen nuevas simulaciones que realizar; para esto chequea si existen recursos desocupados que a la vez
     * poseen viajes asignados. En el caso de existir recursos con viajes asignados, los despacha.
     * Retorna la información requerida por el simulador para ejecutar los viajes.
     * 
     * @return VIA AJAX
     * $resultado['data'] = Array(Id_pedido, Id_recurso, Origen, Destino, Lat_origen, Long_origen, Lat_recurso, Long_recurso, Lat_destino, Long_destino).
     * $resultado['error'] = Error en caso de corresponder.
     */
    public function generar_nuevas_simulaciones(){
        //$this->control_origen_ajax();
        
        $resultado = array(); 
        $resultado['data'] = array();
        
        //Obtenemos los recursos desocupados.
        $recursos = $this->MRecursos->get_desocupados_resumido();
        
        $this->db->trans_start();
        
        //Indicamos que se despachen aquellos pedidos asociados a los recursos desocupados.
        $retorno = $this->MPedidos->despachar($recursos);

        //Si el despacho fue correcto
        if ($retorno['resultado']){
            //Si se despacharon viajes
            if (count($retorno['pedidos_despachados'])!==0){
                //Se actualiza el estado de los recursos despachados por "Ocupado"
                if ($this->MRecursos->ocupar($retorno['recursos_ocupados'])){
                    //Se generan los metadatos y se retorna el resultado al simulador
                    $resultado['data'] = $this->MPedidos->get_datos_simulacion($retorno['pedidos_despachados']);
                    $this->db->trans_complete();
                }else{
                    $resultado['error'] = 'Se produjo un error al intentar ocupar los recursos';
                }
            }
        }else{
            $resultado['error'] = 'Se produjo un error al intentar despachar viajes';
        }
        
        echo json_encode($resultado);
    }    
    
    /**
     * Computa la finalización de un conjunto de viajes, y la liberación de sus recursos asociados, indicados por el simulador. 
     * Para esto modifica el estado de los viajes indicados en la tabla pedidos_procesados y de los recursos en la tabla Recursos,
     * indicándolos como finalizados y desocupados respectivamente. 
     * 
     * @return VIA AJAX
     * $resultado['data'] = Array().
     * $resultado['error'] = Error en caso de corresponder.
     */
    public function actualiza_viajes_finalizados(){
        $this->control_origen_ajax();
        
        $resultado = array(); 
        $resultado['data'] = array();
        
        $ids_pedidos = $this->input->post('ids_pedidos');
        $ids_recursos = $this->input->post('ids_recursos');
        
        //Si existen datos que procesar, y llegaron adecuadamente.
        if ((count($ids_pedidos) == count($ids_recursos)) && (count($ids_pedidos)!== 0)){
            $this->db->trans_start();
            
            //Indicamos con estado finalizado los pedidos procesados cuyos id son $ids_pedidos.
            if ( $this->MPedidos->finalizar($ids_pedidos) ){
                
                //Indicamos con estado desocupado los recursos cuyos id son $ids_recursos.
                if ( $this->MRecursos->liberar($ids_recursos) ){
                    $this->db->trans_complete();
                }else{
                    $resultado['error'] = 'Se produjo un error al intentar liberar recursos.';
                }
            }else{
                $resultado['error'] = 'Se produjo un error al intentar finalizar viajes';
            }   
        }else{
            $resultado['error'] = 'No existen datos, o se encuentran corrompidos.';
        }
        echo json_encode($resultado);
    }

    /**
     * Chequea que el origen de la petición actual sea vía ajax.
     * En caso de no serlo, redirige al index del sitio; caso contrario, no 
     * redirige.
     */
    private function control_origen_ajax(){
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            redirect(site_url());
        }
    }
    
    public function prueba(){
        print_r ($this->generar_nuevas_simulaciones());
    }
}
