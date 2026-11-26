<?php

namespace Tests\Feature;

use App\Http\Controllers\PostController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExploreFeaturedSearchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.foreign_key_constraints' => false]);
        DB::purge('sqlite');
        $db = DB::connection();
        $db->getPdo()->sqliteCreateFunction('FIND_IN_SET', function ($id, $list) {
            return in_array((string) $id, explode(',', (string) $list), true) ? 1 : 0;
        }, 2);
        $db->statement('CREATE TABLE users (id INTEGER PRIMARY KEY, is_block INTEGER, block_user_ids TEXT)');
        $db->statement('CREATE TABLE posts (id INTEGER PRIMARY KEY, user_id INTEGER, `desc` TEXT, tags TEXT, interest_ids TEXT, is_restricted INTEGER, is_paid INTEGER, city TEXT, state TEXT, country TEXT, zip_code TEXT, created_at TEXT, updated_at TEXT)');
        $db->statement('CREATE TABLE post_contents (id INTEGER PRIMARY KEY, post_id INTEGER, content_type INTEGER)');
        $db->insert("INSERT INTO users VALUES (1, 0, ''), (2, 0, '')");
        Carbon::setTestNow(Carbon::parse('2026-11-26 12:00:00'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_featured_and_ordinary_captions_share_scope_and_chronological_pagination(): void
    {
        // Featured #3 is active; #1 has expired; #2 and #4 are ordinary.
        $this->insertPost(1, 1, '2026-11-24 12:00:00');
        $this->insertPost(2, 0, '2026-11-26 08:00:00');
        $this->insertPost(3, 1, '2026-11-26 10:00:00');
        $this->insertPost(4, 0, '2026-11-26 11:00:00');
        $this->insertPost(5, 1, '2026-11-26 11:30:00', 'Other');
        $this->insertPost(6, 1, '2026-11-26 11:45:00', 'Boston', '8');
        $this->insertPost(7, 1, '2026-11-26 11:50:00', 'Boston', '7', 1);
        $this->insertPost(8, 0, '2026-11-23 12:00:00');

        $params = ['user_id' => 1, 'limit' => 4, 'start' => 0,
            'keyword' => 'QuArTz', 'city' => 'Boston', 'interest_ids' => [7]];
        $response = (new PostController())->explorePosts(new Request($params))->getData(true);
        $this->assertTrue($response['status']);
        $this->assertSame([4, 3, 2, 1], array_column($response['data'], 'id'));
        $this->assertEquals([false, true, false, false], array_column($response['data'], 'is_paid'));

        $params['start'] = 4;
        $next = (new PostController())->explorePosts(new Request($params))->getData(true);
        $this->assertSame([8], array_column($next['data'], 'id'));

        Carbon::setTestNow(Carbon::parse('2026-11-28 12:00:00'));
        $params['start'] = 0;
        $expired = (new PostController())->explorePosts(new Request($params))->getData(true);
        $this->assertSame([4, 3, 2, 1], array_column($expired['data'], 'id'));
        $this->assertEquals([false, false, false, false], array_column($expired['data'], 'is_paid')); 
    }

    private function insertPost(int $id, int $paid, string $createdAt, string $city = 'Boston', string $category = '7', int $restricted = 0): void
    {
        DB::insert('INSERT INTO posts (id, user_id, `desc`, tags, interest_ids, is_restricted, is_paid, city, created_at) VALUES (?, 2, ?, ?, ?, ?, ?, ?, ?)',
            [$id, 'Distinctive quartz caption', '', $category, $restricted, $paid, $city, $createdAt]);
        DB::insert('INSERT INTO post_contents VALUES (?, ?, 0)', [$id, $id]);
    }
}
