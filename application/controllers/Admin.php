<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {  
    /**
     * Chequea que el usuario se encuentre logueado como empleado.
     * Carga la vista cliente si hay sesión abierta como cliente.
     * Redirige a Intranet si hay sesión abierta como empleado, y no tiene permisos suficientes.
     * No redirige si hay sesión abierta como empleado, y tiene permisos para esta operación.
     * Redirige a Inicio si no hay sesión iniciada.
     */
    public function index(){
        //Chequea que el usuario esté logueado correctamente
        $this->check_login_redirect('admin');
        $data['funcion'] = 'Admin';
        $this->load->view('en_construccion', $data);
    }
    
    /**
     * Cheque el estado del usuario actual y redirige al controlador correspondiente;
     * si es empleado, y no tiene permisos de $rol, al controlador intranet; 
     * si es empleado, y tiene permisos de $rol, no redirige; 
     * si es cliente, al index de clientes;
     * si no es ni empleado ni cliente, al index del sitio.
     */
    private function check_login_redirect($rol){
        if (!($this->session->userdata('eid') === NULL)){
            if (!(in_array($rol, $this->session->userdata('roles')))){
                redirect(site_url()."intranet");
            }
        }else{
            if (($this->session->userdata('cid') === NULL)){
                redirect(site_url());
            }else{
                redirect(site_url()."clientes");
            }
        }
    }
}
