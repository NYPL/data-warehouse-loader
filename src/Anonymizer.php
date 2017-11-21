<?php
namespace NYPL\WarehouseExporter;

class Anonymizer
{
    /**
     * @param array $record
     * @param string $fieldName
     *
     * @return array
     */
    public static function anonymizeRecord(array $record = [], $fieldName = '')
    {
        if (empty($record[$fieldName])) {
            return $record;
        }

        $anonymousPatronId = self::anonymousPatronId($record[$fieldName]);

        return array_merge(
            $record,
            [
                $fieldName => $anonymousPatronId,
                'patron_record_id' => $anonymousPatronId
            ]
        );
    }

    /**
     * @param int $patronId
     *
     * @return int
     */
    protected static function anonymousPatronId($patronId = 0)
    {
        $keyName = 'Patron:' . $patronId;

        if ($anonymousPatronId = RecordStorage::getRedis()->get($keyName)) {
            return (int) $anonymousPatronId;
        }

        $anonymousPatronId = RecordStorage::getRedis()->incr('PatronIdCounter');

        RecordStorage::getRedis()->set($keyName, $anonymousPatronId);

        return $anonymousPatronId;
    }
}
