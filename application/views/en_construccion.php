<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<!doctype html>

<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
        <title>Auto SMART :: En construcción </title>
        
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
                    <li><a class="modal-trigger" data-target="modal_nosotros">Nosotros</a></li>
                    <li><a href="<?php echo site_url(); ?>login/logout">Cerrar sesión</a></li>
                </ul>

                <!-- BOTONES CELULARES -->
                <ul id="nav-mobile" class="side-nav">
                    <li>
                        <a class="modal-trigger" data-target="modal_nosotros">
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
        
        <h5 class="row txt-cabecera">
            ¡Sección en Construcción!
        </h5>
        <h6 class="row txt-info">
            ..: Vista de <?php echo $funcion; ?> :..
        </h6>
        <div class="row center-align">
            <img src="<?php echo site_url()?>img/construccion/Boton_construccion.png" class="btn-en-construccion btn-floating transparent waves-effect waves-light">
            <img src="<?php echo site_url()?>img/construccion/Boton_construccion_1.png" class="btn-en-construccion btn-floating transparent waves-effect waves-light">
            <img src="<?php echo site_url()?>img/construccion/Boton_construccion_2.png" class="btn-en-construccion btn-floating transparent waves-effect waves-light">

        </div>
        <div class="row center-align">
            <a class="btn waves-effect tooltipped" data-tooltip="Volver" href="<?php echo site_url()?>intranet" >Volver</a>
        </div>
        
        <?php include 'componentes/modal_nosotros.phtml'; ?>

        <!-- FOOTER -->
        <?php include 'componentes/footer.phtml'; ?>
    </body>
</html>