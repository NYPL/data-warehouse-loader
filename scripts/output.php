<?php
require __DIR__ . '/../vendor/autoload.php';

use NYPL\WarehouseExporter\RecordReader;
use NYPL\WarehouseExporter\Logger;

try {
    if (empty($argv[1])) {
        throw new InvalidArgumentException('No filename specifed');
    }

    RecordReader::readFile(
        'circ_trans',
        $argv[1]
    );
} catch (Throwable $exception) {
    Logger::log()->addError('Exception thrown: ' . $exception->getMessage());
}
