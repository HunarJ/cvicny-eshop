<?php


namespace App\Model;

use Nette\Database\Explorer;
use Nette\SmartObject;

abstract class DatabaseManager
{
    use SmartObject;

    protected $database;

    public  function __construct(Explorer $database)
    {
        $this->database = $database;
    }

}