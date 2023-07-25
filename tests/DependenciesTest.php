<?php

declare(strict_types=1);

namespace UnitTests;

use Locr\Lib\Dependencies;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Locr\Lib\Dependencies
 * @coversDefaultClass \Locr\Lib\Dependencies
 */
final class DependenciesTest extends TestCase
{
    protected function setUp(): void
    {
        putenv('FORCE_COMPOSER_JSON_COULD_NOT_BEEN_READ=0');
        putenv('FORCE_COMPOSER_JSON_DATA_IS_NOT_AN_ARRAY=0');
        putenv('FORCE_COMPOSER_LOCK_COULD_NOT_BEEN_READ=0');
        putenv('FORCE_COMPOSER_LOCK_DATA_IS_NOT_AN_ARRAY=0');
    }

    /**
     * @covers ::getComposerDependencies
     */
    public function testGetComposerDependenciesComposerJsonDoesNotExists(): void
    {
        $this->expectExceptionMessage(
            'Locr\Lib\Dependencies::getComposerDependencies(string $path, bool $filesAreRequired): array' .
                ' => composer.json does not exists'
        );

        Dependencies::getComposerDependencies(
            __DIR__ .
                DIRECTORY_SEPARATOR . 'assets' .
                DIRECTORY_SEPARATOR . 'composer' .
                DIRECTORY_SEPARATOR . 'json_does_not_exists'
        );
    }

    /**
     * @covers ::getComposerDependencies
     */
    public function testGetComposerDependenciesComposerLockDoesNotExists(): void
    {
        $this->expectExceptionMessage(
            'Locr\Lib\Dependencies::getComposerDependencies(string $path, bool $filesAreRequired): array' .
                ' => composer.lock does not exists'
        );

        Dependencies::getComposerDependencies(
            __DIR__ .
                DIRECTORY_SEPARATOR . 'assets' .
                DIRECTORY_SEPARATOR . 'composer' .
                DIRECTORY_SEPARATOR . 'lock_does_not_exists'
        );
    }

    /**
     * @covers ::getDependencies
     */
    public function testGetDependenciesComposerJsonDoesNotExists(): void
    {
        $deps = Dependencies::getDependencies(
            __DIR__ .
                DIRECTORY_SEPARATOR . 'assets' .
                DIRECTORY_SEPARATOR . 'composer' .
                DIRECTORY_SEPARATOR . 'json_does_not_exists'
        );

        $this->assertEquals(0, count($deps));
    }

    /**
     * @covers ::getDependencies
     */
    public function testGetDependenciesComposerLockDoesNotExists(): void
    {
        $deps = Dependencies::getDependencies(
            __DIR__ .
                DIRECTORY_SEPARATOR . 'assets' .
                DIRECTORY_SEPARATOR . 'composer' .
                DIRECTORY_SEPARATOR . 'lock_does_not_exists'
        );

        $this->assertEquals(0, count($deps));
    }

    /**
     * @covers ::getDependencies
     * @covers \Locr\Lib\DependencyData
     */
    public function testGetDependencies(): void
    {
        $deps = Dependencies::getDependencies(dirname(__DIR__), withDev: true);
        $this->assertGreaterThanOrEqual(1, count($deps));
    }

    /**
     * @covers ::getDependencies
     */
    public function testGetDependenciesComposerJsonCouldNotBeenRead(): void
    {
        $this->expectExceptionMessage(
            'Locr\Lib\Dependencies::getComposerDependencies(string $path, bool $filesAreRequired): array' .
                ' => composer.json could not been read'
        );

        putenv('FORCE_COMPOSER_JSON_COULD_NOT_BEEN_READ=1');

        Dependencies::getDependencies(dirname(__DIR__), withDev: true);
    }

    /**
     * @covers ::getDependencies
     */
    public function testGetDependenciesComposerJsonDataIsNotAnArray(): void
    {
        $this->expectExceptionMessage(
            'Locr\Lib\Dependencies::getComposerDependencies(string $path, bool $filesAreRequired): array' .
                ' => composer.json data is not an array'
        );

        putenv('FORCE_COMPOSER_JSON_DATA_IS_NOT_AN_ARRAY=1');

        Dependencies::getDependencies(dirname(__DIR__), withDev: true);
    }

    /**
     * @covers ::getDependencies
     */
    public function testGetDependenciesComposerLockCouldNotBeenRead(): void
    {
        $this->expectExceptionMessage(
            'Locr\Lib\Dependencies::getComposerDependencies(string $path, bool $filesAreRequired): array' .
                ' => composer.lock could not been read'
        );

        putenv('FORCE_COMPOSER_LOCK_COULD_NOT_BEEN_READ=1');

        Dependencies::getDependencies(dirname(__DIR__), withDev: true);
    }

    /**
     * @covers ::getDependencies
     */
    public function testGetDependenciesComposerLockDataIsNotAnArray(): void
    {
        $this->expectExceptionMessage(
            'Locr\Lib\Dependencies::getComposerDependencies(string $path, bool $filesAreRequired): array' .
                ' => composer.lock data is not an array'
        );

        putenv('FORCE_COMPOSER_LOCK_DATA_IS_NOT_AN_ARRAY=1');

        Dependencies::getDependencies(dirname(__DIR__), withDev: true);
    }
}
