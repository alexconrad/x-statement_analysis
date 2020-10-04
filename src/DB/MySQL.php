<?php

namespace Misico\DB;

use Closure;
use Common\MySQLOnErrorInterface;
use Exception;
use PDO;
use PDOException;
use PDOStatement;

class MySQL
{
    const CONNECTION_TIMEZONE = 'UTC';

    const MAX_PREPARED_STATEMENT_STACK = 10;

    /** @var PDOStatement[]  */
    private $statementStack = array();

    /** @var MySQLOnErrorInterface */
    private $errorHandler;


    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var int
     */
    private $last_affected_rows;

    /**
     * MySQL constructor.
     * @param null $database
     * @throws Exception
     */
    public function __construct($database = null)
    {
        if ($database === null) {
            $db = 'dbname=' . APP_DB_NAME . ';';
        } elseif ($database === '') {
            $db = ''; //no database in connection
        } else {
            $db = 'dbname=' . $database . ';';
        }

        $dsn = 'mysql:'.$db.'port='.APP_DB_PORT.';host=' . APP_DB_HOST;
        $this->pdo = new PDO($dsn, APP_DB_USER, APP_DB_PASS);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $result = $this->oneCell(
            "SELECT CONVERT_TZ('2010-01-01 00:00:00', 'UTC', :tz)",
            ['tz'=>self::CONNECTION_TIMEZONE]
        );
        if (empty($result)) {
            throw new Exception('MySQL does not have timezone information for timezone '.
                '['.self::CONNECTION_TIMEZONE.']');
        }

        /** @noinspection UnusedFunctionResultInspection */
        $this->rawQuery("SET time_zone = '".self::CONNECTION_TIMEZONE."';");
    }

    public function setErrorHandler(MySQLOnErrorInterface $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     * @param $query
     * @param Exception $e
     */
    public function errorHandler($query, Exception $e)
    {
        if ($this->errorHandler instanceof MySQLOnErrorInterface) {
            $this->errorHandler->onErrorAction($query, $e);
        } else {
            http_response_code(500);
            die("DB ERROR:" . $query.$e->getMessage());
        }
    }

    /**
     * @return int
     */
    public function getLastAffectedRows()
    {
        return $this->last_affected_rows;
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    private function getStatement($query)
    {
        if (count($this->statementStack) >= self::MAX_PREPARED_STATEMENT_STACK) {
            $toClose = array_shift($this->statementStack);
            $toClose->closeCursor();
            unset($toClose);
        }

        $hash = md5($query);
        if (array_key_exists($hash, $this->statementStack)) {
            return $this->statementStack[$hash];
        }

        //echo "NEW !!!";
        $this->statementStack[$hash] = $this->pdo->prepare($query);
        return $this->statementStack[$hash];
    }

    /**
     * @param $query
     * @param array $binds
     * @return string|null
     */
    public function write($query, $binds = array())
    {
        try {
            $statement = $this->getStatement($query);

            foreach ($binds as $key => $value) {
                /** @noinspection OneTimeUseVariablesInspection IT DOES MAKE SENSE FOR BINDING */
                $$key = $value;
                $bindingType = PDO::PARAM_STR;
                if (strpos($key, 'integer_') === 0) {
                    $bindingType = PDO::PARAM_INT;
                }

                $statement->bindParam(':' . $key, $$key, $bindingType);
            }

            $this->last_affected_rows = null;
            $statement->execute();
            $this->last_affected_rows = $statement->rowCount();

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $this->errorHandler($query, $e);
        }

        return null;
    }

    /**
     * @param $query
     * @param array $binds
     * @return array
     */
    public function all($query, $binds = array())
    {
        try {
            $statement = $this->pdo->prepare($query);
            foreach ($binds as $key => $value) {

                /** @noinspection OneTimeUseVariablesInspection IT DOES MAKE SENSE FOR BINDING */
                $$key = $value;

                $bindingType = PDO::PARAM_STR;
                if (strpos($key, 'integer_') === 0) {
                    $bindingType = PDO::PARAM_INT;
                }

                //$statement->bindParam(':' . $key, $value, $bindingType);
                $statement->bindParam(':' . $key, $$key, $bindingType);
            }

            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->errorHandler($query, $e);
        }

        return null;
    }

    /**
     * @param $query
     * @param array $binds
     * @return array
     */
    public function oneRow($query, $binds = array())
    {
        $rows = $this->all($query, $binds);

        if (count($rows) >= 1) {
            return $rows[0];
        }

        return [];
    }

    /**
     * @param $query
     * @param array $binds
     * @return array
     */
    public function assoc($query, $binds = array())
    {
        $rows = $this->all($query, $binds);
        $ret = [];
        foreach ($rows as $row) {
            $ret[current($row)] =  next($row);
        }
        return $ret;
    }

    /**
     * @param $query
     * @param array $binds
     * @return string|null
     */
    public function oneCell($query, $binds = array())
    {
        $row = $this->oneRow($query, $binds);
        return array_shift($row);
    }

    public function bigSelect($sql, Closure $function)
    {
        $stmt = $this->pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $function($row);
        }
        $stmt = null;
    }

    /**
     * @param $query
     * @param int $mode
     * @return false|PDOStatement
     */
    public function rawQuery($query, $mode = null)
    {
        if ($mode === null) {
            return $this->pdo->query($query);
        }

        return $this->pdo->query($query, $mode);
    }

    public function closeConnection()
    {
        //$this->pdo->query('KILL CONNECTION_ID()');
        $this->pdo = null;
    }
}
