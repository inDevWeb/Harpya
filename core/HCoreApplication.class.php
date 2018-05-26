<?php
/*
 * New Proposed Methods For AdiantiCoreAplicarion Basic structure to run a web application
 *
 * @version    5.1
 * @package    core
 * @author     Rodrigo Moglia
 * @copyright  Copyright (c) 2018 Interatia Sistemas de Informação. (http://www.interatia.com)
 * @license    http://www.adianti.com.br/framework-license
 */

class HCoreApplication
{
      /**
     * Class Constructor
     */
    function __construct()
    {

    }
      
    public static function setHistory($PARAM,$INDEX=NULL)
    {
        $SYSTEM_PROGRAMS = array
        (
            'Adianti\Base\TStandardSeek','LoginForm','AdiantiMultiSearchService','AdiantiUploaderService',
            'AdiantiAutocompleteService','EmptyPage','MessageList','SystemDocumentUploaderService',
            'NotificationList','SearchBox','SearchInputBox','SystemPageService','SystemPageBatchUpdate','SystemPageUpdate'
        );
          
        $REFERER_METHOD = isset($PARAM['method']) ? $PARAM['method'] : NULL;
        $REFERER_CLASS = isset($PARAM['class']) ? $PARAM['class'] : NULL;
        unset($PARAM['class']);
        unset($PARAM['method']);
        $referer = array('class'=>$REFERER_CLASS,'method'=>$REFERER_METHOD,'param'=>$PARAM);
        ((isset($_SESSION[APPLICATION_NAME]['referers'])))?($referers = $_SESSION[APPLICATION_NAME]['referers']):($referers=array());
        if(!empty($REFERER_CLASS))
        {
            if(empty(array_search($REFERER_CLASS, $SYSTEM_PROGRAMS)))
            {
                if((sizeof($referers) -1) == 4)
                {
                    array_shift($referers);
                }
                  
                if($referers[(sizeof($referers) -1)]['class']==$REFERER_CLASS)
                {
                    $referers[(sizeof($referers) -1)] = $referer;   
                }
                elseif(!empty($INDEX) and isset($referers[$INDEX]))
                {
                    $referers[$INDEX]=$referer;    
                }
                else
                {
                    $referers[]=$referer;
                }
                $_SESSION[APPLICATION_NAME]['referers'] = $referers;
            }  
        }
    }
      
    public static function linkBack($value, $level=1, $extra_param=array(), $color = null, $size = null, $decoration = null, $icon = null)
    {
        $action = HCoreApplication::actionBack($level,$extra_param);
        return new TActionLink($value, $action, $color = null, $size = null, $decoration = null, $icon = null);
    }
      
    public static function actionBack($level=1,$extra_param=array())
    {
        $level = abs((int) $level);                
        $referers = $_SESSION[APPLICATION_NAME]['referers'];   
        $idx = (sizeof($referers) - 1) - $level;
        $REFERER_CLASS = trim($referers[$idx]['class']);
        $REFERER_METHOD = trim($referers[$idx]['method']);
        $PARAM = array_merge($referers[$idx]['param'],$extra_param);
          
        $TAction = array();
        (!empty($REFERER_CLASS))?($TAction[0]=$REFERER_CLASS):('');
        (!empty($REFERER_METHOD))?($TAction[1]=$REFERER_METHOD):($TAction[1]=NULL);
          
        if($TAction[1]==NULL)
        {
            if(method_exists ( new $REFERER_CLASS, 'onReload' ))
            {
                  $TAction[1] = 'onReload';    
            }
        }
          
        if($TAction[1]==NULL)
        {
            if(method_exists ( new $REFERER_CLASS, 'onEdit' ))
            {
                $TAction[1] = 'onEdit';    
            }
        }
        
        $TAction = new TAction($TAction);
        (!empty($PARAM))?($TAction->setParameters($PARAM)):('');
        return $TAction;
    }
      
    public static function gotoBack($level=1,$extra_param=array()) 
    {
        $level = abs((int) $level);                
        $referers = $_SESSION[APPLICATION_NAME]['referers'];   
        $idx = (sizeof($referers) - 1) - $level;
        $REFERER_CLASS = $referers[$idx]['class'];
        $REFERER_METHOD = $referers[$idx]['method'];
        $PARAM = array_merge($referers[$idx]['param'],$extra_param);
        AdiantiCoreApplication::gotoPage($REFERER_CLASS, $REFERER_METHOD, $PARAM);
    }
      
    public static function loadBack($level=1,$extra_param=array()) 
    {
        $level = abs((int) $level);
        $referers = $_SESSION[APPLICATION_NAME]['referers'];   
        $idx = (sizeof($referers) - 1) - $level;
        $REFERER_CLASS = $referers[$idx]['class'];
        $REFERER_METHOD = $referers[$idx]['method'];
        $PARAM = array_merge($referers[$idx]['param'],$extra_param);
        AdiantiCoreApplication::loadPage($REFERER_CLASS, $REFERER_METHOD, $PARAM);
    }
}
?>
