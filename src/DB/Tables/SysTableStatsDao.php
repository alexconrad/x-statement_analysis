<?php


namespace Misico\DB\Tables;


use Misico\DB\MySQL;
use Misico\Entity\TableStatsRow;

class SysTableStatsDao
{
    private MySQL $mySQL;

    public function __construct(MySQL $mySQL)
    {
        $this->mySQL = $mySQL;
    }


    public function multiInsert($lines)
    {

        $query = 'INSERT INTO sys_table_stats (
                             file_id, 
                             table_id, 
                             total_latency, 
                             rows_fetched, 
                             fetch_latency, 
                             rows_inserted, 
                             insert_latency, 
                             rows_updated, 
                             update_latency, 
                             rows_deleted, 
                             delete_latency, 
                             io_read_requests, 
                             io_read, 
                             io_read_latency, 
                             io_write_requests, 
                             io_write, 
                             io_write_latency, 
                             io_misc_requests, 
                             io_misc_latency) VALUES ' . null;

        $binds = [];
        foreach ($lines as $key => $line) {
            $query .= "(     :integer_r{$key}file_id, 
                             :integer_r{$key}table_id, 
                             :r{$key}total_latency, 
                             :r{$key}rows_fetched, 
                             :r{$key}fetch_latency, 
                             :r{$key}rows_inserted, 
                             :r{$key}insert_latency, 
                             :r{$key}rows_updated, 
                             :r{$key}update_latency, 
                             :r{$key}rows_deleted, 
                             :r{$key}delete_latency, 
                             :r{$key}io_read_requests, 
                             :r{$key}io_read, 
                             :r{$key}io_read_latency, 
                             :r{$key}io_write_requests, 
                             :r{$key}io_write, 
                             :r{$key}io_write_latency, 
                             :r{$key}io_misc_requests, 
                             :r{$key}io_misc_latency), ";

