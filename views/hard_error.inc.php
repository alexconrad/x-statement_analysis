<?php
/** @var $this \Misico\Controller\Output\ViewOutput */

use Misico\FriendlyException;

$this->render('_head');

$error = $this->variables['error'] ?? '';
$exception = $this->variables['exception'] ?? '';
$eerror = $this->variables['exceptionMessage'] ?? '';
?>
<br>
<div class="alert alert-danger" role="alert">
  <?php if (!empty($error)) echo htmlentities($error); else echo 'Unspecified error.'; ?>
  <br>
  <?php if (!empty($eerror)) echo htmlentities($eerror); else echo 'Unspecified Exception.'; ?>
    <?php if ($exception instanceof FriendlyException) {
        echo '<br><pre>'.htmlentities($exception->getMessage()).'</pre>';

    } ?>
</div>
