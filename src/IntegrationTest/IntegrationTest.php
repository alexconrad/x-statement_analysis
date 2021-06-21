<?php

namespace IntegrationTest;

use Misico\DatabaseDeployment\DatabaseDeployment;
use Misico\DB\MySQL;
use function DI\autowire;
use DI\Container;
use DI\ContainerBuilder;
use Exception;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTest extends TestCase
{
    protected static $tiDatabaseName;
    /**
     * @var DatabaseDeployment
     */
    protected static $tiDatabaseDeployment;

    /** @var Container */
    protected static $DI;

    /**
     * @throws Exception
     */
    public function setUp()
    {
        parent::setUp();

        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            MySQL::class => autowire()
                ->constructor(self::$tiDatabaseName),
        ]);
        self::$DI = $builder->build();
    }

    /**
     * @throws Exception
     */
    public static function setUpBeforeClass()
    {

        self::$tiDatabaseName = 'testi_' . md5(microtime());

        $creator = new MySQL('');
        $creator->rawQuery('CREATE DATABASE ' . self::$tiDatabaseName);

        $builder = new ContainerBuilder();
        $builder->addDefinitions([MySQL::class => autowire()->constructor(self::$tiDatabaseName)]);
        $container = $builder->build();
        self::$tiDatabaseDeployment = $container->get(DatabaseDeployment::class);
        self::$tiDatabaseDeployment->deployUp();
    }

    public static function tearDownAfterClass()
    {
        $creator = new MySQL('');
        $creator->rawQuery('DROP DATABASE ' . self::$tiDatabaseName);
    }
}
