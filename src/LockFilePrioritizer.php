<?php namespace LockFiles;

class LockFilePrioritizer
{
    protected $lockFiles;

    /**
     * LockFilePrioritizer constructor.
     * @param array $lockFiles
     */
    public function __construct(array $lockFiles){
        $this->lockFiles = $lockFiles;
    }

    public function prioritize(string $prioritize = 'yarn')
    {
        usort($this->lockFiles, function(LockFileInterface $lockFileA, LockFileInterface $lockFileB) use ($prioritize) {

            if($lockFileA->getName() === $lockFileB->getName()) {
                return 0;
            }


            switch ($prioritize) {

                /**
                 * Check if this is the LockFile to prioritize
                 */
                case $lockFileA->getName():
                    return -1;

                /**
                 * Check if the other LockFile should be prioritized, then prioritize down the first file
                 */
                case $lockFileB->getName():
                    return 1;

                /**
                 * Else we remain neutral
                 */
                default:
                    return 0;

            }

        });

        return $this->lockFiles;
    }

}