<?php
/**
 * JQuery dialog container with proposed updates
 *
 * @version    5.1
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @author     Rodrigo Moglia
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @copyright  Copyright (c) 2018 Interatia Sistemas de Informação. (http://www.interatia.com)
 * @license    http://www.adianti.com.br/framework-license
 */
class HJQueryDialog extends TElement
{
    private $actions;
    private $width;
    private $height;
    private $top;
    private $left;
    private $modal;
    private $draggable;
    private $resizable;
    private $useOKButton;
    private $useCloseButton;
    private $stackOrder;
    private $closeText;
    private static $router;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct()
    {
        parent::__construct('div');
        $this->useOKButton = TRUE;
        $this->useCloseButton = TRUE;
        $this->closeText = 'Close';
        $this->top = NULL;
        $this->left = NULL;
        $this->modal = 'true';
        $this->draggable = 'true';
        $this->resizable = 'true';
        $this->stackOrder = 2000;
        $this->{'id'} = 'jquery_dialog_'.mt_rand(1000000000, 1999999999);
        $this->{'style'}="overflow:auto";
    }
    
    /**
     * Define if will use OK Button
     * @param $bool boolean
     */
    public function setUseOKButton($bool)
    {
        $this->useOKButton = $bool;
    }
    
    /**
     * Define if will use CLOSE Button
     * @param $bool boolean
     */
    public function setUseCloseButton($bool)
    {
        $this->useCloseButton = $bool;
    }
    
    /**
     * Define the dialog title
     * @param $title title
     */
    public function setTitle($title)
    {
        $this->{'title'} = $title;
    }
    
     /**
     * When Closes the dialog
     */
    public function setCloseText($text)
    {
        $this->closeText = $text;
    }
    
    /**
     * Turn on/off modal
     * @param $modal Boolean
     */
    public function setModal($bool)
    {
        $this->modal = $bool ? 'true' : 'false';
    }
    
    /**
     * Turn on/off resizeable
     * @param $bool Boolean
     */
    public function setResizable($bool)
    {
        $this->resizable = $bool ? 'true' : 'false';
    }
    
    /**
     * Turn on/off draggable
     * @param $bool Boolean
     */
    public function setDraggable($bool)
    {
        $this->draggable = $bool ? 'true' : 'false';
    }
    
    /**
     * Returns the element ID
     */
    public function getId()
    {
        return $this->{'id'};
    }
    
    /**
     * Define the dialog size
     * @param $width width
     * @param $height height
     */
    public function setSize($width, $height)
    {
        $this->width  = $width  < 1 ? "\$(window).width() * $width" : $width;
        
        if (is_null($height))
        {
            $this->height = "'auto'";
        }
        else
        {
            $this->height = $height < 1 ? "\$(window).height() * $height" : $height;
        }
    }
    
    /**
     * Define the dialog position
     * @param $left left
     * @param $top top
     */
    public function setPosition($left, $top)
    {
        $this->left = $left;
        $this->top  = $top;
    }
    
    /**
     * Add a JS button to the dialog
     * @param $label button label
     * @param $action JS action
     */
    public function addAction($label, $action)
    {
        $this->actions[] = array($label, $action);
    }
    
    /**
     * Define the stack order (zIndex)
     * @param $order Stack order
     */
    public function setStackOrder($order)
    {
        $this->stackOrder = $order;
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        $action_code = '';
        if ($this->actions)
        {
            foreach ($this->actions as $action_array)
            {
                $label  = $action_array[0];
                $action = $action_array[1];
                $action_code .= "\"{$label}\": function() {  $action },";
            }
        }
        
        $ok_button = '';
        if ($this->useOKButton)
        {
            $ok_button = '  OK: function() {
                				$( this ).remove();
                			}';
        }
        
        $left = $this->left ? $this->left : 0;
        $top  = $this->top  ? $this->top  : 0;
        
       
        $pos_string = '';
        $id = $this->{'id'};
        
        if ($this->useCloseButton == FALSE) //Para Implementar Tá Bugando A Janela
        {
            //
        }
        parent::add(TScript::create("tjquerydialog_start( '#{$id}', {$this->modal}, {$this->draggable}, {$this->resizable}, {$this->width}, {$this->height}, {$top}, {$left}, {$this->stackOrder}, { {$action_code} {$ok_button} } ); ", FALSE));
        parent::show();
    }
    
    /**
     * Closes the dialog
     */
    public function close()
    {
        $script = new TElement('script');
        $script->{'type'} = 'text/javascript';
        $script->add( '$( "#' . $this->{'id'} . '" ).remove();');
        parent::add($script);
    }
    
     /**
     * When Closes the dialog
     */
    public function closeAction($class, $method = NULL, $parameters = NULL, $callback = NULL, $type='load')
    {
        $query = self::buildHttpQuery($class, $method, $parameters);
        ($type=='goto')?($page = "__adianti_goto_page('{$query}');"):($page = "__adianti_load_page('{$query}');");
        $script = new TElement('script');
        $script->{'type'} = 'text/javascript';
        $script->add( '$( "#' . $this->{'id'} . '" ).on( "dialogclose", function( event, ui ) {'.$page.'} );');
        parent::add($script);
    }
    
    public function closeBack($level=1, $type='load', $extra_param=array())
    {
        $level = abs((int) $level);                
        $referers = $_SESSION[APPLICATION_NAME]['referers'];   
        $idx = (sizeof($referers) - 1) - $level;
        $REFERER_CLASS = trim($referers[$idx]['class']);
        $REFERER_METHOD = trim($referers[$idx]['method']);
        $PARAM = array_merge($referers[$idx]['param'],$extra_param);
        $query = self::buildHttpQuery($REFERER_CLASS, $REFERER_METHOD, $PARAM);
        
        ($type=='goto')?($page = "__adianti_goto_page('{$query}');"):($page = "__adianti_load_page('{$query}');");
        $script = new TElement('script');
        $script->{'type'} = 'text/javascript';
        $script->add( '$( "#' . $this->{'id'} . '" ).on( "dialogclose", function( event, ui ) {'.$page.'} );');
        parent::add($script);
        
    }
    
    /**
     * Build HTTP Query
     *
     * @param $class class name
     * @param $method method name
     * @param $parameters array of parameters
     */
    public static function buildHttpQuery($class, $method = NULL, $parameters = NULL)
    {
        $url = array();
        $url['class']  = $class;
        $url['method'] = $method;
        unset($parameters['class']);
        unset($parameters['method']);
        $query = http_build_query($url);
        $callback = self::$router;
        $short_url = null;
        
        if ($callback)
        {
            $query  = $callback($query, TRUE);
        }
        else
        {
            $query = 'index.php?'.$query;
        }
        
        if (strpos($query, '?') !== FALSE)
        {
            return $query . ( count($parameters)>0 ? '&'.http_build_query($parameters) : '' );
        }
        else
        {
            return $query . ( count($parameters)>0 ? '?'.http_build_query($parameters) : '' );
        }
    }
    
    /**
     * Set router callback
     */
    public static function setRouter(Callable $callback)
    {
        self::$router = $callback;
    }
    
    /**
     * Get router callback
     */
    public static function getRouter()
    {
        return self::$router;
    }
    
    
    
    /**
     * Close all TJQueryDialog
     */
    public static function closeAll()
    {
        if (!isset($_REQUEST['ajax_lookup']) OR $_REQUEST['ajax_lookup'] !== '1')
        {
            // it has to be inline (not external function call)
            TScript::create( ' $(\'[widget="TWindow"]\').remove(); ' );
        }
    }
}
