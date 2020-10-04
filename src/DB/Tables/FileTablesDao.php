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

    public function addTable(string $table): int {
        $query = "INSERT INTO file_tables SET table_name = :name";
        return $this->mySQL->write($query,['name'=>$table]);
    }
}
