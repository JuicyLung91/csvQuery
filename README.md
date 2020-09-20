## Where query
Add a new CSV model class

```php

use CSV\CSVModel;

class Example extends CSVModel {

/**
 * an array with the names of the headerColumns
 * @var array
 */
  protected $headerColumns = ['Columnname', 'Price'];

  protected $csvFilePath = __DIR__.'/example.csv';



}


```

### Filter a CSV file.

```php
Example::Reader()->where('Price','==', 3)->get();
```

### combine multiple queries

```php
Example::Reader()
        ->where([
            ['Price','==', 3],
            ['Columnname','==', 'test2']
        ])->get();
```

### Possible filter
* ```==``` is equal
* ```!=``` is not equal
* ```%..%``` contains a value 
* ```filter``` custom filter