<?php

namespace Misico\Controller\Output;

interface OutputInterface
{

    public function processControllerReturn($controller, $action);
}
