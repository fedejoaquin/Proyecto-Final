<?php
$config = array(
'login/registrar_cliente' => array(
    array(
        'field'   => 'r_username', 
        'label'   => 'Nombre Usuario', 
        'rules'   => 'required|min_length[5]'
    ),
    array(
        'field' => 'r_telefono',
        'label' => 'Telefono',
        'rules' => 'required|min_length[8]'
    ),
    array(
       'field'   => 'r_password', 
       'label'   => 'Password', 
       'rules'   => 'required|min_length[5]'
    ),
    array(
       'field'   => 'r_confirm_password', 
       'label'   => 'Password confirmación', 
       'rules'   => 'required|min_length[5]|matches[r_password]'
    ), 
    
),//Fin login/registrar_cliente
    
'login/login_cliente' => array(
    array(
        'field'   => 'username', 
        'label'   => 'Nombre Usuario', 
        'rules'   => 'required'
    ),
    array(
       'field'   => 'password', 
       'label'   => 'Password', 
       'rules'   => 'required'
    ),    
),//Fin login/login_cliente
    
'login/login_empleado' => array(
    array(
        'field'   => 'i_dni', 
        'label'   => 'DNI', 
        'rules'   => 'required'
    ),
    array(
       'field'   => 'i_password', 
       'label'   => 'Password', 
       'rules'   => 'required'
    ),    
),//Fin login/login_empleado
    
); //Fin config
