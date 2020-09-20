<?php
require ('bootstrap.php');

$price = Example::Reader()->where('Price','==', 3)->get();

print_r($price);