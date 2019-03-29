<?php


class HTMenuItem extends TElement
{
    private $label;
    private $action;
    private $image;
    private $menu;
    private $level;
    private $link;
    private $linkClass;
    
    /**
     * Class constructor
     * @param $label  The menu label
     * @param $action The menu action
     * @param $image  The menu image
     */
    public function __construct($label, $action, $image = NULL, $class_item = 'nav-item',$class_link = 'nav-link')
    {
        parent::__construct('li');
        $this->label = $label;
        $this->action    = $action;
        $this->{'class'} = $class_item;
        $this->link      = new TElement('a');
        $this->link->{'class'} = $class_link;
        $this->linkClass = 'dropdown-toggle';
        
        if ($image)
        {
            $this->image = $image;
        }
    }
    
    /**
     * Set link class
     */
    public function setLinkClass($class)
    {
        $this->linkClass = $class;
    }
    
    /**
     * Define the submenu for the item
     * @param $menu A TMenu object
     */
    public function setMenu(HTMenu $menu)
    {
        $this->{'class'} = 'dropdown-submenu';
        $this->menu = $menu;
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        if ($this->action)
        {
            //$url['class'] = $this->action;
            //$url_str = http_build_query($url);
            $action = str_replace('#', '&', $this->action);
            if ((substr($action,0,7) == 'http://') or (substr($action,0,8) == 'https://'))
            {
                $this->link-> href = $action;
                $this->link-> target = '_blank';
            }
            else
            {
                if ($router = AdiantiCoreApplication::getRouter())
                {
                    $this->link-> href = $router("class={$action}", true);
                }
                else
                {
                    $this->link-> href = "index.php?class={$action}";
                }
                $this->link-> generator = 'adianti';
               
            }
        }
        else
        {
            $this->link-> href = '#';
        }
        
        if (isset($this->image))
        {
            $image = new TImage($this->image);
            $this->link->add($image);
        }
        
      
        if (substr($this->label, 0, 3) == '_t{')
        {
            $this->link->add(_t(substr($this->label,3,-1)));
        }
        else
        {
            $this->link->add($this->label);
         
        }
        
        if (!empty($this->label))
        {
         
            $this->add($this->link);
        }
        
       
          
        if ($this->menu instanceof HTMenu)
        {
            
            if ($this->linkClass == 'dropdown-toggle')
            {
                $this->link->{'data-toggle'} = "dropdown";
                $this->link->{'class'} = 'dropdown-toggle nav-link dropdown-toggle';
            }

            parent::add($this->menu);
        }
        
        parent::show();
    }
}
