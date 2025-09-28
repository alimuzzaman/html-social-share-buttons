<?php
use PHPUnit\Framework\TestCase;

class InMemoryWpdbStub
{
    public $prefix = 'wp_';
    private $rows = [];
    private $autoId = 1;

    public function get_var($query)
    {
        // Simulate existence check: return id if present for INSERT
        // We'll parse a simple WHERE post_id = %d AND network = %s pattern
        return null;
    }

    public function get_row($query, $output = OBJECT)
    {
        // Return a stored row when asked
        // Very naive: match post_id and network in query
        if (preg_match('/WHERE post_id = (\d+) AND network = \'(.*?)\'/i', $query, $m)) {
            $postId = (int)$m[1];
            $network = $m[2];
            foreach ($this->rows as $r) {
                if ($r['post_id'] === $postId && $r['network'] === $network) {
                    return (object)[ 'count' => $r['count'], 'updated_at' => $r['updated_at'] ];
                }
            }
        }
        return null;
    }

    public function update($table, $data, $where, $format = [], $where_format = [])
    {
        // find by id
        foreach ($this->rows as &$r) {
            if (isset($where['id']) && $r['id'] === $where['id']) {
                $r = array_merge($r, $data);
                return 1;
            }
        }
        return false;
    }

    public function insert($table, $data, $format = [])
    {
        $data['id'] = $this->autoId++;
        $this->rows[] = $data;
        return 1;
    }

    // Helper for tests
    public function setRow(array $row)
    {
        $this->rows[] = $row;
    }
}

class TestShareCountStorage extends TestCase
{
    public function testSaveAndGetCountWorkflow()
    {
        if (!class_exists('HtmlSocialShare\\ShareCounts\\ShareCountManager')) {
            $this->markTestSkipped('ShareCountManager not available');
        }

        // Create settings mock and cache mock
        $settingsMock = $this->createMock(\HtmlSocialShare\Settings::class);
        $settingsMock->method('get')->willReturnMap([
            ['share_counts_cache_ttl', 43200, 43200],
            ['enabled_networks', [], []]
        ]);

        $cacheMock = $this->createMock(\HtmlSocialShare\CacheInterface::class);
        $cacheMock->expects($this->any())->method('get')->willReturn(null);
        $cacheMock->expects($this->any())->method('set')->willReturn(null);

        // Replace global $wpdb with stub
        $stub = new InMemoryWpdbStub();
        $GLOBALS['wpdb'] = $stub;

        $manager = new \HtmlSocialShare\ShareCounts\ShareCountManager($cacheMock, $settingsMock);

        // Save a count for post 123
        $saved = $manager->saveCount(123, 'facebook', 42);
        $this->assertTrue($saved);

        // Now simulate reading from DB by setting a row on stub
        $stub->setRow([
            'id' => 1,
            'post_id' => 123,
            'url' => 'https://example.com/post/123',
            'network' => 'facebook',
            'count' => 42,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $count = $manager->getCountForPostNetwork(123, 'facebook');
        $this->assertEquals(42, $count);
    }
}
