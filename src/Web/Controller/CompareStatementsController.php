<?php


namespace Misico\Web\Controller;


use Misico\Controller\Output\ViewOutput;
use Misico\DB\Tables\FileTablesDao;
use Misico\DB\Tables\FileUploadsDao;
use Misico\DB\Tables\SysTableStatsDao;
use Misico\Service\StatementService;

class CompareStatementsController
{

    private FileTablesDao $fileTablesDao;
    private ViewOutput $viewOutput;
    private FileUploadsDao $fileUploadsDao;
    private StatementService $statementService;

    public function __construct(FileTablesDao $fileTablesDao, ViewOutput $viewOutput, FileUploadsDao $fileUploadsDao, StatementService $statementService)
    {
        $this->fileTablesDao = $fileTablesDao;
        $this->viewOutput = $viewOutput;
        $this->fileUploadsDao = $fileUploadsDao;
        $this->statementService = $statementService;
    }

    public function actionIndex()
    {
        $firstFileId = $_GET['first'] ?? null; //prevent E_NOTICE

        $firstFileUpload = $this->fileUploadsDao->getFileUpload($firstFileId);
        $firstStatements = $this->statementService->getStatementsFromFileId($firstFileUpload->getFileId());

        $statements = $this->statementService->getStatements(array_keys($firstStatements));




        $compareStatementsData = [];
        $compareStatements = [];
        $_GET['compareTo'] = trim($_GET['compareTo']);
        if (!empty($_GET['compareTo'])) {
            $compareToFileIds = explode(',', $_GET['compareTo']);
            foreach ($compareToFileIds as $fileId) {
                $compareStatements[$fileId] = $this->fileUploadsDao->getFileUpload($fileId);
                $compareStatementsData[$fileId] = $this->statementService->getStatementsFromFileId($fileId);
            }
        }

        $this->viewOutput->assign('firstFileUpload', $firstFileUpload);
        $this->viewOutput->assign('statements', $statements);
        $this->viewOutput->assign('firstStatements', $firstStatements);
        $this->viewOutput->assign('compareStatements', $compareStatements);
        $this->viewOutput->assign('compareStatementsData', $compareStatementsData);


        $this->viewOutput->setTemplate('compare_statement');
        return $this->viewOutput;

    }
}
