<?php

class HWindow extends HPage
{
    private $wrapper;
    
    public function __construct()
    {
        parent::__construct();
        $this->wrapper = new HJQueryDialog;
        $this->wrapper->setUseOKButton(FALSE);
        $this->wrapper->setTitle('');
        $this->wrapper->setCloseText('Fechar');
        $this->wrapper->setSize(1000, 500);
        $this->wrapper->setModal(TRUE);
        $this->wrapper->{'widget'} = 'T'.'Window';
        
        parent::add($this->wrapper);
    }
    
    /**
     * Create a window
     */
    public static function create($title, $width, $height, $params = null)
    {
        $inst = new static($params);
        $inst->setIsWrapped(TRUE);
        $inst->setTitle($title);
        $inst->setSize($width, $height);
        unset($inst->wrapper->{'widget'});
        return $inst;
    }
    
    /**
     * Define the stack order (zIndex)
     * @param $order Stack order
     */
    public function setStackOrder($order)
    {
        $this->wrapper->setStackOrder($order);
    }
    
    /**
     * Define the window's title
     * @param  $title Window's title
     */
    public function setTitle($title)
    {
        $this->wrapper->setTitle($title);
    }
    
    /**
     * Define if will use CLOSE Button
     * @param $bool boolean
     */
    public function setUseCloseButton($bool)
    {
        $this->wrapper->setUseCloseButton($bool);
    }
    
    public function setCloseText($text)
    {
        $this->wrapper->setCloseText($text);   
    }
    
    /**
     * Turn on/off modal
     * @param $modal Boolean
     */
    public function setModal($modal)
    {
        $this->wrapper->setModal($modal);
    }
    
    /**
     * Define the window's size
     * @param  $width  Window's width
     * @param  $height Window's height
     */
    public function setSize($width, $height)
    {
        $this->wrapper->setSize($width, $height);
    }
    
    /**
     * Define the top corner positions
     * @param $x left coordinate
     * @param $y top  coordinate
     */
    public function setPosition($x, $y)
    {
        $this->wrapper->setPosition($x, $y);
    }
    
    /**
     * Define the Property value
     * @param $property Property name
     * @param $value Property value
     */
    public function setProperty($property, $value)
    {
        $this->wrapper->$property = $value;
    }
    
    /**
     * Add some content to the window
     * @param $content Any object that implements the show() method
     */
    public function add($content)
    {
        $this->wrapper->add($content);
    }
    
    public function closeAction($class, $method = NULL, $parameters = NULL, $callback = NULL, $type='load')
    {
        $this->wrapper->closeAction($class, $method = NULL, $parameters = NULL, $callback = NULL, $type);    
    }
    
    public function closeBack($level=1, $type='load', $extra_param=array())
    {
        $this->wrapper->closeBack($level, $type, $extra_param);    
    }
    
    /**
     * Close HJQueryDialog's
     */
    public static function closeWindow()
    {
        HJQueryDialog::closeAll();
    }
    

}
