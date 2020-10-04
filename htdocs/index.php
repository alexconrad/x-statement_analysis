<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

use Misico\Application\Application;
use Misico\Common\Common;
use Misico\Controller\Output\OutputInterface;
use Misico\DB\MySQL;
use DI\ContainerBuilder;

require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'defines.php';

require APP_ROOT_PATH.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';


if (isset($_GET['c'])) {
    $controller = $_GET['c'];
} else {
    $controller = 'HomePage';
}

if (isset($_GET['a'])) {
    $action = $_GET['a'];
} else {
    $action = 'index';
}

$controller = Common::safeString($controller).'Controller';
$action = Common::safeString($action);


try {
    date_default_timezone_set(MySQL::CONNECTION_TIMEZONE);

    $builder = new ContainerBuilder();
    $container = $builder->build();
    Application::$container = $container;

    $className = 'Misico\\Web\\Controller\\' . $controller;
    $controllerObject = $container->get($className);

    $method = 'action'.ucfirst($action);

    if (!method_exists($controllerObject, $method)) {
        throw new RuntimeException('Invalid action.');
    }
    /** @var OutputInterface $controllerReturn */
    $controllerReturn = $controllerObject->$method();

    if (!$controllerReturn instanceof OutputInterface) {
        throw new RuntimeException('Invalid return.');
    }

    $controllerReturn->processControllerReturn($controller, $action);
} catch (Exception $e) {
    if ($container instanceof \DI\Container) {

        $object = $container->get(\Misico\Controller\Output\ViewOutput::class);
        $object->setTemplate('hard_error');
        $object->assign('exceptionMessage', get_class($e).':'.$e->getMessage());
        $object->processControllerReturn('','');
    } else {
        die('ERROR: ' . $e->getMessage());
    }
}
