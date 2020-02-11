<?php

namespace App\Models;
require_once 'Printable.php';



class BaseElement implements Printable {
    private $title;
    public $description;
    public $visible = true;
    public $months;
  
  
    public function __construct($title , $description){  //El método constructor nos permitirá inicializar valores por default, así como también pasar datos como parámetro al momento de inicializar un objeto.
      $this->title = $title;
      $this->description = $description;
    }
  
    public function setTitle($t){
      if($t == '') {
        $this->title  = 'N/A';  //en metodos podemos hacer validaciones 
      }else {
        $this->title = $t;
      }
    }
  
    public function getTitle() {
      return $this->title;  //para poder haceder a una variable privada 
    }
  
     public function getDurationAsString() {
      $years = floor($this->months / 12);
      $extraMonths = $this->months % 12;
    
      if ($years == 0) {
        echo  "$extraMonths months";
      } elseif ($extraMonths == 0) {
         echo " $years years ";
        } else {
          echo "$years years $extraMonths months";
        };
    }

    public function getDescription() {
      return $this->description;
  }
  
  };