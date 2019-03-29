<?php

class HPage extends TPage {


    public function __construct(){
    parent::__construct();
    
    }
    
    
    public function show(){
    
    if(method_exists($this,'onAfterLoad')){
        $this->onAfterLoad($_REQUEST);
    }
    
    parent::show();
    
        if(method_exists($this,'onBeforeLoad')){
        $this->onBeforeLoad($_REQUEST);
    }
    
    }
    
    
    }