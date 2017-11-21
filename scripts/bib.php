<?php
require __DIR__ . '/../vendor/autoload.php';

use NYPL\WarehouseExporter\RecordGetter;
use NYPL\WarehouseExporter\Logger;

$lastRecordId = 1;

while ($lastRecordId) {
    Logger::log()->addInfo('Starting from ' . $lastRecordId);

    $records = RecordGetter::getAllRecords('b', $lastRecordId, 100000);

    foreach ($records as $record) {
        $lastRecordId = $record['record_num'];
    }
}

Logger::log()->addInfo('Done retrieving records');
