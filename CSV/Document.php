<?php

namespace CSV;

use Exception;

class Document {


    /**
     * the delimiter for the columns
     * @var string only one string
     */
    protected $delimiter;

    /**
     * the enclosure character for fields inside a column
     * @var string only one string
     */
    protected $enclosure;

    /**
     * the character for linebreaks
     */
    protected $lineBreak;

    /**
     * filePath
     */
    protected $filePath;

    /**
     * the content of the file
     */
    protected $fileContent;

    /**
     * @param string the csv file Path
     */
    public function __construct($csvFilePath, $delimiter = ',', $enclosure = '"', $lineBreak = '\r\n')
    {

        if(!file_exists($csvFilePath) || !is_readable($csvFilePath)) {
            throw new Exception("Der Pfad der Datei $csvFilePath ist nicht vorhanden oder bestitzt keine Schreibrechte");
        }

        $this->filePath = $csvFilePath;
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->lineBreak = $lineBreak;
    }


    public function getFileContent() : array {
        return $this->fileContent;
    }

    public function getDelimiter() : string {
        return $this->delimiter;
    }
    public function getLineBreak() : string {
        return $this->lineBreak;
    }
    public function getEnclosure() : string {
        return $this->enclosure;
    }

    public function readFile() {
        return fopen($this->filePath, 'r');
    }


}