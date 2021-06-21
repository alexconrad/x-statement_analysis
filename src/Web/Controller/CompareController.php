<?php


namespace Misico\Web\Controller;


use Misico\Common\Common;
use Misico\Controller\Output\ViewOutput;
use Misico\DB\MySQL;
use Misico\DB\Tables\FileTablesDao;
use Misico\DB\Tables\FileUploadsDao;
use Misico\DB\Tables\SysTableStatsDao;
use Misico\FriendlyException;

class CompareController
{
    public const COMPARE_EVERYTHING = ':Everything';
    /**
     * @var FileTablesDao
     */
    private $fileTablesDao;
    /**
     * @var ViewOutput
     */
    private $viewOutput;
    /**
     * @var FileUploadsDao
     */
    private $fileUploadsDao;
    /**
     * @var SysTableStatsDao
     */
    private $sysTableStatsDao;

    /** @var Common */
    private $common;
    /**
     * @var MySQL
     */
    private $mySQL;

    public function __construct(FileTablesDao $fileTablesDao, ViewOutput $viewOutput, FileUploadsDao $fileUploadsDao, SysTableStatsDao $sysTableStatsDao, Common $common, MySQL $mySQL)
    {
        $this->fileTablesDao = $fileTablesDao;
        $this->viewOutput = $viewOutput;
        $this->fileUploadsDao = $fileUploadsDao;
        $this->sysTableStatsDao = $sysTableStatsDao;
        $this->common = $common;
        $this->mySQL = $mySQL;
    }


    public function actionIndex()
    {
        $fileIds = $_GET['se'] ?? null; //prevent E_NOTICE
        if (!is_array($fileIds)) {
            throw new FriendlyException('No selected files.');
        }
        $firstFileId = array_shift($fileIds);

        $firstFileUpload = $this->fileUploadsDao->getFileUpload($firstFileId);

        $compareTo = $this->sysTableStatsDao->getFileTotals($firstFileId);

        $compareFilesData = [];
        $compareFiles = [];
        foreach ($fileIds as $fileId) {
            $compareFilesData[$fileId] = $this->sysTableStatsDao->getFileTotals($fileId);
            $compareFiles[] = $this->fileUploadsDao->getFileUpload($fileId);
        }

        $query = 'SELECT table_id FROM sys_table_stats WHERE file_id = :id ORDER BY total_latency DESC LIMIT 20';
        $tableIds = $this->mySQL->column($query, ['id' => $firstFileId]);

        $pairs = [];
        foreach ($tableIds as $tableId) {
            $pair = [];
            $pair['first'] = $this->sysTableStatsDao->getTableRow($firstFileId, $tableId);
            foreach ($fileIds as $fileId) {
                $pair['compareTo'][] = $this->sysTableStatsDao->getTableRow($fileId, $tableId);
            }
            $pairs[$tableId] = $pair;
        }

        $allTables = $this->fileTablesDao->idsToTable();


        $this->viewOutput->assign('tables', $allTables);
        $this->viewOutput->assign('pairs', $pairs);

        $this->viewOutput->assign('firstData', $compareTo);
        $this->viewOutput->assign('firstFileUpload', $firstFileUpload);
        $this->viewOutput->assign('compareFilesData', $compareFilesData);
        $this->viewOutput->assign('compareFiles', $compareFiles);

        $this->viewOutput->setTemplate('compare');
        return $this->viewOutput;

    }

}
