<?php namespace LockFiles\Parsers;

use LockFiles\ParserAbstract;
use LockFiles\ParserInterface;

class JSONParser extends ParserAbstract implements ParserInterface
{

    /**
     * Begin parsing the file
     * @return array
     */
    public function parse(): array
    {
        return json_decode(file_get_contents($this->filePath), true);
    }

}