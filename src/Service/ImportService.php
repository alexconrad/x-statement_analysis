<?php


namespace Misico\Service;


use Misico\Common\Common;
use Misico\DB\Tables\FileStatementsDao;
use Misico\DB\Tables\FileTablesDao;
use Misico\DB\Tables\FileUploadsDao;
use Misico\DB\Tables\StatementTablesDao;
use Misico\DB\Tables\SysStatementStatsDao;
use Misico\DB\Tables\SysTableStatsDao;
use Misico\Entity\DbFileUpload;
use Misico\Service\Exceptions\ImportException;

class ImportService
{
    public const BUILD_FILENAME_SECRET = 'sson6@T^I0O3';

    private SysTableStatsDao $sysTableStatsDao;
    private FileTablesDao $fileTablesDao;
    /**
     * @var SysStatementStatsDao
     */
    private SysStatementStatsDao $sysStatementStatsDao;
    /**
     * @var FileStatementsDao
     */
    private FileStatementsDao $fileStatementsDao;
    /**
     * @var StatementTablesDao
     */
    private StatementTablesDao $statementTablesDao;

    private static array $tableIds = [];

    public function __construct(
        FileTablesDao $fileTablesDao,
        FileStatementsDao $fileStatementsDao,
        StatementTablesDao $statementTablesDao,
        SysTableStatsDao $sysTableStatsDao,
        SysStatementStatsDao $sysStatementStatsDao
    )
    {
        $this->sysTableStatsDao = $sysTableStatsDao;
        $this->fileTablesDao = $fileTablesDao;
        $this->sysStatementStatsDao = $sysStatementStatsDao;
        $this->fileStatementsDao = $fileStatementsDao;
        $this->statementTablesDao = $statementTablesDao;
    }

    /**
     * @param DbFileUpload $fileUpload
     * @param string $onlySchema
     * @return true
     * @throws ImportException
     */
    public function saveStatement(DbFileUpload $fileUpload, string $onlySchema): bool
    {
        $statementFileLocation = $this->buildFullFileName($fileUpload->getFileNameStatement());

        $fp = fopen($statementFileLocation, 'rb');
        if (!$fp) {
            throw new ImportException('Cannot open statement file.', 'Cannot open ' . $statementFileLocation);
        }

        $header = [];
        $linec = 0;

        while ($row = fgetcsv($fp)) {

            $linec++;
            if ($linec === 1) {
                $check = [
                    'query', 'db', 'full_scan', 'exec_count', 'err_count', 'warn_count',
                    'total_latency', 'max_latency', 'avg_latency', 'lock_latency', 'rows_sent',
                    'rows_sent_avg', 'rows_examined', 'rows_examined_avg', 'rows_affected', 'rows_affected_avg',
                    'tmp_tables', 'tmp_disk_tables', 'rows_sorted', 'sort_merge_passes',
                    'digest', 'first_seen', 'last_seen',
                ];
                //"query","db","full_scan","exec_count","err_count","warn_count","total_latency","max_latency","avg_latency","lock_latency","rows_sent","rows_sent_avg","rows_examined","rows_examined_avg","rows_affected","rows_affected_avg","tmp_tables","tmp_disk_tables","rows_sorted","sort_merge_passes","digest","first_seen","last_seen"

                foreach ($check as $key) {
                    if (!in_array($key, $row, true)) {
                        throw new ImportException('Invalid header (' . $key . ')');
                    }
                }
                $header = $row;
                continue;
            }

            $line = [];
            foreach ($row as $key => $value) {
                $line[$header[$key]] = $value;
            }

            if ($line['db'] !== $onlySchema) {
                continue;
            }


            $line = [
                'file_id' => $fileUpload->getFileId(),
                'statement_id' => $this->getIdForStatement($line['query']),
                'full_scan' => (empty($line['full_scan']) ? '0' : '1'),
                'exec_count' => $this->needToBeInteger($line['exec_count']),
                'err_count' => $this->needToBeInteger($line['err_count']),
                'warn_count' => $this->needToBeInteger($line['warn_count']),
                'total_latency' => $this->timeToMilliSeconds($line['total_latency'], 'total_latency'),
                'max_latency' => $this->timeToMilliSeconds($line['max_latency'], 'max_latency'),
                'avg_latency' => $this->timeToMilliSeconds($line['avg_latency'], 'avg_latency'),
                'lock_latency' => $this->timeToMilliSeconds($line['lock_latency'], 'lock_latency'),
                'rows_sent' => $this->needToBeInteger($line['rows_sent']),
                'rows_sent_avg' => $this->needToBeInteger($line['rows_sent_avg']),
                'rows_examined' => $this->needToBeInteger($line['rows_examined']),
                'rows_examined_avg' => $this->needToBeInteger($line['rows_examined_avg']),
                'rows_affected' => $this->needToBeInteger($line['rows_affected']),
                'rows_affected_avg' => $this->needToBeInteger($line['rows_affected_avg']),
                'tmp_tables' => $this->needToBeInteger($line['tmp_tables']),
                'tmp_disk_tables' => $this->needToBeInteger($line['tmp_disk_tables']),
                'rows_sorted' => $this->needToBeInteger($line['rows_sorted']),
                'sort_merge_passes' => $this->needToBeInteger($line['sort_merge_passes']),
                'first_seen' => $this->needsToBeMySQLDateTime($line['first_seen']),
                'last_seen' => $this->needsToBeMySQLDateTime($line['last_seen']),
            ];

            $this->lazySave($line, 'statement');
        }

        $this->lazySave([], 'statement', true);
        fclose($fp);

        return true;

    }

