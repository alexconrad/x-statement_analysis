<?php


namespace Misico\DB\Tables;


use Misico\DB\MySQL;

class SysTableStatsDao
{
    /**
     * @var MySQL
     */
    private $mySQL;

    public function __construct(MySQL $mySQL)
    {
        $this->mySQL = $mySQL;
    }


    public function multiInsert($lines)
    {

        $query = "INSERT INTO sys_table_stats (
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
                             io_misc_latency) VALUES " . null;

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

}