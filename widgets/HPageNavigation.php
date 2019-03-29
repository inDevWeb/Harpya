<?php
/**
 * Page Navigation provides navigation for a datagrid with proposed updates
 *
 * @version    5.1
 * @package    widget
 * @subpackage datagrid
 * @author     Pablo Dall'Oglio
 * @author     Rodrigo Moglia
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @copyright  Copyright (c) 2018 Interatia Sistemas de Informação. (http://www.interatia.com)
 * @license    http://www.adianti.com.br/framework-license
 */
class HPageNavigation
{
    private $limit;
    private $count;
    private $order;
    private $page;
    private $first_page;
    private $action;
    private $width;
    private $direction;
    private $hidden;
    private $name;
    
    public function __construct($name = '')
    {
        $this->hidden = false;
        (!empty($name))?($this->name = $name.'_'):($this->name='');
    }
    
    /**
     * Hide
     */
    public function hide()
    {
        $this->hidden = true;
    }
    
    /**
     * Set the Amount of displayed records
     * @param $limit An integer
     */
    public function setLimit($limit)
    {
        $this->limit  = (int) $limit;
    }
    
    /**
     * Returns the limit of records
     */
    public function getLimit()
    {
        return $this->limit;
    }
    
    /**
     * Define the PageNavigation's width
     * @param $width PageNavigation's width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }
    
    /**
     * Define the total count of records
     * @param $count An integer (the total count of records)
     */
    public function setCount($count)
    {
        $this->count = (int) $count;
    }
    
    /**
     * Return the total count of records
     */
    public function getCount()
    {
        return $this->count;
    }
    
    /**
     * Define the current page
     * @param $page An integer (the current page)
     */
    public function setPage($page)
    {
        $this->page = (int) $page;
    }
    
    /**
     * Returns the current page
     */
    public function getPage()
    {
        return $this->page;
    }
    
    /**
     * Define the first page
     * @param $page An integer (the first page)
     */
    public function setFirstPage($first_page)
    {
        $this->first_page = (int) $first_page;
    }
    
    /**
     * Define the ordering
     * @param $order A string containint the column name
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }
    
    /**
     * Define the ordering
     * @param $direction asc, desc
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }
        
    /**
     * Set the page navigation properties
     * @param $properties array of properties
     */
    public function setProperties($properties)
    {
        $order      = isset($properties[$this->name.'order'])  ? addslashes($properties[$this->name.'order'])  : '';
        $page       = isset($properties[$this->name.'page'])   ? $properties[$this->name.'page']   : 1;
        $direction  = (isset($properties[$this->name.'direction']) AND in_array($properties[$this->name.'direction'], array('asc', 'desc')))  ? $properties[$this->name.'direction']   : NULL;
        $first_page = isset($properties[$this->name.'first_page']) ? $properties[$this->name.'first_page']: 1;
        
        $this->setOrder($order);
        $this->setPage($page);
        $this->setDirection($direction);
        $this->setFirstPage($first_page);
    }
    
    /**
     * Define the PageNavigation action
     * @param $action TAction object (fired when the user navigates)
     */
    public function setAction($action)
    {
        $this->action = $action;
    }
    
