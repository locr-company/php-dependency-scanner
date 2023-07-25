<?php

declare(strict_types=1);

namespace Locr\Lib;

/**
 * @property-read array<string, DependencyData> $Dependencies
 * @property-read string[] $Licenses
 * @property-read string $Version
 * @property-read string $Website
 */
class DependencyData
{
    /**
     * Constructs a new DependencyData instance. This is intended to be used only internally.
     *
     * ```php
     * <?php
     *
     * use Locr\Lib\DependencyData;
     *
     * $depData = new DependencyData(
     *  version: '1.0',
     *  website: 'https://example.com',
     *  licenses: ['MIT'],
     *  dependencies: ['sub' => new DependencyData()]
     * );
     * ```
     *
     * @param string[] $licenses
     * @param array<string, DependencyData> $dependencies
     */
    public function __construct(
        private string $version = 'unknown',
        private string $website = '',
        private array $licenses = [],
        private array $dependencies = []
    ) {
    }

    /**
     * @internal
     */
    public function __get(string $name): mixed
    {
        return match ($name) {
            'Dependencies' => $this->dependencies,
            'Licenses' => $this->licenses,
            'Version' => $this->version,
            'Website' => $this->website,
            default => null
        };
    }

    /**
     * adds a dependency to this dependency.
     *
     * ```php
     * <?php
     *
     * use Locr\Lib\DependencyData;
     *
     * $depData = new DependencyData(
     *  version: '1.0',
     *  website: 'https://example.com',
     *  licenses: ['MIT']
     * );
     * subDepData = new DependencyData(
     *  version: '1.1',
     *  website: 'https://example.org',
     *  licenses: ['MIT']
     * );
     * $depData->addDependency('sub', $subDepData);
     * print count($depData->Dependencies); // 1
     * ```
     */
    public function addDependency(string $name, self $data): self
    {
        $this->dependencies[$name] = $data;

        return $this;
    }

    /**
     * sets the dependency licenses.
     *
     * ```php
     * <?php
     *
     * use Locr\Lib\DependencyData;
     *
     * $depData = new DependencyData(
     *  version: '1.0',
     *  website: 'https://example.com'
     * );
     * $depData->setLicenses(['MIT']);
     * print $depData->Licenses[0]; // MIT
     * ```
     *
     * @param string[] $licenses
     */
    public function setLicenses(array $licenses): self
    {
        $this->licenses = $licenses;

        return $this;
    }

    /**
     * sets the dependency version.
     *
     * ```php
     * <?php
     *
     * use Locr\Lib\DependencyData;
     *
     * $depData = new DependencyData(
     *  website: 'https://example.com'
     * );
     * $depData->setVersion('1.0');
     * print $depData->Version; // 1.0
     * ```
     */
    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * sets the dependency website.
     *
     * ```php
     * <?php
     *
     * use Locr\Lib\DependencyData;
     *
     * $depData = new DependencyData(
     *  version: '1.0'
     * );
     * $depData->setWebsite('https://example.com');
     * print $depData->Website; // https://example.com
     * ```
     */
    public function setWebsite(string $website): self
    {
        $this->website = $website;

        return $this;
    }
}
