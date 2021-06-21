<?php


namespace Misico\DB\Tables;


use Misico\DB\MySQL;
use Misico\DB\Tables\Exceptions\NotFoundException;
use Misico\Entity\DbFileUpload;

class FileUploadsDao
{
    /**
     * @var MySQL
     */
    private $mySQL;

    public function __construct(MySQL $mySQL)
    {
        $this->mySQL = $mySQL;
    }

    public function addUpload($fileNameTable, $fileNameStatement, $description): int {
        $query = 'INSERT INTO file_uploads SET file_name =:file, file_name_statement =:file2, description =:desc, dated = NOW()';
        return $this->mySQL->write($query, ['file'=>$fileNameTable, 'file2'=>$fileNameStatement, 'desc'=>$description]);
    }

    public function homepageList(): array
    {
        $query = 'SELECT * FROM file_uploads ORDER BY file_id DESC LIMIT 100';
        return $this->mySQL->all($query, []);
    }

    /**
     * @param $id
     * @return DbFileUpload
     * @throws NotFoundException
     */
    public function getFileUpload($id): DbFileUpload
    {
        $query = 'SELECT * FROM file_uploads WHERE file_id = :id';
        $row = $this->mySQL->oneRow($query, ['id'=>$id]);
        if (empty($row)) {
            throw new NotFoundException('Cannot find file update '.$id);
        }
        return new DbFileUpload($row['file_id'], $row['description'], $row['file_name'], $row['file_name_statement'], $row['dated']);
    }



}
