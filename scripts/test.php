<?php
require __DIR__ . '/../vendor/autoload.php';

use NYPL\WarehouseExporter\Anonymizer;
use NYPL\WarehouseExporter\RecordGetter;

$record = RecordGetter::getRecord($argv[1]);

$record = Anonymizer::anonymizeRecord($record, 'patron_id');

print_r($record);
