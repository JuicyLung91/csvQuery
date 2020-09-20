<?php

namespace CSV;

use stdClass;

class Item extends CSVModel {

/**
 * an array with the names of the headerColumns
 * @var array
 */
  protected $headerColumns = ['ItemId', 'CommodityGroup'];

  protected $csvFilePath = __DIR__.'/../Files/Item.csv';



}
