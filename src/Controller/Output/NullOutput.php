<?php

namespace Misico\Controller\Output;

class NullOutput implements OutputInterface
{

    private $url;

    public function setRedirectUrl($url)
    {
        $this->url = $url;
    }

    public function processControllerReturn($controller, $action)
    {
    }
}
