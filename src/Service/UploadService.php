<?php


namespace Misico\Service;

use Misico\DB\Tables\FileUploadsDao;
use Misico\Entity\DbFileUpload;
use Misico\Service\Exceptions\UploadException;

class UploadService
{
    private FileUploadsDao $fileUploadsDao;
    private ImportService $importService;

    public function __construct(FileUploadsDao $fileUploadsDao, ImportService $importService)
    {
        $this->fileUploadsDao = $fileUploadsDao;
        $this->importService = $importService;
    }


    /**
     * @param array $tableFile
     * @param array $statementFile
     * @param string|null $uploadDescription
     * @return DbFileUpload
     * @throws UploadException
     */
    public function upload(array $tableFile, array $statementFile, ?string $uploadDescription): DbFileUpload
    {
        if (strtolower(substr($tableFile['name'], strrpos($tableFile['name'], '.') + 1)) !== 'csv') {
            throw new UploadException('Invalid table file');
        }
        if (strtolower(substr($statementFile['name'], strrpos($statementFile['name'], '.') + 1)) !== 'csv') {
            throw new UploadException('Invalid statement file');
        }

        $fileNameTable = $tableFile['name'];
        $description = $fileNameTable;
        if (!empty($uploadDescription)) {
            $description = $uploadDescription;
        }

        $fileNameStatement = $statementFile['name'];

        $ok = move_uploaded_file($tableFile['tmp_name'], $this->importService->buildFullFileName($fileNameTable));
        if ($ok !== true) {
            throw new UploadException('Cannot upload table file');
        }
        $ok = move_uploaded_file($statementFile['tmp_name'], $this->importService->buildFullFileName($fileNameStatement));
        if ($ok !== true) {
            throw new UploadException('Cannot upload statement file');
        }

        $fileId = $this->fileUploadsDao->addUpload($fileNameTable, $fileNameStatement, $description);

        return $this->fileUploadsDao->getFileUpload($fileId);
    }


}
