<?php namespace LockFiles\Parsers;

use LockFiles\ParserInterface;

class YarnParser implements ParserInterface
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * ParserInterface construct the parser with the file to parse
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Begin parsing the file
     * @return array
     */
    public function parse(): array
    {
        $fileContents = file_get_contents($this->filePath);

        return $this->cleanup($fileContents);
    }

    private function cleanup(string $fileContents): array
    {
        $trimmedContent = [];
        foreach (explode("\n", $fileContents) as $lineContents) {
/*
            //is dependency?
            if(trim($fileContents) === 'dependencies:' && $dependencyList){
                $dependencyList = true;
                continue;
            }*/

            /**
             * If the line starts with more than two spaces (a dependency)
             * ..or if the line is a comment
             */
            if(substr($lineContents,0, 3) === "" || substr($lineContents, 0, 1) === '#') {
                continue;
            }


            $trimmedContent[] = $lineContents;

        }

        return $trimmedContent;
    }

}