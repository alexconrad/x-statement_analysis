<?php


namespace Misico\Entity;


class StatementStatsRow
{
    private int $statement_id;
    private $full_scan;
    private $exec_count;
    private $err_count;
    private $warn_count;
    private $total_latency;
    private $max_latency;
    private $avg_latency;
    private $lock_latency;
    private $rows_sent;
    private $rows_sent_avg;
    private $rows_examined;
    private $rows_examined_avg;
    private $rows_affected;
    private $rows_affected_avg;
    private $tmp_tables;
    private $tmp_disk_tables;
    private $rows_sorted;
    private $sort_merge_passes;
    private $first_seen;
    private $last_seen;

    public function __construct($statement_id,
                                $full_scan,
                                $exec_count,
                                $err_count,
                                $warn_count,
                                $total_latency,
                                $max_latency,
                                $avg_latency,
                                $lock_latency,
                                $rows_sent,
                                $rows_sent_avg,
                                $rows_examined,
                                $rows_examined_avg,
                                $rows_affected,
                                $rows_affected_avg,
                                $tmp_tables,
                                $tmp_disk_tables,
                                $rows_sorted,
                                $sort_merge_passes,
                                $first_seen,
                                $last_seen)
    {

        $this->statement_id = (int)$statement_id;
        $this->full_scan = $full_scan;
        $this->exec_count = $exec_count;
        $this->err_count = $err_count;
        $this->warn_count = $warn_count;
        $this->total_latency = $total_latency;
        $this->max_latency = $max_latency;
        $this->avg_latency = $avg_latency;
        $this->lock_latency = $lock_latency;
        $this->rows_sent = $rows_sent;
        $this->rows_sent_avg = $rows_sent_avg;
        $this->rows_examined = $rows_examined;
        $this->rows_examined_avg = $rows_examined_avg;
        $this->rows_affected = $rows_affected;
        $this->rows_affected_avg = $rows_affected_avg;
        $this->tmp_tables = $tmp_tables;
        $this->tmp_disk_tables = $tmp_disk_tables;
        $this->rows_sorted = $rows_sorted;
        $this->sort_merge_passes = $sort_merge_passes;
        $this->first_seen = $first_seen;
        $this->last_seen = $last_seen;
    }

    /**
     * @return mixed
     */
    public function getStatementId():int
   {
        return $this->statement_id;
    }

    /**
     * @return mixed
     */
    public function getFullScan()
    {
        return $this->full_scan;
    }

    /**
     * @return mixed
     */
    public function getExecCount()
    {
        return $this->exec_count;
    }

    /**
     * @return mixed
     */
    public function getErrCount()
    {
        return $this->err_count;
    }

    /**
     * @return mixed
     */
    public function getWarnCount()
    {
        return $this->warn_count;
    }

    /**
     * @return mixed
     */
    public function getTotalLatency()
    {
        return $this->total_latency;
    }

    /**
     * @return mixed
     */
    public function getMaxLatency()
    {
        return $this->max_latency;
    }

    /**
     * @return mixed
     */
    public function getAvgLatency()
    {
        return $this->avg_latency;
    }

    /**
     * @return mixed
     */
    public function getLockLatency()
    {
        return $this->lock_latency;
    }

    /**
     * @return mixed
     */
    public function getRowsSent()
    {
        return $this->rows_sent;
    }

    /**
     * @return mixed
     */
    public function getRowsSentAvg()
    {
        return $this->rows_sent_avg;
    }

    /**
     * @return mixed
     */
    public function getRowsExamined()
    {
        return $this->rows_examined;
    }

    /**
     * @return mixed
     */
    public function getRowsExaminedAvg()
    {
        return $this->rows_examined_avg;
    }

    /**
     * @return mixed
     */
    public function getRowsAffected()
    {
        return $this->rows_affected;
    }

    /**
     * @return mixed
     */
    public function getRowsAffectedAvg()
    {
        return $this->rows_affected_avg;
    }

    /**
     * @return mixed
     */
    public function getTmpTables()
    {
        return $this->tmp_tables;
    }

    /**
     * @return mixed
     */
    public function getTmpDiskTables()
    {
        return $this->tmp_disk_tables;
    }

    /**
     * @return mixed
     */
    public function getRowsSorted()
    {
        return $this->rows_sorted;
    }

    /**
     * @return mixed
     */
    public function getSortMergePasses()
    {
        return $this->sort_merge_passes;
    }

    /**
     * @return mixed
     */
    public function getFirstSeen()
    {
        return $this->first_seen;
    }

    /**
     * @return mixed
     */
    public function getLastSeen()
    {
        return $this->last_seen;
    }



}
