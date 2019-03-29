<?php



class HCriteria extends TCriteria {

    /**
     * Return the amount of objects that satisfy a given criteria once time at database
     * @param $expression a attribute name
     * @param $criteria  An TCriteria object, specifiyng the filters
     * @param $resetProperties Reset TCriteria object
     * @return An Integer containing the amount of objects that satisfy the criteria
     */
    
    public function countOnce($expression='*', TCriteria $criteria = NULL,$resetProperties=TRUE)
    {
        (!empty($resetProperties))?($criteria->resetProperties()):('');
        if(isset($_SESSION[APPLICATION_NAME][$this->class.$this->name.'_count']))
        {
            $count = $_SESSION[APPLICATION_NAME][$this->class.$this->name.'_count'];    
        }
        else
        {
            ($expression == '*')?($expression = constant($this->class.'::PRIMARYKEY')):('');
            $count = $this->count($expression, $criteria);
        }
        
        return $count;
    }
    
}


