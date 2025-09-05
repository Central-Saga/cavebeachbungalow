<?php

namespace App\Traits;

use Spatie\Activitylog\LogOptions;

trait LogsActivity
{
    use \Spatie\Activitylog\Traits\LogsActivity;

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getLoggableAttributes())
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($this->getLogName());
    }

    /**
     * Get the attributes that should be logged.
     */
    protected function getLoggableAttributes(): array
    {
        return $this->loggableAttributes ?? [];
    }

    /**
     * Get the log name for the model.
     */
    protected function getLogName(): string
    {
        return $this->logName ?? class_basename($this);
    }

    /**
     * Get the description for the activity log.
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        return match ($eventName) {
            'created' => "{$this->getLogName()} baru dibuat",
            'updated' => "{$this->getLogName()} diperbarui",
            'deleted' => "{$this->getLogName()} dihapus",
            'restored' => "{$this->getLogName()} dipulihkan",
            default => "{$this->getLogName()} {$eventName}",
        };
    }

    /**
     * Boot the trait.
     */
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            activity()
                ->performedOn($model)
                ->causedBy(auth()->user())
                ->log("{$model->getLogName()} baru dibuat");
        });

        static::updated(function ($model) {
            activity()
                ->performedOn($model)
                ->causedBy(auth()->user())
                ->log("{$model->getLogName()} diperbarui");
        });

        static::deleted(function ($model) {
            activity()
                ->performedOn($model)
                ->causedBy(auth()->user())
                ->log("{$model->getLogName()} dihapus");
        });
    }
}
