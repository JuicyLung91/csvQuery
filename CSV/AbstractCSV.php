<?php

namespace CSV;

use Exception;

abstract class AbstractCSV {

    /**
     * the line number of the header - default = 0
     * @var int
     */
    protected $headerRowNumber = 0;

    /**
     * an array with the names of the headerColumns
     * @var array
     */
    protected $headerColumns = [];


    /**
     *  the csv document
     *  @var Document an object of class CSV\Document
     */
    protected $document;

    /**
     *  the rows in a csv without the header column in a connected multidimensional array
     *  @var array rows
     */
    protected $rows;


    /**
     * @param Document the csv file
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    function utf8_filter(string $value): string{
        return preg_replace('/[^[:print:]\n]/u', '', mb_convert_encoding($value, 'UTF-8', 'UTF-8'));
    }

    public function create() {
        $this->createRows();
    }

    /**
     * create a header and connect the structure from header to row data
     */
    public function createRows() {
        if (($handle = $this->document->readFile()) === false) {
            throw new Exception('Die Datei konnte nicht gelesen werden');
        }
            $c = 0;
            while (($row = fgetcsv($handle, 0, $this->document->getDelimiter())) !== false) {
                if ($this->headerColumns === [] && $c == $this->headerRowNumber) { //header columns are not set yet
                    $this->headerColumns = $row; //set this row as header
                } else if ( count($this->headerColumns) > 0 && $c > $this->headerRowNumber ) { // a header is set or found
                    $data[] = array_combine($this->headerColumns, $row);
                }
                $c++;
            }
            fclose($handle);
        $this->rows = $data;
        return $this->rows;

    }
    /**
     * set the headerRowsNumber - default = 0
     * @var int
     */
    public function setHeaderRowNumber(int $headerOffset): AbstractCSV {
        $this->headerRowNumber = $headerOffset;
        return $this;
    }

    public function setHeaderColumns(array $header) : array {
        $this->headerColumns = $header;
        return $this->headerColumns;
    }

    public function getRows() : array {
        if ($this->rows === null) {
            throw new Exception('Die CSV wurde bisher nicht geparst. Bitte rufe zunächst die ->createRows Funktion auf');
        }

        return $this->rows;
    }


    public function getRowsCount() : int {
        return count($this->getRows());
    }


}