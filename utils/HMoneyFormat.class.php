<?php
/*
 * Wrapper For Simple Class To Monet Format For Grids and More
 *
 * @version    5.1
 * @package    core
 * @author     Rodrigo Moglia
 * @copyright  Copyright (c) 2018 Interatia Sistemas de Informação. (http://www.interatia.com)
 * @license    http://www.adianti.com.br/framework-license
 */
 
  class HMoneyFormat
  {
      /**
     * Class Constructor
     */
      function __construct()
      {

      }
      
      public static function reais($value, $object = NULL, $row = NULL) 
      { 
            if(is_nan((float)$value) or empty($value)){ return ''; }
            if(is_numeric($value) == TRUE) { return 'R$ ' . number_format($value, 2, ',', '.'); } 
      }
      
      public static function numeric($value, $object = NULL, $row = NULL) 
      { 
          $value = str_replace(array('.','R$ '), array('',''), $value);
          $value = str_replace(',', '.', $value);    
          return $value;
      }    
  }
  
  
?>
