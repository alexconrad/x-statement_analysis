<?php

namespace Misico\DatabaseDeployment;

use Misico\Files\DirectoryService;

class DatabaseDeployment
{


    /**
     * @var DirectoryService
     */
    private $directoryService;

    /**
     * @var DatabaseDeploymentDAO
     */
    private $databaseDeploymentDAO;

    public function __construct(DirectoryService $directoryService, DatabaseDeploymentDAO $databaseDeploymentDAO)
    {
        $this->directoryService = $directoryService;
        $this->databaseDeploymentDAO = $databaseDeploymentDAO;

        $this->databaseDeploymentDAO->createTableIfExists();
    }

    public function deployUp()
    {

        $lastAppliedFilename = $this->databaseDeploymentDAO->getLastFilenameThatWasApplied();

        $dir = APP_ROOT_PATH.'sql';

        $files = $this->directoryService->listFilesInDirectory($dir, 'M', ['php'], $lastAppliedFilename);

        echo 'Found: ' .count($files)." database deployment files. Processing\n";

        foreach ($files as $file) {
            echo 'File: ' .basename($file). ' ... ';
            /** @var DatabaseDeploymentFileInterface $object */
            $object = $this->directoryService->returnObjectFromFile($file, 'sql\\');
            $object->migrateUp();

            [$filenameNoExtension] = $this->directoryService->filenameWithoutExtension(basename($file));
            $this->databaseDeploymentDAO->setFilenameAsApplied($filenameNoExtension);
            echo "Done\n";
        }

        echo "Finished.\n";
    }

    public function deployDown(int $times) : void
    {
        $lastAppliedFilename = $this->databaseDeploymentDAO->getLastFilenameThatWasApplied();

        $dir = APP_ROOT_PATH.'sql';

        $files = $this->directoryService->listFilesInDirectory($dir, 'M', ['php'], null, $lastAppliedFilename);

        echo '[REVERTING] Found: ' .count($files)." database deployment files. Trying to revert [$times] times.\n";

        if ($times <= 0) {
            return;
        }

        $cnt = 0;
        foreach ($files as $file) {
            $cnt++;
            echo "[$cnt] File: ".basename($file). ' ... ';
            /** @var DatabaseDeploymentFileInterface $object */
            $object = $this->directoryService->returnObjectFromFile($file, 'sql\\');
            $object->migrateDown();

            [$filenameNoExtension] = $this->directoryService->filenameWithoutExtension(basename($file));
            $this->databaseDeploymentDAO->setFilenameAsReverted($filenameNoExtension);
            echo "Done\n";
            if ($times >= $cnt) {
                break;
            }
        }

        echo "Finished.\n";
    }
}
