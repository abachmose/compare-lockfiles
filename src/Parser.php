<?php namespace LockFiles;

class Parser extends ParserAbstract
{
    protected $handle;

    /** @var FilePath */
    protected $filePath;

    public function getSize(): string
    {
        return filesize($this->getFilePath()->getPath());
    }

    public function closeHandle(): void
    {
        fclose($this->handle);
    }

    /**
     * @throws CannotReadLockFileException
     */
    private function cannotReadException(): CannotReadLockFileException
    {
        throw new CannotReadLockFileException("Cannot read file with path: {$this->getPath()}");
    }

    /**
     * Get the LockFile file content
     * @return string
     * @throws CannotReadLockFileException
     */
    public function toString(): string
    {
        $this->handle = fopen($this->getFilePath()->getPath(), 'r') or $this->cannotReadException();

        return fread($this->handle, $this->getSize());
    }

    /**
     * @return LockFileInterface
     * @throws CannotParseLockfileException
     * @throws CannotReadLockFileException
     */
    public function getLockfile(): LockFileInterface
    {
        $lockFile = null;

        if ($this->isYarnLockfile()) {
            $lockFile = new Yarn($this->toString());
        }

        if ($this->isPackageLockfile()) {
            $lockFile = new Npm($this->toString());
        }

        if ($lockFile) {
            $this->closeHandle();
            return $lockFile;
        }

        throw new CannotParseLockfileException("Lockfile with path: {$this->getFilePath()->getPath()} cannot be parsed. Lockfile is not known by the parser.");

    }

    private function isYarnLockfile()
    {
        return $this->getFilePath()->hasName('yarn.lock');
    }

    private function isPackageLockfile()
    {
        return $this->getFilePath()->hasName('package-lock.json');
    }

    /**
     * @return FilePath
     */
    public function getFilePath(): FilePath
    {
        return $this->filePath;
    }
}