    /**
     * @param DbFileUpload $fileUpload
     * @param string $onlySchema
     * @return bool
     * @throws ImportException
     */
    public function saveTable(DbFileUpload $fileUpload, string $onlySchema): bool
    {
        $tableFileLocation = $this->buildFullFileName($fileUpload->getFileNameTable());

        $fp = fopen($tableFileLocation, 'rb');
        if (!$fp) {
            throw new ImportException('Cannot open tablefile.', 'Cannot open ' . $tableFileLocation);
        }

        $header = [];
        $linec = 0;

        while ($row = fgetcsv($fp)) {

            $linec++;
            if ($linec === 1) {
                $check = [
                    'table_name', 'total_latency', 'rows_fetched', 'fetch_latency',
                    'rows_inserted', 'insert_latency', 'rows_updated', 'update_latency',
                    'rows_deleted', 'delete_latency', 'io_read_requests',
                    'io_read', 'io_read_latency', 'io_write_requests', 'io_write',
                    'io_write_latency', 'io_misc_requests', 'io_misc_latency',
                ];

                //"table_schema","table_name","total_latency","rows_fetched","fetch_latency","rows_inserted","insert_latency","rows_updated","update_latency","rows_deleted","delete_latency","io_read_requests","io_read","io_read_latency","io_write_requests","io_write","io_write_latency","io_misc_requests","io_misc_latency"

                foreach ($check as $key) {
                    if (!in_array($key, $row, true)) {
                        throw new ImportException('Invalid header (' . $key . ')');
                    }
                }
                $header = $row;
                continue;
            }

            $line = [];
            foreach ($row as $key => $value) {
                if ($value === '\\N') {
                    $value = NULL;
                }
                $line[$header[$key]] = $value;
            }

            if ($line['table_schema'] !== $onlySchema) {
                continue;
            }

            try {

                $line = [
                    'file_id' => $fileUpload->getFileId(),
                    'table_id' => $this->getIdForTable($line['table_name']),
                    'total_latency' => $this->timeToMilliSeconds($line['total_latency'], 'total_latency'),
                    'rows_fetched' => $this->needToBeInteger($line['rows_fetched']),
                    'fetch_latency' => $this->timeToMilliSeconds($line['fetch_latency'], 'fetch_latency'),
                    'rows_inserted' => $this->needToBeInteger($line['rows_inserted']),
                    'insert_latency' => $this->timeToMilliSeconds($line['insert_latency'], 'insert_latency'),
                    'rows_updated' => $this->needToBeInteger($line['rows_updated']),
                    'update_latency' => $this->timeToMilliSeconds($line['update_latency'], 'update_latency'),
                    'rows_deleted' => $this->needToBeInteger($line['rows_deleted']),
                    'delete_latency' => $this->timeToMilliSeconds($line['delete_latency'], 'delete_latency'),
                    'io_read_requests' => $this->needToBeInteger($line['io_read_requests']),
                    'io_read' => $this->sizeToKib($line['io_read']),
                    'io_read_latency' => $this->timeToMilliSeconds($line['io_read_latency'],'io_read_latency'),
                    'io_write_requests' => $this->needToBeInteger($line['io_write_requests']),
                    'io_write' => $this->sizeToKib($line['io_write']),
                    'io_write_latency' => $this->timeToMilliSeconds($line['io_write_latency'], 'io_write_latency'),
                    'io_misc_requests' => $this->needToBeInteger($line['io_misc_requests']),
                    'io_misc_latency' => $this->timeToMilliSeconds($line['io_misc_latency'], 'io_misc_latency'),
                ];


            } catch (ImportException $exception) {
                throw new ImportException($exception->getMessage().' at line '.$linec.' file '.$fileUpload->getFileNameTable());
            }

            $this->lazySave($line, 'table');
        }

        $this->lazySave([], 'table', true);
        fclose($fp);

        return true;
    }


