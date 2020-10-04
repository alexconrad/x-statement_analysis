<?php

namespace Misico\DB;

class DbExpr
{

    private $string;

    public function __construct($string)
    {
        $this->string = $string;
    }

    /**
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }
}
