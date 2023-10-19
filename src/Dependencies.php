<?php

declare(strict_types=1);

namespace Locr\Lib;

class Dependencies
{
    public const DEFAULT_MAX_RECURSION_DEPTH = 500;

    private static function forceComposerJsonCouldNotBeenRead(): bool
    {
        $env = getenv('FORCE_COMPOSER_JSON_COULD_NOT_BEEN_READ');
        return ($env === '1');
    }

    private static function forceComposerJsonDataIsNotAnArray(): bool
    {
        $env = getenv('FORCE_COMPOSER_JSON_DATA_IS_NOT_AN_ARRAY');
        return ($env === '1');
    }

    private static function forceComposerLockCouldNotBeenRead(): bool
    {
        $env = getenv('FORCE_COMPOSER_LOCK_COULD_NOT_BEEN_READ');
        return ($env === '1');
    }

    private static function forceComposerLockDataIsNotAnArray(): bool
    {
        $env = getenv('FORCE_COMPOSER_LOCK_DATA_IS_NOT_AN_ARRAY');
        return ($env === '1');
    }

    /**
     * @return array<string, DependencyData>
     */
    public static function getComposerDependencies(
        string $path,
        bool $withDev = false,
        int $maxRecursionDepth = self::DEFAULT_MAX_RECURSION_DEPTH,
        bool $filesAreRequired = true
    ): array {
        $dependencies = [];

        $composerJsonFilename = $path . DIRECTORY_SEPARATOR . 'composer.json';
        $composerLockFilename = $path . DIRECTORY_SEPARATOR . 'composer.lock';
        if (!file_exists($composerJsonFilename)) {
            if ($filesAreRequired) {
                throw new \Exception(
                    __METHOD__ . '(string $path, bool $filesAreRequired): array => composer.json does not exists!'
                );
            }
            return $dependencies;
        }
        if (!file_exists($composerLockFilename)) {
            if ($filesAreRequired) {
                throw new \Exception(
                    __METHOD__ . '(string $path, bool $filesAreRequired): array => composer.lock does not exists!'
                );
            }
            return $dependencies;
        }
        $composerJsonContent = file_get_contents($composerJsonFilename);
        if ($composerJsonContent === false || self::forceComposerJsonCouldNotBeenRead()) {
            throw new \Exception(
                __METHOD__ . '(string $path, bool $filesAreRequired): array => composer.json could not been read!'
            );
        }
        $composerLockContent = file_get_contents($composerLockFilename);
        if ($composerLockContent === false || self::forceComposerLockCouldNotBeenRead()) {
            throw new \Exception(
                __METHOD__ . '(string $path, bool $filesAreRequired): array => composer.lock could not been read!'
            );
        }

        $composerJson = json_decode($composerJsonContent, true);
        if (!is_array($composerJson) || self::forceComposerJsonDataIsNotAnArray()) {
            throw new \Exception(
                __METHOD__ . '(string $path, bool $filesAreRequired): array => composer.json data is not an array!'
            );
        }

        $composerLock = json_decode($composerLockContent, true);
        if (!is_array($composerLock) || self::forceComposerLockDataIsNotAnArray()) {
            throw new \Exception(
                __METHOD__ . '(string $path, bool $filesAreRequired): array => composer.lock data is not an array!'
            );
        }

        $arrayKeysToScan = ['require'];
        if ($withDev) {
            $arrayKeysToScan[] = 'require-dev';
        }
        foreach ($arrayKeysToScan as $arrayKeyToScan) {
            if (isset($composerJson[$arrayKeyToScan]) && is_array($composerJson[$arrayKeyToScan])) {
                $packagesKey = 'packages';
                if ($arrayKeyToScan === 'require-dev') {
                    $packagesKey = 'packages-dev';
                }
                foreach ($composerJson[$arrayKeyToScan] as $name => $version) {
                    if (isset($composerLock[$packagesKey]) && is_array($composerLock[$packagesKey])) {
                        $packageDependencies = self::getComposerPackageDependenciesRecursive(
                            packages: $composerLock[$packagesKey],
                            packageName: $name,
                            withDev: $withDev,
                            maxRecursionDepth: $maxRecursionDepth
                        );
                        if (isset($packageDependencies[$name])) {
                            $dependencies[$name] = $packageDependencies[$name];
                            foreach ($packageDependencies as $dependencyName => $dependencyData) {
                                if ($dependencyName === $name) {
                                    continue;
                                }
                                $dependencies[$name]->addDependency($dependencyName, $dependencyData);
                            }
                        }
                    }
                }
            }
        }

        return $dependencies;
    }

    /**
     * @param array<mixed> $packages
     * @return array<string, DependencyData> $dependencies
     */
    private static function getComposerPackageDependenciesRecursive(
        array $packages,
        string $packageName,
        bool $withDev = false,
        int $maxRecursionDepth = -1,
        int $currentRecursionDepth = 0
    ): array {
        $dependencies = [];

        if ($maxRecursionDepth > 0 && $currentRecursionDepth > $maxRecursionDepth) {
            return $dependencies;
        }

        foreach ($packages as $package) {
            if (is_array($package) && isset($package['name'])) {
                if ($package['name'] !== $packageName) {
                    continue;
                }
                $dependency = new DependencyData();
                if (isset($package['version'])) {
                    $dependency->setVersion($package['version']);
                }
                if (isset($package['license']) && is_array($package['license'])) {
                    $dependency->setLicenses($package['license']);
                }
                if (isset($package['source']) && is_array($package['source'])) {
                    if (isset($package['source']['url'])) {
                        $dependency->setWebsite($package['source']['url']);
                    }
                }

                $dependencies[$packageName] = $dependency;

                if (isset($package['require']) && is_array($package['require'])) {
                    foreach ($package['require'] as $packageRequirement => $packageRequirementVersion) {
                        if ($packageRequirement === 'php') {
                            continue;
                        }
                        $subPackages = self::getComposerPackageDependenciesRecursive(
                            packages: $packages,
                            packageName: $packageRequirement,
                            withDev: $withDev,
                            maxRecursionDepth: $maxRecursionDepth,
                            currentRecursionDepth: $currentRecursionDepth + 1
                        );
                        if (isset($subPackages[$packageRequirement])) {
                            $dependencies[$packageRequirement] = $subPackages[$packageRequirement];
                            foreach ($subPackages as $dependencyName => $dependencyData) {
                                if ($dependencyName === $packageRequirement) {
                                    continue;
                                }
                                $dependencies[$packageRequirement]->addDependency($dependencyName, $dependencyData);
                            }
                        }
                    }
                }
            }
        }

        return $dependencies;
    }

    /**
     * @return array<string, DependencyData>
     */
    public static function getDependencies(
        string $path,
        bool $withDev = false,
        int $maxRecursionDepth = self::DEFAULT_MAX_RECURSION_DEPTH
    ): array {
        return self::getComposerDependencies(
            path: $path,
            withDev: $withDev,
            maxRecursionDepth: $maxRecursionDepth,
            filesAreRequired: false
        );
    }
}
