<?php

namespace CSV;

class CSVModel extends AbstractCSV {

  /**
   * an array with the names of the headerColumns
   * @var array
   */
  protected $headerColumns = [];

  /**
   * the path for the csv 
   * @var string path
   */
  protected $csvFilePath;

  public function __construct()
  {
    $doc = new Document($this->csvFilePath);
    parent::__construct($doc, $this);
  }

  /**
   * creates an Reader object
   * @return Reader object
   */
  public static function Reader() : Reader {
    $instance = new static;
    $reader = new Reader($instance->document);
    $reader->setHeaderColumns($instance->headerColumns);
    $reader->createRows();
    return $reader;
  } 

}
