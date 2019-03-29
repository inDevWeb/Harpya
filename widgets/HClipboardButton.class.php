<?php


class HClipboardButton extends TField implements AdiantiWidgetInterface
{
    private $clipboardData;
    private $clipboardAction;
    private $clipboardTarget;
    private $image = 'fa:clipboard blue';
    private $properties;
    protected $label = 'Copy To Clipboard';
    protected $formName;
    
    /**
     * Class Constructor
     */
    public function __construct($name)
    {
        
    }
    
     /**
     * Create a button with icon and action
     */
    public static function create($name, $label, $image)
    {
        $button = new HClipboardButton( $name );
        $button->setLabel( $label );
        $button->setImage( $image );
        return $button;
    }
    
    /**
     * Add CSS class
     */
    public function addStyleClass($class)
    {
        $this->{'class'} = 'btn btn-default '. $class;
    }
    
    
    /**
     * Define the icon of the button
     * @param  $image  image path
     */
    public function setImage($image)
    {
        $this->image = $image;
    }
    
    /**
     * Define the label of the button
     * @param  $label button label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }
    
    /**
     * Returns the button label
     */
    public function getLabel()
    {
        return $this->label;
    }
    
    /**
     * Add a JavaScript function to be executed by the button
     * @param $function A piece of JavaScript code
     * @ignore-autocomplete on
     */
    public function addFunction($function)
    {
        if ($function)
        {
            $this->functions = $function.';';
        }
    }
    
    /**
     * Define a field property
     * @param $name  Property Name
     * @param $value Property Value
     */
    public function setProperty($name, $value, $replace = TRUE)
    {
        $this->properties[$name] = $value;
    }
    
    /**
     * Return field property
     */
    public function getProperty($name)
    {
        return $this->properties[$name];
    }
    
    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " tbutton_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " tbutton_disable_field('{$form_name}', '{$field}'); " );
    }
   
    
    public function setData($data)
    {
        $this->clipboardData = $data;
    }
    
    public function setAction($action='copy')
    {
        $this->clipboardAction = $action; 
        if($action != 'copy' or $action != 'cut')
        {
            throw new Exception('Action Is Invalid Only Cut and Copy Is Allowed!');
            return FALSE;
        }   
    }
    
    public function setTarget($target)
    {
        $this->clipboardTarget = '#'.$target;
    }
    
    public function show()
    {
        TPage::include_js('app/lib/hlib/include/clipboard.min.js');
        $button_id = 'clipbutton_'.$this->name.'_'. mt_rand(1000000000, 1999999999);
        $button = new TElement('button');
        $button->{'id'} = $button_id;
        $button->{'name'} = $button_id;
        $button->{'class'} = 'btn btn-default btn-sm';
        if(!empty($this->clipboardData))
        {
            $button->{'data-clipboard-text'} = $this->clipboardData;
        }
        
        if(!empty($this->clipboardAction))
        {
            $button->{'data-clipboard-action'} = $this->clipboardAction;
        }
        
        if(!empty($this->clipboardTarget))
        {
            $button->{'data-clipboard-target'} = $this->clipboardTarget;
        }
        
        $script =new TElement('script');
        $script->type = 'text/javascript';
        $script->add("var btn = document.getElementById('$button_id');");
        $script->add("var clipboard = new Clipboard(btn);");
        
        
        $span = new TElement('span');
        $span->add($script);
        if ($this->image)
        {
            $image = new TElement('span');
            $image->{'style'} = 'padding-right:4px';
            
            if (substr($this->image,0,3) == 'bs:')
            {
                $image = new TElement('i');
                $image->{'style'} = 'padding-right:4px';
                $image->{'class'} = 'glyphicon glyphicon-'.substr($this->image,3);
            }
            else if (substr($this->image,0,3) == 'fa:')
            {
                $fa_class = substr($this->image,3);
                if (strstr($this->image, '#') !== FALSE)
                {
                    $pieces = explode('#', $fa_class);
                    $fa_class = $pieces[0];
                    $fa_color = $pieces[1];
                }
                $image = new TElement('i');
                $image->{'style'} = 'padding-right:4px';
                $image->{'class'} = 'fa fa-'.$fa_class;
                if (isset($fa_color))
                {
                    $image->{'style'} .= "; color: #{$fa_color}";
                }
            }
            else if (file_exists('app/images/'.$this->image))
            {
                $image = new TImage('app/images/'.$this->image);
                $image->{'style'} = 'padding-right:4px';
            }
            else if (file_exists('lib/adianti/images/'.$this->image))
            {
                $image = new TImage('lib/adianti/images/'.$this->image);
                $image->{'style'} = 'padding-right:4px';
            }
            
            $span->add($image);
        }
        
        if ($this->label)
        {
            $span->add($this->label);
        }
        $button->add($span);
        $button->show();
    }
}
