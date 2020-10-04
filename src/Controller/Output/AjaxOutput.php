<?php

namespace Misico\Controller\Output;

class AjaxOutput implements OutputInterface
{


    private $jsonData = [];
    /**
     * @var bool
     */
    private $success = true;

    public function setArray(array $json)
    {
        $this->jsonData = $json;
    }

    public function setError(bool $error)
    {
        $this->success = $error ? false : true;
    }


    public function processControllerReturn($controller, $action) : void
    {
        echo json_encode(['success'=>$this->success, 'data'=> $this->jsonData]);
    }
}
