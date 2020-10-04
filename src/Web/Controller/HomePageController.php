<?php

namespace Misico\Web\Controller;

use Misico\Common\Common;
use Misico\Controller\Output\RedirectOutput;
use Misico\Controller\Output\ViewOutput;
use Misico\DB\Tables\FileTablesDao;
use Misico\DB\Tables\FileUploadsDao;
use Misico\DB\Tables\SysTableStatsDao;

class HomePageController
{
    /**
     * @var ViewOutput
     */
    private $viewOutput;
    /**
     * @var FileTablesDao
     */
    private $fileTablesDao;
    /**
     * @var FileUploadsDao
     */
    private $fileUploadsDao;
    /**
     * @var SysTableStatsDao
     */
    private $sysTableStatsDao;
    /**
     * @var Common
     */
    private $common;

    public function __construct(FileTablesDao $fileTablesDao, ViewOutput $viewOutput, FileUploadsDao $fileUploadsDao, SysTableStatsDao $sysTableStatsDao, Common $common)
    {

        $this->fileTablesDao = $fileTablesDao;
        $this->viewOutput = $viewOutput;
        $this->fileUploadsDao = $fileUploadsDao;
        $this->sysTableStatsDao = $sysTableStatsDao;
        $this->common = $common;
    }

    /** @noinspection PhpUnused */
    public function actionIndex(): ViewOutput
    {
        $this->viewOutput->assign('uploads', $this->fileUploadsDao->homepageList());
        $this->viewOutput->setTemplate('homepage');
        return $this->viewOutput;
    }

    /**
     * @return RedirectOutput|ViewOutput
     */
    public function actionUpload()
    {
        if (!isset($_FILES)) {
            $this->viewOutput->assign('error', 'No files uploaded');
            return $this->viewOutput;
        }

        $existingTableIds = $this->fileTablesDao->tablesToIds();

        $file = $_FILES['table_file'];

        if (strtolower(substr($file['name'], strrpos($file['name'], '.') + 1)) !== 'csv') {
            $this->viewOutput->assign('error', 'invalid file');
            return $this->viewOutput;
        }

        $file_name = $file['name'];
        $descr = $file_name;
        if (!empty($_POST['descr'])) {
            $descr = $_POST['descr'];
        }

        $fileId = $this->fileUploadsDao->addUpload($file_name, $descr);
        $storeTo = APP_ROOT_PATH . 'htdocs/uploads/' . md5($file_name . FileUploadsDao::BUILD_FILENAME_SECRET) . '.csv';
        $ok = move_uploaded_file($file['tmp_name'], $storeTo);
        if ($ok !== true) {
            $this->viewOutput->assign('error', 'cannot upload file');
            return $this->viewOutput;
        }

        $fp = fopen($storeTo, 'rb');
        if (!$fp) {
            $this->viewOutput->assign('error', 'cannot open file');
            return $this->viewOutput;
        }

        $header = [];
        $line = 0;

        while ($row = fgetcsv($fp)) {

            $line++;
            if ($line === 1) {
                $check = [
                    'table_name', 'total_latency', 'rows_fetched', 'fetch_latency',
                    'rows_inserted', 'insert_latency', 'rows_updated', 'update_latency',
                    'rows_deleted', 'delete_latency', 'io_read_requests',
                    'io_read', 'io_read_latency', 'io_write_requests', 'io_write',
                    'io_write_latency', 'io_misc_requests', 'io_misc_latency',
                ];

                foreach ($check as $key) {
                    if (!in_array($key, $row, true)) {
                        $this->viewOutput->assign('error', 'invalid header (' . $key . ')');
                        return $this->viewOutput;
                    }
                }
                $header = $row;
                continue;
            }

            $line = [];
            foreach ($row as $key => $value) {
                $line[$header[$key]] = $value;
            }


            $tableId = null;
            if (array_key_exists($line['table_name'], $existingTableIds)) {
                $tableId = $existingTableIds[$line['table_name']];
            } else {
                $tableId = $this->fileTablesDao->addTable($line['table_name']);
                $existingTableIds[$line['table_name']] = $tableId;
            }

            $line = [
                'file_id' => $fileId,
                'table_id' => $tableId,
                'total_latency' => $this->timeToMilliSeconds($line['total_latency']),
                'rows_fetched' => $line['rows_fetched'],
                'fetch_latency' => $this->timeToMilliSeconds($line['fetch_latency']),
                'rows_inserted' => $line['rows_inserted'],
                'insert_latency' => $this->timeToMilliSeconds($line['insert_latency']),
                'rows_updated' => $line['rows_updated'],
                'update_latency' => $this->timeToMilliSeconds($line['update_latency']),
                'rows_deleted' => $line['rows_deleted'],
                'delete_latency' => $this->timeToMilliSeconds($line['delete_latency']),
                'io_read_requests' => $line['io_read_requests'],
                'io_read' => $this->sizeToKib($line['io_read']),
                'io_read_latency' => $this->timeToMilliSeconds($line['io_read_latency']),
                'io_write_requests' => $line['io_write_requests'],
                'io_write' => $this->sizeToKib($line['io_write']),
                'io_write_latency' => $this->timeToMilliSeconds($line['io_write_latency']),
                'io_misc_requests' => $line['io_misc_requests'],
                'io_misc_latency' => $this->timeToMilliSeconds($line['io_misc_latency']),
            ];

            $this->lazySave($line);


        }

        $this->lazySave([], true);
        fclose($fp);

        $ret = new RedirectOutput();
        $ret->setRedirectUrl($this->common->link(self::class, 'index', ['uploaded' => $file['name']]));
        return $ret;

    }

    private function lazySave($line, $flush = false): void
    {
        static $pool = [];

        if (!empty($line)) {
            $pool[] = $line;
        }

        if ((count($pool) >= 50) || ($flush && count($pool) > 0)) {
            $this->sysTableStatsDao->multiInsert($pool);
            $pool = [];
        }

    }

    private function timeToMilliSeconds($string): int
    {

        $val = substr($string, 0, strrpos($string, ' '));
        if ($val === '0') {
            return 0;
        }

        $milliSecondsMultiplier = 0; // less than us, must be 0

        if (strpos($string, ' m') !== false) {
            $milliSecondsMultiplier = 60000;
        }
        if (strpos($string, ' h') !== false) {
            $milliSecondsMultiplier = 3600 * 1000;
        }
        if (strpos($string, ' s') !== false) {
            $milliSecondsMultiplier = 1000;
        }
        if (strpos($string, ' ms') !== false) {
            $milliSecondsMultiplier = 1;
        }

        return (int)floor($val * $milliSecondsMultiplier);
    }

    private function sizeToKib($string): int
    {

        $val = substr($string, 0, strrpos($string, ' '));
        if ($val === '0') {
            return 0;
        }

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


}