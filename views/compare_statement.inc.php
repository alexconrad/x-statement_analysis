<?php
/** @var ViewOutput $this */
/** @var StatementStatsRow[] $firstStatements */
/** @var DbFileUpload $firstFileUpload */
/** @var DbFileUpload[] $compareStatements */
/** @var StatementStatsRow[][] $compareStatementsData */
/** @var DbFileUpload[] $compareFiles */

/** @var string[] $statements */

use Misico\Common\Common;
use Misico\Controller\Output\ViewOutput;
use Misico\Entity\DbFileUpload;
use Misico\Entity\StatementStatsRow;
use Misico\Web\Controller\CompareController;
use Misico\Web\Controller\CompareStatementsController;



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

        .na {
            background-color: #c0c0c0;
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
<?php

$currentCompareTo = explode(',', $_GET['compareTo']);
$currentFirst = $_GET['first'];
$currentTable = $_GET['table'];

?>

<style>

table {
  text-align: left;
  position: relative;
  border-collapse: collapse;
}
th, td {
  padding: 0.25rem;
}
tr.red th {
  background: red;
  color: white;
}
tr.green th {
  background: green;
  color: white;
}
tr.purple th {
  background: purple;
  color: white;
}
th.stickTh {
  background: white;
  position: sticky;
  top: 0; /* Don't forget this, required for the stickiness */
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
}



</style>

    <div class="container-fluid w-100 mt-1">
        <table class="table table-hover table-sm table-bordered">
            <thead class="thead-dark">
            <tr class="red">
                <th scope="col" class="right-border stickTh">
                    <?php
                        $backLink = $this->common->link(CompareController::class, 'index', [
                                'se' => [
                                        $currentFirst,
                                        ...$currentCompareTo
                                ]
                        ]);

                    ?>
                    <A class="btn btn-sm btn-success" href="<?php echo $backLink; ?>">Back to table compare</A>

                    </th>
                <th scope="col" class="right-border center-cell stickTh">
                    <?php
                        $setFirst = $currentCompareTo[0];
                        $setFirstCompareTo = $currentCompareTo;
                        $setFirstCompareTo[0] = $currentFirst;
                        $toRightLink = $this->common->link(CompareStatementsController::class, 'index', [
                                'first' => $setFirst,
                                'compareTo' => implode(',', $setFirstCompareTo),
                                'table' => $currentTable
                        ]);

                        $setFirst = $currentCompareTo[0];
                        $setFirstCompareTo = $currentCompareTo;
                        unset($setFirstCompareTo[0]);
                        $setFirstCompareTo[] = $currentFirst;
                        $setFirstCompareTo = array_values($setFirstCompareTo);

                        $toLastLink = $this->common->link(CompareStatementsController::class, 'index', [
                                'first' => $setFirst,
                                'compareTo' => implode(',', $setFirstCompareTo),
                                'table' => $currentTable
                        ]);

                    ?>
                      <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-sm btn-primary">&nbsp;&nbsp;</button>
                        <button class="btn btn-sm btn-primary">&nbsp;</button>
                        <a class="btn btn-sm btn-primary" ><?php echo $firstFileUpload->getDescription(); ?></a>
                        <a class="btn btn-sm btn-primary" href="<?php echo $toRightLink; ?>">&gt;</a>
                        <a class="btn btn-sm btn-primary" href="<?php echo $toLastLink; ?>">&gt;&gt;</a>
                        </div>
                    <?php
                    ?>
                </th>
                <?php foreach ($compareStatements as $compareFile) { ?>
                    <?php
                        $key = array_search($compareFile->getFileId(), $currentCompareTo, false);
                        $lastOne = false;
                        if ($key >= (count($currentCompareTo)-1)) {
                            $lastOne = true;
                        }
                        $firstOne = false;
                        if ($key === 0) {
                            $firstOne = true;
                        }

                        if (true === $firstOne) {
                            $setFirst = $currentCompareTo[$key];
                            $setFirstCompareTo = $currentCompareTo;
                            unset($setFirstCompareTo[$key]);
                            array_unshift($setFirstCompareTo, $currentFirst);
                            $setFirstCompareTo = array_values($setFirstCompareTo);
                            $toLeftLink = $this->common->link(CompareStatementsController::class, 'index', [
                                'first' => $setFirst,
                                'compareTo' => implode(',', $setFirstCompareTo),
                                'table' => $currentTable
                            ]);
                        }else{
                            $setFirst = $currentFirst;
                            $setFirstCompareTo = $currentCompareTo;
                            [$setFirstCompareTo[$key-1], $setFirstCompareTo[$key]] = [$setFirstCompareTo[$key], $setFirstCompareTo[$key-1]];
                            $setFirstCompareTo = array_values($setFirstCompareTo);
                            $toLeftLink = $this->common->link(CompareStatementsController::class, 'index', [
                                'first' => $setFirst,
                                'compareTo' => implode(',', $setFirstCompareTo),
                                'table' => $currentTable
                            ]);
                        }

                        $setFirst = $currentCompareTo[$key];
                        $setFirstCompareTo = $currentCompareTo;
                        unset($setFirstCompareTo[$key]);
                        array_unshift($setFirstCompareTo, $currentFirst);
                        $setFirstCompareTo = array_values($setFirstCompareTo);
                        $toFirstLink = $this->common->link(CompareStatementsController::class, 'index', [
                            'first' => $setFirst,
                            'compareTo' => implode(',', $setFirstCompareTo),
                            'table' => $currentTable
                        ]);





                        $toRightLink = null;
                        $toLastLink = null;

                        if (false === $lastOne) {

                            $setFirst = $currentFirst;
                            $setFirstCompareTo = $currentCompareTo;
                            [$setFirstCompareTo[$key], $setFirstCompareTo[$key+1]] = [$setFirstCompareTo[$key+1], $setFirstCompareTo[$key]];
                            $setFirstCompareTo = array_values($setFirstCompareTo);
                            $toRightLink = $this->common->link(CompareStatementsController::class, 'index', [
                                'first' => $setFirst,
                                'compareTo' => implode(',', $setFirstCompareTo),
                                'table' => $currentTable
                            ]);

                            $setFirst = $currentFirst;
                            $setFirstCompareTo = $currentCompareTo;
                            $lastKey = count($currentCompareTo) - 1;
                            [$setFirstCompareTo[$key], $setFirstCompareTo[$lastKey]] = [$setFirstCompareTo[$lastKey], $setFirstCompareTo[$key]];
                            $setFirstCompareTo = array_values($setFirstCompareTo);

                            $toLastLink = $this->common->link(CompareStatementsController::class, 'index', [
                                'first' => $setFirst,
                                'compareTo' => implode(',', $setFirstCompareTo),
                                'table' => $currentTable
                            ]);
                        }



                    ?>
                    <th scope="col" class="right-border center-cell stickTh">

                      <div class="btn-group btn-group-sm" role="group">
                        <a class="btn btn-sm btn-secondary" href="<?php echo $toFirstLink; ?>">&lt;&lt;</a>
                        <a class="btn btn-sm btn-secondary" href="<?php echo $toLeftLink; ?>">&lt;</a>
                        <a class="btn btn-sm btn-secondary" ><?php echo $compareFile->getDescription(); ?></a>
                        <a class="btn btn-sm btn-secondary" href="<?php echo $toRightLink; ?>"><?php if (false === $lastOne) { echo '&gt;'; }?></a>
                        <a class="btn btn-sm btn-secondary" href="<?php echo $toLastLink; ?>"><?php if (false === $lastOne) { echo '&gt;&gt;'; }?></a>
                        </div>



                    </th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>

            <?php foreach ($firstStatements as $statement) { ?>


                <tr class="table-dark no-hover bg-dark">
                    <th colspan="<?=2+count($compareStatementsData)?>" class="bg-dark subth">
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
