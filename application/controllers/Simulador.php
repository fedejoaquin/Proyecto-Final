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
     * Computa la actualización de la hora del sistema en función del step de avance que
     * indica cada paso de simulación.
     * 
     * @return VIA AJAX
     * $resultado['data'] = Array().
     * $resultado['error'] = Error en caso de corresponder.
     */
    public function actualizar_hora(){
        $this->control_origen_ajax();
        
        $resultado = array(); 
        $resultado['data'] = array();
        
        $time = $this->input->post('fecha_actual');
        
        //Seteamos la nueva hora del sistema
        if (! $this->MHora->set_hora($time)){
            $resultado['error'] = 'No se pudo actualizar la hora correctamente';
        }else{
            $resultado['data'] = $time;
        }
        
        echo json_encode($resultado);
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
        $this->control_origen_ajax();
        
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
                //Se actualiza el estado de los recursos despachados por "Ocupado", y el Stress asociado.
                if ($this->MRecursos->ocupar($retorno['recursos_ocupados'])){
                    //Damos indicación a prolog que se efectivizó el despacho de los pedidos y recursos indicados.
                    if ($this->MSocket->indicar_despacho($retorno['pedidos_despachados'], $retorno['recursos_ocupados'])){
                        //Se generan los metadatos y se retorna el resultado al simulador
                        $resultado['data'] = $this->MPedidos->get_datos_simulacion($retorno['pedidos_despachados']);
                        $this->db->trans_complete();    
                    }else{
                        $resultado['error'] = 'Se produjo un error al enviar mensaje a prolog.';
                    }
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
            
            //Indicamos con estado finalizado los pedidos procesados cuyos id son $ids_pedidos, e indicamos la hora de egreso
            //en la tabla de Pedidos.
            if ( $this->MPedidos->finalizar($ids_pedidos) ){
                //Indicamos con estado desocupado los recursos cuyos id son $ids_recursos.
                if ( $this->MRecursos->liberar($ids_recursos) ){
                    //Damos indicación a prolog que se efectivizó el despacho de los pedidos y recursos indicados.
                    if ($this->MSocket->indicar_finalizacion($ids_recursos)){
                        $this->db->trans_complete();
                    }else{
                        $resultado['error'] = 'Se produjo un error al enviar mensaje a prolog.';
                    }
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
     * Computa la actualización de la posición de los recursos cuyos ids se encuentran en $ids_recursos.
     * Considera las latitudes y longitudes dentro de $latitudes y $longitudes respectivamente. 
     * 
     * @return VIA AJAX
     * $resultado['data'] = Array().
     * $resultado['error'] = Error en caso de corresponder.
     */
    public function actualizar_ultimas_posiciones(){
        $this->control_origen_ajax();
        
        $resultado = array(); 
        $resultado['data'] = array();
        
        $ids_recursos = $this->input->post('ids_recursos');
        $latitudes = $this->input->post('latitudes');
        $longitudes = $this->input->post('longitudes');
        
        //Si existen datos que procesar, y llegaron adecuadamente.
        if ((count($ids_recursos) == count($latitudes)) && (count($latitudes) == count($longitudes)) && (count($ids_recursos)!== 0)){
            //Indicamos la ultima posicion de los recursos cuyos id son $ids_recursos.
            if ( ! $this->MRecursos->actualizar_posiciones($ids_recursos, $latitudes, $longitudes) ){
                $resultado['error'] = 'Se produjo un error al intentar liberar recursos.';
            }
        }else{
            $resultado['error'] = 'No existen datos, o se encuentran corrompidos.';
        }
        echo json_encode($resultado);
    }
    
    /**
     * Consulta y retorna la colección de recursos desocupados . 
     * 
     * @return VIA AJAX
     * $resultado['data'] = Array().
     * $resultado['error'] = Error en caso de corresponder.
     */
    public function consultar_recursos_no_disponibles(){
        $this->control_origen_ajax();
        
        $resultado = array(); 
        $resultado['data'] = array();
        
        $resultado['data'] = $this->MRecursos->get_desocupados();
        
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
}
