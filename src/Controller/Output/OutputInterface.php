<?php

namespace Misico\Controller\Output;

use Misico\FriendlyException;

interface OutputInterface
{

    public function processControllerReturn($controller, $action);
}
