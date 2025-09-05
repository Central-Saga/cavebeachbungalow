<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity as SpatieActivity;

class ActivityLog extends SpatieActivity
{
    protected $table = 'activity_log';

    /**
     * Scope to get recent activities.
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->latest()->limit($limit);
    }

    /**
     * Scope to get activities by event type.
     */
    public function scopeByEvent($query, $event)
    {
        return $query->where('event', $event);
    }

    /**
     * Scope to get activities by causer.
     */
    public function scopeByCauser($query, $causerId)
    {
        return $query->where('causer_id', $causerId);
    }

    /**
     * Get formatted description for display.
     */
    public function getFormattedDescriptionAttribute()
    {
        $description = $this->description;

        // Add user name if available
        if ($this->causer) {
            $description = "{$this->causer->name} - {$description}";
        }

        return $description;
    }

    /**
     * Get the time ago for the activity.
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the icon for the activity type.
     */
    public function getIconAttribute()
    {
        return match ($this->event) {
            'created' => 'plus-circle',
            'updated' => 'pencil',
            'deleted' => 'trash',
            'restored' => 'arrow-path',
            default => 'information-circle',
        };
    }

    /**
     * Get the color for the activity type.
     */
    public function getColorAttribute()
    {
        return match ($this->event) {
            'created' => 'green',
            'updated' => 'blue',
            'deleted' => 'red',
            'restored' => 'yellow',
            default => 'gray',
        };
    }
}
