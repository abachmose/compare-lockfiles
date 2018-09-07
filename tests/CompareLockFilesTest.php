<?php namespace tests;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CompareLockFilesTest extends \PHPUnit\Framework\TestCase
{

    /** @var CommandTester */
    protected $commandTester;

    /** @var \Symfony\Component\Console\Command\Command */
    protected $command;

    protected function setUp()
    {
        parent::setUp();
        $app = new Application();

        $app->add(new \LockFiles\Commands\CompareLockFiles);

        $this->command       = $app->find('compare');
        $this->commandTester = new CommandTester($this->command);
    }

    /** @test */
    public function it_shows_a_table_that_compares_modules_from_the_package_lock_with_modules_from_the_yarn_lock_file()
    {
        $this->markTestIncomplete('Find a way to test the table');

        $this->defaultExecute();

        dd($this->commandTester->getDisplay());
    }

    /** @test */
    public function it_prioritizes_the_yarn_lockfile_per_default()
    {
        $this->defaultExecute();
        $this->assertContains('Yarn Version (Prioritized)', $this->commandTester->getDisplay());
    }

    /** @test */
    public function it_prioritizes_the_npm_lockfile()
    {
        $this->commandTester->execute([
            'command'   => $this->command->getName(),
            'lockfiles' => ['./tests/yarn.lock:yarn', './tests/package-lock.json:npm'],
            '--prioritize' => 'npm'
        ]);

        $this->assertContains('Npm Version (Prioritized)', $this->commandTester->getDisplay());
    }

    private function defaultExecute(): void
    {
        $this->commandTester->execute([
            'command'   => $this->command->getName(),
            'lockfiles' => ['./tests/yarn.lock:yarn', './tests/package-lock.json:npm']
        ]);
    }
}