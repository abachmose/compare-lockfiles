<?php namespace tests;

use LockFiles\FilePath;
use PHPUnit\Framework\TestCase;

class FilePathTest extends TestCase
{

    /** @test **/
    function it_gives_the_file_path() {

        $faker = \Faker\Factory::create();

        $path     = $faker->word;
        $filePath = new FilePath($path);

        $this->assertEquals($path, $filePath->getPath());

    }

    /** @test **/
    function it_gives_the_extension() {

        $path = 'a/path/to/a/phpFile.php';

        $filePath = new FilePath($path);

        $this->assertEquals('php', $filePath->getExtension());

    }

    /** @test **/
    function it_tells_if_the_path_has_the_given_extension() {

        $path = 'tell/me/the/extension/for/file.xml';

        $filePath = new FilePath($path);

        $this->assertTrue($filePath->hasExtension('xml'));

    }


    /** @test **/
    function it_tells_if_the_path_doesnt_have_the_given_extension() {

        $path = 'tell/me/the/that/this/is/the/wrong/extension/for/this/file.xml';

        $filePath = new FilePath($path);

        $this->assertFalse($filePath->hasExtension('i3d'));

    }

    /** @test **/
    function it_tells_if_the_path_has_the_given_name_of_a_file() {

        $path = 'tell/me/the/file/name/of/this/file.svg';

        $filePath = new FilePath($path);

        $this->assertTrue($filePath->hasName('file.svg'));

    }


    /** @test **/
    function it_tells_if_the_path_doesnt_have_the_given_name_of_a_file() {

        $path = 'tell/me/that/this/is/the/wrong/name/of/this/file.svg';

        $filePath = new FilePath($path);

        $this->assertFalse($filePath->hasName('tester.svg'));

    }

}