    private function lazySave($line, string $on, $flush = false): void
    {
        static $pool = [];

        if (!empty($line)) {
            $pool[] = $line;
        }

        if ((count($pool) >= 50) || ($flush && count($pool) > 0)) {
            if ($on === 'statement') {
                $this->sysStatementStatsDao->multiInsert($pool);
            }
            if ($on === 'table') {
                $this->sysTableStatsDao->multiInsert($pool);
            }
            $pool = [];
        }

    }

    /**
     * @param $picoSeconds
     * @param $fieldName
     * @return int
     * @throws ImportException
     */
    private function timeToMilliSeconds($picoSeconds, $fieldName): int
    {
        if ($picoSeconds === null) {
            return 0;
        }

        if (str_contains($picoSeconds, ' ')) {
            throw new ImportException('String contains spaces for ['.$fieldName.']: ['.$picoSeconds.']');
        }

        if ($picoSeconds === '') {
            $picoSeconds = "0";
        }

        if (ctype_digit($picoSeconds) === false) {
            throw new ImportException('String doesnt contain only digits for ['.$fieldName.']: ['.$picoSeconds.']');
        }

        $result = bcmul($picoSeconds, bcpow("10", "-9", 9));
        if (str_contains($result, 'E')) {
            $result = sprintf('%F', $result);
        }

        if (bccomp($result, "0") === 1) {
            return (int)$result;
        }

        return 0;


        //below uses
        /*$val = substr($picoSeconds, 0, strrpos($picoSeconds, ' '));
        if ($val === '0') {
            return 0;
        }
        $val = (float)$val;

        $milliSecondsMultiplier = 0; // less than us, must be 0

        if (strpos($picoSeconds, ' m') !== false) {
            $milliSecondsMultiplier = 60000;
        }
        if (strpos($picoSeconds, ' h') !== false) {
            $milliSecondsMultiplier = 3600 * 1000;
        }
        if (strpos($picoSeconds, ' s') !== false) {
            $milliSecondsMultiplier = 1000;
        }
        if (strpos($picoSeconds, ' ms') !== false) {
            $milliSecondsMultiplier = 1;
        }

        return (int)floor($val * $milliSecondsMultiplier);*/
    }

    private function sizeToKib($string): int
    {
        if ($string === NULL) {
            return  0;
        }

        $val = substr($string, 0, strrpos($string, ' '));
        if ($val === '0') {
            return 0;
        }
        $val = (float)$val;

        $kibiByteMultiplier = 0; // less than us, must be 0

        if (strpos($string, ' GiB') !== false) {
            $kibiByteMultiplier = 1024 * 1024;
        }
        if (strpos($string, ' MiB') !== false) {
            $kibiByteMultiplier = 1024;
        }
        if (strpos($string, ' KiB') !== false) {
            $kibiByteMultiplier = 1;
        }

        return (int)floor($val * $kibiByteMultiplier);
    }

    private function getIdForTable(string $tableName): int
    {
        if (empty(self::$tableIds)) {
            self::$tableIds = $this->fileTablesDao->tablesToIds();
        }

        if (array_key_exists($tableName, self::$tableIds) === false) {
            $tableId = $this->fileTablesDao->getTable($tableName);
            if ($tableId === null) {
                $tableId = $this->fileTablesDao->addTable($tableName);
            }
            self::$tableIds[$tableName] = $tableId;
        }

        return self::$tableIds[$tableName];
    }

    public function buildFullFileName($fileName): string
    {
        return APP_ROOT_PATH . 'htdocs' . DS . 'uploads' . DS . md5($fileName . self::BUILD_FILENAME_SECRET) . '.csv';
    }

    private function getIdForStatement(string $mysqlStatement): int
    {
        $hash = $this->fileStatementsDao->statementHash($mysqlStatement);

        $statementId = $this->fileStatementsDao->statementIdByHash($hash);

        if ($statementId === null) {
            $statementId = $this->fileStatementsDao->addStatement($mysqlStatement);
            $tables = Common::getTablesFromSql($mysqlStatement);

            $statementTableIds = [];
            foreach ($tables as $table) {
                $statementTableIds[] = $this->getIdForTable($table);
            }

            $this->statementTablesDao->save($statementId, $statementTableIds);
        }
        return $statementId;
    }

    private function needToBeInteger($value): int {
        if ($value === '') {
            return 0;
        }
        return (int)$value;
    }

    private function needsToBeMySQLDateTime($string): string {

        $format = "Y-m-d H:i:s";
        $dateTime = \DateTime::createFromFormat($format, $string);
        if ($dateTime === false) {
            $format = "j/n/Y H:i:s";
            $dateTime = \DateTime::createFromFormat($format, $string);
        }

        if (!($dateTime instanceof \DateTime)) {
            throw new ImportException('Invalid date format : '.$string);
        }

        return $dateTime->format('Y-m-d H:i:s');
    }


}
