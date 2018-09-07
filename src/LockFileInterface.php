<?php namespace LockFiles;

use Illuminate\Support\Collection;

interface LockFileInterface
{
    /**
     * Get the modules the LockFile is depending on
     * @return Collection
     */
    public function getModules() : Collection;

    /**
     * Get the name of the lockfile
     * @return string
     */
    public function getName() :string;

}