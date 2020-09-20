<?php

use CSV\CSVModel;

class Example extends CSVModel {

/**
 * an array with the names of the headerColumns
 * @var array
 */
  protected $headerColumns = ['Columnname', 'Price'];

  protected $csvFilePath = __DIR__.'/example.csv';



}
