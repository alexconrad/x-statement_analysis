<?php

namespace Common;

use Exception;
use RuntimeException;

interface MySQLOnErrorInterface
{

    /**
     * @param $query
     * @param Exception $e
     * @return mixed
     * @throws RuntimeException
     */
    public function onErrorAction($query, Exception $e);
}
