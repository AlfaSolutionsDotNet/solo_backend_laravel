<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public $table = "posts";

    protected $fillable = [
        'user_id',
        'desc',
        'tags',
        'link_preview_json',
        'interest_ids',
        'is_paid',
        'zip_code',
        'country',
        'state',
        'city',
        'latitude',
        'longitude',
        'cta',
        'external_link',
    ];

    // ========== ADD THIS ACCESSOR ==========
    /**
     * Get the is_paid value with 24-hour expiry check
     * If the post is paid and created more than 24 hours ago, it becomes free
     */
    public function getIsPaidAttribute($value)
    {
        // If the post is not paid in database, return false
        if (!$value) {
            return false;
        }
        
        // Check if 24 hours have passed since creation
        if ($this->created_at && now()->diffInHours($this->created_at) >= 24) {
            return false; // Expired after 24 hours
        }
        
        return true; // Still paid (within 24 hours)
    }

    /**
     * Check if the post is currently paid
     */
    public function isPaid()
    {
        return $this->is_paid && now()->diffInHours($this->created_at) < 24;
    }
    // ========== END OF ADDED CODE ==========

    public function content()
    {
        return $this->hasMany(PostContent::class, 'post_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public static function processPosts($posts, $userId)
    {
        $postIds = $posts->pluck('id');
        $blockedUserIds = User::where('is_block', 1)->pluck('id');

        $likedPosts = Like::whereIn('post_id', $postIds)
            ->where('user_id', $userId)
            ->pluck('post_id')
            ->toArray();

        $commentCounts = Comment::whereIn('post_id', $postIds)
            ->whereNotIn('user_id', $blockedUserIds)
            ->selectRaw('post_id, COUNT(*) as count')
            ->groupBy('post_id')
            ->pluck('count', 'post_id');

        $likeCounts = Like::whereIn('post_id', $postIds)
            ->whereNotIn('user_id', $blockedUserIds)
            ->selectRaw('post_id, COUNT(*) as count')
            ->groupBy('post_id')
            ->pluck('count', 'post_id');

        foreach ($posts as $post) {
            $post->is_like = in_array($post->id, $likedPosts) ? 1 : 0;
            $post->comments_count = $commentCounts[$post->id] ?? 0;
            $post->likes_count = $likeCounts[$post->id] ?? 0;
        }

        return $posts;
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }
}