<?php
namespace NYPL\WarehouseExporter;

use Aura\Sql\ExtendedPdo;

class RecordGetter
{
    /**
     * @var ExtendedPdo
     */
    public static $pdo;

    /**
     * @param string $recordType
     * @param int $startId
     * @param int $limit
     *
     * @return \Aura\Sql\Iterator\AllIterator
     */
    public static function getRecords($recordType = '', $startId = 0, $limit = 0)
    {
        $statement = 'SELECT 
        item.record_num AS item_id,
        patron.record_num AS patron_id,
        bib.record_num AS bib_id,
        circ_trans.* 
        FROM sierra_view.circ_trans AS circ_trans
        LEFT JOIN sierra_view.record_metadata AS item ON item.id = circ_trans.item_record_id
        LEFT JOIN sierra_view.record_metadata AS patron ON patron.id = circ_trans.patron_record_id
        LEFT JOIN sierra_view.record_metadata AS bib ON bib.id = circ_trans.bib_record_id
        WHERE circ_trans.id >= :start_id
        ORDER BY circ_trans.id
        LIMIT :limit';

        $bind = ['start_id' => $startId, 'limit' => $limit];

        return self::getPdo()->yieldAll($statement, $bind);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public static function getRecord($id = 0)
    {
        $statement = 'SELECT 
        item.record_num AS item_id,
        patron.record_num AS patron_id,
        bib.record_num AS bib_id,
        circ_trans.* 
        FROM sierra_view.circ_trans AS circ_trans
        LEFT JOIN sierra_view.record_metadata AS item ON item.id = circ_trans.item_record_id
        LEFT JOIN sierra_view.record_metadata AS patron ON patron.id = circ_trans.patron_record_id
        LEFT JOIN sierra_view.record_metadata AS bib ON bib.id = circ_trans.bib_record_id
        WHERE circ_trans.id = :id';

        $bind = ['id' => $id];

        return self::getPdo()->fetchOne($statement, $bind);
    }

    /**
     * @param string $recordType
     *
     * @return int
     */
    public static function getMaximumId($recordType = '')
    {
        $statement = 'SELECT MAX(circ_trans.id) FROM sierra_view.circ_trans';

        return (int) self::getPdo()->fetchValue($statement);
    }

    /**
     * @return ExtendedPdo
     */
    public static function getPdo()
    {
        if (!self::$pdo) {
            self::setPdo(
                new ExtendedPdo(
                    Config::get('DB_DSN'),
                    Config::get('DB_USERNAME'),
                    Config::get('DB_PASSWORD'),
                    [],
                    []
                )
            );
        }

        return self::$pdo;
    }

    /**
     * @param ExtendedPdo $pdo
     */
    public static function setPdo($pdo)
    {
        self::$pdo = $pdo;
    }
}
