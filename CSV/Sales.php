<?php

namespace CSV;

class Sales extends CSVModel {

/**
 * an array with the names of the headerColumns
 * @var array
 */
  protected $headerColumns = ['ItemId', 'Price', 'Quantity', 'PriceLine', 'Currency', 'TaxRate', 'Date'];

  protected $csvFilePath = __DIR__.'/../Files/Sales.csv';


  /**
   * finds an exchange for a specific sale and converts it to eur
   */
  public static function getExchangeInEur(array $sale) {
    $exchangeRates = ExchangeRates::Reader();
    
    $exchange =  $exchangeRates->where( [
      ['Currency', '==', $sale['Currency']],
      [
        'ValidFrom', 'filter', function ($column, $row) use ($sale) { //custom filter to find an exchange between valiidform and validto
            $exchangeFrom = strtotime( $column  ); //valid from
            $exchangeTo = strtotime( $row['ValidTo'] );
            $saleDate = strtotime( $sale['Date'] );
            return $saleDate >= $exchangeFrom && $saleDate <= $exchangeTo;
        } 
      ],
    ] )->first();

    return number_format((float) $sale['PriceLine'] / $exchange['EuroExchangeRate'], 2);
  } 


  /**
   * calculates the vat rate for a specific price without Vat
   * @param float price excluding vat
   * @param float vatRate default 0.19
   */
  public static function calcVat($price, $vatRate = 0.19) {
    $vatRate = (float) $vatRate;
    $price = (float) $price;
    $vat =  ( $price / (1 + $vatRate) ) * $vatRate;
    return round($vat, 3);
  }

}
