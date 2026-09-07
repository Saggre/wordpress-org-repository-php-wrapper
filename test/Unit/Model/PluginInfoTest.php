<?php

namespace Saggre\WordPress\Repository\Test\Unit\Model;

use Saggre\WordPress\Repository\Model\Contributor;
use Saggre\WordPress\Repository\Model\PluginInfo;
use Saggre\WordPress\Repository\Test\Unit\UnitTestCase;

class PluginInfoTest extends UnitTestCase
{
    protected function getPluginInfo(): PluginInfo
    {
        return PluginInfo::fromArray($this->getJsonFixture('plugin_information.json'));
    }

    public function testFromArrayReadsScalarFields()
    {
        $info = $this->getPluginInfo();

        self::assertSame('hello-dolly', $info->slug);
        self::assertSame('Hello Dolly', $info->name);
        self::assertSame('1.7.2', $info->version);
        self::assertSame('https://profiles.wordpress.org/matt/', $info->authorProfile);
        self::assertSame('http://wordpress.org/plugins/hello-dolly/', $info->homepage);
        self::assertSame('https://downloads.wordpress.org/plugin/hello-dolly.1.7.2.zip', $info->downloadLink);
        self::assertSame('4.6', $info->requires);
        self::assertSame('6.9.7', $info->tested);
        self::assertSame(600000, $info->activeInstalls);
        self::assertSame(88.0, $info->rating);
        self::assertSame(177, $info->numRatings);
    }

    public function testFromArrayReadsVersionsMap()
    {
        $versions = $this->getPluginInfo()->versions;

        self::assertSame(['1.5', '1.6', '1.7.2', 'trunk'], array_keys($versions));
        self::assertSame('https://downloads.wordpress.org/plugin/hello-dolly.1.6.zip', $versions['1.6']);
    }

    public function testFromArrayReadsContributors()
    {
        $contributors = $this->getPluginInfo()->contributors;

        self::assertSame(['matt', 'wordpressdotorg'], array_keys($contributors));
        self::assertContainsOnlyInstancesOf(Contributor::class, $contributors);
        self::assertSame('matt', $contributors['matt']->username);
        self::assertSame('Matt Mullenweg', $contributors['matt']->displayName);
        self::assertSame('https://profiles.wordpress.org/matt/', $contributors['matt']->profile);
        self::assertStringStartsWith('https://secure.gravatar.com/', $contributors['matt']->avatar);
    }

    public function testFromArrayParsesDates()
    {
        $info = $this->getPluginInfo();

        self::assertSame('2025-10-24T04:13:00+00:00', $info->lastUpdated->format(DATE_ATOM));
        self::assertSame('2008-07-06T00:00:00+00:00', $info->added->format(DATE_ATOM));
    }

    public function testFromArrayKeepsRawPayload()
    {
        $info = $this->getPluginInfo();

        self::assertSame('<p>Only the smallest of plugins.</p>', $info->sections['description']);
        self::assertSame('https://wordpress.org/support/plugin/hello-dolly/', $info->raw['support_url']);
    }

    public function testFromArrayReadsFalseFieldsAsEmpty()
    {
        $info = $this->getPluginInfo();

        self::assertNull($info->requiresPhp);
        self::assertSame([], $info->tags);
    }

    public function testFromArrayReadsTrimmedRecord()
    {
        $info = PluginInfo::fromArray(['slug' => 'hello-dolly']);

        self::assertSame('hello-dolly', $info->slug);
        self::assertNull($info->name);
        self::assertNull($info->lastUpdated);
        self::assertNull($info->activeInstalls);
        self::assertNull($info->rating);
        self::assertSame([], $info->contributors);
        self::assertSame([], $info->versions);
        self::assertSame([], $info->sections);
    }
}