            $binds['integer_r' . $key . 'file_id'] = $line['file_id'];
            $binds['integer_r' . $key . 'table_id'] = $line['table_id'];
            $binds['r' . $key . 'total_latency'] = $line['total_latency'];
            $binds['r' . $key . 'rows_fetched'] = $line['rows_fetched'];
            $binds['r' . $key . 'fetch_latency'] = $line['fetch_latency'];
            $binds['r' . $key . 'rows_inserted'] = $line['rows_inserted'];
            $binds['r' . $key . 'insert_latency'] = $line['insert_latency'];
            $binds['r' . $key . 'rows_updated'] = $line['rows_updated'];
            $binds['r' . $key . 'update_latency'] = $line['update_latency'];
            $binds['r' . $key . 'rows_deleted'] = $line['rows_deleted'];
            $binds['r' . $key . 'delete_latency'] = $line['delete_latency'];
            $binds['r' . $key . 'io_read_requests'] = $line['io_read_requests'];
            $binds['r' . $key . 'io_read'] = $line['io_read'];
            $binds['r' . $key . 'io_read_latency'] = $line['io_read_latency'];
            $binds['r' . $key . 'io_write_requests'] = $line['io_write_requests'];
            $binds['r' . $key . 'io_write'] = $line['io_write'];
            $binds['r' . $key . 'io_write_latency'] = $line['io_write_latency'];
            $binds['r' . $key . 'io_misc_requests'] = $line['io_misc_requests'];
            $binds['r' . $key . 'io_misc_latency'] = $line['io_misc_latency'];
        }

        $query = substr($query, 0, strrpos($query, ','));

        /** @noinspection UnusedFunctionResultInspection */
        $this->mySQL->write($query, $binds);

    }

    public function getFileTotals($id): TableStatsRow
    {
        $query = 'SELECT * FROM sys_table_stats WHERE file_id = :id ORDER BY row_id';
        $rows = $this->mySQL->all($query, ['id' => $id]);

        $totals = [];
        $totals['total_latency'] = 0;
        $totals['rows_fetched'] = 0;
        $totals['fetch_latency'] = 0;
        $totals['rows_inserted'] = 0;
        $totals['insert_latency'] = 0;
        $totals['rows_updated'] = 0;
        $totals['update_latency'] = 0;
        $totals['rows_deleted'] = 0;
        $totals['delete_latency'] = 0;
        $totals['io_read_requests'] = 0;
        $totals['io_read'] = 0;
        $totals['io_read_latency'] = 0;
        $totals['io_write_requests'] = 0;
        $totals['io_write'] = 0;
        $totals['io_write_latency'] = 0;
        $totals['io_misc_requests'] = 0;
        $totals['io_misc_latency'] = 0;


        foreach ($rows as $row) {
            $totals['total_latency'] = bcadd($totals['total_latency'], $row['total_latency']);
            $totals['rows_fetched'] = bcadd($totals['rows_fetched'], $row['rows_fetched']);
            $totals['fetch_latency'] = bcadd($totals['fetch_latency'], $row['fetch_latency']);
            $totals['rows_inserted'] = bcadd($totals['rows_inserted'], $row['rows_inserted']);
            $totals['insert_latency'] = bcadd($totals['insert_latency'], $row['insert_latency']);
            $totals['rows_updated'] = bcadd($totals['rows_updated'], $row['rows_updated']);
            $totals['update_latency'] = bcadd($totals['update_latency'], $row['update_latency']);
            $totals['rows_deleted'] = bcadd($totals['rows_deleted'], $row['rows_deleted']);
            $totals['delete_latency'] = bcadd($totals['delete_latency'], $row['delete_latency']);
            $totals['io_read_requests'] = bcadd($totals['io_read_requests'], $row['io_read_requests']);
            $totals['io_read'] = bcadd($totals['io_read'], $row['io_read']);
            $totals['io_read_latency'] = bcadd($totals['io_read_latency'], $row['io_read_latency']);
            $totals['io_write_requests'] = bcadd($totals['io_write_requests'], $row['io_write_requests']);
            $totals['io_write'] = bcadd($totals['io_write'], $row['io_write']);
            $totals['io_write_latency'] = bcadd($totals['io_write_latency'], $row['io_write_latency']);
            $totals['io_misc_requests'] = bcadd($totals['io_misc_requests'], $row['io_misc_requests']);
            $totals['io_misc_latency'] = bcadd($totals['io_misc_latency'], $row['io_misc_latency']);
        }

        return new TableStatsRow(
            $totals['total_latency'],
            $totals['rows_fetched'],
            $totals['fetch_latency'],
            $totals['rows_inserted'],
            $totals['insert_latency'],
            $totals['rows_updated'],
            $totals['update_latency'],
            $totals['rows_deleted'],
            $totals['delete_latency'],
            $totals['io_read_requests'],
            $totals['io_read'],
            $totals['io_read_latency'],
            $totals['io_write_requests'],
            $totals['io_write'],
            $totals['io_write_latency'],
            $totals['io_misc_requests'],
            $totals['io_misc_latency']
        );

    }

    public function getTableRow($fileId, $tableId) {
        $query = 'SELECT * FROM sys_table_stats WHERE file_id = :id and table_id = :tid ORDER BY row_id';
        $row = $this->mySQL->oneRow($query, ['id' => $fileId, 'tid'=>$tableId]);

        return new TableStatsRow(
            $row['total_latency'],
            $row['rows_fetched'],
            $row['fetch_latency'],
            $row['rows_inserted'],
            $row['insert_latency'],
            $row['rows_updated'],
            $row['update_latency'],
            $row['rows_deleted'],
            $row['delete_latency'],
            $row['io_read_requests'],
            $row['io_read'],
            $row['io_read_latency'],
            $row['io_write_requests'],
            $row['io_write'],
            $row['io_write_latency'],
            $row['io_misc_requests'],
            $row['io_misc_latency']
        );
    }


}
