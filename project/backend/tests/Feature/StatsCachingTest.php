<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StatsCachingTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_stats_endpoint_is_cached_and_invalidated_on_changes(): void
    {
        DB::table('users')->insert([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = User::where('email', 'test@example.com')->first();
        $article = Article::create([
            'title' => 'Test Article',
            'content' => 'Content',
            'author_id' => $user->id,
        ]);

        $response1 = $this->get('/api/stats');
        $response1->assertStatus(200);
        $json1 = $response1->json();

        // Second call should return same payload (from cache)
        $response2 = $this->get('/api/stats');
        $response2->assertStatus(200);
        $json2 = $response2->json();
        $this->assertEquals($json1, $json2, 'Stats response should be identical due to cache');

        // Mutate data -> should invalidate stats cache via model events
        Comment::create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'content' => 'Test comment',
        ]);

        $response3 = $this->get('/api/stats');
        $response3->assertStatus(200);
        $json3 = $response3->json();

        $this->assertNotEquals($json2['total_comments'], $json3['total_comments'], 'Stats should reflect updated comment count after invalidation');
    }
}
