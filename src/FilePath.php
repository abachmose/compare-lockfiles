<?php namespace LockFiles;

use Illuminate\Support\Collection;

class FilePath
{
    /**
     * @var string
     */
    private $filePath;

    public function __construct(string $path)
    {
        $this->filePath = $path;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->filePath;
    }

    /**
     * @return Collection
     */
    protected function getPathParts(): Collection
    {
        return collect(explode('.', $this->filePath));
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->getPathParts()->last();
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->getPathParts()->first();
    }

    public function getName(): string
    {
        return $this->getDirectorySeperated()->last();
    }

    /**
     * @return Collection
     */
    private function getDirectorySeperated(): Collection
    {
        return collect(explode(DIRECTORY_SEPARATOR, $this->getPath()));
    }

    public function hasName(string $name)
    {
        return $this->getName() === $name;
    }

    public function hasExtension(string $exts)
    {
        return $this->getExtension() === $exts;
    }

}