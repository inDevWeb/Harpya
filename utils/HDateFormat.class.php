<?php
/*
 * Wrapper For Simple Class To Format Data For Grids and More
 *
 * @version    5.1
 * @package    core
 * @author     Rodrigo Moglia
 * @copyright  Copyright (c) 2018 Interatia Sistemas de Informação. (http://www.interatia.com)
 * @license    http://www.adianti.com.br/framework-license
 */
 
  class HDateFormat
  {
      /**
     * Class Constructor
     */
      function __construct()
      {

      }
      
      public static function date2br($value, $object = NULL, $row = NULL) 
      { 
            if(empty($value)){ return ''; }
            return TDate::date2br($value);
      }
      
      public static function date2us($value, $object = NULL, $row = NULL) 
      { 
          return TDate::date2us($value);
      }    
  }
  
  
?>
