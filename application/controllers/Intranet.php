<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Intranet extends CI_Controller {
    
    /**
     * Chequea que el usuario se encuentre logueado como empleado; en caso de tener más de un rol, carga la vista asociada
     * para seleccionar rol.
     * Redirige al rol correspondiente en caso de que el empleado posea sólo uno.
     * Redirige a Inicio si no hay sesión abierta.
     * Redirige a Clientes si hay sesión abierta como cliente.
     */
    public function index(){
        if (!($this->session->userdata('eid') === NULL)){
            if (count($this->session->userdata('roles'))>1){
                $this->load->view('intranet');
            }else{
                redirect(site_url().$this->session->userdata('roles')[0]);
            }
        }else{
            if (!($this->session->userdata('cid') === NULL)){
                redirect(site_url()."clientes");
            }else{
                redirect(site_url());
            }
        }
    }
}
