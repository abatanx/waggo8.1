<?php

require_once 'waggo_example.php';

$price = new WGMModel('waggo8_example_price');
$price->getVars(['id'=>1, 'name'=>'Apple']);

print_r($price->avars);
