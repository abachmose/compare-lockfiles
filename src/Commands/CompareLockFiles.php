<?php namespace LockFiles\Commands;

use LockFiles\LockFileComparator;
use LockFiles\Npm;
use LockFiles\Parsers\JSONParser;
use LockFiles\Parsers\YarnParser;
use LockFiles\Yarn;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompareLockFiles extends Command
{
    protected $nameMap = [
        'yarn' => Yarn::class,
        'npm' => Npm::class
    ];

    public function __construct()
    {
        parent::__construct();

        $this->addArgument('lockfiles', \Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'Lockfiles', ['yarn.lock:yarn', 'package-lock.json:npm']);
    }

    public function configure()
    {
        $this->setName('compare')
            ->addOption('prioritize', 'p', \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'The kind of lock file to prioritize', 'yarn')
            ->setDescription('A comparison of lock files that makes the upgrade to another package manager easier.')
            ->setHelp('Use --p to specify which lockfile to prioritize.');

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \LockFiles\Exceptions\IncompatibleLockfileException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {

        $lockFiles = array_map(function($arg){
            $exploded = explode(":", $arg);

            switch(count($exploded)){
                case 1:
                    throw new \Exception(sprintf("You need to provide the lockfile type for %s. One of: %s, e.g. (%s:npm)", $exploded[0], join(", ", array_keys($this->nameMap)), $exploded[0]));
            }

            list($path, $type) = $exploded;

            switch($type){
                case 'yarn':
                    return new Yarn(
                        (new YarnParser($path))->parse()
                    );
                case 'npm':
                    return new Npm(
                        (new JSONParser($path))->parse()
                    );

                default:
                    throw new \Exception("Not able to parse lockfile: ${$type}. A parser is not yet written.");
            }
        }, $input->getArgument('lockfiles'));

        $CLITableDTO = (new LockFileComparator($lockFiles))->compare($input->getOption('prioritize'));
        $CLITableDTO->render($output);

    }

}