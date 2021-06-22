<?php


namespace Misico\Entity;


class TableStatsRow
{
    private const SCALE = 6;
    private const DIFF_SCALE_CALC = 6;
    private const DIFF_SCALE_SHOW = 2;

    private $total_latency;
    private $rows_fetched;
    public $fetch_latency;
    private $rows_inserted;
    private $insert_latency;
    private $rows_updated;
    private $update_latency;
    private $rows_deleted;
    private $delete_latency;
    private $io_read_requests;
    private $io_read;
    private $io_read_latency;
    private $io_write_requests;
    private $io_write;
    private $io_write_latency;
    private $io_misc_requests;
    private $io_misc_latency;

    private $cache;

    public function __construct(
        $total_latency,
        $rows_fetched,
        $fetch_latency,
        $rows_inserted,
        $insert_latency,
        $rows_updated,
        $update_latency,
        $rows_deleted,
        $delete_latency,
        $io_read_requests,
        $io_read,
        $io_read_latency,
        $io_write_requests,
        $io_write,
        $io_write_latency,
        $io_misc_requests,
        $io_misc_latency
    )
    {

        $this->total_latency = $total_latency;
        $this->rows_fetched = $rows_fetched;
        $this->fetch_latency = $fetch_latency;
        $this->rows_inserted = $rows_inserted;
        $this->insert_latency = $insert_latency;
        $this->rows_updated = $rows_updated;
        $this->update_latency = $update_latency;
        $this->rows_deleted = $rows_deleted;
        $this->delete_latency = $delete_latency;
        $this->io_read_requests = $io_read_requests;
        $this->io_read = $io_read;
        $this->io_read_latency = $io_read_latency;
        $this->io_write_requests = $io_write_requests;
        $this->io_write = $io_write;
        $this->io_write_latency = $io_write_latency;
        $this->io_misc_requests = $io_misc_requests;
        $this->io_misc_latency = $io_misc_latency;
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
    public function getRowsFetched()
    {
        return $this->rows_fetched;
    }

    /**
     * @return mixed
     */
    public function getFetchLatency()
    {
        return $this->fetch_latency;
    }

    /**
     * @return mixed
     */
    public function getRowsInserted()
    {
        return $this->rows_inserted;
    }

    /**
     * @return mixed
     */
    public function getInsertLatency()
    {
        return $this->insert_latency;
    }

    /**
     * @return mixed
     */
    public function getRowsUpdated()
    {
        return $this->rows_updated;
    }

    /**
     * @return mixed
     */
    public function getUpdateLatency()
    {
        return $this->update_latency;
    }

    /**
     * @return mixed
     */
    public function getRowsDeleted()
    {
        return $this->rows_deleted;
    }

    /**
     * @return mixed
     */
    public function getDeleteLatency()
    {
        return $this->delete_latency;
    }

    /**
     * @return mixed
     */
    public function getIoReadRequests()
    {
        return $this->io_read_requests;
    }

    /**
     * @return mixed
     */
    public function getIoRead()
    {
        return $this->io_read;
    }

    /**
     * @return mixed
     */
    public function getIoReadLatency()
    {
        return $this->io_read_latency;
    }

    /**
     * @return mixed
     */
    public function getIoWriteRequests()
    {
        return $this->io_write_requests;
    }

    /**
     * @return mixed
     */
    public function getIoWrite()
    {
        return $this->io_write;
    }

    /**
     * @return mixed
     */
    public function getIoWriteLatency()
    {
        return $this->io_write_latency;
    }

    /**
     * @return mixed
     */
    public function getIoMiscRequests()
    {
        return $this->io_misc_requests;
    }

    /**
     * @return mixed
     */
    public function getIoMiscLatency()
    {
        return $this->io_misc_latency;
    }

    public function getFetchAverage()
    {

        if (!isset($this->cache['fetchAverage'])) {
            $this->cache['fetchAverage'] = 'n/a';
            if ($this->fetch_latency > 0) {
                $wtf = bcdiv($this->rows_fetched, $this->fetch_latency, self::SCALE);
                $this->cache['fetchAverage'] = $wtf;
            }
        }
        return $this->cache['fetchAverage'];
    }

    public function getInsertAverage()
    {
        if ($this->insert_latency == 0) {
            return null;
        }

        if (!isset($this->cache['insertAverage'])) {
            $this->cache['insertAverage'] = bcdiv($this->rows_inserted, $this->insert_latency, self::SCALE);
        }
        return $this->cache['insertAverage'];
    }

    public function getUpdateAverage()
    {
        if ($this->insert_latency == 0) {
            return null;
        }

        if (!isset($this->cache['updateAverage'])) {
            $this->cache['updateAverage'] = bcdiv($this->rows_updated, $this->insert_latency, self::SCALE);
        }
        return $this->cache['updateAverage'];
    }

    public function getTotalRows()
    {
        if (!isset($this->cache['totalRows'])) {
            $this->cache['totalRows'] = bcadd(bcadd($this->getRowsFetched(), $this->getRowsInserted()), $this->getRowsUpdated());
        }
        return $this->cache['totalRows'];
    }

    public function getTotalAverage()
    {
        if (!isset($this->cache['totalAverage'])) {
            //echo $this->getTotalRows().", ".$this->getTotalLatency()."<br>\n";
            $this->cache['totalAverage'] = 'n/a';
            if ($this->getTotalLatency() > 0) {
                $this->cache['totalAverage'] = bcdiv($this->getTotalRows(), $this->getTotalLatency(), self::SCALE);
            }
        }
        return $this->cache['totalAverage'];
    }

    public function diffTotalAverage($with)
    {
        return $this->diff($with, $this->getTotalAverage());
    }

    public function diffFetchAverage($with)
    {
        return $this->diff($with, $this->getFetchAverage());
    }

    public function diffInsertAverage($with)
    {
        return $this->diff($with, $this->getInsertAverage());
    }

    public function diffUpdateAverage($with)
    {
        return $this->diff($with, $this->getUpdateAverage());
    }

    private function diff($with, $thisValue)
    {
        if ($with === 'n/a') {
            return 'n/a';
        }
        if ($thisValue === 'n/a') {
            return 'n/a';
        }

        /** @noinspection TypeUnsafeComparisonInspection */
        if ($thisValue == $with) {
            return 0;
        }

        $positive = true;
        if ($with < $thisValue) {
            if ($with == 0) {
                return null;
            }
            $ratio = bcdiv($thisValue, $with, self::DIFF_SCALE_CALC);
        } else {
            if ($thisValue == 0) {
                return null;
            }
            $ratio = bcdiv($with, $thisValue, self::DIFF_SCALE_CALC);
            $positive = false;
        }

        $ratio = bcsub($ratio, '1', self::DIFF_SCALE_CALC);
        $ratio = bcmul($ratio, '100', self::DIFF_SCALE_CALC);

        if ($positive) {
            return bcmul($ratio, '1', self::DIFF_SCALE_SHOW);
        }

        return bcmul($ratio, '-1', self::DIFF_SCALE_SHOW);
    }

    public function requestPercentFromTotal(): array
    {
        if (!isset($this->cache['percentFromTotal'])) {
            try {
                $this->cache['percentFromTotal'] = [
                    'fetch' => bcmul(bcdiv($this->getRowsFetched(), $this->getTotalRows(), 6), 100, 1),
                    'insert' => bcmul(bcdiv($this->getRowsInserted(), $this->getTotalRows(), 6), 100, 1),
                    'update' => bcmul(bcdiv($this->getRowsUpdated(), $this->getTotalRows(), 6), 100, 1),
                ];
            } catch (\DivisionByZeroError $e) {
                $this->cache['percentFromTotal'] = [
                    'fetch' => 'n/a',
                    'insert' => 'n/a',
                    'update' => 'n/a',
                ];

            }
        }
        return $this->cache['percentFromTotal'];
    }

    public function latencyPercentFromTotal(): array
    {
        if (!isset($this->cache['latencyPercentFromTotal'])) {
            $this->cache['latencyPercentFromTotal'] = [
                'fetch' => 'n/a',
                'insert' => 'n/a',
                'update' => 'n/a',
            ];
            if ($this->getTotalLatency() > 0) {
                $this->cache['latencyPercentFromTotal'] = [
                    'fetch' => bcmul(bcdiv($this->getFetchLatency(), $this->getTotalLatency(), 6), 100, 1),
                    'insert' => bcmul(bcdiv($this->getInsertLatency(), $this->getTotalLatency(), 6), 100, 1),
                    'update' => bcmul(bcdiv($this->getUpdateLatency(), $this->getTotalLatency(), 6), 100, 1),
                ];
            }
        }
        return $this->cache['latencyPercentFromTotal'];
    }

}
