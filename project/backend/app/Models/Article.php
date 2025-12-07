<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Cache\TaggableStore;

class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'author_id',
        'image_path',
        'published_at',
        'image_versions',
    ];

    protected static function boot()
    {
        parent::boot();

        $invalidate = function () {
            Cache::forget('api.stats');
            $store = Cache::getStore();
            if ($store instanceof TaggableStore) {
                Cache::tags(['articles_list'])->flush();
            } else {
                // On file driver, flush all cache to clear all paginated keys
                Cache::flush();
            }
        };

        static::created($invalidate);
        static::updated($invalidate);
        static::deleted($invalidate);
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'published_at' => 'datetime',
        'image_versions' => 'array',
    ];

    /**
     * Get the author of the article.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the comments for the article.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}

