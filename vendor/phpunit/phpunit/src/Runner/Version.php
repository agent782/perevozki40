<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner;

use SebastianBergmann\Version as VersionId;

/**
 * This class defines the current version of PHPUnit.
 */
class Version
{
    private static $pharVersion;

    private static $version;

    /**
     * Returns the current version of PHPUnit.
     */
    public static function id(): string
    {
        if (self::$pharVersion !== null) {
            return self::$pharVersion;
        }

        if (self::$version === null) {
<<<<<<< HEAD
            $version       = new VersionId('7.4.5', \dirname(__DIR__, 2));
=======
            $version       = new VersionId('7.5.0', \dirname(__DIR__, 2));
>>>>>>> 64b161444950a0f9b805f8b88c3e6ae18c821a5c
            self::$version = $version->getVersion();
        }

        return self::$version;
    }

    public static function series(): string
    {
        if (\strpos(self::id(), '-')) {
            $version = \explode('-', self::id())[0];
        } else {
            $version = self::id();
        }

        return \implode('.', \array_slice(\explode('.', $version), 0, 2));
    }

    public static function getVersionString(): string
    {
        return 'PHPUnit ' . self::id() . ' by Sebastian Bergmann and contributors.';
    }

    public static function getReleaseChannel(): string
    {
        if (\strpos(self::$pharVersion, '-') !== false) {
            return '-nightly';
        }

        return '';
    }
}
