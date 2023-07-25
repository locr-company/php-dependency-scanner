<?php

declare(strict_types=1);

namespace UnitTests;

use Locr\Lib\DependencyData;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Locr\Lib\DependencyData
 * @coversDefaultClass \Locr\Lib\DependencyData
 */
final class DependencyDataTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::__get
     */
    public function testConstructor(): void
    {
        $defaultConstructor = new DependencyData();

        $this->assertEquals('unknown', $defaultConstructor->Version);
        $this->assertEquals('', $defaultConstructor->Website);
        $this->assertEquals([], $defaultConstructor->Licenses);
        $this->assertEquals([], $defaultConstructor->Dependencies);

        $constructor = new DependencyData(
            version: '1.0',
            website: 'https://example.com',
            licenses: ['MIT'],
            dependencies: ['sub' => new DependencyData()]
        );

        $this->assertEquals('1.0', $constructor->Version);
        $this->assertEquals('https://example.com', $constructor->Website);
        $this->assertEquals(['MIT'], $constructor->Licenses);
        $this->assertEquals(['sub' => new DependencyData()], $constructor->Dependencies);
    }

    /**
     * @covers ::addDependency
     */
    public function testAddDependency(): void
    {
        $depData = new DependencyData();
        $this->assertEquals(0, count($depData->Dependencies));

        $subDepData = new DependencyData(
            version: '1.0',
            website: 'https://example.com',
            licenses: ['MIT']
        );
        $depData->addDependency('sub', $subDepData);
        $this->assertEquals(1, count($depData->Dependencies));
        $this->assertArrayHasKey('sub', $depData->Dependencies);
        $this->assertEquals($subDepData, $depData->Dependencies['sub']);
    }

    /**
     * @covers ::setLicenses
     */
    public function testSetLicenses(): void
    {
        $depData = new DependencyData();
        $depData->setLicenses(['MIT']);

        $this->assertEquals(['MIT'], $depData->Licenses);
    }

    /**
     * @covers ::setVersion
     */
    public function testSetVersion(): void
    {
        $depData = new DependencyData();
        $depData->setVersion('1.0');

        $this->assertEquals('1.0', $depData->Version);
    }

    /**
     * @covers ::setWebsite
     */
    public function testSetWebsite(): void
    {
        $depData = new DependencyData();
        $depData->setWebsite('https://example.com');

        $this->assertEquals('https://example.com', $depData->Website);
    }
}
