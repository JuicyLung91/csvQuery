## Where query
Filter a CSV file.

```php

Sales::Reader()->where( [
    ['ItemId', '==', $itemId],
    ['Date', '%..%', $date]
] )->get();

Filter
* ```==``` is equal
* ```!=``` is not equal
* ```%..%``` contains a value 
* ```filter``` custom filter