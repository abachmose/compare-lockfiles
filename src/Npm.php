<?php namespace LockFiles;

use Illuminate\Support\Collection;

class Npm extends LockFileAbstract implements LockFileInterface
{

    protected $jsonContents;

    public function __construct(array $jsonContents)
    {
        $this->jsonContents = $jsonContents;
    }
    
    public function getName(): string
    {
        return 'npm';
    }

    /**
     * Get the modules the LockFile is depending on
     * @return Collection
     * @throws NpmDependenciesCannotBeRead
     */
    public function getModules(): Collection
    {
        $modules = new Collection;

        if(!array_key_exists('dependencies', $this->jsonContents)) {
            throw new NpmDependenciesCannotBeRead('The NPM dependencies cannot be read from the lock file');
        }

        foreach ($this->jsonContents['dependencies'] as $dependencyName => $definition) {
            $modules->push(new Module($dependencyName, $definition['version']));
        }

        return $modules;
    }
}