<?php
declare(strict_types=1);

namespace Misico\DB\Tables;


use Misico\DB\MySQL;

class StatementTablesDao
{
/**
     * @var MySQL
     */
    private $mySQL;

    public function __construct(MySQL $mySQL)
    {
        $this->mySQL = $mySQL;
    }

    /**
     * @param int $statementId
     * @param int[] $tableIds
     */
    public function save(int $statementId, array $tableIds): bool {
        if (empty($tableIds)) {
            return false;
        }
        $query = 'INSERT INTO statement_tables (statement_id, table_id) VALUES ' .null;
        $binds = [];
        foreach ($tableIds as $key=>$tableId) {
            $query .= "(:integer_s{$key}, :integer_r{$key}),";
            $binds["integer_s{$key}"] = $statementId;
            $binds["integer_r{$key}"] = $tableId;
        }
        $query = substr($query, 0, strrpos($query, ','));
        $this->mySQL->write($query, $binds);
        return true;
    }

}
