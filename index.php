<?php
require $_SERVER['DOCUMENT_ROOT'] . "/classes/DB.class.php";
require $_SERVER['DOCUMENT_ROOT'] . "/classes/position.class.php";
require $_SERVER['DOCUMENT_ROOT'] . "/classes/invoice.class.php";

$invoice = new Invoice();
var_dump($invoice->filter(array('date:>' => '01.01.2020', 'status:=' => 'Оплачен')));