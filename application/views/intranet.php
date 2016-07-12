<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>

<!doctype html>

<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
        <title>Auto SMART :: Intranet </title>
        <?php include 'componentes/recursos.phtml'; ?>
    </head>
    <body>
        <!-- BOTONERA PRINCIPAL -->
        <nav class="white" role="navigation">
            <div class="nav-wrapper container">
                
                <!-- BOTONES PANTALLA NORMAL -->
                <ul class="left">
                    <li><a class="brand-logo" href="<?php echo site_url(); ?>"><img class="img-logo" src="<?php echo site_url()?>img/Auto_Smart.png"</a></li>
                </ul>

                <ul class="right hide-on-med-and-down">
                    <li><a class="modal-trigger" data-target="nosotros">Nosotros</a></li>
                    <li><a href="<?php echo site_url(); ?>login/logout">Cerrar sesión</a></li>
                </ul>

                <!-- BOTONES CELULARES -->
                <ul id="nav-mobile" class="side-nav">
                    <li>
                        <a class="modal-trigger" data-target="nosotros">
                            <i class="material-icons left">info</i>
                            Nosotros
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo site_url(); ?>login/logout">
                            <i class="material-icons left">account_circle</i>
                            Cerrar sesión
                        </a>
                    </li>
                </ul>
                
                <!-- ICONO MENU PARA CELULARES -->
                <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
            </div>
        </nav>

        <!-- CUERPO PRINCIPAL -->
        <h5 class="row txt-cabecera">¡Bienvenido al sistema Auto-SMART!</h5>
        <h6 class="row txt-info">..: Seleccione el rol a utilizar :..</h6>
        
        <div class="row">
            <div class="col s12 center-align">
                <?php
                    $roles = $this->session->userdata('roles');
                    if (in_array("admin", $roles)){ ?>
                        <a class="tooltipped" data-tooltip="Admin" href="<?php echo site_url() ?>admin">
                            <img src="<?php echo site_url()?>img/perfiles/admin.png" class="btn-perfil btn-floating transparent waves-effect waves-light">
                        </a>
                <?php } ?>
                <?php if (in_array("operador", $roles)){ ?>
                        <a class="tooltipped" data-tooltip="Operador" href="<?php echo site_url() ?>operador">
                            <img src="<?php echo site_url()?>img/perfiles/operador.png" class="btn-perfil btn-floating transparent waves-effect waves-light">
                        </a>
                <?php } ?>
                <?php if (in_array("conductor", $roles)){ ?>
                        <a class="tooltipped" data-tooltip="Conductor" href="<?php echo site_url() ?>taxista">
                            <img src="<?php echo site_url()?>img/perfiles/conductor.png" class="btn-perfil btn-floating transparent waves-effect waves-light">
                        </a>
                <?php } ?>
            </div>
        </div>
              
        <?php include 'componentes/modal_nosotros.phtml'; ?>

        <!-- FOOTER -->
        <?php include 'componentes/footer.phtml'; ?>
        
    </body>
</html>

