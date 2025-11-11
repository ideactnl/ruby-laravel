<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoContent extends Model
{
    protected $fillable = [
        'title',
        'location',
        'order',
        'condition',
        'video_url',
        'video_type',
        'video_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope to get active videos only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get videos by location
     */
    public function scopeForLocation($query, string $location)
    {
        return $query->where('location', $location);
    }

    /**
     * Scope to get videos ordered by their order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Scope to get videos that should appear on daily view
     */
    public function scopeForDailyView($query)
    {
        return $query->whereNotNull('condition');
    }

    /**
     * Extract YouTube video ID from URL
     */
    public static function extractYoutubeId(string $url): ?string
    {
        if (preg_match('/youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        if (preg_match('/[?&]v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Get the thumbnail URL for YouTube videos
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->video_type === 'youtube' && $this->video_id) {
            return "https://img.youtube.com/vi/{$this->video_id}/hqdefault.jpg";
        }

        return null;
    }

    /**
     * Get the embed URL for YouTube videos
     */
    public function getEmbedUrlAttribute(): ?string
    {
        if ($this->video_type === 'youtube' && $this->video_id) {
            return "https://www.youtube-nocookie.com/embed/{$this->video_id}?rel=0&modestbranding=1";
        }

        return $this->video_url;
    }

    /**
     * Get the watch URL for YouTube videos
     */
    public function getWatchUrlAttribute(): ?string
    {
        if ($this->video_type === 'youtube' && $this->video_id) {
            return "https://www.youtube.com/watch?v={$this->video_id}";
        }

        return $this->video_url;
    }
}
