<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ArticleListCachingTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_articles_list_is_cached_and_invalidated_on_create_update_delete(): void
    {
        $uniqueEmail = 'test+' . uniqid('', true) . '@example.com';
        DB::table('users')->insert([
            'name' => 'Test User',
            'email' => $uniqueEmail,
            'password' => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = User::where('email', $uniqueEmail)->first();
        $a1 = Article::create(['author_id' => $user->id, 'title' => 'A1', 'content' => '']);
        $a2 = Article::create(['author_id' => $user->id, 'title' => 'A2', 'content' => '']);

        // First fetch warms cache
        $r1 = $this->get('/api/articles?page=1&per_page=20');
        $r1->assertStatus(200);
        $j1 = $r1->json();
        $this->assertNotEmpty($j1);

        // Second fetch should be identical (from cache)
        $r2 = $this->get('/api/articles?page=1&per_page=20');
        $r2->assertStatus(200);
        $j2 = $r2->json();
        $this->assertEquals($j1, $j2, 'Articles list should be identical due to cache');

        // Create new article -> should flush tagged cache
        Article::create(['author_id' => $user->id, 'title' => 'A3', 'content' => '']);
        $r3 = $this->get('/api/articles?page=1&per_page=20');
        $r3->assertStatus(200);
        $j3 = $r3->json();
        
        // Verify that the new article appears in the refreshed list
        $titlesInJ3 = collect($j3)->pluck('title')->toArray();
        $this->assertContains('A3', $titlesInJ3, 'Newly created article A3 should appear after cache invalidation');

        // Update article -> invalidates again
        $a1->update(['title' => 'A1-updated']);
        $r4 = $this->get('/api/articles?page=1&per_page=20');
        $r4->assertStatus(200);
        $j4 = $r4->json();
        $this->assertTrue(collect($j4)->contains(fn($it) => $it['title'] === 'A1-updated'));

        // Delete article -> invalidates again
        $a2->delete();
        $r5 = $this->get('/api/articles?page=1&per_page=20');
        $r5->assertStatus(200);
        $j5 = $r5->json();
        $this->assertFalse(collect($j5)->contains(fn($it) => $it['title'] === 'A2'));
    }
}
