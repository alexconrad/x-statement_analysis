<?php


namespace Misico\Web\Controller;


use Misico\Common\Common;
use Misico\Controller\Output\ViewOutput;
use Misico\DB\Tables\FileTablesDao;
use Misico\DB\Tables\FileUploadsDao;
use Misico\DB\Tables\SysTableStatsDao;

class CompareController
{
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

    public function __construct(FileTablesDao $fileTablesDao, ViewOutput $viewOutput, FileUploadsDao $fileUploadsDao, SysTableStatsDao $sysTableStatsDao, Common $common)
    {
        $this->fileTablesDao = $fileTablesDao;
        $this->viewOutput = $viewOutput;
        $this->fileUploadsDao = $fileUploadsDao;
        $this->sysTableStatsDao = $sysTableStatsDao;
        $this->common = $common;
    }


    public function actionIndex()
    {

        $this->viewOutput->setTemplate('compare');
        return $this->viewOutput;

    }

}