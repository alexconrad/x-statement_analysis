<?php


namespace Misico\DB\Tables;


use Misico\DB\MySQL;
use Misico\DB\Tables\Exceptions\NotFoundException;
use Misico\Entity\StatementStatsRow;

class SysStatementStatsDao
{
    private MySQL $mySQL;

    public function __construct(MySQL $mySQL)
    {
        $this->mySQL = $mySQL;
    }


    public function multiInsert($lines): void
    {

        $query = 'INSERT INTO sys_statement_stats (
                                 file_id, 
                                 statement_id, 
                                 full_scan,
                                 exec_count, 
                                 err_count, 
                                 warn_count, 
                                 total_latency, 
                                 max_latency, 
                                 avg_latency, 
                                 lock_latency, 
                                 rows_sent, 
                                 rows_sent_avg, 
                                 rows_examined, 
                                 rows_examined_avg, 
                                 rows_affected, 
                                 rows_affected_avg, 
                                 tmp_tables, 
                                 tmp_disk_tables, 
                                 rows_sorted, 
                                 sort_merge_passes, 
                                 first_seen, 
                                 last_seen) VALUES ' . null;

        $binds = [];
        foreach ($lines as $key => $line) {
            $query .= "(     :integer_r{$key}file_id, 
                             :integer_r{$key}statement_id,
                             :r{$key}full_scan,
                             :r{$key}exec_count,
                             :r{$key}err_count,
                             :r{$key}warn_count,
                             :r{$key}total_latency,
                             :r{$key}max_latency,
                             :r{$key}avg_latency,
                             :r{$key}lock_latency,
                             :r{$key}rows_sent,
                             :r{$key}rows_sent_avg,
                             :r{$key}rows_examined,
                             :r{$key}rows_examined_avg,
                             :r{$key}rows_affected,
                             :r{$key}rows_affected_avg,
                             :r{$key}tmp_tables,
                             :r{$key}tmp_disk_tables,
                             :r{$key}rows_sorted,
                             :r{$key}sort_merge_passes,
                             :r{$key}first_seen,
                             :r{$key}last_seen), ";

            $binds['integer_r' . $key . 'file_id'] = $line['file_id'];
            $binds['integer_r' . $key . 'statement_id'] = $line['statement_id'];
            $binds['r' . $key . 'full_scan'] = $line['full_scan'];
            $binds['r' . $key . 'exec_count'] = $line['exec_count'];
            $binds['r' . $key . 'err_count'] = $line['err_count'];
            $binds['r' . $key . 'warn_count'] = $line['warn_count'];
            $binds['r' . $key . 'total_latency'] = $line['total_latency'];
            $binds['r' . $key . 'max_latency'] = $line['max_latency'];
            $binds['r' . $key . 'avg_latency'] = $line['avg_latency'];
            $binds['r' . $key . 'lock_latency'] = $line['lock_latency'];
            $binds['r' . $key . 'rows_sent'] = $line['rows_sent'];
            $binds['r' . $key . 'rows_sent_avg'] = $line['rows_sent_avg'];
            $binds['r' . $key . 'rows_examined'] = $line['rows_examined'];
            $binds['r' . $key . 'rows_examined_avg'] = $line['rows_examined_avg'];
            $binds['r' . $key . 'rows_affected'] = $line['rows_affected'];
            $binds['r' . $key . 'rows_affected_avg'] = $line['rows_affected_avg'];
            $binds['r' . $key . 'tmp_tables'] = $line['tmp_tables'];
            $binds['r' . $key . 'tmp_disk_tables'] = $line['tmp_disk_tables'];
            $binds['r' . $key . 'rows_sorted'] = $line['rows_sorted'];
            $binds['r' . $key . 'sort_merge_passes'] = $line['sort_merge_passes'];
            $binds['r' . $key . 'first_seen'] = $line['first_seen'];
            $binds['r' . $key . 'last_seen'] = $line['last_seen'];
        }

        $query = substr($query, 0, strrpos($query, ','));
        $query .= ' ON DUPLICATE KEY UPDATE 
            exec_count = exec_count + VALUES(exec_count),            
            err_count = err_count + VALUES(err_count),            
            warn_count = warn_count + VALUES(warn_count),            
            total_latency = total_latency + VALUES(total_latency),            
            max_latency = IF (max_latency < VALUES(max_latency), VALUES(max_latency), max_latency) ,            
            avg_latency = IFNULL(FLOOR((total_latency + VALUES(total_latency)) / (exec_count + VALUES(exec_count))), 0),            
            lock_latency = lock_latency + VALUES(lock_latency),            
            rows_sent = rows_sent + VALUES(rows_sent),            
            rows_sent_avg = IFNULL(FLOOR((rows_sent + VALUES(rows_sent)) / (exec_count + VALUES(exec_count))), 0),            
            rows_examined = rows_examined + VALUES(rows_examined),            
            rows_examined_avg = IFNULL(FLOOR((rows_examined + VALUES(rows_examined)) / (exec_count + VALUES(exec_count))), 0),            
            rows_affected = rows_affected + VALUES(rows_affected),            
            rows_affected_avg = IFNULL(FLOOR((rows_affected + VALUES(rows_affected)) / (exec_count + VALUES(exec_count))), 0),
            tmp_tables = tmp_tables + VALUES(tmp_tables),            
            tmp_disk_tables = tmp_disk_tables + VALUES(tmp_disk_tables),            
            rows_sorted = rows_sorted + VALUES(rows_sorted),            
            sort_merge_passes = sort_merge_passes + VALUES(sort_merge_passes),            
            first_seen = IF(first_seen < VALUES(first_seen), first_seen,  VALUES(first_seen) ),            
            last_seen = IF(last_seen < VALUES(last_seen), VALUES(last_seen), last_seen )            
        ';

        /** @noinspection UnusedFunctionResultInspection */
        $this->mySQL->write($query, $binds);
    }

    /**
     * @return StatementStatsRow[]
     */
    public function getStatementStatsByFileId($fileId)
    {
        $query = 'SELECT * FROM sys_statement_stats WHERE file_id = :id ORDER BY total_latency DESC';
        return $this->mySQL->all($query, ['id' => $fileId]);

    }

    public function getStatements(array $ids): array
    {
        $query = 'SELECT statement_id,statement FROM file_statements WHERE statement_id IN (' . null;
        $binds = [];
        foreach ($ids as $nr => $id) {
            $query .= ':integer_' . $nr . ',';
            $binds['integer_' . $nr] = $id;
        }

        $query = substr($query, 0, strrpos($query, ','));
        $query .= ')';

        return $this->mySQL->assoc($query, $binds);
    }


}
