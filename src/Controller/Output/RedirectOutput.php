<?php

namespace Misico\Controller\Output;

class RedirectOutput implements OutputInterface
{

    private $url;

    public function setRedirectUrl($url)
    {
        $this->url = $url;
    }

    public function processControllerReturn($controller, $action)
    {
        header("Location: ".$this->url);
    }
}
