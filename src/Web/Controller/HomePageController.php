<?php

namespace Misico\Web\Controller;

use Misico\Common\Common;
use Misico\Controller\Output\RedirectOutput;
use Misico\Controller\Output\ViewOutput;
use Misico\DB\Tables\FileTablesDao;
use Misico\DB\Tables\FileUploadsDao;
use Misico\DB\Tables\SysTableStatsDao;
use Misico\Service\Exceptions\ImportException;
use Misico\Service\Exceptions\UploadException;
use Misico\Service\ImportService;
use Misico\Service\UploadService;

class HomePageController
{
    /**
     * @var ViewOutput
     */
    private $viewOutput;
    /**
     * @var FileUploadsDao
     */
    private $fileUploadsDao;
    /**
     * @var Common
     */
    private $common;
    /**
     * @var UploadService
     */
    private UploadService $uploadService;
    private ImportService $importService;

    public function __construct(ViewOutput $viewOutput, FileUploadsDao $fileUploadsDao, Common $common, UploadService $uploadService, ImportService $importService)
    {
        $this->viewOutput = $viewOutput;
        $this->fileUploadsDao = $fileUploadsDao;
        $this->common = $common;
        $this->uploadService = $uploadService;
        $this->importService = $importService;
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
     * @throws UploadException
     * @throws ImportException
     */
    public function actionUpload()
    {
        if (!isset($_FILES)) {
            $this->viewOutput->assign('error', 'No files uploaded');
            return $this->viewOutput;
        }



        $tableFile = $_FILES['table_file'];
        $statementFile = $_FILES['statement_file'];

        $fileUpload = $this->uploadService->upload($tableFile, $statementFile, $_POST['descr']);

        $this->importService->saveTable($fileUpload, $_POST['table_schema']);
        $this->importService->saveStatement($fileUpload, $_POST['table_schema']);

        $ret = new RedirectOutput();
        $ret->setRedirectUrl($this->common->link(self::class, 'index', ['uploaded' => $fileUpload->getFileNameTable().' and '.$fileUpload->getFileNameStatement()]));
        return $ret;

    }



}
