<?php
namespace NYPL\WarehouseExporter;

class RecordExporter
{
    const PATRON_ID_FIELD = 'patron_id';

    protected static $count = 0;

    /**
     * @param string $recordType
     * @param int $startId
     * @throws \DomainException
     *
     * @return int
     */
    protected static function determineStartId($recordType = '', $startId = 0)
    {
        if ($startId) {
            return $startId;
        }

        if ($storedStartId = RecordStorage::getRedis()->get('LastRecordId:' . $recordType)) {
            return (int) $storedStartId + 1;
        }

        throw new \DomainException('No starting record ID was found.');
    }

    /**
     * @param string $recordType
     * @param int $specifiedStartId
     * @param string $idField
     * @throws \DomainException
     *
     * @return int
     */
    public static function exportRecords($recordType = '', $specifiedStartId = 0, $idField = '')
    {
        self::validateRecordType($recordType);

        $startId = self::determineStartId($recordType, $specifiedStartId);

        RecordWriter::setStartId($startId);

        $maximumId = RecordGetter::getMaximumId();

        $lastRecordId = 0;

        for ($currentStartId = $startId; $currentStartId <= $maximumId; $currentStartId += (int) Config::get('MAX_RECORDS_PER_QUERY')) {
            $lastRecordId = self::processRecords($recordType, $currentStartId, $idField);
        }

        if (!$lastRecordId) {
            return false;
        }

        RecordWriter::flushRecords();

        WarehouseWriter::copyRecords(RecordWriter::getS3Prefix());

        RecordStorage::getRedis()->set('LastRecordId:' . $recordType, $lastRecordId);

        return self::$count;
    }

    /**
     * @param string $recordType
     * @throws \DomainException
     * @return bool
     */
    protected static function validateRecordType($recordType = '')
    {
        switch ($recordType) {
            case 'circ_trans':
                return true;
        }

        throw new \DomainException('Table specified (' . $recordType . ') is not currently supported.');
    }

    /**
     * @param string $recordType
     * @param int $currentStartId
     * @param string $idField
     *
     * @throws \DomainException
     * @return int|null
     */
    protected static function processRecords($recordType = '', $currentStartId = 0, $idField = '')
    {
        Logger::log()->addDebug('Retrieving ' . number_format(Config::get('MAX_RECORDS_PER_QUERY'), 0) .
            ' record(s) from ' . $currentStartId);

        $records = self::getRecords($recordType, $currentStartId);

        if (!$records) {
            return null;
        }

        $record = [];

        foreach ($records as $record) {
            ++self::$count;

            $record = array_map(function ($value) {
                if (is_string($value)) {
                    return trim($value);
                }
                return $value;
            }, $record);

            RecordWriter::writeRecord(
                $recordType,
                Anonymizer::anonymizeRecord($record, self::PATRON_ID_FIELD),
                $idField
            );
        }

        return (int) $record[$idField];
    }

    /**
     * @param string $recordType
     * @param int $currentStartId
     *
     * @return \Aura\Sql\Iterator\AllIterator
     */
    protected static function getRecords($recordType = '', $currentStartId = 0)
    {
        return RecordGetter::getRecords($recordType, $currentStartId, Config::get('MAX_RECORDS_PER_QUERY'));
    }
}
