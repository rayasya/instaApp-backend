<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'caption',
        'image_url',
        'post_type',
        'visibility',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function saves()
    {
        return $this->hasMany(Save::class);
    }

    // Helper
    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function isSavedBy($userId)
    {
        return $this->saves()->where('user_id', $userId)->exists();
    }

    public function canBeViewedBy($userId)
    {
        if ($this->visibility === 'public') {
            return true;
        }

        if ($this->user_id === $userId) {
            return true;
        }

        if ($this->visibility === 'private') {
            return false;
        }

        return false;
    }
}
