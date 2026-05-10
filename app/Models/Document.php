<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Document extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'trainee_id', 'type', 'level_year',
        'status', 'reference_number', 'scan_file'
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    public function latestSortie()
    {
        return $this->hasOne(Movement::class)
            ->where('action_type', 'Sortie')
            ->latestOfMany();
    }
}