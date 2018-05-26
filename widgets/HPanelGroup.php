<?php
/**
 * Bootstrap native panel for Adianti Framework  with proposed updates
 *
 * @version    0.5
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio, Rodrigo Moglia
 * @copyright  Copyright (c) 2006 Pablo Dall'Oglio, 2018 Rodrigo Moglia
 * @license    http://www.adianti.com.br/framework-license
 */
class HPanelGroup extends TElement
{
    private $head;
    private $body;
    private $footer;
    private $background;
    
    /**
     * Static creator for panels
     * @param $title Panel title
     * @param $element Panel content
     */
    public static function pack($title, $element, $footer = null)
    {
        $panel = new self($title);
        $panel->add($element);
        
        if ($footer)
        {
            $panel->addFooter($footer);
        }
        
        return $panel;
    }
    
    /**
     * Constructor method
     * @param $title  Panel Title
     * @param $footer Panel Footer
     */
    public function __construct($title = NULL, $background = NULL, $color = NULL)
    {
        parent::__construct('div');
        $this->{'class'} = 'panel panel-default';
        
        $this->head = new TElement('div');
        $this->head->{'class'} = 'panel-heading';
        
        $this->background = $background;
        
        if ($title)
        {
            $panel_title = new TElement('div');
            $panel_title->{'class'} = 'panel-title';
            $panel_title->add( $title );
            
            if (!empty($this->background))
            {
                $this->head->{'style'} = 'background:'.$this->background;
            }
            
            if (!empty($color))
            {
                $this->head->{'style'} .= ";color:$color";
            }
            
            $this->head->add($panel_title);
            parent::add($this->head);
        }
        
        $this->body = new TElement('div');
        $this->body->{'class'} = 'panel-body';
        parent::add($this->body);
        
        $this->footer = new TElement('div');
        $this->footer->{'class'} = 'panel-footer';
    }
    
    /**
     * Add the panel content
     */
    public function add($content)
    {
        $this->body->add($content);
        
        if ($content instanceof BootstrapFormWrapper)
        {
            $buttons = $content->detachActionButtons();
            if ($buttons)
            {
                foreach ($buttons as $button)
                {
                    $this->footer->add( $button );
                }
                parent::add($this->footer);
            }
        }
    }
    
    /**
     * Return panel header
     */
    public function getHeader()
    {
        return $this->head;
    }
    
    /**
     * Return panel body
     */
    public function getBody()
    {
        return $this->body;
    }
    
    /**
     * Return panel footer
     */
    public function getFooter()
    {
        return $this->footer;
    }
    
    /**
     * Add footer
     */
    public function addFooter($footer, $background = NULL)
    {
        if (!empty($this->background))
        {
            $this->footer->{'style'} = 'background:'.$this->background;
        }
        
        if (!empty($background))
        {
            $this->footer->{'style'} = 'background:'.$background;
        }
        $this->footer->add( $footer );
        parent::add($this->footer);
    }
}
