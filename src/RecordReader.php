<?php
namespace NYPL\WarehouseExporter;

class RecordReader
{
    protected static $count = 0;

    /**
     * @var \AvroDataIOReader
     */
    protected static $avroIo;

    public static function readFile($recordType = '', $fileName = '')
    {
        AvroInitializer::initialize();

        self::initializeAvroIo($recordType, $fileName);

        Logger::log()->addDebug('Outputting...');

        $count = 0;

        foreach (self::getAvroIo()->data() as $datum) {
            echo ++$count . "\n";
            echo var_export($datum, true) . "\n";
        }
    }

    public static function flushRecords()
    {
        self::getAvroIo()->close();
    }

    protected static function initializeAvroIo($recordType = '', $fileName = '')
    {
        $schema = \AvroSchema::parse(file_get_contents(__DIR__ . '/../schemas/' . $recordType . '.json'));

        $file = new \AvroFile($fileName, \AvroFile::READ_MODE);

        $reader = new \AvroIODatumReader(null, $schema);

        self::setAvroIo(
            new \AvroDataIOReader($file, $reader)
        );
    }

    /**
     * @return \AvroDataIOReader
     */
    public static function getAvroIo()
    {
        return self::$avroIo;
    }

    /**
     * @param \AvroDataIOReader $avroIo
     */
    public static function setAvroIo($avroIo)
    {
        self::$avroIo = $avroIo;
    }
}
