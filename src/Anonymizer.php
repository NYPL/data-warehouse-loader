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

        return array_merge(
            $record,
            [
                $fieldName => self::anonymousPatronId($record[$fieldName])
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
        if ($anonymousPatronId = RecordStorage::getRedis()->get('Patron:' . $patronId)) {
            return (int) $anonymousPatronId;
        }

        $nextPatronId = RecordStorage::getRedis()->incr('PatronIdCounter');

        RecordStorage::getRedis()->set('Patron:' . $patronId, $nextPatronId);

        return $nextPatronId;
    }
}
