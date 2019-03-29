<?php
/**
 * Implements the TRepository and New Proposed Methods for Pattern to deal with collections of Active Records
 *
 * @version    5.1
 * @package    database
 * @author     Pablo Dall'Oglio
 * @author     Rodrigo Moglia
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @copyright  Copyright (c) 2018 Interatia Sistemas de Informação. (http://www.interatia.com)
 * @license    http://www.adianti.com.br/framework-license
 */
final class HRepository
{
    private $class; // Active Record class to be manipulated
    private $name;
    private $criteria; // buffered criteria to use with fluent interfaces
    private $setValues;
    private $select_fields;
    
    /**
     * Class Constructor
     * @param $class = Active Record class name
     */
    public function __construct($class,$name="")
    {
        (!empty($name))?($this->name = '_'.$name):($this->name="");
        
        if (class_exists($class))
        {
            if (is_subclass_of($class, 'TRecord'))
            {
                $this->class = $class;
                $this->criteria = new TCriteria;
            }
            else
            {
                throw new Exception(AdiantiCoreTranslator::translate('The class ^1 was not accepted as argument. The class informed as parameter must be subclass of ^2.', $class, 'TRecord'));
            }
        }
        else
        {
            throw new Exception(AdiantiCoreTranslator::translate('The class ^1 was not found. Check the class name or the file name. They must match', '"' . $class . '"'));
        }
    }
    
    /**
     * Returns the name of database entity
     * @return A String containing the name of the entity
     */
    protected function getEntity()
    {
        return constant($this->class.'::TABLENAME');
    }
    
    /**
     * Add a run time criteria using fluent interfaces
     * 
     * @param  $variable = variable
     * @param  $operator = comparison operator (>,<,=)
     * @param  $value    = value to be compared
     * @param  $logicOperator = logical operator (TExpression::AND_OPERATOR, TExpression::OR_OPERATOR)
     * @return A TRepository object
     */
    public function where($variable, $operator, $value, $logicOperator = TExpression::AND_OPERATOR)
    {
        $this->criteria->add(new TFilter($variable, $operator, $value), $logicOperator);
        
        return $this;
    }
    
    /**
     * Assign values to the database columns
     * 
     * @param  $column = column name
     * @param  $value  = column value
     * @return A TRepository object
     */
    public function set($column, $value)
    {
        if (is_scalar($value) OR is_null($value))
        {
            $this->setValues[$column] = $value;
        }
        
        return $this;
    }
    
    /**
     * Add a run time OR criteria using fluent interfaces
     * 
     * @param  $variable = variable
     * @param  $operator = comparison operator (>,<,=)
     * @param  $value    = value to be compared
     * @return A TRepository object
     */
    public function orWhere($variable, $operator, $value)
    {
        $this->criteria->add(new TFilter($variable, $operator, $value), TExpression::OR_OPERATOR);
        
        return $this;
    }
    
    /**
     * Define the ordering for criteria using fluent interfaces
     * 
     * @param  $order = Order column
     * @param  $direction = Order direction (asc, desc)
     * @return A TRepository object
     */
    public function orderBy($order, $direction = 'asc')
    {
        $this->criteria->setProperty('order', $order);
        $this->criteria->setProperty('direction', $direction);
        
        return $this;
    }
    
    /**
     * Define the LIMIT criteria using fluent interfaces
     * 
     * @param  $limit = Limit
     * @return A TRepository object
     */
    public function take($limit)
    {
        $this->criteria->setProperty('limit', $limit);
        
        return $this;
    }
    
    /**
     * Define the OFFSET criteria using fluent interfaces
     * 
     * @param  $offset = Offset
     * @return A TRepository object
     */
    public function skip($offset)
    {
        $this->criteria->setProperty('offset', $offset);
        
        return $this;
    }
    
