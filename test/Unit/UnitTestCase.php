<?php

namespace Saggre\WordPress\Repository\Test\Unit;

use PHPUnit\Framework\TestCase;
use Saggre\WordPress\Repository\Util\Path;

abstract class UnitTestCase extends TestCase
{
    /**
     * Get the raw content of a fixture file.
     *
     * @param string $name File name in the fixtures directory.
     * @return string
     */
    protected function getFixture(string $name): string
    {
        $path = (new Path('/'))->join(__DIR__, 'fixtures', $name);

        if (!file_exists($path)) {
            $this->fail("Fixture {$path} does not exist.");
        }

        return file_get_contents($path);
    }

    /**
     * Get the decoded content of a JSON fixture file.
     *
     * @param string $name File name in the fixtures directory.
     * @return array<string,mixed>
     */
    protected function getJsonFixture(string $name): array
    {
        return json_decode($this->getFixture($name), true);
    }
}
