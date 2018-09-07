<?php namespace LockFiles;

interface ParserInterface
{
    /**
     * ParserInterface construct the parser with the file to parse
     * @param string $filePath
     */
    public function __construct(string $filePath);

    /**
     * Begin parsing the file
     * @return array
     */
    public function parse(): array;

}