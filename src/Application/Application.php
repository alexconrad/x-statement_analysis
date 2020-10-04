<?php

namespace Misico\Application;

use Psr\Container\ContainerInterface;

class Application
{

    /** @var ContainerInterface|null */
    public static $container = null;

    public static $cliArguments = [];
}
