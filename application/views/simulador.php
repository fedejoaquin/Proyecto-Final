<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<!doctype html>

<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
        <title>Auto SMART :: Simulador </title>
        
        <?php include 'componentes/recursos.phtml'; ?>
        <script src="<?php echo site_url(); ?>js/simulador/controlador.js"></script>
        <script src="<?php echo site_url(); ?>js/simulador/vista.js"></script>
        <script src="<?php echo site_url(); ?>js/mapa.js"></script>
        <script 
            async defer 
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBTSGP0llQCtftW0W4QVaEh60dzNyOv09M&callback=mapa.init">         
        </script> 
        
    </head>
    <body>
        <!-- BOTONERA PRINCIPAL -->
        <nav class="white" role="navigation">
            <div class="nav-wrapper container">
                <!-- BRAND LOGO -->
                <ul class="left">
                    <li><a class="brand-logo"><img class="img-logo" src="<?php echo site_url()?>img/Auto_Smart.png" /></a><li>
                </ul>
                
                <!-- BOTONES -->
                <ul class="right">
                    <li><a>Step avance:</a></li>
                    <li>
                        <p class="range-field">
                            <input type="range" id="tiempo_refresh" min="1" max="60" value="20" onchange="simulador.set_tiempo_refresh()"/>
                        </p>
                    </li>
                    <li><a id="fecha_sistema"></a></li>
                </ul>
            </div>
        </nav>
        <div class="row"></div>
        
        <!--DIV PARA VISUALIZACIÓN DEL MAPA -->
        <div class="row center-align">
            <div id="div_mapa" class="col m10 offset-m1 mapa_simulador">
            </div>
        </div> 
        
        <!-- MODAL NOSOTROS -->
        <?php include 'componentes/modal_nosotros.phtml'; ?>

        <!-- FOOTER -->
        <footer class="page-footer teal">
            <div class="footer-copyright">
                <div class="container">
                    Martín <b>BURON BRARDA</b> - Federico <b>JOAQUÍN</b> 
                </div>
            </div>
        </footer>
    </body>
</html>