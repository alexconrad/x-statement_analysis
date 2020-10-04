<?php

namespace DatabaseDeployment;

use Misico\DB\MySQL;

class DatabaseDeploymentDAO
{
    public const TABLE_NAME = 'database_deployment';

    /**
     * @var MySQL
     */
    private $mySQL;

    public function __construct(MySQL $mySQL)
    {
        $this->mySQL = $mySQL;
    }

    public function createTableIfExists()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS  `' . self::TABLE_NAME . '` (
    `filename` VARCHAR(50) NOT NULL,
	`applied` TINYINT(1) UNSIGNED NOT NULL,
	`last_applied` DATETIME NULL DEFAULT NULL,
	`last_revert` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`filename`)
) ENGINE=InnoDB;';
        $this->mySQL->rawQuery($sql);
    }

    public function getLastFilenameThatWasApplied()
    {
        /** @noinspection SqlResolve */
        $query = '
            SELECT filename 
            FROM `' . self::TABLE_NAME . '` 
            WHERE applied = :applied 
            ORDER BY filename DESC 
            LIMIT 1
        ';

        $row = $this->mySQL->oneRow($query, ['applied' => 1]);
        $lastFilename = 0;
        if (!empty($row)) {
            $lastFilename = $row['filename'];
        }
        return $lastFilename;
    }

    public function setFilenameAsApplied($file)
    {
        $query = '
            INSERT INTO `' . self::TABLE_NAME . '` 
            (filename,applied,last_applied,last_revert) 
            VALUES 
            (:filename, 1, NOW(), NULL) 
            ON DUPLICATE KEY UPDATE applied = 1, last_applied =NOW()
        ';
        $this->mySQL->write($query, ['filename' => $file]);
    }

    public function setFilenameAsReverted($file)
    {
        $query = 'UPDATE `' . self::TABLE_NAME . '` SET applied = 0, last_revert = NOW() WHERE filename = :filename';
        $this->mySQL->write($query, ['filename' => $file]);
    }
}
