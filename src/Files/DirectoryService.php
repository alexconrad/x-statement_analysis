<?php

namespace Misico\Files;

use DI\Container;
use Misico\Files\Exception\DirectoryServiceException;

class DirectoryService
{


    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $dir
     * @param null $prefix
     * @param array $onlyExtensions
     * @param string $filenameGreaterThan
     * @param string|null $filenameLesserOrEqualThan
     * @return array
     */
    public function listFilesInDirectory(
        string $dir,
        $prefix = null,
        array $onlyExtensions = [],
        string $filenameGreaterThan = null,
        string $filenameLesserOrEqualThan = null
    ): array {

        $files = [];
        if (is_dir($dir)) {
            $readFiles = scandir($dir, SCANDIR_SORT_ASCENDING);

            foreach ($readFiles as $file) {
                if (in_array($file, ['.', '..'])) {
                    continue;
                }

                [$name, $extension] = $this->filenameWithoutExtension($file);

                if (!empty($prefix) && strpos($name, $prefix) !== 0) {
                    continue;
                }

                if (!empty($extension) && !in_array($extension, $onlyExtensions, false)) {
                    continue;
                }

                if (($filenameGreaterThan !== null) && $name <= $filenameGreaterThan) {
                    continue;
                }

                if (($filenameLesserOrEqualThan !== null) && $name > $filenameLesserOrEqualThan) {
                    continue;
                }

                $files[] = $dir . DIRECTORY_SEPARATOR . $file;
            }
        }

        return $files;
    }

    /**
     * @param string $file
     * @return array
     */
    public function filenameWithoutExtension(string $file): array
    {

        if (substr_count($file, DIRECTORY_SEPARATOR) != 0) {
            throw new DirectoryServiceException('File cannot contain directory separator.');
        }

        $parts = explode('.', $file);
        $extension = array_pop($parts);
        return array(implode('.', $parts), $extension);
    }

    /**
     * @param string $filename
     * @param string $namespace
     * @return object
     */
    public function returnObjectFromFile(string $filename, $namespace)
    {

        [$className] = $this->filenameWithoutExtension(basename($filename));
        if (class_exists($namespace.$className)) {
            throw new DirectoryServiceException('Class already exists ! ' . $namespace.$className);
        }

        if (file_exists($filename)) {
            /** @noinspection PhpIncludeInspection */
            require_once $filename;
        } else {
            throw new DirectoryServiceException('File does not exist ' . $filename);
        }


        if (class_exists($namespace.$className)) {
            return $this->container->get($namespace.$className);
        }

        throw new DirectoryServiceException('No class ' . $namespace.$className . ' to be returned from ' . $filename);
    }
}
