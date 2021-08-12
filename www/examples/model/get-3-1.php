<?php

require_once 'waggo_example.php';

$price = new WGMModel('waggo8_example_price');
$price->vars['id'] = 5;
$price->get('id');

print_r($price->avars);
