<?php namespace LockFiles;

abstract class ParserAbstract
{

    /**
     * @var string
     */
    public $filePath;

    /**
     * ParserInterface construct the parser with the file to parse
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

}