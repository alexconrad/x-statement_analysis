<?php
/** @var $this \Misico\Controller\Output\ViewOutput */

$this->render('_head');

$error = $this->variables['error'];
$eerror = $this->variables['exceptionMessage'];
?>
<br>
<div class="alert alert-danger" role="alert">
  <?php if (!empty($error)) echo $error; else echo 'Unspecified error.'; ?>
  <br>
  <?php if (!empty($eerror)) echo $eerror; else echo 'Unspecified Exception.'; ?>
</div>