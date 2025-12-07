<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Cache\TaggableStore;

class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'article_id',
        'user_id',
        'content',
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
     * Get the article that the comment belongs to.
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the user who wrote the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

