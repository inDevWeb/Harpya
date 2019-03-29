<?php
/*
 * Simple Debuging Class For web application
 *
 * @version    5.1
 * @package    core
 * @author     Rodrigo Moglia
 * @copyright  Copyright (c) 2018 Interatia Sistemas de Informação. (http://www.interatia.com)
 * @license    http://www.adianti.com.br/framework-license
 */
 
class HDebug
{
    /**
     * Class Constructor
     */
    function __construct()
    {

    }
    
    public static function debug($var,$title='info')
    {
        $retorno = var_export($var, TRUE);
        new TMessage('info', "<b>$title</b>:<br/>".$retorno);        
    }
    
    public static function raw($var)
    {
        $retorno = var_export($var, TRUE);
        echo "<pre>$retorno</pre>";        
    }
    
    public static function box($var,$title='info')
    {
        $retorno = var_export($var, TRUE);
        echo "<h2>$title</h2><textarea cols='100' rows='10'>$retorno</textarea><br/>";        
    }
}
?>