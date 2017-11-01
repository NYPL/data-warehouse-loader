<?php
namespace NYPL\WarehouseExporter;

class RecordWriter
{
    protected static $count = 0;

    /**
     * @var \AvroDataIOWriter
     */
    protected static $avroIo;

    /**
     * @var int
     */
    protected static $lastRecordId;

    /**
     * @var string
     */
    protected static $recordType = '';

    /**
     * @var \AvroSchema
     */
    protected static $schema;

    /**
     * @var int
     */
    protected static $startId = 0;

    /**
     * @param string $recordType
     * @param array $record
     * @param string $idField
     *
     * @throws \DomainException
     */
    public static function writeRecord($recordType = '', array $record = [], $idField = '')
    {
        AvroInitializer::initialize();

        if (empty($record[$idField])) {
            throw new \DomainException('No ID field found in record');
        }

        self::setRecordType($recordType);

        if (!self::getAvroIo() || self::$count >= (int) Config::get('MAX_RECORDS_PER_FILE')) {
            self::startNewFile($record[$idField]);
        }

        ++self::$count;

        self::getAvroIo()->append($record);
    }

    public static function flushRecords()
    {
        if (self::getAvroIo()) {
            $fileName = self::getFileName(self::getLastRecordId());

            Logger::log()->addNotice(
                'Wrote ' . number_format(self::$count, 0) . ' record(s) to ' .
                basename($fileName)
            );

            self::getAvroIo()->close();

            S3Uploader::uploadFile(
                self::getS3Prefix() . basename($fileName),
                $fileName
            );
        }
    }

    /**
     * @return string
     */
    public static function getS3Prefix()
    {
        return self::getRecordType() . '/' . self::getStartId() . '/';
    }

    /**
     * @param int $startRecordId
     */
    protected static function startNewFile($startRecordId = 0)
    {
        if (self::getAvroIo()) {
            self::flushRecords();
        }

        self::initializeAvroIo($startRecordId);

        self::$count = 0;
    }

    /**
     * @param int $startRecordId
     *
     * @return string
     */
    protected static function getFileName($startRecordId = 0)
    {
        return __DIR__ . '/../exports/' . self::getRecordType() . '_' . $startRecordId . '_' .
            Config::get('MAX_RECORDS_PER_FILE') . '.avr';
    }

    /**
     * @param int $startRecordId
     */
    protected static function initializeAvroIo($startRecordId = 0)
    {
        $file = new \AvroFile(self::getFileName($startRecordId), \AvroFile::WRITE_MODE);

        $writer = new \AvroIODatumWriter(self::getSchema());

        self::setAvroIo(
            new \AvroDataIOWriter($file, $writer, self::getSchema())
        );

        self::setLastRecordId($startRecordId);
    }

    /**
     * @return \AvroDataIOWriter
     */
    public static function getAvroIo()
    {
        return self::$avroIo;
    }

    /**
     * @param \AvroDataIOWriter $avroIo
     */
    public static function setAvroIo($avroIo)
    {
        self::$avroIo = $avroIo;
    }

    /**
     * @return int
     */
    public static function getLastRecordId()
    {
        return self::$lastRecordId;
    }

    /**
     * @param int $lastRecordId
     */
    public static function setLastRecordId($lastRecordId)
    {
        self::$lastRecordId = (int) $lastRecordId;
    }

    /**
     * @return string
     */
    public static function getRecordType()
    {
        return self::$recordType;
    }

    /**
     * @param string $recordType
     */
    public static function setRecordType($recordType)
    {
        self::$recordType = $recordType;
    }

    /**
     * @return \AvroSchema
     */
    public static function getSchema()
    {
        if (!self::$schema) {
            self::setSchema(
                \AvroSchema::parse(file_get_contents(__DIR__ . '/../schemas/' . self::getRecordType() . '.json'))
            );
        }

        return self::$schema;
    }

    /**
     * @param \AvroSchema $schema
     */
    public static function setSchema($schema)
    {
        self::$schema = $schema;
    }

    /**
     * @return int
     */
    public static function getStartId()
    {
        return self::$startId;
    }

    /**
     * @param int $startId
     */
    public static function setStartId($startId)
    {
        self::$startId = $startId;
    }
}
