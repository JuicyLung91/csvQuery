<?php

namespace CSV;

/**
 * helper Class for creating a where clause
 */
class WhereClauseStatment {

    /**
     * name of a column
     */
     public $column;

    /**
     * value to check by operator inside one column
     */
    public $value;

    /**
     * operator for the statement
     */
    public $operator;

    /**
     * define the allowed operators
     * @var array
     */
    private $allowedOperators = [ '==', '!=', '%..%', 'filter' ];

    protected function __construct(string $column, $operator = '=', $value = null)
    {
        if (!in_array($operator, $this->allowedOperators)) {
            $allowOperatorsString = implode(',', $this->allowedOperators);

            throw new \Exception('Der $operator darf nur aus den folgenden Zeichen bestehen: '. $allowOperatorsString);
        }   
        $this->column = $column;
        $this->operator = $operator; 
        $this->value = $value; 
    }


    /**
     * defines the structure of the where statment and return it as an array
     */
    public function getStatment() : array {
        return [
            $this->column, //columnname
            $this->operator, //operator
            $this->value, //value to check in a column
        ];
    }


    /**
     * @param string $column
     * @param mixed $operator can be '==' or '!='
     * @param mixed $value
     * @return $this
     */
    public static function createStatment(string $column, $operator = '=', $value = null) : WhereClauseStatment {
        $instance = new static($column, $operator, $value);
        return $instance;
    }



} 