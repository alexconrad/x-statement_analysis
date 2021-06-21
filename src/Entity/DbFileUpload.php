<?php


namespace Misico\Entity;

use Misico\DB\Tables\FileUploadsDao;

class DbFileUpload
{
    private $file_id;
    private $description;
    private $file_name_table;
    private $dated;
    private $file_name_statement;

    public function __construct($file_id, $description, $file_name, $file_name_statement, $dated)
    {
        $this->file_id = $file_id;
        $this->description = $description;
        $this->file_name_table = $file_name;
        $this->file_name_statement = $file_name_statement;
        $this->dated = $dated;
    }

    /**
     * @return mixed
     */
    public function getFileId()
    {
        return $this->file_id;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getFileNameTable()
    {
        return $this->file_name_table;
    }

    /**
     * @return mixed
     */
    public function getFileNameStatement()
    {
        return $this->file_name_statement;
    }



    /**
     * @return mixed
     */
    public function getDated()
    {
        return $this->dated;
    }




}
