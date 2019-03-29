<?php

class HDataTable extends TElement {

private $connection;
private $model;
private $time;
private $rows = [];
private $columnsHeader = [];
    function __construct($connection, $model,$time = 3000){

        parent::__construct('table');

        $this->connection = $connection;
        $this->model = $model;
        $this->time = $time;
        
        $this->{'class'} = 'table-scroll';
        $this->{'id'}    = 'tdatagrid_' . mt_rand(1000000000, 1999999999);
    }

    public function addColumnsHeader($names = array()){
                $this->columnsHeader = $names;
    }
    public function addRows($rows = array()){
        $this->rows = $rows;
    }


    public function show(){

        parent::show();
    }
}