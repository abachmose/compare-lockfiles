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

    /** @test **/
    function it_throws_when_one_of_the_lockfiles_wasnt_found() {

        /**
         * Expect that it throws at the first lockfile
         */
        $this->expectException(\LockFiles\Exceptions\LockFileNotFound::class);

        $this->commandTester->execute([
            'command' => $this->command->getName(),
            'lockfiles' => ['a/wrong/path/to/package-lock.json:npm', 'a/wrong/path/to/yarn.lock:yarn']
        ]);

    }

    /** @test **/
    function it_shows_a_upgrade_arrow_in_the_upgrade_directions_for_the_prioritized_lockfile() {

        $this->defaultExecute();

        /**
         * In the example lockfiles we see that ajv-keywords has a version of 3.1.0 in package-lock.json
         * In the yarn.lock lockfile the version is 2.1.1
         * We expect that a downward direction of the arrow, because we wan't to have yarn as our source of truth of the version (it's per default prioritized)
         */

        $downwardArrow = json_decode('"\u21e3"');

        $this->assertContains("3.1.0 ".$downwardArrow, $this->commandTester->getDisplay());

        /**
         * For the module: ajv we see that the NPM version is 6.2.0
         * The first defined version version of the module in the yarn.lock file is: 4.11.8
         * We wan't to downgrade to 4.11.8
         */
        $this->assertContains('4.11.8 '.$downwardArrow, $this->commandTester->getDisplay());

    }

    /** @test **/
    function it_throws_when_a_lockfile_type_is_not_specified_in_arguments() {

        $this->expectException(\LockFiles\Exceptions\LockfileTypeNotSpecified::class);

        $this->commandTester->execute([
            'command' => $this->command->getName(),
            'lockfiles' => ['a/path/to/package-lock.json', 'a/path/to/yarn.lock']
        ]);

    }

    /** @test **/
    function it_throws_when_package_lockfile_has_no_dependencies() {

        $this->expectException(\LockFiles\NpmDependenciesCannotBeRead::class);

        $this->commandTester->execute([
            'command' => $this->command->getName(),
            'lockfiles' => ['tests/package-lock-without-dependencies.json:npm', 'tests/yarn.lock:yarn']
        ]);

    }

    /** @test **/
    function it_doesnt_add_the_yarn_module_if_it_doesnt_have_a_version() {

        $this->commandTester->execute([
            'command' => $this->command->getName(),
            'lockfiles' => ['tests/package-lock.json:npm', 'tests/yarn-modules-without-versions.lock:yarn']
        ]);

        /**
         * Assert that the only module in the yarn lockfile is removed
         */
        $this->assertNotContains('ajv-keywords', $this->commandTester->getDisplay());

    }

    /** @test **/
    function it_doesnt_prioritize_a_lockfile() {

        $this->commandTester->execute([
            'command'   => $this->command->getName(),
            'lockfiles' => ['./tests/yarn.lock:yarn', './tests/package-lock.json:npm'],
            '--prioritize' => 'js'
        ]);

        $this->assertContains('Yarn Version', $this->commandTester->getDisplay());
        $this->assertContains('Npm Version', $this->commandTester->getDisplay());

    }
    /** @test **/
    function it_doesnt_prioritize_when_lockfiles_are_the_same() {

        $this->commandTester->execute([
            'command'   => $this->command->getName(),
            'lockfiles' => ['./tests/yarn.lock:yarn', './tests/yarn.lock:yarn'],
            '--prioritize' => 'js'
        ]);

        $this->assertContains('Yarn Version', $this->commandTester->getDisplay());
        $this->assertContains('Yarn Version', $this->commandTester->getDisplay());

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