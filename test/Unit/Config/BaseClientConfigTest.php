<?php

namespace Saggre\WordPress\Repository\Test\Unit\Config;

use Closure;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Saggre\WordPress\Repository\Config\BaseClientConfig;

class BaseClientConfigTest extends TestCase
{
    protected function getBuilder(): Closure
    {
        return fn(array $params) => new class (...$params) extends BaseClientConfig {
        };
    }

    public function testConstructorInvalidSlug()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Slug cannot be empty.');

        call_user_func($this->getBuilder(), [
            '',
            '1.0.0',
            'https://example.com',
            'TestUserAgent/1.0',
        ]);
    }

    public function testConstructorInvalidVersion()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Version cannot be empty.');

        call_user_func($this->getBuilder(), [
            'test-plugin',
            '',
            'https://example.com',
            'TestUserAgent/1.0',
        ]);
    }
}
