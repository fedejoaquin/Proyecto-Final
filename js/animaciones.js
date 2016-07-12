//Animaciones javascript via Materialize

$(document).ready(function(){
    $('.button-collapse').sideNav();
    $('.parallax').parallax();
    $('.modal-trigger').leanModal();
    $('ul.tabs').tabs();
    
    $("#btn-registrar").click(function() {
        $("#div_ingresar").hide();
        $("#div_registrar").show("size");
    });
    
    $("#btn-ingresar").click(function() {
        $("#div_registrar").hide();
        $("#div_ingresar").show("size");
    });
});