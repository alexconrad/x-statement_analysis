<?php
/** @var ViewOutput $this */
/** @var StatementStatsRow[] $firstStatements */
/** @var DbFileUpload $firstFileUpload */
/** @var StatementStatsRow[][] $compareStatementsData */
/** @var DbFileUpload[] $compareFiles */

/** @var string[] $statements */

use Misico\Common\Common;
use Misico\Controller\Output\ViewOutput;
use Misico\Entity\DbFileUpload;
use Misico\Entity\StatementStatsRow;

$firstFileUpload = $this->variables['firstFileUpload'];
$statements = $this->variables['statements'];
$firstStatements = $this->variables['firstStatements'];
$compareStatements = $this->variables['compareStatements'];
$compareStatementsData = $this->variables['compareStatementsData'];


$this->render('_head');
$this->render('_menu');

if (isset($_GET['uploaded'])) { ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Holy guacamole!</strong> You uploaded <?= $_GET['uploaded'] ?> ok !
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php } ?>

    <style>
        .right-border {
            border-right: 2px solid #909090 !important;
        }

        .center-cell {
            text-align: center;
        }

        .number-cell {
            text-align: right;
        }

        .same {
            background-color: #fafafa;
        }

        .bad-bad {
            background-color: #FFDDDD;
        }

        .bad {
            background-color: #FFCCCC;
        }

        .good {
            background-color: #DDFFDD;
        }

        .good-good {
            background-color: #CCFFCC;
        }

        .table-hover > tbody > tr.no-hover:hover {
            background-color: #454d55;
            color: white;
        }

    </style>


    <div class="container-fluid w-100 mt-1">
        <table class="table table-hover table-sm table-bordered">
            <thead class="thead-dark">
            <tr>
                <th scope="col" class="right-border">&nbsp;</th>
                <th scope="col" class="right-border center-cell"><?php echo $firstFileUpload->getDescription(); ?></th>
                <?php foreach ($compareStatements as $compareFile) { ?>
                    <th scope="col" class="right-border center-cell"><?php echo $compareFile->getDescription(); ?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>

            <?php foreach ($firstStatements as $statement) { ?>


                <tr class="table-dark no-hover bg-dark">
                    <th colspan="<?=2+count($compareStatementsData)?>" class="bg-dark">
                        <?php
                        echo $statements[$statement->getStatementId()];

                        ?>
                    </th>
                </tr>


                <tr>
                    <td>Exec count</td>
                    <td class="number-cell"><?php
                        echo Common::formatRows($statement->getExecCount());
                        ?></td>
                    <?php
                    foreach ($compareStatementsData as $fileId => $compareStatementsDatas) {
                        if (isset($compareStatementsDatas[$statement->getStatementId()])) {
                            $compareStatementsDatum = $compareStatementsDatas[$statement->getStatementId()];
                            $diff = Common::percentChange($statement->getExecCount(), $compareStatementsDatum->getExecCount());
                            $class = Common::classForPercent($diff);

                            ?>
                            <td class="number-cell right-border <?=$class?>"><?php
                                echo Common::formatRows($compareStatementsDatum->getExecCount());
                                echo Common::formatDiffPercentage($diff);
                                ?></td>
                        <?php } else {
                            echo '<td class="number-cell right-border">-</td>';
                        } ?>

                    <?php } ?>
                </tr>

                <tr>
                    <td>Total Latency (ms)</td>
                    <td class="number-cell"><?php
                        echo Common::formatLatency($statement->getTotalLatency());
                        ?></td>
                    <?php
                    foreach ($compareStatementsData as $fileId => $compareStatementsDatas) {
                        if (isset($compareStatementsDatas[$statement->getStatementId()])) {

                            $compareStatementsDatum = $compareStatementsDatas[$statement->getStatementId()];
                            $diff = Common::percentChange($statement->getTotalLatency(), $compareStatementsDatum->getTotalLatency());
                            $class = Common::classForPercent($diff);
                            ?>
                            <td class="number-cell right-border <?=$class?>"><?php
                                echo Common::formatLatency($compareStatementsDatum->getTotalLatency());
                                echo Common::formatDiffPercentage($diff);


                                ?></td>
                        <?php } else {
                            echo '<td class="number-cell right-border">-</td>';
                        } ?>
                    <?php } ?>
                </tr>

                <tr>
                    <td>Average Latency (ms)</td>
                    <td class="number-cell"><?php
                        $averageLatency1 = $statement->getTotalLatency() / $statement->getExecCount();
                        echo Common::formatLatency($averageLatency1, 2).' ms/query';

                        ?>
                    </td>
                    <?php
                    foreach ($compareStatementsData as $fileId => $compareStatementsDatas) {
                        if (isset($compareStatementsDatas[$statement->getStatementId()])) {

                        $compareStatementsDatum = $compareStatementsDatas[$statement->getStatementId()];
                        $averageLatency = $compareStatementsDatum->getTotalLatency() / $compareStatementsDatum->getExecCount();

                        $diff = Common::percentChange($averageLatency1, $averageLatency);
                        $class = Common::classForPercent($diff);
                        ?>
                        <td class="number-cell right-border <?=$class?>"><?php
                            echo Common::formatLatency($averageLatency, 2).' ms/query';
                            echo Common::formatDiffPercentage($diff);
                            ?></td>
                        <?php } else {
                            echo '<td class="number-cell right-border">-</td>';
                        } ?>
                    <?php } ?>
                </tr>

                <tr>
                    <td>Lock Latency (ms)</td>
                    <td class="number-cell"><?php
                        echo Common::formatLatency($statement->getLockLatency());
                        ?>
                    </td>
                    <?php
                    foreach ($compareStatementsData as $fileId => $compareStatementsDatas) {
                        if (isset($compareStatementsDatas[$statement->getStatementId()])) {
                        $compareStatementsDatum = $compareStatementsDatas[$statement->getStatementId()];
                        $diff = Common::percentChange($statement->getLockLatency(), $compareStatementsDatum->getLockLatency());
                        $class = Common::classForPercent($diff);

                        ?>
                        <td class="number-cell right-border <?=$class?>"><?php
                            echo Common::formatLatency($compareStatementsDatum->getLockLatency());
                            echo Common::formatDiffPercentage($diff);
                            ?></td>
                        <?php } else {
                            echo '<td class="number-cell right-border">-</td>';
                        } ?>
                    <?php } ?>
                </tr>

                <tr>
                    <td>Lock Latency/ExecCount (ms)</td>
                    <td class="number-cell"><?php
                        $lockAvg1 = (round($statement->getLockLatency() / $statement->getExecCount(), 2));
                        echo Common::formatLatency($lockAvg1, 2);
                        ?>
                    </td>
                    <?php
                    foreach ($compareStatementsData as $fileId => $compareStatementsDatas) {
                        if (isset($compareStatementsDatas[$statement->getStatementId()])) {



                        $compareStatementsDatum = $compareStatementsDatas[$statement->getStatementId()];
                        $lockAvg = (round($compareStatementsDatum->getLockLatency() / $compareStatementsDatum->getExecCount(), 2));
                        $diff = Common::percentChange($lockAvg1, $lockAvg);
                        $class = Common::classForPercent($diff);
                        ?>
                        <td class="number-cell right-border <?=$class?>"><?php
                            echo Common::formatLatency($lockAvg, 2);
                            echo Common::formatDiffPercentage($diff);
                            //echo '<br>'.$diff.'---'.$lockAvg1.'---'.$lockAvg;
                            ?></td>
                        <?php } else {
                            echo '<td class="number-cell right-border">-</td>';
                        } ?>
                    <?php } ?>
                </tr>
            <?php } ?>







            <?php
            //$this->renderTableStat($firstData, $compareFilesData, CompareController::COMPARE_EVERYTHING);
            //$pairs = $this->variables['pairs'] ?? [];
            //foreach ($pairs as $tableId => $pair) {
            //    $this->renderStatementStat($pair['first'], $pair['compareTo'] ?? [], $this->variables['tables'][$tableId]);
            //}
            ?>
            </tbody>
        </table>
    </div>


<?php

$this->render('_foot');
