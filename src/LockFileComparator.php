<?php namespace LockFiles;

use LockFiles\Exceptions\IncompatibleLockfileException;

class LockFileComparator
{
    protected $CLITableDTO;
    /**
     * @var array
     */
    private $lockFiles;

    /**
     * LockFileComparator constructor.
     * @param array $lockFiles
     * @throws IncompatibleLockfileException
     */
    public function __construct(array $lockFiles)
    {
        $this->checkCompabilityAndPrioritize($lockFiles);
        $this->lockFiles = $lockFiles;
        $this->CLITableDTO = new CLITableDTO();
    }

    /**
     * @param array $lockFiles
     * @throws IncompatibleLockfileException
     */
    private function checkCompabilityAndPrioritize(array $lockFiles): void
    {
        foreach ($lockFiles as $lockFile) {

            if (!$lockFile instanceof LockFileInterface) {
                $className = get_class($lockFile);
                throw new IncompatibleLockfileException("The lockFile with the class name: {$className} is not an instance of: LockFileInterface");
            }

        }
    }

    public function compare(string $prioritize = 'yarn'): CLITableDTO
    {

        $this->lockFiles = (new LockFilePrioritizer($this->lockFiles))->prioritize($prioritize);
        $this->addHeaders($prioritize);

        foreach ($this->lockFiles[0]->getModules() as $compareModule) {
            $this->isConflicting($compareModule, $this->lockFiles[0]);
        }

        return $this->CLITableDTO;

    }

    private function isConflicting(Module $compareModule, LockFileInterface $prioritizedLockFile)
    {

        /** @var LockFileInterface $lockfile */
        foreach ($this->lockFiles as $lockFile) {

            if ($lockFile->getName() === $prioritizedLockFile->getName()) {
                continue;
            }

            /** @var Module $module */
            foreach ($lockFile->getModules() as $module) {

                if ($compareModule->getName() !== $module->getName()) {
                    continue;
                }

                /**
                 * If the version matches, everything is good
                 */
                if ($compareModule->getVersion() === $module->getVersion()) {
                    continue;
                }

                $this->CLITableDTO->addRow(
                    $this->getUniqueKey($prioritizedLockFile, $lockFile, $module),
                    $this->getConflictingModulesAsArray($compareModule, $module)
                );
                return true;

            }
        }

        return false;

    }

    /**
     * @param LockFileInterface $baseLockFile
     * @param $lockFile
     * @param $module
     * @return string
     */
    private function getUniqueKey(LockFileInterface $baseLockFile, $lockFile, $module): string
    {
        $lockFileNames = [$lockFile->getName(), $baseLockFile->getName()];

        sort($lockFileNames);
        $key = join("-", $lockFileNames) . '-' . $module->getName();
        return $key;
    }

    /**
     * @param Module $compareModule
     * @param LockFileInterface $prioritizedLockFile
     * @param $lockFile
     * @param $module
     * @return array
     */
    private function getConflictingModulesAsArray(
        Module $compareModule,
        Module $module
    ): array {

        $upgradeArrow = '';

        if($module->getVersion() > $compareModule->getVersion()) {
            $upgradeArrow = json_decode('"\u21e1"');
        }

        if($module->getVersion() < $compareModule->getVersion()) {
            $upgradeArrow = json_decode('"\u21e3"');
        }

        return [
            'moduleName' => $module->getName(),
            'moduleVersion'        => $module->getVersion().' '.$upgradeArrow,
            'compareModuleVersion' => $compareModule->getVersion(),
        ];
    }

    private function addHeaders(string $prioritize = 'yarn')
    {
        $this->CLITableDTO->addHeader('Module name');

        /** @var LockFileInterface $lockFile */
        foreach ($this->lockFiles as $lockFile) {

            if($lockFile->getName() === $prioritize) {
                $this->CLITableDTO->addHeader(ucfirst("{$lockFile->getName()} Version (Prioritized)"));
                continue;
            }

            $this->CLITableDTO->addHeader(ucfirst("{$lockFile->getName()} Version"));
        }

    }

}