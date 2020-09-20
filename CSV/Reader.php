<?php

namespace CSV;

use Exception;

class Reader extends AbstractCSV { 

    /**
     * a specific where clause for search rows by values inside a column 
     * @var array
     */
    protected $whereClause = [];

    /**
     * sort by a column or custom function
     * @var mixed can be a string with a column name or a function
     */
    protected $orderBy;
    
    
    /**
     * sort order
     * @var string // ASC OR DESC
     */
    protected $sortOrder = 'ASC'; 

    

    /**
     * a limit for the output
     * @var int
     */
    protected $limit = 0;

    /**
     * an offset for the start
     * @var int
     */
    protected $offset = 0;


    /**
     * add a where statment to the current query 
     * like where column == xx or where column != yy
     * 
     * @param mixed $columnOrArray
     * @param mixed $operator can be '==' or '!='
     * @param mixed $value
     * @return Reader $this
     */
    public function where($columnOrArray, $operator = '==', $value = null) : Reader {
        if (is_array($columnOrArray)) {
            $this->whereClause = []; //init where clause
            foreach ($columnOrArray as $whereClause) {
                $column = $whereClause[0];
                $operator = $whereClause[1];
                $value = $whereClause[2];
                $this->whereClause[] = WhereClauseStatment::createStatment($column, $operator, $value);
            }
        } else {
            $this->whereClause = [ WhereClauseStatment::createStatment($columnOrArray, $operator, $value) ];
        }

        return $this;
    }


    public function setLimit(int $limit) {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param mixed
     */
    public function setOrderBy( $columnName) {
        if (!is_callable($columnName) && !in_array($columnName, $this->headerColumns)) {
            throw new Exception('Eine Sortierung nach dieser Spalte ist nicht möglich. Die Spalte ist in der CSV nicht definiert.');
        }
        $this->orderBy = $columnName;
        return $this;
    }

    /**
     * @param string ASC or DESC
     */
    public function setSortOrder(string $order) {
        if ($order !== 'ASC' && $order !== 'DESC') {
            throw new Exception("Die Order erlaubt nicht die Werte ASC oder DESC. Der Wert $order weicht davon ab.");
        }

        $this->sortOrder = $order;
        return $this;
    }

    // @todo : check if the number of rows inside $this->document is smaller then the offset
    public function setOffset(int $offset) {
        if ( $offset > $this->getRowsCount()) {
            throw new Exception('Der Offset darf nicht größer sein als die Anzahl an Zeilen in der CSV');
        }
        $this->offset = $offset;
        return $this;
    }


    /**
     * returns an array with the found rows
     * if a where clause is set it will search for specific rows based by a column value
     * if an offset is set it will skip the offset number of rows
     * if an limit is set it will return only the limit number of rows
     * @return array 
     */
    public function get() : array {

        if ($this->whereClause === [] && $this->limit === 0 && $this->offset === 0) {
            return $this->getRows(); //return all rows in the csv because no clause is set
        }

        $rows = $this->getRows(); //get all rows

        if ($this->whereClause !== []) { // if specific where clause is set

            $found = array();
            foreach ($rows as $csvRowKey => $csvRow) {
                $intersection = 0;
                foreach ($this->whereClause as $whereKey => $whereClause) {
                    $operator = $whereClause->operator;
                    $columnName = $whereClause->column;
                    $valueToFind = $whereClause->value;
                    
                    if (!array_key_exists($columnName, $csvRow)) {
                        throw new Exception("Die Spalte $columnName existiert nicht in der CSV");
                    }

                    //@todo refactor this to WhereClauseStatment
                    //@todo refactor the returning array_key_exists function
                    
                    if ( $operator === '==') { //find where specific column has value
  
                        if (array_key_exists($columnName, $csvRow) && $csvRow[$columnName] == $valueToFind) {
                            $intersection++;
                        }
                    }

                    if ( $operator === '%..%') { //find if column contains string
                        if (array_key_exists($columnName, $csvRow) && strpos( strtolower( $csvRow[$columnName]), strtolower ($valueToFind) ) !== false ) {
                            $intersection++;
                        }
                    }

                    if ($operator === '!=') { //find where specific column has NOT value
                        if (array_key_exists($columnName, $csvRow) && $csvRow[$columnName] != $valueToFind) {
                            $intersection++;
                        }
                    }

                    if ($operator === 'filter' && is_callable($valueToFind)) { //custom filter function
                        if (call_user_func($valueToFind, $csvRow[$columnName], $csvRow)) {
                            $intersection++;
                        }
                    }
                }
                if ($intersection == count($this->whereClause)) {
                    $found[$csvRowKey] = $csvRow;
                }
            }
            $rows = $found;

        }


        if ($this->offset > 0) {
            // get the offset
            $rows = $this->offset === 0 ? $rows : array_splice($rows, $this->offset);
        }

        if ( $this->limit > 0 && ($this->getRowsCount() - $this->limit) > 0 ) {
            //get only the limit number of rows if limit is set and the left number of rows minus limit is > 0 
           array_splice($rows, $this->limit );
        }

        if ($this->orderBy && is_string($this->orderBy)) {   
            usort($rows, function ($a, $b)
            {
                $t1 = $a[$this->orderBy];
                $t2 = $b[$this->orderBy];
                if ($this->sortOrder === 'ASC') {
                    return $t1 - $t2;
                } else {
                    return $t2 - $t1;
                }
            } );
        } else if ($this->orderBy && is_callable($this->orderBy)) {
            usort($rows, $this->orderBy);
        }
        return array_values($rows);
    }

    public function first() : array {
        return $this->get()[0];
    }

    public function last() : array {
        return end($this->get());
    }

    public function pluck(string $column) : array {
        if (!$column) {
            throw new \Exception('Bitte gib einen Spaltennamen an. ');
        }

        return array_map(function($item) use($column) { 
            return $item[$column]; 
        }, $this->rows);

    }

}