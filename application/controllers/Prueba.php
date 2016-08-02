<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prueba extends CI_Controller {
    
    public function index(){
//      $from = "-38.7243,-62.2609|-38.7243,-62.2609|-38.7243,-62.2609";
//        $to = "-38.7017,-62.2702|-38.7272,-62.2275";
//        $to = "-38.7017,-62.2702";
//        $from = "-38.7243,-62.2609|-38.7295,-62.2673|-38.7295,-60.2673";
//      $to = "-38.7017,-62.2702";
        
//        $data = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$from."&destinations=".$to."&key=AIzaSyDroafadj_pILISoUsU2m1WJO7rxQGC4_M");
//        $json = json_decode($data);
//        print_r($json->rows);
//        
//        echo "<br><br><br>";
//        
//        $data = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$to."&destinations=".$from."&key=AIzaSyDroafadj_pILISoUsU2m1WJO7rxQGC4_M");
//        $json = json_decode($data);
//        print_r($json->rows);
//        
        
//        foreach($json->rows as $element){
//            echo $element->elements."<br>";
//            print_r($element);
//            echo "<br>";
//        }
//        
        $this->MConexiones->generar(36,-38.7234,-62.2598,-38.725,-62.2616);
    }
}
