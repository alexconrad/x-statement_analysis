<?php

namespace sql;

use \Misico\DatabaseDeployment\DatabaseDeploymentFileInterface;
use \Misico\DB\MySQL;

/** @noinspection PhpUnused */

class M20000101 implements DatabaseDeploymentFileInterface
{
    /**
     * @var MySQL
     */
    private $mysql;

    public function __construct(MySQL $mySQL)
    {
        $this->mysql = $mySQL;
    }


    public function migrateUp()
    {
        $this->mysql->rawQuery("CREATE TABLE `file_tables` (
	`table_id` INT(11) NOT NULL AUTO_INCREMENT,
	`table_name` VARCHAR(255) NOT NULL DEFAULT '0',
	PRIMARY KEY (`table_id`)
) ENGINE=InnoDB;");

        $this->mysql->rawQuery("CREATE TABLE `file_uploads` (
	`file_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`description` VARCHAR(32) NOT NULL DEFAULT '0',
	`file_name` CHAR(32) NOT NULL DEFAULT '0',
	`dated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`file_id`)
) ENGINE=InnoDB");

        $this->mysql->rawQuery("CREATE TABLE `sys_table_stats` (
	`row_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`file_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`table_id` INT(11) NULL DEFAULT NULL,
	`total_latency` BIGINT(20) NULL DEFAULT NULL,
	`rows_fetched` BIGINT(20) NULL DEFAULT NULL,
	`fetch_latency` BIGINT(20) NULL DEFAULT NULL,
	`rows_inserted` BIGINT(20) NULL DEFAULT NULL,
	`insert_latency` BIGINT(20) NULL DEFAULT NULL,
	`rows_updated` BIGINT(20) NULL DEFAULT NULL,
	`update_latency` BIGINT(20) NULL DEFAULT NULL,
	`rows_deleted` BIGINT(20) NULL DEFAULT NULL,
	`delete_latency` BIGINT(20) NULL DEFAULT NULL,
	`io_read_requests` BIGINT(20) NULL DEFAULT NULL,
	`io_read` BIGINT(20) NULL DEFAULT NULL,
	`io_read_latency` BIGINT(20) NULL DEFAULT NULL,
	`io_write_requests` BIGINT(20) NULL DEFAULT NULL,
	`io_write` BIGINT(20) NULL DEFAULT NULL,
	`io_write_latency` BIGINT(20) NULL DEFAULT NULL,
	`io_misc_requests` BIGINT(20) NULL DEFAULT NULL,
	`io_misc_latency` BIGINT(20) NULL DEFAULT NULL,
	PRIMARY KEY (`row_id`),
	INDEX `file_id` (`file_id`),
	INDEX `table_id` (`table_id`)
) ENGINE=InnoDB;");


    }

    public function migrateDown()
    {
        $this->mysql->rawQuery('DROP TABLE file_tables');
        $this->mysql->rawQuery('DROP TABLE file_uploads');
        $this->mysql->rawQuery('DROP TABLE sys_table_stats');
    }
}
