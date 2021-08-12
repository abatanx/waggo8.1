<?php

require_once 'waggo_example.php';

$price = new WGMModel('waggo8_example_price');
$price->get();

print_r($price->avars);
