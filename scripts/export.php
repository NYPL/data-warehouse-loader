<?php
require __DIR__ . '/../vendor/autoload.php';

use NYPL\WarehouseExporter\RecordExporter;
use NYPL\WarehouseExporter\Logger;

try {
    if (empty($argv[1])) {
        throw new InvalidArgumentException('Table name must be specified.');
    }

    $startRecordId = isset($argv[2]) ? $argv[2] : 0;

    $exportCount = RecordExporter::exportRecords($argv[1], $startRecordId, 'id');

    Logger::log()->addInfo(number_format($exportCount, 0) . ' record(s) exported.');
} catch (Throwable $exception) {
    Logger::log()->addError('Exception thrown: ' . $exception->getMessage());
}
