<?php
/** @var ViewOutput $this */

use Misico\Controller\Output\ViewOutput;
use Misico\Web\Controller\CompareController;
use Misico\Web\Controller\HomePageController;

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

    <div class="card w-100">
        <div class="card-header">
            Upload MySQL profiler CSV file
        </div>
        <div class="card-body">
            <form action="<?php echo $this->common->link(HomePageController::class, 'upload'); ?>" method="post"
                  enctype="multipart/form-data">
                <div class="form-group">
                    <label for="exampleFormControlFile1">Table CSV</label>
                    <input type="file" name="table_file" class="form-control-file" id="exampleFormControlFile2" required>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlInput1"></label>
                    <input type="text" name="descr" class="form-control" id="exampleFormControlInput1"
                           placeholder="Description (optional)" maxlength="120">
                </div>
                <button type="submit" class="btn btn-primary mb-2">Upload file</button>
            </form>

        </div>
    </div>
    <form action="<?php echo $this->common->link(CompareController::class, 'index'); ?>" method="post">
        <div class="container-fluid w-100 mt-1">
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Filename</th>
                    <th scope="col">Desc</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($this->variables['uploads'] as $cnt => $upload) { ?>

                    <tr>
                        <th scope="row"><?= $cnt ?></th>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="selected_uploads[]"
                                       value="<?= $upload['file_id'] ?>" id="defaultCheck<?= $cnt ?>">
                                <label class="form-check-label" for="defaultCheck<?= $cnt ?>">
                                    <?= $upload['file_name'] ?>
                                </label>
                            </div>
                        </td>
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