<?php


namespace Misico\Service;


use Misico\DB\Tables\Exceptions\NotFoundException;
use Misico\DB\Tables\SysStatementStatsDao;
use Misico\Entity\StatementStatsRow;

class StatementService
{
    /**
     * @var SysStatementStatsDao
     */
    private SysStatementStatsDao $sysStatementStatsDao;

    public function __construct(SysStatementStatsDao $sysStatementStatsDao)
    {
        $this->sysStatementStatsDao = $sysStatementStatsDao;
    }

    /**
     * @param $fileId
     * @return StatementStatsRow[]
     * @throws NotFoundException
     */
    public function getStatementsFromFileId($fileId): array
    {
        $rows = $this->sysStatementStatsDao->getStatementStatsByFileId($fileId);
        $ret = [];
        foreach ($rows as $row) {
            $ret[$row['statement_id']] = $this->buildFromRow($row);
        }

        return $ret;
    }

    /**
     * @param StatementStatsRow[] $array
     * @return int[]
     */
    public function getStatementIds($array): array
    {
        $ret = [];
        foreach ($array as $obj) {
            $ret[] = $obj->getStatementId();
        }
        return $ret;
    }

    public function getStatements(array $ids): array {
        return $this->sysStatementStatsDao->getStatements($ids);
    }

/**
     * @param $row
     * @return StatementStatsRow
     * @throws NotFoundException
     */
    private function buildFromRow($row): StatementStatsRow
    {

        if (!is_array($row)) {
            throw new NotFoundException('cannot build object');
        }

        return new StatementStatsRow(
            $row['statement_id'],
            $row['full_scan'],
            $row['exec_count'],
            $row['err_count'],
            $row['warn_count'],
            $row['total_latency'],
            $row['max_latency'],
            $row['avg_latency'],
            $row['lock_latency'],
            $row['rows_sent'],
            $row['rows_sent_avg'],
            $row['rows_examined'],
            $row['rows_examined_avg'],
            $row['rows_affected'],
            $row['rows_affected_avg'],
            $row['tmp_tables'],
            $row['tmp_disk_tables'],
            $row['rows_sorted'],
            $row['sort_merge_passes'],
            $row['first_seen'],
            $row['last_seen']
        );
    }

}
