<?php
/** @var ViewOutput $this */

use Misico\Controller\Output\ViewOutput;
use Misico\Web\Controller\CompareController;
use Misico\Web\Controller\HomePageController;

$this->render('_head');
$this->render('_menu');

if (isset($_GET['uploaded'])) { ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Holy guacamole!</strong> You uploaded <?= htmlspecialchars($_GET['uploaded'], ENT_QUOTES | ENT_HTML5) ?> ok !
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php } ?>

    <div class="card w-100">
        <div class="card-header">
            Upload MySQL profiler CSV file
        </div>
        <div class="card-body">
            <form action="<?php echo $this->common->link(HomePageController::class, 'upload'); ?>" method="post"
                  enctype="multipart/form-data">
                <div class="form-group">
                    <label for="exampleFormControlInput3"></label>
                    <input type="text" name="table_schema" class="form-control" id="exampleFormControlInput3"
                           placeholder="Table schema to parse" maxlength="120" required value="epay_ro_anonymized">
                </div>
                <div class="form-group">
                    <label for="exampleFormControlFile1">Upload x$schema_table_statistics CSV</label>
                    <input type="file" name="table_file" class="form-control-file" id="exampleFormControlFile2" required>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlFile1">Upload +
                        6x5$statement_analysis CSV</label>
                    <input type="file" name="statement_file" class="form-control-file" id="exampleFormControlFile2" required>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlInput1"></label>
                    <input type="text" name="descr" class="form-control" id="exampleFormControlInput1"
                           placeholder="Description (required, max 22 chars)" maxlength="22" required>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Upload file</button>
            </form>

        </div>
    </div>
    <form action="<?=$this->common->bootstrapFile()?>" method="get">
        <?=$this->common->hiddenParams(CompareController::class, 'index')?>
        <div class="container-fluid w-100 mt-1">
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">x$schema_table_statistics</th>
                    <th scope="col">x$statement_analysis</th>
                    <th scope="col">Description</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($this->variables['uploads'] as $cnt => $upload) { ?>

                    <tr>
                        <th scope="row"><?= ($cnt+1) ?></th>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="se[]"
                                       value="<?= $upload['file_id'] ?>" id="defaultCheck<?= $cnt ?>">
                                <label class="form-check-label" for="defaultCheck<?= $cnt ?>">
                                    <?= $upload['file_name'] ?>
                                </label>
                            </div>
                        </td>
                        <td><?= $upload['file_name_statement'] ?></td>
                        <td><?= $upload['description'] ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="container-fluid w-100 mt-1">
            <button type="submit" class="btn btn-primary mb-2">Compare</button>
        </div>

    </form>
<?php


$this->render('_foot');
