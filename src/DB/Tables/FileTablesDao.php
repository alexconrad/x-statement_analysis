<?php

namespace Misico\DB\Tables;

use Misico\DB\MySQL;

class FileTablesDao
{


    /**
     * @var MySQL
     */
    private $mySQL;

    public function __construct(MySQL $mySQL)
    {
        $this->mySQL = $mySQL;
    }


    public function tablesToIds() {
        return $this->mySQL->assoc('SELECT table_name,table_id FROM file_tables');
    }

    public function idsToTable() {
        return $this->mySQL->assoc('SELECT table_id,table_name FROM file_tables');
    }

    public function getTable(string $table): ?int {
        $query = 'SELECT table_id FROM file_tables WHERE table_name = :name';
        return $this->mySQL->oneCell($query,['name'=>$table]);
    }


    public function addTable(string $table): int {
        $query = "INSERT INTO file_tables SET table_name = :name";
        return $this->mySQL->write($query,['name'=>$table]);
    }
}