    /**
     * Show the PageNavigation widget
     */
    public function show()
    {
        if ($this->hidden)
        {
            return;
        }
        
        if (!$this->action instanceof TAction)
        {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before add this component', __CLASS__ . '::' . 'setAction()'));
        }
        
        $first_page  = isset($this->first_page) ? $this->first_page : 1;
        $direction   = 'asc';
        $page_size = isset($this->limit) ? $this->limit : 10;
        $max = 10;
        $registros = $this->count;
        
        if (!$registros)
        {
            $registros = 0;
        }
        
        if ($page_size > 0)
        {
            $pages = (int) ($registros / $page_size) - $first_page +1;
        }
        else
        {
            $pages = 1;
        }
        
        if ($page_size>0)
        {
            $resto = $registros % $page_size;
        }
        
        $pages += $resto>0 ? 1 : 0;
        $last_page = min($pages, $max);
        
        $nav = new TElement('nav');
        $nav-> align = 'center';
        
        $ul = new TElement('ul');
        $ul->{'class'} = 'pagination';
          
        $nav->add($ul);
        
        // First
        $parameters = $this->action->getParameters();
        $parameters[$this->name.'offset']=0;
        $parameters[$this->name.'limit']=$page_size;
        $parameters[$this->name.'direction']=$this->direction;
        $parameters[$this->name.'page']=1;
        $parameters[$this->name.'first_page']=1;
        $parameters[$this->name.'order']=$this->order;
        (!empty($this->name))?($parameters = array_merge($_GET,$parameters)) : NULL;
        $this->action->setParameters($parameters);
        
        $item = new TElement('li');
        $item->{'class'} = 'page-item';
        $link = new TElement('a');
        $link->{'class'} = 'page-link glyphicon glyphicon-fast-backward';
        $link->{'alt'} = 'First';
        $link->{'aria-label'} = '';
        //$link->{'popover'} = 'true';
        //$link->{'popcontent'} = "<b>$registros</b> registros em <b>$pages</b> páginas<br><b>$page_size</b> registros por página<br>";
        $link-> href      = $this->action->serialize();
        $link-> generator = 'adianti';
        
        $ul->add($item);
        $item->add($link);
        
        // previous
        $item = new TElement('li');
        $item->{'class'} = 'page-item';
        $link = new TElement('a');
        $link->{'class'} = 'page-link glyphicon glyphicon-step-backward';
        $link->{'href'} = '#';
        $link->{'alt'} = 'Previous';
        $link->{'aria-label'} = '';
        
        $ul->add($item);
        $item->add($link);
  
        
        if ($first_page > 1)
        {
            $parameters = $this->action->getParameters();
            $parameters[$this->name.'offset']=($first_page - $max -1) * $page_size;
            $parameters[$this->name.'limit']=$page_size;
            $parameters[$this->name.'direction']=$this->direction;
            $parameters[$this->name.'page']=$first_page - $max;
            $parameters[$this->name.'first_page']=$first_page - $max;
            $parameters[$this->name.'order']=$this->order;
            
            (!empty($this->name))?($parameters = array_merge($_GET,$parameters)) : NULL;
            
            $this->action->setParameters($parameters);
            
            //$link->{'popover'} = 'true';
            //$link->{'popcontent'} = "<b>$registros</b> registros em <b>$pages</b> páginas<br><b>$page_size</b> registros por página<br>";
            
            $link-> href      = $this->action->serialize();
            $link-> generator = 'adianti';
            $link->add('');
        }
        else
        {
            $item->{'class'} = 'page-item disabled';
            $link->add(''); //Previous
            $link->{'tabindex'} = '-1';
        }
        
        for ($n = $first_page; $n <= $last_page + $first_page -1; $n++)
        {
            $offset = ($n -1) * $page_size;
            $item = new TElement('li');
            $item->{'class'} = 'page-item';
            $link = new TElement('a');

           
            $parameters = $this->action->getParameters();
            $parameters[$this->name.'offset']=$offset;
            $parameters[$this->name.'limit']=$page_size;
            $parameters[$this->name.'direction']=$this->direction;
            $parameters[$this->name.'page']=$n;
            $parameters[$this->name.'first_page']=$first_page;
            $parameters[$this->name.'order']=$this->order;
            
            (!empty($this->name))?($parameters = array_merge($_GET,$parameters)) : NULL;
            
            $this->action->setParameters($parameters);
            
            $link-> href      = $this->action->serialize();
            $link-> generator = 'adianti';
            
            $ul->add($item);
            $item->add($link);
            $link->add($n);
 
            
            if($this->page == $n)
            {
                $item->{'class'} = 'active';
            }
        }
        
        for ($z=$n; $z<=10; $z++)
        {
            $item = new TElement('li');
            $item->{'class'} = 'page-item disabled';
            $link = new TElement('a');
            $link->{'class'} = 'page-link';
     
            $ul->add($item);
            $item->add($link);
            $link->add($z);
        }
        
        $item = new TElement('li');
        $item->{'class'} = 'page-item';
        $link = new TElement('a');
        $link->{'class'} = 'page-link glyphicon glyphicon-step-forward';
        $link->{'aria-label'} = "";
        $link->{'alt'} = 'Next';
        $ul->add($item);
        $item->add($link);
        
        if ($pages > $max)
        {
            $offset = ($n -1) * $page_size;
            $first_page = $n;
            
            $parameters = $this->action->getParameters();
            $parameters[$this->name.'offset']=$offset;
            $parameters[$this->name.'limit']=$page_size;
            $parameters[$this->name.'direction']=$this->direction;
            $parameters[$this->name.'page']=$n;
            $parameters[$this->name.'first_page']=$first_page;
            $parameters[$this->name.'order']=$this->order;
            
            (!empty($this->name))?($parameters = array_merge($_GET,$parameters)) : NULL;
            
            $this->action->setParameters($parameters);
            
            //$link->{'popover'} = 'true';
            //$link->{'popcontent'} = "<b>$registros</b> registros em <b>$pages</b> páginas<br><b>$page_size</b> registros por página<br>";
            
            
            $link-> href      = $this->action->serialize();
            $link-> generator = 'adianti';
            
            $link->add('');
        }
        else
        {
           $item->{'class'} = 'page-item disabled';
            $link->add(''); //Next
            $link->{'tabindex'} = '-1';
        }
        
        // Last
        $parameters = $this->action->getParameters();
        $parameters[$this->name.'offset']=floor($registros / $pages) * $pages;
        $parameters[$this->name.'limit']=$page_size;
        $parameters[$this->name.'direction']=$this->direction;
        $parameters[$this->name.'page']=$pages;
        $parameters[$this->name.'first_page']=1;
        $parameters[$this->name.'order']=$this->order;
        (!empty($this->name))?($parameters = array_merge($_GET,$parameters)) : NULL;
        $this->action->setParameters($parameters);
        
        $item = new TElement('li');
        $item->{'class'} = 'page-item';
        $link = new TElement('a');
        $link->{'class'} = 'page-link glyphicon glyphicon-fast-forward';
        $link->{'alt'} = 'First';
        $link->{'aria-label'} = '';
        //$link->{'popover'} = 'true';
        //$link->{'popcontent'} = "<b>$registros</b> registros em <b>$pages</b> páginas<br><b>$page_size</b> registros por página<br>";
        $link-> href      = $this->action->serialize();
        $link-> generator = 'adianti';
        
        $ul->add($item);
        $item->add($link);
        
        $item = new TElement('li');
        $item->{'class'} = 'page-item';
        $link = new TElement('a');
        $link->{'class'} = 'page-link glyphicon glyphicon-exclamation-sign';
        $link->{'alt'} = 'First';
        $link->{'aria-label'} = '';
        $link->{'popover'} = 'true';
        $link->{'popcontent'} = "<b>$registros</b> registros em <b>$pages</b> páginas<br><b>$page_size</b> registros por página<br>";
        //$link-> href      = $this->action->serialize();
        $link-> generator = 'adianti';
        
        $ul->add($item);
        $item->add($link);

        
        //Hdebug::debug($parameters);
        $nav->show();
    }
    
    public function prepareParameters($param = array())
    {
        $return_param = array();
        foreach ($param as $key => $value)
        {
            if(strstr($key, $this->name))
            {
                $key2 = str_replace($this->name,'',$key);
                $return_param[$key]=$value;
                $return_param[$key2]=$value;    
            }
        }
        return $return_param;
    }
}
