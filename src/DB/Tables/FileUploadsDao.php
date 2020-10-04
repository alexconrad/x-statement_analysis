<?php


namespace Misico\DB\Tables;


use Misico\DB\MySQL;

class FileUploadsDao
{
    public const BUILD_FILENAME_SECRET = 'sson6@T^I0O3';
    /**
     * @var MySQL
     */
    private $mySQL;

    public function __construct(MySQL $mySQL)
    {
        $this->mySQL = $mySQL;
    }

    public function addUpload($fileName, $description): int {
        $query = "INSERT INTO file_uploads SET file_name =:file, description =:desc, dated = NOW()";
        return $this->mySQL->write($query, ['file'=>$fileName, 'desc'=>$description]);
    }

    public function homepageList()
    {
        $query = "SELECT * FROM file_uploads ORDER BY file_id DESC LIMIT 100";
        return $this->mySQL->all($query, []);
    }


}