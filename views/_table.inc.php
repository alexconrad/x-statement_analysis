<?php
/** @var ViewOutput $this */
/** @var TableStatsRow $firstData */
/** @var DbFileUpload $firstFileUpload */
/** @var TableStatsRow[] $compareFilesData */
/** @var DbFileUpload[] $compareFiles */

use Misico\Common\Common;
use Misico\Controller\Output\ViewOutput;
use Misico\Entity\DbFileUpload;
use Misico\Entity\TableStatsRow;
use Misico\Web\Controller\CompareStatementsController;

$firstData = $this->variables['firstData'];
$firstFileUpload = $this->variables['firstFileUpload'];
$compareFilesData = $this->variables['compareFilesData'];
$compareFiles = $this->variables['compareFiles'];

$compareTo = [];
foreach ($compareFiles as $compareFile) {
    $compareTo[] = $compareFile->getFileId();
}
?>

                <tr class="table-dark no-hover bg-dark subSticky">
                    <th scope="col" colspan="2" class="bg-dark right-border">&nbsp;</th>
                    <th scope="col" class="bg-dark center-cell">Rows</th>
                    <th scope="col" class="bg-dark center-cell">Latency (ms)</th>
                    <th scope="col" class="bg-dark right-border center-cell">Rows/ms</th>
                    <?php foreach ($compareFiles as $compareFile) { ?>
                    <th scope="col" class="bg-dark center-cell">Rows</th>
                    <th scope="col" class="bg-dark center-cell">Latency (ms)</th>
                    <th scope="col" class="bg-dark right-border center-cell">Rows/ms</th>
                    <?php } ?>
                </tr>
                    <tr>
                        <th scope="row" rowspan="5" class="w-10">
                            <a href="<?=$this->common->link(CompareStatementsController::class, 'index', [
                                    'first'=> $firstFileUpload->getFileId(),
                                    'compareTo'=> implode(',', $compareTo),
                                    'table' => $this->variables['whatIs']
                            ])?>">
                            <?php
                            echo $this->variables['whatIs'];
                            ?></a></th>
                        <td>Total</td>
                        <td class="number-cell"><?php
                            echo Common::formatRows($firstData->getTotalRows());
                            ?></td>
                        <td class="number-cell"><?php
                            echo Common::formatLatency($firstData->getTotalLatency());
                        ?></td>
                        <td class="number-cell right-border"><?php
                            echo Common::formatAverage($firstData->getTotalAverage());
                        ?></td>
                        <?php foreach ($compareFilesData as $compareFileData) {
                            $diff = $compareFileData->diffTotalAverage($firstData->getTotalAverage());
                            ?>
                        <td class="number-cell"><?php echo Common::formatRows($compareFileData->getTotalRows()); ?></td>
                        <td class="number-cell"><?php echo Common::formatLatency($compareFileData->getTotalLatency());?></td>
                        <td class="number-cell right-border <?=Common::classForPercent($diff)?>"> <?php
                            echo Common::formatAverage($compareFileData->getTotalAverage());
                            echo Common::formatDiffPercentage($diff);
                            ?></td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td>FETCH</td>
                        <td class="number-cell"><?php
                            echo Common::formatRows($firstData->getRowsFetched());
                            echo Common::formatRequestPercentage($firstData->requestPercentFromTotal()['fetch']);
                            ?></td>
                        <td class="number-cell"><?php
                            echo Common::formatLatency($firstData->getFetchLatency());
                            echo Common::formatLatencyPercentage($firstData->latencyPercentFromTotal()['fetch']);
                        ?></td>
                        <td class="number-cell right-border"><?php
                            echo Common::formatAverage($firstData->getFetchAverage());

                        ?></td>
                        <?php foreach ($compareFilesData as $compareFileData) {
                            $diff = $compareFileData->diffFetchAverage($firstData->getFetchAverage());
                            ?>
                        <td class="number-cell"><?php
                            echo Common::formatRows($compareFileData->getRowsFetched());
                            echo Common::formatRequestPercentage($compareFileData->requestPercentFromTotal()['fetch']);
                            ?></td>
                        <td class="number-cell"><?php
                            echo Common::formatLatency($compareFileData->getFetchLatency());
                            echo Common::formatLatencyPercentage($compareFileData->latencyPercentFromTotal()['fetch']);
                            ?></td>
                        <td class="number-cell right-border <?=Common::classForPercent($diff)?>"><?php
                            echo Common::formatAverage($compareFileData->getFetchAverage());
                            echo Common::formatDiffPercentage($diff);
                            ?></td>
                        <?php } ?>
                    </tr>

                    <tr>
                        <td>INSERT</td>
                        <td class="number-cell"><?php
                            echo Common::formatRows($firstData->getRowsInserted());
                            echo Common::formatRequestPercentage($firstData->requestPercentFromTotal()['insert']);

                            ?></td>
                        <td class="number-cell"><?php
                            echo Common::formatLatency($firstData->getInsertLatency());
                            echo Common::formatLatencyPercentage($firstData->latencyPercentFromTotal()['insert']);

                        ?></td>
                        <td class="number-cell right-border"><?php
                            echo Common::formatAverage($firstData->getInsertAverage());
                        ?></td>
                        <?php foreach ($compareFilesData as $compareFileData) {
                            $diff = $compareFileData->diffInsertAverage($firstData->getInsertAverage());
                            ?>
                        <td class="number-cell"><?php
                            echo Common::formatRows($compareFileData->getRowsInserted());
                            echo Common::formatRequestPercentage($compareFileData->requestPercentFromTotal()['insert']);

                            ?></td>
                        <td class="number-cell"><?php
                            echo Common::formatLatency($compareFileData->getInsertLatency());
                            echo Common::formatLatencyPercentage($compareFileData->latencyPercentFromTotal()['insert']);

                            ?></td>
                        <td class="number-cell right-border <?=Common::classForPercent($diff)?>"><?php
                            echo Common::formatAverage($compareFileData->getInsertAverage());
                            echo Common::formatDiffPercentage($diff);
                            ?></td>
                        <?php } ?>
                    </tr>

                    <tr>
                        <td>UPDATE</td>
                        <td class="number-cell"><?php
                            echo Common::formatRows($firstData->getRowsUpdated());
                            echo Common::formatRequestPercentage($firstData->requestPercentFromTotal()['update']);

                            ?></td>
                        <td class="number-cell"><?php
                            echo Common::formatLatency($firstData->getUpdateLatency());
                            echo Common::formatLatencyPercentage($firstData->latencyPercentFromTotal()['update']);

                        ?></td>
                        <td class="number-cell right-border"><?php
                            echo Common::formatAverage($firstData->getUpdateAverage());
                        ?></td>
                        <?php foreach ($compareFilesData as $compareFileData) {
                            $diff = $compareFileData->diffUpdateAverage($firstData->getUpdateAverage());
                        ?>
                        <td class="number-cell"><?php
                            echo Common::formatRows($compareFileData->getRowsUpdated());
                            echo Common::formatRequestPercentage($compareFileData->requestPercentFromTotal()['update']);

                            ?></td>
                        <td class="number-cell"><?php
                            echo Common::formatLatency($compareFileData->getUpdateLatency());
                            echo Common::formatLatencyPercentage($compareFileData->latencyPercentFromTotal()['update']);
                            ?></td>
                        <td class="number-cell right-border <?=Common::classForPercent($diff)?>"><?php
                            echo Common::formatAverage($compareFileData->getUpdateAverage());
                            echo Common::formatDiffPercentage($diff);

                            ?></td>
                        <?php } ?>
                    </tr>


<tr>
                        <td>I/O read</td>
                        <td class="number-cell"><?php
                            echo Common::formatRows($firstData->getIoReadRequests()).'';
                            ?></td>
                        <td class="number-cell"><?php
                            echo Common::formatLatency($firstData->getIoReadLatency());
                        ?></td>
                        <td class="number-cell right-border"><?php
                            if (!empty($firstData->getIoReadLatency())) {
                                $ioRead1 = bcdiv($firstData->getIoReadRequests(), $firstData->getIoReadLatency(), 6);
                                echo Common::formatAverage($ioRead1).' req/ms';
                            }else{
                                $ioRead1 = 0;
                                echo '-';
                            }
                        ?></td>
                        <?php foreach ($compareFilesData as $compareFileData) {
                            if (!empty($compareFileData->getIoReadLatency())) {
                                $ioRead2 = bcdiv($compareFileData->getIoReadRequests(), $compareFileData->getIoReadLatency(), 6);
                                $diff = Common::percentChange($ioRead1, $ioRead2);
                            } else {
                                $ioRead2 = 0;
                                $diff = 0;
                            }

                        ?>
                        <td class="number-cell"><?php
                            echo Common::formatRows($compareFileData->getIoReadRequests()).'';


                            ?></td>
                        <td class="number-cell"><?php
                            echo Common::formatLatency($compareFileData->getIoReadLatency());

                            ?></td>
                        <td class="number-cell right-border <?=Common::classForPercent($diff)?>"><?php
                            echo Common::formatAverage($ioRead2).' req/ms';
                            echo Common::formatDiffPercentage($diff);

                            ?></td>
                        <?php } ?>
                    </tr>
