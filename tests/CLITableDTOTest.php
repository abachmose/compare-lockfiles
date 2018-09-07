<?php namespace tests;

use LockFiles\CLITableDTO;
use PHPUnit\Framework\TestCase;

class CLITableDTOTest extends TestCase
{
    /** @test * */
    function it_returns_the_headers_and_rows()
    {
        $headers = [
            'Module name',
            'Npm Version',
            'Yarn Version'
        ];

        $rows = [
            'react',
            '3.2.1',
            '2.5.2'
        ];

        $dto = new CLITableDTO($headers, $rows);

        $this->assertEquals($headers, $dto->getHeaders()->toArray());
        $this->assertEquals($rows, $dto->getRows()->toArray());

    }

    /** @test **/
    function it_returns_the_added_rows_and_headers() {

        $headers = [
            'Module name',
            'Yarn version',
            'Npm version',
        ];

        $row = [
            'npm',
            '2.3.2',
            '4.3.2'
        ];

        $dto = new CLITableDTO();

        foreach ($headers as $header) {
            $dto->addHeader($header);
        }

        $dto->addRow(0, $row);

        $this->assertEquals($headers, $dto->getHeaders()->toArray());
        $this->assertEquals([$row], $dto->getRows()->toArray());

    }

}