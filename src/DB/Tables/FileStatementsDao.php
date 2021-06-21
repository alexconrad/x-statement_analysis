<?php


namespace Misico\DB\Tables;


use Misico\DB\MySQL;

class FileStatementsDao
{
    private MySQL $mySQL;

    public function __construct(MySQL $mySQL)
    {
        $this->mySQL = $mySQL;
    }

    public function statementIdByHash(string $hash): ?int
    {
        return $this->mySQL->oneCell('SELECT statement_id FROM file_statements WHERE hash = :hash', ['hash' => $hash]);
    }

    public function addStatement($mysqlStatement)
    {
        $query = 'INSERT INTO file_statements SET hash = :asd, statement = :qwe';
        return $this->mySQL->write($query, [
            'asd' => $this->statementHash($mysqlStatement),
            'qwe' => $mysqlStatement
            ]
        );
    }

    public function statementHash($sql): string
    {
        return md5($sql);
    }


}
