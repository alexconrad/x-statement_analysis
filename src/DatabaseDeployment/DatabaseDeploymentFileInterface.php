<?php

namespace Misico\DatabaseDeployment;

use Misico\DB\MySQL;

interface DatabaseDeploymentFileInterface
{

    public function __construct(MySQL $mySQL);

    public function migrateUp();

    public function migrateDown();
}
