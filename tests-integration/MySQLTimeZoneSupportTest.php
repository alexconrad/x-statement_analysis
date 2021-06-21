<?php
declare(strict_types=1);

namespace MisicoIntegrationTest;

use Misico\DB\MySQL;
use function DI\autowire;
use DI\ContainerBuilder;
use IntegrationTest\IntegrationTest;

final class MySQLTimeZoneSupportTest extends IntegrationTest
{

    /** @var MySQL */
    private static $connection;

    public function setUp()
    {
    }

    public static function setUpBeforeClass()
    {
        self::$tiDatabaseName = '';

        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            MySQL::class => autowire()
                ->constructor(self::$tiDatabaseName),
        ]);
        self::$DI = $builder->build();

        self::$connection = self::$DI->get(MySQL::class);
    }

    public static function tearDownAfterClass()
    {
    }

    /**
     * @dataProvider providerData
     * @param $timezone
     */
    public function testTimeZoneSupport($timezone)
    {
        $query = "SELECT CONVERT_TZ('2019-01-01 00:00:00', 'UTC', :tz) AS ctz";
        $result = self::$connection->oneCell($query, ['tz'=>$timezone]);

        /** @noinspection PhpParamsInspection */
        $this->assertNotNull($result, "TimeZone [$timezone] is not setup in mysql!");
    }

    public function providerData()
    {
        $timezones = timezone_identifiers_list();
        $ret = [];
        foreach ($timezones as $timezone) {
            $ret[] = [$timezone];
        }
        return $ret;
    }
}
