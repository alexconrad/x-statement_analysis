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
use Misico\Web\Controller\CompareController;

$firstData = $this->variables['firstData'];
$firstFileUpload = $this->variables['firstFileUpload'];
$compareFilesData = $this->variables['compareFilesData'];
$compareFiles = $this->variables['compareFiles'];


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
        background-color: #C0C0C0;
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
    .lightLink {
        color: lightblue;
    }
    .lightLink:hover {
        color: lightblue;
    }

.table-hover>tbody>tr.no-hover:hover {
    background-color: #454d55;
    color: white;
}

</style>
<?php
    $currentArray = $_GET['se'];
?>


<div class="container-fluid w-100 mt-1">
    <table class="table table-hover table-sm table-bordered">
        <thead class="thead-dark">
        <tr>
            <th scope="col" colspan="2" class="right-border">
            <A class="btn btn-sm btn-success" href="index.php">Back</A>
            </th>
            <th scope="col" colspan="3" class="right-border center-cell"><?php

                $toRight = $currentArray;
                [$toRight[0], $toRight[1]] = [$toRight[1], $toRight[0]];

                $toLast = $currentArray;
                unset($toLast[0]);
                $toLast[] = $currentArray[0];
                $toLast = array_values($toLast);

                $toRightLink = $this->common->link(CompareController::class, 'index', [
                        'se' => array_values($toRight)
                ]);

                $toLastLink = $this->common->link(CompareController::class, 'index', [
                        'se' => $toLast
                ]);



                ?>
                      <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-sm btn-secondary">&nbsp;&nbsp;</button>
                        <button class="btn btn-sm btn-secondary">&nbsp;</button>
                        <a class="btn btn-sm btn-secondary" ><?php echo $firstFileUpload->getDescription(); ?></a>
                        <a class="btn btn-sm btn-secondary" href="<?php echo $toRightLink; ?>">&gt;</a>
                        <a class="btn btn-sm btn-secondary" href="<?php echo $toLastLink; ?>">&gt;&gt;</a>
                        </div>

                <?php

            ?></th>
            <?php foreach ($compareFiles as $compareFile) { ?>
            <th scope="col" colspan="3" class="right-border center-cell text-center"><?php

                $lastOne = false;
                $key = array_search($compareFile->getFileId(), $currentArray, false);
                if ($key >= (count($currentArray)-1)) {
                    $lastOne = true;
                }

                $toFirst = $currentArray;
                unset($toFirst[$key]);
                array_unshift($toFirst, $currentArray[$key]);
                $toFirst = array_values($toFirst);

                $toLeft = $currentArray;
                [$toLeft[$key-1], $toLeft[$key]] = [$toLeft[$key], $toLeft[$key-1]];

                $toRight = null;
                $toLast = null;
                if (false === $lastOne) {
                    $toRight = $currentArray;
                    [$toRight[$key], $toRight[$key + 1]] = [$toRight[$key + 1], $toRight[$key]];

                    $toLast = $currentArray;
                    unset($toLast[$key]);
                    $toLast[] = $currentArray[$key];
                    $toLast = array_values($toLast);
                }

                $toFirstLink = $this->common->link(CompareController::class, 'index', [
                        'se' => $toFirst
                ]);
                $toLeftLink = $this->common->link(CompareController::class, 'index', [
                        'se' => $toLeft
                ]);
                $toRightLink = '#';
                $toLastLink = '#';

                if (false === $lastOne) {
                    $toRightLink = $this->common->link(CompareController::class, 'index', [
                            'se' => $toRight
                    ]);

                    $toLastLink = $this->common->link(CompareController::class, 'index', [
                            'se' => $toLast
                    ]);
                }

                ?>
                      <div class="btn-group btn-group-sm" role="group">
                        <a class="btn btn-sm btn-secondary" href="<?php echo $toFirstLink; ?>">&lt;&lt;</a>
                        <a class="btn btn-sm btn-secondary" href="<?php echo $toLeftLink; ?>">&lt;</a>
                        <a class="btn btn-sm btn-secondary" ><?php echo $compareFile->getDescription(); ?></a>
                        <a class="btn btn-sm btn-secondary" href="<?php echo $toRightLink; ?>"><?php if (false === $lastOne) { echo '&gt;'; }?></a>
                        <a class="btn btn-sm btn-secondary" href="<?php echo $toLastLink; ?>"><?php if (false === $lastOne) { echo '&gt;&gt;'; }?></a>
                        </div>


                <?php



                ?></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>

    <?php
    $this->renderTableStat($firstData, $compareFilesData, CompareController::COMPARE_EVERYTHING);
    $pairs = $this->variables['pairs'] ?? [];
    foreach ($pairs as $tableId => $pair) {
        $this->renderTableStat($pair['first'], $pair['compareTo'] ?? [], $this->variables['tables'][$tableId]);
    }
    ?>
        </tbody>
    </table>
</div>


<?php

$this->render('_foot');
