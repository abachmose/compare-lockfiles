<?php namespace LockFiles;

use Illuminate\Support\Collection;

class Yarn extends LockFileAbstract implements LockFileInterface
{
    protected $modulesCollection;

    /**
     * Yarn constructor.
     * @param array $yarnContents
     */
    public function __construct(array $yarnContents)
    {
        $this->parse($yarnContents);
    }

    /**
     * Get the name of the lockfile
     * @return string
     */
    public function getName(): string
    {
        return 'yarn';
    }

    public function parse($yarnContents)
    {
        $this->modulesCollection = collect();

        foreach ($yarnContents as $lineIndex => $lineContents) {

            /**
             * Jump over if the line doesn't start with a whitespace
             */
            if (preg_match('/\s/', substr($lineContents, 0, 3))) {
                continue;
            }

            /**
             * Doesn't contain: @
             * ..or contains a space in the start (skip a dependency module)
             */
            if (strpos($lineContents, '@') === false || substr($lineContents,0, 1) === ' ') {
                continue;
            }

            $this->modulesCollection
                ->push(new Module($this->extractModuleName($lineContents), $this->extractVersion($yarnContents, $lineIndex)));

        }

    }

    /**
     * Get the modules the LockFile is depending on
     * @return Collection
     */
    public function getModules(): Collection
    {
        return $this->modulesCollection;
    }

    private function extractModuleName($lineContents): string
    {

        /**
         * Explode the version from the dependency on the line
         */
        $versionExploded = explode('@', $lineContents);

        /**
         * Get the name after the first: @, if the dependency has a: @ in the name ex: "@babel/code-frame@^7.0.0-beta.35":
         */
        if ($versionExploded[0] === '"') {
            return $versionExploded[1];
        }

        return $versionExploded[0];

    }

    private function extractVersion(array $yarnContents, $lineIndex): string
    {
        /**
         * The version is defined on the next line for the dependency
         */
        $versionLine = $yarnContents[$lineIndex + 1];

        /**
         * Explode the version from the line
         */
        $versionExplode = explode('version', $versionLine);

        /**
         * Get the last index of the exploded version string
         */
        $explodedVersionIndex = count($versionExplode) - 1;

        $version = $versionExplode[$explodedVersionIndex];

        /**
         * Trim " and whitespaces from the version
         */
        return trim($version, '" ');

    }
}