    /**
     * Load a collection       of objects from database using a criteria
     * @param $criteria        An TCriteria object, specifiyng the filters
     * @param $callObjectLoad  If load() method from Active Records must be called to load object parts
     * @return                 An array containing the Active Records
     */
    public function load(TCriteria $criteria = NULL, $callObjectLoad = TRUE)
    {
        if (!$criteria)
        {
            $criteria = isset($this->criteria) ? $this->criteria : new TCriteria;
        }
        // creates a SELECT statement
        $sql = new TSqlSelect;
        $sql->addColumn('*');
        $sql->setEntity($this->getEntity());
        // assign the criteria to the SELECT statement
        $sql->setCriteria($criteria);
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
            $dbinfo = TTransaction::getDatabaseInfo(); // get dbinfo
            if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
            {
                $result = $conn-> prepare ( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $result-> execute ( $criteria->getPreparedVars() );
            }
            else
            {
                // execute the query
                $result= $conn-> query($sql->getInstruction());
            }
            $results = array();
            
            $class = $this->class;
            $callback = array($class, 'load'); // bypass compiler
            
            // Discover if load() is overloaded
            $rm = new ReflectionMethod($class, $callback[1]);
            
            if ($result)
            {
                // iterate the results as objects
                while ($raw = $result-> fetchObject())
                {
                    $object = new $this->class;
                    if (method_exists($object, 'onAfterLoadCollection'))
                    {
                        $object->onAfterLoadCollection($raw);
                    }
                    $object->fromArray( (array) $raw);
                    
                    if ($callObjectLoad)
                    {
                        // reload the object because its load() method may be overloaded
                        if ($rm->getDeclaringClass()-> getName () !== 'Adianti\Database\TRecord')
                        {
                            $object->reload();
                        }
                    }
                    
                    if ( $cache = $object->getCacheControl() )
                    {
                        $pk = $object->getPrimaryKey();
                        $record_key = $class . '['. $object->$pk . ']';
                        if ($cache::setValue( $record_key, $object->toArray() ))
                        {
                            TTransaction::log($record_key . ' stored in cache');
                        }
                    }
                    // store the object in the $results array
                    $results[] = $object;
                }
            }
            return $results;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * Return a indexed array
     */
    public function getIndexedArray($indexColumn, $valueColumn, $criteria = NULL)
    {
        $criteria = (empty($criteria)) ? $this->criteria : $criteria;
        $objects = $this->load($criteria, FALSE);
        
        $indexedArray = array();
        if ($objects)
        {
            foreach ($objects as $object)
            {
                if (isset($object->$valueColumn))
                {
                    $indexedArray[ $object->$indexColumn ] = $object->$valueColumn;
                }
                else
                {
                    $indexedArray[ $object->$indexColumn ] = $object->render($valueColumn);
                }
            }
        }
        
        if (empty($criteria) or ( $criteria instanceof TCriteria and empty($criteria->getProperty('order')) ))
        {
            asort($indexedArray);
        }
        return $indexedArray;
    }
    
    /**
     * Update values in the repository
     */
    public function update($setValues = NULL, TCriteria $criteria = NULL)
    {
        if (!$criteria)
        {
            $criteria = isset($this->criteria) ? $this->criteria : new TCriteria;
        }
        $setValues = isset($setValues) ? $setValues : $this->setValues;
        
        $class = $this->class;
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            $dbinfo = TTransaction::getDatabaseInfo(); // get dbinfo
            
            // creates a UPDATE statement
            $sql = new TSqlUpdate;
            if ($setValues)
            {
                foreach ($setValues as $column => $value)
                {
                    $sql->setRowData($column, $value);
                }
            }
            $sql->setEntity($this->getEntity());
            // assign the criteria to the UPDATE statement
            $sql->setCriteria($criteria);
            
            if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
            {
                $statement = $conn-> prepare ( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $result = $statement-> execute ( $sql->getPreparedVars() );
            }
            else
            {
                // execute the UPDATE statement
                $result = $conn->exec($sql->getInstruction());
            }
            
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
            
            // update cache
            $record = new $class;
            if ( $cache = $record->getCacheControl() )
            {
                $pk = $record->getPrimaryKey();
                
                // creates a SELECT statement
                $sql = new TSqlSelect;
                $sql->addColumn('*');
                $sql->setEntity($this->getEntity());
                // assign the criteria to the SELECT statement
                $sql->setCriteria($criteria);
                
                if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
                {
                    $subresult = $conn-> prepare ( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $subresult-> execute ( $criteria->getPreparedVars() );
                }
                else
                {
                    $subresult = $conn-> query($sql->getInstruction());
                }
                
                if ($subresult)
                {
                    // iterate the results as objects
                    while ($raw = $subresult-> fetchObject())
                    {
                        $object = new $this->class;
                        $object->fromArray( (array) $raw);
                    
                        $record_key = $class . '['. $raw->$pk . ']';
                        if ($cache::setValue( $record_key, $object->toArray() ))
                        {
                            TTransaction::log($record_key . ' stored in cache');
                        }
                    }
                }
            }
            
            return $result;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * Delete a collection of Active Records from database
     * @param $criteria  An TCriteria object, specifiyng the filters
     * @return           The affected rows
     */
    public function delete(TCriteria $criteria = NULL, $callObjectLoad = FALSE)
    {
        if (!$criteria)
        {
            $criteria = isset($this->criteria) ? $this->criteria : new TCriteria;
        }
        $class = $this->class;
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            $dbinfo = TTransaction::getDatabaseInfo(); // get dbinfo
            
            // first, clear cache
            $record = new $class;
            if ( ($cache = $record->getCacheControl()) OR $callObjectLoad )
            {
                $pk = $record->getPrimaryKey();
                
                // creates a SELECT statement
                $sql = new TSqlSelect;
                $sql->addColumn( $pk );
                $sql->setEntity($this->getEntity());
                // assign the criteria to the SELECT statement
                $sql->setCriteria($criteria);
                
                if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
                {
                    $result = $conn-> prepare ( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $result-> execute ( $criteria->getPreparedVars() );
                }
                else
                {
                    $result = $conn-> query($sql->getInstruction());
                }
                
                if ($result)
                {
                    // iterate the results as objects
                    while ($row = $result-> fetchObject())
                    {
                        if ($cache)
                        {
                            $record_key = $class . '['. $row->$pk . ']';
                            if ($cache::delValue( $record_key ))
                            {
                                TTransaction::log($record_key . ' deleted from cache');
                            }
                        }
                        
                        if ($callObjectLoad)
                        {
                            $object = new $this->class;
                            $object->fromArray( (array) $row);
                            $object->delete();
                        }
                    }
                }
            }
            
            // creates a DELETE statement
            $sql = new TSqlDelete;
            $sql->setEntity($this->getEntity());
            // assign the criteria to the DELETE statement
            $sql->setCriteria($criteria);
            
            if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
            {
                $result = $conn-> prepare ( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $result-> execute ( $criteria->getPreparedVars() );
            }
            else
            {
                // execute the DELETE statement
                $result = $conn->exec($sql->getInstruction());
            }
            
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
            
            return $result;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    public function select($expression,$function="",$alias="")
    {
        (is_array($expression))?($expression = implode(",", $expression)):('');
        $functions = array("avg", "max", "min", "sum","stddev","variance","count");
        (in_array($function, $functions))?($field = "$function($expression)"):($field = "$expression");
        (!empty($alias))?($this->select_fields[] = "$field as $alias"):($this->select_fields[] = $field);
    }
    
    public function aggregate(TCriteria $criteria = NULL)
    {
        if (!$criteria)
        {
            $criteria = isset($this->criteria) ? $this->criteria : new TCriteria;
        }
        $select_fields = implode(",", $this->select_fields);
        (empty($select_fields))?($select_fields='count(*)'):('');

        // creates a SELECT statement
        $sql = new TSqlSelect;
        //print_r($select_fields);
        $sql->addColumn($select_fields);
        $sql->setEntity($this->getEntity());
        // assign the criteria to the SELECT statement
        $sql->setCriteria($criteria);
       
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
           
            $dbinfo = TTransaction::getDatabaseInfo(); // get dbinfo
            if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
            {
                $result = $conn-> prepare ( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $result-> execute ( $criteria->getPreparedVars() );
            }
            else
            {
                // executes the SELECT statement
                $result= $conn->query($sql->getInstruction());
            }
           
            $row=FALSE;
            if ($result)
            {
                $row = $result->fetch();
            }
            // returns the result
            return $row;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * Return the amount of objects that satisfy a given criteria
     * @param $expression a attribute name
     * @param $criteria  An TCriteria object, specifiyng the filters
     * @return           An Integer containing the amount of objects that satisfy the criteria
     */
     
    public function count(TCriteria $criteria = NULL,$expression='*')
    {
        unset($this->select_fields);
        $this->select($expression,"count");
        $return = $this->aggregate($criteria);
        return $return["count($expression)"];
    }
    
     /**
     * Return the amount of objects that satisfy a given criteria once time at database
     * @param $expression a attribute name
     * @param $criteria  An TCriteria object, specifiyng the filters
     * @param $resetProperties Reset TCriteria object
     * @return An Integer containing the amount of objects that satisfy the criteria
     */
    
    public function countOnce(TCriteria $criteria = NULL,$resetProperties=TRUE,$expression=FALSE)
    {
        (!empty($resetProperties))?($criteria->resetProperties()):('');
        if(isset($_SESSION[APPLICATION_NAME][$this->class.$this->name.'_count']))
        {
            $count = $_SESSION[APPLICATION_NAME][$this->class.$this->name.'_count'];    
        }
        else
        {
            (empty($expression))?($expression = constant($this->class.'::PRIMARYKEY')):('');
            $count = $this->count($expression, $criteria);
        }
        
        return $count;
    }
    
    /**
     * Return the sum of attribute on objects that satisfy a given criteria
     * @param $criteria  An TCriteria object, specifiyng the filters
     * @param  $expression a attribute name
     * @return           An Float containing the amount of objects that satisfy the criteria
    */
    
    public function sum($expression, TCriteria $criteria = NULL)
    {
        unset($this->select_fields);
        $this->select($expression,"sum");
        $return = $this->aggregate($criteria);
        return $return["sum($expression)"];
    }
    
    /**
     * Return the min of attribute on objects that satisfy a given criteria
     * @param  $criteria  An TCriteria object, specifiyng the filters
     * @param  $expression a attribute name
     * @return An Float containing the amount of objects that satisfy the criteria
    */
    
    public function min($expression, TCriteria $criteria = NULL)
    {
        unset($this->select_fields);
        $this->select($expression,"min");
        $return = $this->aggregate($criteria);
        return $return["min($expression)"];
    }
    
    /**
     * Return the max of attribute on objects that satisfy a given criteria
     * @param  $criteria  An TCriteria object, specifiyng the filters
     * @param  $expression a attribute name
     * @return An Float containing the amount of objects that satisfy the criteria
    */

    
    public function max($expression, TCriteria $criteria = NULL)
    {
        unset($this->select_fields);
        $this->select($expression,"max");
        $return = $this->aggregate($criteria);
        return $return["max($expression)"];
    }
    
    /**
     * Return the avg of attribute on objects that satisfy a given criteria
     * @param  $criteria  An TCriteria object, specifiyng the filters
     * @param  $expression a attribute name
     * @return An Float containing the amount of objects that satisfy the criteria
    */

    
    public function avg($expression, TCriteria $criteria = NULL)
    {
        unset($this->select_fields);
        $this->select($expression,"avg");
        $return = $this->aggregate($criteria);
        return $return["avg($expression)"];
    }
    
    public function get(TCriteria $criteria = NULL, $callObjectLoad = TRUE)
    {
        return $this->load($criteria, $callObjectLoad);
    }
}
