<?php

namespace Saggre\WordPress\Repository\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Saggre\WordPress\Repository\Model\PluginBrowse;
use Saggre\WordPress\Repository\Model\PluginQuery;

class PluginQueryTest extends TestCase
{
    public function testToRequestParametersDefaults()
    {
        $query = new PluginQuery();

        self::assertEquals(['page' => 1, 'per_page' => 250], $query->toRequestParameters());
    }

    public function testToRequestParametersEnumeratesRecentlyUpdatedPlugins()
    {
        $query = new PluginQuery(browse: PluginBrowse::Updated, page: 3, perPage: 250);

        self::assertEquals(
            ['page' => 3, 'per_page' => 250, 'browse' => 'updated'],
            $query->toRequestParameters()
        );
    }

    public function testToRequestParametersTogglesFields()
    {
        $query = new PluginQuery(fields: [
            'sections' => false,
            'description' => false,
            'screenshots' => false,
            'icons' => false,
            'contributors' => true,
        ]);

        self::assertEquals(
            [
                'sections' => '0',
                'description' => '0',
                'screenshots' => '0',
                'icons' => '0',
                'contributors' => '1',
            ],
            $query->toRequestParameters()['fields']
        );
    }

    public function testToRequestParametersOmitsUnusedFilters()
    {
        $parameters = (new PluginQuery(search: 'seo'))->toRequestParameters();

        self::assertSame('seo', $parameters['search']);
        self::assertArrayNotHasKey('tag', $parameters);
        self::assertArrayNotHasKey('author', $parameters);
        self::assertArrayNotHasKey('browse', $parameters);
        self::assertArrayNotHasKey('fields', $parameters);
    }

    public function testToRequestParametersIncludesAllFilters()
    {
        $query = new PluginQuery(
            browse: PluginBrowse::Popular,
            search: 'cache',
            tag: 'performance',
            author: 'automattic',
            page: 2,
            perPage: 10,
        );

        self::assertEquals(
            [
                'page' => 2,
                'per_page' => 10,
                'browse' => 'popular',
                'search' => 'cache',
                'tag' => 'performance',
                'author' => 'automattic',
            ],
            $query->toRequestParameters()
        );
    }
}
